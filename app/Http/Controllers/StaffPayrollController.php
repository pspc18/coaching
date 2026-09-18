<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\AttendanceMark;
use App\Models\PayrollSetting;
use App\Models\PayrollDeduction;
use App\Models\PayrollLoan;
use App\Models\PayrollSalary;
use App\Models\PayrollPayment;
use App\Exports\PayrollPaymentHistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Carbon\Carbon;
use Session;
use DB;

class StaffPayrollController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $today = Carbon::today();

        // "Till now" means: if selected month is current month => till today, else full month.
        $rangeEnd = ($today->year === $year && $today->month === $month) ? $today : $monthEnd;

        $payrollSetting = PayrollSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$payrollSetting) {
            $payrollSetting = PayrollSetting::create([
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'created_by' => Session::get('id'),
                // defaults handled by migration
            ]);
        }

        $staff = User::select('id', 'first_name', 'last_name', 'role_id', 'salary', 'attendance_unique_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->whereNotIn('role_id', [1, 3]) // exclude Super Admin + Students
            ->orderBy('first_name')
            ->get();

        $uniqueIds = $staff->map(fn ($u) => 'USR-' . $u->id)->values()->all();
        $attendanceKeysByPayrollUid = [];
        $attendanceLookupKeys = [];
        foreach ($staff as $u) {
            $payrollUid = 'USR-' . $u->id;
            $keys = [$payrollUid];
            $attendanceUid = trim((string) ($u->attendance_unique_id ?? ''));
            if ($attendanceUid !== '') {
                $keys[] = $attendanceUid;
            }
            $keys = array_values(array_unique($keys));
            $attendanceKeysByPayrollUid[$payrollUid] = $keys;
            $attendanceLookupKeys = array_merge($attendanceLookupKeys, $keys);
        }
        $attendanceLookupKeys = array_values(array_unique($attendanceLookupKeys));

        $staffRoleIds = $staff->pluck('role_id')->unique()->values();
        $rolesById = Role::whereIn('id', $staffRoleIds)->orderBy('name')->get()->keyBy('id');

        if ($request->isMethod('post')) {
            $action = $request->input('action', 'salary');

            if ($action === 'generate_salary') {
                $request->validate([
                    'unique_id' => 'required|string',
                ]);

                $uid = (string) $request->unique_id;
                $resolved = $this->resolvePayrollMember($branchId, $sessionId, $uid);
                if (!$resolved) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Invalid staff selection.');
                }
                $uid = (string) $resolved['payroll_uid'];

                $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, [$uid]);
                $payload = $this->buildSalaryPayload($branchId, $sessionId, $uid, $month, $year);
                if ($payload === null) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Unable to generate salary for this staff.');
                }
                if (($payload['net_amount'] ?? 0) < 0) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Net salary is negative. Salary cannot be generated.');
                }

                $alreadyGenerated = PayrollSalary::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uid)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();
                if ($alreadyGenerated) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Salary is already finalized. Use regenerate if an authorized correction is required.');
                }

                PayrollSalary::create(array_merge([
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'unique_id' => $uid,
                    'month' => $month,
                    'year' => $year,
                ], $payload));

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', 'Salary generated successfully.');
            }

            if ($action === 'regenerate_salary') {
                $request->validate([
                    'unique_id' => 'required|string',
                ]);

                $resolved = $this->resolvePayrollMember($branchId, $sessionId, (string) $request->unique_id);
                if (!$resolved) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Invalid staff selection.');
                }

                $uid = (string) $resolved['payroll_uid'];
                // Regeneration is the explicit correction workflow, so rebuild
                // this period's EMI before replacing the finalized snapshot.
                $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, [$uid], true);
                $payload = $this->buildSalaryPayload($branchId, $sessionId, $uid, $month, $year);
                if ($payload === null) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Unable to regenerate salary for this staff.');
                }
                if (($payload['net_amount'] ?? 0) < 0) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Net salary is negative. Salary cannot be regenerated.');
                }

                try {
                    DB::transaction(function () use ($branchId, $sessionId, $uid, $month, $year, $payload) {
                        $salary = PayrollSalary::where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('unique_id', $uid)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->lockForUpdate()
                            ->first();

                        if (!$salary) {
                            throw new \RuntimeException('Generate salary before regenerating it.');
                        }

                        $paid = (float) PayrollPayment::where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('unique_id', $uid)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->sum('amount');

                        if ((float) $payload['net_amount'] < $paid) {
                            throw new \RuntimeException(
                                'Regenerated net salary cannot be less than already paid amount of ' . number_format($paid, 2) . '.'
                            );
                        }

                        $salary->fill($payload);
                        $salary->save();
                    });
                } catch (\RuntimeException $e) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', $e->getMessage());
                }

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', 'Salary regenerated successfully.');
            }

            if ($action === 'pay_salary') {
                $request->validate([
                    'unique_id' => 'required|string',
                    'amount' => 'required|numeric|min:0.01|max:99999999',
                ]);

                $resolved = $this->resolvePayrollMember($branchId, $sessionId, (string) $request->unique_id);
                if (!$resolved) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Invalid staff selection.');
                }

                $uid = (string) $resolved['payroll_uid'];

                try {
                    DB::transaction(function () use ($branchId, $sessionId, $uid, $month, $year, $request) {
                        $salary = PayrollSalary::where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('unique_id', $uid)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->lockForUpdate()
                            ->first();

                        if (!$salary) {
                            throw new \RuntimeException('Generate salary before recording payment.');
                        }

                        $paid = (float) PayrollPayment::where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('unique_id', $uid)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->sum('amount');

                        $remaining = round(max(0, (float) $salary->net_amount - $paid), 2);
                        $amount = round((float) $request->amount, 2);

                        if ($remaining <= 0) {
                            throw new \RuntimeException('This salary is already fully paid.');
                        }
                        if ($amount > $remaining) {
                            throw new \RuntimeException('Payment cannot exceed remaining amount of ' . number_format($remaining, 2) . '.');
                        }

                        PayrollPayment::create([
                            'branch_id' => $branchId,
                            'session_id' => $sessionId,
                            'unique_id' => $uid,
                            'month' => $month,
                            'year' => $year,
                            'amount' => $amount,
                            'paid_at' => now(),
                            'paid_by' => Session::get('id'),
                        ]);
                    });
                } catch (\RuntimeException $e) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', $e->getMessage());
                }

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', 'Salary payment recorded successfully.');
            }

            if ($action === 'update_salary_payment') {
                $request->validate([
                    'payment_id' => 'required|integer',
                    'amount' => 'required|numeric|min:0.01|max:99999999',
                    'paid_at' => 'required|date',
                ]);

                try {
                    DB::transaction(function () use ($branchId, $sessionId, $month, $year, $request) {
                        $payment = PayrollPayment::where('id', (int) $request->payment_id)
                            ->where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->lockForUpdate()
                            ->first();
                        if (!$payment) {
                            throw new \RuntimeException('Salary payment record not found.');
                        }

                        $salary = PayrollSalary::where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('unique_id', $payment->unique_id)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->lockForUpdate()
                            ->first();
                        if (!$salary) {
                            throw new \RuntimeException('Finalized salary record not found.');
                        }

                        $otherPaid = (float) PayrollPayment::where('branch_id', $branchId)
                            ->where('session_id', $sessionId)
                            ->where('unique_id', $payment->unique_id)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->where('id', '<>', $payment->id)
                            ->sum('amount');
                        $amount = round((float) $request->amount, 2);
                        $maximumAllowed = round(max(0, (float) $salary->net_amount - $otherPaid), 2);

                        if ($amount > $maximumAllowed) {
                            throw new \RuntimeException(
                                'Edited payment cannot exceed the available amount of ' .
                                number_format($maximumAllowed, 2) . '.'
                            );
                        }

                        $payment->amount = $amount;
                        $payment->paid_at = Carbon::parse($request->paid_at);
                        $payment->save();
                    });
                } catch (\RuntimeException $e) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', $e->getMessage());
                }

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', 'Salary payment updated successfully.');
            }

            if ($action === 'reset_salary') {
                $request->validate([
                    'unique_id' => 'required|string',
                ]);

                $uid = (string) $request->unique_id;
                $resolved = $this->resolvePayrollMember($branchId, $sessionId, $uid);
                if (!$resolved) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Invalid staff selection.');
                }
                $uid = (string) $resolved['payroll_uid'];

                $hasPayments = PayrollPayment::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uid)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();
                if ($hasPayments) {
                    return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                        ->with('error', 'Salary cannot be reset after a payment has been recorded.');
                }

                PayrollSalary::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uid)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->delete();

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', 'Salary reset successfully.');
            }

            if ($action === 'generate_salary_bulk') {
                $blocked = 0;
                $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, $uniqueIds);
                foreach ($uniqueIds as $uid) {
                    $alreadyGenerated = PayrollSalary::where('branch_id', $branchId)
                        ->where('session_id', $sessionId)
                        ->where('unique_id', $uid)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->exists();
                    if ($alreadyGenerated) {
                        continue;
                    }

                    $payload = $this->buildSalaryPayload($branchId, $sessionId, $uid, $month, $year);
                    if ($payload === null) {
                        continue;
                    }
                    if (($payload['net_amount'] ?? 0) < 0) {
                        $blocked++;
                        continue;
                    }
                    PayrollSalary::updateOrCreate(
                        [
                            'branch_id' => $branchId,
                            'session_id' => $sessionId,
                            'unique_id' => $uid,
                            'month' => $month,
                            'year' => $year,
                        ],
                        $payload
                    );
                }

                $msg = $blocked > 0
                    ? 'Salary generated. Skipped ' . $blocked . ' staff due to negative net salary.'
                    : 'Salary generated for all staff.';

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', $msg);
            }

            if ($action === 'settings') {
                $request->validate([
                    'paid_leave_limit' => 'nullable|integer|min:0|max:31',
                    'early_out_weight' => 'required|numeric|min:0|max:1',
                    'leave_weight' => 'required|numeric|min:0|max:1',
                    'halfday_weight' => 'required|numeric|min:0|max:1',
                    'late_frequency' => 'nullable|integer|min:0|max:31',
                    'early_out_frequency' => 'nullable|integer|min:0|max:31',
                ]);

                $payrollSetting->update([
                    'created_by' => Session::get('id'),
                    'paid_leave_limit' => $request->paid_leave_limit === null || $request->paid_leave_limit === '' ? null : (int) $request->paid_leave_limit,
                    'early_out_weight' => (float) $request->early_out_weight,
                    'leave_weight' => (float) $request->leave_weight,
                    'halfday_weight' => (float) $request->halfday_weight,
                    'late_frequency' => $request->late_frequency === null || $request->late_frequency === '' || (int) $request->late_frequency <= 0 ? null : (int) $request->late_frequency,
                    'early_out_frequency' => $request->early_out_frequency === null || $request->early_out_frequency === '' || (int) $request->early_out_frequency <= 0 ? null : (int) $request->early_out_frequency,
                ]);

                return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                    ->with('message', 'Payroll settings saved successfully.');
            }

            $request->validate([
                'salary' => 'array',
                'salary.*' => 'nullable|numeric|min:0|max:99999999',
            ]);

            foreach (($request->salary ?? []) as $userId => $salary) {
                // Allow blank => skip update
                if ($salary === null || $salary === '') {
                    continue;
                }

                User::where('id', $userId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->update(['salary' => $salary]);
            }

            return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                ->with('message', 'Staff salary updated successfully.');
        }

        // Auto-generate loan EMI deductions for selected month so payroll shows net salary correctly.
        $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, $uniqueIds);

        $deductionsAgg = PayrollDeduction::select(
            'unique_id',
            DB::raw('SUM(CASE WHEN loan_id IS NULL THEN amount ELSE 0 END) as manual_total'),
            DB::raw('SUM(CASE WHEN loan_id IS NOT NULL THEN amount ELSE 0 END) as loan_total')
        )
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('unique_id', $uniqueIds)
            ->where('is_applied', 1)
            ->groupBy('unique_id')
            ->get()
            ->keyBy('unique_id');

        $attendanceMarks = AttendanceMark::select('unique_id', 'status', 'date')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('entity_type', 'staff')
            ->whereIn('unique_id', $attendanceLookupKeys)
            ->whereBetween('date', [$monthStart->toDateString(), $rangeEnd->toDateString()])
            ->get();

        $countsByUid = [];
        $attendanceMarksByUid = $attendanceMarks->groupBy('unique_id');
        foreach ($attendanceMarks as $mark) {
            if (!isset($countsByUid[$mark->unique_id][$mark->status])) {
                $countsByUid[$mark->unique_id][$mark->status] = 0;
            }
            $countsByUid[$mark->unique_id][$mark->status]++;
        }

        // Salary calculation (based on payroll settings; can be refined later)
        $weights = [
            'present' => (float) ($payrollSetting->present_weight ?? 1),
            'late' => (float) ($payrollSetting->late_weight ?? 1),
            'early_out' => (float) ($payrollSetting->early_out_weight ?? 1),
            'leave' => (float) ($payrollSetting->leave_weight ?? 1),
            'holiday' => (float) ($payrollSetting->holiday_weight ?? 1),
            'halfday' => (float) ($payrollSetting->halfday_weight ?? 0.5),
            'absent' => (float) ($payrollSetting->absent_weight ?? 0),
        ];
        $statusKeys = array_keys($weights);

        $generatedMap = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('unique_id', $uniqueIds)
            ->get()
            ->keyBy('unique_id');

        $paymentAgg = PayrollPayment::select('unique_id', DB::raw('SUM(amount) as paid_total'), DB::raw('COUNT(*) as payment_count'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('unique_id', $uniqueIds)
            ->groupBy('unique_id')
            ->get()
            ->keyBy('unique_id');

        $paymentRows = PayrollPayment::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('unique_id', $uniqueIds)
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();
        $paymentUsers = User::whereIn('id', $paymentRows->pluck('paid_by')->filter()->unique())
            ->get(['id', 'first_name', 'last_name'])
            ->keyBy('id');
        $paymentsByUid = $paymentRows->groupBy('unique_id');

        $rows = [];
        $totals = [
            'salary_till_now' => 0,
            'manual_deductions' => 0,
            'loan_deductions' => 0,
            'net_salary' => 0,
            'paid_amount' => 0,
            'balance_amount' => 0,
        ];
        $daysInMonth = (int) $monthStart->daysInMonth;

        foreach ($staff as $member) {
            $uid = 'USR-' . $member->id;
            $generatedSalary = $generatedMap[$uid] ?? null;
            $calculationEnd = $rangeEnd->copy();
            if ($generatedSalary && $generatedSalary->generated_at) {
                $generatedDate = Carbon::parse($generatedSalary->generated_at)->startOfDay();
                if ($generatedDate->year === $year && $generatedDate->month === $month && $generatedDate->lt($calculationEnd)) {
                    $calculationEnd = $generatedDate;
                }
            }
            $displayUniqueId = trim((string) ($member->attendance_unique_id ?? '')) !== ''
                ? trim((string) $member->attendance_unique_id)
                : $uid;
            $counts = array_fill_keys($statusKeys, 0);
            $attendanceKeys = $attendanceKeysByPayrollUid[$uid] ?? [$uid];

            foreach ($statusKeys as $k) {
                $sum = 0;
                foreach ($attendanceKeys as $attKey) {
                    if ($generatedSalary) {
                        $sum += ($attendanceMarksByUid[$attKey] ?? collect())
                            ->filter(function ($mark) use ($k, $calculationEnd) {
                                return $mark->status === $k
                                    && Carbon::parse($mark->date)->lte($calculationEnd);
                            })
                            ->count();
                    } else {
                        $sum += (int) (($countsByUid[$attKey][$k] ?? 0));
                    }
                }
                $counts[$k] = $sum;
            }

            $paidDays = 0;

            // Leave allowed logic: paid_leave_limit (NULL = unlimited), extra leaves become 0 day credit.
            $paidDays = $this->calculatePaidDays(
                $counts,
                $weights,
                $payrollSetting->paid_leave_limit,
                $payrollSetting->late_frequency,
                $payrollSetting->early_out_frequency
            );

            $monthlySalary = (float) ($member->salary ?? 0);
            $perDay = ($daysInMonth > 0) ? ($monthlySalary / $daysInMonth) : 0;
            $salaryTillNow = round($perDay * $paidDays, 2);
            $manualDeduction = round((float) (($deductionsAgg[$uid]->manual_total ?? 0)), 2);
            $loanDeduction = round((float) (($deductionsAgg[$uid]->loan_total ?? 0)), 2);
            $deductionTotal = round($manualDeduction + $loanDeduction, 2);
            $netSalary = round($salaryTillNow - $deductionTotal, 2);
            if ($generatedSalary) {
                // A generated payroll is the financial snapshot for this period.
                // Attendance/salary changes must not silently rewrite historical figures.
                $salaryTillNow = round((float) $generatedSalary->gross_amount, 2);
                $deductionTotal = round((float) $generatedSalary->deduction_amount, 2);
                $netSalary = round((float) $generatedSalary->net_amount, 2);
            }
            $payableAmount = $netSalary;
            $paidAmount = round((float) ($paymentAgg[$uid]->paid_total ?? 0), 2);
            $balanceAmount = round(max(0, $payableAmount - $paidAmount), 2);
            $paymentCount = (int) ($paymentAgg[$uid]->payment_count ?? 0);
            $paymentHistory = ($paymentsByUid[$uid] ?? collect())->values()->map(function ($payment, $index) use ($paymentUsers) {
                $paidBy = $paymentUsers[$payment->paid_by] ?? null;
                return [
                    'id' => $payment->id,
                    'number' => $index + 1,
                    'date' => $payment->paid_at ? Carbon::parse($payment->paid_at)->format('d M Y, h:i A') : '-',
                    'paid_at_input' => $payment->paid_at ? Carbon::parse($payment->paid_at)->format('Y-m-d\TH:i') : '',
                    'amount' => round((float) $payment->amount, 2),
                    'recorded_by' => $paidBy
                        ? trim(($paidBy->first_name ?? '') . ' ' . ($paidBy->last_name ?? ''))
                        : 'System User',
                ];
            })->all();

            $totals['salary_till_now'] += $salaryTillNow;
            $totals['manual_deductions'] += $manualDeduction;
            $totals['loan_deductions'] += $loanDeduction;
            $totals['net_salary'] += $netSalary;
            $totals['paid_amount'] += $paidAmount;
            $totals['balance_amount'] += $balanceAmount;

            $rows[] = [
                'user_id' => $member->id,
                'role_id' => $member->role_id,
                'unique_id' => $displayUniqueId,
                'name' => trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')),
                'role' => $rolesById[$member->role_id]->name ?? '-',
                'monthly_salary' => $monthlySalary,
                'working_days' => $daysInMonth,
                'per_day_salary' => round($perDay, 2),
                'counts' => $counts,
                'paid_days' => $paidDays,
                'salary_till_now' => $salaryTillNow,
                'manual_deductions' => $manualDeduction,
                'loan_deductions' => $loanDeduction,
                'deductions' => $deductionTotal,
                'net_salary' => $netSalary,
                'generated_at' => $generatedSalary->generated_at ?? null,
                'calculation_through' => $calculationEnd->format('d M Y'),
                'payable_amount' => $payableAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'payment_count' => $paymentCount,
                'payment_history' => $paymentHistory,
            ];
        }

        $search = trim((string) ($request->search ?? ''));
        $searchUid = trim((string) ($request->search_uid ?? ''));
        $searchName = trim((string) ($request->search_name ?? ''));
        $roleFilter = $request->role_id ? (int) $request->role_id : null;
        $statusFilter = (string) ($request->status ?? 'all');
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = array_values(array_filter($rows, function ($r) use ($search, $searchUid, $searchName, $roleFilter, $statusFilter) {
            if ($search !== '') {
                $matchName = stripos($r['name'], $search) !== false;
                $matchUid = stripos($r['unique_id'], $search) !== false;
                $matchRole = stripos($r['role'], $search) !== false;
                if (!$matchName && !$matchUid && !$matchRole) {
                    return false;
                }
            }
            if ($searchUid !== '' && stripos($r['unique_id'], $searchUid) === false) {
                return false;
            }
            if ($searchName !== '') {
                $matchNameCol = stripos($r['name'], $searchName) !== false;
                $matchRoleCol = stripos($r['role'], $searchName) !== false;
                if (!$matchNameCol && !$matchRoleCol) {
                    return false;
                }
            }
            if ($roleFilter !== null && (int) $r['role_id'] !== $roleFilter) {
                return false;
            }
            if ($statusFilter === 'finalized' && empty($r['generated_at'])) {
                return false;
            }
            if ($statusFilter === 'pending' && !empty($r['generated_at'])) {
                return false;
            }
            if ($statusFilter === 'paid' && (empty($r['generated_at']) || (float) $r['balance_amount'] > 0)) {
                return false;
            }
            if ($statusFilter === 'balance' && (float) $r['balance_amount'] <= 0) {
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

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'html' => view('payroll.staff_rows', [
                    'rows' => $pageRows,
                    'month' => $month,
                    'year' => $year,
                    'totals' => $totals
                ])->render(),
                'pagination' => $pagination,
                'kpis' => [
                    'total_staff' => count($staff),
                    'salary_till_now' => number_format($totals['salary_till_now'], 2),
                    'manual_deductions' => number_format($totals['manual_deductions'], 2),
                    'loan_deductions' => number_format($totals['loan_deductions'], 2),
                    'total_deductions' => number_format($totals['manual_deductions'] + $totals['loan_deductions'], 2),
                    'net_salary' => number_format($totals['net_salary'], 2),
                    'paid_amount' => number_format($totals['paid_amount'], 2),
                    'balance_amount' => number_format($totals['balance_amount'], 2),
                ]
            ]);
        }

        $rows = $pageRows;
        return view('payroll.staff', compact(
            'rows',
            'staff',
            'rolesById',
            'month',
            'year',
            'monthStart',
            'rangeEnd',
            'payrollSetting',
            'totals',
            'pagination',
            'search',
            'roleFilter',
            'statusFilter',
            'perPage'
        ));
    }

    public function edit(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $uniqueId = (string) ($request->staff ?? '');
        $resolved = $this->resolvePayrollMember($branchId, $sessionId, $uniqueId);
        if (!$resolved) {
            return redirect()->to('payroll/staff')->with('error', 'Please select a staff member.');
        }

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $today = Carbon::today();
        $rangeEnd = ($today->year === $year && $today->month === $month) ? $today : $monthEnd;
        $salarySnapshot = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        if ($salarySnapshot && $salarySnapshot->generated_at) {
            $finalizedDate = Carbon::parse($salarySnapshot->generated_at)->startOfDay();
            if ($finalizedDate->year === $year && $finalizedDate->month === $month && $finalizedDate->lt($rangeEnd)) {
                $rangeEnd = $finalizedDate;
            }
        }

        $payrollSetting = PayrollSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$payrollSetting) {
            $payrollSetting = PayrollSetting::create([
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'created_by' => Session::get('id'),
            ]);
        }

        $roleName = Role::where('id', $member->role_id)->value('name') ?? '-';

        // Handle POST actions (deductions + salary update)
        if ($request->isMethod('post')) {
            $action = (string) $request->input('action', '');

            if ($action === 'add_deduction') {
                $request->validate([
                    'amount' => 'required|numeric|min:0.01|max:99999999',
                    'title' => 'nullable|string|max:100',
                    'remark' => 'nullable|string|max:1000',
                ]);

                PayrollDeduction::create([
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'unique_id' => $uniqueId,
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

                return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                    ->with('message', 'Deduction added successfully.');
            }

            if ($action === 'delete_deduction') {
                $request->validate([
                    'deduction_id' => 'required|integer',
                ]);

                PayrollDeduction::where('id', (int) $request->deduction_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uniqueId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->whereNull('loan_id')
                    ->where('type', 'manual')
                    ->delete();

                return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                    ->with('message', 'Deduction removed successfully.');
            }

            if ($action === 'toggle_deduction') {
                $request->validate([
                    'deduction_id' => 'required|integer',
                    'is_applied' => 'required|in:0,1',
                ]);

                PayrollDeduction::where('id', (int) $request->deduction_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uniqueId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->update(['is_applied' => (int) $request->is_applied]);

                return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                    ->with('message', 'Deduction status updated successfully.');
            }

            if ($action === 'update_salary') {
                $request->validate([
                    'salary' => 'required|numeric|min:0|max:99999999',
                ]);

                User::where('id', $member->id)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->update(['salary' => $request->salary]);

                return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                    ->with('message', 'Salary updated successfully.');
            }

            if ($action === 'update_loan_emi') {
                $request->validate([
                    'deduction_id' => 'required|integer',
                    'amount' => 'required|numeric|min:0|max:99999999',
                ]);

                $deduction = PayrollDeduction::where('id', (int) $request->deduction_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uniqueId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->whereNotNull('loan_id')
                    ->first();

                if (!$deduction) {
                    return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                        ->with('error', 'Loan deduction not found.');
                }

                $newAmount = (float) $request->amount;

                $loan = PayrollLoan::where('id', $deduction->loan_id)->first();
                if (!$loan) {
                    return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                        ->with('error', 'Loan not found.');
                }

                // Calculate gross till now for available salary limit
                $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $today = Carbon::today();
                $rangeEnd = ($today->year === $year && $today->month === $month) ? $today : $monthEnd;

                $payrollSetting = PayrollSetting::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                if (!$payrollSetting) {
                    $payrollSetting = PayrollSetting::create([
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                        'created_by' => Session::get('id'),
                    ]);
                }

                $statusCounts = AttendanceMark::select('status', DB::raw('COUNT(*) as cnt'))
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('entity_type', 'staff')
                    ->where('unique_id', $uniqueId)
                    ->whereBetween('date', [$monthStart->toDateString(), $rangeEnd->toDateString()])
                    ->groupBy('status')
                    ->get();

                $countsByStatus = [
                    'present' => 0,
                    'absent' => 0,
                    'leave' => 0,
                    'late' => 0,
                    'early_out' => 0,
                    'halfday' => 0,
                    'holiday' => 0,
                ];
                foreach ($statusCounts as $row) {
                    if (array_key_exists($row->status, $countsByStatus)) {
                        $countsByStatus[$row->status] = (int) $row->cnt;
                    }
                }

                $weights = [
                    'present' => (float) ($payrollSetting->present_weight ?? 1),
                    'late' => (float) ($payrollSetting->late_weight ?? 1),
                    'early_out' => (float) ($payrollSetting->early_out_weight ?? 1),
                    'leave' => (float) ($payrollSetting->leave_weight ?? 1),
                    'holiday' => (float) ($payrollSetting->holiday_weight ?? 1),
                    'halfday' => (float) ($payrollSetting->halfday_weight ?? 0.5),
                    'absent' => (float) ($payrollSetting->absent_weight ?? 0),
                ];

                $paidDays = $this->calculatePaidDays(
                    $countsByStatus,
                    $weights,
                    $payrollSetting->paid_leave_limit,
                    $payrollSetting->late_frequency,
                    $payrollSetting->early_out_frequency
                );

                $daysInMonth = (int) $monthStart->daysInMonth;
                $monthlySalary = (float) ($member->salary ?? 0);
                $perDay = ($daysInMonth > 0) ? ($monthlySalary / $daysInMonth) : 0;
                $grossTillNow = round($perDay * $paidDays, 2);

                $manualAppliedTotal = (float) PayrollDeduction::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uniqueId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->whereNull('loan_id')
                    ->where('is_applied', 1)
                    ->sum('amount');

                $loanAppliedOther = (float) PayrollDeduction::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('unique_id', $uniqueId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->whereNotNull('loan_id')
                    ->where('is_applied', 1)
                    ->where('id', '!=', $deduction->id)
                    ->sum('amount');

                $maxBySalary = max(0, $grossTillNow - $manualAppliedTotal - $loanAppliedOther);

                $deductedExcl = (float) PayrollDeduction::join('payroll_salaries', function ($join) {
                        $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                            ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                            ->on('payroll_salaries.year', '=', 'payroll_deductions.year');
                    })
                    ->where('payroll_deductions.loan_id', $loan->id)
                    ->where('payroll_deductions.is_applied', 1)
                    ->where('payroll_deductions.id', '!=', $deduction->id)
                    ->sum('payroll_deductions.amount');

                $paymentsTotal = (float) \App\Models\PayrollLoanPayment::where('loan_id', $loan->id)->sum('amount');
                $remainingAllowed = max(0, (float) $loan->principal_amount - ($deductedExcl + $paymentsTotal));

                if ($newAmount > $remainingAllowed) {
                    return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                        ->with('error', 'EMI cannot exceed remaining loan amount (' . number_format($remainingAllowed, 2) . ').');
                }

                if ($newAmount > $maxBySalary) {
                    return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                        ->with('error', 'EMI cannot exceed available salary (' . number_format($maxBySalary, 2) . ').');
                }

                $deduction->update([
                    'amount' => $newAmount,
                    'original_amount' => $newAmount,
                ]);

                PayrollLoan::where('id', $deduction->loan_id)
                    ->update(['monthly_deduction' => $newAmount]);

                return redirect()->to('payroll/staff/edit?staff=' . $uniqueId . '&month=' . $month . '&year=' . $year)
                    ->with('message', 'Loan EMI updated successfully.');
            }
        }

        // Ensure loan EMI rows exist for the selected month before calculating totals.
        $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, [$uniqueId]);

        // Attendance counts for this staff in selected month range
        $statusCounts = AttendanceMark::select('status', DB::raw('COUNT(*) as cnt'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('entity_type', 'staff')
            ->whereIn('unique_id', $attendanceKeys)
            ->whereBetween('date', [$monthStart->toDateString(), $rangeEnd->toDateString()])
            ->groupBy('status')
            ->get();

        $countsByStatus = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'late' => 0,
            'early_out' => 0,
            'halfday' => 0,
            'holiday' => 0,
        ];
        foreach ($statusCounts as $row) {
            if (array_key_exists($row->status, $countsByStatus)) {
                $countsByStatus[$row->status] = (int) $row->cnt;
            }
        }

        $weights = [
            'present' => (float) ($payrollSetting->present_weight ?? 1),
            'late' => (float) ($payrollSetting->late_weight ?? 1),
            'early_out' => (float) ($payrollSetting->early_out_weight ?? 1),
            'leave' => (float) ($payrollSetting->leave_weight ?? 1),
            'holiday' => (float) ($payrollSetting->holiday_weight ?? 1),
            'halfday' => (float) ($payrollSetting->halfday_weight ?? 0.5),
            'absent' => (float) ($payrollSetting->absent_weight ?? 0),
        ];

        $paidDays = $this->calculatePaidDays(
            $countsByStatus,
            $weights,
            $payrollSetting->paid_leave_limit,
            $payrollSetting->late_frequency,
            $payrollSetting->early_out_frequency
        );

        $daysInMonth = (int) $monthStart->daysInMonth;
        $monthlySalary = (float) ($member->salary ?? 0);
        $perDay = ($daysInMonth > 0) ? ($monthlySalary / $daysInMonth) : 0;
        $grossTillNow = round($perDay * $paidDays, 2);

        $manualDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereNull('loan_id')
            ->where('type', 'manual')
            ->orderBy('id', 'desc')
            ->get();

        $loanDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereNotNull('loan_id')
            ->orderBy('id', 'desc')
            ->get();

        $manualAppliedTotal = round((float) ($manualDeductions->where('is_applied', 1)->sum('amount')), 2);
        $loanAppliedTotal = round((float) ($loanDeductions->where('is_applied', 1)->sum('amount')), 2);
        $deductionTotal = round($manualAppliedTotal + $loanAppliedTotal, 2);
        $netTillNow = round($grossTillNow - $deductionTotal, 2);

        $loans = PayrollLoan::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->orderBy('id', 'desc')
            ->get();

        $loanIds = $loans->pluck('id')->values();
        $loanDeductedTotals = $loanIds->isEmpty()
            ? collect()
            : PayrollDeduction::select('payroll_deductions.loan_id', DB::raw('SUM(payroll_deductions.amount) as total'))
                ->join('payroll_salaries', function ($join) {
                    $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                        ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                        ->on('payroll_salaries.year', '=', 'payroll_deductions.year');
                })
                ->whereIn('payroll_deductions.loan_id', $loanIds->all())
                ->where('payroll_deductions.branch_id', $branchId)
                ->where('payroll_deductions.session_id', $sessionId)
                ->groupBy('payroll_deductions.loan_id')
                ->pluck('total', 'payroll_deductions.loan_id');

        $loanThisMonth = $loanIds->isEmpty()
            ? collect()
            : PayrollDeduction::whereIn('loan_id', $loanIds->all())
                ->where('month', $month)
                ->where('year', $year)
                ->pluck('amount', 'loan_id');

        $loanPaymentTotals = $loanIds->isEmpty()
            ? collect()
            : \App\Models\PayrollLoanPayment::select('loan_id', DB::raw('SUM(amount) as total'))
                ->whereIn('loan_id', $loanIds->all())
                ->groupBy('loan_id')
                ->pluck('total', 'loan_id');

        $loanRemainingById = [];
        foreach ($loans as $loan) {
            $ded = (float) ($loanDeductedTotals[$loan->id] ?? 0);
            $paid = (float) ($loanPaymentTotals[$loan->id] ?? 0);
            $loanRemainingById[$loan->id] = max(0, (float) $loan->principal_amount - ($ded + $paid));
        }

        return view('payroll.staff_edit', compact(
            'member',
            'uniqueId',
            'displayUniqueId',
            'roleName',
            'month',
            'year',
            'monthStart',
            'monthEnd',
            'rangeEnd',
            'countsByStatus',
            'paidDays',
            'daysInMonth',
            'monthlySalary',
            'perDay',
            'grossTillNow',
            'manualDeductions',
            'loanDeductions',
            'manualAppliedTotal',
            'loanAppliedTotal',
            'deductionTotal',
            'netTillNow',
            'payrollSetting',
            'loans',
            'loanDeductedTotals',
            'loanThisMonth',
            'loanRemainingById'
        ));
    }

    public function slip(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $uniqueId = (string) ($request->staff ?? '');
        $resolved = $this->resolvePayrollMember($branchId, $sessionId, $uniqueId);
        if (!$resolved) {
            return redirect()->to('payroll/staff')->with('error', 'Please select a staff member.');
        }

        $uniqueId = (string) $resolved['payroll_uid'];
        $member = $resolved['member'];
        $attendanceKeys = $resolved['attendance_keys'];
        $displayUniqueId = trim((string) ($member->attendance_unique_id ?? '')) !== ''
            ? trim((string) $member->attendance_unique_id)
            : $uniqueId;

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $today = Carbon::today();
        $rangeEnd = ($today->year === $year && $today->month === $month) ? $today : $monthEnd;

        $payrollSetting = PayrollSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$payrollSetting) {
            $payrollSetting = PayrollSetting::create([
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'created_by' => Session::get('id'),
            ]);
        }

        $roleName = Role::where('id', $member->role_id)->value('name') ?? '-';

        $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, [$uniqueId]);

        $statusCounts = AttendanceMark::select('status', DB::raw('COUNT(*) as cnt'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('entity_type', 'staff')
            ->whereIn('unique_id', $attendanceKeys)
            ->whereBetween('date', [$monthStart->toDateString(), $rangeEnd->toDateString()])
            ->groupBy('status')
            ->get();

        $countsByStatus = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'late' => 0,
            'early_out' => 0,
            'halfday' => 0,
            'holiday' => 0,
        ];
        foreach ($statusCounts as $row) {
            if (array_key_exists($row->status, $countsByStatus)) {
                $countsByStatus[$row->status] = (int) $row->cnt;
            }
        }

        $weights = [
            'present' => (float) ($payrollSetting->present_weight ?? 1),
            'late' => (float) ($payrollSetting->late_weight ?? 1),
            'early_out' => (float) ($payrollSetting->early_out_weight ?? 1),
            'leave' => (float) ($payrollSetting->leave_weight ?? 1),
            'holiday' => (float) ($payrollSetting->holiday_weight ?? 1),
            'halfday' => (float) ($payrollSetting->halfday_weight ?? 0.5),
            'absent' => (float) ($payrollSetting->absent_weight ?? 0),
        ];

        $paidDays = $this->calculatePaidDays(
            $countsByStatus,
            $weights,
            $payrollSetting->paid_leave_limit,
            $payrollSetting->late_frequency,
            $payrollSetting->early_out_frequency
        );

        $daysInMonth = (int) $monthStart->daysInMonth;
        $monthlySalary = (float) ($member->salary ?? 0);
        $perDay = ($daysInMonth > 0) ? ($monthlySalary / $daysInMonth) : 0;
        $gross = round($perDay * $paidDays, 2);

        $manualDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereNull('loan_id')
            ->where('type', 'manual')
            ->where('is_applied', 1)
            ->orderBy('id', 'desc')
            ->get();

        $loanDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereNotNull('loan_id')
            ->where('is_applied', 1)
            ->orderBy('id', 'desc')
            ->get();

        $manualTotal = round((float) $manualDeductions->sum('amount'), 2);
        $loanTotal = round((float) $loanDeductions->sum('amount'), 2);
        $deductionTotal = round($manualTotal + $loanTotal, 2);
        $net = round($gross - $deductionTotal, 2);

        return view('payroll.staff_slip', compact(
            'member',
            'uniqueId',
            'displayUniqueId',
            'roleName',
            'month',
            'year',
            'monthStart',
            'monthEnd',
            'rangeEnd',
            'countsByStatus',
            'paidDays',
            'daysInMonth',
            'monthlySalary',
            'perDay',
            'gross',
            'manualDeductions',
            'loanDeductions',
            'manualTotal',
            'loanTotal',
            'deductionTotal',
            'net',
            'payrollSetting'
        ));
    }

    public function paymentsExcel(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));

        $resolved = $this->resolvePayrollMember($branchId, $sessionId, (string) ($request->staff ?? ''));
        if (!$resolved) {
            return redirect()->to('payroll/staff')->with('error', 'Invalid staff selection.');
        }

        $uid = (string) $resolved['payroll_uid'];
        $member = $resolved['member'];
        $displayUid = trim((string) ($member->attendance_unique_id ?? '')) ?: $uid;
        $staffName = trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? ''));
        $salary = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uid)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        if (!$salary) {
            return redirect()->to('payroll/staff?month=' . $month . '&year=' . $year)
                ->with('error', 'Generate salary before exporting its payment history.');
        }

        $payments = PayrollPayment::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uid)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();
        $users = User::whereIn('id', $payments->pluck('paid_by')->filter()->unique())
            ->get(['id', 'first_name', 'last_name'])
            ->keyBy('id');
        $totalPaid = round((float) $payments->sum('amount'), 2);
        $pending = round(max(0, (float) $salary->net_amount - $totalPaid), 2);
        $period = Carbon::createFromDate($year, $month, 1)->format('F Y');

        $rows = $payments->values()->map(function ($payment, $index) use (
            $users, $displayUid, $staffName, $period, $salary, $totalPaid, $pending
        ) {
            $user = $users[$payment->paid_by] ?? null;
            return [
                $index + 1,
                $displayUid,
                $staffName,
                $period,
                $payment->paid_at ? Carbon::parse($payment->paid_at)->format('d-m-Y h:i A') : '-',
                (float) $payment->amount,
                $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : 'System User',
                (float) $salary->net_amount,
                $totalPaid,
                $pending,
            ];
        })->all();

        if (empty($rows)) {
            $rows[] = [1, $displayUid, $staffName, $period, 'No payments recorded', 0, '-', (float) $salary->net_amount, 0, (float) $salary->net_amount];
        }

        $safeUid = preg_replace('/[^A-Za-z0-9_-]/', '-', $displayUid);
        return Excel::download(
            new PayrollPaymentHistoryExport($rows),
            "salary-payments-{$safeUid}-{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }

    public function slipPdf(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $uniqueId = (string) ($request->staff ?? '');
        $resolved = $this->resolvePayrollMember($branchId, $sessionId, $uniqueId);
        if (!$resolved) {
            return redirect()->to('payroll/staff')->with('error', 'Please select a staff member.');
        }

        $uniqueId = (string) $resolved['payroll_uid'];
        $member = $resolved['member'];
        $attendanceKeys = $resolved['attendance_keys'];
        $displayUniqueId = trim((string) ($member->attendance_unique_id ?? '')) !== ''
            ? trim((string) $member->attendance_unique_id)
            : $uniqueId;

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $today = Carbon::today();
        $rangeEnd = ($today->year === $year && $today->month === $month) ? $today : $monthEnd;
        $slipSalarySnapshot = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        if ($slipSalarySnapshot && $slipSalarySnapshot->generated_at) {
            $finalizedDate = Carbon::parse($slipSalarySnapshot->generated_at)->startOfDay();
            if ($finalizedDate->year === $year && $finalizedDate->month === $month && $finalizedDate->lt($rangeEnd)) {
                $rangeEnd = $finalizedDate;
            }
        }
        $salarySnapshot = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        if ($salarySnapshot && $salarySnapshot->generated_at) {
            $finalizedDate = Carbon::parse($salarySnapshot->generated_at)->startOfDay();
            if ($finalizedDate->year === $year && $finalizedDate->month === $month && $finalizedDate->lt($rangeEnd)) {
                $rangeEnd = $finalizedDate;
            }
        }

        $payrollSetting = PayrollSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$payrollSetting) {
            $payrollSetting = PayrollSetting::create([
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'created_by' => Session::get('id'),
            ]);
        }

        $roleName = Role::where('id', $member->role_id)->value('name') ?? '-';

        $this->syncLoanDeductionsForMonth($branchId, $sessionId, $month, $year, [$uniqueId]);

        $statusCounts = AttendanceMark::select('status', DB::raw('COUNT(*) as cnt'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('entity_type', 'staff')
            ->whereIn('unique_id', $attendanceKeys)
            ->whereBetween('date', [$monthStart->toDateString(), $rangeEnd->toDateString()])
            ->groupBy('status')
            ->get();

        $countsByStatus = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'late' => 0,
            'early_out' => 0,
            'halfday' => 0,
            'holiday' => 0,
        ];
        foreach ($statusCounts as $row) {
            if (array_key_exists($row->status, $countsByStatus)) {
                $countsByStatus[$row->status] = (int) $row->cnt;
            }
        }

        $weights = [
            'present' => (float) ($payrollSetting->present_weight ?? 1),
            'late' => (float) ($payrollSetting->late_weight ?? 1),
            'early_out' => (float) ($payrollSetting->early_out_weight ?? 1),
            'leave' => (float) ($payrollSetting->leave_weight ?? 1),
            'holiday' => (float) ($payrollSetting->holiday_weight ?? 1),
            'halfday' => (float) ($payrollSetting->halfday_weight ?? 0.5),
            'absent' => (float) ($payrollSetting->absent_weight ?? 0),
        ];

        $paidDays = $this->calculatePaidDays(
            $countsByStatus,
            $weights,
            $payrollSetting->paid_leave_limit,
            $payrollSetting->late_frequency,
            $payrollSetting->early_out_frequency
        );

        $daysInMonth = (int) $monthStart->daysInMonth;
        $monthlySalary = (float) ($member->salary ?? 0);
        $perDay = ($daysInMonth > 0) ? ($monthlySalary / $daysInMonth) : 0;
        $gross = round($perDay * $paidDays, 2);

        $manualDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereNull('loan_id')
            ->where('type', 'manual')
            ->where('is_applied', 1)
            ->orderBy('id', 'desc')
            ->get();

        $loanDeductions = PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereNotNull('loan_id')
            ->where('is_applied', 1)
            ->orderBy('id', 'desc')
            ->get();

        $manualTotal = round((float) $manualDeductions->sum('amount'), 2);
        $loanTotal = round((float) $loanDeductions->sum('amount'), 2);
        $deductionTotal = round($manualTotal + $loanTotal, 2);
        $net = round($gross - $deductionTotal, 2);

        if ($salarySnapshot) {
            $gross = round((float) $salarySnapshot->gross_amount, 2);
            $deductionTotal = round((float) $salarySnapshot->deduction_amount, 2);
            $net = round((float) $salarySnapshot->net_amount, 2);
        }

        $payments = PayrollPayment::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();
        $paymentUsers = User::whereIn('id', $payments->pluck('paid_by')->filter()->unique())
            ->get(['id', 'first_name', 'last_name'])
            ->keyBy('id');
        $totalPaid = round((float) $payments->sum('amount'), 2);
        $salaryPending = round(max(0, $net - $totalPaid), 2);

        $loans = PayrollLoan::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where(function ($query) use ($year, $month) {
                $query->where('start_year', '<', $year)
                    ->orWhere(function ($query) use ($year, $month) {
                        $query->where('start_year', $year)->where('start_month', '<=', $month);
                    });
            })
            ->orderBy('id')
            ->get();
        $loanSummaries = $loans->map(function ($loan) use ($monthEnd) {
            $salaryRecovered = (float) PayrollDeduction::join('payroll_salaries', function ($join) {
                    $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                        ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                        ->on('payroll_salaries.year', '=', 'payroll_deductions.year')
                        ->on('payroll_salaries.branch_id', '=', 'payroll_deductions.branch_id')
                        ->on('payroll_salaries.session_id', '=', 'payroll_deductions.session_id');
                })
                ->where('payroll_deductions.loan_id', $loan->id)
                ->where('payroll_deductions.is_applied', 1)
                ->where(function ($query) use ($monthEnd) {
                    $query->where('payroll_deductions.year', '<', $monthEnd->year)
                        ->orWhere(function ($query) use ($monthEnd) {
                            $query->where('payroll_deductions.year', $monthEnd->year)
                                ->where('payroll_deductions.month', '<=', $monthEnd->month);
                        });
                })
                ->sum('payroll_deductions.amount');
            $directPaid = (float) \App\Models\PayrollLoanPayment::where('loan_id', $loan->id)
                ->whereDate('payment_date', '<=', $monthEnd->toDateString())
                ->sum('amount');
            $recovered = round($salaryRecovered + $directPaid, 2);
            return [
                'type' => ucfirst($loan->type ?? 'loan'),
                'title' => $loan->title ?: ucfirst($loan->type ?? 'loan'),
                'principal' => round((float) $loan->principal_amount, 2),
                'monthly' => round((float) $loan->monthly_deduction, 2),
                'recovered' => $recovered,
                'pending' => round(max(0, (float) $loan->principal_amount - $recovered), 2),
            ];
        });
        $loanPrincipalTotal = round((float) $loanSummaries->sum('principal'), 2);
        $loanRecoveredTotal = round((float) $loanSummaries->sum('recovered'), 2);
        $loanPendingTotal = round((float) $loanSummaries->sum('pending'), 2);
        $branch = \App\Models\Master\Branch::find($branchId);
        $organization = \App\Models\Setting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        $html = view('payroll.staff_slip_pdf', compact(
            'member',
            'uniqueId',
            'displayUniqueId',
            'roleName',
            'month',
            'year',
            'monthStart',
            'rangeEnd',
            'countsByStatus',
            'paidDays',
            'daysInMonth',
            'monthlySalary',
            'perDay',
            'gross',
            'manualDeductions',
            'loanDeductions',
            'manualTotal',
            'loanTotal',
            'deductionTotal',
            'net',
            'payrollSetting',
            'salarySnapshot',
            'monthEnd',
            'payments',
            'paymentUsers',
            'totalPaid',
            'salaryPending',
            'loanSummaries',
            'loanPrincipalTotal',
            'loanRecoveredTotal',
            'loanPendingTotal',
            'branch',
            'organization'
        ))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfFileUniqueId = preg_replace('/[^A-Za-z0-9_-]/', '-', (string) ($displayUniqueId ?? $uniqueId));
        return $dompdf->stream("salary-slip-{$pdfFileUniqueId}-{$month}-{$year}.pdf");
    }

    private function syncLoanDeductionsForMonth(
        int $branchId,
        int $sessionId,
        int $month,
        int $year,
        array $uniqueIds,
        bool $allowFinalizedCorrection = false
    ): void
    {
        $selectedKey = ($year * 12) + $month;

        $loans = PayrollLoan::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereIn('unique_id', $uniqueIds)
            ->get();

        if ($loans->isEmpty()) {
            return;
        }

        $loanIds = $loans->pluck('id')->values()->all();

        // Deducted amount *before* the selected month (so current month EMI is calculated correctly).
        $deductedBefore = PayrollDeduction::select('payroll_deductions.loan_id', DB::raw('SUM(payroll_deductions.amount) as total'))
            ->join('payroll_salaries', function ($join) {
                $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                    ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                    ->on('payroll_salaries.year', '=', 'payroll_deductions.year')
                    ->on('payroll_salaries.branch_id', '=', 'payroll_deductions.branch_id')
                    ->on('payroll_salaries.session_id', '=', 'payroll_deductions.session_id');
            })
            ->where('payroll_deductions.branch_id', $branchId)
            ->where('payroll_deductions.session_id', $sessionId)
            ->whereIn('payroll_deductions.loan_id', $loanIds)
            ->where('payroll_deductions.is_applied', 1)
            ->where(function ($q) use ($year, $month) {
                $q->where('payroll_deductions.year', '<', $year)
                    ->orWhere(function ($q) use ($year, $month) {
                        $q->where('payroll_deductions.year', '=', $year)->where('payroll_deductions.month', '<', $month);
                    });
            })
            ->groupBy('payroll_deductions.loan_id')
            ->pluck('total', 'payroll_deductions.loan_id');

        $skippedBefore = PayrollDeduction::select('payroll_deductions.loan_id', DB::raw('SUM(payroll_deductions.amount) as total'))
            ->join('payroll_salaries', function ($join) {
                $join->on('payroll_salaries.unique_id', '=', 'payroll_deductions.unique_id')
                    ->on('payroll_salaries.month', '=', 'payroll_deductions.month')
                    ->on('payroll_salaries.year', '=', 'payroll_deductions.year')
                    ->on('payroll_salaries.branch_id', '=', 'payroll_deductions.branch_id')
                    ->on('payroll_salaries.session_id', '=', 'payroll_deductions.session_id');
            })
            ->where('payroll_deductions.branch_id', $branchId)
            ->where('payroll_deductions.session_id', $sessionId)
            ->whereIn('payroll_deductions.loan_id', $loanIds)
            ->where('payroll_deductions.is_applied', 0)
            ->where(function ($q) use ($year, $month) {
                $q->where('payroll_deductions.year', '<', $year)
                    ->orWhere(function ($q) use ($year, $month) {
                        $q->where('payroll_deductions.year', '=', $year)->where('payroll_deductions.month', '<', $month);
                    });
            })
            ->groupBy('payroll_deductions.loan_id')
            ->pluck('total', 'payroll_deductions.loan_id');

        $paymentsBefore = \App\Models\PayrollLoanPayment::select('loan_id', DB::raw('SUM(amount) as total'))
            ->whereIn('loan_id', $loanIds)
            ->whereDate('payment_date', '<', Carbon::createFromDate($year, $month, 1)->toDateString())
            ->groupBy('loan_id')
            ->pluck('total', 'loan_id');

        $paymentsToEnd = \App\Models\PayrollLoanPayment::select('loan_id', DB::raw('SUM(amount) as total'))
            ->whereIn('loan_id', $loanIds)
            ->whereDate('payment_date', '<=', Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString())
            ->groupBy('loan_id')
            ->pluck('total', 'loan_id');

        $generatedMap = PayrollSalary::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('unique_id', $uniqueIds)
            ->pluck('generated_at', 'unique_id');

        foreach ($loans as $loan) {
            $startKey = (((int) $loan->start_year) * 12) + (int) $loan->start_month;
            if ($selectedKey < $startKey) {
                // Clear any pre-start EMI rows for this month.
                PayrollDeduction::where('loan_id', $loan->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->whereNotExists(function ($q) use ($branchId, $sessionId) {
                        $q->select(DB::raw(1))
                            ->from('payroll_salaries')
                            ->whereColumn('payroll_salaries.unique_id', 'payroll_deductions.unique_id')
                            ->whereColumn('payroll_salaries.month', 'payroll_deductions.month')
                            ->whereColumn('payroll_salaries.year', 'payroll_deductions.year')
                            ->where('payroll_salaries.branch_id', $branchId)
                            ->where('payroll_salaries.session_id', $sessionId);
                    })
                    ->update(['amount' => 0, 'is_applied' => 0]);
                continue;
            }

            if ((int) ($loan->is_active ?? 0) === 0) {
                // If loan is closed, remove current + future EMI rows.
                PayrollDeduction::where('loan_id', $loan->id)
                    ->where(function ($q) use ($year, $month) {
                        $q->where('year', '>', $year)
                            ->orWhere(function ($q) use ($year, $month) {
                                $q->where('year', '=', $year)->where('month', '>=', $month);
                            });
                    })
                    ->whereNotExists(function ($q) use ($branchId, $sessionId) {
                        $q->select(DB::raw(1))
                            ->from('payroll_salaries')
                            ->whereColumn('payroll_salaries.unique_id', 'payroll_deductions.unique_id')
                            ->whereColumn('payroll_salaries.month', 'payroll_deductions.month')
                            ->whereColumn('payroll_salaries.year', 'payroll_deductions.year')
                            ->where('payroll_salaries.branch_id', $branchId)
                            ->where('payroll_salaries.session_id', $sessionId);
                    })
                    ->delete();
                continue;
            }

            $isGenerated = !empty($generatedMap[$loan->unique_id]);

            // If salary already generated for this staff/month, do not change EMI row.
            if ($isGenerated && !$allowFinalizedCorrection) {
                continue;
            }

            $emi = (float) ($loan->monthly_deduction ?? 0);
            if ($emi <= 0) {
                continue;
            }

            $dedBefore = (float) ($deductedBefore[$loan->id] ?? 0);
            $paidBefore = (float) ($paymentsBefore[$loan->id] ?? 0);
            $paidToEnd = (float) ($paymentsToEnd[$loan->id] ?? 0);
            $remainingBefore = round(((float) $loan->principal_amount - ($dedBefore + $paidBefore)), 2);
            $remainingNow = round(((float) $loan->principal_amount - ($dedBefore + $paidToEnd)), 2);
            $skippedSum = (float) ($skippedBefore[$loan->id] ?? 0);

            if ($remainingNow <= 0) {
                // Auto-close when fully paid.
                $loan->is_active = 0;
                $loan->save();

                // Clear any existing EMI for this month.
                PayrollDeduction::where('loan_id', $loan->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->update(['amount' => 0, 'is_applied' => 0]);

                // Remove future EMI rows (after closing month).
                PayrollDeduction::where('loan_id', $loan->id)
                    ->where(function ($q) use ($year, $month) {
                        $q->where('year', '>', $year)
                            ->orWhere(function ($q) use ($year, $month) {
                                $q->where('year', '=', $year)->where('month', '>', $month);
                            });
                    })
                    ->delete();
                continue;
            }

            $due = round(min($emi + $skippedSum, $remainingNow), 2);

            if ((int) ($loan->is_active ?? 0) === 0) {
                $loan->is_active = 1;
                $loan->save();
            }

            $existing = PayrollDeduction::where('loan_id', $loan->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existing) {
                $now = Carbon::today();
                $currentKey = ($now->year * 12) + $now->month;
                if ($selectedKey >= $currentKey) {
                    if ($due <= 0) {
                        $existing->update([
                            'amount' => 0,
                            'is_applied' => 0,
                        ]);
                    } else {
                        $update = [
                            'amount' => $due,
                            'type' => $loan->type,
                            'title' => $loan->title ?: (ucfirst($loan->type) . ' EMI'),
                            'remark' => $loan->remark,
                        ];
                        $update['original_amount'] = $due;
                        $existing->update($update);
                    }
                }
                $appliedThisMonth = (int) ($existing->is_applied ?? 1) === 1;
            } else {
                PayrollDeduction::create([
                    'loan_id' => $loan->id,
                    'month' => $month,
                    'year' => $year,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'unique_id' => $loan->unique_id,
                    'amount' => $due,
                    'original_amount' => $due,
                    'type' => $loan->type,
                    'title' => $loan->title ?: (ucfirst($loan->type) . ' EMI'),
                    'remark' => $loan->remark,
                    'is_applied' => 1,
                    'created_by' => Session::get('id'),
                ]);
                $appliedThisMonth = true;
            }

            // Mark closed only when salary is generated for this month and EMI is applied.
            if ($isGenerated && $appliedThisMonth && $due >= $remainingBefore) {
                $loan->is_active = 0;
                $loan->save();
            }
        }
    }

    private function resolvePayrollMember(int $branchId, int $sessionId, string $inputUniqueId): ?array
    {
        $uid = trim((string) $inputUniqueId);
        if ($uid === '') {
            return null;
        }

        if (stripos($uid, 'USR-') === 0) {
            $userId = (int) str_replace('USR-', '', $uid);
            $member = User::select('id', 'first_name', 'last_name', 'role_id', 'salary', 'attendance_unique_id')
                ->where('id', $userId)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->first();

            if (!$member) {
                return null;
            }

            $attendanceUid = trim((string) ($member->attendance_unique_id ?? ''));
            $attendanceKeys = array_values(array_unique(array_filter([
                'USR-' . $member->id,
                $attendanceUid,
            ])));

            return [
                'payroll_uid' => 'USR-' . $member->id,
                'attendance_keys' => $attendanceKeys,
                'member' => $member,
            ];
        }

        // Support direct attendance_unique_id input.
        $member = User::select('id', 'first_name', 'last_name', 'role_id', 'salary', 'attendance_unique_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('attendance_unique_id', $uid)
            ->first();

        if (!$member) {
            return null;
        }

        return [
            'payroll_uid' => 'USR-' . $member->id,
            'attendance_keys' => array_values(array_unique(array_filter([
                'USR-' . $member->id,
                trim((string) ($member->attendance_unique_id ?? '')),
            ]))),
            'member' => $member,
        ];
    }

    private function calculatePaidDays(array $counts, array $weights, $paidLeaveLimit, $lateFrequency, $earlyOutFrequency): float
    {
        $paidDays = 0.0;

        $leaveCount = (int) ($counts['leave'] ?? 0);
        $paidLeaveCount = $paidLeaveLimit === null ? $leaveCount : min($leaveCount, (int) $paidLeaveLimit);

        // Late with frequency rule
        $lateCount = (int) ($counts['late'] ?? 0);
        $lateWeight = (float) ($weights['late'] ?? 1);
        if ($lateFrequency && $lateFrequency > 0) {
            $latePenaltyCount = intdiv($lateCount, (int) $lateFrequency);
            $lateNormal = $lateCount - $latePenaltyCount;
            $paidDays += ($lateNormal * 1) + ($latePenaltyCount * $lateWeight);
        } else {
            $paidDays += ($lateCount * $lateWeight);
        }

        // Early out with frequency rule
        $earlyCount = (int) ($counts['early_out'] ?? 0);
        $earlyWeight = (float) ($weights['early_out'] ?? 1);
        if ($earlyOutFrequency && $earlyOutFrequency > 0) {
            $earlyPenaltyCount = intdiv($earlyCount, (int) $earlyOutFrequency);
            $earlyNormal = $earlyCount - $earlyPenaltyCount;
            $paidDays += ($earlyNormal * 1) + ($earlyPenaltyCount * $earlyWeight);
        } else {
            $paidDays += ($earlyCount * $earlyWeight);
        }

        foreach ($weights as $k => $w) {
            if (in_array($k, ['late', 'early_out'], true)) {
                continue;
            }
            if ($k === 'leave') {
                $paidDays += ($paidLeaveCount * $w);
                continue;
            }
            $paidDays += ((int) ($counts[$k] ?? 0) * $w);
        }

        return $paidDays;
    }

    private function buildSalaryPayload(int $branchId, int $sessionId, string $uniqueId, int $month, int $year): ?array
    {
        $resolved = $this->resolvePayrollMember($branchId, $sessionId, $uniqueId);
        if (!$resolved) {
            return null;
        }
        $member = $resolved['member'];
        $attendanceKeys = $resolved['attendance_keys'];
        $uniqueId = (string) $resolved['payroll_uid'];

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $today = Carbon::today();
        $rangeEnd = ($today->year === $year && $today->month === $month) ? $today : $monthEnd;

        $payrollSetting = PayrollSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$payrollSetting) {
            $payrollSetting = PayrollSetting::create([
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'created_by' => Session::get('id'),
            ]);
        }

        $statusCounts = AttendanceMark::select('status', DB::raw('COUNT(*) as cnt'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('entity_type', 'staff')
            ->whereIn('unique_id', $attendanceKeys)
            ->whereBetween('date', [$monthStart->toDateString(), $rangeEnd->toDateString()])
            ->groupBy('status')
            ->get();

        $countsByStatus = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'late' => 0,
            'early_out' => 0,
            'halfday' => 0,
            'holiday' => 0,
        ];
        foreach ($statusCounts as $row) {
            if (array_key_exists($row->status, $countsByStatus)) {
                $countsByStatus[$row->status] = (int) $row->cnt;
            }
        }

        $weights = [
            'present' => (float) ($payrollSetting->present_weight ?? 1),
            'late' => (float) ($payrollSetting->late_weight ?? 1),
            'early_out' => (float) ($payrollSetting->early_out_weight ?? 1),
            'leave' => (float) ($payrollSetting->leave_weight ?? 1),
            'holiday' => (float) ($payrollSetting->holiday_weight ?? 1),
            'halfday' => (float) ($payrollSetting->halfday_weight ?? 0.5),
            'absent' => (float) ($payrollSetting->absent_weight ?? 0),
        ];

        $paidDays = $this->calculatePaidDays(
            $countsByStatus,
            $weights,
            $payrollSetting->paid_leave_limit,
            $payrollSetting->late_frequency,
            $payrollSetting->early_out_frequency
        );

        $daysInMonth = (int) $monthStart->daysInMonth;
        $monthlySalary = (float) ($member->salary ?? 0);
        $perDay = ($daysInMonth > 0) ? ($monthlySalary / $daysInMonth) : 0;
        $gross = round($perDay * $paidDays, 2);

        $deductionTotal = (float) PayrollDeduction::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('unique_id', $uniqueId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_applied', 1)
            ->sum('amount');

        $net = round($gross - $deductionTotal, 2);

        return [
            'gross_amount' => $gross,
            'deduction_amount' => round($deductionTotal, 2),
            'net_amount' => $net,
            'generated_at' => Carbon::now(),
            'generated_by' => Session::get('id'),
        ];
    }
}
