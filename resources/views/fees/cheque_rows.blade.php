@php
    $i = $startIndex ?? 0;
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $receipt)
        @php
            $fullName = trim(($receipt->first_name ?? '') . ' ' . ($receipt->last_name ?? ''));
            $statusVal = (int) ($receipt->status ?? 1);
            $paymentDateFormatted = !empty($receipt->payment_date) ? date('d-m-Y', strtotime($receipt->payment_date)) : '-';
            $chequeDateFormatted = !empty($receipt->cheque_date) ? date('d-m-Y', strtotime($receipt->cheque_date)) : '';

            if ($statusVal === 0) {
                $statusBadgeClass = 'badge-cheque-realised';
                $statusIcon = 'fa-check-circle';
                $statusLabel = 'Cheque Realised';
            } elseif ($statusVal === 2) {
                $statusBadgeClass = 'badge-cheque-dishonoured';
                $statusIcon = 'fa-times-circle';
                $statusLabel = 'Cheque Dishonoured';
            } else {
                $statusBadgeClass = 'badge-cheque-pending';
                $statusIcon = 'fa-clock-o';
                $statusLabel = 'Pending Realisation';
            }

            $slipUrl = '';
            if (!empty($receipt->payment_receipt)) {
                if (file_exists(public_path('schoolimage/payment_receipt/' . $receipt->payment_receipt))) {
                    $slipUrl = asset('public/schoolimage/payment_receipt/' . $receipt->payment_receipt);
                } elseif (file_exists(base_path('schoolimage/payment_receipt/' . $receipt->payment_receipt))) {
                    $slipUrl = url('schoolimage/payment_receipt/' . $receipt->payment_receipt);
                }
            }
        @endphp
        <tr class="cheque-row" data-id="{{ $receipt->id }}">
            {{-- 1. Sr. No. --}}
            <td class="text-center font-weight-bold text-muted col-sr">
                {{ ++$i }}
            </td>

            {{-- 2. Receipt No. --}}
            <td class="col-receipt">
                <a href="{{ url('fees_receipt/'.$receipt->id.'/view') }}" target="_blank" class="ca-invoice-link font-weight-bold" title="Print Fee Receipt ({{ $receipt->invoice_no ?? '' }})">
                    <i class="fa fa-print mr-1 text-primary"></i>{{ $receipt->invoice_no ?: '-' }}
                </a>
            </td>

            {{-- 3. Status --}}
            <td class="text-center col-status">
                <span class="cheque-status-badge {{ $statusBadgeClass }}" title="{{ $receipt->remark ?: $statusLabel }}">
                    <i class="fa {{ $statusIcon }} mr-1"></i>{{ $statusLabel }}
                </span>
                @if(!empty($receipt->remark))
                    <div class="text-truncate text-muted mt-0.5" style="font-size: 9.5px; max-width: 100%;" title="{{ $receipt->remark }}">
                        {{ $receipt->remark }}
                    </div>
                @endif
            </td>

            {{-- 4. Admission No. --}}
            <td class="col-adm">
                @if(!empty($receipt->admission_id))
                    <a href="{{ url('studentDetail/' . $receipt->admission_id) }}" class="ca-admission-link font-weight-bold" title="View Student Profile">
                        {{ $receipt->admissionNo ?: '-' }}
                    </a>
                @else
                    <span class="font-weight-bold">{{ $receipt->admissionNo ?: '-' }}</span>
                @endif
            </td>

            {{-- 5. Class --}}
            <td class="col-class">
                <span class="badge-class" title="{{ $receipt->class_name ?? '' }}">{{ $receipt->class_name ?: '-' }}</span>
            </td>

            {{-- 6. Student Name --}}
            <td class="col-name" title="{{ $fullName }}">
                @if(!empty($receipt->admission_id))
                    <a href="{{ url('studentDetail/' . $receipt->admission_id) }}" class="student-name font-weight-600 text-truncate d-inline-block" style="max-width: 100%;">
                        {{ $fullName ?: '-' }}
                    </a>
                @else
                    <span class="font-weight-600 text-truncate d-inline-block" style="max-width: 100%;">{{ $fullName ?: '-' }}</span>
                @endif
            </td>

            {{-- 7. Father's Name --}}
            <td class="col-father" title="{{ $receipt->father_name ?? '' }}">
                <div class="font-weight-600 text-truncate" style="max-width: 100%;">
                    {{ $receipt->father_name ?: '-' }}
                </div>
            </td>

            {{-- 8. Mobile --}}
            <td class="col-mobile">
                @if(!empty($receipt->mobile))
                    <a href="tel:{{ $receipt->mobile }}" class="text-secondary font-weight-600" style="text-decoration: none;" title="Call {{ $receipt->mobile }}">
                        <i class="fa fa-phone text-muted mr-1" style="font-size: 10px;"></i>{{ $receipt->mobile }}
                    </a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            {{-- 9. Cheque / Bank Info --}}
            <td class="col-cheque">
                @if(!empty($receipt->cheque_number) || !empty($receipt->bank_name) || !empty($receipt->transaction_id))
                    <div class="text-truncate font-weight-600" style="font-size: 10.5px; color: #0f172a;" title="Cheque/Ref: {{ $receipt->cheque_number ?: $receipt->transaction_id }} | Bank: {{ $receipt->bank_name }}">
                        @if(!empty($receipt->cheque_number))
                            <span>#{{ $receipt->cheque_number }}</span>
                        @elseif(!empty($receipt->transaction_id))
                            <span>Ref: {{ $receipt->transaction_id }}</span>
                        @endif
                        @if(!empty($chequeDateFormatted))
                            <span class="text-muted ml-1">({{ $chequeDateFormatted }})</span>
                        @endif
                    </div>
                    @if(!empty($receipt->bank_name))
                        <div class="text-truncate text-muted" style="font-size: 9.5px;" title="{{ $receipt->bank_name }}">
                            <i class="fa fa-university mr-1"></i>{{ $receipt->bank_name }}
                        </div>
                    @endif
                @else
                    <span class="text-muted">-</span>
                @endif
                @if(!empty($slipUrl))
                    <button type="button" class="btn-slip-preview profileImg mt-0.5" data-img="{{ $slipUrl }}" title="View Uploaded Cheque / Payment Slip">
                        <i class="fa fa-picture-o mr-1"></i>View Slip
                    </button>
                @endif
            </td>

            {{-- 10. Payment Date --}}
            <td class="text-center font-weight-600 col-date">
                <i class="fa fa-calendar-o mr-1 text-muted"></i>{{ $paymentDateFormatted }}
            </td>

            {{-- 11. Payment Mode --}}
            <td class="text-center col-mode">
                <span class="badge-mode">{{ $receipt->payment_mode ?: 'Cheque' }}</span>
            </td>

            {{-- 12. Amount --}}
            <td class="text-right font-weight-bold col-amount" style="color: #059669; font-size: 12px;">
                ₹{{ number_format((float) ($receipt->amount ?? 0), 2) }}
            </td>

            {{-- 13. Action (Status Dropdown) --}}
            <td class="text-center fixed_action_col col-action">
                <select name="status"
                        data-id="{{ $receipt->id ?? '' }}"
                        data-current="{{ $statusVal }}"
                        data-invoice="{{ $receipt->invoice_no ?? '' }}"
                        data-student="{{ $fullName }}"
                        data-amount="{{ number_format((float) ($receipt->amount ?? 0), 2) }}"
                        data-remark="{{ $receipt->remark ?? '' }}"
                        class="cheque-action-select statusDrop">
                    <option value="0" {{ $statusVal === 0 ? 'selected' : '' }}>Cheque Realised</option>
                    <option value="1" {{ $statusVal === 1 ? 'selected' : '' }}>Cheque Pending Realisation</option>
                    <option value="2" {{ $statusVal === 2 ? 'selected' : '' }}>Cheque Dishonoured</option>
                </select>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="13" class="p-0 border-0">
            <div class="dash-empty-state">
                <i class="fa fa-money dash-empty-icon"></i>
                <div class="dash-empty-title">No Cheque / Fee Records Found</div>
                <div class="dash-empty-subtitle">Try changing the cheque status filter, date range, or search criteria.</div>
            </div>
        </td>
    </tr>
@endif
