@php
$getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();
$classType = Helper::classType();
$getState = Helper::getState();
$getcitie = Helper::getCity();
$getgenders = Helper::getgender();
$getCountry = Helper::getCountry();
$bloodGroupType = Helper::bloodGroupType();
$gender = DB::table('gender')->whereNull('deleted_at')->pluck('name')->implode(',');
$villageList = DB::table('custom_villages_list')->whereNull('deleted_at')->pluck('name')->implode(',');
$class = DB::table('class_types')->whereNull('deleted_at')->pluck('name')->implode(',');

$setting = DB::table('settings')->whereNull('deleted_at')->first();
$stateList = DB::table('states')->where('id', 13)->pluck('name')->implode(',');
$cityList = DB::table('citys')->whereNull('deleted_at')->where('state_id', 13)->take(25)->pluck('name')->implode(',');
$bloodgroupList = DB::table('blood_groups')->whereNull('deleted_at')->pluck('name')->implode(',');
$getSession = Helper::getSession();
$permission = Helper::permissioncheck(3);

$admissionStats = $admissionStats ?? [
    'total' => is_countable($data ?? []) ? count($data ?? []) : 0,
    'male' => 0,
    'female' => 0,
    'active' => 0,
    'inactive' => 0,
];
$bloodGroupLookup = $bloodGroupLookup ?? [];
$genderLookup = $genderLookup ?? [];
$feesAssignLookup = $feesAssignLookup ?? [];
$feesPaidLookup = $feesPaidLookup ?? [];
$dataTable = $dataTable ?? array_keys($getAdmissionDatatableFields);
$totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
$startIndex = $startIndex ?? 0;
$lastPage = $lastPage ?? 1;
$studentImages = collect($data ?? [])->filter(fn($item) => !empty($item['image']))->map(fn($item) => ['admissionNo' => $item['admissionNo'] ?? '', 'image' => $item['image']])->values();
@endphp
@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting */
.admission-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.admission-page * {
    box-sizing: border-box;
}
.admission-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner */
.admission-hero {
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
.admission-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.admission-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #fff;
}
.admission-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
}
.admission-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
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

