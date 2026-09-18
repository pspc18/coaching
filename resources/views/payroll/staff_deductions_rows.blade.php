@forelse($rows as $row)
    @php
        $isApplied = !empty($row['is_applied']);
        $isLocked = !empty($row['is_locked']);
    @endphp
    <tr data-deduction-id="{{ $row['id'] }}">
        <td class="text-center" style="width: 70px;">
            <span class="badge badge-light border font-weight-bold" style="font-size:10.5px; letter-spacing:0.3px; color:#002C54;">#{{ $row['id'] }}</span>
        </td>
        <td>
            <div class="font-weight-bold text-dark" style="font-size:12px; line-height:1.25;">{{ $row['name'] }}</div>
            <div class="small text-muted mt-1" style="font-size:10px;">{{ $row['display_unique_id'] }}</div>
        </td>
        <td class="text-center" style="width: 85px;">
            <span class="badge badge-light border font-weight-semibold" style="font-size:11px;">
                {{ sprintf('%02d/%04d', $row['month'], $row['year']) }}
            </span>
        </td>
        <td>
            <div class="font-weight-semibold text-dark" style="font-size:11.5px;">{{ $row['title'] ?: 'Deduction' }}</div>
            @if(!empty($row['remark']))
                <div class="small text-muted" style="font-size:9.5px;">{{ Str::limit($row['remark'], 40) }}</div>
            @endif
        </td>
        <td class="text-right font-weight-bold" style="font-size:12px; color:#dc2626; width: 110px;">
            ₹{{ number_format($row['amount'], 2) }}
        </td>
        <td class="text-center" style="width: 105px;">
            @if($isApplied)
                <span class="badge badge-success font-weight-semibold" style="font-size:10px; padding:2px 6px;">Applied</span>
            @else
                <span class="badge badge-warning font-weight-semibold" style="font-size:10px; padding:2px 6px; color:#0f172a; background:#fef08a;">Skipped</span>
            @endif
            @if($isLocked)
                <span class="badge badge-secondary font-weight-semibold" style="font-size:9.5px; padding:2px 5px;" title="Salary finalized for this period">
                    <i class="fa fa-lock"></i> Locked
                </span>
            @endif
        </td>
        <td class="text-center fixed_action_col" style="width: 85px;">
            <div class="d-inline-flex align-items-center" style="gap:3px;">
                @if(!$isLocked)
                    <button type="button" class="btn-action-icon btn-action-edit js-edit-deduction"
                            data-toggle="modal" data-target="#editDeductionModal"
                            data-id="{{ $row['id'] }}"
                            data-unique-id="{{ $row['unique_id'] }}"
                            data-name="{{ $row['name'] }} ({{ $row['display_unique_id'] }})"
                            data-amount="{{ number_format($row['amount'], 2, '.', '') }}"
                            data-title="{{ $row['title'] }}"
                            data-remark="{{ $row['remark'] }}"
                            title="Edit Deduction">
                        <i class="fa fa-edit"></i>
                    </button>
                    <form method="post" action="{{ url('payroll/staff/deductions?month='.$month.'&year='.$year) }}" style="display:inline-block;" onsubmit="return confirm('Delete this manual deduction?');">
                        @csrf
                        <input type="hidden" name="action" value="delete_deduction">
                        <input type="hidden" name="deduction_id" value="{{ $row['id'] }}">
                        <button type="submit" class="btn-action-icon btn-action-delete" title="Delete Deduction">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @else
                    <button type="button" class="btn-action-icon" disabled style="background:#f1f5f9; color:#94a3b8; border-color:#e2e8f0; cursor:not-allowed;" title="Locked after salary generation">
                        <i class="fa fa-lock"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr class="empty-row">
        <td colspan="7" class="text-center py-5" style="text-align: center !important; vertical-align: middle !important; background: #ffffff !important;">
            <div class="d-flex flex-column align-items-center justify-content-center text-center w-100 py-3" style="margin: 0 auto;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="fa fa-folder-open-o text-muted" style="font-size: 18px;"></i>
                </div>
                <div class="font-weight-bold text-dark" style="font-size: 12.5px;">No Manual Deductions Found</div>
                <div class="text-muted mt-1" style="font-size: 11px;">No deduction entries recorded for this period.</div>
            </div>
        </td>
    </tr>
@endforelse