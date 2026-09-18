<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\Notification;
use App\Models\NotificationToken;
use App\Models\SupportComplaint;
use App\Models\SupportComplaintReply;
use App\Models\User;
use Helper;
use Illuminate\Support\Facades\Log;

class SupportComplaintNotificationService
{
    public function notifyAdmins(SupportComplaint $complaint, SupportComplaintReply $reply, bool $isNew = false): void
    {
        $complaint->loadMissing('student');
        $studentName = trim((string) optional($complaint->student)->first_name.' '.(string) optional($complaint->student)->last_name);
        $title = $isNew ? 'New Complaint Received' : 'New Complaint Reply';
        $body = ($studentName ?: 'Student').' '.($isNew ? 'submitted' : 'replied to').' complaint '.$complaint->ticket_no.'.'
            ."\nSubject: ".$complaint->subject
            ."\nStatus: ".(SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status))
            ."\nMessage: ".trim(strip_tags($reply->message));

        $admins = User::where('role_id', 1)->where('status', 1)
            ->where('branch_id', $complaint->branch_id)->where('session_id', $complaint->session_id)->get();

        foreach ($admins as $admin) {
            $sourceKey = 'complaint:'.$complaint->id.':reply:'.$reply->id.':admin:'.$admin->id;
            if (!$this->createRecord($sourceKey, $complaint, $reply, $title, $body, 'complaint_admin', 'user', $admin->id)) continue;
            $this->push('user', $admin->id, $title, $body, $this->payload($complaint, $reply, 'complaint_admin'));
        }
    }

    public function notifyStudentReply(SupportComplaint $complaint, SupportComplaintReply $reply): void
    {
        $title = 'Admin Replied to Your Complaint';
        $body = 'Reply received for '.$complaint->ticket_no.'.'
            ."\nStatus: ".(SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status))
            ."\nMessage: ".trim(strip_tags($reply->message));
        $sourceKey = 'complaint:'.$complaint->id.':reply:'.$reply->id.':student:'.$complaint->admission_id;
        if (!$this->createRecord($sourceKey, $complaint, $reply, $title, $body, 'complaint_reply', 'student', $complaint->admission_id)) return;
        $this->push('student', $complaint->admission_id, $title, $body, $this->payload($complaint, $reply, 'complaint_reply'));
    }

    public function notifyStudentStatus(SupportComplaint $complaint): void
    {
        $title = 'Complaint Status Updated';
        $status = SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status);
        $body = $complaint->ticket_no.' status changed to '.$status.'.';
        $sourceKey = 'complaint:'.$complaint->id.':status:'.$complaint->status.':'.time().':student:'.$complaint->admission_id;
        if (!$this->createRecord($sourceKey, $complaint, null, $title, $body, 'complaint_status', 'student', $complaint->admission_id)) return;
        $this->push('student', $complaint->admission_id, $title, $body, $this->payload($complaint, null, 'complaint_status'));
    }

    private function createRecord(string $sourceKey, SupportComplaint $complaint, ?SupportComplaintReply $reply, string $title, string $body, string $type, string $recipientType, int $recipientId): bool
    {
        $record = Notification::firstOrCreate(['source_key' => $sourceKey], [
            'title' => $title, 'content' => $body, 'type' => $type,
            'admission_id' => $recipientType === 'student' ? $recipientId : null,
            'user_id' => $recipientType === 'user' ? $recipientId : null,
            'device_token' => null, 'branch_id' => $complaint->branch_id, 'session_id' => $complaint->session_id,
            'attachment_path' => $reply->attachment_path ?? null,
            'attachment_name' => $reply->attachment_name ?? null,
            'attachment_mime' => $reply->attachment_mime ?? null,
            'message_seen' => 0, 'show_status' => 1,
        ]);
        return $record->wasRecentlyCreated;
    }

    private function payload(SupportComplaint $complaint, ?SupportComplaintReply $reply, string $type): array
    {
        return [
            'type' => $type, 'notification_type' => 'default', 'channel_id' => 'default', 'channelId' => 'default',
            'complaint_id' => (string) $complaint->id, 'ticket_no' => (string) $complaint->ticket_no,
            'status' => (string) $complaint->status,
            'complaint_url' => $type === 'complaint_admin' ? url('complaints-management/'.$complaint->id) : url('student-complaints/'.$complaint->id),
            'has_attachment' => !empty($reply->attachment_path) ? '1' : '0',
            'attachment_name' => (string) ($reply->attachment_name ?? ''),
            'attachment_url' => !empty($reply->attachment_path) ? url('support-complaint-attachment/'.$reply->id) : '',
        ];
    }

    private function push(string $ownerType, int $ownerId, string $title, string $body, array $data): void
    {
        $owner = $ownerType === 'student'
            ? Admission::select('id', 'attendance_unique_id')->find($ownerId)
            : User::select('id', 'attendance_unique_id')->find($ownerId);
        $attendanceUniqueId = trim((string) ($owner->attendance_unique_id ?? ''));
        if ($attendanceUniqueId === '') {
            Log::warning('Complaint push skipped: attendance unique id missing.', compact('ownerType', 'ownerId'));
            return;
        }
        $tokens = NotificationToken::where('attendance_unique_id', $attendanceUniqueId)
            ->where('platform', 'android')->whereNull('deleted_at')->whereNotNull('device_token')
            ->where('device_token', '!=', '')->pluck('device_token')->map(fn($token) => trim((string) $token))->filter()->unique()->values()->all();
        if (!$tokens) {
            Log::warning('Complaint push skipped: Android token missing.', compact('ownerType', 'ownerId', 'attendanceUniqueId'));
            return;
        }
        $data['attendance_unique_id'] = $attendanceUniqueId;
        try {
            $result = Helper::sendNotification($title, $body, 'firebase', $tokens, null, null, $data, 'high');
            if (empty($result['success'])) Log::warning('Complaint Firebase push failed.', ['owner_type'=>$ownerType,'owner_id'=>$ownerId,'result'=>$result]);
        } catch (\Throwable $exception) {
            Log::error('Complaint Firebase push error.', ['owner_type'=>$ownerType,'owner_id'=>$ownerId,'error'=>$exception->getMessage()]);
        }
    }
}
