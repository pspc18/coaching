@extends('layout.app')
@section('content')

@include('payroll.theme')
<style>
    @media print {
        .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .main-header, .main-sidebar, .main-footer { display: none !important; }
    }

    .payroll-slip-card {
        border: 1px solid var(--payroll-border);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: var(--payroll-shadow-soft);
        overflow: hidden;
    }

    .payroll-slip-header {
        padding: 16px 18px;
        background: linear-gradient(120deg, #0f3d56 0%, #145c7a 60%, #1f8a70 100%);
        color: #ffffff;
    }

    .payroll-slip-title {
        font-size: 20px;
        font-weight: 700;
    }

    .payroll-slip-meta {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.75);
    }

    .payroll-slip-actions {
        padding: 10px 18px;
        border-bottom: 1px solid var(--payroll-border);
        background: #ffffff;
    }

    .payroll-slip-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }

    .payroll-slip-table th,
    .payroll-slip-table td {
        border: 1px solid var(--payroll-border);
        padding: 6px;
        font-size: 12px;
    }

    .payroll-slip-table.no-border td {
        border: none;
        padding: 0;
    }

    .payroll-slip-kpi {
        font-size: 11px;
        color: var(--payroll-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .payroll-slip-value {
        font-size: 15px;
        font-weight: 700;
    }

    .payroll-slip-highlight {
        background: #ecfdf5;
        color: #166534;
    }
</style>

@php
    $monthLabel = date('F', mktime(0,0,0,$month,1));
@endphp

<div class="content-wrapper payroll-page">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="payroll-slip-card">
                <div class="payroll-slip-header">
                    <table class="payroll-slip-table no-border">
                        <tr>
                            <td><div class="payroll-slip-title">Salary Slip</div></td>
                            <td class="text-right payroll-slip-meta">
                                Period: {{ \Carbon\Carbon::parse($monthStart)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($rangeEnd)->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="payroll-slip-meta">
                                {{ trim(($member->first_name ?? '').' '.($member->last_name ?? '')) }} ({{ $displayUniqueId ?? $uniqueId }})
                                &nbsp;|&nbsp; {{ $monthLabel }} {{ $year }}
                            </td>
                            <td class="text-right payroll-slip-meta">Role: {{ $roleName }}</td>
                        </tr>
                    </table>
                </div>

                <div class="no-print payroll-slip-actions">
                    <a href="{{ url('payroll/staff/edit?staff='.$uniqueId.'&month='.$month.'&year='.$year) }}" class="btn btn-sm btn-payroll btn-payroll-outline">Edit Payroll</a>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-payroll btn-payroll-outline">Print</button>
                </div>

                <div style="padding:16px 18px;">
                    <table class="payroll-slip-table">
                        <tr>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Monthly Salary</div>
                                <div class="payroll-slip-value">{{ number_format((float)$monthlySalary, 2) }}</div>
                            </td>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Working Days</div>
                                <div class="payroll-slip-value">{{ $daysInMonth }}</div>
                            </td>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Per Day Salary</div>
                                <div class="payroll-slip-value">{{ number_format((float)$perDay, 2) }}</div>
                            </td>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Paid Days</div>
                                <div class="payroll-slip-value">{{ rtrim(rtrim(number_format((float)$paidDays, 2), '0'), '.') }}</div>
                            </td>
                        </tr>
                    </table>

                    <table class="payroll-slip-table">
                        <tr>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Gross (Attendance)</div>
                                <div class="payroll-slip-value">{{ number_format((float)$gross, 2) }}</div>
                            </td>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Manual Deductions</div>
                                <div class="payroll-slip-value">{{ number_format((float)$manualTotal, 2) }}</div>
                            </td>
                            <td style="width:25%;padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Loan/Advance</div>
                                <div class="payroll-slip-value">{{ number_format((float)$loanTotal, 2) }}</div>
                            </td>
                            <td style="width:25%;" class="payroll-slip-highlight">
                                <div class="payroll-slip-kpi" style="color:#166534;">Net Payable</div>
                                <div class="payroll-slip-value" style="color:#166534;">{{ number_format((float)$net, 2) }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Total Deductions</div>
                                <div class="payroll-slip-value">{{ number_format((float)$deductionTotal, 2) }}</div>
                            </td>
                            <td style="padding:6px;border:1px solid #e5e7eb;">
                                <div class="payroll-slip-kpi">Leave Allowed</div>
                                <div style="font-size:14px;font-weight:700;">
                                    {{ $payrollSetting->paid_leave_limit === null ? 'Unlimited' : $payrollSetting->paid_leave_limit.' days' }}
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-bottom:14px;">
                        <div style="font-weight:700;font-size:13px;margin-bottom:6px;">Attendance Summary</div>
                        <table class="payroll-slip-table no-border">
                            <tr>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Present: <b>{{ $countsByStatus['present'] }}</b></td>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Late: <b>{{ $countsByStatus['late'] }}</b></td>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Early Out: <b>{{ $countsByStatus['early_out'] }}</b></td>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Half Day: <b>{{ $countsByStatus['halfday'] }}</b></td>
                            </tr>
                            <tr>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Absent: <b>{{ $countsByStatus['absent'] }}</b></td>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Holiday: <b>{{ $countsByStatus['holiday'] }}</b></td>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">Leave: <b>{{ $countsByStatus['leave'] }}</b></td>
                                <td style="padding:6px;border:1px solid #e5e7eb;font-size:12px;">&nbsp;</td>
                            </tr>
                        </table>
                    </div>

                    <table class="payroll-slip-table no-border">
                        <tr>
                            <td style="width:50%;padding-right:6px;vertical-align:top;">
                                <div style="font-weight:700;font-size:13px;margin-bottom:6px;">Manual Deductions</div>
                                <table class="payroll-slip-table no-border">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left;border:1px solid #e5e7eb;padding:6px;font-size:12px;background:#f8fafc;">Title</th>
                                            <th style="text-align:left;border:1px solid #e5e7eb;padding:6px;font-size:12px;background:#f8fafc;">Remark</th>
                                            <th style="text-align:right;border:1px solid #e5e7eb;padding:6px;font-size:12px;background:#f8fafc;width:90px;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($manualDeductions as $d)
                                            <tr>
                                                <td style="border:1px solid #e5e7eb;padding:6px;font-size:12px;">{{ $d->title ?? '-' }}</td>
                                                <td style="border:1px solid #e5e7eb;padding:6px;font-size:12px;">{{ $d->remark ?? '-' }}</td>
                                                <td style="border:1px solid #e5e7eb;padding:6px;font-size:12px;text-align:right;">{{ number_format((float)$d->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" style="border:1px solid #e5e7eb;padding:6px;font-size:12px;text-align:center;color:#6c757d;">No manual deductions.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                            <td style="width:50%;padding-left:6px;vertical-align:top;">
                                <div style="font-weight:700;font-size:13px;margin-bottom:6px;">Loan / Advance Deductions</div>
                                <table class="payroll-slip-table no-border">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left;border:1px solid #e5e7eb;padding:6px;font-size:12px;background:#f8fafc;">Type</th>
                                            <th style="text-align:left;border:1px solid #e5e7eb;padding:6px;font-size:12px;background:#f8fafc;">Title</th>
                                            <th style="text-align:right;border:1px solid #e5e7eb;padding:6px;font-size:12px;background:#f8fafc;width:90px;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($loanDeductions as $d)
                                            <tr>
                                                <td style="border:1px solid #e5e7eb;padding:6px;font-size:12px;">{{ ucfirst($d->type ?? 'loan') }}</td>
                                                <td style="border:1px solid #e5e7eb;padding:6px;font-size:12px;">{{ $d->title ?? 'EMI' }}</td>
                                                <td style="border:1px solid #e5e7eb;padding:6px;font-size:12px;text-align:right;">{{ number_format((float)$d->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" style="border:1px solid #e5e7eb;padding:6px;font-size:12px;text-align:center;color:#6c757d;">No loan/advance deductions.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
