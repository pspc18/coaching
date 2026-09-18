@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exactly Matching admissionView */
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
    height: calc(100vh - var(--header-height, 60px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner - Exact Arise ERP Style from admissionView */
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

/* thead Titles Row */
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

/* Sticky Filter Row with Dark Navy Theme */
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

.btn-clear-filters {
    width: 26px;
    height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    color: #ffffff;
    cursor: pointer;
    font-size: 11px;
    transition: all .15s;
}
.btn-clear-filters:hover {
    background: #031426;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Table Body */
.dash-table tbody td {
    padding: 6px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    font-size: 11.5px;
    color: #1e293b;
}
.dash-table tbody tr:nth-child(odd) {
    background: #f8fafc;
}
.dash-table tbody tr:nth-child(even) {
    background: #edf2f7;
}
.dash-table tbody tr:hover {
    background: #e2e8f0;
}

/* Action Buttons */
.btn-action-icon {
    width: 24px;
    height: 24px;
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
    min-height: 260px;
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

/* Table Loading State during AJAX */
.table-loading-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 44, 84, 0.45);
    backdrop-filter: blur(1px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}
.loading-badge {
    background: #001f3d;
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgba(255,255,255,.2);
    box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <div class="container-fluid p-2">
        <div class="admission-page-layout">

            <!-- Hero Banner -->
            <div class="admission-hero">
                <div>
                    <span class="admission-kicker"><i class="fa fa-graduation-cap mr-1"></i> ARIS MASTER MANAGEMENT</span>
                    <h1 class="admission-title">Class & Grade Master</h1>
                    <p class="admission-subtitle">Manage school class levels, sort orders, and academic sections</p>
                </div>
                <div class="admission-hero-actions">
                    <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#addClassModal">
                        <i class="fa fa-plus-circle mr-1 text-primary"></i> Add New Class
                    </button>
                    <button type="button" class="dash-btn dash-btn-outline" data-toggle="modal" data-target="#quickBulkClassModal">
                        <i class="fa fa-bolt mr-1 text-warning"></i> Quick Bulk Import
                    </button>
                </div>
            </div>

            <!-- Table Card -->
            <div class="dash-card admission-table-card">
                <div class="dash-card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <i class="fa fa-list-alt text-light" style="font-size: 13px;"></i>
                        <h2 class="dash-card-title">Academic Classes & Grades Directory</h2>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="badge-total-records" id="badgeTotalRecords">
                            Total Records: <strong id="totalRecordsCount">{{ $pagination['total_records'] ?? count($data) }}</strong>
                        </span>
                    </div>
                </div>

                <div class="table-scroll-container">
                    <div id="tableLoadingOverlay" class="table-loading-overlay" style="display: none;">
                        <div class="loading-badge">
                            <i class="fa fa-spinner fa-spin text-info"></i> Updating classes list...
                        </div>
                    </div>

                    <table class="dash-table" id="classesTable">
                        <thead>
                            <tr class="header-titles-row">
                                <th style="width: 65px;" class="text-center">#ID</th>
                                <th style="min-width: 180px;">Class Name</th>
                                <th style="width: 120px;" class="text-center">Sort Order</th>
                                <th style="width: 140px;" class="text-center">Enrolled Students</th>
                                <th style="width: 130px;" class="text-center">Fees Heads</th>
                                <th style="width: 140px;" class="text-center">Assigned Subjects</th>
                                <th style="width: 90px;" class="text-center fixed_action_head">Action</th>
                            </tr>
                            <tr class="excel-filter-row">
                                <th>
                                    <input type="text" class="excel-col-filter" id="col_filter_id" placeholder="ID..." value="{{ $searchId ?? '' }}" autocomplete="off">
                                </th>
                                <th>
                                    <input type="text" class="excel-col-filter" id="col_filter_name" placeholder="Filter class name..." value="{{ $searchName ?? '' }}" autocomplete="off">
                                </th>
                                <th>
                                    <input type="text" class="excel-col-filter" id="col_filter_order" placeholder="Sort order..." value="{{ $searchOrder ?? '' }}" autocomplete="off">
                                </th>
                                <th class="text-center text-muted" style="font-size:10px;">—</th>
                                <th class="text-center text-muted" style="font-size:10px;">—</th>
                                <th class="text-center text-muted" style="font-size:10px;">—</th>
                                <th class="text-center fixed_action_filter">
                                    <button type="button" class="btn-clear-filters" id="btn_clear_all_filters" title="Clear all filters">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="classes_tbody">
                            @include('master.class.class_rows', [
                                'rows' => $rows,
                                'admissionsCounts' => $admissionsCounts,
                                'feesCounts' => $feesCounts,
                                'subjectCounts' => $subjectCounts,
                            ])
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Pagination Bar -->
                <div class="table-pagination-bar">
                    <div class="pagination-info" id="paginationInfo">
                        Showing <strong id="infoFrom">{{ $pagination['from'] ?? 0 }}</strong> to <strong id="infoTo">{{ $pagination['to'] ?? 0 }}</strong> of <strong id="infoTotal">{{ $pagination['total_records'] ?? 0 }}</strong> entries
                    </div>
                    <div class="pagination-controls">
                        <div class="rows-per-page-selector">
                            <label for="perPageSelect">Rows:</label>
                            <select id="perPageSelect">
                                <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                <option value="-1" {{ ($perPage ?? 25) == -1 ? 'selected' : '' }}>All</option>
                            </select>
                        </div>
                        <div class="pagination-nav">
                            <button type="button" class="page-btn" id="btnFirstPage" title="First Page" {{ ($pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <i class="fa fa-angle-double-left"></i>
                            </button>
                            <button type="button" class="page-btn" id="btnPrevPage" title="Previous Page" {{ ($pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <i class="fa fa-angle-left"></i>
                            </button>
                            <span class="page-current-indicator">
                                Page <span id="currentPageNum">{{ $pagination['current_page'] ?? 1 }}</span> of <span id="totalPagesNum">{{ $pagination['total_pages'] ?? 1 }}</span>
                            </span>
                            <button type="button" class="page-btn" id="btnNextPage" title="Next Page" {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
                                <i class="fa fa-angle-right"></i>
                            </button>
                            <button type="button" class="page-btn" id="btnLastPage" title="Last Page" {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
                                <i class="fa fa-angle-double-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Class -->
<div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #002C54; box-shadow: 0 10px 25px rgba(0,0,0,.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #fff; padding: 10px 14px;">
                <h5 class="modal-title" id="addClassModalLabel" style="font-size: 13px; font-weight: 700;">
                    <i class="fa fa-plus-circle mr-1"></i> Add New Class
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: .9; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addClassForm" action="{{ url('add_class') }}" method="post">
                @csrf
                <div class="modal-body" style="padding: 14px; font-size: 12px; background: #fff;">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark mb-1" style="font-size: 11.5px;">Class / Grade Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. 1st, 2nd, Nursery, 10th" required autocomplete="off">
                        <small class="text-muted mt-1 d-block">Sort order will be calculated automatically.</small>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 8px 14px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 6px;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size: 11px; padding: 4px 10px;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitAddClass" style="font-size: 11px; padding: 4px 12px; background: #002C54; border-color: #002C54;">
                        <i class="fa fa-save mr-1"></i> Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Class -->
<div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-labelledby="editClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #002C54; box-shadow: 0 10px 25px rgba(0,0,0,.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #fff; padding: 10px 14px;">
                <h5 class="modal-title" id="editClassModalLabel" style="font-size: 13px; font-weight: 700;">
                    <i class="fa fa-edit mr-1"></i> Edit Class
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: .9; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editClassForm" method="post">
                @csrf
                <input type="hidden" id="edit_class_id" name="id" value="">
                <div class="modal-body" style="padding: 14px; font-size: 12px; background: #fff;">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark mb-1" style="font-size: 11.5px;">Class / Grade Name <span class="text-danger">*</span></label>
                        <input type="text" id="edit_class_name" name="name" class="form-control form-control-sm" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer" style="padding: 8px 14px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 6px;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size: 11px; padding: 4px 10px;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitEditClass" style="font-size: 11px; padding: 4px 12px; background: #002C54; border-color: #002C54;">
                        <i class="fa fa-check mr-1"></i> Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Quick Bulk Import Classes -->
<div class="modal fade" id="quickBulkClassModal" tabindex="-1" role="dialog" aria-labelledby="quickBulkClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #002C54; box-shadow: 0 10px 25px rgba(0,0,0,.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #fff; padding: 10px 14px;">
                <h5 class="modal-title" id="quickBulkClassModalLabel" style="font-size: 13px; font-weight: 700;">
                    <i class="fa fa-bolt text-warning mr-1"></i> Quick Bulk Import Standard Classes
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: .9; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('save_selected_classes') }}" method="post">
                @csrf
                <div class="modal-body" style="padding: 16px; font-size: 12px; background: #fff;">
                    <p class="text-muted mb-3 font-italic">Select the standard school classes to create automatically for the current academic session:</p>
                    @php
                        $presetClasses = [
                            1 => 'Play Group',
                            2 => 'Nursery',
                            3 => 'LKG',
                            4 => 'UKG',
                            5 => '1st',
                            6 => '2nd',
                            7 => '3rd',
                            8 => '4th',
                            9 => '5th',
                            10 => '6th',
                            11 => '7th',
                            12 => '8th',
                            13 => '9th',
                            14 => '10th',
                            15 => '11th',
                            16 => '12th',
                        ];
                    @endphp
                    <div class="row">
                        @foreach($presetClasses as $cId => $cName)
                            <div class="col-6 col-md-3 mb-2">
                                <div class="custom-control custom-checkbox p-2 border rounded" style="background:#f8fafc;">
                                    <input type="checkbox" class="custom-control-input" id="class_chk_{{ $cId }}" name="class_id[]" value="{{ $cId }}">
                                    <input type="hidden" name="class[{{ $cId }}]" value="{{ $cName }}">
                                    <label class="custom-control-label font-weight-bold text-dark" for="class_chk_{{ $cId }}" style="font-size: 12px; cursor: pointer;">
                                        {{ $cName }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer" style="padding: 8px 14px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btnSelectAllBulkClasses">Select All</button>
                    <div style="display:flex; gap:6px;">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size: 11px; padding: 4px 10px;">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary" style="font-size: 11px; padding: 4px 12px; background: #002C54; border-color: #002C54;">
                            <i class="fa fa-download mr-1"></i> Import Selected Classes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = {{ (int)($pagination['current_page'] ?? 1) }};
    let totalPages = {{ (int)($pagination['total_pages'] ?? 1) }};
    let debounceTimeout = null;

    const overlay = document.getElementById('tableLoadingOverlay');
    const tbody = document.getElementById('classes_tbody');
    const filterId = document.getElementById('col_filter_id');
    const filterName = document.getElementById('col_filter_name');
    const filterOrder = document.getElementById('col_filter_order');
    const btnClearAll = document.getElementById('btn_clear_all_filters');
    const perPageSelect = document.getElementById('perPageSelect');

    const btnFirst = document.getElementById('btnFirstPage');
    const btnPrev = document.getElementById('btnPrevPage');
    const btnNext = document.getElementById('btnNextPage');
    const btnLast = document.getElementById('btnLastPage');
    const curPageSpan = document.getElementById('currentPageNum');
    const totalPagesSpan = document.getElementById('totalPagesNum');
    const infoFrom = document.getElementById('infoFrom');
    const infoTo = document.getElementById('infoTo');
    const infoTotal = document.getElementById('infoTotal');
    const totalRecordsCount = document.getElementById('totalRecordsCount');

    function updatePaginationControls(meta) {
        currentPage = parseInt(meta.current_page) || 1;
        totalPages = parseInt(meta.total_pages) || 1;

        if (curPageSpan) curPageSpan.textContent = currentPage;
        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
        if (infoFrom) infoFrom.textContent = meta.from || 0;
        if (infoTo) infoTo.textContent = meta.to || 0;
        if (infoTotal) infoTotal.textContent = meta.total_records || 0;
        if (totalRecordsCount) totalRecordsCount.textContent = meta.total_records || 0;

        const isFirst = currentPage <= 1;
        const isLast = currentPage >= totalPages;

        if (btnFirst) btnFirst.disabled = isFirst;
        if (btnPrev) btnPrev.disabled = isFirst;
        if (btnNext) btnNext.disabled = isLast;
        if (btnLast) btnLast.disabled = isLast;
    }

    function fetchClasses(targetPage = 1) {
        if (overlay) overlay.style.display = 'flex';

        const params = new URLSearchParams({
            page: targetPage,
            per_page: perPageSelect ? perPageSelect.value : 25,
            search_id: filterId ? filterId.value.trim() : '',
            search_name: filterName ? filterName.value.trim() : '',
            search_order: filterOrder ? filterOrder.value.trim() : '',
        });

        fetch(`{{ url('add_class') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (overlay) overlay.style.display = 'none';
            if (data.status === 'success') {
                if (tbody) tbody.innerHTML = data.html;
                if (data.pagination) {
                    updatePaginationControls(data.pagination);
                }
            }
        })
        .catch(err => {
            if (overlay) overlay.style.display = 'none';
            console.error('Error loading classes data:', err);
        });
    }

    function triggerDebouncedFetch() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchClasses(1);
        }, 250);
    }

    [filterId, filterName, filterOrder].forEach(input => {
        if (input) {
            input.addEventListener('input', triggerDebouncedFetch);
        }
    });

    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => fetchClasses(1));
    }

    if (btnClearAll) {
        btnClearAll.addEventListener('click', function () {
            if (filterId) filterId.value = '';
            if (filterName) filterName.value = '';
            if (filterOrder) filterOrder.value = '';
            fetchClasses(1);
        });
    }

    // Pagination Nav Click Handlers
    if (btnFirst) {
        btnFirst.addEventListener('click', () => {
            if (currentPage > 1) fetchClasses(1);
        });
    }
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) fetchClasses(currentPage - 1);
        });
    }
    if (btnNext) {
        btnNext.addEventListener('click', () => {
            if (currentPage < totalPages) fetchClasses(currentPage + 1);
        });
    }
    if (btnLast) {
        btnLast.addEventListener('click', () => {
            if (currentPage < totalPages) fetchClasses(totalPages);
        });
    }

    // Bulk Select All Toggle
    const btnSelectAll = document.getElementById('btnSelectAllBulkClasses');
    if (btnSelectAll) {
        let allSelected = false;
        btnSelectAll.addEventListener('click', function () {
            allSelected = !allSelected;
            document.querySelectorAll('#quickBulkClassModal input[type="checkbox"]').forEach(chk => {
                chk.checked = allSelected;
            });
            btnSelectAll.textContent = allSelected ? 'Deselect All' : 'Select All';
        });
    }

    // Modal Edit Trigger
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.js-edit-class');
        if (editBtn) {
            const id = editBtn.dataset.id;
            const name = editBtn.dataset.name;

            document.getElementById('edit_class_id').value = id;
            document.getElementById('edit_class_name').value = name;
            document.getElementById('editClassForm').action = `{{ url('class_edit') }}/${id}`;
        }
    });

    // Add Class Form AJAX Submit
    const addClassForm = document.getElementById('addClassForm');
    if (addClassForm) {
        addClassForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnSubmitAddClass');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
            }

            const formData = new FormData(addClassForm);
            fetch(addClassForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-save mr-1"></i> Save Class';
                }
                if (data.status === 'success') {
                    $('#addClassModal').modal('hide');
                    addClassForm.reset();
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Class added successfully.');
                    } else {
                        alert(data.message || 'Class added successfully.');
                    }
                    fetchClasses(1);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to save class.');
                    } else {
                        alert(data.message || 'Failed to save class.');
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-save mr-1"></i> Save Class';
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred. Please try again.');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    }

    // Edit Class Form AJAX Submit
    const editClassForm = document.getElementById('editClassForm');
    if (editClassForm) {
        editClassForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnSubmitEditClass');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
            }

            const formData = new FormData(editClassForm);
            fetch(editClassForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-check mr-1"></i> Update Class';
                }
                if (data.status === 'success') {
                    $('#editClassModal').modal('hide');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Class updated successfully.');
                    } else {
                        alert(data.message || 'Class updated successfully.');
                    }
                    fetchClasses(currentPage);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to update class.');
                    } else {
                        alert(data.message || 'Failed to update class.');
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-check mr-1"></i> Update Class';
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred. Please try again.');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    }

    // Delete Class AJAX Form handling
    document.addEventListener('submit', function(e) {
        const delForm = e.target.closest('.js-delete-class-form');
        if (delForm) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this class?')) return;

            const formData = new FormData(delForm);
            fetch(delForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Class deleted successfully.');
                    } else {
                        alert(data.message || 'Class deleted successfully.');
                    }
                    fetchClasses(currentPage);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to delete class.');
                    } else {
                        alert(data.message || 'Failed to delete class.');
                    }
                }
            })
            .catch(err => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred while deleting.');
                } else {
                    alert('An error occurred while deleting.');
                }
            });
        }
    });
});
</script>
@endsection