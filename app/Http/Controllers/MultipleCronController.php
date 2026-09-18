<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AttendanceMark;
use App\Models\AttendanceSetting;
use App\Models\AttendanceStatus;
use App\Models\Master\Weekendcalendar;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use App\Services\FcmDirectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Session;

class MultipleCronController extends Controller
{
    public function testBiometricAttendance(Request $request)
    {
        $request->validate([
            'attendance_unique_id' => 'required|string|max:100',
            'date' => 'required|date_format:Y-m-d',
            'time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ]);

        $attendanceUniqueId = trim((string) $request->input('attendance_unique_id'));
        $date = (string) $request->input('date');
        $timeInput = trim((string) $request->input('time'));
        $time = strlen($timeInput) === 5 ? ($timeInput . ':00') : $timeInput;
        $now = now();

        $entity = $this->resolveEntity(
            $attendanceUniqueId,
            (int) $request->input('branch_id', Session::get('branch_id', 0)),
            (int) $request->input('session_id', Session::get('session_id', 0))
        );

        if (!$entity) {
            return response()->json([
                'ok' => false,
                'message' => 'No student or staff member found for this attendance unique ID.',
            ], 422);
        }

        $branchId = (int) $entity['branch_id'];
        $sessionId = (int) $entity['session_id'];
        $setting = AttendanceSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderByDesc('id')
            ->first();

        if (!$setting || (int) $setting->attendance_type !== 1) {
            return response()->json([
                'ok' => false,
                'message' => 'Biometric attendance is not enabled for this branch and session.',
            ], 422);
        }

        DB::table('biometric_attendance')->updateOrInsert([
            'unique_id' => $attendanceUniqueId,
            'date' => $date,
            'time' => $time,
        ], [
            'user_id' => $entity['source_table'] === 'users' ? (int) $entity['entity_id'] : null,
            'admission_id' => $entity['source_table'] === 'admission' ? (int) $entity['entity_id'] : null,
            'branch_id' => $branchId,
            'session_id' => $sessionId,
            'entity_type' => (string) $entity['entity_type'],
            'source' => 'manual',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $services = $this->normalizedServices($setting->messaging_services ?? []);
        $sync = $this->syncBiometricRows($setting, $branchId, $sessionId, $services);

        return response()->json([
            'ok' => true,
            'message' => !empty($sync['processed'])
                ? 'Biometric attendance marked successfully.'
                : 'Biometric punch saved but could not be marked yet.',
            'sync' => $sync,
            'data' => [
                'unique_id' => $attendanceUniqueId,
                'date' => $date,
                'time' => $time,
            ],
        ]);
    }

    private function isHolidayDate(int $branchId, int $sessionId, string $date): bool
    {
        return Weekendcalendar::leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereDate('weekendcalendar.date', $date)
            ->whereRaw("LOWER(COALESCE(attendance_status.name, '')) = ?", ['holiday'])
            ->exists();
    }

    private function normalizeAttendanceStatusName(?string $name, string $fallback = 'holiday'): string
    {
        $value = strtolower(trim((string) $name));
        if ($value === '') {
            return $fallback;
        }

        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        $value = trim((string) $value, '_');

        return $value !== '' ? $value : $fallback;
    }

    private function checkCalendarOrSundayAttendanceForToday(int $branchId, int $sessionId): array
    {
        $today = Carbon::today();
        $date = $today->toDateString();

        if ($branchId <= 0 || $sessionId <= 0) {
            return [
                'triggered' => false,
                'date' => $date,
                'reason' => 'Invalid branch/session context',
            ];
        }

        $event = Weekendcalendar::query()
            ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereDate('weekendcalendar.date', $date)
            ->orderBy('weekendcalendar.id', 'asc')
            ->select([
                'weekendcalendar.id',
                'weekendcalendar.event_title',
                'weekendcalendar.event_description',
                'weekendcalendar.auto_message_enabled',
                'weekendcalendar.message_services',
                'weekendcalendar.auto_message_text',
                'attendance_status.name as attendance_status_name',
            ])
            ->first();

        if ($event) {
            return [
                'triggered' => true,
                'source' => 'academic_calendar',
                'date' => $date,
                // Any academic calendar event/date should mark holiday before absent logic runs.
                'status' => 'holiday',
                'event' => [
                    'id' => (int) ($event->id ?? 0),
                    'title' => (string) ($event->event_title ?? ''),
                    'description' => (string) ($event->event_description ?? ''),
                    'attendance_status_name' => (string) ($event->attendance_status_name ?? ''),
                    'auto_message_enabled' => (int) ($event->auto_message_enabled ?? 0),
                    'message_services' => (string) ($event->message_services ?? 'whatsapp,firebase,sms'),
                    'auto_message_text' => (string) ($event->auto_message_text ?? ''),
                ],
            ];
        }

        if ($today->isSunday()) {
            return [
                'triggered' => true,
                'source' => 'sunday',
                'date' => $date,
                'status' => 'holiday',
                'event' => null,
            ];
        }

        return [
            'triggered' => false,
            'date' => $date,
            'reason' => 'No academic calendar event and not Sunday',
        ];
    }

    private function buildCalendarAutoMessageForRecipient(string $messageTemplate, string $name, string $date): string
    {
        $custom = trim((string) $messageTemplate);
        if ($custom === '') {
            return 'Holiday/Event update from school.';
        }

        return $custom;
    }

    private function queueAcademicCalendarAutoMessages(int $branchId, int $sessionId, string $date, array $calendarCheck, array $serviceFilter = []): array
    {
        $event = (array) ($calendarCheck['event'] ?? []);
        $enabled = (int) ($event['auto_message_enabled'] ?? 0) === 1;
        $messageText = trim((string) ($event['auto_message_text'] ?? ''));

        if (!$enabled || $messageText === '') {
            return [
                'enabled' => false,
                'queued' => 0,
                'services' => [],
                'unique_ids' => [],
                'skipped_reason' => 'Academic calendar auto message is disabled or empty for today',
            ];
        }

        $services = $this->normalizedServices($event['message_services'] ?? ['whatsapp', 'firebase', 'sms']);
        if (!empty($serviceFilter)) {
            $services = array_values(array_intersect($services, $this->normalizedServices($serviceFilter)));
        }
        if (empty($services)) {
            return [
                'enabled' => true,
                'queued' => 0,
                'services' => [],
                'unique_ids' => [],
                'skipped_reason' => 'No matching messaging services after filter',
            ];
        }

        $marks = AttendanceMark::query()
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereDate('date', $date)
            ->where('status', 'holiday')
            ->get();

        $queued = 0;
        $queuedUniqueIds = [];
        $serviceCounts = array_fill_keys($services, 0);
        $sent = 0;
        $failed = 0;
        $duplicateSkipped = 0;

        foreach ($marks as $mark) {
            $uid = trim((string) ($mark->unique_id ?? ''));
            if ($uid === '') {
                continue;
            }

            $entity = $this->resolveEntity($uid, $branchId, $sessionId);
            if (!$entity) {
                continue;
            }

            $recipientName = trim((string) ($entity['display_name'] ?? $uid));
            $finalMessage = $this->buildCalendarAutoMessageForRecipient($messageText, $recipientName, $date);
            $dispatch = $this->sendDirectAttendanceNotifications(
                $services,
                $mark,
                $entity,
                [
                    'calendar_auto_message' => true,
                    'calendar_date' => $date,
                    'calendar_source' => (string) ($calendarCheck['source'] ?? 'academic_calendar'),
                    'calendar_event_id' => (int) ($event['id'] ?? 0),
                    'calendar_event_title' => (string) ($event['title'] ?? ''),
                    'calendar_event_description' => (string) ($event['description'] ?? ''),
                    'name' => $recipientName,
                    'date' => $date,
                    'status' => 'holiday',
                    'message_template' => $finalMessage,
                ]
            );

            $queued += (int) ($dispatch['processed'] ?? 0);
            $sent += (int) ($dispatch['sent'] ?? 0);
            $failed += (int) ($dispatch['failed'] ?? 0);
            $duplicateSkipped += (int) ($dispatch['duplicate_skipped'] ?? 0);

            foreach (($dispatch['service_counts'] ?? []) as $service => $count) {
                $serviceCounts[$service] = (int) ($serviceCounts[$service] ?? 0) + (int) $count;
            }

            if (($dispatch['processed'] ?? 0) > 0) {
                $queuedUniqueIds[] = $uid;
            }
        }

        return [
            'enabled' => true,
            'queued' => $queued,
            'sent' => $sent,
            'failed' => $failed,
            'duplicate_skipped' => $duplicateSkipped,
            'services' => $services,
            'service_counts' => $serviceCounts,
            'attendance_unique_ids' => array_values(array_unique($queuedUniqueIds)),
            'message_preview' => $messageText,
        ];
    }

    private function markAllAttendanceForStatusWithoutMessaging(int $branchId, int $sessionId, string $date, string $status): array
    {
        $status = $this->normalizeAttendanceStatusName($status, 'holiday');

        $students = Admission::query()
            ->select('attendance_unique_id')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->whereNotNull('attendance_unique_id')
            ->whereRaw("TRIM(attendance_unique_id) != ''")
            ->get()
            ->map(fn ($row) => [
                'unique_id' => trim((string) $row->attendance_unique_id),
                'entity_type' => 'student',
            ]);

        $staff = User::query()
            ->select('attendance_unique_id', 'role_id')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->whereNotNull('attendance_unique_id')
            ->whereRaw("TRIM(attendance_unique_id) != ''")
            ->get()
            ->map(fn ($row) => [
                'unique_id' => trim((string) $row->attendance_unique_id),
                'entity_type' => ((int) ($row->role_id ?? 0) === 3) ? 'student' : 'staff',
            ]);

        $all = [];
        foreach ($students->concat($staff) as $row) {
            $uid = trim((string) ($row['unique_id'] ?? ''));
            if ($uid === '') {
                continue;
            }
            if (!isset($all[$uid])) {
                $all[$uid] = $row;
            }
        }

        $createdOrUpdated = 0;
        $unchanged = 0;

        if (!empty($all)) {
            $alreadyProcessedCount = AttendanceMark::query()
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->whereDate('date', $date)
                ->whereIn('unique_id', array_keys($all))
                ->where('status', $status)
                ->whereNull('in_time')
                ->whereNull('out_time')
                ->count();

            if ($alreadyProcessedCount === count($all)) {
                return [
                    'date' => $date,
                    'status' => $status,
                    'total_candidates' => count($all),
                    'created_or_updated' => 0,
                    'unchanged' => count($all),
                    'already_processed' => true,
                    'messages_created' => 0,
                    'messages_dispatched' => 0,
                ];
            }
        }

        foreach ($all as $uid => $row) {
            $mark = AttendanceMark::where('unique_id', $uid)
                ->where('date', $date)
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->first();

            $newValues = [
                'entity_type' => (string) ($row['entity_type'] ?? 'staff'),
                'status' => $status,
                'in_time' => null,
                'out_time' => null,
            ];

            $isChanged = true;
            if ($mark) {
                $isChanged =
                    (string) ($mark->entity_type ?? '') !== (string) $newValues['entity_type'] ||
                    (string) ($mark->status ?? '') !== (string) $newValues['status'] ||
                    !empty($mark->in_time) ||
                    !empty($mark->out_time);
            }

            if (!$isChanged) {
                $unchanged++;
                continue;
            }

            AttendanceMark::updateOrCreate(
                [
                    'unique_id' => $uid,
                    'date' => $date,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                ],
                [
                    'entity_type' => $newValues['entity_type'],
                    'status' => $newValues['status'],
                    'in_time' => null,
                    'out_time' => null,
                    'created_by' => Session::get('id') ?: 0,
                ]
            );

            $createdOrUpdated++;
        }

        return [
            'date' => $date,
            'status' => $status,
            'total_candidates' => count($all),
            'created_or_updated' => $createdOrUpdated,
            'unchanged' => $unchanged,
            'already_processed' => $createdOrUpdated === 0 && $unchanged === count($all),
            'messages_created' => 0,
            'messages_dispatched' => 0,
        ];
    }

    private function resolveEntity(string $attendanceUniqueId, int $branchId, int $sessionId): ?array
    {
        $student = Admission::select('id', 'branch_id', 'session_id', 'mobile', 'first_name', 'last_name')
            ->where('attendance_unique_id', $attendanceUniqueId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$student) {
            $student = Admission::select('id', 'branch_id', 'session_id', 'mobile', 'first_name', 'last_name')
                ->where('attendance_unique_id', $attendanceUniqueId)
                ->first();
        }

        if ($student) {
            return [
                'entity_type' => 'student',
                'source_table' => 'admission',
                'entity_id' => (int) $student->id,
                'attendance_unique_id' => $attendanceUniqueId,
                'branch_id' => (int) ($student->branch_id ?? $branchId),
                'session_id' => (int) ($student->session_id ?? $sessionId),
                'mobile' => (string) ($student->mobile ?? ''),
                'firebase_token' => '',
                'display_name' => trim((string) (($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))) ?: $attendanceUniqueId,
            ];
        }

        $staff = User::select('id', 'branch_id', 'session_id', 'role_id', 'mobile', 'first_name', 'last_name')
            ->where('attendance_unique_id', $attendanceUniqueId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$staff) {
            $staff = User::select('id', 'branch_id', 'session_id', 'role_id', 'mobile', 'first_name', 'last_name')
                ->where('attendance_unique_id', $attendanceUniqueId)
                ->first();
        }

        if ($staff) {
            $entityType = ((int) $staff->role_id === 3) ? 'student' : 'staff';

            return [
                'entity_type' => $entityType,
                'source_table' => 'users',
                'entity_id' => (int) $staff->id,
                'attendance_unique_id' => $attendanceUniqueId,
                'branch_id' => (int) ($staff->branch_id ?? $branchId),
                'session_id' => (int) ($staff->session_id ?? $sessionId),
                'mobile' => (string) ($staff->mobile ?? ''),
                'firebase_token' => '',
                'display_name' => trim((string) (($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''))) ?: $attendanceUniqueId,
            ];
        }

        return null;
    }

    private function resolveLunchTimes(AttendanceSetting $setting, Carbon $date): array
    {
        $month = (int) $date->format('n');
        $isSummer = $month >= 4 && $month <= 9;

        $fromRaw = $isSummer ? $setting->summer_lunch_from_time : $setting->winter_lunch_from_time;
        $toRaw = $isSummer ? $setting->summer_lunch_to_time : $setting->winter_lunch_to_time;

        if (!$fromRaw) {
            $fromRaw = $setting->winter_lunch_from_time ?: $setting->summer_lunch_from_time;
        }
        if (!$toRaw) {
            $toRaw = $setting->winter_lunch_to_time ?: $setting->summer_lunch_to_time;
        }

        $from = $fromRaw ? Carbon::parse($date->format('Y-m-d') . ' ' . substr((string) $fromRaw, 0, 8)) : null;
        $to = $toRaw ? Carbon::parse($date->format('Y-m-d') . ' ' . substr((string) $toRaw, 0, 8)) : null;

        return [$from, $to];
    }

    private function isAfterLunchCutoff(AttendanceSetting $setting, Carbon $date, Carbon $punch): bool
    {
        [$lunchFrom, $lunchTo] = $this->resolveLunchTimes($setting, $date);
        if ($lunchTo) {
            return $punch->gte($lunchTo);
        }
        if ($lunchFrom) {
            return $punch->gte($lunchFrom);
        }
        return false;
    }

    private function normalizedServices($raw): array
    {
        $default = ['whatsapp', 'firebase', 'sms'];
        if (is_string($raw) && trim($raw) !== '') {
            $raw = explode(',', $raw);
        }
        if (!is_array($raw) || empty($raw)) {
            return $default;
        }

        $allowed = ['whatsapp', 'firebase', 'sms'];
        $services = [];
        foreach ($raw as $item) {
            $service = strtolower(trim((string) $item));
            if (in_array($service, $allowed, true)) {
                $services[] = $service;
            }
        }

        $services = array_values(array_unique($services));
        return !empty($services) ? $services : $default;
    }

    private function resolveAttendanceFirebaseTokens(string $attendanceUniqueId, int $branchId, int $sessionId): array
    {
        if (!Schema::hasTable('notification_tokens')) {
            return [];
        }

        $attendanceUniqueId = trim($attendanceUniqueId);
        if ($attendanceUniqueId === '') {
            return [];
        }

        $admissionId = Admission::query()
            ->when($branchId > 0, fn ($q) => $q->where('branch_id', $branchId))
            ->when($sessionId > 0, fn ($q) => $q->where('session_id', $sessionId))
            ->where(function ($q) use ($attendanceUniqueId) {
                $q->where('attendance_unique_id', $attendanceUniqueId)
                    ->orWhere('admissionNo', $attendanceUniqueId);
            })
            ->value('id');

        $query = DB::table('notification_tokens as nt')
            ->where(function ($q) use ($attendanceUniqueId, $admissionId) {
                $q->where('nt.attendance_unique_id', $attendanceUniqueId);
                if ($admissionId) {
                    $q->orWhere('nt.admission_id', $admissionId);
                }
            })
            ->where('nt.platform', 'android')
            ->whereNotNull('nt.device_token')
            ->where('nt.device_token', '!=', '')
            ->whereNull('nt.deleted_at');

        return $query->orderByDesc('nt.id')
            ->pluck('nt.device_token')
            ->filter(fn ($token) => trim((string) $token) !== '')
            ->map(fn ($token) => trim((string) $token))
            ->unique()
            ->values()
            ->all();
    }

    private function resolveAdminFirebaseTokens(int $branchId, int $sessionId): array
    {
        if (!Schema::hasTable('notification_tokens')) {
            return [];
        }

        $query = DB::table('notification_tokens as nt')
            ->join('users as u', function ($join) {
                $join->on('u.attendance_unique_id', '=', 'nt.attendance_unique_id')
                    ->orOn('u.id', '=', 'nt.user_id');
            })
            ->where('u.role_id', 1)
            ->where('nt.platform', 'android')
            ->whereNotNull('nt.device_token')
            ->where('nt.device_token', '!=', '')
            ->whereNull('nt.deleted_at')
            ->where('u.status', 1);

        if ($branchId > 0) {
            $query->where('u.branch_id', $branchId);
        }

        if ($sessionId > 0) {
            $query->where('u.session_id', $sessionId);
        }

        return $query->orderByDesc('nt.id')
            ->pluck('nt.device_token')
            ->filter(fn ($token) => trim((string) $token) !== '')
            ->map(fn ($token) => trim((string) $token))
            ->unique()
            ->values()
            ->all();
    }

    private function resolveUserFirebaseTokens(int $userId, string $attendanceUniqueId): array
    {
        if (!Schema::hasTable('notification_tokens')) {
            return [];
        }

        return DB::table('notification_tokens')
            ->where(function ($query) use ($userId, $attendanceUniqueId) {
                $query->where('user_id', $userId);
                if ($attendanceUniqueId !== '') {
                    $query->orWhere('attendance_unique_id', $attendanceUniqueId);
                }
            })
            ->where('platform', 'android')
            ->whereNotNull('device_token')->where('device_token', '!=', '')
            ->whereNull('deleted_at')->orderByDesc('id')->pluck('device_token')
            ->map(fn ($token) => trim((string) $token))->filter()->unique()->values()->all();
    }

    public function nextDayHolidayNotification(Request $request)
    {
        $date = (string) ($request->attributes->get('holiday_notification_date') ?: Carbon::tomorrow()->toDateString());
        $branchId = (int) ($request->input('branch_id') ?: Session::get('branch_id') ?: 0);
        $sessionId = (int) ($request->input('session_id') ?: Session::get('session_id') ?: 0);

        if ($branchId <= 0 && $sessionId <= 0) {
            $contexts = Weekendcalendar::query()
                ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
                ->whereDate('weekendcalendar.date', $date)
                ->whereRaw("LOWER(TRIM(COALESCE(attendance_status.name, ''))) = ?", ['holiday'])
                ->where('weekendcalendar.auto_message_enabled', 1)
                ->whereNotNull('weekendcalendar.auto_message_text')
                ->where('weekendcalendar.auto_message_text', '!=', '')
                ->select('weekendcalendar.branch_id', 'weekendcalendar.session_id')->distinct()->get();

            if ($contexts->isEmpty()) {
                return response()->json(['ok'=>true, 'date'=>$date, 'holiday_found'=>false,
                    'contexts_processed'=>0, 'sent'=>0, 'failed'=>0, 'duplicate_skipped'=>0,
                    'message'=>'No enabled holiday notification found for the requested date.']);
            }

            $summary = ['ok'=>true, 'date'=>$date, 'holiday_found'=>true, 'contexts_processed'=>0,
                'events'=>0, 'recipients'=>0, 'sent'=>0, 'failed'=>0, 'duplicate_skipped'=>0, 'contexts'=>[]];
            foreach ($contexts as $context) {
                $childRequest = Request::create($request->path(), 'GET', [
                    'branch_id'=>(int) $context->branch_id, 'session_id'=>(int) $context->session_id,
                ]);
                $childRequest->attributes->set('holiday_notification_date', $date);
                $result = $this->nextDayHolidayNotification($childRequest)->getData(true);
                $summary['contexts_processed']++;
                foreach (['events','recipients','sent','failed','duplicate_skipped'] as $key) {
                    $summary[$key] += (int) ($result[$key] ?? 0);
                }
                $summary['contexts'][] = $result;
            }
            return response()->json($summary);
        }

        if ($branchId <= 0 || $sessionId <= 0) {
            $setting = AttendanceSetting::query()
                ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
                ->when($sessionId > 0, fn ($query) => $query->where('session_id', $sessionId))
                ->orderByDesc('id')->first();
            $branchId = $branchId > 0 ? $branchId : (int) ($setting->branch_id ?? 0);
            $sessionId = $sessionId > 0 ? $sessionId : (int) ($setting->session_id ?? 0);
        }
        $events = Weekendcalendar::query()
            ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereDate('weekendcalendar.date', $date)
            ->whereRaw("LOWER(TRIM(COALESCE(attendance_status.name, ''))) = ?", ['holiday'])
            ->where('weekendcalendar.auto_message_enabled', 1)
            ->whereNotNull('weekendcalendar.auto_message_text')
            ->where('weekendcalendar.auto_message_text', '!=', '')
            ->select('weekendcalendar.*')->get();

        if ($events->isEmpty()) {
            return response()->json(['ok' => true, 'date' => $date, 'holiday_found' => false,
                'sent' => 0, 'failed' => 0, 'duplicate_skipped' => 0,
                'message' => 'No enabled holiday notification found for the requested date.']);
        }

        $students = Admission::query()->where('branch_id', $branchId)->where('session_id', $sessionId)
            ->select('id', 'attendance_unique_id')->get();
        $staff = User::query()->where('branch_id', $branchId)->where('session_id', $sessionId)
            ->where('status', 1)->where('role_id', '!=', 3)->select('id', 'role_id', 'attendance_unique_id')->get();
        $fcm = new FcmDirectService();
        $sent = 0; $failed = 0; $duplicateSkipped = 0; $eligibleRecipients = 0;
        $runId = now()->format('YmdHisv');

        foreach ($events as $event) {
            $title = trim((string) $event->event_title) ?: 'Holiday Notification';
            $body = trim((string) $event->auto_message_text);
            $baseSource = 'academic_holiday:' . (int) $event->id . ':' . $date . ':run:' . $runId;
            $selectedRoles = array_values(array_filter(array_map('intval', explode(',', (string) ($event->notification_role_ids ?? '')))));
            $eventStudents = empty($selectedRoles) || in_array(3, $selectedRoles, true) ? $students : collect();
            $eventStaff = empty($selectedRoles) ? $staff : $staff->whereIn('role_id', $selectedRoles)->values();
            $eligibleRecipients += $eventStudents->count() + $eventStaff->count();

            foreach ($eventStudents as $student) {
                $sourceKey = $baseSource . ':student:' . (int) $student->id;
                $tokens = $this->resolveAttendanceFirebaseTokens((string) $student->attendance_unique_id, $branchId, $sessionId);
                $delivered = false;
                foreach ($tokens as $token) {
                    $result = $fcm->send($token, ['type'=>'default','notification_type'=>'default','date'=>$date,'event_id'=>(string) $event->id], 'high', $title, $body);
                    if (!empty($result['success'])) { $sent++; $delivered = true; } else { $failed++; }
                }
                if ($delivered) Notification::create(['source_key'=>$sourceKey,'admission_id'=>$student->id,'branch_id'=>$branchId,'session_id'=>$sessionId,'title'=>$title,'content'=>$body,'type'=>'student','show_status'=>1,'message_seen'=>0]);
            }

            foreach ($eventStaff as $user) {
                $sourceKey = $baseSource . ':user:' . (int) $user->id;
                $tokens = $this->resolveUserFirebaseTokens((int) $user->id, trim((string) $user->attendance_unique_id));
                $delivered = false;
                foreach ($tokens as $token) {
                    $result = $fcm->send($token, ['type'=>'default','notification_type'=>'default','date'=>$date,'event_id'=>(string) $event->id], 'high', $title, $body);
                    if (!empty($result['success'])) { $sent++; $delivered = true; } else { $failed++; }
                }
                if ($delivered) Notification::create(['source_key'=>$sourceKey,'user_id'=>$user->id,'branch_id'=>$branchId,'session_id'=>$sessionId,'title'=>$title,'content'=>$body,'type'=>'user','show_status'=>1,'message_seen'=>0]);
            }
        }

        return response()->json(['ok'=>true,'date'=>$date,'holiday_found'=>true,'events'=>$events->count(),
            'recipients'=>$eligibleRecipients,'sent'=>$sent,'failed'=>$failed,
            'duplicate_skipped'=>$duplicateSkipped]);
    }

    public function todayHolidayNotification(Request $request)
    {
        $request->attributes->set('holiday_notification_date', Carbon::today()->toDateString());
        return $this->nextDayHolidayNotification($request);
    }

    private function createAttendanceNotificationRecords($messageRow, string $title, string $body): void
    {
        $branchId = (int) ($messageRow->branch_id ?? 0);
        $sessionId = (int) ($messageRow->session_id ?? 0);
        $attendanceUniqueId = trim((string) ($messageRow->attendance_unique_id ?? ''));
        $sourceTable = (string) ($messageRow->source_table ?? 'users');
        $now = now();

        if ($sourceTable === 'admission') {
            $admissionId = Admission::query()
                ->when($branchId > 0, fn ($q) => $q->where('branch_id', $branchId))
                ->when($sessionId > 0, fn ($q) => $q->where('session_id', $sessionId))
                ->where('attendance_unique_id', $attendanceUniqueId)
                ->value('id');
            $deviceToken = $this->resolveAttendanceFirebaseTokens($attendanceUniqueId, $branchId, $sessionId)[0] ?? null;

            if ($admissionId) {
                Notification::create([
                    'title' => $title,
                    'content' => $body,
                    'type' => 'student',
                    'admission_id' => $admissionId,
                    'user_id' => null,
                    'device_token' => $deviceToken,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'message_seen' => 0,
                    'show_status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return;
        }

        $admins = User::query()
            ->leftJoin('notification_tokens as nt', function ($join) {
                $join->on('nt.attendance_unique_id', '=', 'users.attendance_unique_id')
                    ->where('nt.platform', 'android')
                    ->whereNull('nt.deleted_at');
            })
            ->where('users.role_id', 1)
            ->where('users.status', 1)
            ->when($branchId > 0, fn ($q) => $q->where('users.branch_id', $branchId))
            ->when($sessionId > 0, fn ($q) => $q->where('users.session_id', $sessionId))
            ->select('users.id', 'nt.device_token')
            ->get()
            ->unique('id')
            ->values();

        foreach ($admins as $admin) {
            Notification::create([
                'title' => $title,
                'content' => $body,
                'type' => 'user',
                'admission_id' => null,
                'user_id' => (int) ($admin->id ?? 0),
                'device_token' => $admin->device_token ?? null,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'message_seen' => 0,
                'show_status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }


    private function buildAttendanceMessage(AttendanceMark $mark, array $entity): string
    {
        $name = trim((string) ($entity['display_name'] ?? ''));
        if ($name === '') {
            $name = (string) $mark->unique_id;
        }

        $dateStr = Carbon::parse((string) $mark->date)->format('d/m/Y');
        $hasIn = !empty($mark->in_time);
        $hasOut = !empty($mark->out_time);
        $inStr = $hasIn ? Carbon::parse((string) $mark->in_time)->format('h:i A') : '';
        $outStr = $hasOut ? Carbon::parse((string) $mark->out_time)->format('h:i A') : '';
        $rawStatus = (string) ($mark->status ?? '');
        $status = ucwords(str_replace('_', ' ', $rawStatus));

        if (!$hasIn && !$hasOut) {
            return "Attendance update for {$name}: Date {$dateStr}, Status {$status}.";
        }

        if (!$hasIn && $hasOut) {
            return "Attendance update for {$name}: Check-out {$outStr} on {$dateStr}, Status {$status}.";
        }

        if ($hasIn && !$hasOut) {
            return "Attendance update for {$name}: Check-in {$inStr} on {$dateStr}, Status {$status}.";
        }

        return "Attendance update for {$name}: Check-in {$inStr} on {$dateStr}, Check-out {$outStr}, Status {$status}.";
    }

    private function buildDirectDispatchPayload(AttendanceMark $mark, array $entity, string $service, array $overrides = []): array
    {
        $payload = [
            'entity_type' => $entity['entity_type'] ?? '',
            'service' => $service,
            'name' => $entity['display_name'] ?? (string) $mark->unique_id,
            'date' => (string) $mark->date,
            'status' => (string) ($mark->status ?? ''),
            'message_template' => $this->buildAttendanceMessage($mark, $entity),
        ];

        if (!empty($mark->in_time)) {
            $payload['check_in'] = (string) $mark->in_time;
        }

        if (!empty($mark->out_time)) {
            $payload['check_out'] = (string) $mark->out_time;
        }

        return array_merge($payload, $overrides);
    }

    private function buildDirectDispatchMessageRow(AttendanceMark $mark, array $entity, string $service)
    {
        $sourceTable = (string) ($entity['source_table'] ?? 'users');
        $attendanceUniqueId = (string) ($mark->unique_id ?? '');
        $branchId = (int) ($mark->branch_id ?? 0);
        $sessionId = (int) ($mark->session_id ?? 0);

        return (object) [
            'id' => null,
            'attendance_mark_id' => (int) ($mark->id ?? 0),
            'branch_id' => $branchId,
            'session_id' => $sessionId,
            'attendance_unique_id' => $attendanceUniqueId,
            'service' => $service,
            'mobile' => in_array($service, ['whatsapp', 'sms'], true) ? trim((string) ($entity['mobile'] ?? '')) : null,
            'firebase_tokens' => $service === 'firebase'
                ? (
                    $sourceTable === 'admission'
                        ? $this->resolveAttendanceFirebaseTokens($attendanceUniqueId, $branchId, $sessionId)
                        : $this->resolveAdminFirebaseTokens($branchId, $sessionId)
                )
                : [],
            'attendance_date' => (string) ($mark->date ?? ''),
            'in_time' => $mark->in_time,
            'out_time' => $mark->out_time,
            'attendance_status' => (string) ($mark->status ?? ''),
            'source_table' => $sourceTable,
        ];
    }

    private function directDispatchCacheKey(string $service, AttendanceMark $mark, array $payload): string
    {
        $fingerprint = [
            'uid' => (string) ($mark->unique_id ?? ''),
            'date' => (string) ($payload['date'] ?? $mark->date ?? ''),
            'status' => (string) ($payload['status'] ?? $mark->status ?? ''),
            'check_in' => (string) ($payload['check_in'] ?? $mark->in_time ?? ''),
            'check_out' => (string) ($payload['check_out'] ?? $mark->out_time ?? ''),
            'calendar_auto_message' => !empty($payload['calendar_auto_message']) ? 1 : 0,
            'calendar_event_id' => (int) ($payload['calendar_event_id'] ?? 0),
            'service' => $service,
        ];

        return 'attendance_direct_push:' . md5(json_encode($fingerprint));
    }

    public function sendDirectAttendanceNotifications(array $services, AttendanceMark $mark, array $entity, array $payloadOverrides = []): array
    {
        $processed = 0;
        $sent = 0;
        $failed = 0;
        $duplicateSkipped = 0;
        $serviceCounts = [];
        $results = [];

        foreach ($services as $service) {
            $service = strtolower(trim((string) $service));
            if ($service === '' || $service !== 'firebase') {
                continue;
            }

            $payload = $this->buildDirectDispatchPayload($mark, $entity, $service, $payloadOverrides);
            $messageRow = $this->buildDirectDispatchMessageRow($mark, $entity, $service);

            if (($service === 'whatsapp' || $service === 'sms') && empty($messageRow->mobile)) {
                continue;
            }

            if ($service === 'firebase' && empty($messageRow->firebase_tokens)) {
                $failed++;
                $results[] = [
                    'service' => $service,
                    'ok' => false,
                    'error' => 'No firebase token found for recipient',
                    'meta' => null,
                ];
                continue;
            }

            $cacheKey = $this->directDispatchCacheKey($service, $mark, $payload);
            if (Cache::has($cacheKey)) {
                $duplicateSkipped++;
                continue;
            }

            $processed++;
            $dispatch = $this->dispatchAttendanceMessageByService($messageRow, $payload);

            if (!empty($dispatch['ok'])) {
                $sent++;
                $serviceCounts[$service] = (int) ($serviceCounts[$service] ?? 0) + 1;
                Cache::put($cacheKey, now()->toDateTimeString(), now()->copy()->addDays(2));
            } else {
                $failed++;
            }

            $results[] = [
                'service' => $service,
                'ok' => !empty($dispatch['ok']),
                'error' => $dispatch['error'] ?? null,
                'meta' => $dispatch['meta'] ?? null,
            ];
        }

        return [
            'processed' => $processed,
            'sent' => $sent,
            'failed' => $failed,
            'duplicate_skipped' => $duplicateSkipped,
            'service_counts' => $serviceCounts,
            'results' => $results,
        ];
    }

    private function syncBiometricRows(AttendanceSetting $setting, int $branchId, int $sessionId, array $services): array
    {

        $rows = DB::table('biometric_attendance')
            ->select('unique_id', 'date', 'time')
            ->whereNotNull('unique_id')
            ->whereNotNull('date')
            ->whereNotNull('time')
            ->when($branchId > 0, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($sessionId > 0, function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })
            ->orderBy('date')
            ->orderBy('unique_id')
            ->orderBy('time')
            ->get();

        $grouped = [];
        foreach ($rows as $row) {
            $uid = trim((string) $row->unique_id);
            $date = (string) $row->date;
            $time = substr((string) $row->time, 0, 8);

            if ($uid === '' || $date === '' || $time === '') {
                continue;
            }

            $key = $uid . '|' . $date;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'attendance_unique_id' => $uid,
                    'date' => $date,
                    'times' => [],
                ];
            }

            $grouped[$key]['times'][] = $time;
        }

        $processed = 0;
        $skipped = 0;
        $deleted = 0;
        $messageRows = 0;
        $messageFailures = 0;

        foreach ($grouped as $row) {
            $uid = $row['attendance_unique_id'];
            $date = $row['date'];
            $times = $row['times'];
            sort($times);

            if (empty($times)) {
                $skipped++;
                continue;
            }

            $entity = $this->resolveEntity($uid, $branchId, $sessionId);
            if (!$entity) {
                $skipped++;
                continue;
            }

            $entryBranchId = (int) $entity['branch_id'];
            $entrySessionId = (int) $entity['session_id'];

            if ($entryBranchId <= 0) {
                $entryBranchId = $branchId;
            }
            if ($entrySessionId <= 0) {
                $entrySessionId = $sessionId;
            }

            if ($entryBranchId <= 0 || $entrySessionId <= 0) {
                $skipped++;
                continue;
            }

            if ($this->isHolidayDate($entryBranchId, $entrySessionId, $date)) {
                $skipped++;
                continue;
            }

            $existingMark = AttendanceMark::where('unique_id', $uid)
                ->where('date', $date)
                ->where('branch_id', $entryBranchId)
                ->where('session_id', $entrySessionId)
                ->first();

            $inTimeRaw = $existingMark && !empty($existingMark->in_time)
                ? substr((string) $existingMark->in_time, 0, 8)
                : $times[0];
            $outTimeRaw = null;
            $effectiveSetting = AttendanceSetting::where('branch_id', $entryBranchId)
                ->where('session_id', $entrySessionId)
                ->orderByDesc('id')
                ->first() ?: $setting;

            // Punch sequence for each user/date:
            // 1st punch = IN, 2nd punch = OUT, every later punch updates OUT.
            // When an IN already exists, every newly received punch is therefore
            // an OUT candidate. Use the latest punch in the current batch.
            if ($existingMark && !empty($existingMark->in_time)) {
                $latestPunch = $times[count($times) - 1];
                $existingOutTime = !empty($existingMark->out_time)
                    ? substr((string) $existingMark->out_time, 0, 8)
                    : null;

                if ($existingOutTime === null || $latestPunch > $existingOutTime) {
                    $outTimeRaw = $latestPunch;
                } else {
                    $outTimeRaw = $existingOutTime;
                }
            } elseif (count($times) >= 2) {
                $outTimeRaw = $times[count($times) - 1];
            }

            // Do not send a duplicate push when stale/already processed biometric
            // rows do not change either attendance time.
            $attendanceChanged = !$existingMark
                || substr((string) ($existingMark->in_time ?? ''), 0, 8) !== (string) ($inTimeRaw ?? '')
                || substr((string) ($existingMark->out_time ?? ''), 0, 8) !== (string) ($outTimeRaw ?? '');

            // First punch is In. From the second punch onward mark Half Day when
            // worked duration is below the configured minimum; otherwise mark Out.
            $status = 'in';
            if ($outTimeRaw !== null) {
                $status = 'out';
                $halfDayMinMinutes = max(0, (int) ($effectiveSetting->half_day_min_minutes ?? 0));
                if ($inTimeRaw !== null && $halfDayMinMinutes > 0) {
                    $workedMinutes = Carbon::parse($date . ' ' . $inTimeRaw)
                        ->diffInMinutes(Carbon::parse($date . ' ' . $outTimeRaw), false);
                    if ($workedMinutes > 0 && $workedMinutes < $halfDayMinMinutes) {
                        $status = 'halfday';
                    }
                }
            }

            if (!$attendanceChanged) {
                $deleted += DB::table('biometric_attendance')
                    ->where('unique_id', $uid)
                    ->whereDate('date', $date)
                    ->where('branch_id', $entryBranchId)
                    ->where('session_id', $entrySessionId)
                    ->delete();
                $skipped++;
                continue;
            }

            $mark = AttendanceMark::updateOrCreate(
                [
                    'unique_id' => $uid,
                    'date' => $date,
                    'branch_id' => $entryBranchId,
                    'session_id' => $entrySessionId,
                ],
                [
                    'entity_type' => (string) $entity['entity_type'],
                    'in_time' => $inTimeRaw,
                    'out_time' => $outTimeRaw,
                    'status' => $status,
                    'created_by' => Session::get('id') ?: 0,
                ]
            );

            $dispatch = $this->sendDirectAttendanceNotifications($services, $mark, $entity);
            $messageRows += (int) ($dispatch['sent'] ?? 0);
            $messageFailures += (int) ($dispatch['failed'] ?? 0);

            $deleted += DB::table('biometric_attendance')
                ->where('unique_id', $uid)
                ->whereDate('date', $date)
                ->where('branch_id', $entryBranchId)
                ->where('session_id', $entrySessionId)
                ->delete();

            $processed++;
        }

        return [
            'total_groups' => count($grouped),
            'processed' => $processed,
            'skipped' => $skipped,
            'deleted' => $deleted,
            'messages_created' => $messageRows,
            'messages_failed' => $messageFailures,
        ];
    }

    private function autoMarkAbsentForToday(AttendanceSetting $setting, int $branchId, int $sessionId, array $services): array
    {
        $enabled = (int) ($setting->auto_absent_mark_enabled ?? 0) === 1;
        $timeRaw = trim((string) ($setting->auto_absent_mark_time ?? ''));
        $today = Carbon::today();

        if (!$enabled) {
            return [
                'enabled' => false,
                'triggered' => false,
                'date' => $today->toDateString(),
                'reason' => 'Auto absent mark disabled',
                'marked' => 0,
                'messages_created' => 0,
            ];
        }

        if ($timeRaw === '') {
            return [
                'enabled' => true,
                'triggered' => false,
                'date' => $today->toDateString(),
                'reason' => 'Auto absent mark time not set',
                'marked' => 0,
                'messages_created' => 0,
            ];
        }

        $triggerAt = Carbon::parse($today->toDateString() . ' ' . substr($timeRaw, 0, 8));
        if (Carbon::now()->lt($triggerAt)) {
            return [
                'enabled' => true,
                'triggered' => false,
                'date' => $today->toDateString(),
                'trigger_at' => $triggerAt->format('H:i:s'),
                'reason' => 'Current time is before auto absent mark time',
                'marked' => 0,
                'messages_created' => 0,
            ];
        }

        if ($branchId <= 0 || $sessionId <= 0) {
            return [
                'enabled' => true,
                'triggered' => false,
                'date' => $today->toDateString(),
                'reason' => 'Invalid branch/session context',
                'marked' => 0,
                'messages_created' => 0,
            ];
        }

        if ($this->isHolidayDate($branchId, $sessionId, $today->toDateString())) {
            return [
                'enabled' => true,
                'triggered' => false,
                'date' => $today->toDateString(),
                'trigger_at' => $triggerAt->format('H:i:s'),
                'reason' => 'Holiday date',
                'marked' => 0,
                'messages_created' => 0,
            ];
        }

        $studentIds = Admission::query()
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->whereNotNull('attendance_unique_id')
            ->whereRaw("TRIM(attendance_unique_id) != ''")
            ->pluck('attendance_unique_id')
            ->map(fn ($v) => trim((string) $v))
            ->all();

        $staffIds = User::query()
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->whereNotNull('attendance_unique_id')
            ->whereRaw("TRIM(attendance_unique_id) != ''")
            ->pluck('attendance_unique_id')
            ->map(fn ($v) => trim((string) $v))
            ->all();

        $allUniqueIds = array_values(array_unique(array_filter(array_merge($studentIds, $staffIds))));
        if (empty($allUniqueIds)) {
            return [
                'enabled' => true,
                'triggered' => true,
                'date' => $today->toDateString(),
                'trigger_at' => $triggerAt->format('H:i:s'),
                'total_candidates' => 0,
                'marked' => 0,
                'messages_created' => 0,
            ];
        }

        $alreadyMarked = AttendanceMark::query()
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereDate('date', $today->toDateString())
            ->whereIn('unique_id', $allUniqueIds)
            ->pluck('unique_id')
            ->map(fn ($v) => trim((string) $v))
            ->all();

        $alreadyMarkedMap = array_fill_keys($alreadyMarked, true);
        $marked = 0;
        $skippedExisting = 0;
        $skippedUnresolved = 0;
        $messageRows = 0;
        $messageFailures = 0;

        foreach ($allUniqueIds as $uid) {
            if (isset($alreadyMarkedMap[$uid])) {
                $skippedExisting++;
                continue;
            }

            $entity = $this->resolveEntity($uid, $branchId, $sessionId);
            if (!$entity) {
                $skippedUnresolved++;
                continue;
            }

            $mark = AttendanceMark::updateOrCreate(
                [
                    'unique_id' => $uid,
                    'date' => $today->toDateString(),
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                ],
                [
                    'entity_type' => (string) ($entity['entity_type'] ?? 'staff'),
                    'in_time' => null,
                    'out_time' => null,
                    'status' => 'absent',
                    'created_by' => Session::get('id') ?: 0,
                ]
            );

            $dispatch = $this->sendDirectAttendanceNotifications($services, $mark, $entity);
            $messageRows += (int) ($dispatch['sent'] ?? 0);
            $messageFailures += (int) ($dispatch['failed'] ?? 0);
            $marked++;
        }

        return [
            'enabled' => true,
            'triggered' => true,
            'date' => $today->toDateString(),
            'trigger_at' => $triggerAt->format('H:i:s'),
            'total_candidates' => count($allUniqueIds),
            'already_marked' => $skippedExisting,
            'unresolved' => $skippedUnresolved,
            'marked' => $marked,
            'messages_created' => $messageRows,
            'messages_failed' => $messageFailures,
        ];
    }

    private function sendWhatsappAttendanceMessage(array $payload, $messageRow)
    {
        return null;
    }

    private function sendSmsAttendanceMessage(array $payload, $messageRow)
    {
        return null;
    }

    private function resolveNotificationBranding(int $branchId): array
    {
        $setting = Setting::query()
            ->when($branchId > 0, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->orderByDesc('id')
            ->first();

        if (!$setting) {
            $setting = Setting::query()->orderByDesc('id')->first();
        }

        $schoolName = trim((string) ($setting->name ?? 'School ERP'));
        $leftLogo = trim((string) ($setting->left_logo ?? ''));
        $baseImagePath = rtrim((string) env('IMAGE_SHOW_PATH', ''), '/');

        $logoUrl = '';
        // Temporarily disable logo/icon in Firebase attendance notifications.
        // if ($baseImagePath !== '' && $leftLogo !== '') {
        //     $logoUrl = $baseImagePath . '/setting/left_logo/' . $leftLogo;
        // }

        return [
            'school_name' => $schoolName,
            'logo_url' => $logoUrl,
        ];
    }

    private function buildFirebaseNotificationContent(array $payload, $messageRow, array $branding): array
    {
        if (!empty($payload['calendar_auto_message'])) {
            $schoolName = trim((string) ($branding['school_name'] ?? 'School ERP'));
            $customTitle = trim((string) ($payload['calendar_event_title'] ?? 'Academic Calendar Update'));
            $customBody = trim((string) ($payload['message_template'] ?? ''));
            if ($customBody === '') {
                $customBody = 'Academic calendar update.';
            }

            return [
                'title' => $customTitle,
                'body' => $customBody . "\n" . $schoolName,
                'layout' => [
                    'layout_type' => 'academic_calendar_update',
                    'title' => $customTitle,
                    'subtitle' => $schoolName,
                    'show_logo' => !empty($branding['logo_url']),
                    'show_checkout' => false,
                    'show_checkin' => false,
                ],
                'is_checkout' => false,
            ];
        }

        $name = trim((string) ($payload['name'] ?? $messageRow->attendance_unique_id));
        $name = preg_replace('/\s+/', ' ', $name);
        $name = $name !== '' ? ucwords(strtolower($name)) : 'User';
        $status = ucwords(str_replace('_', ' ', (string) ($payload['status'] ?? $messageRow->attendance_status ?? 'Present')));
        $date = trim((string) ($payload['date'] ?? $messageRow->attendance_date ?? ''));
        $checkIn = trim((string) ($payload['check_in'] ?? $messageRow->in_time ?? ''));
        $checkOut = trim((string) ($payload['check_out'] ?? $messageRow->out_time ?? ''));

        $isCheckout = $checkOut !== '';
        $schoolName = trim((string) ($branding['school_name'] ?? 'School ERP'));
        $prettyDate = $date !== '' ? Carbon::parse($date)->format('d M Y') : date('d M Y');
        $checkIn12 = $checkIn !== '' ? Carbon::parse($checkIn)->format('h:i A') : '';
        $checkOut12 = $checkOut !== '' ? Carbon::parse($checkOut)->format('h:i A') : '';
        $isCheckoutOnly = $checkIn === '' && $checkOut !== '';
        $hasNoTimes = ($checkIn === '' && $checkOut === '');

        $title = 'Attendance Marked';
        $bodyLines = [
            "Name: {$name}",
            "Date: {$prettyDate}",
        ];

        if (!$hasNoTimes && !$isCheckoutOnly && $checkIn12 !== '') {
            $bodyLines[] = "Check-in: {$checkIn12}";
        }

        if ($checkOut12 !== '') {
            $bodyLines[] = "Check-out: {$checkOut12}";
        }

        $bodyLines[] = "Status: {$status}";
        $bodyLines[] = $schoolName;
        $body = implode("\n", $bodyLines);

        $layout = [
            'layout_type' => $hasNoTimes ? 'attendance_status_only' : ($isCheckoutOnly ? 'attendance_checkout_only' : ($isCheckout ? 'attendance_checkout' : 'attendance_checkin')),
            'title' => $title,
            'subtitle' => $schoolName,
            'show_logo' => !empty($branding['logo_url']),
            'show_checkout' => $checkOut12 !== '',
            'show_checkin' => !$isCheckoutOnly && $checkIn12 !== '',
        ];

        return [
            'title' => $title,
            'body' => $body,
            'layout' => $layout,
            'is_checkout' => $isCheckout,
        ];
    }

    private function resolveFirebaseNotificationType(array $payload, $messageRow): string
    {
        // Staff/users must use the application's general notification channel in
        // every attendance flow. Attendance-specific Android channels are only
        // used for student/admission recipients.
        if (strtolower(trim((string) ($messageRow->source_table ?? ''))) === 'users') {
            return 'default';
        }

        $status = strtolower(trim((string) ($payload['status'] ?? $messageRow->attendance_status ?? '')));
        $normalizedStatus = str_replace([' ', '-', '_'], '', $status);
        $statusId = 0;

        if (is_numeric($status)) {
            $statusId = (int) $status;
        } elseif (in_array($normalizedStatus, ['in', 'present'], true)) {
            $statusId = 1;
        } elseif (in_array($normalizedStatus, ['out', 'halfday'], true)) {
            $statusId = 2;
        } elseif ($normalizedStatus === 'absent') {
            $statusId = 3;
        } else {
            $statusId = (int) AttendanceStatus::query()
                ->whereRaw("REPLACE(REPLACE(REPLACE(LOWER(name), ' ', ''), '-', ''), '_', '') = ?", [$normalizedStatus])
                ->value('id');
        }

        if ($statusId === 1) {
            return 'attendance_in';
        }

        if ($statusId === 2) {
            return 'attendance_out';
        }

        if ($statusId === 3) {
            return 'attendance_absent_v2';
        }

        return 'default';
    }

    private function sendFirebaseAttendanceMessage(array $payload, $messageRow)
    {
        $tokens = collect($messageRow->firebase_tokens ?? [])
            ->filter(fn ($token) => trim((string) $token) !== '')
            ->map(fn ($token) => trim((string) $token))
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            $sourceTable = (string) ($messageRow->source_table ?? 'users');
            $tokens = collect(
                $sourceTable === 'admission'
                    ? $this->resolveAttendanceFirebaseTokens(
                        (string) ($messageRow->attendance_unique_id ?? ''),
                        (int) ($messageRow->branch_id ?? 0),
                        (int) ($messageRow->session_id ?? 0)
                    )
                    : $this->resolveAdminFirebaseTokens(
                        (int) ($messageRow->branch_id ?? 0),
                        (int) ($messageRow->session_id ?? 0)
                    )
            )->values();
        }

        if ($tokens->isEmpty()) {
            return [
                'ok' => false,
                'error' => 'No firebase token found for recipient',
                'meta' => null,
            ];
        }

        $branding = $this->resolveNotificationBranding((int) ($messageRow->branch_id ?? 0));
        $content = $this->buildFirebaseNotificationContent($payload, $messageRow, $branding);

        $title = (string) $content['title'];
        $body = (string) $content['body'];
        $image = (string) ($branding['logo_url'] ?? '');
        $notificationType = $this->resolveFirebaseNotificationType($payload, $messageRow);
        $normalizedAttendanceStatus = str_replace(
            [' ', '-', '_'],
            '',
            strtolower((string) ($messageRow->attendance_status ?? ''))
        );
        $attendanceStatusId = match ($normalizedAttendanceStatus) {
            'in', 'present' => 1,
            'out' => 2,
            'absent' => 3,
            'halfday' => 4,
            'holiday' => 5,
            default => 0,
        };
        $data = [
            'attendance_unique_id' => (string) $messageRow->attendance_unique_id,
            'attendance_status_id' => (string) $attendanceStatusId,
            'date' => (string) ($messageRow->attendance_date ?? ''),
            'status' => (string) ($messageRow->attendance_status ?? ''),
            'school_name' => (string) ($branding['school_name'] ?? ''),
            'logo_url' => $image,
            'layout' => json_encode($content['layout']),
            'is_checkout' => !empty($content['is_checkout']) ? '1' : '0',
            'check_in' => (string) ($payload['check_in'] ?? ''),
            'check_out' => (string) ($payload['check_out'] ?? ''),
            'type' => $notificationType,
            'notification_type' => $notificationType,
            'channel_id' => $notificationType,
            'channelId' => $notificationType,
        ];

        $fcmService = new FcmDirectService();
        $lastError = null;
        $sentTokens = [];
        $failedTokens = [];

        foreach ($tokens as $token) {
            $result = $fcmService->send((string) $token, $data, 'high', $title, $body, $image !== '' ? $image : null);
            if (!empty($result['success'])) {
                $sentTokens[] = [
                    'token' => (string) $token,
                    'firebase_message_id' => $result['message_id'] ?? null,
                ];
                continue;
            }
            $lastError = $result['error'] ?? 'Unknown firebase error';
            $failedTokens[] = [
                'token' => (string) $token,
                'error' => $lastError,
            ];
        }

        if (!empty($sentTokens)) {
            $notificationRecordError = null;

            // Firebase has already accepted at least one push at this point.
            // A local notification-history insert failure must not turn that
            // successfully sent push into a failed delivery in the UI.
            try {
                $this->createAttendanceNotificationRecords($messageRow, $title, $body);
            } catch (\Throwable $e) {
                $notificationRecordError = $e->getMessage();
                Log::warning('Firebase attendance push sent, but notification history could not be saved.', [
                    'attendance_unique_id' => (string) ($messageRow->attendance_unique_id ?? ''),
                    'attendance_date' => (string) ($messageRow->attendance_date ?? ''),
                    'error' => $notificationRecordError,
                ]);
            }

            return [
                'ok' => true,
                'error' => null,
                'meta' => [
                    'sent_tokens' => $sentTokens,
                    'failed_tokens' => $failedTokens,
                    'notification_record_error' => $notificationRecordError,
                ],
            ];
        }

        return [
            'ok' => false,
            'error' => $lastError,
            'meta' => [
                'failed_tokens' => $failedTokens,
            ],
        ];
    }

    private function dispatchAttendanceMessageByService($messageRow, array $payload)
    {
        $service = strtolower(trim((string) ($messageRow->service ?? '')));

        if ($service === 'firebase') {
            return $this->sendFirebaseAttendanceMessage($payload, $messageRow);
        }

        if ($service === 'whatsapp') {
            return [
                'ok' => true,
                'error' => null,
                'meta' => $this->sendWhatsappAttendanceMessage($payload, $messageRow),
            ];
        }

        if ($service === 'sms') {
            return [
                'ok' => true,
                'error' => null,
                'meta' => $this->sendSmsAttendanceMessage($payload, $messageRow),
            ];
        }

        return [
            'ok' => false,
            'error' => 'Unsupported service',
            'meta' => null,
        ];
    }

    public function sendAttendanceMessages(Request $request)
    {
        try {
            if (strtolower(trim((string) $request->input('service', ''))) === 'firebase') {
                return response()->json([
                    'status' => true,
                    'message' => 'Firebase attendance push is disabled for bulk dispatch. Use attendance-cron-manager only.',
                    'processed' => 0,
                    'sent' => 0,
                    'failed' => 0,
                ]);
            }

            $dispatchDate = trim((string) $request->input('attendance_date', ''));
            if ($dispatchDate === '') {
                $dispatchDate = Carbon::today()->toDateString();
            }
            $limit = (int) $request->input('limit', 50);
            if ($limit < 1) {
                $limit = 50;
            }
            if ($limit > 500) {
                $limit = 500;
            }

            $query = DB::table('attendance_messages')
                ->where('status', 0)
                ->whereDate('attendance_date', $dispatchDate)
                ->orderBy('id')
                ->limit($limit);

            if ($request->filled('service')) {
                $query->where('service', strtolower(trim((string) $request->input('service'))));
            }

            if ($request->filled('attendance_unique_id')) {
                $query->where('attendance_unique_id', trim((string) $request->input('attendance_unique_id')));
            }

            if ((int) $request->input('calendar_only', 0) === 1) {
                $query->where('payload', 'like', '%"calendar_auto_message":true%');
            }

            $messages = $query->get();

            if ($messages->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No pending attendance messages.',
                    'dispatch_date' => $dispatchDate,
                    'processed' => 0,
                    'sent' => 0,
                    'failed' => 0,
                ]);
            }

            $processed = 0;
            $sent = 0;
            $failed = 0;

            foreach ($messages as $msg) {
                $processed++;

                $payload = [];
                if (!empty($msg->payload)) {
                    $decoded = json_decode((string) $msg->payload, true);
                    if (is_array($decoded)) {
                        $payload = $decoded;
                    }
                }

                $dispatch = $this->dispatchAttendanceMessageByService($msg, $payload);

                if (!empty($dispatch['ok'])) {
                    $payload['send_status'] = 'sent';
                    $payload['service_response'] = $dispatch['meta'] ?? null;

                    DB::table('attendance_messages')->where('id', $msg->id)->update([
                        'status' => 1,
                        'payload' => json_encode($payload),
                        'updated_at' => now(),
                    ]);
                    $sent++;
                } else {
                    $payload['send_status'] = 'failed';
                    $payload['send_error'] = $dispatch['error'] ?? 'Unknown error';

                    DB::table('attendance_messages')->where('id', $msg->id)->update([
                        'status' => 2,
                        'payload' => json_encode($payload),
                        'updated_at' => now(),
                    ]);
                    $failed++;
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Attendance message dispatch completed.',
                'dispatch_date' => $dispatchDate,
                'processed' => $processed,
                'sent' => $sent,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dispatchAttendanceMessagesByType(Request $request)
    {
        $type = strtolower(trim((string) $request->input('type', 'all')));
        $allowed = ['all', 'firebase', 'whatsapp', 'sms'];

        if (!in_array($type, $allowed, true)) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid type. Allowed: all, firebase, whatsapp, sms'
            ], 422);
        }

        if ($type !== 'all') {
            $request->merge(['service' => $type]);
        }

        return $this->sendAttendanceMessages($request);
    }

    public function firebaseNotiication(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Firebase attendance push is available only through attendance-cron-manager.',
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
        ]);
    }

    public function detectAttendanceType(Request $request)
    {
        $branchId = (int) ($request->input('branch_id') ?: Session::get('branch_id') ?: 0);
        $sessionId = (int) ($request->input('session_id') ?: Session::get('session_id') ?: 0);
        $services = [];

        $query = AttendanceSetting::query();

        if ($branchId > 0) {
            $query->where('branch_id', $branchId);
        }

        if ($sessionId > 0) {
            $query->where('session_id', $sessionId);
        }

        $setting = $query->orderByDesc('id')->first();

        // Cron may hit this route without login session; use the selected setting context.
        if ($setting) {
            if ($branchId <= 0) {
                $branchId = (int) ($setting->branch_id ?? 0);
            }
            if ($sessionId <= 0) {
                $sessionId = (int) ($setting->session_id ?? 0);
            }
        }

        $type = (int) ($setting->attendance_type ?? 2);

        $labels = [
            1 => 'biometric',
            2 => 'normal',
            3 => 'qr',
        ];

        $response = [
            'ok' => true,
            'branch_id' => $branchId,
            'session_id' => $sessionId,
            'attendance_type' => $type,
            'attendance_type_label' => $labels[$type] ?? 'normal',
        ];

        if ($setting) {
            $servicesSource = $request->input('services');
            if ($servicesSource === null || $servicesSource === '') {
                $servicesSource = $setting->messaging_services ?? ['whatsapp', 'firebase', 'sms'];
            }
            $services = $this->normalizedServices($servicesSource);
            $response['requested_services'] = $services;
        }

        // Must run before biometric sync / auto absent.
        $calendarCheck = $this->checkCalendarOrSundayAttendanceForToday($branchId, $sessionId);
        $response['calendar_or_sunday'] = $calendarCheck;
        if (!empty($calendarCheck['triggered'])) {
            $response['calendar_marking'] = $this->markAllAttendanceForStatusWithoutMessaging(
                $branchId,
                $sessionId,
                (string) ($calendarCheck['date'] ?? Carbon::today()->toDateString()),
                'holiday'
            );

            $calendarDate = (string) ($calendarCheck['date'] ?? Carbon::today()->toDateString());
            $response['calendar_auto_message'] = $this->queueAcademicCalendarAutoMessages(
                $branchId,
                $sessionId,
                $calendarDate,
                $calendarCheck,
                $services
            );

            $response['pipeline_stopped'] = true;
            $response['pipeline_stop_reason'] = 'Academic calendar event/Sunday holiday applied before auto absent.';

            return response()->json($response);
        }

        if ($type === 1 && $setting) {
            $response['sync'] = $this->syncBiometricRows($setting, $branchId, $sessionId, $services);
        }

        // Auto absent marking is common for all attendance types (after configured time, not exact-only).
        if ($setting) {
            $response['auto_absent'] = $this->autoMarkAbsentForToday($setting, $branchId, $sessionId, $services);

            $response['dispatch'] = [
                'mode' => 'direct',
                'services' => $services,
                'skipped' => empty($services),
                'reason' => empty($services) ? 'No messaging services configured' : null,
                'sent' => (int) ($response['sync']['messages_created'] ?? 0)
                    + (int) ($response['auto_absent']['messages_created'] ?? 0),
                'failed' => (int) ($response['sync']['messages_failed'] ?? 0)
                    + (int) ($response['auto_absent']['messages_failed'] ?? 0),
            ];
        }

        return response()->json($response);
    }
}
