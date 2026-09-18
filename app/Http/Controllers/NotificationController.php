<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationToken;
use Illuminate\Http\Request;
use Session;
use Helper;

class NotificationController extends Controller
{
    public function notification(Request $request)
    {
        $tokens = NotificationToken::where('platform', 'android')
            ->pluck('device_token')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $result = Helper::sendNotification(
            'Hello World!',
            'This is a notification with image and icon.',
            'firebase',
            $tokens,
            'https://demo3.rusoft.in/schoolimage/setting/left_logo/17502424226852947652a461748931241_xbv2FRwzun.jpeg',
            'https://rukmanisoftware.com/public/assets/img/header-logo.png',
            ['customKey' => 'customValue']
        );

        return response()->json($result, !empty($result['success']) ? 200 : 502);
    }

    public function notificationFatch(Request $request)
    {
        if ($request->has('hide_id')) {
            Notification::where('id', $request->hide_id)
                ->where('admission_id', Session::get('id'))
                ->update(['show_status' => 0]);
        }

        $notifications = Notification::where('admission_id', Session::get('id'))
            ->where('show_status', 1)
            ->orderBy('message_seen', 'ASC')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        return view('notifications.notification_list', compact('notifications'));
    }

    public function notificationDetailStu(Request $request, $id)
    {
        Notification::where('id', $id)
            ->where('admission_id', Session::get('id'))
            ->update(['message_seen' => 1]);

        $notification = Notification::find($id);
        if (!$notification) {
            abort(404, 'Notification not found');
        }

        return view('notifications.notification_details', compact('notification'));
    }
}
