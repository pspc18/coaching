<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $displayUniqueId ?? $uniqueId }}</title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; font-size: 9.5px; color: #172b3a; }
        .page { min-height: 272mm; border: 1px solid #9fb4c3; position: relative; background: #fff; }
        .header { background: #123f5a; color: #fff; padding: 14px 16px; }
        .org { font-size: 19px; font-weight: bold; margin-bottom: 3px; }
        .doc-title { font-size: 17px; font-weight: bold; text-align: right; }
        .subtle { color: #d8e7f0; font-size: 9px; }
        .section { padding: 8px 12px 0; }
        .section-title { background: #e9f1f6; border-left: 4px solid #1976a3; padding: 5px 7px; font-size: 10.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .35px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd8e1; padding: 4px 5px; vertical-align: middle; }
        th { background: #f2f6f8; font-weight: bold; text-align: left; }
        .plain td { border: 0; padding: 1px 0; }
        .right { text-align: right; }
        .center { text-align: center; }
        .amount { font-weight: bold; text-align: right; white-space: nowrap; }
        .summary td { padding: 7px; }
        .label { color: #607482; font-size: 8.5px; text-transform: uppercase; }
        .big { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .green { color: #137143; }
        .red { color: #b42318; }
        .blue { color: #115d85; }
        .total-row th, .total-row td { background: #edf4f7; font-weight: bold; }
        .status { display: inline-block; padding: 2px 6px; border-radius: 8px; background: #dff3e8; color: #136c41; font-weight: bold; }
        .footer { position: absolute; left: 12px; right: 12px; bottom: 10px; border-top: 1px solid #cbd8e1; padding-top: 6px; color: #6a7c88; }
        .signature { padding-top: 24px; text-align: center; border-bottom: 1px solid #738895; }
    </style>
</head>
<body>
@php
    $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');
    $staffName = trim(($member->first_name ?? '').' '.($member->last_name ?? ''));
    $orgName = $organization->name ?? $branch->branch_name ?? config('app.name', 'Organization');
    $orgAddress = $organization->address ?? $branch->address ?? '';
@endphp
<div class="page">
    <div class="header">
        <table class="plain">
            <tr>
                <td style="width:65%;">
                    <div class="org">{{ $orgName }}</div>
                    <div class="subtle">{{ $orgAddress }}</div>
                    @if(!empty($organization->mobile) || !empty($organization->gmail))
                        <div class="subtle">{{ $organization->mobile ?? '' }}{{ !empty($organization->mobile) && !empty($organization->gmail) ? ' | ' : '' }}{{ $organization->gmail ?? '' }}</div>
                    @endif
                </td>
                <td style="width:35%;" class="right">
                    <div class="doc-title">SALARY SLIP</div>
                    <div class="subtle">{{ $monthLabel }}</div>
                    <div class="subtle">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Employee & Payroll Information</div>
        <table>
            <tr>
                <th style="width:16%;">Employee</th><td style="width:34%;"><b>{{ $staffName }}</b></td>
                <th style="width:16%;">Employee ID</th><td style="width:34%;">{{ $displayUniqueId ?? $uniqueId }}</td>
            </tr>
            <tr>
                <th>Designation / Role</th><td>{{ $roleName }}</td>
                <th>Payroll Period</th><td>{{ $monthLabel }} ({{ \Carbon\Carbon::parse($monthStart)->format('d M') }} – {{ \Carbon\Carbon::parse($rangeEnd)->format('d M Y') }})</td>
            </tr>
            <tr>
                <th>Payroll Status</th><td><span class="status">{{ $salarySnapshot ? 'FINALIZED' : 'PROVISIONAL' }}</span></td>
                <th>Finalized On</th><td>{{ $salarySnapshot && $salarySnapshot->generated_at ? \Carbon\Carbon::parse($salarySnapshot->generated_at)->format('d M Y, h:i A') : '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Salary Summary</div>
        <table class="summary">
            <tr>
                <td><div class="label">Monthly Salary</div><div class="big">{{ number_format((float)$monthlySalary, 2) }}</div></td>
                <td><div class="label">Gross Earned</div><div class="big blue">{{ number_format((float)$gross, 2) }}</div></td>
                <td><div class="label">Total Deductions</div><div class="big red">{{ number_format((float)$deductionTotal, 2) }}</div></td>
                <td><div class="label">Total Payable</div><div class="big green">{{ number_format((float)$net, 2) }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Paid Amount</div><div class="big green">{{ number_format((float)$totalPaid, 2) }}</div></td>
                <td><div class="label">Pending Salary</div><div class="big {{ $salaryPending > 0 ? 'red' : 'green' }}">{{ number_format((float)$salaryPending, 2) }}</div></td>
                <td><div class="label">Loan Original</div><div class="big">{{ number_format((float)$loanPrincipalTotal, 2) }}</div></td>
                <td><div class="label">Loan Pending</div><div class="big red">{{ number_format((float)$loanPendingTotal, 2) }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Attendance & Earnings Calculation</div>
        <table>
            <tr>
                <th>Working Days</th><th>Paid Days</th><th>Present</th><th>Absent</th><th>Leave</th><th>Late</th><th>Early Out</th><th>Half Day</th><th>Holiday</th>
            </tr>
            <tr class="center">
                <td>{{ $daysInMonth }}</td>
                <td>{{ rtrim(rtrim(number_format((float)$paidDays, 2), '0'), '.') }}</td>
                <td>{{ $countsByStatus['present'] }}</td><td>{{ $countsByStatus['absent'] }}</td>
                <td>{{ $countsByStatus['leave'] }}</td><td>{{ $countsByStatus['late'] }}</td>
                <td>{{ $countsByStatus['early_out'] }}</td><td>{{ $countsByStatus['halfday'] }}</td>
                <td>{{ $countsByStatus['holiday'] }}</td>
            </tr>
            <tr>
                <th colspan="2">Per Day Salary</th><td colspan="2" class="amount">{{ number_format((float)$perDay, 2) }}</td>
                <th colspan="3">Gross Earned</th><td colspan="2" class="amount">{{ number_format((float)$gross, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Deduction Breakdown</div>
        <table>
            <tr><th>#</th><th>Category</th><th>Title</th><th>Remark</th><th class="right">Amount</th></tr>
            @php $deductionSr = 1; @endphp
            @foreach($manualDeductions as $item)
                <tr><td>{{ $deductionSr++ }}</td><td>Manual</td><td>{{ $item->title ?? '-' }}</td><td>{{ $item->remark ?? '-' }}</td><td class="amount">{{ number_format((float)$item->amount, 2) }}</td></tr>
            @endforeach
            @foreach($loanDeductions as $item)
                <tr><td>{{ $deductionSr++ }}</td><td>{{ ucfirst($item->type ?? 'Loan') }}</td><td>{{ $item->title ?? 'Loan EMI' }}</td><td>{{ $item->remark ?? '-' }}</td><td class="amount">{{ number_format((float)$item->amount, 2) }}</td></tr>
            @endforeach
            @if($deductionSr === 1)
                <tr><td colspan="5" class="center">No deductions for this period.</td></tr>
            @endif
            <tr class="total-row"><th colspan="4" class="right">Total Deductions</th><th class="amount">{{ number_format((float)$deductionTotal, 2) }}</th></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Salary Payment Breakdown</div>
        <table>
            <tr><th>#</th><th>Payment Date</th><th>Recorded By</th><th class="right">Amount</th></tr>
            @forelse($payments as $index => $payment)
                @php $paidBy = $paymentUsers[$payment->paid_by] ?? null; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, h:i A') : '-' }}</td>
                    <td>{{ $paidBy ? trim(($paidBy->first_name ?? '').' '.($paidBy->last_name ?? '')) : 'System User' }}</td>
                    <td class="amount">{{ number_format((float)$payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="center">No salary payments recorded.</td></tr>
            @endforelse
            <tr class="total-row"><th colspan="3" class="right">Paid / Pending</th><th class="amount">{{ number_format((float)$totalPaid, 2) }} / {{ number_format((float)$salaryPending, 2) }}</th></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Loan / Advance Position as of {{ \Carbon\Carbon::parse($monthEnd)->format('d M Y') }}</div>
        <table>
            <tr><th>Type</th><th>Title</th><th class="right">Original</th><th class="right">Monthly EMI</th><th class="right">Recovered</th><th class="right">Pending</th></tr>
            @forelse($loanSummaries as $loan)
                <tr>
                    <td>{{ $loan['type'] }}</td><td>{{ $loan['title'] }}</td>
                    <td class="amount">{{ number_format($loan['principal'], 2) }}</td>
                    <td class="amount">{{ number_format($loan['monthly'], 2) }}</td>
                    <td class="amount">{{ number_format($loan['recovered'], 2) }}</td>
                    <td class="amount">{{ number_format($loan['pending'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="center">No loan or advance applicable.</td></tr>
            @endforelse
            <tr class="total-row"><th colspan="2" class="right">Loan Totals</th><th class="amount">{{ number_format((float)$loanPrincipalTotal, 2) }}</th><th></th><th class="amount">{{ number_format((float)$loanRecoveredTotal, 2) }}</th><th class="amount">{{ number_format((float)$loanPendingTotal, 2) }}</th></tr>
        </table>
    </div>

    <div class="section">
        <table class="plain">
            <tr>
                <td style="width:30%;"><div class="signature"></div><div class="center">Employee Signature</div></td>
                <td style="width:40%;"></td>
                <td style="width:30%;"><div class="signature"></div><div class="center">Authorized Signatory</div></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table class="plain"><tr><td>This is a system-generated payroll document. Figures are based on the finalized payroll snapshot and recorded transactions.</td><td class="right">Page 1 of 1</td></tr></table>
    </div>
</div>
</body>
</html>
