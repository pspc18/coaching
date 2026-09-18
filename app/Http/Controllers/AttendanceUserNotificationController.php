<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Session;

class AttendanceUserNotificationController extends Controller
{
    private ?array $notificationColumns = null;

    private function notificationColumns(): array
    {
        if ($this->notificationColumns !== null) {
            return $this->notificationColumns;
        }

        $tableExists = Schema::hasTable('notifications');
        $this->notificationColumns = [
            'table' => $tableExists,
            'attendance_unique_id' => $tableExists && Schema::hasColumn('notifications', 'attendance_unique_id'),
        ];

        return $this->notificationColumns;
    }

    private function resolveCurrentContext(): ?array
    {
        $roleId = (int) Session::get('role_id');
        $sessionUserId = (int) Session::get('id');
        $sessionBranchId = (int) Session::get('branch_id');
        $sessionSessionId = (int) Session::get('session_id');

        $attendanceUniqueId = trim((string) Session::get('attendance_unique_id'));
        $branchId = $sessionBranchId;
        $sessionId = $sessionSessionId;
        $admissionId = null;
        $entityType = $roleId === 3 ? 'student' : 'staff';

        if ($roleId === 3) {
            $student = Admission::select('id', 'attendance_unique_id', 'branch_id', 'session_id')
                ->where('id', $sessionUserId)
                ->first();

            if ($student) {
                $attendanceUniqueId = trim((string) ($student->attendance_unique_id ?? $attendanceUniqueId));
                $branchId = (int) ($student->branch_id ?? $branchId);
                $sessionId = (int) ($student->session_id ?? $sessionId);
                $admissionId = (int) $student->id;
            }
        } else {
            $user = User::select('id', 'attendance_unique_id', 'branch_id', 'session_id')
                ->where('id', $sessionUserId)
                ->first();

            if ($user) {
                $attendanceUniqueId = trim((string) ($user->attendance_unique_id ?? $attendanceUniqueId));
                $branchId = (int) ($user->branch_id ?? $branchId);
                $sessionId = (int) ($user->session_id ?? $sessionId);
            }
        }

        if ($attendanceUniqueId === '') {
            return null;
        }

        return [
            'attendance_unique_id' => $attendanceUniqueId,
            'branch_id' => $branchId > 0 ? $branchId : null,
            'session_id' => $sessionId > 0 ? $sessionId : null,
            'admission_id' => $admissionId,
            'entity_type' => $entityType,
        ];
    }

    private function queryForContext(array $ctx, bool $includeHidden = false)
    {
        $columns = $this->notificationColumns();
        $query = Notification::query();

        if (!$includeHidden) {
            $query->where('show_status', 1);
        }

        if (!empty($columns['attendance_unique_id'])) {
            $query->where('attendance_unique_id', (string) $ctx['attendance_unique_id']);
        } elseif (!empty($ctx['admission_id'])) {
            $query->where('admission_id', (int) $ctx['admission_id']);
        } else {
            $query->whereRaw('1 = 0');
        }

        if (!empty($ctx['branch_id'])) {
            $query->where('branch_id', (int) $ctx['branch_id']);
        }
        if (!empty($ctx['session_id'])) {
            $query->where('session_id', (int) $ctx['session_id']);
        }

        return $query;
    }

    private function formatRow(Notification $row): array
    {
        return [
            'id' => (int) $row->id,
            'title' => (string) ($row->title ?? ''),
            'content' => (string) ($row->content ?? ''),
            'type' => (string) ($row->type ?? ''),
            'attendance_unique_id' => (string) ($row->attendance_unique_id ?? ''),
            'entity_type' => (string) ($row->entity_type ?? ''),
            'message_seen' => (int) ($row->message_seen ?? 0),
            'is_read' => (int) ($row->message_seen ?? 0) === 1,
            'show_status' => (int) ($row->show_status ?? 1),
            'created_at' => optional($row->created_at)->toDateTimeString(),
            'updated_at' => optional($row->updated_at)->toDateTimeString(),
        ];
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->ajax()
            || str_contains(strtolower((string) $request->header('accept')), 'application/json');
    }

