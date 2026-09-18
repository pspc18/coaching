@php
    $i = $startIndex ?? 0;
    $expensePermission = Helper::permissioncheck(16);
    $canEdit = $expensePermission->edit ?? true;
    $canDelete = $expensePermission->delete ?? true;
    $imageShowPath = env('IMAGE_SHOW_PATH');
@endphp

@if(!empty($data) && count($data) > 0)
    @foreach($data as $item)
        @php
            $categoryName = $categories[$item->category_id] ?? 'Other';
            $isPaid = ($item->payment_status ?? 'paid') === 'paid';
            $isRecurring = ($item->expense_type ?? '') === 'recurring';
            $hasAttachment = !empty($item->attachment);
            $attachmentUrl = $hasAttachment ? ($imageShowPath . 'expense/' . $item->attachment) : '';
        @endphp
        <tr class="expense-row {{ $i % 2 == 0 ? 'even-row' : 'odd-row' }}"
            data-id="{{ $item->id }}"
            data-voucher="{{ strtolower($item->invoice_no ?? '') }}"
            data-name="{{ strtolower($item->name ?? '') }}"
            data-category="{{ $item->category_id ?? '' }}"
            data-payee="{{ strtolower($item->payee_name ?? '') }}"
            data-date="{{ $item->date ?? '' }}"
            data-amount="{{ (float) ($item->amount ?? 0) }}"
            data-status="{{ $item->payment_status ?? 'paid' }}">

            {{-- 1. Serial Number --}}
            <td class="text-center font-weight-bold text-muted serial-cell">{{ ++$i }}</td>

            {{-- 2. Voucher No. & Date --}}
            <td>
                <span class="voucher-badge">{{ $item->invoice_no ?: '-' }}</span>
                <div class="date-meta">
                    <i class="fa fa-calendar-o"></i> {{ $item->date ? date('d M Y', strtotime($item->date)) : '-' }}
                </div>
            </td>

            {{-- 3. Category & Particular --}}
            <td>
                <span class="category-pill">{{ $categoryName }}</span>
                <span class="particular-title">{{ $item->name ?: '-' }}</span>
                @if($isRecurring)
                    <span class="recurring-tag">
                        <i class="fa fa-repeat"></i> {{ ucfirst(str_replace('_', ' ', $item->recurring_frequency ?: 'recurring')) }}
                    </span>
                @endif
                @if(!empty($item->description))
                    <div class="notes-meta" title="{{ $item->description }}">
                        <i class="fa fa-info-circle text-muted"></i> {{ Str::limit($item->description, 45) }}
                    </div>
                @endif
            </td>

            {{-- 4. Paid To / Vendor & Bill --}}
            <td>
                <span class="payee-text">{{ $item->payee_name ?: '-' }}</span>
                @if(!empty($item->bill_no))
                    <span class="bill-tag"><i class="fa fa-file-text-o"></i> Bill: {{ $item->bill_no }}</span>
                @endif
            </td>

            {{-- 5. Payment Mode & Ref --}}
            <td>
                <span class="pm-badge">{{ $item->payment_mode_name ?: 'Cash' }}</span>
                @if(!empty($item->payment_reference))
                    <span class="ref-text" title="Payment Reference / UTR"><i class="fa fa-hashtag"></i> {{ $item->payment_reference }}</span>
                @endif
            </td>

            {{-- 6. Rate x Qty --}}
            <td class="text-right">
                <span class="rate-qty-text">{{ (float)($item->quantity ?: 1) }} × ₹{{ number_format((float)($item->rate ?: 0), 2) }}</span>
            </td>

            {{-- 7. Total Amount --}}
            <td class="text-right">
                <span class="amount-text">₹{{ number_format((float)($item->amount ?: 0), 2) }}</span>
            </td>

            {{-- 8. Payment Status --}}
            <td class="text-center">
                @if($isPaid)
                    <span class="status-badge status-paid">
                        <i class="fa fa-check"></i> Paid
                    </span>
                @else
                    <span class="status-badge status-pending">
                        <i class="fa fa-clock-o"></i> Pending
                    </span>
                @endif
            </td>

            {{-- 9. Receipt / Attachment --}}
            <td class="text-center">
                @if($hasAttachment)
                    <a href="{{ $attachmentUrl }}" target="_blank" class="table-btn btn-receipt" title="View Attachment / Receipt">
                        <i class="fa fa-paperclip text-primary"></i>
                    </a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            {{-- 10. Actions (Sticky Right Column) --}}
            <td class="text-center fixed_action_col">
                <div class="table-actions">
                    @if($canEdit)
                        <a href="{{ url('expenseEdit/' . $item->invoice_no) }}" class="table-btn btn-action-edit" title="Edit Voucher">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endif

                    @if($canDelete)
                        <button type="button" class="table-btn btn-action-delete delete-expense-trigger" 
                                data-id="{{ $item->id }}" 
                                data-invoice="{{ $item->invoice_no }}"
                                data-name="{{ $item->name }}"
                                data-amount="₹{{ number_format((float)$item->amount, 2) }}"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal" 
                                title="Delete Entry">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr id="empty-state-row">
        <td colspan="10" class="text-center p-0">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-file-text-o"></i></div>
                <div class="empty-title">No Expense Records Found</div>
                <div class="empty-desc">No expense entries found matching your chosen filters and search criteria.</div>
                @if($canAdd ?? true)
                    <a href="{{ url('expenseAdd') }}" class="dash-btn dash-btn-primary mt-2">
                        <i class="fa fa-plus mr-1"></i> Add New Expense
                    </a>
                @endif
            </div>
        </td>
    </tr>
@endif