/* Table Card & Header - Dark Navy Unified */
.admission-table-card {
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

/* Sticky Action Column */
.fixed_action_head {
    position: sticky !important;
    right: 0;
    z-index: 25 !important;
    background: #002C54 !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_filter {
    position: sticky !important;
    right: 0;
    z-index: 24 !important;
    background: #08335c !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_col {
    position: sticky !important;
    right: 0;
    z-index: 5;
    background: inherit;
    box-shadow: -3px 0 6px rgba(0,0,0,.08);
}
.dash-table tbody tr:nth-child(odd) .fixed_action_col {
    background: #f8fafc !important;
}
.dash-table tbody tr:nth-child(even) .fixed_action_col {
    background: #edf2f7 !important;
}
.dash-table tbody tr:hover .fixed_action_col {
    background: #e2e8f0 !important;
}

/* In-Column Excel Filters */
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
    padding: 5px 8px;
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
.admission-table-row--inactive td {
    background: #fff8e8 !important;
}
.admission-table-row--inactive td.fixed_action_col {
    background: #fff8e8 !important;
}

.student-name {
    font-weight: 600;
    color: #0284c7;
    white-space: nowrap;
    text-decoration: none !important;
}
.student-name:hover {
    color: #0369a1;
    text-decoration: underline !important;
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
.badge-admission-type {
    font-size: 10px;
    padding: 1px 5px;
    border-radius: 2px;
    white-space: nowrap;
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
.btn-action-view {
    background: #faf5ff;
    color: #7c3aed;
    border-color: #e9d5ff;
}
.btn-action-view:hover {
    background: #7c3aed;
    color: #fff;
}
.btn-action-id {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
}
.btn-action-id:hover {
    background: #16a34a;
    color: #fff;
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

/* Centered Empty State */
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

/* Table Loading State during AJAX */
.admission-table-loading {
    opacity: 0.55;
    pointer-events: none;
    transition: opacity 0.15s ease-in-out;
}
</style>
@endsection

@section('content')

<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">
                
                {{-- Top Header & Action Banner (Arise ERP Style) --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-users mr-1"></i> Students & Admissions</span>
                        <h1 class="admission-title">{{ __('Admission List') }}</h1>
                        <p class="admission-subtitle">Real-time Excel-style grid with cached partitioned queries, instant filters & pinned pagination</p>
                    </div>
                    <div class="admission-hero-actions">
                        @if($permission->add ?? true)
                            <a href="{{ url('admissionAdd') }}" class="dash-btn dash-btn-light">
                                <i class="fa fa-plus mr-1"></i> {{ __('common.Add') }} Admission
                            </a>
                        @endif
                        <button type="button" class="dash-btn dash-btn-outline" data-bs-toggle="modal" data-bs-target="#datatableFieldsModal" title="Column Visibility Settings">
                            <i class="fa fa-columns mr-1"></i> Columns
                        </button>
                        <a href="{{ url('admissionBulkEdit') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-edit mr-1"></i> Bulk Edit
                        </a>
                        <button type="button" class="dash-btn dash-btn-outline" data-bs-toggle="modal" data-bs-target="#BulkImages">
                            <i class="fa fa-picture-o mr-1"></i> Bulk Images
                        </button>
                        <button type="button" class="dash-btn dash-btn-outline" id="btn-push-notification" title="Send Push Notification">
                            <i class="fa fa-bell mr-1"></i> Push Notification
                        </button>
                        <button type="button" class="dash-btn dash-btn-outline" id="btn-whatsapp" title="Send WhatsApp Message">
                            <i class="fa fa-whatsapp mr-1"></i> WhatsApp
                        </button>
                    </div>
                </div>

                {{-- Full-Height Table Card with Excel-Like In-Column Filters & Pinned Pagination --}}
                <div class="dash-card admission-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Students Admission Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ isset($totalCount) ? $totalCount : (is_countable($data ?? []) ? count($data ?? []) : 0) }}</span> Total Admissions</span>
                        </div>
                    </div>
                    
                    {{-- Scrollable Table Body (Available Screen Height) --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="admission-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers (Arise Dark Navy ERP Style) --}}
                                <tr class="header-titles-row">
                                    <th style="width: 52px;" class="text-center">
                                        <input type="checkbox" id="select_all_students" title="Select All Students" style="cursor:pointer; vertical-align:middle; width:15px; height:15px;">
                                        <span class="ml-1">#</span>
                                    </th>
                                    
                                    @if(in_array('Biomax', $dataTable))
                                        <th style="min-width: 110px;">Unique ID</th>
                                    @endif

                                    @if($dataTable)
                                        @foreach($dataTable as $val)
                                            @if($val == 'Biomax')
                                                @continue
                                            @endif
                                            @if($val == 'Student Photo')
                                                <th style="width: 56px;" class="text-center">{{ __('Photo') }}</th>
                                            @elseif($val == 'Student Name')
                                                <th style="min-width: 140px;">{{ __('Student Name') }}</th>
                                            @elseif($val == 'Class')
                                                <th style="min-width: 95px;">{{ __('Class') }}</th>
                                            @elseif($val == 'Date Of .Birth')
                                                <th style="min-width: 100px;">{{ __('DOB') }}</th>
                                            @elseif($val == 'Date Of Admission')
                                                <th style="min-width: 155px;">{{ __('Ad. Date (From - To)') }}</th>
                                            @elseif($val == 'Fees Progress')
                                                <th style="min-width: 110px;" class="text-center">{{ __('Fees Progress') }}</th>
                                            @else
                                                <th style="min-width: 110px;">{{ __($val) }}</th>
                                            @endif
                                        @endforeach
                                    @endif

                                    <th style="width: 135px;" class="text-center fixed_action_head">{{ __('common.Action') }}</th>
                                </tr>

                                {{-- Row 2: Excel In-Column Filters (Dark Navy Palette - No White Glare) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-clear-filters" title="Reset All Filters">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>

                                    @if(in_array('Biomax', $dataTable))
                                        <th>
                                            <input type="text" class="excel-col-filter" id="filter-biomax" placeholder="Filter ID...">
                                        </th>
                                    @endif

                                    @if($dataTable)
                                        @foreach($dataTable as $val)
                                            @if($val == 'Biomax')
                                                @continue
                                            @endif

                                            @if($val == 'Student Photo')
                                                <th class="text-center">-</th>
                                            @elseif($val == 'Student Name')
                                                <th>
                                                    <input type="text" class="excel-col-filter" id="filter-name" placeholder="Filter Name...">
                                                </th>
                                            @elseif($val == "Father Name" || $val == "Father's Name")
                                                <th>
                                                    <input type="text" class="excel-col-filter" id="filter-father" placeholder="Filter Father...">
                                                </th>
                                            @elseif($val == "Mother Name" || $val == "Mother's Name")
                                                <th>
                                                    <input type="text" class="excel-col-filter" id="filter-mother" placeholder="Filter Mother...">
                                                </th>
                                            @elseif($val == 'Class')
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
                                            @elseif($val == 'Mobile' || $val == 'Mobile No.' || $val == 'Mobile No')
                                                <th>
                                                    <input type="text" class="excel-col-filter" id="filter-mobile" placeholder="Filter Mobile...">
                                                </th>
                                            @elseif($val == 'Gender')
                                                <th>
                                                    <select class="excel-col-filter" id="filter-gender">
                                                        <option value="">All</option>
                                                        @if(!empty($getgenders))
                                                            @foreach($getgenders as $g)
                                                                <option value="{{ strtolower($g->name ?? '') }}">{{ $g->name ?? '' }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </th>
                                            @elseif($val == 'Category')
                                                <th>
                                                    <select class="excel-col-filter" id="filter-category">
                                                        <option value="">All</option>
                                                        <option value="obc">OBC</option>
                                                        <option value="sc">SC</option>
                                                        <option value="st">ST</option>
                                                        <option value="bc">BC</option>
                                                        <option value="gen">GEN</option>
                                                        <option value="sbc">SBC</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </th>
                                            @elseif($val == 'Date Of Admission')
                                                <th>
                                                    <div class="excel-date-range-box">
                                                        <div class="excel-date-wrap" title="Admission From Date">
                                                            <span class="excel-date-lbl">From</span>
                                                            <input type="date" class="excel-date-input" id="filter-from-date" title="Select From Date">
                                                        </div>
                                                        <div class="excel-date-wrap" title="Admission To Date">
                                                            <span class="excel-date-lbl">To</span>
                                                            <input type="date" class="excel-date-input" id="filter-to-date" title="Select To Date">
                                                        </div>
                                                    </div>
                                                </th>
                                            @elseif($val == 'Status' || $val == 'Student Status')
                                                <th>
                                                    <select class="excel-col-filter" id="filter-status">
                                                        <option value="">All</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </th>
                                            @elseif($val == 'Fees Progress')
                                                <th class="text-center">-</th>
                                            @else
                                                @php $colSlug = Str::slug($val); @endphp
                                                <th>
                                                    <input type="text" class="excel-col-filter generic-col-filter" data-col="{{ $colSlug }}" placeholder="Filter...">
                                                </th>
                                            @endif
                                        @endforeach
                                    @endif

                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="admission-table-body">
                                @include('students.admission.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- Pinned Bottom Pagination Toolbar (Dark Navy Arise ERP Theme) --}}
                                        <div class="dash-card-footer table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                            <span id="filter-info" class="text-white-50 ml-1" style="display:none;">(filtered from <span id="grand-total">{{ $admissionStats['total'] ?? $totalCount }}</span> total)</span>
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows per page:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ ($perPage ?? 25) == 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">{{ $currentPage ?? 1 }}</span> of <span id="total-pages">{{ $lastPage ?? 1 }}</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page" {{ ($currentPage ?? 1) >= ($lastPage ?? 1) ? 'disabled' : '' }}><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page" {{ ($currentPage ?? 1) >= ($lastPage ?? 1) ? 'disabled' : '' }}><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- 1. Bulk Images Modal --}}
<div class="modal fade" id="BulkImages" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-picture-o mr-1"></i> Bulk Images Upload & Download</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form action="{{ url('studentBulkImageUpload') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-8">
                            <div class="form-group mb-0">
                                <label class="font-size-11 font-weight-bold mb-1">Choose Images <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="image[]" multiple required>
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-success btn-sm w-100" title="Images Upload">
                                <i class="fa fa-upload mr-1"></i> Upload
                            </button>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-info btn-sm w-100" id="downloadZip" title="Bulk Images Download [Zip]">
                                <i class="fa fa-arrow-circle-down mr-1"></i> Zip
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-1 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Edit Student AJAX Modal --}}
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-edit mr-1"></i> Admission Edit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" id="editStudentContent">
                <div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted font-size-12">Loading student details...</p></div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Push Notification Modal --}}
<div class="modal fade" id="pushNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-bell mr-1"></i> Send Push Notification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="pushNotificationForm">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="push-title" class="form-label font-size-11 font-weight-bold mb-1">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="push-title" name="title" placeholder="Enter Notification Title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="push-message" class="form-label font-size-11 font-weight-bold mb-1">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" id="push-message" name="message" rows="4" placeholder="Enter Notification Message" required></textarea>
                    </div>
                    
                    <div class="alert alert-info py-2 px-3 mb-3 font-size-11">
                        <strong>Selected Students:</strong> <span id="push-selected-count">0</span>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-send-push">Send Notification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 4. Single Image Upload/Rotate/Delete Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <form id="imageForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="admission_id" id="admission_id">
            <input type="hidden" name="action_type" id="action_type" value="upload">

            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title font-size-14"><i class="fa fa-image mr-1"></i> Student Photo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center p-3">
                    <img id="previewImgModal" src="" style="max-width:140px; max-height:140px; border-radius:2px; border:1px solid #cbd5e1;">
                    <input type="file" name="student_img" id="student_img" class="form-control form-control-sm mt-2">
                </div>

                <div class="modal-footer py-1 bg-light d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-danger btn-sm" id="deleteBtn" title="Delete Photo"><i class="fa fa-trash"></i></button>
                        <button type="button" class="btn btn-primary btn-sm" id="rotateBtn" title="Rotate Photo"><i class="fa fa-repeat"></i></button>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- 5. Datatable Fields Modal (Column Preferences) --}}
<div id="datatableFieldsModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="master_checkbox" style="width:16px; height:16px; cursor:pointer;">
                    <label for="master_checkbox" class="mb-0 text-white font-weight-bold font-size-13 cursor-pointer">Select All Columns</label>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form action="{{ url('saveAdmissionDatatableFields') }}" method="post">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3 col-sm-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input checkbox" id="field_biomax" name="fields[]" {{ in_array('Biomax', $dataTable) ? 'checked' : '' }} value="Biomax">
                                <label class="custom-control-label font-size-12" for="field_biomax">Attendance Unique ID</label>
                            </div>
                        </div>
                        @if(!empty($getAdmissionDatatableFields))
                            @foreach($getAdmissionDatatableFields as $key => $dataFields)
                                @if($key != 'SR.NO')
                                    <div class="col-md-3 col-sm-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input checkbox" id="field_{{ $dataFields ?? '' }}" name="fields[]" {{ in_array($key, $dataTable) ? 'checked' : ''}} value="{{ $key ?? '' }}">
                                            <label class="custom-control-label font-size-12" for="field_{{ $dataFields ?? '' }}">{{ $key ?? '' }}</label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="col-md-12 text-center mt-3">
                            <button class="btn btn-primary btn-sm px-4" type="submit"><i class="fa fa-save mr-1"></i> Save Column Preferences</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-1 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- 6. Compose WhatsApp Message Modal --}}
<div class="modal fade" id="myLargeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-whatsapp mr-1"></i> Compose WhatsApp Message</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <span class="font-size-11 font-weight-bold text-muted mr-1">Quick Templates:</span>
                        <button class="btn btn-primary btn-xs message-button" data-message="Hello {#name#},&#10;&#10;This is Administrator from {#school_name#}. I hope this message finds you well.&#10;&#10;We are pleased to provide you with your credentials to access our school platform:&#10;&#10;APK: https://demo3.rusoft.in/schoolimage/default/GreenGarden.apk&#10;Username: {#user_name#}&#10;Password: {#password#}&#10;Please ensure you keep these credentials secure. If you have questions, contact us at {#school_mobile#}.&#10;&#10;Best regards,&#10;Administrator&#10;{#school_name#}">Credentials</button>
                        <button class="btn btn-info btn-xs message-button" data-message="Hello {#name#},&#10;&#10;This is Administrator from {#school_name#}. Please be informed of our latest school announcements.&#10;&#10;If you have any questions, contact us at {#school_mobile#}.&#10;&#10;Best regards,&#10;Administrator&#10;{#school_name#}">General Notice</button>
                        <button class="btn btn-success btn-xs message-button" data-message="Hello {#name#},&#10;&#10;Warm festive greetings from {#school_name#}! Wishing you and your family joy and prosperity.&#10;&#10;Best regards,&#10;Administrator&#10;{#school_name#}">Festive Wishes</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group mb-2">
                            <label for="message-text" class="font-size-11 font-weight-bold mb-1">Message:</label>
                            <textarea class="form-control font-size-12" id="message-text" rows="7" required placeholder="Type your WhatsApp message..."></textarea>
                        </div> 
                        <div class="form-group mb-0">
                            <div class="d-flex align-items-center gap-2">
                                <img id="img_preview" style="border:1px solid #cbd5e1; border-radius:2px; object-fit:cover;" src="https://demo3.rusoft.in/schoolimage/default/6605525.jpg" width="50px" height="50px"/>
                                <div class="flex-grow-1">
                                    <label for="attachment-file" class="font-size-11 font-weight-bold mb-1">Attachment:</label>
                                    <input type="file" class="form-control form-control-sm" id="attachment-file" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <span class="text-danger font-size-11 d-block mb-1">Note: Students without a mobile number will be skipped.</span>
                        <div style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0;">
                            <table class="table table-sm table-striped table-bordered mb-0 font-size-11">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th>Adm. No</th>
                                        <th>Name</th>
                                        <th>F. Name</th>
                                        <th>Mobile</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="secondary_tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <hr class="my-2 border-muted">
                        <h6 class="font-size-12 font-weight-bold mb-1">Today's Sent Messages</h6>
                        <div style="max-height: 120px; overflow-y: auto; border: 1px solid #e2e8f0;">
                            <table class="table table-sm table-striped table-bordered mb-0 font-size-11">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th>Message Id</th>
                                        <th>Message</th>
                                        <th>Attachment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="third_tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <div>
                    <span style="display:none" class="previousIds font-size-11"><input type="checkbox" id="previousIds"> Do Not Use Previous Id's</span>
                </div>
                <div>
                    <button type="button" class="btn btn-info btn-sm" id="reset_modal">Reset</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="close_modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" id="sendButton"><i class="fa fa-paper-plane mr-1"></i> Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 7. Normal Delete Modal --}}
<div class="modal fade" id="Modal_id" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border: 1px solid #cbd5e1;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-trash mr-1"></i> Delete Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('admissionDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="delete_id_normal" name="delete_id">
                    <div class="delete-icon-box mb-2">
                        <i class="fa fa-exclamation-triangle text-danger" style="font-size:28px;"></i>
                    </div>
                    <h6 class="font-weight-bold text-dark mb-1">Are you sure you want to delete?</h6>
                    <p class="text-muted font-size-11 mb-0">This student record will be permanently deleted.</p>
                </div>
                <div class="modal-footer py-1 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 8. Warning Delete Modal (If Fees/Marks Data Exists) --}}
<div class="modal fade" id="Modal_Warning_id" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-exclamation-triangle mr-1"></i> Warning: Data Exists!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('admissionDelete') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="delete_id_warning" name="delete_id">
                    <div class="alert alert-warning py-2 px-3 font-size-12 mb-2">
                        <strong>Caution:</strong> This student's Fees or Marks data is already entered.
                    </div>
                    <p class="font-size-12 text-dark mb-0">Are you sure you want to delete this student and all related fees, invoices, and exam marks?</p>
                </div>
                <div class="modal-footer py-1 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Yes, Delete All Data</button>
                </div>
            </form>
        </div>
    </div>
</div>



{{-- Real-Time Server-Side Filter & Pagination Engine --}}
<script>
$(document).ready(function() {
    let currentPage = {{ $currentPage ?? 1 }};
    let lastPage = {{ $lastPage ?? 1 }};
    let currentPerPage = "{{ $perPage ?? 25 }}";
    let isFetching = false;
    let filterTimer = null;
    const admissionViewAjaxUrl = "{{ url('admissionView') }}";

    function getFilterParams(pageOverride) {
        const page = pageOverride !== undefined ? pageOverride : currentPage;
        const params = {
            ajax: 1,
            page: page,
            per_page: $('#rows-per-page-select').val() || currentPerPage,
            name: ($('#filter-name').val() || '').trim(),
            father_name: ($('#filter-father').val() || '').trim(),
            mother_name: ($('#filter-mother').val() || '').trim(),
            class_type_id: $('#filter-class').val() || '',
            mobile: ($('#filter-mobile').val() || '').trim(),
            gender_id: $('#filter-gender').val() || '',
            category: $('#filter-category').val() || '',
            biomax_id: ($('#filter-biomax').val() || '').trim(),
            from_date: $('#filter-from-date').val() || '',
            to_date: $('#filter-to-date').val() || '',
            status: $('#filter-status').val() !== undefined ? $('#filter-status').val() : '',
        };

        // Generic text filters if present
        $('.generic-col-filter').each(function() {
            const col = $(this).data('col');
            const val = ($(this).val() || '').trim();
            if (col && val) {
                params[col] = val;
            }
        });

        return params;
    }

    function fetchAdmissions(page) {
        if (isFetching) return;
        isFetching = true;
        $('#admission-grid-table').addClass('admission-table-loading');

        const params = getFilterParams(page);

        $.ajax({
            url: admissionViewAjaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.status === 200 || res.status === 1 || res.html !== undefined)) {
                    $('#admission-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    $('#page-start').text(res.from);
                    $('#page-end').text(res.to);
                    $('#total-records').text(res.total);
                    $('#header-records-count').text(res.total);
                    $('#current-page').text(res.current_page);
                    $('#total-pages').text(res.last_page);

                    if (res.stats && res.stats.total !== undefined) {
                        if (res.total < res.stats.total) {
                            $('#grand-total').text(res.stats.total);
                            $('#filter-info').show();
                        } else {
                            $('#filter-info').hide();
                        }
                    }

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage || lastPage <= 1);

                    // Reset selection master checkbox
                    $('#select_all_students').prop('checked', false).prop('indeterminate', false);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch admissions:', xhr);
            },
            complete: function() {
                isFetching = false;
                $('#admission-grid-table').removeClass('admission-table-loading');
            }
        });
    }

    // In-column debounce for real-time text filter typing (350ms)
    $('#filter-name, #filter-father, #filter-mother, #filter-mobile, #filter-biomax, .generic-col-filter').on('input', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchAdmissions(1);
        }, 350);
    });

    // Select & Date inputs change instantly
    $('#filter-class, #filter-gender, #filter-category, #filter-status, #filter-from-date, #filter-to-date').on('change', function() {
        currentPage = 1;
        fetchAdmissions(1);
    });

    // Rows per page dropdown
    $('#rows-per-page-select').on('change', function() {
        currentPage = 1;
        currentPerPage = $(this).val();
        fetchAdmissions(1);
    });

    // Pagination navigation buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            fetchAdmissions(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchAdmissions(currentPage);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            currentPage++;
            fetchAdmissions(currentPage);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            currentPage = lastPage;
            fetchAdmissions(currentPage);
        }
    });

    // Reset Filters button
    $('.btn-clear-filters, .btn-reset-filters').on('click', function() {
        $('.excel-col-filter').val('');
        $('#filter-status').val('');
        $('#filter-from-date').val('');
        $('#filter-to-date').val('');
        currentPage = 1;
        fetchAdmissions(1);
    });

    // Select all checkboxes sync
    function updateStudentSelectionControls() {
        var visibleCheckboxes = $('#admission-table-body .checkbox_id');
        var totalVisible = visibleCheckboxes.length;
        var checkedVisible = visibleCheckboxes.filter(':checked').length;
        var master = $('#select_all_students');

        master.prop('checked', totalVisible > 0 && checkedVisible === totalVisible);
        master.prop('indeterminate', checkedVisible > 0 && checkedVisible < totalVisible);
    }

    $('#select_all_students').on('change', function() {
        $('#admission-table-body .checkbox_id').prop('checked', this.checked);
        updateStudentSelectionControls();
    });

    $(document).on('change', '.checkbox_id', updateStudentSelectionControls);
});
</script>

