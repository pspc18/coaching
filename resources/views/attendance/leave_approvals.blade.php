@extends('layout.app')

@php
    $counts = $counts ?? (object)[
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'cancelled' => 0,
        'total' => 0
    ];
    $statusFilter = $statusFilter ?? '2';
    $totalCount = $totalCount ?? count($rows ?? []);
    $page = $page ?? 1;
    $perPage = $perPage ?? 20;
    $lastPage = $lastPage ?? 1;
    $startIndex = $startIndex ?? 0;
    $from = $from ?? ($totalCount > 0 ? 1 : 0);
    $to = $to ?? min(count($rows ?? []), $totalCount);
@endphp

@section('styles')
<style>
/* Page Layout & Viewport Fitting (Identical to User View & Student List) */
.leave-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.leave-page * {
    box-sizing: border-box;
}
.leave-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Header (ARISE ERP Signature Dark Navy Theme) */
.leave-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.leave-hero-text {
    display: flex;
    flex-direction: column;
}
.leave-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.leave-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}
.leave-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Summary Stats Badges */
.leave-hero-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 12px;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.22);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-red {
    background: rgba(239, 68, 68, 0.22);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fca5a5;
}
.hero-stat-badge.badge-gray {
    background: rgba(148, 163, 184, 0.22);
    border-color: rgba(148, 163, 184, 0.4);
    color: #e2e8f0;
}

/* Full Table Card (Fits available screen height) */
.leave-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    margin-bottom: 0;
    overflow: hidden;
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
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

/* Scrollable Table Area (Fits Available Viewport Height) */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Grid Table */
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

/* Header Titles Row */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 7px 8px;
    height: 36px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
}

/* Sticky Filter Row */
.excel-filter-row th {
    position: sticky;
    top: 36px;
    background: #08335c;
    color: #ffffff;
    padding: 4px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
}

/* Fixed Sticky Action Column */
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
    padding: 2px 6px;
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

/* Reset Button */
.btn-reset-filters {
    height: 27px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: all .15s;
    white-space: nowrap;
}
.btn-reset-filters:hover {
    background: #002C54;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Table Body Rows */
.dash-table tbody td {
    padding: 4px 8px;
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

/* Strict Uniform Badge Height */
.leave-page .badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 22px !important;
    line-height: 22px !important;
    padding: 0 8px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    border-radius: 2px !important;
    vertical-align: middle !important;
    box-sizing: border-box !important;
    white-space: nowrap !important;
    letter-spacing: .2px !important;
}
.badge-type-student {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
}
.badge-type-staff {
    background: #f1f5f9 !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
}

/* Uniform Action Buttons */
.leave-page .table .btn-sm {
    height: 22px !important;
    line-height: 20px !important;
    padding: 0 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    border-radius: 2px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 3px !important;
}

/* Centered Empty State (Exact match with User View & Student List) */
#empty-state-row td {
    padding: 0 !important;
    border: none !important;
    background: #eef2f6 !important;
    vertical-align: middle !important;
    text-align: center !important;
}
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height, 56px) - 200px);
    min-height: max(320px, calc(100vh - var(--header-height, 56px) - 200px));
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
    margin: 0 auto;
}
.dash-empty-state .empty-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
}
.dash-empty-state .empty-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 420px;
    line-height: 1.5;
}

