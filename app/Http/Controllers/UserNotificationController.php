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

        $query = $this->notifications()->with('managedNotice.recipients');
        if ($filter === 'read') {
            $query->where('message_seen', 1);
        } elseif ($filter === 'unread') {
            $query->where('message_seen', 0);
        }

        $notifications = $query
            ->orderBy('message_seen')
            ->latest('id')
            ->paginate(20)
            ->appends(['filter' => $filter]);

        $complaintIds = $notifications->getCollection()
            ->filter(function ($notification) {
                return $notification->type === 'complaint_admin';
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

        $unreadCount = (int) $this->notifications()->where('message_seen', 0)->count();

        return view('notifications.user_index', compact('notifications', 'filter', 'unreadCount'));
    }

    public function markRead(Request $request, int $id)
    {
        $notification = $this->notifications()->where('id', $id)->firstOrFail();
        if ((int) $notification->message_seen !== 1) {
            $notification->update(['message_seen' => 1]);
        }

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : redirect()->back()->with('message', 'Notification marked as read.');
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