{{-- Delete Data via AJAX check --}}
<script>
$(document).on('click', '.deleteData', function() {
    var delete_id = $(this).data('id'); 

    $.ajax({
        url: "{{ url('checkStudentData') }}",
        type: "GET",
        data: { id: delete_id },
        success: function(response) {
            if(response.has_data) {
                $('#delete_id_warning').val(delete_id); 
                $('#Modal_Warning_id').modal('show');
            } else {
                $('#delete_id_normal').val(delete_id); 
                $('#Modal_id').modal('show');
            }
        },
        error: function() {
            alert("Something went wrong while checking data!");
        }
    });
});
</script>

{{-- Quick Edit AJAX Modal --}}
<script>
const admissionBaseUrl = @json(url('/'));

$(document).on("click", ".openEditModal", function () {
    var id = $(this).data("id");
    $("#editStudentContent").html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted font-size-12">Loading form...</p></div>');
    $("#editStudentModal").modal("show");

    $.ajax({
        url: admissionBaseUrl + "/admissionEdit/" + id,
        type: "GET",
        success: function(data) {
            $("#editStudentContent").html(data);
        },
        error: function() {
            $("#editStudentContent").html('<div class="alert alert-danger">Failed to load student details.</div>');
        }
    });
});
</script>

{{-- Image Upload / Rotate / Delete Modal --}}
<script>
let currentImgTag = null;
let currentImgId = '';
let rotationAngle = 0;
const csrf_token = '{{ csrf_token() }}';
const save_url = '{{ url("imageRotateSave") }}';

