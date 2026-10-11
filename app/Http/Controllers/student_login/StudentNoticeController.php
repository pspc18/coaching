<?php

namespace App\Http\Controllers\student_login;

use App\Http\Controllers\Controller;
use App\Models\ManagedNotice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StudentNoticeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless((int) Session::get('role_id') === 3, 403);

        $studentId = (int) Session::get('id');
        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');

        $filter = strtolower(trim((string) $request->query('filter', 'all')));
        if (!in_array($filter, ['all', 'active', 'upcoming', 'expired'], true)) {
            $filter = 'all';
        }

        $search = trim((string) $request->query('search', ''));
        $today = Carbon::today()->toDateString();

        // High-performance base query with only necessary columns and light eager loading
        $baseQuery = ManagedNotice::query()
            ->select([
                'id',
                'title',
                'message',
                'attachment_path',
                'attachment_name',
                'attachment_size',
                'from_date',
                'to_date',
                'created_by',
                'published_at',
                'status',
            ])
            ->with(['creator:id,first_name,last_name'])
            ->where('status', 'approved')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereHas('recipients', function ($q) use ($studentId) {
                $q->where('recipient_type', 'student')
                  ->where('recipient_id', $studentId);
            });

        // Fast aggregate metric counts across student's circulars
        $noticeCounts = (clone $baseQuery)
            ->setEagerLoads([])
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN from_date <= ? AND to_date >= ? THEN 1 ELSE 0 END) as active_count', [$today, $today])
            ->selectRaw('SUM(CASE WHEN from_date > ? THEN 1 ELSE 0 END) as upcoming_count', [$today])
            ->selectRaw('SUM(CASE WHEN to_date < ? THEN 1 ELSE 0 END) as expired_count', [$today])
            ->first();

        // Apply text search keyword filter
        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%");
            });
        }

        // Apply tab status filter
        if ($filter === 'active') {
            $baseQuery->whereDate('from_date', '<=', $today)->whereDate('to_date', '>=', $today);
        } elseif ($filter === 'upcoming') {
            $baseQuery->whereDate('from_date', '>', $today);
        } elseif ($filter === 'expired') {
            $baseQuery->whereDate('to_date', '<', $today);
        }

        $notices = $baseQuery->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(12)
            ->appends($request->query());

        return view('student_login.notices.index', compact('notices', 'filter', 'search', 'today', 'noticeCounts'));
    }
}
