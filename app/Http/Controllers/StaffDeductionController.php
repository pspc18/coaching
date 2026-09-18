<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollDeduction;
use App\Models\PayrollSalary;
use App\Models\User;
use Session;

class StaffDeductionController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));

        $staffList = User::select('id', 'first_name', 'last_name', 'role_id', 'attendance_unique_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->whereNotIn('role_id', [1, 3])
            ->orderBy('first_name')
            ->get();

        if ($request->isMethod('post')) {
            $action = (string) $request->input('action', '');

            if ($action === 'add_deduction') {
                $request->validate([
                    'unique_id' => 'required|string',
                    'amount' => 'required|numeric|min:0.01|max:99999999',
                    'title' => 'nullable|string|max:100',
                    'remark' => 'nullable|string|max:1000',
                ]);

                $uid = (string) $request->unique_id;
                if (stripos($uid, 'USR-') !== 0) {
                    return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                        ->with('error', 'Invalid staff selection.');
                }

                $userId = (int) str_replace('USR-', '', $uid);
                $user = User::where('id', $userId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$user) {
                    return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                        ->with('error', 'Staff not found.');
                }

                if ($this->isPayrollGenerated($branchId, $sessionId, $uid, $month, $year)) {
                    return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                        ->with('error', 'Salary is finalized for this staff and period. Deductions are locked.');
                }

                PayrollDeduction::create([
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'unique_id' => $uid,
                    'month' => $month,
                    'year' => $year,
                    'amount' => (float) $request->amount,
                    'original_amount' => (float) $request->amount,
                    'type' => 'manual',
                    'title' => $request->title,
                    'remark' => $request->remark,
                    'is_applied' => 1,
                    'created_by' => Session::get('id'),
                ]);

                return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                    ->with('message', 'Deduction added successfully.');
            }

            if ($action === 'update_deduction') {
                $request->validate([
                    'deduction_id' => 'required|integer',
                    'amount' => 'required|numeric|min:0.01|max:99999999',
                    'title' => 'nullable|string|max:100',
                    'remark' => 'nullable|string|max:1000',
                ]);

                $deduction = PayrollDeduction::where('id', (int) $request->deduction_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->whereNull('loan_id')
                    ->where('type', 'manual')
                    ->first();

                if ($deduction && $this->isPayrollGenerated(
                    $branchId,
                    $sessionId,
                    (string) $deduction->unique_id,
                    (int) $deduction->month,
                    (int) $deduction->year
                )) {
                    return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                        ->with('error', 'Salary is finalized for this staff and period. This deduction cannot be changed.');
                }

                if ($deduction) {
                    $update = [
                        'amount' => (float) $request->amount,
                        'title' => $request->title,
                        'remark' => $request->remark,
                    ];
                    $update['original_amount'] = (float) $request->amount;
                    $deduction->update($update);
                }

                return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                    ->with('message', 'Deduction updated successfully.');
            }

            if ($action === 'delete_deduction') {
                $request->validate([
                    'deduction_id' => 'required|integer',
                ]);

                $deduction = PayrollDeduction::where('id', (int) $request->deduction_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->whereNull('loan_id')
                    ->where('type', 'manual')
                    ->first();

                if ($deduction && $this->isPayrollGenerated(
                    $branchId,
                    $sessionId,
                    (string) $deduction->unique_id,
                    (int) $deduction->month,
                    (int) $deduction->year
                )) {
                    return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                        ->with('error', 'Salary is finalized for this staff and period. This deduction cannot be deleted.');
                }

                if ($deduction) {
                    $deduction->delete();
                }

                return redirect()->to('payroll/staff/deductions?month=' . $month . '&year=' . $year)
                    ->with('message', 'Deduction deleted successfully.');
            }
        }

        $allDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('loan_id')
            ->where('type', 'manual')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('id', 'desc')
            ->get();

        $userIds = $allDeductions
            ->map(function ($d) {
                $uid = (string) ($d->unique_id ?? '');
                return (stripos($uid, 'USR-') === 0) ? (int) str_replace('USR-', '', $uid) : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $usersById = empty($userIds)
            ? collect()
            : User::select('id', 'first_name', 'last_name', 'role_id', 'attendance_unique_id')
                ->whereIn('id', $userIds)
                ->get()
                ->keyBy('id');

        $lockedUniqueIds = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('month', $month)
            ->where('year', $year)
            ->pluck('unique_id')
            ->flip();

        $allRows = [];
        $totalAmount = 0;
        $appliedAmount = 0;
        $appliedCount = 0;
        $skippedCount = 0;
        $staffIdsCovered = [];

        foreach ($allDeductions as $d) {
            $uid = (string) ($d->unique_id ?? '');
            $userId = (stripos($uid, 'USR-') === 0) ? (int) str_replace('USR-', '', $uid) : null;
            $user = $userId ? ($usersById[$userId] ?? null) : null;
            $name = $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : $uid;
            $displayUid = $user && trim((string) ($user->attendance_unique_id ?? '')) !== '' ? trim((string) $user->attendance_unique_id) : $uid;
            $isLocked = isset($lockedUniqueIds[$uid]);
            $isApplied = (int) ($d->is_applied ?? 1) === 1;
            $amount = (float) ($d->amount ?? 0);

            $totalAmount += $amount;
            if ($isApplied) {
                $appliedCount++;
                $appliedAmount += $amount;
            } else {
                $skippedCount++;
            }

            if ($uid !== '') {
                $staffIdsCovered[$uid] = true;
            }

            $allRows[] = [
                'id' => $d->id,
                'unique_id' => $uid,
                'display_unique_id' => $displayUid,
                'name' => $name,
                'title' => $d->title ?? '',
                'remark' => $d->remark ?? '',
                'amount' => $amount,
                'is_applied' => $isApplied,
                'is_locked' => $isLocked,
                'month' => (int) $d->month,
                'year' => (int) $d->year,
            ];
        }

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $searchName = trim((string) ($request->search_name ?? ''));
        $searchTitle = trim((string) ($request->search_title ?? ''));
        $statusFilter = (string) ($request->status ?? 'all');
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = array_values(array_filter($allRows, function ($r) use ($search, $searchId, $searchName, $searchTitle, $statusFilter) {
            if ($search !== '') {
                $matchName = stripos($r['name'], $search) !== false;
                $matchUid = stripos($r['unique_id'], $search) !== false;
                $matchDisplay = stripos($r['display_unique_id'], $search) !== false;
                $matchTitle = stripos((string)($r['title'] ?? ''), $search) !== false;
                $matchRemark = stripos((string)($r['remark'] ?? ''), $search) !== false;
                if (!$matchName && !$matchUid && !$matchDisplay && !$matchTitle && !$matchRemark) {
                    return false;
                }
            }
            if ($searchId !== '' && stripos((string)$r['id'], $searchId) === false) {
                return false;
            }
            if ($searchName !== '') {
                $matchNameCol = stripos($r['name'], $searchName) !== false;
                $matchUidCol = stripos($r['unique_id'], $searchName) !== false;
                $matchDisplayCol = stripos($r['display_unique_id'], $searchName) !== false;
                if (!$matchNameCol && !$matchUidCol && !$matchDisplayCol) {
                    return false;
                }
            }
            if ($searchTitle !== '' && stripos((string)($r['title'] ?? ''), $searchTitle) === false && stripos((string)($r['remark'] ?? ''), $searchTitle) === false) {
                return false;
            }
            if ($statusFilter === 'applied' && !$r['is_applied']) {
                return false;
            }
            if ($statusFilter === 'skipped' && $r['is_applied']) {
                return false;
            }
            return true;
        }));

        $totalFiltered = count($filteredRows);
        $totalPages = ($perPage === -1 || $totalFiltered === 0) ? 1 : (int) ceil($totalFiltered / $perPage);
        $page = min($page, max(1, $totalPages));
        $pageRows = ($perPage === -1) ? $filteredRows : array_slice($filteredRows, ($page - 1) * $perPage, $perPage);
        $fromRecord = $totalFiltered > 0 ? (($page - 1) * ($perPage === -1 ? $totalFiltered : $perPage) + 1) : 0;
        $toRecord = $totalFiltered > 0 ? min($page * ($perPage === -1 ? $totalFiltered : $perPage), $totalFiltered) : 0;

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $totalFiltered,
            'total_pages' => $totalPages,
            'from' => $fromRecord,
            'to' => $toRecord,
        ];

        $kpis = [
            'total_deductions' => count($allDeductions),
            'applied_deductions' => $appliedCount,
            'skipped_deductions' => $skippedCount,
            'total_amount' => number_format($totalAmount, 2),
            'applied_amount' => number_format($appliedAmount, 2),
            'staff_covered' => count($staffIdsCovered),
        ];

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'html' => view('payroll.staff_deductions_rows', ['rows' => $pageRows, 'month' => $month, 'year' => $year])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        $rows = $pageRows;
        return view('payroll.staff_deductions', compact(
            'rows',
            'staffList',
            'lockedUniqueIds',
            'pagination',
            'kpis',
            'search',
            'perPage',
            'statusFilter',
            'month',
            'year'
        ));
    }

    private function isPayrollGenerated($branchId, $sessionId, string $uniqueId, int $month, int $year): bool
    {
        return PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }
}