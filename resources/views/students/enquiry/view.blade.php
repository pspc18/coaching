@php
  $classType = Helper::classType();
  $getEnquiryStatus = Helper::getEnquiryStatus();
  $permission = Helper::permissioncheck(28);
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper enquiry-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="enquiry-page-layout">
                
                {{-- Top Header & Action Banner (Sharp & Compact ERP Style) --}}
                <div class="enquiry-hero">
                    <div class="enquiry-hero-text">
                        <span class="enquiry-kicker"><i class="fa fa-address-book-o mr-1"></i> Admissions & Reception</span>
                        <h1 class="enquiry-title">{{ __('student.View Students Enquiry') }}</h1>
                        <p class="enquiry-subtitle">Excel-style grid with real-time column filters, sorting & pagination</p>
                    </div>
                    <div class="enquiry-hero-actions">
                        @if($permission->add ?? true)
                            <a href="{{ url('enquiryAdd') }}" class="dash-btn dash-btn-light">
                                <i class="fa fa-plus mr-1"></i> {{ __('common.Add') }} Enquiry
                            </a>
                        @endif
                        <form action="{{ url('enquiryView') }}" method="post" class="d-inline m-0">
                            @csrf
                            <button type="submit" name="pdf" value="pdf" class="dash-btn dash-btn-pdf" title="Export PDF">
                                <i class="fa fa-file-pdf-o mr-1"></i> {{ __('common.Pdf') }}
                            </button>
                        </form>
                        <a href="{{ url('reception_file') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-arrow-left mr-1"></i> {{ __('common.Back') }}
                        </a>
                    </div>
                </div>

                {{-- Full-Height Table Card with Excel-Like In-Column Filters & Pinned Pagination --}}
                <div class="dash-card enquiry-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Student Enquiries Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ count($data ?? []) }}</span> Total Enquiries</span>
                        </div>
                    </div>
                    
                    {{-- Scrollable Table Body (Available Screen Height) --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="enquiry-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers (Proper ERP Padding & Alignment) --}}
                                <tr class="header-titles-row">
                                    <th style="width: 42px;" class="text-center">#</th>
                                    <th style="min-width: 135px;">{{ __('common.Name') }}</th>
                                    <th style="min-width: 105px;">{{ __('common.Mobile No.') }}</th>
                                    <th style="min-width: 115px;">{{ __('common.F Name') }}</th>
                                    <th style="min-width: 115px;">{{ __('common.M Name') }}</th>
                                    <th style="min-width: 100px;">{{ __('Class') }}</th>
                                    <th style="min-width: 155px;">{{ __('Enq. Date (From - To)') }}</th>
                                    <th style="min-width: 130px;">{{ __('Next Follow Up') }}</th>
                                    <th style="min-width: 95px;" class="text-center">{{ __('Ad. Status') }}</th>
                                    <th style="min-width: 95px;" class="text-center">{{ __('Status') }}</th>
                                    <th style="width: 105px;" class="text-center">{{ __('common.Action') }}</th>
                                </tr>
                                {{-- Row 2: Excel In-Column Filters with Date Range Pickers (Dark Navy Theme - No White) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-clear-filters" title="Reset All Filters">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Filter Name...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-mobile" placeholder="Filter Mobile...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-father" placeholder="Filter Father...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-mother" placeholder="Filter Mother...">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-class">
                                            <option value="">All Classes</option>
                                            @if(!empty($classType))
                                                @foreach($classType as $type)
                                                    <option value="{{ strtolower($type->name ?? '') }}">{{ $type->name ?? '' }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>
                                    <th>
                                        <div class="excel-date-range-box">
                                            <div class="excel-date-wrap" title="Enquiry From Date">
                                                <span class="excel-date-lbl">From</span>
                                                <input type="date" class="excel-date-input" id="filter-from-date" title="Select From Date">
                                            </div>
                                            <div class="excel-date-wrap" title="Enquiry To Date">
                                                <span class="excel-date-lbl">To</span>
                                                <input type="date" class="excel-date-input" id="filter-to-date" title="Select To Date">
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="excel-date-wrap" title="Select Follow Up Date">
                                            <span class="excel-date-lbl">Date</span>
                                            <input type="date" class="excel-date-input" id="filter-followup-date" title="Select Follow Up Date">
                                        </div>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-ad-status">
                                            <option value="">All</option>
                                            <option value="admission">Admission</option>
                                            <option value="-">Non-Admission</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-status">
                                            <option value="">All</option>
                                            @if(!empty($getEnquiryStatus))
                                                @foreach($getEnquiryStatus as $type)
                                                    <option value="{{ strtolower($type->name ?? '') }}">{{ $type->name ?? '' }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>
                                    <th class="text-center">
                                        <button type="button" class="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="enquiry-table-body">
                                @if(!empty($data) && count($data) > 0)
                                    @php $i = 1; @endphp
                                    @foreach ($data as $item)
                                        @php
                                            $latestStatus = $item->latest_status ?? 'New';
                                            $statusClass = match($latestStatus) {
                                                'Active' => 'status-badge-active',
                                                'Partially Closed' => 'status-badge-partially',
                                                'Missed' => 'status-badge-missed',
                                                'Closed' => 'status-badge-closed',
                                                default => 'status-badge-default'
                                            };
                                            $regDate = !empty($item['registration_date']) ? date('d-M-Y', strtotime($item['registration_date'])) : '-';
                                            $regRaw = !empty($item['registration_date']) ? date('Y-m-d', strtotime($item['registration_date'])) : '';
                                            $followupDate = !empty($item->latest_followup_date) ? date('d-M-Y', strtotime($item->latest_followup_date)) : '-';
                                            $followupRaw = !empty($item->latest_followup_date) ? date('Y-m-d', strtotime($item->latest_followup_date)) : '';
                                            $adStatus = ($item->ad_status ?? '') == 'Admission' ? 'Admission' : '-';
                                        @endphp
                                        <tr class="enquiry-row"
                                            data-name="{{ strtolower(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? '')) }}"
                                            data-mobile="{{ strtolower($item['mobile'] ?? '') }}"
                                            data-father="{{ strtolower($item['father_name'] ?? '') }}"
                                            data-mother="{{ strtolower($item['mother_name'] ?? '') }}"
                                            data-class="{{ strtolower($item['class_name'] ?? '') }}"
                                            data-enq-date="{{ $regRaw }}"
                                            data-followup-date="{{ $followupRaw }}"
                                            data-ad-status="{{ strtolower($adStatus) }}"
                                            data-status="{{ strtolower($latestStatus) }}">
                                            <td class="text-center font-weight-bold text-muted row-sr-no">{{ $i++ }}</td>
                                            <td>
                                                <span class="student-name">{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</span>
                                            </td>
                                            <td>
                                                <a href="tel:{{ $item['mobile'] ?? '' }}" class="student-mobile text-decoration-none">
                                                    <i class="fa fa-phone text-muted mr-1"></i>{{ $item['mobile'] ?? '-' }}
                                                </a>
                                            </td>
                                            <td>{{ $item['father_name'] ?? '-' }}</td>
                                            <td>{{ $item['mother_name'] ?? '-' }}</td>
                                            <td>
                                                <span class="badge-class">{{ $item['class_name'] ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <span class="date-text font-weight-bold">{{ $regDate }}</span>
                                            </td>
                                            <td>
                                                @if($followupDate !== '-')
                                                    <span class="date-text text-primary"><i class="fa fa-calendar-check-o mr-1"></i>{{ $followupDate }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($adStatus === 'Admission')
                                                    <span class="badge-admission"><i class="fa fa-check mr-1"></i>Admission</span>
                                                @else
                                                    <span class="text-muted font-italic">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge {{ $statusClass }}">{{ $latestStatus }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="table-actions">
                                                    @if($permission->print ?? true)
                                                        <a href="{{ url('registrationPrint', $item->id) }}" target="_blank" class="table-btn btn-action-print" title="Registration Print">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    @if($permission->edit ?? true)
                                                        <a href="{{ url('enquiryEdit', $item->id) }}" class="table-btn btn-action-edit" title="Edit Enquiry">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    @if($permission->delete ?? true)
                                                        <button type="button" data-id="{{ $item->id }}" class="table-btn btn-action-delete deleteData" title="Delete Enquiry">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($permission->view ?? true)
                                                        <a href="{{ url('studentRegistrationDetail', $item->id) }}" class="table-btn btn-action-view" title="View Enquiry Details">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                
                                {{-- Empty State Row (Shown when 0 records or filters match 0) --}}
                                <tr id="empty-state-row" style="{{ empty($data) || count($data) === 0 ? '' : 'display:none;' }}">
                                    <td colspan="11" class="p-0">
                                        <div class="dash-empty-state">
                                            <div class="empty-icon"><i class="fa fa-address-book-o"></i></div>
                                            <div class="empty-title">No Enquiries Found</div>
                                            <div class="empty-desc">There are no student enquiries matching your current search criteria or branch filters.</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pinned Bottom Pagination Toolbar (Dark Navy ERP Theme) --}}
                    <div class="dash-card-footer table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">0</span> to <span id="page-end" class="font-weight-bold text-white">0</span> of <span id="total-records" class="font-weight-bold text-white">0</span> entries
                            <span id="filter-info" class="text-white-50 ml-1" style="display:none;">(filtered from <span id="grand-total">0</span> total)</span>
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows per page:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page"><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page"><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page"><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

{{-- Sharp Clean Delete Confirmation Modal --}}
<div class="custom-modal" id="Modal_id" style="display: none;">
    <div class="custom-modal-backdrop"></div>
    <div class="custom-modal-dialog">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h4 class="custom-modal-title"><i class="fa fa-trash-o mr-1"></i> {{ __('common.Delete Confirmation') }}</h4>
                <button type="button" class="custom-modal-close close-modal">&times;</button>
            </div>
            <form action="{{ url('enquiryDelete') }}" method="post">
                @csrf
                <input type="hidden" id="delete_id" name="delete_id">
                <div class="custom-modal-body text-center p-4">
                    <div class="delete-icon-box mb-2">
                        <i class="fa fa-exclamation-triangle text-danger"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ __('common.Are you sure you want to delete') }}?</h5>
                    <p class="text-muted font-size-12 mb-0">This enquiry record will be permanently deleted from the system.</p>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn-modal btn-modal-secondary close-modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn-modal btn-modal-danger">{{ __('common.Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Page Layout & Viewport Fitting */
.enquiry-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12.5px;
}
.enquiry-page * {
    box-sizing: border-box;
}
.enquiry-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner */
.enquiry-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.enquiry-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.enquiry-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #fff;
}
.enquiry-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
}
.enquiry-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    line-height: 1.4;
}
.dash-btn-light {
    background: #fff;
    color: #002C54;
    border-color: #fff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.dash-btn-pdf {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.dash-btn-pdf:hover {
    background: #dc2626;
    color: #fff;
}
.dash-btn-outline {
    background: transparent;
    color: #fff;
    border-color: rgba(255,255,255,.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #fff;
    border-color: #fff;
}

/* Table Card & Header - Dark Navy Unified (No Glaring White) */
.enquiry-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin-bottom: 0;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}
.badge-excel-mode {
    font-size: 9.5px;
    background: rgba(16,185,129,.15);
    color: #34d399;
    padding: 2px 6px;
    border-radius: 2px;
    border: 1px solid rgba(16,185,129,.3);
    font-weight: 600;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 3px 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Table Design */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Proper thead Titles Padding & Alignment */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 9px 8px;
    height: 38px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
    box-sizing: border-box;
}

/* Sticky Filter Row with Proper Padding & Dark Navy Theme (No White) */
.excel-filter-row th {
    position: sticky;
    top: 38px;
    background: #08335c;
    color: #ffffff;
    padding: 5px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
    box-sizing: border-box;
}

/* In-Column Excel Filters (Dark Navy Palette - No White on Focus) */
.excel-col-filter {
    width: 100%;
    height: 27px;
    padding: 3px 7px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    color-scheme: dark;
}
.excel-col-filter::placeholder {
    color: rgba(255,255,255,.6);
}
.excel-col-filter:focus {
    background: #031426 !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
}
select.excel-col-filter {
    background-color: #051e38 !important;
    color: #ffffff !important;
    cursor: pointer;
}
select.excel-col-filter option {
    background-color: #002C54 !important;
    color: #ffffff !important;
}

/* Date Range Selection Elements */
.excel-date-range-box {
    display: flex;
    flex-direction: column;
    gap: 3px;
    width: 100%;
}
.excel-date-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 5px;
    height: 24px;
    transition: all .15s;
}
.excel-date-wrap:focus-within {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
    background: #031426 !important;
}
.excel-date-lbl {
    font-size: 9.5px;
    font-weight: 700;
    color: rgba(255,255,255,.75);
    text-transform: uppercase;
    min-width: 28px;
    user-select: none;
    letter-spacing: .02em;
}
.excel-date-input {
    flex: 1;
    min-width: 0;
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 10.5px;
    padding: 0;
    height: 100%;
    outline: none;
    color-scheme: dark;
}
.excel-date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: .85;
    padding: 0;
    margin-right: 1px;
}
.excel-date-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Clear & Reset Buttons */
.btn-clear-filters {
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
    margin: auto;
    transition: all .15s;
}
.btn-clear-filters:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}
.btn-reset-filters {
    height: 27px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
    width: 100%;
    transition: all .15s;
}
.btn-reset-filters:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

