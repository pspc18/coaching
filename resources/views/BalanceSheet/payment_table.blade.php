<div class="card balance-table-card">
    <div class="dash-card-header">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h3 class="dash-card-title mb-0">
                <i class="fa fa-table mr-1 text-info"></i> {{ $title }}
            </h3>
            <span class="badge badge-info brand-status-badge">
                <span class="table-total-count">{{ count($records ?? []) }}</span> Records
            </span>
            @if(isset($modeTotal))
                <span class="badge badge-success brand-status-badge">
                    Total: ₹ {{ number_format($modeTotal, 2) }}
                </span>
            @endif
        </div>
    </div>

    <div class="table-scroll-container">
        <table id="{{ $tableId }}" class="dash-table balance-data-table">
            <thead>
                {{-- Row 1: Header Titles (Dark Navy #002C54) --}}
                <tr class="header-titles-row">
                    <th class="text-center" style="width: 45px;">#</th>
                    <th style="width: 110px;">Adm No.</th>
                    <th>Student &amp; Parent Details</th>
                    <th style="width: 90px;">Class</th>
                    <th style="width: 130px;">Receipt / Inv</th>
                    <th class="text-center" style="width: 100px;">Date</th>
                    <th style="width: 140px;">Payment Mode / Ref</th>
                    <th class="text-right" style="width: 100px;">Discount</th>
                    <th class="text-right" style="width: 120px;">Paid Amount</th>
                </tr>

                {{-- Row 2: In-Column Excel Filters (#08335c) --}}
                <tr class="excel-filter-row">
                    <th class="text-center">
                        <i class="fa fa-filter text-info" style="font-size: 10px;"></i>
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="adm" placeholder="Adm No...">
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="student" placeholder="Search name/father...">
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="class" placeholder="Class...">
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="receipt" placeholder="Receipt/Inv...">
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="date" placeholder="DD-MM-YYYY...">
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="ref" placeholder="Mode / Txn / Chq...">
                    </th>
                    <th>
                        <input type="text" class="excel-col-filter" data-col="discount" placeholder="Discount...">
                    </th>
                    <th class="text-right">
                        <button type="button" class="btn-reset-filters" onclick="resetTableFilters('{{ $tableId }}')" title="Reset Filters">
                            <i class="fa fa-refresh mr-1"></i> Clear
                        </button>
                    </th>
                </tr>
            </thead>

            <tbody>
                @php
                    $grandDiscount = 0;
                    $grandAmount = 0;
                @endphp

                @forelse($records as $key => $item)
                    @php
                        $discount = $item->discount ?? $item->discount_amount ?? 0;
                        $amount = $item->amount ?? $item->paid_amount ?? 0;

                        $grandDiscount += $discount;
                        $grandAmount += $amount;

                        $dateDisplay = '-';
                        if (!empty($item->payment_date)) {
                            $dateDisplay = date('d-M-Y', strtotime($item->payment_date));
                        } elseif (!empty($item->created_at)) {
                            $dateDisplay = date('d-M-Y', strtotime($item->created_at));
                        }

                        $studentFullName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                        $fatherName = $item->father_name ?? '';
                    @endphp

                    <tr class="table-data-row" 
                        data-adm="{{ strtolower($item->admissionNo ?? '') }}"
                        data-student="{{ strtolower($studentFullName . ' ' . $fatherName) }}"
                        data-class="{{ strtolower($item->class_name ?? '') }}"
                        data-receipt="{{ strtolower(($item->offline_receipt_no ?? '') . ' ' . ($item->invoice_no ?? '')) }}"
                        data-date="{{ strtolower($dateDisplay) }}"
                        data-ref="{{ strtolower(($item->payment_mode_name ?? '') . ' ' . ($item->cheque_number ?? '') . ' ' . ($item->transaction_id ?? '') . ' ' . ($item->bank_name ?? '')) }}"
                        data-discount="{{ $discount }}"
                        data-amount="{{ $amount }}">
                        
                        <td class="text-center text-muted font-weight-bold row-index">{{ $key + 1 }}</td>

                        <td>
                            <span class="badge badge-light border text-dark" style="font-size: 10.5px; padding: 2px 6px;">
                                {{ $item->admissionNo ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <div class="student-name font-weight-bold text-dark" style="font-size: 11.5px;">
                                {{ $studentFullName }}
                            </div>
                            @if(!empty($fatherName))
                                <div class="text-muted" style="font-size: 10px; line-height: 1.2;">
                                    <i class="fa fa-user-o mr-1"></i>S/D/O: {{ $fatherName }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-secondary" style="font-size: 10px; font-weight: 600;">
                                {{ $item->class_name ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <div class="text-dark font-weight-bold" style="font-size: 11px;">
                                {{ $item->offline_receipt_no ?? $item->invoice_no ?? '-' }}
                            </div>
                            @if(!empty($item->offline_receipt_no) && !empty($item->invoice_no))
                                <div class="text-muted" style="font-size: 9.5px;">
                                    Inv: {{ $item->invoice_no }}
                                </div>
                            @endif
                        </td>

                        <td class="text-center">
                            <span class="text-dark" style="font-size: 11px; white-space: nowrap;">
                                {{ $dateDisplay }}
                            </span>
                        </td>

                        <td>
                            <span class="badge badge-info" style="font-size: 9.5px;">
                                {{ $item->payment_mode_name ?? 'Online' }}
                            </span>
                            @if(!empty($item->cheque_number))
                                <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;">
                                    Chq: {{ $item->cheque_number }}
                                    @if(!empty($item->cheque_date))
                                        ({{ date('d/m/y', strtotime($item->cheque_date)) }})
                                    @endif
                                </div>
                            @elseif(!empty($item->transaction_id))
                                <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;" title="{{ $item->transaction_id }}">
                                    Txn: {{ Str::limit($item->transaction_id, 14) }}
                                </div>
                            @elseif(!empty($item->bank_name))
                                <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;">
                                    Bank: {{ Str::limit($item->bank_name, 14) }}
                                </div>
                            @endif
                        </td>

                        <td class="text-right">
                            @if($discount > 0)
                                <span class="text-danger font-weight-bold" style="font-size: 11px;">
                                    ₹ {{ number_format($discount, 2) }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 11px;">-</span>
                            @endif
                        </td>

                        <td class="text-right">
                            <span class="text-success font-weight-bold" style="font-size: 11.5px;">
                                ₹ {{ number_format($amount, 2) }}
                            </span>
                        </td>
                    </tr>

                @empty
                    <tr class="empty-row">
                        <td colspan="9" class="p-0 border-0">
                            <div class="empty-data-wrapper">
                                <div class="empty-data-icon">
                                    <i class="fa fa-folder-open-o"></i>
                                </div>
                                <div class="empty-data-title">No Payment Records Found</div>
                                <div class="empty-data-subtitle">No fee collection transactions recorded for the selected period or filters.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if(count($records ?? []) > 0)
            <tfoot>
                <tr class="balance-table-total-row">
                    <td colspan="7" class="text-right font-weight-bold" style="font-size: 11.5px;">
                        Filtered Total (<span class="footer-count">{{ count($records) }}</span> Records):
                    </td>
                    <td class="text-right text-danger font-weight-bold footer-discount" style="font-size: 11.5px;">
                        ₹ {{ number_format($grandDiscount, 2) }}
                    </td>
                    <td class="text-right text-success font-weight-bold footer-amount" style="font-size: 12px;">
                        ₹ {{ number_format($grandAmount, 2) }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- Bottom Pinned Pagination Bar --}}
    <div class="table-pagination-bar" data-table="#{{ $tableId }}">
        <div class="pagination-info">
            Showing <span class="page-start">1</span> to <span class="page-end">{{ min(25, count($records ?? [])) }}</span> of <span class="page-total">{{ count($records ?? []) }}</span> entries
        </div>

        <div class="pagination-controls">
            <div class="rows-per-page-selector">
                <label>Rows:</label>
                <select class="rows-select" onchange="changeTablePageSize('{{ $tableId }}', this.value)">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                </select>
            </div>

            <div class="pagination-nav">
                <button type="button" class="page-btn btn-first" onclick="navigateTablePage('{{ $tableId }}', 'first')" title="First Page">&laquo;</button>
                <button type="button" class="page-btn btn-prev" onclick="navigateTablePage('{{ $tableId }}', 'prev')" title="Previous Page">&lsaquo;</button>
                <span class="page-indicator">Page <b class="current-page-num">1</b> of <b class="total-pages-num">1</b></span>
                <button type="button" class="page-btn btn-next" onclick="navigateTablePage('{{ $tableId }}', 'next')" title="Next Page">&rsaquo;</button>
                <button type="button" class="page-btn btn-last" onclick="navigateTablePage('{{ $tableId }}', 'last')" title="Last Page">&raquo;</button>
            </div>
        </div>
    </div>
</div>