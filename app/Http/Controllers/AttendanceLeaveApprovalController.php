<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMark;
use App\Models\Admission;
use App\Models\Master\LeaveManagement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;

class AttendanceLeaveApprovalController extends Controller
{
    private function leaveStatusMeta($status)
    {
        $raw = (string) $status;
        if ($raw === '1') {
            return ['label' => 'Approved', 'class' => 'badge-success'];
        }

        if ($raw === '0') {
            return ['label' => 'Rejected', 'class' => 'badge-danger'];
        }

        if ($raw === '3') {
            return ['label' => 'Cancelled', 'class' => 'badge-secondary'];
        }

        return ['label' => 'Pending', 'class' => 'badge-warning text-dark font-weight-bold'];
    }

    private function syncLeaveMarks($leave, $approvedBy)
    {
        $isStudent = !empty($leave->admission_id) || strtolower((string) $leave->user_type) === 'student';
        $entityType = $isStudent ? 'student' : 'staff';
        $uniqueId = $leave->attendance_unique_id;

        if (empty($uniqueId)) {
            if ($isStudent) {
                $sid = !empty($leave->admission_id) ? $leave->admission_id : $leave->user_id;
                $uniqueId = Admission::where('id', $sid)->value('attendance_unique_id');
            } else {
                $uniqueId = User::where('id', $leave->user_id)->value('attendance_unique_id');
            }
            if (!empty($uniqueId)) {
                $leave->attendance_unique_id = $uniqueId;
                $leave->save();
            }
        }

        if (empty($uniqueId)) {
            return;
        }

        $fromDate = Carbon::parse($leave->from_date)->startOfDay();
        $toDate = Carbon::parse($leave->to_date)->startOfDay();

        $cursor = $fromDate->copy();
        while ($cursor->lte($toDate)) {
            AttendanceMark::updateOrCreate(
                [
                    'unique_id' => $uniqueId,
                    'date' => $cursor->toDateString(),
                    'branch_id' => $leave->branch_id,
                    'session_id' => $leave->session_id,
                ],
                [
                    'entity_type' => $entityType,
                    'status' => 'absent',
                    'in_time' => null,
                    'out_time' => null,
                    'created_by' => $approvedBy,
                ]
            );

            $cursor->addDay();
        }
    }

    private function clearLeaveMarks($leave)
    {
        $uniqueId = $leave->attendance_unique_id;
        if (empty($uniqueId)) {
            $isStudent = !empty($leave->admission_id) || strtolower((string) $leave->user_type) === 'student';
            if ($isStudent) {
                $sid = !empty($leave->admission_id) ? $leave->admission_id : $leave->user_id;
                $uniqueId = Admission::where('id', $sid)->value('attendance_unique_id');
            } else {
                $uniqueId = User::where('id', $leave->user_id)->value('attendance_unique_id');
            }
        }

        if (empty($uniqueId)) {
            return;
        }

        AttendanceMark::where('unique_id', $uniqueId)
            ->where('branch_id', $leave->branch_id)
            ->where('session_id', $leave->session_id)
            ->whereBetween('date', [$leave->from_date, $leave->to_date])
            ->where('status', 'absent')
            ->delete();
    }