/* Table Body Rows - Soft Slate Palette (No Glaring White) */
.dash-table tbody td {
    padding: 6px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
}
.dash-table tbody tr:nth-child(odd) td {
    background: #f8fafc;
}
.dash-table tbody tr:nth-child(even) td {
    background: #edf2f7;
}
.dash-table tbody tr:hover td {
    background: #e2e8f0 !important;
}

.student-name {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
}
.student-mobile {
    font-size: 11px;
    color: #0284c7;
    white-space: nowrap;
}
.badge-class {
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    white-space: nowrap;
}
.date-text {
    font-size: 10.5px;
    white-space: nowrap;
}
.badge-admission {
    background: #ecfdf5;
    color: #059669;
    padding: 1px 5px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid #a7f3d0;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

/* Status Badges */
.status-badge {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
    line-height: 1.2;
    text-transform: uppercase;
    white-space: nowrap;
}
.status-badge-active {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.status-badge-partially {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fef3c7;
}
.status-badge-missed {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.status-badge-closed {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.status-badge-default {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
}

/* Action Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.table-btn {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 10.5px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s;
    line-height: 1;
}
.btn-action-print {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
.btn-action-print:hover {
    background: #059669;
    color: #fff;
}
.btn-action-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-action-edit:hover {
    background: #2563eb;
    color: #fff;
}
.btn-action-delete {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #fff;
}
.btn-action-view {
    background: #faf5ff;
    color: #7c3aed;
    border-color: #e9d5ff;
}
.btn-action-view:hover {
    background: #7c3aed;
    color: #fff;
}

/* Bottom Pagination Toolbar - Dark Navy */
.table-pagination-bar {
    background: #002342;
    color: #ffffff;
    border-top: 1px solid rgba(255,255,255,.12);
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    min-height: 34px;
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
    font-weight: 500;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 4px;
}
.rows-per-page-selector label {
    margin: 0;
    font-size: 11px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 2px 5px;
    font-size: 11px;
    outline: none;
    cursor: pointer;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 26px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 12px;
    transition: all .15s;
}
.page-btn:hover:not(:disabled) {
    background: #0284c7;
    border-color: #0284c7;
}
.page-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}
.page-current-indicator {
    font-size: 11px;
    font-weight: 600;
    padding: 0 6px;
    color: #f1f5f9;
}

/* Clean Empty State (Centered in Full Available Table Height) */
#empty-state-row td {
    padding: 0 !important;
    border: none !important;
    background: #eef2f6 !important;
    vertical-align: middle !important;
}
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height) - 195px);
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 44px;
    color: #94a3b8;
    margin-bottom: 12px;
    line-height: 1;
}
.dash-empty-state .empty-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.dash-empty-state .empty-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 380px;
    line-height: 1.5;
}

