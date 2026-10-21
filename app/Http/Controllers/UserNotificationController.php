<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\SupportComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserNotificationController extends Controller
{
    private function notifications(bool $includeHidden = false)
    {
        abort_if((int) Session::get('role_id') === 3, 403);

        $query = Notification::query()
            ->where('user_id', (int) Session::get('id'))
            ->where('branch_id', (int) Session::get('branch_id'))
            ->where('session_id', (int) Session::get('session_id'));

        if (!$includeHidden) {
            $query->where('show_status', 1);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $filter = strtolower(trim((string) $request->query('filter', 'all')));
        if (!in_array($filter, ['all', 'read', 'unread'], true)) {
            $filter = 'all';
        }

        $type = strtolower(trim((string) $request->query('type', 'all')));
        if (!in_array($type, ['all', 'notice', 'approval', 'complaint', 'system'], true)) {
            $type = 'all';
        }

        $search = trim((string) $request->query('q', ''));

        // Compute independent counts across all visible notifications
        $baseCountQuery = $this->notifications();
        $totalCount = (int) (clone $baseCountQuery)->count();
        $unreadCount = (int) (clone $baseCountQuery)->where('message_seen', 0)->count();
        $readCount = max(0, $totalCount - $unreadCount);
        $noticeCount = (int) (clone $baseCountQuery)->whereIn('type', ['notice', 'notice_approved', 'notice_rejected'])->count();
        $approvalCount = (int) (clone $baseCountQuery)->where('type', 'notice_approval_request')->count();
        $complaintCount = (int) (clone $baseCountQuery)->whereIn('type', ['complaint_admin', 'complaint'])->count();
        $systemCount = max(0, $totalCount - ($noticeCount + $approvalCount + $complaintCount));

        $query = $this->notifications()->with('managedNotice.recipients');

        if ($filter === 'read') {
            $query->where('message_seen', 1);
        } elseif ($filter === 'unread') {
            $query->where('message_seen', 0);
        }

        if ($type === 'notice') {
            $query->whereIn('type', ['notice', 'notice_approved', 'notice_rejected']);
        } elseif ($type === 'approval') {
            $query->where('type', 'notice_approval_request');
        } elseif ($type === 'complaint') {
            $query->whereIn('type', ['complaint_admin', 'complaint']);
        } elseif ($type === 'system') {
            $query->whereNotIn('type', ['notice', 'notice_approved', 'notice_rejected', 'notice_approval_request', 'complaint_admin', 'complaint']);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('source_key', 'like', "%{$search}%");
            });
        }

        $notifications = $query
            ->orderBy('message_seen')
            ->latest('id')
            ->paginate(25)
            ->appends([
                'filter' => $filter,
                'type' => $type,
                'q' => $search,
            ]);

        $complaintIds = $notifications->getCollection()
            ->filter(function ($notification) {
                return in_array($notification->type, ['complaint_admin', 'complaint'], true);
            })
            ->map(function ($notification) {
                return preg_match('/^complaint:(\d+):/', (string) $notification->source_key, $matches)
                    ? (int) $matches[1]
                    : null;
            })
            ->filter()
            ->unique()
            ->values();

        $complaints = SupportComplaint::with(['student.ClassTypes'])
            ->whereIn('id', $complaintIds)
            ->where('branch_id', (int) Session::get('branch_id'))
            ->where('session_id', (int) Session::get('session_id'))
            ->get()
            ->keyBy('id');

        $notifications->getCollection()->each(function ($notification) use ($complaints) {
            $complaintId = preg_match('/^complaint:(\d+):/', (string) $notification->source_key, $matches)
                ? (int) $matches[1]
                : null;
            $notification->setRelation('complaintContext', $complaintId ? $complaints->get($complaintId) : null);
        });

        return \Helper::view('notifications.user_index', compact(
            'notifications',
            'filter',
            'type',
            'search',
            'unreadCount',
            'totalCount',
            'readCount',
            'noticeCount',
            'approvalCount',
            'complaintCount',
            'systemCount'
        ));
    }

    public function markRead(Request $request, int $id)
    {
        $notification = $this->notifications()->where('id', $id)->firstOrFail();
        if ((int) $notification->message_seen !== 1) {
            $notification->update(['message_seen' => 1]);
        }

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'id' => $id, 'message_seen' => 1])
            : redirect()->back()->with('message', 'Notification marked as read.');
    }

    public function markUnread(Request $request, int $id)
    {
        $notification = $this->notifications()->where('id', $id)->firstOrFail();
        if ((int) $notification->message_seen !== 0) {
            $notification->update(['message_seen' => 0]);
        }

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'id' => $id, 'message_seen' => 0])
            : redirect()->back()->with('message', 'Notification marked as unread.');
    }

    public function destroy(Request $request, int $id)
    {
        $notification = $this->notifications()->where('id', $id)->firstOrFail();
        $notification->show_status = 0;
        $notification->save();
        $notification->delete();

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'id' => $id])
            : redirect()->back()->with('message', 'Notification deleted successfully.');
    }

    public function markAllRead()
    {
        $updated = $this->notifications()->where('message_seen', 0)->update([
            'message_seen' => 1,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with(
            'message',
            $updated ? 'All notifications marked as read.' : 'No unread notifications found.'
        );
    }

    public function clearAll()
    {
        foreach ($this->notifications()->get() as $notification) {
            $notification->show_status = 0;
            $notification->message_seen = 1;
            $notification->save();
            $notification->delete();
        }

        return redirect('user-notifications')->with('message', 'All notifications cleared.');
    }
}
