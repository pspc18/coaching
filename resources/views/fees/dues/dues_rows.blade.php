@php
    $i = $startIndex ?? 0;
    $feesGroups = $feesGroups ?? collect();
    $colSpanCount = 12 + count($feesGroups);
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $stu)
        @php
            $stuId = $stu['id'] ?? '';
            $statusVal = (int) ($stu['status'] ?? 1);
            $assignedAmt = (float) ($stu['fees_assigned'] ?? 0);
            $paidAmt = (float) ($stu['fees_paid'] ?? 0);
            $pendingAmt = (float) ($stu['total_pending'] ?? 0);
            $groupBreakdown = $stu['group_breakdown'] ?? [];
            $headSummaryTitle = $stu['head_summary_plain'] ?? '';
        @endphp
        <tr class="dues-row" data-id="{{ $stuId }}">
            {{-- 1. Checkbox & Sr. No. --}}
            <td class="text-center font-weight-bold text-muted col-sr">
                <input type="hidden" name="message[{{ $stuId }}]" value="{{ $stu['message'] ?? '' }}">
                <input type="hidden" name="mobile[{{ $stuId }}]" value="{{ $stu['mobile'] ?? '' }}">
                <div class="d-inline-flex align-items-center" style="gap: 5px;">
                    <input type="checkbox" class="checkbox_id row-select-cb" name="checkbox[]" value="{{ $stuId }}" style="cursor: pointer;">
                    <span>{{ ++$i }}</span>
                </div>
            </td>

            {{-- 2. Admission No. --}}
            <td class="col-adm">
                <a href="{{ url('studentDetail/' . $stuId) }}" class="ca-admission-link font-weight-bold" title="View Student Profile">
                    {{ $stu['admissionNo'] ?: '-' }}
                </a>
            </td>

            {{-- 3. Class --}}
            <td class="col-class">
                <span class="badge-class" title="{{ $stu['className'] ?? '' }}">{{ $stu['className'] ?: '-' }}</span>
            </td>

            {{-- 4. Student Name --}}
            <td class="col-name" title="{{ $stu['name'] ?? '' }}">
                <a href="{{ url('studentDetail/' . $stuId) }}" class="student-name font-weight-600 text-truncate d-inline-block" style="max-width: 100%;">
                    {{ $stu['name'] ?: '-' }}
                </a>
            </td>

            {{-- 5. Father's Name --}}
            <td class="col-father" title="{{ $stu['father_name'] ?? '' }}">
                <div class="font-weight-600 text-truncate" style="max-width: 100%;">
                    {{ $stu['father_name'] ?: '-' }}
                </div>
            </td>

            {{-- 6. Mobile --}}
            <td class="col-mobile">
                @if(!empty($stu['mobile']))
                    <a href="tel:{{ $stu['mobile'] }}" class="text-secondary font-weight-600" style="text-decoration: none;" title="Call {{ $stu['mobile'] }}">
                        <i class="fa fa-phone text-muted mr-1" style="font-size: 10px;"></i>{{ $stu['mobile'] }}
                    </a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            {{-- 7. Gender & Category --}}
            <td class="text-center col-gender">
                <span class="font-weight-600">{{ $stu['gender_name'] ?: '-' }}</span>
                @if(!empty($stu['category']))
                    <span class="badge-cat ml-1">{{ $stu['category'] }}</span>
                @endif
            </td>

            {{-- 8. Status --}}
            <td class="text-center col-status">
                @if($statusVal === 1)
                    <span class="dues-status-badge badge-status-continue"><i class="fa fa-check-circle mr-1"></i>Continue</span>
                @else
                    <span class="dues-status-badge badge-status-discontinue"><i class="fa fa-times-circle mr-1"></i>Discontinue</span>
                @endif
            </td>

            {{-- Dynamic Fee Group Columns (When Class is Selected) --}}
            @if(!empty($feesGroups) && count($feesGroups) > 0)
                @foreach($feesGroups as $fg)
                    @php
                        $fgDue = (float) ($groupBreakdown[$fg->id] ?? 0);
                    @endphp
                    <td class="text-right font-weight-600 col-group" style="color: {{ $fgDue > 0 ? '#dc2626' : '#64748b' }};">
                        ₹{{ number_format($fgDue, 0) }}
                    </td>
                @endforeach
            @endif

            {{-- 9. Assigned Fee --}}
            <td class="text-right font-weight-bold col-assigned" style="color: #1e293b;">
                ₹{{ number_format($assignedAmt, 0) }}
            </td>

            {{-- 10. Paid Fee --}}
            <td class="text-right font-weight-bold col-paid" style="color: #059669;">
                ₹{{ number_format($paidAmt, 0) }}
            </td>

            {{-- 11. Pending Due --}}
            <td class="text-right font-weight-bold col-pending" style="color: #dc2626;" title="{{ $headSummaryTitle }}">
                ₹{{ number_format($pendingAmt, 0) }}
            </td>

            {{-- 12. Action --}}
            <td class="text-center fixed_action_col col-action">
                <div class="table-actions">
                    <a href="{{ url('Fees/add?student_id=' . $stuId) }}" class="ca-action-btn btn-collect" title="Collect Fees for {{ $stu['name'] ?? '' }}">
                        <i class="fa fa-inr"></i>
                    </a>
                    <button type="button"
                            class="ca-action-btn btn-remind btn-single-remind"
                            data-id="{{ $stuId }}"
                            data-name="{{ $stu['name'] ?? '' }}"
                            data-mobile="{{ $stu['mobile'] ?? '' }}"
                            data-pending="{{ number_format($pendingAmt, 0) }}"
                            title="Send Fee Reminder to {{ $stu['name'] ?? '' }}">
                        <i class="fa fa-whatsapp"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="{{ $colSpanCount }}" class="p-0 border-0">
            <div class="dash-empty-state">
                <i class="fa fa-check-circle-o dash-empty-icon text-success"></i>
                <div class="dash-empty-title">No Pending Fee Dues Found</div>
                <div class="dash-empty-subtitle">No students match the current filter criteria with outstanding fee balances.</div>
            </div>
        </td>
    </tr>
@endif