    public function index(Request $request)
    {
        if ((int) Session::get('role_id') !== 1) {
            return redirect()->to(url('access-denied'));
        }

        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        // Status filter (by default: 2 = Pending on initial page load, or user selection)
        if ($request->has('status')) {
            $rawStatus = (string) $request->input('status');
            $statusFilter = ($rawStatus === 'all' || $rawStatus === '') ? 'all' : $rawStatus;
        } else {
            $statusFilter = '2';
        }

        // Additional in-column filters
        $userTypeFilter = trim((string) $request->input('user_type', ''));
        $nameFilter = trim((string) $request->input('name', ''));
        $uidFilter = trim((string) $request->input('attendance_unique_id', ''));
        $fromDateFilter = trim((string) $request->input('from_date', ''));
        $toDateFilter = trim((string) $request->input('to_date', ''));
        $reasonFilter = trim((string) $request->input('reason', ''));

        // Pagination settings
        $perPageInput = $request->input('per_page', 20);
        $perPage = ($perPageInput === 'all' || is_numeric($perPageInput)) ? $perPageInput : 20;
        $page = max(1, (int) $request->input('page', 1));

        // Base query for counts across all leave records in active branch & session
        $baseQuery = LeaveManagement::where('branch_id', $branchId)
            ->where('session_id', $sessionId);

        $counts = (clone $baseQuery)->selectRaw("
            COUNT(CASE WHEN status = '2' THEN 1 END) as pending,
            COUNT(CASE WHEN status = '1' THEN 1 END) as approved,
            COUNT(CASE WHEN status = '0' THEN 1 END) as rejected,
            COUNT(CASE WHEN status = '3' THEN 1 END) as cancelled,
            COUNT(*) as total
        ")->first();

        // Main Query
        $query = (clone $baseQuery);

        // Status Filter
        if ($statusFilter !== 'all') {
            $query->where('status', (string) $statusFilter);
        }

        // User Type Filter
        if (!empty($userTypeFilter) && $userTypeFilter !== 'all') {
            if ($userTypeFilter === 'student') {
                $query->where(function($q) {
                    $q->where('user_type', 'student')
                      ->orWhereNotNull('admission_id');
                });
            } else {
                $query->where(function($q) {
                    $q->where('user_type', '!=', 'student')
                      ->whereNull('admission_id');
                });
            }
        }

        // Attendance Unique ID Filter
        if (!empty($uidFilter)) {
            $matchingStuIds = Admission::where('branch_id', $branchId)
                ->where('attendance_unique_id', 'like', "%{$uidFilter}%")
                ->pluck('id')->all();

            $matchingStaffIds = User::where('branch_id', $branchId)
                ->where('attendance_unique_id', 'like', "%{$uidFilter}%")
                ->pluck('id')->all();

            $query->where(function($q) use ($uidFilter, $matchingStuIds, $matchingStaffIds) {
                $q->where('attendance_unique_id', 'like', "%{$uidFilter}%")
                  ->orWhereIn('admission_id', $matchingStuIds)
                  ->orWhere(function($sq) use ($matchingStuIds) {
                      $sq->where('user_type', 'student')->whereIn('user_id', $matchingStuIds);
                  })
                  ->orWhere(function($sq) use ($matchingStaffIds) {
                      $sq->where('user_type', '!=', 'student')->whereIn('user_id', $matchingStaffIds);
                  });
            });
        }

        // Date Filters
        if (!empty($fromDateFilter)) {
            $query->whereDate('from_date', '>=', $fromDateFilter);
        }

        if (!empty($toDateFilter)) {
            $query->whereDate('to_date', '<=', $toDateFilter);
        }

        // Reason Filter
        if (!empty($reasonFilter)) {
            $query->where('reason', 'like', "%{$reasonFilter}%");
        }

        // Filter by person name across both Student and Staff tables
        if (!empty($nameFilter)) {
            $matchedStudentIds = Admission::where('branch_id', $branchId)
                ->where(function($q) use ($nameFilter) {
                    $q->where('first_name', 'like', "%{$nameFilter}%")
                      ->orWhere('last_name', 'like', "%{$nameFilter}%")
                      ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$nameFilter}%"]);
                })->pluck('id')->all();

            $matchedStaffIds = User::where('branch_id', $branchId)
                ->where(function($q) use ($nameFilter) {
                    $q->where('first_name', 'like', "%{$nameFilter}%")
                      ->orWhere('last_name', 'like', "%{$nameFilter}%")
                      ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$nameFilter}%"]);
                })->pluck('id')->all();

            $query->where(function($q) use ($matchedStudentIds, $matchedStaffIds) {
                $q->where(function($sq) use ($matchedStudentIds) {
                    $sq->whereNotNull('admission_id')->whereIn('admission_id', $matchedStudentIds);
                })->orWhere(function($sq) use ($matchedStudentIds) {
                    $sq->where('user_type', 'student')->whereIn('user_id', $matchedStudentIds);
                })->orWhere(function($sq) use ($matchedStaffIds) {
                    $sq->where('user_type', '!=', 'student')->whereNull('admission_id')->whereIn('user_id', $matchedStaffIds);
                });
            });
        }

        $totalCount = $query->count();
        $query->orderByDesc('id');

        // Apply pagination
        if ($perPage === 'all') {
            $rowsRaw = $query->get();
            $startIndex = 0;
            $lastPage = 1;
        } else {
            $perPage = (int) $perPage;
            $rowsRaw = $query->forPage($page, $perPage)->get();
            $startIndex = ($page - 1) * $perPage;
            $lastPage = max(1, (int) ceil($totalCount / $perPage));
        }

        // Resolve students & staff data
        $studentIds = collect();
        $staffIds = collect();

