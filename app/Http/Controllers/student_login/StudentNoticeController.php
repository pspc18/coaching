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

        $filter = strtolower(trim((string) $request->query('filter', 'all')));
        if (!in_array($filter, ['all', 'active', 'upcoming', 'expired'], true)) {
            $filter = 'all';
        }

        $today = Carbon::today()->toDateString();
        $query = ManagedNotice::query()
            ->with(['creator', 'recipients' => function ($query) {
                $query->where('recipient_type', 'student')
                    ->where('recipient_id', (int) Session::get('id'));
            }])
            ->where('status', 'approved')
            ->where('branch_id', (int) Session::get('branch_id'))
            ->where('session_id', (int) Session::get('session_id'))
            ->whereHas('recipients', function ($query) {
                $query->where('recipient_type', 'student')
                    ->where('recipient_id', (int) Session::get('id'));
            });

        $noticeCounts = (clone $query)
            ->setEagerLoads([])
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN from_date <= ? AND to_date >= ? THEN 1 ELSE 0 END) as active_count', [$today, $today])
            ->selectRaw('SUM(CASE WHEN from_date > ? THEN 1 ELSE 0 END) as upcoming_count', [$today])
            ->selectRaw('SUM(CASE WHEN to_date < ? THEN 1 ELSE 0 END) as expired_count', [$today])
            ->first();

        if ($filter === 'active') {
            $query->whereDate('from_date', '<=', $today)->whereDate('to_date', '>=', $today);
        } elseif ($filter === 'upcoming') {
            $query->whereDate('from_date', '>', $today);
        } elseif ($filter === 'expired') {
            $query->whereDate('to_date', '<', $today);
        }

        $notices = $query->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(15)
            ->appends(['filter' => $filter]);

        return view('student_login.notices.index', compact('notices', 'filter', 'today', 'noticeCounts'));
    }
}