$(document).on('click', '.profileImg', function(){
    currentImgTag = this;
    currentImgId = $(this).data('id');
    rotationAngle = 0;

    const src = $(this).attr('src') || '';
    $('#previewImgModal').attr('src', src);
    $('#admission_id').val(currentImgId);
    $('#action_type').val('upload');
    
    var modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
});

$('#student_img').on('change', function(){
    const file = this.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = e => $('#previewImgModal').attr('src', e.target.result);
    reader.readAsDataURL(file);
});

function sendImageRequest(action){
    let formData = new FormData();
    formData.append('_token', csrf_token);
    formData.append('admission_id', currentImgId);
    formData.append('action_type', action);

    if(action === 'upload'){
        let file = $('#student_img')[0].files[0];
        if(!file){ alert('Select image first'); return; }
        formData.append('student_img', file);
    }

    $.ajax({
        url: save_url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res){
            if(res.success){
                const newUrl = res.image_url + '?t=' + new Date().getTime();
                $(currentImgTag).attr('src', newUrl);
                $('#previewImgModal').attr('src', newUrl);

                if(action === 'delete'){
                    $('#student_img').val('');
                }

                var modalEl = document.getElementById('imageModal');
                bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                toastr.success(res.message || 'Success!');
            } else {
                alert(res.message || 'Action failed!');
            }
        },
        error: function(){
            alert('Something went wrong!');
        }
    });
}

