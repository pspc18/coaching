@extends('layout.app')
@section('content')

@include('payroll.theme')
<style>
    .payroll-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }
    .payroll-dot-present { background: #22c55e; }
    .payroll-dot-absent { background: #ef4444; }
    .payroll-dot-leave { background: #f97316; }
    .payroll-dot-late { background: #f59e0b; }
    .payroll-dot-early_out { background: #3b82f6; }
    .payroll-dot-halfday { background: #8b5cf6; }
    .payroll-dot-holiday { background: #94a3b8; }
</style>

@php
    $monthLabel = date('F', mktime(0,0,0,$month,1));
@endphp

<div class="content-wrapper payroll-page">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="payroll-hero">
                <div class="payroll-hero-inner">
                    <div>
                        <div class="payroll-hero-kicker">Payroll Center</div>
                        <div class="payroll-hero-title">Staff Payroll Edit</div>
                        <div class="payroll-hero-subtitle">
                            {{ trim(($member->first_name ?? '').' '.($member->last_name ?? '')) }} ({{ $displayUniqueId ?? $uniqueId }}) - {{ $monthLabel }} {{ $year }}
                        </div>
                        <div class="payroll-hero-chips">
                            <span class="payroll-chip"><i class="fa fa-id-badge"></i> Role: {{ $roleName }}</span>
                            <span class="payroll-chip"><i class="fa fa-calendar"></i> Till: {{ \Carbon\Carbon::parse($rangeEnd)->format('d/m/Y') }}</span>
                            <span class="payroll-chip"><i class="fa fa-sliders"></i> Leave Allowed: {{ $payrollSetting->paid_leave_limit === null ? 'Unlimited' : $payrollSetting->paid_leave_limit.' days' }}</span>
                        </div>
                    </div>
                    <div class="payroll-hero-actions">
                        <a href="{{ url('payroll/staff/slip?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}" class="btn btn-sm btn-payroll btn-payroll-light" target="_blank">
                            <i class="fa fa-print"></i> Salary Slip
                        </a>
                        <a href="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" class="btn btn-sm btn-payroll btn-payroll-light">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="payroll-stats">
                <div class="payroll-stat">
                    <div class="payroll-stat-label">Monthly Salary</div>
                    <div class="payroll-stat-value">{{ number_format((float)$monthlySalary, 2) }}</div>
                    <div class="payroll-stat-sub">Fixed monthly payout</div>
                </div>
                <div class="payroll-stat">
                    <div class="payroll-stat-label">Working Days</div>
                    <div class="payroll-stat-value">{{ $daysInMonth }}</div>
                    <div class="payroll-stat-sub">Calendar days</div>
                </div>
                <div class="payroll-stat">
                    <div class="payroll-stat-label">Per Day Salary</div>
                    <div class="payroll-stat-value">{{ number_format((float)$perDay, 2) }}</div>
                    <div class="payroll-stat-sub">Daily rate</div>
                </div>
                <div class="payroll-stat">
                    <div class="payroll-stat-label">Paid Days</div>
                    <div class="payroll-stat-value">{{ rtrim(rtrim(number_format((float)$paidDays, 2), '0'), '.') }}</div>
                    <div class="payroll-stat-sub">Credits after rules</div>
                </div>
            </div>

            <div class="payroll-stats">
                <div class="payroll-stat">
                    <div class="payroll-stat-label">Gross Salary</div>
                    <div class="payroll-stat-value">{{ number_format((float)$grossTillNow, 2) }}</div>
                    <div class="payroll-stat-sub">Till date</div>
                </div>
                <div class="payroll-stat">
                    <div class="payroll-stat-label">Total Deductions</div>
                    <div class="payroll-stat-value">{{ number_format((float)$deductionTotal, 2) }}</div>
                    <div class="payroll-stat-sub">Manual + loans</div>
                </div>
                <div class="payroll-stat payroll-stat-success">
                    <div class="payroll-stat-label">Net Payable</div>
                    <div class="payroll-stat-value">{{ number_format((float)$netTillNow, 2) }}</div>
                    <div class="payroll-stat-sub">Final amount</div>
                </div>
            </div>

            <div class="card card-outline card-orange payroll-card">
                <div class="card-header payroll-card-header">
                    <div>
                        <div class="payroll-card-title"><i class="fa fa-user"></i> Payroll Breakdown</div>
                        <div class="payroll-inline-note">Review attendance, deductions, and salary adjustments.</div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center" style="gap:8px;">
                        <span class="badge-soft">Period: {{ $monthLabel }} {{ $year }}</span>
                        <span class="badge-soft">Till: {{ \Carbon\Carbon::parse($rangeEnd)->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Validation Error:</strong>
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card mt-3 payroll-subcard">
                        <div class="card-header">
                            <b>Attendance Summary</b>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap" style="gap:10px;">
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-present"></span> Present: {{ $countsByStatus['present'] }}</span>
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-late"></span> Late: {{ $countsByStatus['late'] }}</span>
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-early_out"></span> Early Out: {{ $countsByStatus['early_out'] }}</span>
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-halfday"></span> Half Day: {{ $countsByStatus['halfday'] }}</span>
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-absent"></span> Absent: {{ $countsByStatus['absent'] }}</span>
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-holiday"></span> Holiday: {{ $countsByStatus['holiday'] }}</span>
                                <span class="payroll-pill"><span class="payroll-dot payroll-dot-leave"></span> Leave: {{ $countsByStatus['leave'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3 payroll-subcard">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <b>Manual Deductions</b>
                                <div class="text-muted small">Applied Total: {{ number_format((float)$manualAppliedTotal, 2) }}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}" class="mb-3">
                                @csrf
                                <input type="hidden" name="action" value="add_deduction">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="number" step="0.01" min="0" name="amount" class="form-control form-control-sm" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Penalty">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Remark</label>
                                            <input type="text" name="remark" class="form-control form-control-sm" placeholder="Optional note">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button class="btn btn-sm btn-payroll btn-payroll-primary w-100"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered payroll-table">
                                    <thead>
                                        <tr>
                                            <th style="width:70px;">#</th>
                                            <th>Title</th>
                                            <th>Remark</th>
                                            <th style="width:160px;" class="text-right">Amount</th>
                                            <th style="width:120px;">Status</th>
                                            <th style="width:90px;" class="text-center">Apply</th>
                                            <th style="width:90px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sr = 1; @endphp
                                        @forelse($manualDeductions as $d)
                                            <tr>
                                                <td>{{ $sr++ }}</td>
                                                <td>{{ $d->title ?? '-' }}</td>
                                                <td>{{ $d->remark ?? '-' }}</td>
                                                <td class="text-right">{{ number_format((float)$d->amount, 2) }}</td>
                                                <td>
                                                    @if((int)($d->is_applied ?? 1) === 1)
                                                        <span class="badge badge-payroll badge-payroll-success">Applied</span>
                                                    @else
                                                        <span class="badge badge-payroll badge-payroll-warning">Skipped</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form method="post" action="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}">
                                                        @csrf
                                                        <input type="hidden" name="action" value="toggle_deduction">
                                                        <input type="hidden" name="deduction_id" value="{{ $d->id }}">
                                                        <input type="hidden" name="is_applied" value="0">
                                                        <input type="checkbox" name="is_applied" value="1" {{ (int)($d->is_applied ?? 1) === 1 ? 'checked' : '' }}
                                                            onchange="this.form.submit()">
                                                    </form>
                                                </td>
                                                <td>
                                                    <form method="post" action="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}" onsubmit="return confirm('Delete this deduction?');">
                                                        @csrf
                                                        <input type="hidden" name="action" value="delete_deduction">
                                                        <input type="hidden" name="deduction_id" value="{{ $d->id }}">
                                                        <button class="btn btn-sm btn-payroll btn-payroll-danger"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No deductions added for this month.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3 payroll-subcard">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <b>Loan / Advance Deductions (This Month)</b>
                                <div class="text-muted small">Applied Total: {{ number_format((float)$loanAppliedTotal, 2) }}</div>
                            </div>
                            <small class="text-muted">Uncheck to skip deduction for this month.</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered payroll-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:70px;">#</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Remark</th>
                                            <th style="width:200px;" class="text-right">Amount</th>
                                            <th style="width:120px;">Status</th>
                                            <th style="width:90px;" class="text-center">Apply</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sr = 1; @endphp
                                        @forelse($loanDeductions as $d)
                                            <tr>
                                                <td>{{ $sr++ }}</td>
                                                <td>{{ ucfirst($d->type ?? 'loan') }}</td>
                                                <td>{{ $d->title ?? 'EMI' }}</td>
                                                <td>{{ $d->remark ?? '-' }}</td>
                                                <td class="text-right">
                                                    <form method="post" action="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}" class="d-inline-flex align-items-center" style="gap:6px;">
                                                        @csrf
                                                        <input type="hidden" name="action" value="update_loan_emi">
                                                        <input type="hidden" name="deduction_id" value="{{ $d->id }}">
                                                        <input type="number" step="0.01" min="0" name="amount" value="{{ number_format((float)$d->amount, 2, '.', '') }}" class="form-control form-control-sm text-right" style="width:110px;">
                                                        <button class="btn btn-sm btn-payroll btn-payroll-outline">Update</button>
                                                    </form>
                                                    @php
                                                        $remaining = $loanRemainingById[$d->loan_id] ?? 0;
                                                        $maxBySalary = max(0, (float)$grossTillNow - (float)$manualAppliedTotal - ((float)$loanAppliedTotal - (float)$d->amount));
                                                    @endphp
                                                    <div class="text-muted small text-right">
                                                        Remaining: {{ number_format($remaining, 2) }} | Max: {{ number_format($maxBySalary, 2) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if((int)($d->is_applied ?? 1) === 1)
                                                        <span class="badge badge-payroll badge-payroll-success">Applied</span>
                                                    @else
                                                        <span class="badge badge-payroll badge-payroll-warning">Skipped</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form method="post" action="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}">
                                                        @csrf
                                                        <input type="hidden" name="action" value="toggle_deduction">
                                                        <input type="hidden" name="deduction_id" value="{{ $d->id }}">
                                                        <input type="hidden" name="is_applied" value="0">
                                                        <input type="checkbox" name="is_applied" value="1" {{ (int)($d->is_applied ?? 1) === 1 ? 'checked' : '' }}
                                                            onchange="this.form.submit()">
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No loan/advance deductions for this month.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3 payroll-subcard">
                        <div class="card-header">
                            <b>Salary (Quick Update)</b>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}">
                                @csrf
                                <input type="hidden" name="action" value="update_salary">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Monthly Salary</label>
                                            <input type="number" step="0.01" min="0" name="salary" class="form-control form-control-sm" value="{{ $monthlySalary }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button class="btn btn-sm btn-payroll btn-payroll-primary w-100"><i class="fa fa-save"></i> Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

@endsection
