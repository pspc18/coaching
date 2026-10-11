<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\SupportComplaint;
use App\Models\SupportComplaintReply;
use App\Rules\ComplaintAttachment;
use App\Services\SupportComplaintNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportComplaintController extends Controller
{
    private function query()
    {
        abort_unless((int) Session::get('role_id') === 1, 403);
        return SupportComplaint::where('branch_id', Session::get('branch_id'))
            ->where('session_id', Session::get('session_id'));
    }

    public function index(Request $request)
    {
        $query = $this->query()->with(['student' => function ($q) {
            $q->select('id', 'first_name', 'last_name', 'mobile', 'image', 'admissionNo');
        }])->withCount('replies');

        if ($request->filled('status') && array_key_exists($request->status, SupportComplaint::STATUSES)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category') && array_key_exists($request->category, SupportComplaint::CATEGORIES)) {
            $query->where('category', $request->category);
        }
        if ($request->filled('priority') && in_array($request->priority, ['low', 'medium', 'high', 'urgent'])) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('ticket_no')) {
            $query->where('ticket_no', 'like', '%' . trim($request->ticket_no) . '%');
        }
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . trim($request->subject) . '%');
        }
        if ($request->filled('student_name')) {
            $name = trim($request->student_name);
            $query->whereHas('student', function ($q) use ($name) {
                $q->where(DB::raw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))"), 'like', '%' . $name . '%')
                  ->orWhere('admissionNo', 'like', '%' . $name . '%')
                  ->orWhere('mobile', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%')
                    ->orWhereHas('student', fn($student) => $student->where('first_name', 'like', '%'.$search.'%')->orWhere('last_name', 'like', '%'.$search.'%')->orWhere('admissionNo', 'like', '%'.$search.'%'));
            });
        }

        $perPageInput = $request->input('per_page', 20);
        if ($perPageInput === 'all') {
            $countTotal = (clone $query)->count();
            $complaints = $query->latest('last_replied_at')->latest()->paginate(max($countTotal, 1))->appends($request->query());
        } else {
            $perPage = in_array((int)$perPageInput, [10, 20, 50, 100]) ? (int)$perPageInput : 20;
            $complaints = $query->latest('last_replied_at')->latest()->paginate($perPage)->appends($request->query());
        }

        $counts = $this->query()->selectRaw('status, COUNT(*) total')->groupBy('status')->pluck('total', 'status');
        $totalCount = $counts->sum();

        if ($request->ajax() || $request->input('ajax') == '1') {
            $html = view('master.support_complaints.table_rows', compact('complaints'))->render();
            return response()->json([
                'status' => true,
                'html' => $html,
                'current_page' => $complaints->currentPage(),
                'last_page' => $complaints->lastPage(),
                'from' => $complaints->firstItem() ?? 0,
                'to' => $complaints->lastItem() ?? 0,
                'total' => $complaints->total(),
                'counts' => $counts,
                'totalCount' => $totalCount,
            ]);
        }

        return view('master.support_complaints.index', compact('complaints', 'counts', 'totalCount'));
    }

    public function show($id)
    {
        $complaint = $this->query()->with(['student.ClassTypes', 'replies', 'assignee'])->findOrFail($id);
        return view('master.support_complaints.show', compact('complaint'));
    }

    public function reply(Request $request, $id)
    {
        $complaint = $this->query()->findOrFail($id);
        $request->validate([
            'message' => 'required|string|max:10000',
            'status' => 'required|in:'.implode(',', array_keys(SupportComplaint::STATUSES)),
            'attachment' => ['nullable', 'file', 'max:10240', new ComplaintAttachment],
        ]);
        $attachment = $this->storeAttachment($request);
        $reply = null;
        DB::transaction(function () use ($complaint, $request, $attachment, &$reply) {
            $reply = $complaint->replies()->create(array_merge($attachment, [
                'sender_type' => 'admin', 'sender_id' => Session::get('id'), 'message' => $request->message,
            ]));
            $this->applyStatus($complaint, $request->status);
            $complaint->assigned_to = $complaint->assigned_to ?: Session::get('id');
            $complaint->last_replied_by = 'admin'; $complaint->last_replied_at = now(); $complaint->save();
            $reply->status_after = $complaint->status;
            $reply->save();
        });
        app(SupportComplaintNotificationService::class)->notifyStudentReply($complaint->fresh(), $reply);
        return redirect('complaints-management/'.$complaint->id)->with('message', 'Reply sent and status updated.');
    }

    public function status(Request $request, $id)
    {
        $complaint = $this->query()->findOrFail($id);
        $request->validate(['status' => 'required|in:'.implode(',', array_keys(SupportComplaint::STATUSES))]);
        $this->applyStatus($complaint, $request->status);
        $complaint->assigned_to = $complaint->assigned_to ?: Session::get('id');
        $complaint->last_replied_by = 'admin';
        $complaint->last_replied_at = now();
        $complaint->save();
        $complaint->replies()->create([
            'sender_type' => 'admin',
            'sender_id' => Session::get('id'),
            'message' => 'Status changed to '.(SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status)).'.',
            'status_after' => $complaint->status,
        ]);
        app(SupportComplaintNotificationService::class)->notifyStudentStatus($complaint->fresh());
        return redirect()->back()->with('message', 'Complaint status updated.');
    }

    public function attachment($replyId)
    {
        $reply = SupportComplaintReply::with('complaint')->findOrFail($replyId);
        $complaint = $reply->complaint;
        $isAdmin = (int) Session::get('role_id') === 1
            && (int) $complaint->branch_id === (int) Session::get('branch_id')
            && (int) $complaint->session_id === (int) Session::get('session_id');
        $isStudent = (int) Session::get('role_id') === 3
            && (int) $complaint->admission_id === (int) Session::get('id');
        abort_unless($isAdmin || $isStudent, 403);
        abort_unless($reply->attachment_path && Storage::disk('local')->exists($reply->attachment_path), 404);
        return Storage::disk('local')->download($reply->attachment_path, $reply->attachment_name);
    }

    private function applyStatus(SupportComplaint $complaint, string $status): void
    {
        $complaint->status = $status;
        if ($status === 'resolved') $complaint->resolved_at = now();
        if ($status === 'closed') $complaint->closed_at = now();
        if (!in_array($status, ['resolved', 'closed'], true)) { $complaint->resolved_at = null; $complaint->closed_at = null; }
    }

    private function storeAttachment(Request $request): array
    {
        if (!$request->hasFile('attachment')) return ['attachment_path'=>null,'attachment_name'=>null,'attachment_mime'=>null,'attachment_size'=>null];
        $file = $request->file('attachment');
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension === 'jpeg') $extension = 'jpg';
        return [
            'attachment_path' => $file->storeAs('support-complaints/'.Session::get('branch_id').'/'.date('Y/m'), Str::uuid().'.'.$extension, 'local'),
            'attachment_name' => Str::limit(basename($file->getClientOriginalName()), 240, ''),
            'attachment_mime' => $file->getMimeType(), 'attachment_size' => $file->getSize(),
        ];
    }
}