$('#imageForm').on('submit', function(e){
    e.preventDefault();
    sendImageRequest('upload');
});

$('#deleteBtn').click(function(){ sendImageRequest('delete'); });

$('#rotateBtn').click(function(){
    const imgEl = document.getElementById('previewImgModal');
    if(!imgEl || !imgEl.src) return;

    rotationAngle += 90;
    rotationAngle %= 360;

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.src = imgEl.src;

    img.onload = function(){
        if(rotationAngle % 180 === 0){
            canvas.width = img.width;
            canvas.height = img.height;
        } else {
            canvas.width = img.height;
            canvas.height = img.width;
        }

        ctx.translate(canvas.width/2, canvas.height/2);
        ctx.rotate(rotationAngle * Math.PI / 180);
        ctx.drawImage(img, -img.width/2, -img.height/2);

        const dataUrl = canvas.toDataURL('image/jpeg');
        $('#previewImgModal').attr('src', dataUrl);
        $(currentImgTag).attr('src', dataUrl);

        canvas.toBlob(function(blob){
            let formData = new FormData();
            formData.append('_token', csrf_token);
            formData.append('admission_id', currentImgId);
            formData.append('action_type', 'upload');
            formData.append('student_img', blob, 'rotated.jpg');

            $.ajax({
                url: save_url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res){
                    if(res.success){
                        var newUrl = res.image_url + '?t=' + new Date().getTime();
                        $('#previewImgModal').attr('src', newUrl);
                        $(currentImgTag).attr('src', newUrl);
                    }
                }
            });
        }, 'image/jpeg', 0.9);
    };
});
</script>