/* Pinned Bottom Pagination Toolbar (Identical to User View) */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 38px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.pagination-info {
    font-size: 11.5px;
    color: #cbd5e1;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 5px;
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
    font-size: 11px;
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
.table-loading {
    opacity: 0.55;
    pointer-events: none;
}
</style>
@endsection

@section('content')
<div class="content-wrapper leave-page">
    <section class="content p-2">
        <div class="container-fluid p-0">
            <div class="leave-page-layout">

                {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
                <div class="leave-hero">
                    <div class="leave-hero-text">
                        <span class="leave-kicker"><i class="fa fa-calendar-minus-o mr-1"></i> Attendance Management</span>
                        <h1 class="leave-title">Leave Approvals Directory</h1>
                        <p class="leave-subtitle">Review, approve, or reject student and staff leave requests with in-column Excel filters &amp; status queue</p>
                    </div>

                    {{-- Hero Summary Stats Badges --}}
                    <div class="leave-hero-stats">
                        <span class="hero-stat-badge badge-amber" title="Pending Leaves">
                            <i class="fa fa-clock-o"></i> Pending: <b id="stat-pending">{{ $counts->pending ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge badge-green" title="Approved Leaves">
                            <i class="fa fa-check-circle"></i> Approved: <b id="stat-approved">{{ $counts->approved ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge badge-red" title="Rejected Leaves">
                            <i class="fa fa-times-circle"></i> Rejected: <b id="stat-rejected">{{ $counts->rejected ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge badge-gray" title="Cancelled Leaves">
                            <i class="fa fa-ban"></i> Cancelled: <b id="stat-cancelled">{{ $counts->cancelled ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge" title="Total Leave Requests">
                            <i class="fa fa-list-alt"></i> Total: <b id="stat-total">{{ $counts->total ?? 0 }}</b>
                        </span>
                    </div>

                </div>

                @if(session('message'))
                    <div class="alert alert-success py-2 mb-2">{{ session('message') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 mb-2">{{ session('error') }}</div>
                @endif

                {{-- Full Height Table Card (Fits available screen height) --}}
                <div class="leave-table-card">
                    <div class="dash-card-header">
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <h3 class="dash-card-title"><i class="fa fa-table mr-1 text-info"></i> Leave Requests Grid</h3>
                        </div>
                        <div>
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Requests</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="leave-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles --}}
                                <tr class="header-titles-row">
                                    <th style="width: 46px;" class="text-center">#</th>
                                    <th style="width: 100px;" class="text-center">Type</th>
                                    <th style="min-width: 170px;">Name</th>
                                    <th style="width: 120px;" class="text-center">Attendance ID</th>
                                    <th style="width: 110px;" class="text-center">From Date</th>
                                    <th style="width: 110px;" class="text-center">To Date</th>
                                    <th style="min-width: 200px;">Reason</th>
                                    <th style="width: 110px;" class="text-center">Status</th>
                                    <th style="width: 185px;" class="text-center fixed_action_head">Actions</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters --}}
                                <tr class="excel-filter-row">
                                    <th></th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-type">
                                            <option value="">All Types</option>
                                            <option value="student" {{ ($userTypeFilter ?? '') === 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="staff" {{ ($userTypeFilter ?? '') === 'staff' ? 'selected' : '' }}>Staff</option>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Search name..." value="{{ $nameFilter ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-uid" placeholder="Search ID..." value="{{ $uidFilter ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="date" class="excel-col-filter" id="filter-from-date" value="{{ $fromDateFilter ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="date" class="excel-col-filter" id="filter-to-date" value="{{ $toDateFilter ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-reason" placeholder="Search reason..." value="{{ $reasonFilter ?? '' }}">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-status">
                                            <option value="" {{ ($statusFilter === '' || $statusFilter === 'all') ? 'selected' : '' }}>All Status</option>
                                            <option value="2" {{ $statusFilter === '2' ? 'selected' : '' }}>Pending</option>
                                            <option value="1" {{ $statusFilter === '1' ? 'selected' : '' }}>Approved</option>
                                            <option value="0" {{ $statusFilter === '0' ? 'selected' : '' }}>Rejected</option>
                                            <option value="3" {{ $statusFilter === '3' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters" title="Reset All In-Column Filters">
                                            <i class="fa fa-refresh mr-1"></i> Clear
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="leave-table-body">
                                @include('attendance.leave_table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. Pinned Bottom Pagination Toolbar (Exact match with User View) --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $from }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ $to }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ ($perPage ?? 20) == 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" {{ $page <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" {{ $page <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">{{ $page }}</span> of <span id="total-pages">{{ $lastPage }}</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page" {{ $page >= $lastPage ? 'disabled' : '' }}><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page" {{ $page >= $lastPage ? 'disabled' : '' }}><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- Client-Side Dynamic AJAX Filtering & Pagination Engine --}}
    <script>
    $(document).ready(function() {
        let currentPage = parseInt('{{ $page ?? 1 }}') || 1;
        let currentLastPage = parseInt('{{ $lastPage ?? 1 }}') || 1;
        let filterDebounceTimer = null;
        let isFetching = false;

        function getFilterParams() {
            const st = $('#filter-status').val();
            return {
                ajax: 1,
                page: currentPage,
                per_page: $('#rows-per-page-select').val() || 20,
                status: (st !== undefined && st !== null) ? st : '',
                user_type: ($('#filter-type').val() || '').trim(),
                name: ($('#filter-name').val() || '').trim(),
                attendance_unique_id: ($('#filter-uid').val() || '').trim(),
                from_date: ($('#filter-from-date').val() || '').trim(),
                to_date: ($('#filter-to-date').val() || '').trim(),
                reason: ($('#filter-reason').val() || '').trim()
            };
        }

        function loadLeaveData() {
            if (isFetching) return;
            isFetching = true;
            const tableCard = $('.leave-table-card');
            tableCard.addClass('table-loading');

            $.ajax({
                url: "{{ url('attendance/leave/approvals') }}",
                type: 'GET',
                data: getFilterParams(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.status) {
                        $('#leave-table-body').html(res.html);

                        currentPage = parseInt(res.current_page) || 1;
                        currentLastPage = parseInt(res.last_page) || 1;

                        $('#page-start').text(res.from);
                        $('#page-end').text(res.to);
                        $('#total-records').text(res.total);
                        $('#header-records-count').text(res.total);
                        $('#current-page').text(currentPage);
                        $('#total-pages').text(currentLastPage);

                        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                        $('#btn-next, #btn-last').prop('disabled', currentPage >= currentLastPage || currentLastPage <= 1);

                        // Update live stats in hero banner
                        if (res.counts) {
                            $('#stat-pending').text(res.counts.pending || 0);
                            $('#stat-approved').text(res.counts.approved || 0);
                            $('#stat-rejected').text(res.counts.rejected || 0);
                            $('#stat-cancelled').text(res.counts.cancelled || 0);
                            $('#stat-total').text(res.counts.total || 0);
                        }
                    }
                },
                error: function(xhr, status, err) {
                    console.error('Leave filter request failed:', err);
                },
                complete: function() {
                    isFetching = false;
                    tableCard.removeClass('table-loading');
                }
            });
        }

        function triggerDebouncedFilter() {
            clearTimeout(filterDebounceTimer);
            filterDebounceTimer = setTimeout(function() {
                currentPage = 1;
                loadLeaveData();
            }, 260);
        }

        // In-Column Filters Listeners
        $('#filter-name, #filter-uid, #filter-reason').on('input keyup paste', triggerDebouncedFilter);
        $('#filter-type, #filter-status, #filter-from-date, #filter-to-date').on('change', function() {
            currentPage = 1;
            loadLeaveData();
        });

        // Enter key support for instant filter execution
        $('.excel-col-filter').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                clearTimeout(filterDebounceTimer);
                currentPage = 1;
                loadLeaveData();
            }
        });

        // Rows per page change
        $('#rows-per-page-select').on('change', function() {
            currentPage = 1;
            loadLeaveData();
        });

        // Pagination Controls
        $('#btn-first').on('click', function() {
            if (currentPage > 1) {
                currentPage = 1;
                loadLeaveData();
            }
        });

        $('#btn-prev').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadLeaveData();
            }
        });

        $('#btn-next').on('click', function() {
            if (currentPage < currentLastPage) {
                currentPage++;
                loadLeaveData();
            }
        });

        $('#btn-last').on('click', function() {
            if (currentPage < currentLastPage) {
                currentPage = currentLastPage;
                loadLeaveData();
            }
        });

        // Clear All Filters
        $('#btn-clear-filters').on('click', function() {
            $('#filter-name, #filter-uid, #filter-reason, #filter-from-date, #filter-to-date').val('');
            $('#filter-type').val('');
            $('#filter-status').val('');
            currentPage = 1;
            loadLeaveData();
        });
    });
    </script>
</div>
@endsection