/* Custom Modal */
.custom-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}
.custom-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15,23,42,.6);
    z-index: 1;
}
.custom-modal-dialog {
    position: relative;
    z-index: 2;
    width: 92%;
    max-width: 380px;
    margin: auto;
}
.custom-modal-content {
    background: #fff;
    border-radius: 2px;
    box-shadow: 0 10px 25px rgba(0,0,0,.2);
    overflow: hidden;
    border: 1px solid #cbd5e1;
}
.custom-modal-header {
    background: #002C54;
    color: #fff;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.custom-modal-title {
    font-size: 12.5px;
    font-weight: 600;
    margin: 0;
    color: #fff;
}
.custom-modal-close {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    opacity: .8;
    padding: 0;
}
.custom-modal-close:hover {
    opacity: 1;
}
.custom-modal-footer {
    padding: 6px 10px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 6px;
}
.delete-icon-box i {
    font-size: 28px;
}
.btn-modal {
    padding: 3px 10px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
}
.btn-modal-secondary {
    background: #e2e8f0;
    color: #475569;
}
.btn-modal-secondary:hover {
    background: #cbd5e1;
    color: #1e293b;
}
.btn-modal-danger {
    background: #dc2626;
    color: #fff;
}
.btn-modal-danger:hover {
    background: #b91c1c;
    color: #fff;
}