{{-- Push Notification Submission --}}
<script>
$(document).ready(function() {
    $(document).on('click', '#btn-push-notification', function() {
        var selectedCount = $('.checkbox_id:checked').length;
        if (selectedCount > 0) {
            $('#push-selected-count').text(selectedCount);
            $('#push-title').val('');
            $('#push-message').val('');
            $('#pushNotificationModal').modal('show');
        } else {
            toastr.error('Please Select Students');
        }
    });

    $('#pushNotificationForm').on('submit', function(e) {
        e.preventDefault();
        var admissionIds = [];
        $('.checkbox_id:checked').each(function() {
            admissionIds.push($(this).val());
        });

        if (admissionIds.length === 0) {
            toastr.error('No students selected');
            return;
        }

        var title = $('#push-title').val();
        var message = $('#push-message').val();
        var btn = $('#btn-send-push');

        btn.prop('disabled', true).text('Sending...');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            url: "{{ url('sendStudentPushNotification') }}",
            method: 'POST',
            data: {
                admission_ids: admissionIds,
                title: title,
                message: message
            },
            success: function(response) {
                btn.prop('disabled', false).text('Send Notification');
                if (response.status) {
                    toastr.success(response.message);
                    $('#pushNotificationModal').modal('hide');
                    $('.checkbox_id').prop('checked', false);
                    $('#select_all_students').prop('checked', false);
                } else {
                    toastr.error(response.message || 'Something went wrong');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Send Notification');
                var errorMsg = 'Failed to send notification';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            }
        });
    });
});
</script>

