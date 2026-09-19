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
    justify-content: center;
    height: 28px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    line-height: 1;
    gap: 5px;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
    border-color: #f1f5f9;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255,255,255,0.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.15);
    color: #ffffff;
    border-color: #ffffff;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.dash-btn-primary:hover {
    background: #001f3d;
    color: #ffffff;
    border-color: #001f3d;
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 22px;
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.14);
    color: #f1f5f9;
    padding: 0 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.18);
    white-space: nowrap;
    line-height: 1;
}

#deleteSubjectModal .modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
}
#deleteSubjectModal .modal-body {
    text-align: left !important;
    padding: 16px 18px !important;
}
#deleteSubjectModal .modal-body p {
    font-size: 11px !important;
    font-weight: 400 !important;
    color: #64748b !important;
}

/* Badges for Table Rows */
.badge-subject-id {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 20px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    background: #e2e8f0;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    line-height: 1;
}
.badge-subject-main {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    line-height: 1;
    white-space: nowrap;
}
.badge-subject-other {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    line-height: 1;
    white-space: nowrap;
}
.badge-assigned-classes {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
    line-height: 1;
    white-space: nowrap;
}
.badge-assigned-classes.has-classes {
    background: #f0fdf4;
    color: #15803d;
    border-color: #bbf7d0;
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

.btn-clear-filters {
    width: 27px;
    height: 27px;
    min-width: 27px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    color: #ffffff;
    cursor: pointer;
    font-size: 11px;
    line-height: 1;
    padding: 0;
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
    min-width: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s;
    line-height: 1;
    padding: 0;
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
    min-width: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    line-height: 1;
    padding: 0;
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
.table-loading-box {
    background: #002342;
    color: #fff;
    border: 1px solid #38bdf8;
    padding: 10px 18px;
    border-radius: 2px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show m-1 py-1 px-3" role="alert" style="font-size:12px; border-radius:2px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-1 py-1 px-3" role="alert" style="font-size:12px; border-radius:2px;">
                        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                {{-- Top Header & Action Banner (admissionView Style) --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-book mr-1"></i> ACADEMIC SETUP &amp; CURRICULUM</span>
                        <h1 class="admission-title">Subject Master Directory</h1>
                        <p class="admission-subtitle">Real-time Excel-style grid with cached partitioned queries, instant filters &amp; pinned pagination</p>
                    </div>
                    <div class="admission-hero-actions">
                        <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#addSubjectModal">
                            <i class="fa fa-plus mr-1"></i> Add Subject
                        </button>
                        <a href="{{ url('add_subject') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-leanpub mr-1"></i> Assign to Class
                        </a>
                    </div>
                </div>

                {{-- Full-Height Table Card with Excel-Like In-Column Filters & Pinned Pagination --}}
                <div class="dash-card admission-table-card position-relative">
                    <div id="tableLoadingOverlay" class="table-loading-overlay" style="display:none;">
                        <div class="table-loading-box">
                            <i class="fa fa-spinner fa-spin text-info"></i>
                            <span>Loading subjects...</span>
                        </div>
                    </div>

                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Subjects Directory Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $pagination['total_records'] }}</span> Total Subjects</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Body --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="subjects-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers (Arise Dark Navy ERP Style) --}}
                                <tr class="header-titles-row">
                                    <th style="width: 70px;" class="text-center">#ID</th>
                                    <th>Subject Name</th>
                                    <th style="width: 160px;" class="text-center">Category</th>
                                    <th style="width: 170px;" class="text-center">Assigned Classes</th>
                                    <th style="width: 100px;" class="text-center fixed_action_head">Action</th>
                                </tr>

                                {{-- Row 2: Excel In-Column Filters (Dark Navy Palette) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <input type="text" class="excel-col-filter text-center" id="filter-id" placeholder="Filter ID..." value="{{ $searchId ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Filter Subject..." value="{{ $search ?? '' }}">
                                    </th>
                                    <th class="text-center">
                                        <select class="excel-col-filter" id="filter-category">
                                            <option value="all" {{ ($categoryFilter ?? '') === 'all' ? 'selected' : '' }}>All Categories</option>
                                            <option value="main" {{ ($categoryFilter ?? '') === 'main' ? 'selected' : '' }}>Main Subject</option>
                                            <option value="other" {{ ($categoryFilter ?? '') === 'other' ? 'selected' : '' }}>Other / Co-Curricular</option>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-clear-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="subjectTableBody">
                                @include('master.subject.create_subject_rows', ['rows' => $rows, 'subjectCounts' => $subjectCounts])
                            </tbody>
                        </table>
                    </div>

                    {{-- Bottom Pagination Toolbar - Dark Navy --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info" id="paginationInfoText">
                            Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total_records'] }} records
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rowsPerPage">Rows:</label>
                                <select id="rowsPerPage">
                                    <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="-1" {{ ($perPage ?? 25) == -1 ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btnPrevPage" {{ $pagination['current_page'] <= 1 ? 'disabled' : '' }}>&lt;</button>
                                <span class="page-current-indicator" id="pageIndicator">Page {{ $pagination['current_page'] }} of {{ $pagination['total_pages'] }}</span>
                                <button type="button" class="page-btn" id="btnNextPage" {{ $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' }}>&gt;</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" style="border-radius:2px; overflow:hidden; border:1px solid #002C54; box-shadow: 0 4px 16px rgba(0,0,0,.2);">
            <div class="modal-header d-flex align-items-center justify-content-between" style="background:#002C54; color:#fff; padding:8px 12px; border-bottom:1px solid rgba(255,255,255,.12);">
                <h5 class="modal-title font-weight-bold" style="font-size:13px; margin:0;">
                    <i class="fa fa-plus-circle mr-1 text-info"></i> Add Master Subject
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="margin: -8px -12px -8px auto; padding: 8px 12px; opacity: .85; outline: none;">&times;</button>
            </div>
            <form id="formAddSubject" action="{{ url('create_subject') }}" method="post">
                @csrf
                <div class="modal-body" style="font-size:12px; padding:14px;">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.02em;">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="add_subject" class="form-control form-control-sm" placeholder="e.g. Mathematics, Science, English" required style="border-radius:2px; font-size:12px; height:32px;">
                    </div>
                    <div class="form-group mb-1">
                        <label class="font-weight-bold text-dark mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.02em;">Category</label>
                        <div class="d-flex align-items-center" style="gap: 16px;">
                            <label class="mb-0 d-inline-flex align-items-center" style="cursor:pointer; font-size:12px; gap:5px;">
                                <input type="radio" name="other_subject" value="0" checked>
                                <span>Main Subject</span>
                            </label>
                            <label class="mb-0 d-inline-flex align-items-center" style="cursor:pointer; font-size:12px; gap:5px;">
                                <input type="radio" name="other_subject" value="1">
                                <span>Other / Co-Curricular</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-end" style="background:#f8fafc; padding:8px 12px; border-top:1px solid #e2e8f0; gap:6px;">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="dash-btn dash-btn-primary">
                        <i class="fa fa-save mr-1"></i> Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" style="border-radius:2px; overflow:hidden; border:1px solid #002C54; box-shadow: 0 4px 16px rgba(0,0,0,.2);">
            <div class="modal-header d-flex align-items-center justify-content-between" style="background:#002C54; color:#fff; padding:8px 12px; border-bottom:1px solid rgba(255,255,255,.12);">
                <h5 class="modal-title font-weight-bold" style="font-size:13px; margin:0;">
                    <i class="fa fa-edit mr-1 text-info"></i> Edit Subject
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="margin: -8px -12px -8px auto; padding: 8px 12px; opacity: .85; outline: none;">&times;</button>
            </div>
            <form id="formEditSubject" action="{{ url('multi_edit_subject') }}" method="post">
                @csrf
                <input type="hidden" name="id[]" id="editSubjectId">
                <div class="modal-body" style="font-size:12px; padding:14px;">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.02em;">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="add_subject[]" id="editSubjectName" class="form-control form-control-sm" required style="border-radius:2px; font-size:12px; height:32px;">
                    </div>
                    <div class="form-group mb-1">
                        <label class="font-weight-bold text-dark mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.02em;">Category</label>
                        <div class="d-flex align-items-center" style="gap: 16px;">
                            <label class="mb-0 d-inline-flex align-items-center" style="cursor:pointer; font-size:12px; gap:5px;">
                                <input type="radio" name="other_subject_custom" id="editRadioMain" value="0">
                                <span>Main Subject</span>
                            </label>
                            <label class="mb-0 d-inline-flex align-items-center" style="cursor:pointer; font-size:12px; gap:5px;">
                                <input type="radio" name="other_subject_custom" id="editRadioOther" value="1">
                                <span>Other / Co-Curricular</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-end" style="background:#f8fafc; padding:8px 12px; border-top:1px solid #e2e8f0; gap:6px;">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="dash-btn dash-btn-primary">
                        <i class="fa fa-save mr-1"></i> Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Delete Subject Confirmation Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px !important; width: 95% !important;">
        <div class="modal-content" style="border: 1px solid #002C54; border-radius: 3px; overflow: hidden; box-shadow: 0 16px 36px rgba(0, 44, 84, 0.35); background: #ffffff;">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important; color: #ffffff !important; padding: 8px 14px !important; border-bottom: 1px solid rgba(255,255,255,0.12) !important; min-height: 42px !important; display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <div style="width: 26px; height: 26px; border-radius: 2px; background: rgba(255, 255, 255, 0.12); color: #f87171; border: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 12px;">
                        <i class="fa fa-trash"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold" style="font-size: 13px; margin: 0; color: #ffffff !important; line-height: 1.2;">
                            Delete Subject Confirmation
                        </h5>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" style="margin: -6px -10px -6px auto; padding: 6px 10px; opacity: .85; outline: none; font-size: 18px;">&times;</button>
            </div>
            <form id="formDeleteSubject" action="{{ url('delete_create_subject') }}" method="post">
                @csrf
                <input type="hidden" name="delete_id" id="deleteSubjectId">
                <div class="modal-body" style="padding: 16px 18px !important; background: #ffffff !important; text-align: left !important;">
                    <div class="d-flex align-items-start" style="gap: 14px;">
                        <div style="width: 38px; height: 38px; min-width: 38px; border-radius: 3px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid #fecaca; margin-top: 2px;">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                                Are you sure you want to delete this subject?
                            </div>
                            <div class="mb-2">
                                <span style="display: inline-flex; align-items: center; gap: 6px; height: 26px; padding: 0 10px; background: #eff6ff; color: #002C54; border: 1px solid #bfdbfe; border-radius: 2px; font-size: 12px; font-weight: 700;">
                                    <i class="fa fa-book text-primary" style="font-size: 11px;"></i>
                                    <span id="deleteSubjectNameLabel">Subject Name</span>
                                </span>
                            </div>
                            <div style="font-size: 11px; color: #64748b; line-height: 1.4;">
                                <i class="fa fa-info-circle mr-1 text-muted"></i> This action cannot be undone and will remove this subject from the master curriculum catalog.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc !important; padding: 8px 14px !important; border-top: 1px solid #e2e8f0 !important; display: flex !important; align-items: center !important; justify-content: flex-end !important; gap: 8px !important;">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" style="height: 28px; padding: 0 14px; font-size: 11px; font-weight: 600; border-radius: 2px; background: #ffffff;">
                        <i class="fa fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="dash-btn btn-danger" style="height: 28px; padding: 0 16px; font-size: 11px; font-weight: 600; border-radius: 2px; background: #dc2626 !important; border-color: #dc2626 !important; color: #ffffff !important;">
                        <i class="fa fa-trash mr-1"></i> Delete Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = {{ $pagination['current_page'] ?? 1 }};
    let totalPages = {{ $pagination['total_pages'] ?? 1 }};
    let debounceTimer = null;

    function fetchSubjects(page = 1) {
        const search = document.getElementById('filter-name').value.trim();
        const searchId = document.getElementById('filter-id').value.trim();
        const category = document.getElementById('filter-category').value;
        const perPage = document.getElementById('rowsPerPage').value;
        const overlay = document.getElementById('tableLoadingOverlay');

        if (overlay) overlay.style.display = 'flex';

        const params = new URLSearchParams({
            search: search,
            search_id: searchId,
            category: category,
            per_page: perPage,
            page: page
        });

        fetch(`{{ url('create_subject') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (overlay) overlay.style.display = 'none';
            if (data.status === 'success') {
                document.getElementById('subjectTableBody').innerHTML = data.html;

                // Update Pagination Toolbar & Header Counter
                document.getElementById('paginationInfoText').textContent =
                    `Showing ${data.pagination.from} to ${data.pagination.to} of ${data.pagination.total_records} records`;
                document.getElementById('header-records-count').textContent = data.pagination.total_records;
                document.getElementById('pageIndicator').textContent =
                    `Page ${data.pagination.current_page} of ${data.pagination.total_pages}`;

                currentPage = data.pagination.current_page;
                totalPages = data.pagination.total_pages;

                const btnPrev = document.getElementById('btnPrevPage');
                const btnNext = document.getElementById('btnNextPage');
                if (btnPrev) btnPrev.disabled = (currentPage <= 1);
                if (btnNext) btnNext.disabled = (currentPage >= totalPages);
            }
        })
        .catch(err => {
            if (overlay) overlay.style.display = 'none';
            console.error('Error fetching subjects:', err);
        });
    }

    // Live In-Column Filter Listeners (Debounced)
    ['filter-name', 'filter-id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchSubjects(1), 300);
            });
        }
    });

    const catSelect = document.getElementById('filter-category');
    if (catSelect) {
        catSelect.addEventListener('change', () => fetchSubjects(1));
    }

    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    if (rowsPerPageSelect) {
        rowsPerPageSelect.addEventListener('change', () => fetchSubjects(1));
    }

    const resetBtn = document.getElementById('btn-reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            document.getElementById('filter-name').value = '';
            document.getElementById('filter-id').value = '';
            document.getElementById('filter-category').value = 'all';
            document.getElementById('rowsPerPage').value = '25';
            fetchSubjects(1);
        });
    }

    // Pagination Nav Buttons
    const btnPrev = document.getElementById('btnPrevPage');
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) fetchSubjects(currentPage - 1);
        });
    }

    const btnNext = document.getElementById('btnNextPage');
    if (btnNext) {
        btnNext.addEventListener('click', () => {
            if (currentPage < totalPages) fetchSubjects(currentPage + 1);
        });
    }

    // Edit Subject Modal delegation
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-edit-subject');
        if (btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const other = parseInt(btn.dataset.other || 0);

            document.getElementById('editSubjectId').value = id;
            document.getElementById('editSubjectName').value = name;
            const radioMain = document.getElementById('editRadioMain');
            const radioOther = document.getElementById('editRadioOther');

            radioMain.name = `other_subject_${id}`;
            radioOther.name = `other_subject_${id}`;

            if (other === 1) {
                radioOther.checked = true;
            } else {
                radioMain.checked = true;
            }
        }
    });

    // Delete Subject Modal delegation
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-delete-subject');
        if (btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            document.getElementById('deleteSubjectId').value = id;
            document.getElementById('deleteSubjectNameLabel').textContent = name || ('Subject #' + id);
        }
    });
});
</script>

@endsection