        foreach ($rowsRaw as $r) {
            $isStudent = !empty($r->admission_id) || strtolower((string) ($r->user_type ?? '')) === 'student';
            if ($isStudent) {
                $sid = !empty($r->admission_id) ? $r->admission_id : $r->user_id;
                if ($sid) $studentIds->push((int) $sid);
            } else {
                if ($r->user_id) $staffIds->push((int) $r->user_id);
            }
        }

        $studentsById = Admission::select('id', 'first_name', 'last_name', 'attendance_unique_id')
            ->whereIn('id', $studentIds->unique()->values())
            ->get()
            ->keyBy('id');

        $staffById = User::select('id', 'first_name', 'last_name', 'attendance_unique_id')
            ->whereIn('id', $staffIds->unique()->values())
            ->get()
            ->keyBy('id');

        $rows = $rowsRaw->map(function ($row) use ($studentsById, $staffById) {
            $meta = $this->leaveStatusMeta($row->status);
            $row->status_label = $meta['label'];
            $row->status_class = $meta['class'];

            $isStudent = !empty($row->admission_id) || strtolower((string) ($row->user_type ?? '')) === 'student';
            $row->resolved_user_type = $isStudent ? 'student' : 'staff';

            $name = '';
            $uid = $row->attendance_unique_id;

            if ($isStudent) {
                $sid = !empty($row->admission_id) ? $row->admission_id : $row->user_id;
                $student = $studentsById->get($sid);
                if ($student) {
                    $name = trim((string) ($student->first_name ?? '') . ' ' . (string) ($student->last_name ?? ''));
                    if (empty($uid)) {
                        $uid = $student->attendance_unique_id;
                    }
                }
            } else {
                $staff = $staffById->get($row->user_id);
                if ($staff) {
                    $name = trim((string) ($staff->first_name ?? '') . ' ' . (string) ($staff->last_name ?? ''));
                    if (empty($uid)) {
                        $uid = $staff->attendance_unique_id;
                    }
                }
            }

            $row->person_name = $name !== '' ? $name : '-';
            $row->resolved_attendance_id = (!empty($uid)) ? $uid : '-';
            return $row;
        });

        $from = $totalCount > 0 ? ($startIndex + 1) : 0;
        $to = $perPage === 'all' ? $totalCount : min($startIndex + count($rowsRaw), $totalCount);

        // AJAX response for fast interactive search/pagination
        if ($request->ajax() || $request->input('ajax') == '1' || $request->wantsJson()) {
            $isMob = ($request->input('layout') === 'mobile' || $request->input('view_type') === 'mobile' || $request->input('view_type') === 'card' || \App\Helpers\Helper::isMobile());
            $viewName = ($isMob && view()->exists('attendance.mobile.leave_cards')) 
                ? 'attendance.mobile.leave_cards' 
                : 'attendance.leave_table_rows';

            $html = view($viewName, [
                'rows' => $rows,
                'startIndex' => $startIndex
            ])->render();

            return response()->json([
                'status' => true,
                'html' => $html,
                'total' => $totalCount,
                'from' => $from,
                'to' => $to,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'counts' => $counts,
            ]);
        }

        return \App\Helpers\Helper::view('attendance.leave_approvals', compact(
            'rows',
            'statusFilter',
            'counts',
            'totalCount',
            'startIndex',
            'page',
            'perPage',
            'lastPage',
            'from',
            'to',
            'userTypeFilter',
            'nameFilter',
            'uidFilter',
            'fromDateFilter',
            'toDateFilter',
            'reasonFilter'
        ));
    }

    public function action(Request $request)
    {
        if ((int) Session::get('role_id') !== 1) {
            return redirect()->to(url('access-denied'));
        }

        $request->validate([
            'leave_id' => 'required|integer',
            'action' => 'required|in:approve,reject,cancel,delete',
        ]);

        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $leave = LeaveManagement::where('id', (int) $request->leave_id)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$leave) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }

        if ($request->action === 'approve') {
            $leave->status = '1';
            $leave->save();
            $this->syncLeaveMarks($leave, (int) Session::get('id'));
            return redirect()->back()->with('message', 'Leave approved successfully.');
        }

        if ($request->action === 'reject') {
            $leave->status = '0';
            $leave->save();
            $this->clearLeaveMarks($leave);
            return redirect()->back()->with('message', 'Leave rejected successfully.');
        }

        if ($request->action === 'cancel') {
            $leave->status = '3';
            $leave->save();
            $this->clearLeaveMarks($leave);
            return redirect()->back()->with('message', 'Leave cancelled successfully.');
        }

        $this->clearLeaveMarks($leave);
        $leave->delete();

        return redirect()->back()->with('message', 'Leave deleted successfully.');
    }
}