{{-- WhatsApp Compose Script --}}
<script>
function generateRandom8DigitNumber() {
    let min = 10000000; 
    let max = 99999999;
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

var messageId = '';
var attachment2 = '';
var id = [];
var i = 0;
var previousIds = []; 
var notUsePreviousIds = false;
var sending = true;

function todayWhatsappMessages(){
    $('#third_tbody').html('');
    var baseUrl = "{{ url('/') }}";
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
        url: baseUrl +'/todayWhatsappMessages',
        method: 'POST',
        success: function(response) {
            if(response.status){ 
                response.data.forEach(function(item) {
                    var attachmentContent = item.attachment ? `<a target="_blank" href="${item.attachment}"><img src="${item.attachment}" width="30px" height="30px"/></a>` : "";
                    var newRow = `<tr>
                        <td class='messageId'>${item.message_id}</td>
                        <td class='old_message'>${item.message}</td>
                        <td class='attachment'>${attachmentContent}</td>
                        <td><a class="useMe" data-ids="${response.ids[item.message_id]}" style="cursor:pointer; text-decoration: underline; color:blue">Use Me</a></td>
                    </tr>`;
                    $('#third_tbody').append(newRow);
                }); 
            }
        }
    });
}

$(document).on('click', '.message-button', function() {
    $('#message-text').val($(this).data('message'));
});