@media(max-width:768px) {
    .table-pagination-bar {
        flex-direction: column;
        gap: 6px;
        height: auto;
        padding: 6px 8px;
    }
    .pagination-controls {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<script>
$(document).ready(function() {
    var $rows = $('.enquiry-row');
    var totalRecords = $rows.length;
    var currentPage = 1;
    var rowsPerPage = 10;
    var filteredRows = $rows.toArray();

    function applyFilters() {
        var nameVal = ($('#filter-name').val() || '').toLowerCase().trim();
        var mobileVal = ($('#filter-mobile').val() || '').toLowerCase().trim();
        var fatherVal = ($('#filter-father').val() || '').toLowerCase().trim();
        var motherVal = ($('#filter-mother').val() || '').toLowerCase().trim();
        var classVal = ($('#filter-class').val() || '').toLowerCase().trim();
        var fromDate = $('#filter-from-date').val() || '';
        var toDate = $('#filter-to-date').val() || '';
        var followupDate = $('#filter-followup-date').val() || '';
        var adStatusVal = ($('#filter-ad-status').val() || '').toLowerCase().trim();
        var statusVal = ($('#filter-status').val() || '').toLowerCase().trim();

        filteredRows = [];
        $rows.each(function() {
            var $row = $(this);
            var match = true;

            if (nameVal && ($row.data('name') || '').indexOf(nameVal) === -1) match = false;
            if (match && mobileVal && ($row.data('mobile') || '').indexOf(mobileVal) === -1) match = false;
            if (match && fatherVal && ($row.data('father') || '').indexOf(fatherVal) === -1) match = false;
            if (match && motherVal && ($row.data('mother') || '').indexOf(motherVal) === -1) match = false;
            if (match && classVal && ($row.data('class') || '') !== classVal) match = false;
            if (match && adStatusVal && ($row.data('ad-status') || '') !== adStatusVal) match = false;
            if (match && statusVal && ($row.data('status') || '') !== statusVal) match = false;

            // Precise Date Range Filter for Enquiry Registration Date
            var enqDate = $row.data('enq-date') || '';
            if (match && fromDate) {
                if (!enqDate || enqDate < fromDate) match = false;
            }
            if (match && toDate) {
                if (!enqDate || enqDate > toDate) match = false;
            }

            // Follow-up Date Filter
            var followDate = $row.data('followup-date') || '';
            if (match && followupDate) {
                if (!followDate || followDate !== followupDate) match = false;
            }

            if (match) {
                filteredRows.push(this);
            }
        });

        currentPage = 1;
        renderPagination();
    }

    function renderPagination() {
        var count = filteredRows.length;
        var rpp = (rowsPerPage === 'all') ? count : parseInt(rowsPerPage, 10);
        var totalPages = count > 0 ? Math.ceil(count / rpp) : 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        var startIdx = (currentPage - 1) * rpp;
        var endIdx = (rowsPerPage === 'all') ? count : Math.min(startIdx + rpp, count);

        // Hide all rows
        $rows.hide();

        if (count === 0) {
            $('#empty-state-row').show();
            $('#page-start').text(0);
            $('#page-end').text(0);
            $('#total-records').text(0);
        } else {
            $('#empty-state-row').hide();
            for (var i = startIdx; i < endIdx; i++) {
                $(filteredRows[i]).show();
            }
            $('#page-start').text(startIdx + 1);
            $('#page-end').text(endIdx);
            $('#total-records').text(count);
        }

        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);
        $('#header-records-count').text(count);

        if (count < totalRecords) {
            $('#filter-info').show();
            $('#grand-total').text(totalRecords);
        } else {
            $('#filter-info').hide();
        }

        // Button state
        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages);
    }

    // Debounced typing for text filters & instant change for dates/selects
    var debounceTimer;
    $('.excel-col-filter').on('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 150);
    });

    $('.excel-col-filter, .excel-date-input').on('change', function() {
        applyFilters();
    });

    // Reset all filters including date range pickers
    $('.btn-reset-filters, .btn-clear-filters').on('click', function() {
        $('.excel-col-filter, .excel-date-input').val('');
        applyFilters();
    });

    // Rows per page change
    $('#rows-per-page-select').on('change', function() {
        rowsPerPage = $(this).val();
        currentPage = 1;
        renderPagination();
    });

    // Pagination controls
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            renderPagination();
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderPagination();
        }
    });

    $('#btn-next').on('click', function() {
        var count = filteredRows.length;
        var rpp = (rowsPerPage === 'all') ? count : parseInt(rowsPerPage, 10);
        var totalPages = count > 0 ? Math.ceil(count / rpp) : 1;
        if (currentPage < totalPages) {
            currentPage++;
            renderPagination();
        }
    });

    $('#btn-last').on('click', function() {
        var count = filteredRows.length;
        var rpp = (rowsPerPage === 'all') ? count : parseInt(rowsPerPage, 10);
        var totalPages = count > 0 ? Math.ceil(count / rpp) : 1;
        if (currentPage < totalPages) {
            currentPage = totalPages;
            renderPagination();
        }
    });

    // Initial render
    renderPagination();

    // Modal handlers
    $('.deleteData').on('click', function(e) {
        e.preventDefault();
        var deleteId = $(this).data('id');
        $('#delete_id').val(deleteId);
        $('#Modal_id').fadeIn(150);
    });

    $('.close-modal, .custom-modal-backdrop').on('click', function() {
        $('#Modal_id').fadeOut(150);
    });
});
</script>

@endsection
