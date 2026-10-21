<?php

namespace App\Http\Controllers\student_login;

use App\Http\Controllers\Controller;
use App\Models\SupportComplaint;
use App\Rules\ComplaintAttachment;
use App\Services\SupportComplaintNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SupportComplaintController extends Controller
{
    private function currentAdmission(): ?\App\Models\Admission
    {
        return \App\Models\Admission::select('id', 'session_id', 'branch_id')
            ->find(Session::get('id'));
    }

    private function resolvedSessionId(): ?int
    {
        $sessionId = Session::get('session_id');
        if (!empty($sessionId)) {
            return (int) $sessionId;
        }

        $admission = $this->currentAdmission();
        return $admission ? (int) $admission->session_id : null;
    }

    private function resolvedBranchId(): ?int
    {
        $branchId = Session::get('branch_id');
        if (!empty($branchId)) {
            return (int) $branchId;
        }

        $admission = $this->currentAdmission();
        return $admission ? (int) $admission->branch_id : null;
    }

    private function query()
    {
        abort_unless((int) Session::get('role_id') === 3, 403);
        $sessionId = $this->resolvedSessionId();
        $branchId = $this->resolvedBranchId();

        abort_unless($sessionId && $branchId, 403, 'Student session details are missing. Please log in again.');

        return SupportComplaint::where('admission_id', Session::get('id'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId);
    }

    public function index(Request $request)
    {
        $filter = strtolower(trim((string) $request->query('status', 'all')));
        if (!in_array($filter, ['all', 'active', 'awaiting', 'resolved'], true)) {
            $filter = 'all';
        }

        $category = strtolower(trim((string) $request->query('category', 'all')));
        if ($category !== 'all' && !array_key_exists($category, SupportComplaint::CATEGORIES)) {
            $category = 'all';
        }

        $search = trim((string) $request->query('search', ''));

        $baseQuery = $this->query();

        $complaintCounts = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status IN ('open','acknowledged','in_progress','reopened') THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN status = 'awaiting_user' THEN 1 ELSE 0 END) as awaiting_count")
            ->selectRaw("SUM(CASE WHEN status IN ('resolved','closed') THEN 1 ELSE 0 END) as resolved_count")
            ->first();

        $categoryCounts = (clone $baseQuery)
            ->selectRaw('category, COUNT(*) as cnt')
            ->groupBy('category')
            ->pluck('cnt', 'category')
            ->toArray();

        $query = clone $baseQuery;

        if ($filter === 'active') {
            $query->whereIn('status', ['open', 'acknowledged', 'in_progress', 'reopened']);
        } elseif ($filter === 'awaiting') {
            $query->where('status', 'awaiting_user');
        } elseif ($filter === 'resolved') {
            $query->whereIn('status', ['resolved', 'closed']);
        }

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $complaints = $query->withCount('replies')
            ->with(['replies' => function ($q) {
                $q->latest('id')->limit(1);
            }])
            ->latest('last_replied_at')
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('student_login.support_complaints.index', compact(
            'complaints',
            'filter',
            'category',
            'search',
            'complaintCounts',
            'categoryCounts'
        ));
    }

    public function create()
    {
        abort_unless((int) Session::get('role_id') === 3, 403);
        return view('student_login.support_complaints.create');
    }

    public function store(Request $request)
    {
        abort_unless((int) Session::get('role_id') === 3, 403);
        $request->validate([
            'submitted_as' => 'required|in:student,parent',
            'subject' => 'required|string|max:255',
            'category' => 'required|in:'.implode(',', array_keys(SupportComplaint::CATEGORIES)),
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string|max:10000',
            'attachment' => ['nullable', 'file', 'max:10240', new ComplaintAttachment],
        ]);

        $attachment = $this->storeAttachment($request);
        $complaint = null;
        $initialReply = null;
        $sessionId = $this->resolvedSessionId();
        $branchId = $this->resolvedBranchId();
        abort_unless($sessionId && $branchId, 403, 'Student session details are missing. Please log in again.');

        DB::transaction(function () use ($request, $attachment, &$complaint, &$initialReply) {
            $complaint = SupportComplaint::create([
                'session_id' => $this->resolvedSessionId(), 'branch_id' => $this->resolvedBranchId(),
                'admission_id' => Session::get('id'), 'submitted_as' => $request->submitted_as,
                'subject' => $request->subject, 'category' => $request->category,
                'priority' => $request->priority, 'status' => 'open',
                'last_replied_by' => $request->submitted_as, 'last_replied_at' => now(),
            ]);
            $complaint->ticket_no = 'CMP-'.date('Ym').'-'.str_pad($complaint->id, 6, '0', STR_PAD_LEFT);
            $complaint->save();
            $initialReply = $complaint->replies()->create(array_merge($attachment, [
                'sender_type' => $request->submitted_as,
                'sender_id' => Session::get('id'),
                'message' => $request->message,
                'status_after' => 'open',
            ]));
        });
        app(SupportComplaintNotificationService::class)->notifyAdmins($complaint, $initialReply, true);

        return redirect('student-complaints')->with('message', 'Complaint submitted successfully.');
    }

    public function show(Request $request, $id)
    {
        if ($request->boolean('poll') || $request->ajax() || $request->wantsJson()) {
            $complaint = $this->query()->withCount('replies')->findOrFail($id);
            $afterId = (int) $request->query('after_id', 0);

            $newReplies = $complaint->replies()
                ->where('id', '>', $afterId)
                ->orderBy('id')
                ->get()
                ->map(function ($reply) use ($complaint) {
                    return $this->formatReplyPayload($complaint, $reply);
                })
                ->values();

            return response()->json([
                'status'      => true,
                'replies'     => $newReplies,
                'ticket_meta' => $this->formatTicketMetaPayload($complaint, (int) $complaint->replies_count),
            ]);
        }

        $complaint = $this->query()->with(['replies', 'student'])->findOrFail($id);
        return view('student_login.support_complaints.show', compact('complaint'));
    }

    public function reply(Request $request, $id)
    {
        $complaint = $this->query()->findOrFail($id);
        $isAjax = $request->ajax() || $request->wantsJson() || $request->boolean('ajax');

        if (in_array($complaint->status, ['resolved', 'closed'], true)) {
            if ($isAjax) {
                return response()->json([
                    'status'  => false,
                    'message' => 'This complaint has been resolved or closed. Reply is disabled.',
                ], 422);
            }
            return redirect('student-complaints/' . $complaint->id)
                ->with('error', 'This complaint has been resolved or closed. Reply is disabled.');
        }

        $validator = Validator::make($request->all(), [
            'message'    => 'required_without:attachment|nullable|string|max:10000',
            'attachment' => ['nullable', 'file', 'max:10240', new ComplaintAttachment],
        ], [
            'message.required_without' => 'Please enter a message or attach a file.',
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return redirect('student-complaints/' . $complaint->id)
                ->withErrors($validator)
                ->withInput();
        }

        $attachment = $this->storeAttachment($request);
        $messageText = trim((string) $request->input('message', ''));
        if ($messageText === '' && !empty($attachment['attachment_name'])) {
            $messageText = 'Shared attachment: ' . $attachment['attachment_name'];
        }

        $reply = null;
        DB::transaction(function () use ($complaint, $messageText, $attachment, &$reply) {
            $reply = $complaint->replies()->create(array_merge($attachment, [
                'sender_type' => $complaint->submitted_as,
                'sender_id'   => Session::get('id'),
                'message'     => $messageText,
            ]));
            $complaint->last_replied_by = $complaint->submitted_as;
            $complaint->last_replied_at = now();
            if (in_array($complaint->status, ['resolved', 'closed'], true)) {
                $complaint->status = 'reopened';
            } elseif ($complaint->status === 'awaiting_user') {
                $complaint->status = 'in_progress';
            }
            $complaint->save();
            $reply->status_after = $complaint->status;
            $reply->save();
        });

        app(SupportComplaintNotificationService::class)->notifyAdmins($complaint->fresh(), $reply, false);

        if ($isAjax) {
            $replyCount = (int) $complaint->replies()->count();
            return response()->json([
                'status'      => true,
                'message'     => 'Reply sent.',
                'reply'       => $this->formatReplyPayload($complaint, $reply),
                'ticket_meta' => $this->formatTicketMetaPayload($complaint, $replyCount),
            ]);
        }

        return redirect('student-complaints/'.$complaint->id)->with('message', 'Reply sent successfully.');
    }

    private function formatReplyPayload(SupportComplaint $complaint, $reply): array
    {
        $submittedAsLabel = $complaint->submitted_as === 'parent' ? 'Parent' : 'Student';
        $isStudent = $reply->sender_type !== 'admin';
        $replyDate = $reply->created_at ? $reply->created_at->format('Y-m-d') : now()->format('Y-m-d');

        if ($replyDate === now()->format('Y-m-d')) {
            $dateHeading = 'Today';
        } elseif ($replyDate === now()->subDay()->format('Y-m-d')) {
            $dateHeading = 'Yesterday';
        } else {
            $dateHeading = optional($reply->created_at)->format('d M Y') ?: 'Today';
        }

        $statusAfterLabel = $reply->status_after
            ? (SupportComplaint::STATUSES[$reply->status_after] ?? ucfirst(str_replace('_', ' ', (string) $reply->status_after)))
            : null;

        return [
            'id'                 => (int) $reply->id,
            'sender_type'        => (string) $reply->sender_type,
            'is_student'         => $isStudent,
            'sender_label'       => $isStudent ? ('You · ' . $submittedAsLabel) : 'School Support',
            'submitted_as_label' => $submittedAsLabel,
            'message'            => (string) $reply->message,
            'has_attachment'     => !empty($reply->attachment_path),
            'attachment_url'     => $reply->attachment_path ? url('support-complaint-attachment/' . $reply->id) : null,
            'attachment_name'    => $reply->attachment_name ?: 'View Attachment',
            'status_after_label' => $statusAfterLabel,
            'created_at_time'    => optional($reply->created_at)->format('h:i A') ?: now()->format('h:i A'),
            'date_key'           => $replyDate,
            'date_heading'       => $dateHeading,
        ];
    }

    private function formatTicketMetaPayload(SupportComplaint $complaint, int $replyCount): array
    {
        $statusMetaMap = [
            'open'          => ['label' => 'Open', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-clock-o'],
            'acknowledged'  => ['label' => 'Acknowledged', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-check'],
            'in_progress'   => ['label' => 'In Progress', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-spinner fa-spin'],
            'awaiting_user' => ['label' => 'Awaiting Reply', 'badge_class' => 'badge-soft-amber', 'tone' => 'amber', 'icon' => 'fa-reply'],
            'resolved'      => ['label' => 'Resolved', 'badge_class' => 'badge-soft-success', 'tone' => 'green', 'icon' => 'fa-check-circle'],
            'closed'        => ['label' => 'Closed', 'badge_class' => 'badge-soft-slate', 'tone' => 'slate', 'icon' => 'fa-lock'],
            'reopened'      => ['label' => 'Reopened', 'badge_class' => 'badge-soft-purple', 'tone' => 'purple', 'icon' => 'fa-refresh'],
        ];

        $fallbackLabel = SupportComplaint::STATUSES[$complaint->status] ?? ucfirst(str_replace('_', ' ', (string) $complaint->status));
        $statusMeta = $statusMetaMap[$complaint->status] ?? [
            'label'       => $fallbackLabel,
            'badge_class' => 'badge-soft-info',
            'tone'        => 'blue',
            'icon'        => 'fa-circle-o',
        ];

        return [
            'status'        => (string) $complaint->status,
            'can_reply'     => !in_array($complaint->status, ['resolved', 'closed'], true),
            'reply_count'   => $replyCount,
            'reply_label'   => $replyCount . ' ' . Str::plural('msg', $replyCount),
            'last_activity' => optional($complaint->last_replied_at)->format('d M Y, h:i A') ?: 'No replies',
            'status_meta'   => $statusMeta,
        ];
    }

    private function storeAttachment(Request $request): array
    {
        if (!$request->hasFile('attachment')) return ['attachment_path'=>null,'attachment_name'=>null,'attachment_mime'=>null,'attachment_size'=>null];
        $file = $request->file('attachment');
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension === 'jpeg') $extension = 'jpg';
        $branchId = $this->resolvedBranchId() ?: Session::get('branch_id');
        return [
            'attachment_path' => $file->storeAs('support-complaints/'.$branchId.'/'.date('Y/m'), Str::uuid().'.'.$extension, 'local'),
            'attachment_name' => Str::limit(basename($file->getClientOriginalName()), 240, ''),
            'attachment_mime' => $file->getMimeType(), 'attachment_size' => $file->getSize(),
        ];
    }
}