$(document).on('click', '#btn-whatsapp', function() {
    var length = $('.checkbox_id:checked').length;
    if(length > 0){
        messageId = generateRandom8DigitNumber();
        id = [];
        previousIds = [];
        notUsePreviousIds = false;
        sending = true;

        $('#previousIds').prop('checked', false);
        $('.previousIds').hide();
        todayWhatsappMessages();
        $('#message-text').val(''); 
        $('#myLargeModal').modal('show');
        $('#secondary_tbody').html('');

        $(".checkbox_id:checked").each(function() {
            var admission_no = $(this).data('admission_no');
            var name = $(this).data('name');
            var mobile = $(this).data('mobile');
            var f_name = $(this).data('father_name');
            var status = mobile ? 'Pending' : 'Mobile Missing';
            var ids = $(this).val();
            
            if(mobile) {
                id.push({id: ids, mobile: mobile});
            }
            
            var newRow = `<tr>
                <td>${admission_no}</td>
                <td>${name}</td>
                <td>${f_name}</td>
                <td>${mobile}</td>
                <td class='status_action' id="status_${ids}">${status}</td> 
            </tr>`;
            $('#secondary_tbody').append(newRow);
        });
    } else {
        toastr.error('Please Select Students');
    }
});

$('#sendButton').click(function() {
    i = 0;
    var arr = id;
    for(i = 0; i < arr.length; i++) {
        if(sending) {
            sendPostRequest(arr[i]);
        }
    }
});

function sendPostRequest(data) {
    var baseUrl = "{{ url('/') }}";
    var message = $('#message-text').val(); 
    var fileInput = $('#attachment-file')[0];
    var file = fileInput.files[0];

    var formData = new FormData();
    formData.append('message_id', messageId);
    formData.append('id', data.id);
    formData.append('message', message);
    formData.append('modal', 'Admission');
    formData.append('mobile', data.mobile);
    formData.append('attachment2', attachment2);
    if (file) formData.append('image', file);

    $.ajax({
        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
        url: baseUrl + '/sendWhatsapp',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status) {
                $('#status_' + response.id).addClass('text-success').text('Sent');
            } else {
                $('#status_' + response.id).addClass('text-danger').text(response.message);
            }
        }
    });
}

$('#attachment-file').on('change', function(event) {
    var file = event.target.files[0];
    if (file && file.type.match('image.*')) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#img_preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
});

// Master checkbox in column settings modal
$('#master_checkbox').click(function(){
    $('.checkbox').prop('checked', $(this).is(':checked'));
});
</script>

{{-- JSZip and FileSaver for Bulk Images Download --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
$(document).ready(function(){
    $('#downloadZip').on('click', function(){
        let btn = $(this);
        btn.prop('disabled', true).text('Generating...');

        var data = @json($studentImages);
        var urls = [];
        var admissionNo = [];

        data.forEach(function(item) {
            if(item.image && item.image.trim() !== '') {
                urls.push(`{{ env('IMAGE_SHOW_PATH').'profile/' }}${item.image}`);
                admissionNo.push(item.admissionNo);
            }
        });

        if(urls.length === 0){
            alert("No images found!");
            btn.prop('disabled', false).html('<i class="fa fa-arrow-circle-down mr-1"></i> Zip');
            return;
        }

        var zip = new JSZip();
        var count = 0;
        var zipFilename = "{{ $setting->name ?? 'Students' }}.zip";

        urls.forEach(function(url, i){
            var filename = admissionNo[i] + ".jpg";

            fetch(url, {mode: 'cors'})
            .then(response => {
                if (!response.ok) throw new Error("HTTP error " + response.status);
                return response.blob();
            })
            .then(blob => {
                zip.file(filename, blob, {binary: true});
                count++;

                if(count === urls.length){
                    zip.generateAsync({type:'blob'}).then(function(content) {
                        saveAs(content, zipFilename);
                        btn.prop('disabled', false).html('<i class="fa fa-arrow-circle-down mr-1"></i> Zip');
                    });
                }
            })
            .catch(function() {
                count++;
                if(count === urls.length){
                    zip.generateAsync({type:'blob'}).then(function(content) {
                        saveAs(content, zipFilename);
                        btn.prop('disabled', false).html('<i class="fa fa-arrow-circle-down mr-1"></i> Zip');
                    });
                }
            });
        });
    });
});
</script>

@endsection
