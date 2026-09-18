<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Validator; 
use App\Models\Admission;
use App\Models\NotificationToken;
use App\Models\Notification;
use App\Models\User;
use Session;
use Carbon\Carbon;
use Str;
use Helper;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StudentNotificationService;

class NotificationController extends Controller

{
        private function notificationsService(): StudentNotificationService
        {
            return app(StudentNotificationService::class);
        }

        private function normalizeCategory(Request $request): string
        {
            $category = strtolower(trim((string) $request->input('category', $request->query('category', 'all'))));
            $legacyType = strtolower(trim((string) $request->query('type', '')));

            if ($category === 'all' && $legacyType === 'notice') {
                $category = 'notice';
            }

            if (!in_array($category, $this->notificationsService()->allowedCategories(), true)) {
                $category = 'all';
            }

            return $category;
        }

        private function notificationQuery(string $category, bool $includeHidden = false)
        {
            $query = $this->notificationsService()->baseQuery((int) Session::get('id'), $includeHidden);

            return $this->notificationsService()->applyCategory($query, $category);
        }

        public function notificationFatch(Request $request)
        {
            $filter = strtolower(trim((string) $request->query('filter', 'all')));
            if (!in_array($filter, ['all', 'read', 'unread'], true)) {
                $filter = 'all';
            }

            $category = $this->normalizeCategory($request);

            $query = $this->notificationQuery($category);
            if ($filter === 'read') {
                $query->where('message_seen', 1);
            } elseif ($filter === 'unread') {
                $query->where('message_seen', 0);
            }

            $notifications = $query
                ->orderBy('message_seen', 'asc')
                ->orderByDesc('id')
                ->paginate(20)
                ->appends(['filter' => $filter, 'category' => $category]);

            $unreadCount = (int) $this->notificationQuery($category)->where('message_seen', 0)->count();
            $categoryCounts = $this->notificationsService()->counts((int) Session::get('id'));

            return view('student_login.notification.notification_list', compact(
                'notifications',
                'filter',
                'unreadCount',
                'category',
                'categoryCounts'
            ));
        }

        public function markAttendanceRead(Request $request, int $id)
        {
            $notification = $this->notificationQuery($this->normalizeCategory($request), true)
                ->where('id', $id)
                ->firstOrFail();

            if ((int) $notification->message_seen !== 1) {
                $notification->message_seen = 1;
                $notification->save();
            }

            return response()->json(['ok' => true]);
        }

        public function markAllAttendanceRead(Request $request)
        {
            $query = $this->notificationQuery($this->normalizeCategory($request), true);
            $updated = $query
                ->where('message_seen', 0)
                ->update([
                    'message_seen' => 1,
                    'updated_at' => now(),
                ]);

            return redirect()->back()->with(
                'message',
                ((int) $updated) > 0
                    ? 'All notifications marked as read.'
                    : 'No unread notifications found.'
            );
        }

        public function clearAllAttendanceNotifications(Request $request)
        {
            $query = $this->notificationQuery($this->normalizeCategory($request), true);
            $notifications = $query->get();

            foreach ($notifications as $notification) {
                $notification->show_status = 0;
                $notification->message_seen = 1;
                $notification->save();
                $notification->delete();
            }

            $redirectUrl = url('notificationFatchStudent');
            $category = $this->normalizeCategory($request);
            if ($category !== 'all') {
                $redirectUrl .= '?category='.$category;
            }

            return redirect()->to($redirectUrl)
                ->with('message', 'All notifications cleared.');
        }
        
        
        public function notificationDetailStu(Request $request , $id){
            
            $notification = $this->notificationQuery($this->normalizeCategory($request), true)
                ->where('id', $id)
                ->firstOrFail();
            $notification->update(['message_seen' => 1]);
            return view('student_login.notification.notification_details', compact('notification'));
        }


}
