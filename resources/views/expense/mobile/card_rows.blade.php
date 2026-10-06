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
            $dateFormatted = $item->date ? date('d M Y', strtotime($item->date)) : '-';
            $amountVal = (float)($item->amount ?: 0);
            $qty = (float)($item->quantity ?: 1);
            $rate = (float)($item->rate ?: 0);

            // Contextual category color styling
            $catIcon = match((int)($item->category_id ?? 0)) {
                1 => 'fa-users',
                2 => 'fa-building-o',
                3 => 'fa-bolt',
                4 => 'fa-wifi',
                5 => 'fa-print',
                6 => 'fa-book',
                7 => 'fa-bullhorn',
                8 => 'fa-wrench',
                9 => 'fa-archive',
                10 => 'fa-laptop',
                11 => 'fa-trash-o',
                12 => 'fa-bus',
                13 => 'fa-calendar-check-o',
                14 => 'fa-briefcase',
                15 => 'fa-university',
                16 => 'fa-coffee',
                17 => 'fa-shield',
                default => 'fa-money'
            };
        @endphp

        <article class="exp-mob-card {{ $isPaid ? 'border-paid' : 'border-pending' }}" id="expense-card-{{ $item->id }}" data-id="{{ $item->id }}">
            {{-- Card Header: Category Icon, Voucher & Paid Status --}}
            <div class="exp-card-header">
                <div class="exp-avatar-box {{ $isPaid ? 'avatar-paid' : 'avatar-pending' }}">
                    <i class="fa {{ $catIcon }}"></i>
                </div>
                <div class="exp-header-info">
                    <div class="exp-title-row">
                        <span class="exp-mob-voucher">{{ $item->invoice_no ?: 'EXP-#' }}</span>
                        <span class="exp-status-pill {{ $isPaid ? 'status-pill-paid' : 'status-pill-pending' }}">
                            <i class="fa {{ $isPaid ? 'fa-check-circle' : 'fa-clock-o' }}"></i> {{ ucfirst($item->payment_status ?? 'paid') }}
                        </span>
                    </div>
                    <div class="exp-pills-wrap">
                        <span class="exp-category-badge">{{ $categoryName }}</span>
                        @if($isRecurring)
                            <span class="exp-recurring-badge">
                                <i class="fa fa-repeat"></i> {{ ucfirst(str_replace('_', ' ', $item->recurring_frequency ?: 'recurring')) }}
                            </span>
                        @endif
                        <span class="exp-date-badge">
                            <i class="fa fa-calendar-o"></i> {{ $dateFormatted }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card Body: Particular Title & Key Meta Rows --}}
            <div class="exp-card-body">
                {{-- Particular Name --}}
                <div class="exp-particular-title" title="{{ $item->name ?: '-' }}">
                    {{ $item->name ?: 'Expense Particular' }}
                </div>

                {{-- Amount and Rate x Qty Glance Box --}}
                <div class="exp-amount-glance">
                    <div class="exp-amount-left">
                        <span class="exp-amount-tag">Total Amount</span>
                        <span class="exp-amount-val">₹{{ number_format($amountVal, 2) }}</span>
                    </div>
                    <div class="exp-amount-right">
                        <span class="exp-calc-text">{{ $qty }} × ₹{{ number_format($rate, 2) }}</span>
                    </div>
                </div>

                {{-- Info Grid: Payee, Bill No, Payment Mode & Reference --}}
                <div class="exp-meta-grid">
                    <div class="exp-meta-item">
                        <span class="exp-meta-label"><i class="fa fa-user-o mr-1"></i> Paid To / Payee</span>
                        <span class="exp-meta-value text-truncate" title="{{ $item->payee_name ?: '-' }}">{{ $item->payee_name ?: '-' }}</span>
                    </div>
                    <div class="exp-meta-item">
                        <span class="exp-meta-label"><i class="fa fa-credit-card mr-1"></i> Payment Mode</span>
                        <span class="exp-meta-value">{{ $item->payment_mode_name ?: 'Cash' }}</span>
                    </div>
                    @if(!empty($item->bill_no))
                        <div class="exp-meta-item">
                            <span class="exp-meta-label"><i class="fa fa-file-text-o mr-1"></i> Bill / Inv No.</span>
                            <span class="exp-meta-value">{{ $item->bill_no }}</span>
                        </div>
                    @endif
                    @if(!empty($item->payment_reference))
                        <div class="exp-meta-item">
                            <span class="exp-meta-label"><i class="fa fa-hashtag mr-1"></i> UTR / Ref</span>
                            <span class="exp-meta-value text-truncate">{{ $item->payment_reference }}</span>
                        </div>
                    @endif
                </div>

                {{-- Description Note if available --}}
                @if(!empty($item->description))
                    <div class="exp-note-preview">
                        <i class="fa fa-info-circle mr-1 text-muted"></i> {{ $item->description }}
                    </div>
                @endif
            </div>

            {{-- Card Actions: Print Voucher, Edit, Delete, Attachment --}}
            <div class="exp-card-actions">
                @if(!empty($item->invoice_no))
                    <a href="{{ url('expensePrint/' . $item->invoice_no) }}" target="_blank" class="exp-act-btn btn-print" title="Print Expense Receipt">
                        <i class="fa fa-print"></i> Receipt
                    </a>
                @endif

                @if($hasAttachment)
                    <a href="{{ $attachmentUrl }}" target="_blank" class="exp-act-btn btn-receipt" title="View Uploaded Bill / Attachment">
                        <i class="fa fa-paperclip"></i> Attachment
                    </a>
                @endif

                @if($canEdit && !empty($item->invoice_no))
                    <a href="{{ url('expenseEdit/' . $item->invoice_no) }}" class="exp-act-btn btn-edit" title="Edit Voucher">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                @endif

                @if($canDelete)
                    <button type="button" class="exp-act-btn btn-delete btn-exp-delete" data-id="{{ $item->id }}" data-voucher="{{ $item->invoice_no ?: '' }}" data-amount="₹{{ number_format($amountVal, 2) }}" title="Delete Expense">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            </div>
        </article>
    @endforeach
@else
    <div class="exp-empty-box">
        <i class="fa fa-calculator"></i>
        <h4>No Expense Vouchers Found</h4>
        <p>No expense entries match your current search or filters.</p>
    </div>
@endif
