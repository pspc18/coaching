<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\ManagedNotice;
use App\Models\Notification;
use App\Models\Master\Role;
use App\Models\User;
use App\Services\NoticeNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NoticeManagementController extends Controller
{
    public static function clearCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        if ($branchId && $sessionId) {
            $verKey = "managed_notices_ver_{$branchId}_{$sessionId}";
            $cur = (int) Cache::get($verKey, 1);
            Cache::put($verKey, $cur + 1, 86400 * 30);
            \App\Helpers\helper::clearNoticeCache($branchId, $sessionId);
            \App\Helpers\helper::clearDashboardCache($branchId, $sessionId);
        }
    }

    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $roleId = (int) Session::get('role_id');
        $userId = (int) Session::get('id');
        $isAdmin = ($roleId === 1);

        if ((int) $request->input('refresh', 0) === 1) {
            self::clearCache($branchId, $sessionId);
            return redirect('notice-management')->with('message', 'Notice cache refreshed successfully.');
        }

        $verKey = "managed_notices_ver_{$branchId}_{$sessionId}";
        $ver = Cache::get($verKey, 1);

        $countsKey = "managed_notices_counts_{$branchId}_{$sessionId}_v{$ver}_role{$roleId}_user" . ($isAdmin ? 'all' : $userId);
        $counts = Cache::remember($countsKey, 600, function () use ($sessionId, $branchId, $isAdmin, $userId) {
            $baseQuery = ManagedNotice::where('session_id', $sessionId)
                ->where('branch_id', $branchId);

            if (!$isAdmin) {
                $baseQuery->where('created_by', $userId);
            }

            return [
                'all' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            ];
        });

        $title = trim((string) $request->input('title', $request->input('search', '')));
        $status = $request->input('status', '');
        $audience = $request->input('audience_type', $request->input('audience', ''));
        $fromDate = $request->input('from_date', '');
        $toDate = $request->input('to_date', '');
        $creator = trim((string) $request->input('creator', ''));
        $perPage = $request->input('per_page', 25);
        $page = max(1, (int) $request->input('page', 1));

        $query = ManagedNotice::with(['recipients', 'creator', 'reviewer'])
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId);

        if (!$isAdmin) {
            $query->where('created_by', $userId);
        }

        if (!empty($title)) {
            $query->where(function ($q) use ($title) {
                $q->where('title', 'LIKE', "%{$title}%")
                  ->orWhere('message', 'LIKE', "%{$title}%");
            });
        }

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        if (in_array($audience, ['role', 'class', 'specific'], true)) {
            $query->where('audience_type', $audience);
        }

        if (!empty($fromDate)) {
            $query->whereDate('to_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('from_date', '<=', $toDate);
        }

        if (!empty($creator)) {
            $query->whereHas('creator', function ($q) use ($creator) {
                $q->where(DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))"), 'LIKE', "%{$creator}%")
                  ->orWhere('userName', 'LIKE', "%{$creator}%");
            });
        }

        $totalCount = $query->count();

        if ($perPage === 'all' || (int) $perPage <= 0) {
            $perPageVal = $totalCount > 0 ? $totalCount : 25;
            $lastPage = 1;
            $currentPage = 1;
            $startIndex = 0;
            $notices = $query->latest()->get();
        } else {
            $perPageVal = max(1, (int) $perPage);
            $lastPage = max(1, (int) ceil($totalCount / $perPageVal));
            $currentPage = min($page, $lastPage);
            $startIndex = ($currentPage - 1) * $perPageVal;
            $notices = $query->latest()->skip($startIndex)->take($perPageVal)->get();
        }

        if ($request->ajax() || (int) $request->input('ajax', 0) === 1) {
            $html = view('master.notice_management.table_rows', [
                'notices' => $notices,
                'data' => $notices,
                'startIndex' => $startIndex,
                'isAdmin' => $isAdmin,
            ])->render();

            return response()->json([
                'status' => true,
                'html' => $html,
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total_count' => $totalCount,
                'start_index' => $startIndex,
                'counts' => $counts,
            ]);
        }

        return view('master.notice_management.index', [
            'notices' => $notices,
            'data' => $notices,
            'counts' => $counts,
            'totalCount' => $totalCount,
            'startIndex' => $startIndex,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'perPage' => $perPage,
            'status' => $status,
            'audience' => $audience,
            'search' => [
                'title' => $title,
                'status' => $status,
                'audience_type' => $audience,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'creator' => $creator,
            ],
            'isAdmin' => $isAdmin,
        ]);
    }

    public function create()
    {
        $scope = function ($query) {
            return $query->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'));
        };

        return view('master.notice_management.create', [
            'roles' => Role::whereNull('deleted_at')->orderBy('name')->get(),
            'classes' => $scope(ClassType::query())->orderBy('orderBy')->get(),
            'users' => $scope(User::query())->where('status', 1)->orderBy('first_name')->get(),
            'students' => $scope(Admission::query())->where('status', 1)->orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:10240',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'audience_type' => 'required|in:role,class,specific',
            'role_ids' => 'required_if:audience_type,role|array',
            'class_ids' => 'required_if:audience_type,class|array',
            'specific_recipients' => 'required_if:audience_type,specific|array',
        ]);

        $isAdmin = (int) Session::get('role_id') === 1;

        $attachment = $this->storeAttachment($request);
        $notice = null;

        try {
        DB::transaction(function () use ($request, $isAdmin, $attachment, &$notice) {
            $notice = ManagedNotice::create([
                'session_id' => Session::get('session_id'),
                'branch_id' => Session::get('branch_id'),
                'created_by' => Session::get('id'),
                'title' => $request->title,
                'message' => $request->message,
                'attachment_path' => $attachment['path'],
                'attachment_name' => $attachment['name'],
                'attachment_size' => $attachment['size'],
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'audience_type' => $request->audience_type,
                'status' => $isAdmin ? 'approved' : 'pending',
                'reviewed_by' => $isAdmin ? Session::get('id') : null,
                'review_notes' => $isAdmin ? 'Published directly by administrator.' : null,
                'reviewed_at' => $isAdmin ? Carbon::now() : null,
                'published_at' => $isAdmin ? Carbon::now() : null,
            ]);

            $recipients = $this->resolveRecipients($request);
            if (empty($recipients)) {
                abort(422, 'No active recipient matched the selected audience.');
            }

            foreach ($recipients as $recipient) {
                $notice->recipients()->create($recipient);
            }
        });
        } catch (\Throwable $exception) {
            if ($attachment['path']) {
                Storage::disk('local')->delete($attachment['path']);
            }
            throw $exception;
        }

        $delivery = null;
        if ($isAdmin && $notice) {
            try {
                $delivery = app(NoticeNotificationService::class)->dispatch($notice);
            } catch (\Throwable $exception) {
                Log::error('Admin notice saved but notification delivery failed.', [
                    'notice_id' => $notice->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        } elseif ($notice) {
            try {
                $delivery = app(NoticeNotificationService::class)->dispatchApprovalRequest($notice);
            } catch (\Throwable $exception) {
                Log::error('Notice saved but admin approval-request delivery failed.', [
                    'notice_id' => $notice->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $message = $isAdmin
            ? 'Notice approved and published. '.($delivery ? $delivery['created'].' notification messages created.' : 'Notification delivery is pending due to an error.')
            : 'Notice submitted for approval. '.($delivery ? $delivery['created'].' admin notification messages created.' : 'Admin notification delivery is pending due to an error.');

        self::clearCache($request->session()->get('branch_id'), $request->session()->get('session_id'));

        return redirect('notice-management')->with('message', $message);
    }

    public function review(Request $request, $id)
    {
        abort_unless((int) Session::get('role_id') === 1, 403);

        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'review_notes' => 'required|string|max:2000',
            'return_to' => 'nullable|in:user-notifications',
        ]);

        $notice = ManagedNotice::where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->where('status', 'pending')
            ->findOrFail($id);

        $notice->update([
            'status' => $request->decision,
            'reviewed_by' => Session::get('id'),
            'review_notes' => $request->review_notes,
            'reviewed_at' => Carbon::now(),
            'published_at' => $request->decision === 'approved' ? Carbon::now() : null,
        ]);

        Notification::where('managed_notice_id', $notice->id)
            ->where('type', 'notice_approval_request')
            ->where('user_id', (int) Session::get('id'))
            ->update([
                'message_seen' => 1,
                'updated_at' => now(),
            ]);

        $delivery = null;
        $reviewDelivery = null;
        try {
            $reviewDelivery = app(NoticeNotificationService::class)
                ->dispatchReviewOutcome($notice->fresh());
        } catch (\Throwable $exception) {
            Log::error('Notice review outcome notification failed.', [
                'notice_id' => $notice->id,
                'status' => $request->decision,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($request->decision === 'approved') {
            try {
                $delivery = app(NoticeNotificationService::class)->dispatch($notice->fresh('recipients'));
            } catch (\Throwable $exception) {
                Log::error('Approved notice notification delivery failed.', [
                    'notice_id' => $notice->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        self::clearCache(Session::get('branch_id'), Session::get('session_id'));

        $returnUrl = $request->return_to === 'user-notifications'
            ? 'user-notifications'
            : 'notice-management';

        return redirect($returnUrl)->with(
            'message',
            $request->decision === 'approved'
                ? 'Notice approved and published. Creator notified; '.($delivery ? $delivery['created'].' recipient notification messages created.' : 'recipient delivery is pending due to an error.')
                : 'Notice rejected with notes. Creator notification '.($reviewDelivery ? 'created.' : 'is pending due to an error.').' No recipient notification was created.'
        );
    }

    public function destroy($id)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $roleId = (int) Session::get('role_id');
        $userId = (int) Session::get('id');
        $isAdmin = ($roleId === 1);

        $notice = ManagedNotice::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->findOrFail($id);

        if (!$isAdmin && (int)$notice->created_by !== $userId) {
            abort(403, 'You are not authorized to delete this notice.');
        }

        if ($notice->attachment_path && Storage::disk('local')->exists($notice->attachment_path)) {
            Storage::disk('local')->delete($notice->attachment_path);
        }

        $notice->recipients()->delete();

        Notification::where('managed_notice_id', $notice->id)->delete();

        $notice->delete();

        self::clearCache($branchId, $sessionId);

        return redirect('notice-management')->with('message', 'Notice deleted successfully and cache cleared.');
    }

    public function attachment($id)
    {
        $notice = ManagedNotice::with('recipients')
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->findOrFail($id);

        $isAdmin = (int) Session::get('role_id') === 1;
        $isCreator = (int) $notice->created_by === (int) Session::get('id');
        $recipientType = (int) Session::get('role_id') === 3 ? 'student' : 'user';
        $isApprovedRecipient = $notice->status === 'approved' && $notice->recipients
            ->contains(function ($recipient) use ($recipientType) {
                return $recipient->recipient_type === $recipientType
                    && (int) $recipient->recipient_id === (int) Session::get('id');
            });

        abort_unless($isAdmin || $isCreator || $isApprovedRecipient, 403);
        abort_unless($notice->attachment_path && Storage::disk('local')->exists($notice->attachment_path), 404);

        return Storage::disk('local')->download(
            $notice->attachment_path,
            $notice->attachment_name ?: 'notice-attachment.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    private function storeAttachment(Request $request)
    {
        if (!$request->hasFile('attachment')) {
            return ['path' => null, 'name' => null, 'size' => null];
        }

        $file = $request->file('attachment');
        $safeOriginalName = Str::limit(basename($file->getClientOriginalName()), 240, '');
        $storedName = Str::uuid().'.pdf';
        $directory = 'managed-notices/'.Session::get('branch_id').'/'.date('Y/m');

        return [
            'path' => $file->storeAs($directory, $storedName, 'local'),
            'name' => $safeOriginalName,
            'size' => $file->getSize(),
        ];
    }

    private function resolveRecipients(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $resolved = [];

        $addUsers = function ($query) use (&$resolved) {
            foreach ($query->get() as $user) {
                $role = Role::find($user->role_id);
                $resolved['user-'.$user->id] = [
                    'recipient_type' => 'user',
                    'recipient_id' => $user->id,
                    'recipient_name' => trim($user->first_name.' '.$user->last_name),
                    'role_name' => $role->name ?? 'Staff',
                    'class_name' => null,
                    'mobile' => $user->mobile,
                ];
            }
        };

        $addStudents = function ($query) use (&$resolved) {
            $classNames = ClassType::pluck('name', 'id');
            foreach ($query->get() as $student) {
                $resolved['student-'.$student->id] = [
                    'recipient_type' => 'student',
                    'recipient_id' => $student->id,
                    'recipient_name' => trim($student->first_name.' '.$student->last_name),
                    'role_name' => 'Student',
                    'class_name' => $classNames[$student->class_type_id] ?? null,
                    'mobile' => $student->mobile ?: $student->father_mobile,
                ];
            }
        };

        if ($request->audience_type === 'role') {
            $roleIds = array_map('intval', $request->role_ids ?: []);
            $staffRoleIds = array_values(array_diff($roleIds, [3]));
            if ($staffRoleIds) {
                $addUsers(User::where('session_id', $sessionId)->where('branch_id', $branchId)
                    ->where('status', 1)->whereIn('role_id', $staffRoleIds));
            }
            if (in_array(3, $roleIds, true)) {
                $addStudents(Admission::where('session_id', $sessionId)->where('branch_id', $branchId)
                    ->where('status', 1));
            }
        } elseif ($request->audience_type === 'class') {
            $addStudents(Admission::where('session_id', $sessionId)->where('branch_id', $branchId)
                ->where('status', 1)->whereIn('class_type_id', array_map('intval', $request->class_ids ?: [])));
        } else {
            $userIds = [];
            $studentIds = [];
            foreach ($request->specific_recipients ?: [] as $value) {
                list($type, $id) = array_pad(explode(':', $value, 2), 2, null);
                if ($type === 'user') $userIds[] = (int) $id;
                if ($type === 'student') $studentIds[] = (int) $id;
            }
            if ($userIds) {
                $addUsers(User::where('session_id', $sessionId)->where('branch_id', $branchId)
                    ->where('status', 1)->whereIn('id', $userIds));
            }
            if ($studentIds) {
                $addStudents(Admission::where('session_id', $sessionId)->where('branch_id', $branchId)
                    ->where('status', 1)->whereIn('id', $studentIds));
            }
        }

        return array_values($resolved);
    }
}
