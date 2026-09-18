<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSetting;
use App\Models\Master\Weekendcalendar;
use Illuminate\Support\Facades\Schema;
use Session;
use DB;
use DateTime;
use Helper;

class AttendanceSettingsController extends Controller
{
    private function holidayAttendanceStatusId(): int
    {
        $holidayStatusId = DB::table('attendance_status')
            ->whereRaw('LOWER(TRIM(name)) = ?', ['holiday'])
            ->value('id');

        if (!$holidayStatusId) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'holiday_status' => 'Holiday attendance status is not configured.',
            ]);
        }

        return (int) $holidayStatusId;
    }

    private function calendarEventRange(Weekendcalendar $event)
    {
        $rows = Weekendcalendar::where('session_id', $event->session_id)
            ->where('branch_id', $event->branch_id)
            ->where('event_title', $event->event_title)
            ->where('event_description', $event->event_description)
            ->where('event_schedule', $event->event_schedule)
            ->where('created_at', $event->created_at)
            ->orderBy('date')->get();

        return $rows->isEmpty() ? collect([$event]) : $rows;
    }

    private function normalizedMessagingServicesList($services): array
    {
        return ['firebase'];
    }

    private function weekendCalendarMessagingColumnMap(): array
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }

        $tableExists = Schema::hasTable('weekendcalendar');
        $map = [
            'enabled' => $tableExists && Schema::hasColumn('weekendcalendar', 'auto_message_enabled'),
            'services' => $tableExists && Schema::hasColumn('weekendcalendar', 'message_services'),
            'roles' => $tableExists && Schema::hasColumn('weekendcalendar', 'notification_role_ids'),
            'text' => $tableExists && Schema::hasColumn('weekendcalendar', 'auto_message_text'),
        ];

        return $map;
    }

    private function syncWeekendDateMessageMeta(int $sessionId, int $branchId, string $dateValue, array $meta): void
    {
        $cols = $this->weekendCalendarMessagingColumnMap();
        $updates = [];

        if ($cols['enabled']) {
            $updates['auto_message_enabled'] = (int) ($meta['auto_message_enabled'] ?? 0);
        }
        if ($cols['services']) {
            $updates['message_services'] = 'firebase';
        }
        if ($cols['roles']) {
            $updates['notification_role_ids'] = (string) ($meta['notification_role_ids'] ?? '');
        }
        if ($cols['text']) {
            $updates['auto_message_text'] = (string) ($meta['auto_message_text'] ?? '');
        }

        if (empty($updates)) {
            return;
        }

        DB::table('weekendcalendar')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->whereDate('date', $dateValue)
            ->update($updates);
    }

    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $setting = AttendanceSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->first();



            
         

            if ($request->isMethod('post')) {

                // HTML time inputs submit `HH:MM`, but DB-backed values are often `HH:MM:SS`.
                // Normalize to `HH:MM` so validation doesn't silently block saving.
                $timeFields = [
                    'summer_lunch_from_time',
                    'summer_lunch_to_time',
                    'winter_lunch_from_time',
                    'winter_lunch_to_time',
                    'auto_absent_mark_time',
                ];
                $normalized = [];
                foreach ($timeFields as $field) {
                    $val = $request->input($field);
                    if (is_string($val) && strlen($val) >= 8) {
                        $normalized[$field] = substr($val, 0, 5);
                    }
                }
                if (!empty($normalized)) {
                    $request->merge($normalized);
                }

                $request->validate([
                    'attendance_type' => 'required|in:1,2,3',
                    'messaging_services' => 'nullable|array',
                    'messaging_services.*' => 'in:whatsapp,firebase,sms',
                    'manual_attendance_messaging_enabled' => 'nullable|in:0,1',
                    'auto_absent_mark_enabled' => 'nullable|in:0,1',
                    'auto_absent_mark_time' => 'nullable|date_format:H:i',
                    'allow_back_date_attendance' => 'nullable|in:0,1',
                    'qr_validity_minutes' => 'required|integer|min:0|max:9999',
                    'half_day_min_minutes' => 'required|integer|min:0|max:9999',
                    'summer_lunch_from_time' => 'nullable|date_format:H:i',
                    'summer_lunch_to_time' => 'nullable|date_format:H:i',
                    'winter_lunch_from_time' => 'nullable|date_format:H:i',
                    'winter_lunch_to_time' => 'nullable|date_format:H:i',
                    'notes' => 'nullable|string|max:1000',
                ]);

            $allowBackDate = (int) ($request->has('allow_back_date_attendance') ? 1 : 0);
            $manualAttendanceMessagingEnabled = (int) ($request->has('manual_attendance_messaging_enabled') ? 1 : 0);
            $autoAbsentMarkEnabled = (int) ($request->has('auto_absent_mark_enabled') ? 1 : 0);
            $autoAbsentMarkTime = $request->auto_absent_mark_time ?: null;
            $selectedServices = $request->input('messaging_services', []);
            if (!is_array($selectedServices)) {
                $selectedServices = [];
            }
            $selectedServices = array_values(array_unique(array_filter(array_map('strtolower', $selectedServices), function ($item) {
                return in_array($item, ['whatsapp', 'firebase', 'sms'], true);
            })));
            $messagingServices = implode(',', $selectedServices);

            $hasMessagingServicesColumn = Schema::hasColumn('attendance_settings', 'messaging_services');
            $hasManualMessagingColumn = Schema::hasColumn('attendance_settings', 'manual_attendance_messaging_enabled');
            $hasAutoAbsentEnabledColumn = Schema::hasColumn('attendance_settings', 'auto_absent_mark_enabled');
            $hasAutoAbsentTimeColumn = Schema::hasColumn('attendance_settings', 'auto_absent_mark_time');

            $saveData = [
                'user_id' => Session::get('id'),
                'attendance_type' => (int) $request->attendance_type,
                'allow_back_date_attendance' => $allowBackDate,
                'qr_validity_minutes' => (int) $request->qr_validity_minutes,
                'half_day_min_minutes' => (int) $request->half_day_min_minutes,
                'summer_lunch_from_time' => $request->summer_lunch_from_time,
                'summer_lunch_to_time' => $request->summer_lunch_to_time,
                'winter_lunch_from_time' => $request->winter_lunch_from_time,
                'winter_lunch_to_time' => $request->winter_lunch_to_time,
                'notes' => $request->notes,
            ];
            if ($hasMessagingServicesColumn) {
                $saveData['messaging_services'] = $messagingServices;
            }
            if ($hasManualMessagingColumn) {
                $saveData['manual_attendance_messaging_enabled'] = $manualAttendanceMessagingEnabled;
            }
            if ($hasAutoAbsentEnabledColumn) {
                $saveData['auto_absent_mark_enabled'] = $autoAbsentMarkEnabled;
            }
            if ($hasAutoAbsentTimeColumn) {
                $saveData['auto_absent_mark_time'] = $autoAbsentMarkTime;
            }

            $saved = AttendanceSetting::updateOrCreate(
                [
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                ],
                $saveData
            );


            // Force update on the saved row (avoid any stale value issues)
            $updateData = [
                'attendance_type' => (int) $request->attendance_type,
                'allow_back_date_attendance' => $allowBackDate,
                'qr_validity_minutes' => (int) $request->qr_validity_minutes,
                'half_day_min_minutes' => (int) $request->half_day_min_minutes,
                'summer_lunch_from_time' => $request->summer_lunch_from_time,
                'summer_lunch_to_time' => $request->summer_lunch_to_time,
                'winter_lunch_from_time' => $request->winter_lunch_from_time,
                'winter_lunch_to_time' => $request->winter_lunch_to_time,
                'notes' => $request->notes,
            ];
            if ($hasMessagingServicesColumn) {
                $updateData['messaging_services'] = $messagingServices;
            }
            if ($hasManualMessagingColumn) {
                $updateData['manual_attendance_messaging_enabled'] = $manualAttendanceMessagingEnabled;
            }
            if ($hasAutoAbsentEnabledColumn) {
                $updateData['auto_absent_mark_enabled'] = $autoAbsentMarkEnabled;
            }
            if ($hasAutoAbsentTimeColumn) {
                $updateData['auto_absent_mark_time'] = $autoAbsentMarkTime;
            }

            DB::table('attendance_settings')
                ->where('id', $saved->id)
                ->update($updateData);

            AttendanceSetting::where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->where('id', '!=', $saved->id)
                ->delete();

            return redirect()->to($request->url())->with('message', 'Attendance settings saved successfully.');
        }

        return Helper::view('attendance.settings', compact('setting'));
    }


    private function buildWeekendEventsByDate($sessionId, $branchId, $fromDate, $toDate)
    {
        $msgCols = $this->weekendCalendarMessagingColumnMap();
        $rows = Weekendcalendar::select('weekendcalendar.*', 'attendance_status.name as attendance_status_name')
            ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.session_id', $sessionId)
            ->where('weekendcalendar.branch_id', $branchId)
            ->whereBetween('weekendcalendar.date', [$fromDate, $toDate])
            ->orderBy('weekendcalendar.date', 'ASC')
            ->orderBy('weekendcalendar.id', 'ASC')
            ->get();

        $eventsByDate = [];
        foreach ($rows as $row) {
            $dateKey = (string) $row->date;
            if (!isset($eventsByDate[$dateKey])) {
                $eventsByDate[$dateKey] = [];
            }

            $eventsByDate[$dateKey][] = [
                'id' => (int) $row->id,
                'date' => $dateKey,
                'event_title' => (string) ($row->event_title ?? ''),
                'event_description' => (string) ($row->event_description ?? ''),
                'event_schedule' => (string) ($row->event_schedule ?? ''),
                'attendance_status' => (int) ($row->attendance_status ?? 0),
                'attendance_status_name' => (string) ($row->attendance_status_name ?? ''),
                'auto_message_enabled' => $msgCols['enabled'] ? (int) ($row->auto_message_enabled ?? 0) : 0,
                'message_services' => 'firebase',
                'notification_role_ids' => $msgCols['roles'] ? (string) ($row->notification_role_ids ?? '') : '',
                'auto_message_text' => $msgCols['text'] ? (string) ($row->auto_message_text ?? '') : '',
            ];
        }

        return $eventsByDate;
    }

    public function addWeekend(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $userId = Session::get('id');

        if ($request->isMethod('post')) {
            $mode = (int) $request->input('mode', 0);
            $messageEnabled = (int) ($request->has('auto_message_enabled') ? 1 : (int) $request->input('auto_message_enabled', 0));
            $messageServices = 'firebase';
            $messageText = trim((string) $request->input('auto_message_text', ''));
            $notificationRoleIds = collect((array) $request->input('notification_role_ids', []))
                ->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->unique()->values()->all();
            $notificationRoles = implode(',', $notificationRoleIds);

            if ($mode === 4) {
                $event = Weekendcalendar::where('id', (int) $request->input('event_id'))
                    ->where('session_id', $sessionId)->where('branch_id', $branchId)->firstOrFail();
                $range = $this->calendarEventRange($event);
                return response()->json(['ok' => true, 'item' => [
                    'id' => (int) $event->id, 'from_date' => (string) $range->min('date'),
                    'to_date' => (string) $range->max('date'), 'event_title' => (string) $event->event_title,
                    'event_description' => (string) $event->event_description,
                    'auto_message_enabled' => (int) ($event->auto_message_enabled ?? 0),
                    'auto_message_text' => (string) ($event->auto_message_text ?? ''),
                    'notification_role_ids' => array_values(array_filter(array_map('intval', explode(',', (string) ($event->notification_role_ids ?? ''))))),
                ]]);
            }

            if ($mode === 2) {
                $request->validate([
                    'event_id' => 'required|integer',
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                    'event_title' => 'required|string|max:200',
                    'event_description' => 'nullable|string|max:1000',
                    'auto_message_text' => 'required_if:auto_message_enabled,1|nullable|string|max:1000',
                    'notification_role_ids' => 'required_if:auto_message_enabled,1|nullable|array|min:1',
                    'notification_role_ids.*' => 'integer|exists:role,id',
                ]);

                $eventId = (int) $request->input('event_id');
                $fromDate = date('Y-m-d', strtotime($request->from_date));
                $toDate = date('Y-m-d', strtotime($request->to_date));

                $row = Weekendcalendar::where('id', $eventId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$row) {
                    return response()->json(['ok' => false, 'message' => 'Calendar event not found.'], 404);
                }

                $eventTitle = trim((string) $request->event_title);
                $eventDescription = trim((string) $request->event_description);
                $eventText = $eventDescription !== '' ? ($eventTitle . ' - ' . $eventDescription) : $eventTitle;
                $attendanceStatus = $this->holidayAttendanceStatusId();
                $msgCols = $this->weekendCalendarMessagingColumnMap();
                $rangeRows = $this->calendarEventRange($row)->values();
                $groupCreatedAt = $rangeRows->first()->created_at;
                $cursor = strtotime($fromDate); $endTs = strtotime($toDate); $index = 0;
                while ($cursor <= $endTs) {
                    $dateValue = date('Y-m-d', $cursor); $dateObj = new DateTime($dateValue);
                    $target = $rangeRows->get($index) ?: new Weekendcalendar();
                    $target->user_id = $userId; $target->session_id = $sessionId; $target->branch_id = $branchId;
                    $target->created_at = $groupCreatedAt;
                    $target->month_id = (int) $dateObj->format('n'); $target->date = $dateValue; $target->day = $dateObj->format('l');
                    $target->event_title = $eventTitle; $target->event_description = $eventDescription; $target->event_schedule = $eventText;
                    $target->attendance_status = $attendanceStatus;
                    if ($msgCols['enabled']) $target->auto_message_enabled = $messageEnabled;
                    if ($msgCols['services']) $target->message_services = $messageServices;
                    if ($msgCols['roles']) $target->notification_role_ids = $notificationRoles;
                    if ($msgCols['text']) $target->auto_message_text = $messageText;
                    $target->save(); if ($index === 0) $row = $target;
                    $index++; $cursor = strtotime('+1 day', $cursor);
                }
                for ($remove = $index; $remove < $rangeRows->count(); $remove++) $rangeRows[$remove]->delete();

                $statusName = (string) DB::table('attendance_status')->where('id', $attendanceStatus)->value('name');

                return response()->json([
                    'ok' => true,
                    'message' => 'Calendar event updated successfully.',
                    'item' => [
                        'id' => (int) $row->id,
                        'date' => (string) $row->date,
                        'event_title' => (string) ($row->event_title ?? ''),
                        'event_description' => (string) ($row->event_description ?? ''),
                        'event_schedule' => (string) ($row->event_schedule ?? ''),
                        'attendance_status' => (int) ($row->attendance_status ?? 0),
                        'attendance_status_name' => $statusName,
                        'auto_message_enabled' => $messageEnabled,
                        'message_services' => $messageServices,
                        'notification_role_ids' => $notificationRoles,
                        'auto_message_text' => $messageText,
                    ],
                ]);
            }

            if ($mode === 3) {
                $request->validate([
                    'event_id' => 'required|integer',
                ]);

                $eventId = (int) $request->input('event_id');
                $row = Weekendcalendar::where('id', $eventId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$row) {
                    return response()->json(['ok' => false, 'message' => 'Calendar event not found.'], 404);
                }

                $deletedDate = (string) $row->date;
                $row->delete();

                return response()->json([
                    'ok' => true,
                    'message' => 'Calendar event deleted successfully.',
                    'event_id' => $eventId,
                    'date' => $deletedDate,
                ]);
            }

            if ($mode === 1) {
                $request->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                    'event_title' => 'required|string|max:200',
                    'event_description' => 'nullable|string|max:1000',
                    'auto_message_text' => 'required_if:auto_message_enabled,1|nullable|string|max:1000',
                    'notification_role_ids' => 'required_if:auto_message_enabled,1|nullable|array|min:1',
                    'notification_role_ids.*' => 'integer|exists:role,id',
                ]);

                $fromDate = date('Y-m-d', strtotime($request->from_date));
                $toDate = date('Y-m-d', strtotime($request->to_date));
                $eventTitle = trim((string) $request->event_title);
                $eventDescription = trim((string) $request->event_description);
                $eventText = $eventDescription !== '' ? ($eventTitle . ' - ' . $eventDescription) : $eventTitle;
                $attendanceStatus = $this->holidayAttendanceStatusId();

                $created = [];
                $cursor = strtotime($fromDate);
                $endTs = strtotime($toDate);
                $statusName = (string) DB::table('attendance_status')->where('id', $attendanceStatus)->value('name');

                while ($cursor <= $endTs) {
                    $dateValue = date('Y-m-d', $cursor);
                    $dateObj = new DateTime($dateValue);

                    $row = new Weekendcalendar();
                    $row->user_id = $userId;
                    $row->session_id = $sessionId;
                    $row->branch_id = $branchId;
                    $row->month_id = (int) $dateObj->format('n');
                    $row->date = $dateValue;
                    $row->day = $dateObj->format('l');
                    $row->event_schedule = $eventText;
                    $row->event_title = $eventTitle;
                    $row->event_description = $eventDescription;
                    $row->attendance_status = $attendanceStatus;
                    $msgCols = $this->weekendCalendarMessagingColumnMap();
                    if ($msgCols['enabled']) {
                        $row->auto_message_enabled = $messageEnabled;
                    }
                    if ($msgCols['services']) {
                        $row->message_services = $messageServices;
                    }
                    if ($msgCols['roles']) {
                        $row->notification_role_ids = $notificationRoles;
                    }
                    if ($msgCols['text']) {
                        $row->auto_message_text = $messageText;
                    }
                    $row->save();

                    $this->syncWeekendDateMessageMeta($sessionId, $branchId, $dateValue, [
                        'auto_message_enabled' => $messageEnabled,
                        'message_services' => $messageServices,
                        'notification_role_ids' => $notificationRoles,
                        'auto_message_text' => $messageText,
                    ]);

                    $created[] = [
                        'id' => (int) $row->id,
                        'date' => $dateValue,
                        'event_title' => $eventTitle,
                        'event_description' => $eventDescription,
                        'event_schedule' => $eventText,
                        'attendance_status' => $attendanceStatus,
                        'attendance_status_name' => $statusName,
                        'auto_message_enabled' => $messageEnabled,
                        'message_services' => $messageServices,
                        'auto_message_text' => $messageText,
                    ];
                    $cursor = strtotime('+1 day', $cursor);
                }

                if ($request->ajax()) {
                    return response()->json([
                        'ok' => true,
                        'message' => 'Calendar event saved successfully.',
                        'dates' => array_column($created, 'date'),
                        'items' => $created,
                    ]);
                }

                return redirect()->to('add_weekend?month=' . (int) date('n', strtotime($fromDate)) . '&year=' . (int) date('Y', strtotime($fromDate)))
                    ->with('message', 'Holiday saved successfully in the academic calendar.');
            }
        }

        $calendarMonth = (int) ($request->input('month') ?: date('n'));
        $calendarYear = (int) ($request->input('year') ?: date('Y'));
        if ($calendarMonth < 1 || $calendarMonth > 12) {
            $calendarMonth = (int) date('n');
        }
        if ($calendarYear < 2000 || $calendarYear > 2100) {
            $calendarYear = (int) date('Y');
        }

        $monthStart = sprintf('%04d-%02d-01', $calendarYear, $calendarMonth);
        $monthEnd = date('Y-m-t', strtotime($monthStart));

        $calendarEvents = $this->buildWeekendEventsByDate($sessionId, $branchId, $monthStart, $monthEnd);

        $monthName = date('F', strtotime($monthStart));
        $notificationRoles = DB::table('role')->whereNull('deleted_at')->orderBy('id')->get(['id', 'name']);

        return view('master.Weekendcalendar.add', compact(
            'calendarMonth',
            'calendarYear',
            'calendarEvents',
            'monthName',
            'notificationRoles'
        ));
    }

    public function academicCalendar(Request $request)
    {
        $data = Weekendcalendar::select('weekendcalendar.*', 'attendance_status.name as attendance_status_name')
            ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.session_id', Session::get('session_id'))
            ->where('weekendcalendar.branch_id', Session::get('branch_id'))
            ->orderBy('weekendcalendar.date', 'ASC')
            ->orderBy('weekendcalendar.id', 'ASC')
            ->get();

        return response()->json(['data' => $data]);
    }

}
