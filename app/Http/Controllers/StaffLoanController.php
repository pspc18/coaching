<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollLoan;
use App\Models\PayrollDeduction;
use App\Models\PayrollLoanPayment;
use App\Models\PayrollSalary;
use App\Models\User;
use Carbon\Carbon;
use Session;
use DB;

class StaffLoanController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $typeFilter = (string) ($request->type ?? '');
        $statusFilter = (string) ($request->status ?? 'active'); // active|closed|all

        if ($request->isMethod('post')) {
            $action = (string) $request->input('action', '');

            if ($action === 'add_loan') {
                $request->validate([
                    'unique_id' => 'required|string',
                    'loan_type' => 'required|in:loan,advance',
                    'principal_amount' => 'required|numeric|min:0.01|max:99999999',
                    'monthly_deduction' => 'required|numeric|min:0.01|max:99999999',
                    'start_month' => 'required|integer|min:1|max:12',
                    'start_year' => 'required|integer|min:2000|max:2100',
                    'title' => 'nullable|string|max:100',
                    'remark' => 'nullable|string|max:1000',
                ]);

                $uid = (string) $request->unique_id;
                $staffExists = User::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('id', (int) str_replace('USR-', '', $uid))
                    ->whereNotIn('role_id', [1, 3])
                    ->exists();
                if (stripos($uid, 'USR-') !== 0 || !$staffExists) {
                    return redirect()->to('payroll/staff/loans')->with('error', 'Invalid staff selection.');
                }

                $startFinalized = PayrollSalary::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uid)
                    ->where(function ($query) use ($request) {
                        $query->where('year', '>', (int) $request->start_year)
                            ->orWhere(function ($query) use ($request) {
                                $query->where('year', (int) $request->start_year)
                                    ->where('month', '>=', (int) $request->start_month);
                            });
                    })
                    ->exists();
                if ($startFinalized) {
                    return redirect()->to('payroll/staff/loans')
                        ->with('error', 'Loan cannot start before or within an already finalized payroll period. Choose the next open month.');
                }

                PayrollLoan::create([
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'unique_id' => $request->unique_id,
                    'type' => $request->loan_type,
                    'principal_amount' => (float) $request->principal_amount,
                    'monthly_deduction' => (float) $request->monthly_deduction,
                    'start_month' => (int) $request->start_month,
                    'start_year' => (int) $request->start_year,
                    'is_active' => 1,
                    'title' => $request->title,
                    'remark' => $request->remark,
                    'created_by' => Session::get('id'),
                ]);

                return redirect()->to('payroll/staff/loans')
                    ->with('message', 'Loan/Advance saved successfully.');
            }

            if ($action === 'update_loan') {
                $request->validate([
                    'loan_id' => 'required|integer',
                    'unique_id' => 'required|string',
                    'loan_type' => 'required|in:loan,advance',
                    'principal_amount' => 'required|numeric|min:0.01|max:99999999',
                    'monthly_deduction' => 'required|numeric|min:0.01|max:99999999',
                    'start_month' => 'required|integer|min:1|max:12',
                    'start_year' => 'required|integer|min:2000|max:2100',
                    'is_active' => 'required|in:0,1',
                    'title' => 'nullable|string|max:100',
                    'remark' => 'nullable|string|max:1000',
                ]);

                $loan = PayrollLoan::where('id', (int) $request->loan_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                if (!$loan) {
                    return redirect()->to('payroll/staff/loans')->with('error', 'Loan/Advance not found.');
                }

                $updatedUid = (string) $request->unique_id;
                $updatedStaffExists = stripos($updatedUid, 'USR-') === 0
                    && User::where('branch_id', $branchId)
                        ->where('session_id', $sessionId)
                        ->where('id', (int) str_replace('USR-', '', $updatedUid))
                        ->whereNotIn('role_id', [1, 3])
                        ->exists();
                if (!$updatedStaffExists) {
                    return redirect()->to('payroll/staff/loans')->with('error', 'Invalid staff selection.');
                }

                if ($this->hasFinalizedActivity($loan)) {
                    return redirect()->to('payroll/staff/loans')
                        ->with('error', 'This loan has finalized payroll activity or payments and its terms are locked.');
                }

                $updatedStartFinalized = PayrollSalary::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $updatedUid)
                    ->where(function ($query) use ($request) {
                        $query->where('year', '>', (int) $request->start_year)
                            ->orWhere(function ($query) use ($request) {
                                $query->where('year', (int) $request->start_year)
                                    ->where('month', '>=', (int) $request->start_month);
                            });
                    })
                    ->exists();
                if ($updatedStartFinalized) {
                    return redirect()->to('payroll/staff/loans')
                        ->with('error', 'The selected start month payroll is already finalized.');
                }

                $loan->update([
                        'unique_id' => $request->unique_id,
                        'type' => $request->loan_type,
                        'principal_amount' => (float) $request->principal_amount,
                        'monthly_deduction' => (float) $request->monthly_deduction,
                        'start_month' => (int) $request->start_month,
                        'start_year' => (int) $request->start_year,
                        'is_active' => (int) $request->is_active,
                        'title' => $request->title,
                        'remark' => $request->remark,
                ]);

                return redirect()->to('payroll/staff/loans')
                    ->with('message', 'Loan/Advance updated successfully.');
            }

            if ($action === 'close_loan') {
                $request->validate([
                    'loan_id' => 'required|integer',
                ]);

                $loan = PayrollLoan::where('id', (int) $request->loan_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                if (!$loan) {
                    return redirect()->to('payroll/staff/loans')->with('error', 'Loan/Advance not found.');
                }

                $deducted = (float) PayrollDeduction::join('payroll_salaries', function ($join) {
                        $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                            ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                            ->on('payroll_salaries.year', '=', 'payroll_deductions.year');
                    })
                    ->where('payroll_deductions.loan_id', $loan->id)
                    ->where('payroll_deductions.is_applied', 1)
                    ->sum('payroll_deductions.amount');

                $payments = (float) PayrollLoanPayment::where('loan_id', $loan->id)->sum('amount');
                $remaining = (float) $loan->principal_amount - ($deducted + $payments);

                if ($remaining > 0) {
                    return redirect()->to('payroll/staff/loans')
                        ->with('error', 'Remaining amount exists. Loan cannot be closed.');
                }

                $loan->is_active = 0;
                $loan->save();

                return redirect()->to('payroll/staff/loans')
                    ->with('message', 'Loan/Advance closed successfully.');
            }

            if ($action === 'add_payment') {
                $request->validate([
                    'loan_id' => 'required|integer',
                    'payment_date' => 'required|date',
                    'amount' => 'required|numeric|min:0.01|max:99999999',
                    'remark' => 'nullable|string|max:200',
                ]);

                $loan = PayrollLoan::where('id', (int) $request->loan_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                if (!$loan) {
                    return redirect()->to('payroll/staff/loans')->with('error', 'Loan/Advance not found.');
                }

                $deductedBeforePayment = (float) PayrollDeduction::join('payroll_salaries', function ($join) {
                        $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                            ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                            ->on('payroll_salaries.year', '=', 'payroll_deductions.year')
                            ->on('payroll_salaries.branch_id', '=', 'payroll_deductions.branch_id')
                            ->on('payroll_salaries.session_id', '=', 'payroll_deductions.session_id');
                    })
                    ->where('payroll_deductions.loan_id', $loan->id)
                    ->where('payroll_deductions.is_applied', 1)
                    ->sum('payroll_deductions.amount');
                $paidBeforePayment = (float) PayrollLoanPayment::where('loan_id', $loan->id)->sum('amount');
                $remainingBeforePayment = round(max(
                    0,
                    (float) $loan->principal_amount - ($deductedBeforePayment + $paidBeforePayment)
                ), 2);
                $paymentAmount = round((float) $request->amount, 2);

                if ($remainingBeforePayment <= 0) {
                    return redirect()->to('payroll/staff/loans')->with('error', 'This loan/advance is already fully settled.');
                }
                if ($paymentAmount > $remainingBeforePayment) {
                    return redirect()->to('payroll/staff/loans')
                        ->with('error', 'Payment cannot exceed remaining amount of ' . number_format($remainingBeforePayment, 2) . '.');
                }

                PayrollLoanPayment::create([
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'loan_id' => $loan->id,
                    'payment_date' => $request->payment_date,
                    'amount' => (float) $request->amount,
                    'remark' => $request->remark,
                    'created_by' => Session::get('id'),
                ]);

                // Auto-close if remaining becomes 0 or less.
                $deducted = (float) PayrollDeduction::join('payroll_salaries', function ($join) {
                        $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                            ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                            ->on('payroll_salaries.year', '=', 'payroll_deductions.year')
                            ->on('payroll_salaries.branch_id', '=', 'payroll_deductions.branch_id')
                            ->on('payroll_salaries.session_id', '=', 'payroll_deductions.session_id');
                    })
                    ->where('payroll_deductions.loan_id', $loan->id)
                    ->where('payroll_deductions.is_applied', 1)
                    ->sum('payroll_deductions.amount');
                $payments = (float) (PayrollLoanPayment::where('loan_id', $loan->id)->sum('amount'));
                $remaining = (float) $loan->principal_amount - ($deducted + $payments);
                if ($remaining <= 0) {
                    $loan->is_active = 0;
                    $loan->save();
                }

                return redirect()->to('payroll/staff/loans')
                    ->with('message', 'Payment saved successfully.');
            }
        }

        $allLoans = PayrollLoan::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->get();

        $loanIds = $allLoans->pluck('id')->values()->all();
        $deductedTotals = empty($loanIds)
            ? collect()
            : PayrollDeduction::select('payroll_deductions.loan_id', DB::raw('SUM(payroll_deductions.amount) as total'))
                ->join('payroll_salaries', function ($join) {
                    $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                        ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                        ->on('payroll_salaries.year', '=', 'payroll_deductions.year');
                })
                ->whereIn('payroll_deductions.loan_id', $loanIds)
                ->where('payroll_deductions.is_applied', 1)
                ->groupBy('payroll_deductions.loan_id')
                ->pluck('total', 'payroll_deductions.loan_id');

        $paymentTotals = empty($loanIds)
            ? collect()
            : PayrollLoanPayment::select('loan_id', DB::raw('SUM(amount) as total'))
                ->whereIn('loan_id', $loanIds)
                ->groupBy('loan_id')
                ->pluck('total', 'loan_id');

        $userIds = $allLoans
            ->map(function ($l) {
                $uid = (string) ($l->unique_id ?? '');
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

        $staffList = User::select('id', 'first_name', 'last_name', 'role_id', 'attendance_unique_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->whereNotIn('role_id', [1, 3])
            ->orderBy('first_name')
            ->get();

        $allRows = [];
        $totalRemainingAll = 0;
        $activeLoansCount = 0;
        $closedLoansCount = 0;

        foreach ($allLoans as $loan) {
            $uid = (string) $loan->unique_id;
            $userId = (stripos($uid, 'USR-') === 0) ? (int) str_replace('USR-', '', $uid) : null;
            $user = $userId ? ($usersById[$userId] ?? null) : null;
            $name = $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : $uid;
            $displayUniqueId = $user && trim((string) ($user->attendance_unique_id ?? '')) !== ''
                ? trim((string) $user->attendance_unique_id)
                : $uid;

            $deducted = (float) ($deductedTotals[$loan->id] ?? 0);
            $paid = (float) ($paymentTotals[$loan->id] ?? 0);
            $remaining = max(0, (float) $loan->principal_amount - ($deducted + $paid));

            if ($remaining <= 0 && (int) ($loan->is_active ?? 0) === 1) {
                $loan->is_active = 0;
                $loan->save();
            }
            if ($remaining > 0 && (int) ($loan->is_active ?? 0) === 0) {
                $loan->is_active = 1;
                $loan->save();
            }

            $isActive = (int) ($loan->is_active ?? 0) === 1;
            if ($isActive) {
                $activeLoansCount++;
            } else {
                $closedLoansCount++;
            }
            $totalRemainingAll += $remaining;

            $allRows[] = [
                'id' => $loan->id,
                'unique_id' => $uid,
                'display_unique_id' => $displayUniqueId,
                'name' => $name,
                'type' => $loan->type,
                'title' => $loan->title,
                'remark' => $loan->remark,
                'principal_amount' => (float) $loan->principal_amount,
                'monthly_deduction' => (float) $loan->monthly_deduction,
                'start_month' => (int) $loan->start_month,
                'start_year' => (int) $loan->start_year,
                'start' => sprintf('%02d/%04d', (int) $loan->start_month, (int) $loan->start_year),
                'deducted' => $deducted,
                'paid' => $paid,
                'remaining' => $remaining,
                'is_active' => $isActive,
                'is_locked' => $this->hasFinalizedActivity($loan),
            ];
        }

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $searchName = trim((string) ($request->search_name ?? ''));
        $searchTitle = trim((string) ($request->search_title ?? ''));
        $typeFilter = (string) ($request->type ?? 'all');
        $statusFilter = (string) ($request->status ?? 'all');
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = array_values(array_filter($allRows, function ($r) use ($search, $searchId, $searchName, $searchTitle, $typeFilter, $statusFilter) {
            if ($search !== '') {
                $matchName = stripos($r['name'], $search) !== false;
                $matchUid = stripos($r['unique_id'], $search) !== false;
                $matchDisplay = stripos($r['display_unique_id'], $search) !== false;
                $matchTitle = stripos((string)($r['title'] ?? ''), $search) !== false;
                if (!$matchName && !$matchUid && !$matchDisplay && !$matchTitle) {
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
            if (in_array($typeFilter, ['loan', 'advance'], true) && $r['type'] !== $typeFilter) {
                return false;
            }
            if ($statusFilter === 'active' && !$r['is_active']) {
                return false;
            }
            if ($statusFilter === 'closed' && $r['is_active']) {
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
            'total_loans' => count($allLoans),
            'active_loans' => $activeLoansCount,
            'closed_loans' => $closedLoansCount,
            'total_remaining' => number_format($totalRemainingAll, 2),
        ];

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'html' => view('payroll.staff_loans_rows', ['rows' => $pageRows])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        $rows = $pageRows;
        return view('payroll.staff_loans', compact('rows', 'typeFilter', 'statusFilter', 'staffList', 'pagination', 'kpis', 'search', 'perPage'));
    }

    private function hasFinalizedActivity(PayrollLoan $loan): bool
    {
        $hasGeneratedDeduction = PayrollDeduction::join('payroll_salaries', function ($join) {
                $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                    ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                    ->on('payroll_salaries.year', '=', 'payroll_deductions.year')
                    ->on('payroll_salaries.branch_id', '=', 'payroll_deductions.branch_id')
                    ->on('payroll_salaries.session_id', '=', 'payroll_deductions.session_id');
            })
            ->where('payroll_deductions.loan_id', $loan->id)
            ->exists();

        return $hasGeneratedDeduction
            || PayrollLoanPayment::where('loan_id', $loan->id)->exists();
    }
}
