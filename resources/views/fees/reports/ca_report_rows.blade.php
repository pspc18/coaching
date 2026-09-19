@php
    $i = $startIndex ?? 0;
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $receipt)
        @php
            $fullName = trim(($receipt->first_name ?? '') . ' ' . ($receipt->last_name ?? ''));
            $collectorName = trim(($receipt->users_first_name ?? '') . ' ' . ($receipt->users_last_name ?? ''));
            $statusVal = (int) ($receipt->status ?? 0);
            
            // Format dates strictly as d-m-Y per user requirement
            $paymentDateFormatted = !empty($receipt->payment_date) ? date('d-m-Y', strtotime($receipt->payment_date)) : '-';

            // Payment Mode badge class
            $modeName = $receipt->payment_mode ?? 'Cash';
            $modeLower = strtolower($modeName);
            $modeBadgeClass = 'badge-mode-cash';
            if (strpos($modeLower, 'online') !== false || strpos($modeLower, 'upi') !== false || strpos($modeLower, 'qr') !== false) {
                $modeBadgeClass = 'badge-mode-online';
            } elseif (strpos($modeLower, 'cheque') !== false) {
                $modeBadgeClass = 'badge-mode-cheque';
            } elseif (strpos($modeLower, 'bank') !== false || strpos($modeLower, 'neft') !== false || strpos($modeLower, 'rtgs') !== false) {
                $modeBadgeClass = 'badge-mode-bank';
            }
        @endphp
        <tr class="ca-report-row" 
            data-invoice="{{ strtolower($receipt->invoice_no ?? '') }}" 
            data-offline="{{ strtolower($receipt->offline_receipt_no ?? '') }}"
            data-admission="{{ strtolower($receipt->admissionNo ?? '') }}"
            data-student="{{ strtolower($fullName) }}"
            data-father="{{ strtolower($receipt->father_name ?? '') }}"
            data-class="{{ (string) ($receipt->class_type_id ?? '') }}"
            data-date="{{ $paymentDateFormatted }}"
            data-status="{{ $statusVal }}">
            
            {{-- 1. Serial Number --}}
            <td class="text-center font-weight-bold text-muted" style="width: 35px; vertical-align: middle;">
                {{ ++$i }}
            </td>

            {{-- 2. Receipt No (Invoice Button) --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                <form action="{{ url('printFeesInvoice') }}" method="post" target="_blank" class="m-0 p-0 d-inline-block">
                    @csrf
                    <button type="submit" name="fees_details_invoice_id" value="{{ $receipt->id ?? '' }}" class="ca-invoice-link font-weight-bold" title="Click to view & print invoice">
                        <i class="fa fa-print mr-1 text-primary"></i>{{ $receipt->invoice_no ?: '-' }}
                    </button>
                </form>
            </td>

            {{-- 3. Offline Slip No --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                @if(!empty($receipt->offline_receipt_no))
                    <span class="badge-offline-slip">{{ $receipt->offline_receipt_no }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            {{-- 4. Payment Date (strictly d-m-Y) --}}
            <td style="white-space: nowrap; vertical-align: middle;" class="font-weight-600 text-center">
                <i class="fa fa-calendar-o mr-1 text-muted"></i>{{ $paymentDateFormatted }}
            </td>

            {{-- 5. Admission No --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                @if(!empty($receipt->admission_id))
                    <a href="{{ url('studentDetail/' . $receipt->admission_id) }}" class="ca-admission-link font-weight-bold" title="View Student Profile">
                        {{ $receipt->admissionNo ?: '-' }}
                    </a>
                @else
                    <span>{{ $receipt->admissionNo ?: '-' }}</span>
                @endif
            </td>

            {{-- 6. Class --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                <span class="badge-class-name">{{ $receipt->class_name ?: '-' }}</span>
            </td>

            {{-- 7. Student Name --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                @if(!empty($receipt->admission_id))
                    <a href="{{ url('studentDetail/' . $receipt->admission_id) }}" class="ca-student-name font-weight-bold" title="View Student Profile">
                        {{ $fullName ?: '-' }}
                    </a>
                @else
                    <span class="font-weight-600">{{ $fullName ?: '-' }}</span>
                @endif
            </td>

            {{-- 8. Father's Name --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                {{ $receipt->father_name ?: '-' }}
            </td>

            {{-- 9. Collected By --}}
            <td style="white-space: nowrap; vertical-align: middle;">
                <span class="ca-collector-name"><i class="fa fa-user-circle-o mr-1 text-secondary"></i>{{ $collectorName ?: 'Admin' }}</span>
            </td>

            {{-- 10. Payment Mode --}}
            <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                <span class="badge-payment-mode {{ $modeBadgeClass }}">{{ $receipt->payment_mode ?: 'Cash' }}</span>
            </td>

            {{-- 11. Discount --}}
            <td class="text-right" style="white-space: nowrap; vertical-align: middle; color: #d97706; font-weight: 600;">
                ₹{{ number_format($receipt->discount ?? 0, 2) }}
            </td>

            {{-- 12. Fine --}}
            <td class="text-right" style="white-space: nowrap; vertical-align: middle; color: #dc2626; font-weight: 600;">
                ₹{{ number_format($receipt->total_fine ?? 0, 2) }}
            </td>

            {{-- 13. Amount (Collected) --}}
            <td class="text-right font-weight-bold" style="white-space: nowrap; vertical-align: middle; color: #059669; font-size: 12px;">
                ₹{{ number_format($receipt->amount ?? 0, 2) }}
            </td>

            {{-- 14. Status Badge --}}
            <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                @if($statusVal === 0)
                    <span class="badge-status-received"><i class="fa fa-check-circle mr-1"></i>Received</span>
                @elseif($statusVal === 1)
                    <span class="badge-status-pending"><i class="fa fa-clock-o mr-1"></i>Pending</span>
                @elseif($statusVal === 2)
                    <span class="badge-status-cancelled"><i class="fa fa-times-circle mr-1"></i>Cancelled</span>
                @else
                    <span class="badge-status-received">Received</span>
                @endif
            </td>

            {{-- 15. Action Column --}}
            <td class="text-center fixed_action_col" style="white-space: nowrap; vertical-align: middle;">
                <div class="table-actions">
                    <form action="{{ url('printFeesInvoice') }}" method="post" target="_blank" class="d-inline-block m-0 p-0">
                        @csrf
                        <button type="submit" name="fees_details_invoice_id" value="{{ $receipt->id ?? '' }}" class="ca-action-btn btn-print" title="Print Fee Receipt">
                            <i class="fa fa-print"></i>
                        </button>
                    </form>
                    @if(!empty($receipt->admission_id))
                        <a href="{{ url('studentDetail/' . $receipt->admission_id) }}" class="ca-action-btn btn-view" title="View Student Profile">
                            <i class="fa fa-eye"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="15" class="p-0 border-0">
            <div class="dash-empty-state">
                <i class="fa fa-folder-open-o dash-empty-icon"></i>
                <div class="dash-empty-title">No Fee Receipts Found</div>
                <div class="dash-empty-subtitle">Try adjusting your date range, search keywords, or filter criteria.</div>
            </div>
        </td>
    </tr>
@endif