    public function viewAll(Request $request)
    {
        $ctx = $this->resolveCurrentContext();
        if (!$ctx) {
            return redirect()->back()->with('error', 'Attendance unique id not found for current user.');
        }

        $type = trim((string) $request->input('type', ''));
        $readStatus = strtolower(trim((string) $request->input('read_status', 'all')));
        $search = trim((string) $request->input('q', ''));
        $perPage = (int) $request->input('per_page', 20);
        if ($perPage < 5) {
            $perPage = 20;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $baseQuery = $this->queryForContext($ctx, false);
        $types = (clone $baseQuery)
            ->select('type')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values();

        $query = $this->queryForContext($ctx, false);
        if ($type !== '') {
            $query->where('type', $type);
        }
        if ($readStatus === 'read') {
            $query->where('message_seen', 1);
        } elseif ($readStatus === 'unread') {
            $query->where('message_seen', 0);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $notifications = $query
            ->orderBy('message_seen', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $unreadCount = (int) $this->queryForContext($ctx, false)->where('message_seen', 0)->count();

        return view('notifications.attendance_index', [
            'notifications' => $notifications,
            'types' => $types,
            'filters' => [
                'type' => $type,
                'read_status' => $readStatus,
                'q' => $search,
                'per_page' => $perPage,
            ],
            'unreadCount' => $unreadCount,
            'attendanceUniqueId' => (string) $ctx['attendance_unique_id'],
        ]);
    }

    public function index(Request $request)
    {
        $ctx = $this->resolveCurrentContext();
        if (!$ctx) {
            return response()->json([
                'ok' => false,
                'message' => 'Attendance unique id not found for current user.',
            ], 422);
        }

        $limit = (int) $request->input('limit', 20);
        if ($limit < 1) {
            $limit = 20;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $includeHidden = (int) $request->input('include_hidden', 0) === 1;
        $type = trim((string) $request->input('type', ''));

        $listQuery = $this->queryForContext($ctx, $includeHidden);
        if ($type !== '') {
            $listQuery->where('type', $type);
        }

        $notifications = $listQuery
            ->orderBy('message_seen', 'asc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $unreadCountQuery = $this->queryForContext($ctx, false)->where('message_seen', 0);
        if ($type !== '') {
            $unreadCountQuery->where('type', $type);
        }

        return response()->json([
            'ok' => true,
            'attendance_unique_id' => (string) $ctx['attendance_unique_id'],
            'unread_count' => (int) $unreadCountQuery->count(),
            'total_returned' => (int) $notifications->count(),
            'items' => $notifications->map(function ($row) {
                return $this->formatRow($row);
            })->values(),
        ]);
    }

    public function markRead(Request $request, int $id)
    {
        $ctx = $this->resolveCurrentContext();
        if (!$ctx) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Attendance unique id not found.'], 422);
            }
            return redirect()->back()->with('error', 'Attendance unique id not found.');
        }

        $row = $this->queryForContext($ctx, true)->where('id', $id)->first();
        if (!$row) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Notification not found.'], 404);
            }
            return redirect()->back()->with('error', 'Notification not found.');
        }

        $row->message_seen = 1;
        $row->save();

        if ($this->wantsJson($request)) {
            return response()->json(['ok' => true, 'message' => 'Notification marked as read.']);
        }

        return redirect()->back()->with('message', 'Notification marked as read.');
    }

    public function markUnread(Request $request, int $id)
    {
        $ctx = $this->resolveCurrentContext();
        if (!$ctx) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Attendance unique id not found.'], 422);
            }
            return redirect()->back()->with('error', 'Attendance unique id not found.');
        }

        $row = $this->queryForContext($ctx, true)->where('id', $id)->first();
        if (!$row) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Notification not found.'], 404);
            }
            return redirect()->back()->with('error', 'Notification not found.');
        }

        $row->message_seen = 0;
        $row->save();

        if ($this->wantsJson($request)) {
            return response()->json(['ok' => true, 'message' => 'Notification marked as unread.']);
        }

        return redirect()->back()->with('message', 'Notification marked as unread.');
    }

    public function markAllRead(Request $request)
    {
        $ctx = $this->resolveCurrentContext();
        if (!$ctx) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Attendance unique id not found.'], 422);
            }
            return redirect()->back()->with('error', 'Attendance unique id not found.');
        }

        $updated = $this->queryForContext($ctx, false)
            ->where('message_seen', 0)
            ->update([
                'message_seen' => 1,
                'updated_at' => now(),
            ]);

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'All notifications marked as read.',
                'updated' => (int) $updated,
            ]);
        }

        return redirect()->back()->with('message', 'All notifications marked as read.');
    }

    public function remove(Request $request, int $id)
    {
        $ctx = $this->resolveCurrentContext();
        if (!$ctx) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Attendance unique id not found.'], 422);
            }
            return redirect()->back()->with('error', 'Attendance unique id not found.');
        }

        $row = $this->queryForContext($ctx, true)->where('id', $id)->first();
        if (!$row) {
            if ($this->wantsJson($request)) {
                return response()->json(['ok' => false, 'message' => 'Notification not found.'], 404);
            }
            return redirect()->back()->with('error', 'Notification not found.');
        }

        $row->show_status = 0;
        $row->message_seen = 1;
        $row->save();
        $row->delete();

        if ($this->wantsJson($request)) {
            return response()->json(['ok' => true, 'message' => 'Notification deleted successfully.']);
        }

        return redirect()->back()->with('message', 'Notification deleted successfully.');
    }
}
