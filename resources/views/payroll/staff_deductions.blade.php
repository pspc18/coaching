@extends('layout.app')

@section('title', 'Staff Manual Deductions - Arise ERP')

@section('content')

@php
    $monthNames = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
    $monthLabel = $monthNames[$month] ?? date('F');
@endphp

<style>
/* Page Layout & Viewport Fitting */
.deduction-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.deduction-page-wrapper * {
    box-sizing: border-box;
}

/* 1. Hero Header Banner - admissionView Theme */
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
    margin-bottom: 8px;
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
    white-space: nowrap;
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

/* 2. KPI Summary Grid */
.admission-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.admission-kpi-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
    cursor: pointer;
    transition: transform .12s ease, border-color .12s ease;
}
.admission-kpi-card:hover {
    transform: translateY(-1px);
    border-color: #0284c7;
}
.admission-kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0369a1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.admission-kpi-info {
    min-width: 0;
    flex: 1;
}
.admission-kpi-label {
    font-size: 9.5px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.admission-kpi-value {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.admission-kpi-sub {
    font-size: 9.5px;
    color: #94a3b8;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 3. Table Card & Excel Filter Row (admissionView Theme) */
.dash-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    margin-bottom: 8px;
    overflow: hidden;
}
.dash-card-header {
    background: #002C54;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dash-card-header h3 {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}

/* Top Controls / Filter Toolbar */
.table-filter-bar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-wrap: wrap;
}
.table-filter-group {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.table-filter-select, .table-search-input {
    height: 26px;
    font-size: 11.5px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    padding: 0 6px;
    background-color: #ffffff;
    color: #0f172a;
    outline: none;
}
.table-filter-select:focus, .table-search-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 1px #0284c7;
}
.table-search-input {
    width: 200px;
    padding-left: 22px;
}
.table-search-box {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.table-search-box i {
    position: absolute;
    left: 6px;
    font-size: 10px;
    color: #94a3b8;
}

/* Viewport Table Scrolling */
.table-scroll-container {
    max-height: calc(100vh - 275px);
    min-height: 380px;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #ffffff;
}
.dash-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}

/* Sticky Headers */
.header-titles-row th {
    background: #002342 !important;
    color: #ffffff !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 5px 6px !important;
    border-right: 1px solid rgba(255,255,255,.12) !important;
    border-bottom: 1px solid #001f3d !important;
    border-top: none !important;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
    height: 32px;
    vertical-align: middle;
}
.excel-filter-row th {
    background: #08335c !important;
    padding: 3px 4px !important;
    border-right: 1px solid rgba(255,255,255,.12) !important;
    border-bottom: 2px solid #001f3d !important;
    border-top: none !important;
    position: sticky;
    top: 32px;
    z-index: 9;
    height: 30px;
}
.excel-col-filter {
    width: 100%;
    height: 22px;
    font-size: 10.5px;
    padding: 1px 4px;
    border: 1px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.95);
    border-radius: 2px;
    color: #0f172a;
    outline: none;
}
.excel-col-filter:focus {
    background: #ffffff;
    border-color: #38bdf8;
    box-shadow: 0 0 0 1px #38bdf8;
}

/* Table Body Cells */
.dash-table tbody td {
    padding: 4px 6px !important;
    vertical-align: middle !important;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    background: #ffffff;
}
.dash-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
.dash-table tbody tr:hover td {
    background: #f1f5f9 !important;
}

/* Fixed Right Sticky Action Column */
.fixed_action_col {
    position: sticky;
    right: 0;
    z-index: 5;
    box-shadow: -2px 0 3px rgba(0,0,0,.04);
}
.header-titles-row th.fixed_action_col {
    z-index: 11;
    background: #002342 !important;
}
.excel-filter-row th.fixed_action_col {
    z-index: 10;
    background: #08335c !important;
}

/* Action Buttons */
.btn-action-icon {
    width: 24px;
    height: 24px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    line-height: 1;
    transition: all .15s;
    text-decoration: none !important;
}
.btn-action-edit {
    background: #e0f2fe;
    color: #0369a1;
    border-color: #bae6fd;
}
.btn-action-edit:hover {
    background: #0284c7;
    color: #fff;
}
.btn-action-delete {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #b91c1c;
    color: #fff;
}

/* 4. Pagination Bar */
.table-pagination-bar {
    padding: 6px 10px;
    border-top: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    font-size: 11px;
}
.pagination-controls {
    display: flex;
    gap: 2px;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
}
.page-btn {
    min-width: 24px;
    height: 24px;
    padding: 0 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    border-radius: 2px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease;
}
.page-btn:hover:not(.disabled):not(.active) {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}
.page-btn.active {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.page-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

@media (max-width: 991px) {
    .admission-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 575px) {
    .admission-kpi-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="content-wrapper deduction-page-wrapper">
    <div class="container-fluid p-2">
        
        <!-- 1. Hero Header Banner (admissionView theme) -->
        <div class="admission-hero">
            <div>
                <span class="admission-kicker"><i class="fa fa-minus-circle mr-1"></i> ARIS PAYROLL SYSTEM</span>
                <h1 class="admission-title">Staff Manual Deductions</h1>
                <p class="admission-subtitle">
                    Period: <strong>{{ $monthLabel }} {{ $year }}</strong> &bull; Manage one-time salary deductions, penalties and adjustments
                </p>
            </div>
            <div class="admission-hero-actions">
                <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#addDeductionModal">
                    <i class="fa fa-plus-circle mr-1 text-primary"></i> Add Deduction
                </button>
                <a href="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-money mr-1"></i> Staff Payroll
                </a>
                <a href="{{ url('payroll/staff/loans') }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-list-alt mr-1"></i> Loans & Advances
                </a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show p-2 mb-2" role="alert" style="font-size:11.5px;">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                <button type="button" class="close p-2" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show p-2 mb-2" role="alert" style="font-size:11.5px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                <button type="button" class="close p-2" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- 2. KPI Summary Grid -->
        <div class="admission-kpi-grid">
            <div class="admission-kpi-card" data-filter-status="all">
                <div class="admission-kpi-icon" style="background:#e0f2fe; color:#0369a1;">
                    <i class="fa fa-users"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Staff Covered</div>
                    <div class="admission-kpi-value" id="kpiStaffCovered">{{ $kpis['staff_covered'] ?? 0 }}</div>
                    <div class="admission-kpi-sub">Members with deductions</div>
                </div>
            </div>

            <div class="admission-kpi-card" data-filter-status="all">
                <div class="admission-kpi-icon" style="background:#fef3c7; color:#b45309;">
                    <i class="fa fa-list"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Total Entries</div>
                    <div class="admission-kpi-value" id="kpiTotalDeductions">{{ $kpis['total_deductions'] ?? count($rows) }}</div>
                    <div class="admission-kpi-sub">Recorded this period</div>
                </div>
            </div>

            <div class="admission-kpi-card" data-filter-status="applied">
                <div class="admission-kpi-icon" style="background:#dcfce7; color:#15803d;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Applied Amount</div>
                    <div class="admission-kpi-value text-success" id="kpiAppliedAmount">₹{{ $kpis['applied_amount'] ?? '0.00' }}</div>
                    <div class="admission-kpi-sub">{{ $kpis['applied_deductions'] ?? 0 }} entries applied</div>
                </div>
            </div>

            <div class="admission-kpi-card" data-filter-status="all">
                <div class="admission-kpi-icon" style="background:#fee2e2; color:#dc2626;">
                    <i class="fa fa-inr"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Total Deductions</div>
                    <div class="admission-kpi-value text-danger" id="kpiTotalAmount">₹{{ $kpis['total_amount'] ?? '0.00' }}</div>
                    <div class="admission-kpi-sub">Gross manual adjustments</div>
                </div>
            </div>
        </div>

        <!-- 3. Table Card with In-Column Excel Filters (admissionView standard) -->
        <div class="dash-card">
            
            <!-- Table Header -->
            <div class="dash-card-header">
                <h3><i class="fa fa-minus-circle mr-1"></i> Staff Deductions Register &bull; {{ $monthLabel }} {{ $year }}</h3>
                <span class="badge badge-light font-weight-bold" style="font-size:10px; color:#002C54;">{{ $pagination['total_records'] ?? count($rows) }} Records</span>
            </div>

            <!-- Top Filter Bar -->
            <div class="table-filter-bar">
                <div class="table-filter-group">
                    <label class="small text-muted font-weight-bold mb-0">Period:</label>
                    <select id="monthSelect" class="table-filter-select font-weight-bold">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)$month === $m ? 'selected' : '' }}>{{ $monthNames[$m] }}</option>
                        @endfor
                    </select>
                    <select id="yearSelect" class="table-filter-select font-weight-bold">
                        @for($y = date('Y')-3; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>

                    <label class="small text-muted font-weight-bold mb-0 ml-2">Status:</label>
                    <select id="statusSelect" class="table-filter-select">
                        <option value="all" {{ ($statusFilter ?? '') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="applied" {{ ($statusFilter ?? '') === 'applied' ? 'selected' : '' }}>Applied Only</option>
                        <option value="skipped" {{ ($statusFilter ?? '') === 'skipped' ? 'selected' : '' }}>Skipped Only</option>
                    </select>
                </div>

                <div class="table-filter-group">
                    <div class="table-search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="liveSearchInput" class="table-search-input" placeholder="Search staff name or UID..." value="{{ $search ?? '' }}">
                    </div>

                    <select id="perPageSelect" class="table-filter-select">
                        <option value="10">10 / page</option>
                        <option value="25" selected>25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                        <option value="-1">All Deductions</option>
                    </select>
                </div>
            </div>

            <!-- Viewport Table Scroll -->
            <div class="table-scroll-container">
                <table class="dash-table" id="deductionMainTable">
                    <thead>
                        <tr class="header-titles-row">
                            <th class="text-center" style="width: 70px;">ID</th>
                            <th>Staff Member</th>
                            <th class="text-center" style="width: 85px;">Period</th>
                            <th>Title / Reason</th>
                            <th class="text-right" style="width: 110px;">Amount (₹)</th>
                            <th class="text-center" style="width: 105px;">Status</th>
                            <th class="text-center fixed_action_col" style="width: 85px;">Action</th>
                        </tr>
                        <tr class="excel-filter-row">
                            <th><input type="text" class="excel-col-filter" id="col_filter_id" placeholder="ID..."></th>
                            <th><input type="text" class="excel-col-filter" id="col_filter_name" placeholder="Name / UID..."></th>
                            <th></th>
                            <th><input type="text" class="excel-col-filter" id="col_filter_title" placeholder="Filter title/remark..."></th>
                            <th></th>
                            <th></th>
                            <th class="fixed_action_col text-center">
                                <button type="button" class="btn btn-xs btn-outline-light py-0 px-1" id="btnClearExcelFilters" style="font-size:9.5px;" title="Reset filters">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="deductionRowsContainer">
                        @include('payroll.staff_deductions_rows', ['rows' => $rows, 'month' => $month, 'year' => $year])
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="table-pagination-bar">
                <div class="text-muted" id="paginationInfoText">
                    Showing {{ $pagination['from'] ?? 0 }} to {{ $pagination['to'] ?? 0 }} of {{ $pagination['total_records'] ?? 0 }} records
                </div>
                <ul class="pagination-controls" id="paginationControlsContainer">
                    <!-- Injected via JavaScript -->
                </ul>
            </div>

        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Modals Section -->
<!-- ========================================================================= -->

<!-- Add Deduction Modal -->
<div class="modal fade" id="addDeductionModal" tabindex="-1" role="dialog" aria-labelledby="addDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:3px; overflow:hidden;">
            <form method="post" action="{{ url('payroll/staff/deductions?month='.$month.'&year='.$year) }}">
                @csrf
                <input type="hidden" name="action" value="add_deduction">
                <div class="modal-header py-2" style="background:#002C54; color:#fff;">
                    <h5 class="modal-title font-weight-bold" style="font-size:13px;"><i class="fa fa-plus-circle mr-1"></i> Add Manual Salary Deduction</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Staff Member <span class="text-danger">*</span></label>
                        <select name="unique_id" class="form-control form-control-sm" required>
                            <option value="">Select Staff Member</option>
                            @foreach($staffList as $st)
                                @php
                                    $uid = 'USR-' . $st->id;
                                    $isLocked = isset($lockedUniqueIds[$uid]);
                                @endphp
                                <option value="{{ $uid }}" {{ $isLocked ? 'disabled' : '' }}>
                                    {{ trim(($st->first_name ?? '') . ' ' . ($st->last_name ?? '')) }}
                                    ({{ trim((string)($st->attendance_unique_id ?? '')) !== '' ? $st->attendance_unique_id : $uid }})
                                    {{ $isLocked ? ' - [Salary Finalized]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Deduction Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm text-right font-weight-bold" placeholder="0.00" required>
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Title / Reason <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Uniform Fine, Damage Recovery, TDS" maxlength="100" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Remark / Notes</label>
                        <textarea name="remark" class="form-control form-control-sm" rows="2" placeholder="Optional reference or approval notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size:11px;">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="font-size:11px;"><i class="fa fa-save mr-1"></i> Save Deduction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Deduction Modal -->
<div class="modal fade" id="editDeductionModal" tabindex="-1" role="dialog" aria-labelledby="editDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:3px; overflow:hidden;">
            <form method="post" action="{{ url('payroll/staff/deductions?month='.$month.'&year='.$year) }}">
                @csrf
                <input type="hidden" name="action" value="update_deduction">
                <input type="hidden" name="deduction_id" id="editDeductionId">
                <div class="modal-header py-2" style="background:#002C54; color:#fff;">
                    <h5 class="modal-title font-weight-bold" style="font-size:13px;"><i class="fa fa-edit mr-1"></i> Edit Manual Deduction</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Staff Member</label>
                        <input type="text" id="editStaffName" class="form-control form-control-sm" readonly style="background:#f8fafc;">
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Deduction Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="editAmount" class="form-control form-control-sm text-right font-weight-bold" required>
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Title / Reason <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editTitle" class="form-control form-control-sm" required maxlength="100">
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Remark / Notes</label>
                        <textarea name="remark" id="editRemark" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size:11px;">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="font-size:11px;"><i class="fa fa-save mr-1"></i> Update Deduction</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var currentPage = {{ $pagination['current_page'] ?? 1 }};
    var perPage = {{ $pagination['per_page'] ?? 25 }};
    var totalPages = {{ $pagination['total_pages'] ?? 1 }};
    var totalRecords = {{ $pagination['total_records'] ?? 0 }};
    var searchTimer = null;

    // Render initial pagination
    renderPagination({{ $pagination['current_page'] ?? 1 }}, {{ $pagination['total_pages'] ?? 1 }});

    // Real-Time AJAX Fetcher
    function fetchDeductionData(page) {
        if (page) currentPage = page;
        var month = $('#monthSelect').val();
        var year = $('#yearSelect').val();
        var status = $('#statusSelect').val();
        var search = $('#liveSearchInput').val();
        var perPageVal = $('#perPageSelect').val();
        var searchId = $('#col_filter_id').val();
        var searchName = $('#col_filter_name').val();
        var searchTitle = $('#col_filter_title').val();

        $('#deductionRowsContainer').css('opacity', '0.5');

        $.ajax({
            url: '{{ url("payroll/staff/deductions") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                month: month,
                year: year,
                status: status,
                search: search,
                search_id: searchId,
                search_name: searchName,
                search_title: searchTitle,
                per_page: perPageVal,
                page: currentPage
            },
            success: function (res) {
                $('#deductionRowsContainer').html(res.html).css('opacity', '1');

                // Update pagination info
                var p = res.pagination;
                currentPage = p.current_page;
                totalPages = p.total_pages;
                totalRecords = p.total_records;

                $('#paginationInfoText').text('Showing ' + p.from + ' to ' + p.to + ' of ' + p.total_records + ' records');
                renderPagination(p.current_page, p.total_pages);

                // Update KPIs
                if (res.kpis) {
                    $('#kpiStaffCovered').text(res.kpis.staff_covered);
                    $('#kpiTotalDeductions').text(res.kpis.total_deductions);
                    $('#kpiAppliedAmount').text('₹' + res.kpis.applied_amount);
                    $('#kpiTotalAmount').text('₹' + res.kpis.total_amount);
                }
            },
            error: function () {
                $('#deductionRowsContainer').css('opacity', '1');
            }
        });
    }

    function renderPagination(current, total) {
        var html = '';
        if (total <= 1) {
            $('#paginationControlsContainer').html('');
            return;
        }

        // Prev
        html += '<li><a href="#" class="page-btn ' + (current <= 1 ? 'disabled' : '') + '" data-page="' + (current - 1) + '"><i class="fa fa-angle-left"></i></a></li>';

        var start = Math.max(1, current - 2);
        var end = Math.min(total, current + 2);

        if (start > 1) {
            html += '<li><a href="#" class="page-btn" data-page="1">1</a></li>';
            if (start > 2) html += '<li><span class="px-1 text-muted" style="font-size:10px;">...</span></li>';
        }

        for (var i = start; i <= end; i++) {
            html += '<li><a href="#" class="page-btn ' + (i === current ? 'active' : '') + '" data-page="' + i + '">' + i + '</a></li>';
        }

        if (end < total) {
            if (end < total - 1) html += '<li><span class="px-1 text-muted" style="font-size:10px;">...</span></li>';
            html += '<li><a href="#" class="page-btn" data-page="' + total + '">' + total + '</a></li>';
        }

        // Next
        html += '<li><a href="#" class="page-btn ' + (current >= total ? 'disabled' : '') + '" data-page="' + (current + 1) + '"><i class="fa fa-angle-right"></i></a></li>';

        $('#paginationControlsContainer').html(html);
    }

    // Pagination Click
    $(document).on('click', '.pagination-controls .page-btn', function (e) {
        e.preventDefault();
        if ($(this).hasClass('disabled') || $(this).hasClass('active')) return;
        var page = parseInt($(this).data('page'));
        if (page > 0) fetchDeductionData(page);
    });

    // Period Change
    $('#monthSelect, #yearSelect').on('change', function () {
        var m = $('#monthSelect').val();
        var y = $('#yearSelect').val();
        window.location.href = '{{ url("payroll/staff/deductions") }}?month=' + m + '&year=' + y;
    });

    $('#statusSelect, #perPageSelect').on('change', function () {
        currentPage = 1;
        fetchDeductionData(1);
    });

    // KPI Card Click Filter
    $('.admission-kpi-card').on('click', function () {
        var status = $(this).data('filter-status');
        if (status) {
            $('#statusSelect').val(status);
            currentPage = 1;
            fetchDeductionData(1);
        }
    });

    // Debounced Search Inputs
    $('#liveSearchInput, #col_filter_id, #col_filter_name, #col_filter_title').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            currentPage = 1;
            fetchDeductionData(1);
        }, 250);
    });

    // Reset In-Column Filters
    $('#btnClearExcelFilters').on('click', function () {
        $('#col_filter_id, #col_filter_name, #col_filter_title, #liveSearchInput').val('');
        currentPage = 1;
        fetchDeductionData(1);
    });

    // Edit Modal Data Handler
    $(document).on('click', '.js-edit-deduction', function () {
        var button = $(this);
        $('#editDeductionId').val(button.data('id'));
        $('#editStaffName').val(button.data('name'));
        $('#editAmount').val(button.data('amount'));
        $('#editTitle').val(button.data('title'));
        $('#editRemark').val(button.data('remark'));
    });
});
</script>
@endpush