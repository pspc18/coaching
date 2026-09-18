<?php

namespace App\Services;

use App\Models\ManagedNotice;
use App\Models\Admission;
use App\Models\Notification;
use App\Models\NotificationToken;
use App\Models\User;
use Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoticeNotificationService
{
    public function dispatchApprovalRequest(ManagedNotice $notice): array
    {
        $notice->loadMissing(['recipients', 'creator']);
        if ($notice->status !== 'pending') {
            return ['created' => 0, 'push_sent' => 0, 'push_failed' => 0, 'skipped' => true];
        }

        $creatorName = trim((string) optional($notice->creator)->first_name.' '.(string) optional($notice->creator)->last_name);
        if ($creatorName === '') {
            $creatorName = 'User #'.$notice->created_by;
        }

        $title = 'Notice Approval Required';
        $body = $creatorName.' submitted "'.$notice->title.'" for approval.'
            ."\nAudience: ".ucfirst((string) $notice->audience_type)
            ."\nRecipients: ".$notice->recipients->count();
        if ($notice->attachment_path) {
            $body .= "\nPDF attached: ".($notice->attachment_name ?: 'Notice attachment.pdf');
        }

        $admins = User::query()
            ->where('role_id', 1)
            ->where('status', 1)
            ->where('branch_id', $notice->branch_id)
            ->where('session_id', $notice->session_id)
            ->get();

        $created = 0;
        $pushSent = 0;
        $pushFailed = 0;

        foreach ($admins as $admin) {
            $notification = Notification::firstOrCreate(
                ['source_key' => 'notice:'.$notice->id.':approval-request:admin:'.$admin->id],
                [
                    'managed_notice_id' => $notice->id,
                    'title' => $title,
                    'content' => $body,
                    'type' => 'notice_approval_request',
                    'admission_id' => null,
                    'user_id' => $admin->id,
                    'device_token' => null,
                    'branch_id' => $notice->branch_id,
                    'session_id' => $notice->session_id,
                    'attachment_path' => $notice->attachment_path,
                    'attachment_name' => $notice->attachment_name,
                    'attachment_mime' => $notice->attachment_path ? 'application/pdf' : null,
                    'message_seen' => 0,
                    'show_status' => 1,
                ]
            );

            if (!$notification->wasRecentlyCreated) {
                continue;
            }

            $created++;
            try {
                $result = $this->sendPushByAttendanceUniqueId(
                    $title,
                    $body,
                    'user',
                    (int) $admin->id,
                    [
                        'type' => 'notice_approval_request',
                        'notification_type' => 'default',
                        'channel_id' => 'default',
                        'channelId' => 'default',
                        'notice_id' => (string) $notice->id,
                        'notice_status' => 'pending',
                        'created_by' => (string) $notice->created_by,
                        'creator_name' => $creatorName,
                        'audience_type' => (string) $notice->audience_type,
                        'recipient_count' => (string) $notice->recipients->count(),
                        'has_attachment' => $notice->attachment_path ? '1' : '0',
                        'attachment_type' => $notice->attachment_path ? 'application/pdf' : '',
                        'attachment_name' => (string) ($notice->attachment_name ?: ''),
                        'attachment_url' => $notice->attachment_path
                            ? url('notice-management/'.$notice->id.'/attachment')
                            : '',
                        'approval_url' => url('notice-management?status=pending'),
                    ]
                );

                if (!empty($result['success'])) {
                    $pushSent++;
                } else {
                    $pushFailed++;
                    Log::warning('Notice approval-request push failed.', [
                        'notice_id' => $notice->id,
                        'admin_id' => $admin->id,
                        'result' => $result,
                    ]);
                }
            } catch (\Throwable $exception) {
                $pushFailed++;
                Log::error('Notice approval-request push error.', [
                    'notice_id' => $notice->id,
                    'admin_id' => $admin->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return [
            'created' => $created,
            'push_sent' => $pushSent,
            'push_failed' => $pushFailed,
            'skipped' => false,
        ];
    }

    public function dispatchReviewOutcome(ManagedNotice $notice): array
    {
        if (!in_array($notice->status, ['approved', 'rejected'], true)) {
            return ['created' => 0, 'push_sent' => 0, 'push_failed' => 0, 'skipped' => true];
        }

        $approved = $notice->status === 'approved';
        $title = $approved ? 'Notice Approved' : 'Notice Rejected';
        $body = 'Your notice "'.$notice->title.'" has been '.($approved ? 'approved and published.' : 'rejected.');
        if (trim((string) $notice->review_notes) !== '') {
            $body .= "\n\nAdmin notes: ".trim((string) $notice->review_notes);
        }

        $sourceKey = 'notice:'.$notice->id.':review:'.$notice->status.':creator:'.$notice->created_by;
        $notification = Notification::firstOrCreate(
            ['source_key' => $sourceKey],
            [
                'managed_notice_id' => $notice->id,
                'title' => $title,
                'content' => $body,
                'type' => $approved ? 'notice_approved' : 'notice_rejected',
                'admission_id' => null,
                'user_id' => $notice->created_by,
                'device_token' => null,
                'branch_id' => $notice->branch_id,
                'session_id' => $notice->session_id,
                'attachment_path' => $notice->attachment_path,
                'attachment_name' => $notice->attachment_name,
                'attachment_mime' => $notice->attachment_path ? 'application/pdf' : null,
                'message_seen' => 0,
                'show_status' => 1,
            ]
        );

        // Only the request that creates the unique history record sends the
        // push, so retries cannot notify the creator twice.
        if (!$notification->wasRecentlyCreated) {
            return ['created' => 0, 'push_sent' => 0, 'push_failed' => 0, 'skipped' => true];
        }

        try {
            $result = $this->sendPushByAttendanceUniqueId(
                $title,
                $body,
                'user',
                (int) $notice->created_by,
                [
                    'type' => $approved ? 'notice_approved' : 'notice_rejected',
                    'notification_type' => 'default',
                    'channel_id' => 'default',
                    'channelId' => 'default',
                    'notice_id' => (string) $notice->id,
                    'notice_status' => $notice->status,
                    'review_notes' => (string) ($notice->review_notes ?: ''),
                    'has_attachment' => $notice->attachment_path ? '1' : '0',
                    'attachment_type' => $notice->attachment_path ? 'application/pdf' : '',
                    'attachment_name' => (string) ($notice->attachment_name ?: ''),
                    'attachment_url' => $notice->attachment_path
                        ? url('notice-management/'.$notice->id.'/attachment')
                        : '',
                ]
            );

            if (!empty($result['success'])) {
                return ['created' => 1, 'push_sent' => 1, 'push_failed' => 0, 'skipped' => false];
            }

            Log::warning('Notice review push notification failed.', [
                'notice_id' => $notice->id,
                'creator_id' => $notice->created_by,
                'status' => $notice->status,
                'result' => $result,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Notice review push notification error.', [
                'notice_id' => $notice->id,
                'creator_id' => $notice->created_by,
                'status' => $notice->status,
                'error' => $exception->getMessage(),
            ]);
        }

        return ['created' => 1, 'push_sent' => 0, 'push_failed' => 1, 'skipped' => false];
    }

    /**
     * Create one notification-history row per resolved recipient and then
     * dispatch recipient-specific FCM pushes. Calling this method repeatedly
     * is safe: source_key prevents duplicate history and notice timestamps
     * prevent duplicate push dispatch.
     */
    public function dispatch(ManagedNotice $notice): array
    {
        $notice->loadMissing('recipients');

        if ($notice->status !== 'approved') {
            return ['created' => 0, 'push_sent' => 0, 'push_failed' => 0, 'skipped' => true];
        }

        $body = $this->notificationBody($notice);
        $created = 0;

        DB::transaction(function () use ($notice, $body, &$created) {
            foreach ($notice->recipients as $recipient) {
                $notification = Notification::firstOrCreate(
                    ['source_key' => $this->sourceKey($notice, $recipient->recipient_type, $recipient->recipient_id)],
                    [
                        'managed_notice_id' => $notice->id,
                        'title' => $notice->title,
                        'content' => $body,
                        'type' => 'notice',
                        'admission_id' => $recipient->recipient_type === 'student' ? $recipient->recipient_id : null,
                        'user_id' => $recipient->recipient_type === 'user' ? $recipient->recipient_id : null,
                        'device_token' => null,
                        'branch_id' => $notice->branch_id,
                        'session_id' => $notice->session_id,
                        'attachment_path' => $notice->attachment_path,
                        'attachment_name' => $notice->attachment_name,
                        'attachment_mime' => $notice->attachment_path ? 'application/pdf' : null,
                        'message_seen' => 0,
                        'show_status' => 1,
                    ]
                );

                if ($notification->wasRecentlyCreated) {
                    $created++;
                }
            }

            if (!$notice->notifications_created_at) {
                $notice->notifications_created_at = now();
                $notice->save();
            }
        });

        // A notice approval is a one-time state transition. Do not push twice
        // if the delivery method is invoked again by a retry or page refresh.
        if ($notice->push_dispatched_at) {
            return ['created' => $created, 'push_sent' => 0, 'push_failed' => 0, 'skipped' => true];
        }

        $pushSent = 0;
        $pushFailed = 0;
        $attachmentUrl = $notice->attachment_path
            ? url('notice-management/'.$notice->id.'/attachment')
            : '';

        foreach ($notice->recipients as $recipient) {
            try {
                $result = $this->sendPushByAttendanceUniqueId(
                    $notice->title,
                    $body,
                    $recipient->recipient_type === 'student' ? 'student' : 'user',
                    (int) $recipient->recipient_id,
                    [
                        'type' => 'notice',
                        'notification_type' => 'default',
                        'channel_id' => 'default',
                        'channelId' => 'default',
                        'notice_id' => (string) $notice->id,
                        'has_attachment' => $notice->attachment_path ? '1' : '0',
                        'attachment_type' => $notice->attachment_path ? 'application/pdf' : '',
                        'attachment_name' => (string) ($notice->attachment_name ?: ''),
                        'attachment_url' => $attachmentUrl,
                    ]
                );

                if (!empty($result['success'])) {
                    $pushSent++;
                } else {
                    $pushFailed++;
                    Log::warning('Notice push notification failed.', [
                        'notice_id' => $notice->id,
                        'recipient_type' => $recipient->recipient_type,
                        'recipient_id' => $recipient->recipient_id,
                        'result' => $result,
                    ]);
                }
            } catch (\Throwable $exception) {
                $pushFailed++;
                Log::error('Notice push notification error.', [
                    'notice_id' => $notice->id,
                    'recipient_type' => $recipient->recipient_type,
                    'recipient_id' => $recipient->recipient_id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $notice->push_dispatched_at = now();
        $notice->save();

        return [
            'created' => $created,
            'push_sent' => $pushSent,
            'push_failed' => $pushFailed,
            'skipped' => false,
        ];
    }

    private function sendPushByAttendanceUniqueId(
        string $title,
        string $body,
        string $recipientType,
        int $recipientId,
        array $data
    ): array {
        $owner = $recipientType === 'student'
            ? Admission::query()->select('id', 'attendance_unique_id')->find($recipientId)
            : User::query()->select('id', 'attendance_unique_id')->find($recipientId);

        $attendanceUniqueId = trim((string) ($owner->attendance_unique_id ?? ''));
        if ($attendanceUniqueId === '') {
            return [
                'success' => false,
                'skipped' => true,
                'message' => 'Attendance unique id not found for notification recipient.',
            ];
        }

        $tokens = NotificationToken::query()
            ->where('attendance_unique_id', $attendanceUniqueId)
            ->where('platform', 'android')
            ->whereNull('deleted_at')
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->latest('id')
            ->pluck('device_token')
            ->map(function ($token) {
                return trim((string) $token);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return [
                'success' => false,
                'skipped' => true,
                'message' => 'No Android token found for attendance unique id.',
            ];
        }

        $data['attendance_unique_id'] = $attendanceUniqueId;

        return Helper::sendNotification(
            $title,
            $body,
            'firebase',
            $tokens,
            null,
            null,
            $data,
            'high'
        );
    }

    private function notificationBody(ManagedNotice $notice): string
    {
        $message = trim(preg_replace('/\s+/', ' ', strip_tags((string) $notice->message)));

        if ($notice->attachment_path) {
            $message .= "\n\nPDF attached: ".($notice->attachment_name ?: 'Notice attachment.pdf');
        }

        return $message;
    }

    private function sourceKey(ManagedNotice $notice, string $type, int $recipientId): string
    {
        return 'notice:'.$notice->id.':'.$type.':'.$recipientId;
    }
}
