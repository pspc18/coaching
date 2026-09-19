@php
    $i = $startIndex ?? 0;
    $canView = Helper::permissioncheck(11)->view ?? false;
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            $fullName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
            $assignDiscount = (float) ($item->assign_discount ?? 0);
            $grossAmount = (float) ($item->total_amount ?? 0);
            $assigned = max(0, $grossAmount - $assignDiscount);

            // Pre-aggregated paid data from Controller (Zero N+1 queries)
            $paid = $paidLookup[$item->id] ?? null;
            $paidAmt = (float) ($paid->total_paid ?? 0);
            $paidFine = (float) ($paid->total_fine ?? 0);
            $paidDisc = (float) ($paid->total_discount ?? 0);

            $pending = max(0, $assigned - $paidAmt - $paidDisc);

            // Determine status
            if ($assigned > 0 && $pending <= 0) {
                $statusBadge = 'badge-status-paid';
                $statusText = 'Paid';
                $statusIcon = 'fa-check-circle';
            } elseif ($paidAmt > 0) {
                $statusBadge = 'badge-status-partial';
                $statusText = 'Partial';
                $statusIcon = 'fa-clock-o';
            } else {
                $statusBadge = 'badge-status-unpaid';
                $statusText = 'Unpaid';
                $statusIcon = 'fa-times-circle';
            }
        @endphp
        <tr class="ledger-row" data-id="{{ $item->id }}">
            {{-- 1. Serial Number --}}
            <td class="text-center font-weight-bold text-muted col-sr">
                {{ ++$i }}
            </td>

            {{-- 2. Admission No --}}
            <td class="col-adm">
                <a href="{{ url('studentDetail/' . $item->id) }}" class="ca-admission-link font-weight-bold" title="View Student Profile">
                    {{ $item->admissionNo ?: '-' }}
                </a>
            </td>

            {{-- 3. Class (Enlarged Width) --}}
            <td class="col-class">
                <span class="badge-class" title="{{ $item->className }}">{{ $item->className ?: '-' }}</span>
            </td>

            {{-- 4. Student Name --}}
            <td class="col-name" title="{{ $fullName }}">
                <a href="{{ url('studentDetail/' . $item->id) }}" class="student-name font-weight-600 text-truncate d-inline-block" style="max-width: 100%;" title="{{ $fullName }}">
                    {{ $fullName ?: '-' }}
                </a>
            </td>

            {{-- 5. Father's Name & Mobile (Combined) --}}
            <td class="col-father">
                <div class="font-weight-600 text-truncate" title="{{ $item->father_name }}" style="max-width: 100%; line-height: 1.25;">
                    {{ $item->father_name ?: '-' }}
                </div>
                @if(!empty($item->mobile))
                    <div class="text-truncate" style="max-width: 100%; line-height: 1.1; margin-top: 2px;">
                        <a href="tel:{{ $item->mobile }}" class="text-secondary" style="font-size: 10.5px; text-decoration: none;" title="Call {{ $item->mobile }}">
                            <i class="fa fa-phone text-muted mr-1" style="font-size: 9.5px;"></i>{{ $item->mobile }}
                        </a>
                    </div>
                @endif
            </td>

            {{-- 6. Total Assigned Fees --}}
            <td class="text-right font-weight-bold col-total" style="color: #1e293b;">
                ₹{{ number_format($assigned, 2) }}
            </td>

            {{-- 7. Paid Fees --}}
            <td class="text-right font-weight-bold col-paid" style="color: #059669;">
                ₹{{ number_format($paidAmt, 2) }}
            </td>

            {{-- 8. Paid Fine --}}
            <td class="text-right font-weight-600 col-fine" style="color: #dc2626;">
                ₹{{ number_format($paidFine, 2) }}
            </td>

            {{-- 9. Discount --}}
            <td class="text-right font-weight-600 col-disc" style="color: #d97706;">
                ₹{{ number_format($paidDisc, 2) }}
            </td>

            {{-- 10. Pending Fees --}}
            <td class="text-right font-weight-bold col-pending" style="color: {{ $pending > 0 ? '#dc2626' : '#059669' }};">
                ₹{{ number_format($pending, 2) }}
            </td>

            {{-- 11. Payment Status Badge --}}
            <td class="text-center col-status">
                <span class="ledger-status-badge {{ $statusBadge }}">
                    <i class="fa {{ $statusIcon }} mr-1"></i>{{ $statusText }}
                </span>
            </td>

            {{-- 12. Action Column --}}
            <td class="text-center fixed_action_col col-action">
                <div class="table-actions">
                    <button type="button" 
                            class="ca-action-btn btn-view data {{ $canView ? '' : 'd-none' }}" 
                            data-id="{{ $item->id }}" 
                            data-toggle="modal" 
                            data-target="#exampleModal" 
                            title="View Ledger Statement">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="12" class="p-0 border-0">
            <div class="dash-empty-state">
                <i class="fa fa-folder-open-o dash-empty-icon"></i>
                <div class="dash-empty-title">No Student Ledger Records Found</div>
                <div class="dash-empty-subtitle">Try adjusting your filters, search keywords, or selecting a different class.</div>
            </div>
        </td>
    </tr>
@endif
