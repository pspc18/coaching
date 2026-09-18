@forelse($rows as $row)
    @php
        $netVal = (float) ($row['net_salary'] ?? 0);
        $paidVal = (float) ($row['paid_amount'] ?? 0);
        $balanceVal = (float) ($row['balance_amount'] ?? 0);
        $isFinalized = !empty($row['generated_at']);
    @endphp
    <tr data-unique-id="{{ $row['unique_id'] }}">
        <td class="text-center" style="width: 80px;">
            <span class="badge badge-light border font-weight-bold" style="font-size:10.5px; letter-spacing:0.3px; color:#002C54;">{{ $row['unique_id'] }}</span>
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px; line-height:1.25;">{{ $row['name'] }}</div>
            <div class="small text-muted d-flex align-items-center mt-1" style="gap:3px;">
                <span class="badge badge-secondary" style="font-size:9.5px; padding:1px 5px; font-weight:600;">{{ $row['role'] }}</span>
            </div>
        </td>
        <td class="text-right font-weight-bold" style="font-size:11.5px; width: 95px;">
            ₹{{ number_format($row['monthly_salary'], 2) }}
        </td>
        <td class="text-center" style="font-size:11px; width: 85px;">
            <span class="badge badge-light border font-weight-semibold">{{ $row['working_days'] }}d</span>
            <div class="small text-muted" style="font-size:9.5px;">₹{{ number_format($row['per_day_salary'], 2) }}/d</div>
        </td>
        <td class="text-center" style="font-size:11.5px; width: 80px;">
            <b class="text-primary font-weight-bold">{{ rtrim(rtrim(number_format($row['paid_days'], 2), '0'), '.') }}</b>
            @if($isFinalized)
                <div class="text-muted" style="font-size:9.5px;" title="Calculation cutoff">Till {{ $row['calculation_through'] }}</div>
            @endif
        </td>
        <td class="text-right font-weight-bold" style="font-size:12px; color:#002C54; width: 100px;">
            ₹{{ number_format($row['salary_till_now'], 2) }}
        </td>
        <td class="text-right font-weight-semibold" style="font-size:11.5px; width: 90px; color: {{ ($row['manual_deductions'] ?? 0) > 0 ? '#dc2626' : '#64748b' }};">
            {{ ($row['manual_deductions'] ?? 0) > 0 ? '-₹' . number_format($row['manual_deductions'], 2) : '₹0.00' }}
        </td>
        <td class="text-right font-weight-semibold" style="font-size:11.5px; width: 90px; color: {{ ($row['loan_deductions'] ?? 0) > 0 ? '#d97706' : '#64748b' }};">
            {{ ($row['loan_deductions'] ?? 0) > 0 ? '-₹' . number_format($row['loan_deductions'], 2) : '₹0.00' }}
        </td>
        <td class="text-right font-weight-bold" style="font-size:12.5px; width: 105px; color: {{ $netVal < 0 ? '#dc2626' : '#002C54' }};">
            ₹{{ number_format($netVal, 2) }}
        </td>
        <td class="text-right font-weight-bold" style="font-size:11.5px; color:#16a34a; width: 95px;">
            ₹{{ number_format($paidVal, 2) }}
            @if(($row['payment_count'] ?? 0) > 0)
                <div class="small text-muted font-weight-normal" style="font-size:9.5px;">{{ $row['payment_count'] }} payment(s)</div>
            @endif
        </td>
        <td class="text-right font-weight-bold" style="font-size:12px; width: 95px; color: {{ $balanceVal > 0 ? '#dc2626' : '#16a34a' }};">
            ₹{{ number_format($balanceVal, 2) }}
            @if($isFinalized && $balanceVal <= 0)
                <div><span class="badge badge-success" style="font-size:9px; padding:1px 4px;">Paid</span></div>
            @endif
        </td>
        <td class="text-center" style="width: 85px;">
            @if($isFinalized)
                <span class="badge badge-success" style="font-size:10px; padding:2px 6px;">
                    <i class="fa fa-lock"></i> Finalized
                </span>
            @else
                <span class="badge badge-warning" style="font-size:10px; padding:2px 6px; color:#0f172a; background:#fef08a;">
                    <i class="fa fa-clock-o"></i> Pending
                </span>
            @endif
        </td>
        <td class="text-center fixed_action_col" style="width: 125px;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                @if($isFinalized)
                    @if($balanceVal > 0)
                        <button type="button"
                                class="btn-action-icon btn-action-pay js-pay-salary"
                                data-toggle="modal"
                                data-target="#salaryPaymentModal"
                                data-unique-id="{{ $row['unique_id'] }}"
                                data-name="{{ $row['name'] }}"
                                data-payable="{{ number_format((float)($row['payable_amount'] ?? 0), 2, '.', '') }}"
                                data-paid="{{ number_format((float)($row['paid_amount'] ?? 0), 2, '.', '') }}"
                                data-balance="{{ number_format((float)($row['balance_amount'] ?? 0), 2, '.', '') }}"
                                title="Record Salary Payment" aria-label="Pay Salary">
                            <i class="fa fa-money"></i>
                        </button>
                    @endif
                    @if(($row['payment_count'] ?? 0) > 0)
                        <button type="button"
                                class="btn-action-icon btn-action-history js-payment-history"
                                data-toggle="modal"
                                data-target="#paymentHistoryModal"
                                data-name="{{ $row['name'] }}"
                                data-unique-id="{{ $row['unique_id'] }}"
                                data-payable="{{ number_format((float)($row['payable_amount'] ?? 0), 2, '.', '') }}"
                                data-paid="{{ number_format((float)($row['paid_amount'] ?? 0), 2, '.', '') }}"
                                data-balance="{{ number_format((float)($row['balance_amount'] ?? 0), 2, '.', '') }}"
                                data-payments='@json($row["payment_history"] ?? [])'
                                data-excel-url="{{ url('payroll/staff/payments-excel?staff='.$row['unique_id'].'&month='.$month.'&year='.$year) }}"
                                title="Payment History Breakdown" aria-label="Payment Breakdown">
                            <i class="fa fa-list-alt"></i>
                        </button>
                    @endif
                    <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" style="display:inline-block;" onsubmit="return confirm('Regenerate salary snapshot using latest attendance and deductions?');">
                        @csrf
                        <input type="hidden" name="action" value="regenerate_salary">
                        <input type="hidden" name="unique_id" value="{{ $row['unique_id'] }}">
                        <button type="submit" class="btn-action-icon btn-action-regen" title="Regenerate Finalized Salary" aria-label="Regenerate Salary">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </form>
                    <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" style="display:inline-block;" onsubmit="return confirm('Reset finalized salary back to open state?');">
                        @csrf
                        <input type="hidden" name="action" value="reset_salary">
                        <input type="hidden" name="unique_id" value="{{ $row['unique_id'] }}">
                        <button type="submit" class="btn-action-icon btn-action-delete" title="Reset Salary Snapshot" aria-label="Reset Salary">
                            <i class="fa fa-undo"></i>
                        </button>
                    </form>
                    <a class="btn-action-icon btn-action-view" target="_blank"
                       href="{{ url('payroll/staff/slip-pdf?staff='.$row['unique_id'].'&month='.$month.'&year='.$year) }}"
                       title="Download Salary Slip PDF" aria-label="Download Salary Slip">
                        <i class="fa fa-file-pdf-o" style="color:#ef4444;"></i>
                    </a>
                @else
                    <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" style="display:inline-block;">
                        @csrf
                        <input type="hidden" name="action" value="generate_salary">
                        <input type="hidden" name="unique_id" value="{{ $row['unique_id'] }}">
                        <button type="submit" class="btn-action-icon btn-action-success" title="Finalize & Generate Salary" aria-label="Generate Salary">
                            <i class="fa fa-check"></i>
                        </button>
                    </form>
                    <a class="btn-action-icon btn-action-edit"
                       href="{{ url('payroll/staff/edit?staff='.$row['unique_id'].'&month='.$month.'&year='.$year) }}"
                       title="Edit Payroll / Attendance Details" aria-label="Edit Payroll">
                        <i class="fa fa-edit"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr class="empty-row">
        <td colspan="13" class="text-center py-5" style="text-align: center !important; vertical-align: middle !important; background: #ffffff !important;">
            <div class="d-flex flex-column align-items-center justify-content-center text-center w-100 py-3" style="margin: 0 auto;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="fa fa-folder-open-o text-muted" style="font-size: 18px;"></i>
                </div>
                <div class="font-weight-bold text-dark" style="font-size: 12.5px;">No Staff Payroll Records Found</div>
                <div class="text-muted mt-1" style="font-size: 11px;">No records available matching your current search or filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse