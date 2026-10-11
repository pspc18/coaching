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

        $query = $this->query();
        $complaintCounts = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status IN ('open','acknowledged','in_progress','reopened') THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN status = 'awaiting_user' THEN 1 ELSE 0 END) as awaiting_count")
            ->selectRaw("SUM(CASE WHEN status IN ('resolved','closed') THEN 1 ELSE 0 END) as resolved_count")
            ->first();

        if ($filter === 'active') {
            $query->whereIn('status', ['open', 'acknowledged', 'in_progress', 'reopened']);
        } elseif ($filter === 'awaiting') {
            $query->where('status', 'awaiting_user');
        } elseif ($filter === 'resolved') {
            $query->whereIn('status', ['resolved', 'closed']);
        }

        $complaints = $query->withCount('replies')
            ->latest('last_replied_at')->latest()->paginate(15)
            ->appends(['status' => $filter]);

        return view('student_login.support_complaints.index', compact('complaints', 'filter', 'complaintCounts'));
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

    public function show($id)
    {
        $complaint = $this->query()->with(['replies', 'student'])->findOrFail($id);
        return view('student_login.support_complaints.show', compact('complaint'));
    }

    public function reply(Request $request, $id)
    {
        $complaint = $this->query()->findOrFail($id);
        if (in_array($complaint->status, ['resolved', 'closed'], true)) {
            return redirect('student-complaints/' . $complaint->id)
                ->with('error', 'This complaint has been resolved or closed. Reply is disabled.');
        }
        $request->validate([
            'message' => 'required|string|max:10000',
            'attachment' => ['nullable', 'file', 'max:10240', new ComplaintAttachment],
        ]);
        $attachment = $this->storeAttachment($request);
        $reply = null;
        DB::transaction(function () use ($complaint, $request, $attachment, &$reply) {
            $reply = $complaint->replies()->create(array_merge($attachment, [
                'sender_type' => $complaint->submitted_as,
                'sender_id' => Session::get('id'), 'message' => $request->message,
            ]));
            $complaint->last_replied_by = $complaint->submitted_as;
            $complaint->last_replied_at = now();
            if (in_array($complaint->status, ['resolved', 'closed'], true)) $complaint->status = 'reopened';
            elseif ($complaint->status === 'awaiting_user') $complaint->status = 'in_progress';
            $complaint->save();
            $reply->status_after = $complaint->status;
            $reply->save();
        });
        app(SupportComplaintNotificationService::class)->notifyAdmins($complaint->fresh(), $reply, false);
        return redirect('student-complaints/'.$complaint->id)->with('message', 'Reply sent successfully.');
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
