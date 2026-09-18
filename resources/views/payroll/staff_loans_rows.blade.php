@forelse($rows as $row)
    @php
        $rem = (float)($row['remaining'] ?? 0);
        $isActive = !empty($row['is_active']);
    @endphp
    <tr data-loan-id="{{ $row['id'] }}">
        <td class="text-center" style="width: 70px;">
            <span class="badge badge-light border font-weight-bold" style="font-size:10.5px; letter-spacing:0.3px; color:#002C54;">#{{ $row['id'] }}</span>
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px; line-height:1.25;">{{ $row['name'] }}</div>
            <div class="small text-muted mt-1" style="font-size:10px;">{{ $row['display_unique_id'] }}</div>
        </td>
        <td class="text-center" style="width: 85px;">
            @if($row['type'] === 'loan')
                <span class="badge badge-primary font-weight-semibold" style="font-size:10px; padding:2px 6px; background:#002C54;">Loan</span>
            @else
                <span class="badge badge-info font-weight-semibold" style="font-size:10px; padding:2px 6px; background:#0284c7;">Advance</span>
            @endif
        </td>
        <td>
            <div class="font-weight-semibold text-dark" style="font-size:11.5px;">{{ $row['title'] ?? '-' }}</div>
            @if(!empty($row['remark']))
                <div class="small text-muted" style="font-size:9.5px;">{{ Str::limit($row['remark'], 35) }}</div>
            @endif
        </td>
        <td class="text-right font-weight-bold" style="font-size:11.5px; width: 100px;">
            ₹{{ number_format($row['principal_amount'], 2) }}
        </td>
        <td class="text-right font-weight-bold" style="font-size:11.5px; color:#002C54; width: 105px;">
            ₹{{ number_format($row['monthly_deduction'], 2) }}/m
        </td>
        <td class="text-center" style="font-size:11px; width: 85px;">
            <span class="badge badge-light border font-weight-semibold">{{ $row['start'] }}</span>
        </td>
        <td class="text-right font-weight-semibold" style="font-size:11.5px; color:#16a34a; width: 95px;">
            ₹{{ number_format($row['deducted'], 2) }}
        </td>
        <td class="text-right font-weight-semibold" style="font-size:11.5px; color:#0284c7; width: 95px;">
            ₹{{ number_format($row['paid'], 2) }}
        </td>
        <td class="text-right font-weight-bold" style="font-size:12px; width: 105px; color: {{ $rem > 0 ? '#dc2626' : '#16a34a' }};">
            ₹{{ number_format($rem, 2) }}
        </td>
        <td class="text-center" style="width: 80px;">
            @if($isActive)
                <span class="badge badge-success font-weight-semibold" style="font-size:10px; padding:2px 6px;">Active</span>
            @else
                <span class="badge badge-secondary font-weight-semibold" style="font-size:10px; padding:2px 6px;">Closed</span>
            @endif
        </td>
        <td class="text-center fixed_action_col" style="width: 105px;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                @if($isActive && $rem > 0)
                    <button type="button" class="btn-action-icon btn-action-pay js-loan-payment"
                            data-toggle="modal" data-target="#loanPaymentModal"
                            data-loan-id="{{ $row['id'] }}"
                            data-name="{{ $row['name'] }}"
                            data-remaining="{{ number_format($rem, 2, '.', '') }}"
                            title="Record Direct Repayment">
                        <i class="fa fa-money"></i>
                    </button>
                @endif
                <button type="button" class="btn-action-icon btn-action-edit js-edit-loan"
                        data-toggle="modal" data-target="#loanEditModal"
                        data-loan-id="{{ $row['id'] }}"
                        data-unique-id="{{ $row['unique_id'] }}"
                        data-type="{{ $row['type'] }}"
                        data-title="{{ $row['title'] ?? '' }}"
                        data-principal="{{ number_format($row['principal_amount'], 2, '.', '') }}"
                        data-monthly="{{ number_format($row['monthly_deduction'], 2, '.', '') }}"
                        data-start-month="{{ $row['start_month'] }}"
                        data-start-year="{{ $row['start_year'] }}"
                        data-is-active="{{ $row['is_active'] ? 1 : 0 }}"
                        data-remark="{{ $row['remark'] ?? '' }}"
                        data-locked="{{ $row['is_locked'] ? 1 : 0 }}"
                        title="Edit Loan Details">
                    <i class="fa fa-edit"></i>
                </button>
                @if($isActive && $rem <= 0)
                    <form method="post" action="{{ url('payroll/staff/loans') }}" style="display:inline-block;" onsubmit="return confirm('Close this fully settled loan?');">
                        @csrf
                        <input type="hidden" name="action" value="close_loan">
                        <input type="hidden" name="loan_id" value="{{ $row['id'] }}">
                        <button type="submit" class="btn-action-icon btn-action-success" title="Close Fully Settled Loan">
                            <i class="fa fa-check-circle"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr class="empty-row">
        <td colspan="12" class="text-center py-5" style="text-align: center !important; vertical-align: middle !important; background: #ffffff !important;">
            <div class="d-flex flex-column align-items-center justify-content-center text-center w-100 py-3" style="margin: 0 auto;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="fa fa-folder-open-o text-muted" style="font-size: 18px;"></i>
                </div>
                <div class="font-weight-bold text-dark" style="font-size: 12.5px;">No Staff Loans or Advances Found</div>
                <div class="text-muted mt-1" style="font-size: 11px;">No loan or advance records found matching your filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse