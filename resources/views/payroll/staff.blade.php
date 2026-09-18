@extends('layout.app')

@section('title', 'Staff Payroll Register - Arise ERP')

@section('content')

@php
    $monthLabel = date('F', mktime(0, 0, 0, $month, 1));
    $staffCount = count($staff ?? []);
    $totalSalaryNow = (float)($totals['salary_till_now'] ?? 0);
    $totalManual = (float)($totals['manual_deductions'] ?? 0);
    $totalLoan = (float)($totals['loan_deductions'] ?? 0);
    $totalDeduction = $totalManual + $totalLoan;
    $totalNet = (float)($totals['net_salary'] ?? ($totalSalaryNow - $totalDeduction));
    $totalPaid = (float)($totals['paid_amount'] ?? 0);
    $totalBalance = (float)($totals['balance_amount'] ?? 0);
    $leaveAllowedLabel = isset($payrollSetting) && $payrollSetting->paid_leave_limit !== null
        ? $payrollSetting->paid_leave_limit . ' days'
        : 'Unlimited';
@endphp

<style>
/* Page Layout & Viewport Fitting */
.payroll-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.payroll-page-wrapper * {
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
.dash-btn-success {
    background: #16a34a;
    color: #fff;
    border-color: #16a34a;
}
.dash-btn-success:hover {
    background: #15803d;
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
    width: 180px;
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
.btn-action-view {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-view:hover {
    background: #dc2626;
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
.btn-action-pay {
    background: #dcfce7;
    color: #15803d;
    border-color: #bbf7d0;
}
.btn-action-pay:hover {
    background: #16a34a;
    color: #fff;
}
.btn-action-history {
    background: #e0e7ff;
    color: #4338ca;
    border-color: #c7d2fe;
}
.btn-action-history:hover {
    background: #4f46e5;
    color: #fff;
}
.btn-action-regen {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.btn-action-regen:hover {
    background: #002C54;
    color: #fff;
}
.btn-action-success {
    background: #dcfce7;
    color: #15803d;
    border-color: #bbf7d0;
}
.btn-action-success:hover {
    background: #16a34a;
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

<div class="content-wrapper payroll-page-wrapper">
    <div class="container-fluid p-2">
        
        <!-- 1. Hero Banner (admissionView theme) -->
        <div class="admission-hero">
            <div>
                <span class="admission-kicker"><i class="fa fa-money mr-1"></i> ARIS PAYROLL SYSTEM</span>
                <h1 class="admission-title">Staff Payroll Register</h1>
                <p class="admission-subtitle">
                    Period: <strong>{{ $monthLabel }} {{ $year }}</strong> &bull; Cutoff: <strong>Till {{ \Carbon\Carbon::parse($rangeEnd)->format('d M Y') }}</strong> &bull; Paid Leave Limit: <strong>{{ $leaveAllowedLabel }}</strong>
                </p>
            </div>
            <div class="admission-hero-actions">
                <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#payrollSettingsModal">
                    <i class="fa fa-sliders mr-1 text-primary"></i> Settings
                </button>
                <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#salaryModal">
                    <i class="fa fa-cog mr-1 text-info"></i> Set Salary
                </button>
                <a href="{{ url('payroll/staff/loans') }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-list-alt mr-1"></i> Loans & Advances
                </a>
                <a href="{{ url('payroll/staff/deductions?month='.$month.'&year='.$year) }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-minus-circle mr-1"></i> Deductions
                </a>
                <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" style="display:inline-block;" onsubmit="return confirm('Generate / Finalize salary for all active staff for {{ $monthLabel }} {{ $year }}?');">
                    @csrf
                    <input type="hidden" name="action" value="generate_salary_bulk">
                    <button type="submit" class="dash-btn dash-btn-success">
                        <i class="fa fa-check-circle mr-1"></i> Bulk Finalize
                    </button>
                </form>
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
                    <div class="admission-kpi-label">Active Staff</div>
                    <div class="admission-kpi-value" id="kpiStaffCount">{{ $staffCount }}</div>
                    <div class="admission-kpi-sub">Total eligible staff</div>
                </div>
            </div>

            <div class="admission-kpi-card" data-filter-status="all">
                <div class="admission-kpi-icon" style="background:#e0e7ff; color:#4338ca;">
                    <i class="fa fa-calculator"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Gross Salary</div>
                    <div class="admission-kpi-value" id="kpiGrossSalary">₹{{ number_format($totalSalaryNow, 2) }}</div>
                    <div class="admission-kpi-sub">Calculated till cutoff</div>
                </div>
            </div>

            <div class="admission-kpi-card" data-filter-status="all">
                <div class="admission-kpi-icon" style="background:#fef3c7; color:#b45309;">
                    <i class="fa fa-minus-circle"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Total Deductions</div>
                    <div class="admission-kpi-value" id="kpiDeductions" style="color:#b45309;">₹{{ number_format($totalDeduction, 2) }}</div>
                    <div class="admission-kpi-sub">Manual & Loan EMIs</div>
                </div>
            </div>

            <div class="admission-kpi-card" data-filter-status="balance">
                <div class="admission-kpi-icon" style="background:#dcfce7; color:#15803d;">
                    <i class="fa fa-inr"></i>
                </div>
                <div class="admission-kpi-info">
                    <div class="admission-kpi-label">Net Balance Due</div>
                    <div class="admission-kpi-value {{ $totalBalance > 0 ? 'text-danger' : 'text-success' }}" id="kpiBalanceAmount">₹{{ number_format($totalBalance, 2) }}</div>
                    <div class="admission-kpi-sub">Paid: <span id="kpiPaidAmount">₹{{ number_format($totalPaid, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- 3. Table Card with Excel Filters (admissionView standard) -->
        <div class="dash-card">
            
            <!-- Table Header -->
            <div class="dash-card-header">
                <h3><i class="fa fa-list mr-1"></i> Staff Payroll Register &bull; {{ $monthLabel }} {{ $year }}</h3>
                <span class="badge badge-light font-weight-bold" style="font-size:10px; color:#002C54;">{{ $pagination['total_records'] ?? $staffCount }} Records</span>
            </div>

            <!-- Top Filter Bar -->
            <div class="table-filter-bar">
                <div class="table-filter-group">
                    <label class="small text-muted font-weight-bold mb-0">Period:</label>
                    <select id="monthSelect" class="table-filter-select font-weight-bold">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)$month === (int)$m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select id="yearSelect" class="table-filter-select font-weight-bold">
                        @for($y = date('Y')-3; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ (int)$year === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>

                    <label class="small text-muted font-weight-bold mb-0 ml-2">Role:</label>
                    <select id="roleSelect" class="table-filter-select">
                        <option value="">All Roles</option>
                        @foreach($rolesById as $roleId => $roleObj)
                            <option value="{{ $roleId }}" {{ (isset($roleFilter) && (int)$roleFilter === (int)$roleId) ? 'selected' : '' }}>{{ $roleObj->name }}</option>
                        @endforeach
                    </select>

                    <label class="small text-muted font-weight-bold mb-0 ml-2">Status:</label>
                    <select id="statusSelect" class="table-filter-select">
                        <option value="all">All Status</option>
                        <option value="finalized">Finalized</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Fully Paid</option>
                        <option value="balance">Balance Due</option>
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
                        <option value="-1">All Staff</option>
                    </select>
                </div>
            </div>

            <!-- Viewport Table Scroll -->
            <div class="table-scroll-container">
                <table class="dash-table" id="payrollMainTable">
                    <thead>
                        <tr class="header-titles-row">
                            <th class="text-center" style="width: 80px;">UID</th>
                            <th>Staff Name & Role</th>
                            <th class="text-right" style="width: 95px;">Monthly Rate</th>
                            <th class="text-center" style="width: 85px;">Work Days</th>
                            <th class="text-center" style="width: 80px;">Paid Days</th>
                            <th class="text-right" style="width: 100px;">Salary Till Now</th>
                            <th class="text-right" style="width: 90px;">Manual Ded.</th>
                            <th class="text-right" style="width: 90px;">Loan Ded.</th>
                            <th class="text-right" style="width: 105px;">Net Salary</th>
                            <th class="text-right" style="width: 95px;">Paid Amt</th>
                            <th class="text-right" style="width: 95px;">Balance</th>
                            <th class="text-center" style="width: 85px;">Status</th>
                            <th class="text-center fixed_action_col" style="width: 125px;">Action</th>
                        </tr>
                        <tr class="excel-filter-row">
                            <th><input type="text" class="excel-col-filter" id="col_filter_uid" placeholder="UID..."></th>
                            <th><input type="text" class="excel-col-filter" id="col_filter_name" placeholder="Filter Name / Role..."></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="fixed_action_col text-center">
                                <button type="button" class="btn btn-xs btn-outline-light py-0 px-1" id="btnClearExcelFilters" style="font-size:9.5px;" title="Reset filters">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="payrollRowsContainer">
                        @include('payroll.staff_rows', ['rows' => $rows, 'month' => $month, 'year' => $year])
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

<!-- Salary Payment Modal -->
<div class="modal fade" id="salaryPaymentModal" tabindex="-1" role="dialog" aria-labelledby="salaryPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:3px; overflow:hidden;">
            <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}">
                @csrf
                <input type="hidden" name="action" value="pay_salary">
                <input type="hidden" name="unique_id" id="paymentUniqueId">
                <div class="modal-header py-2" style="background:#002C54; color:#fff;">
                    <h5 class="modal-title font-weight-bold" style="font-size:13px;"><i class="fa fa-money mr-1"></i> Record Salary Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Staff Member</label>
                        <input type="text" id="paymentStaffName" class="form-control form-control-sm" readonly style="background:#f8fafc;">
                    </div>
                    <div class="row mb-2">
                        <div class="col-4">
                            <label class="small text-muted mb-0" style="font-size:10.5px;">Net Payable</label>
                            <input type="text" id="paymentPayable" class="form-control form-control-sm text-right font-weight-bold" readonly style="background:#f8fafc;">
                        </div>
                        <div class="col-4">
                            <label class="small text-muted mb-0" style="font-size:10.5px;">Already Paid</label>
                            <input type="text" id="paymentAlreadyPaid" class="form-control form-control-sm text-right text-success font-weight-bold" readonly style="background:#f8fafc;">
                        </div>
                        <div class="col-4">
                            <label class="small text-muted mb-0" style="font-size:10.5px;">Balance Due</label>
                            <input type="text" id="paymentBalance" class="form-control form-control-sm text-right text-danger font-weight-bold" readonly style="background:#f8fafc;">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold" style="margin-bottom:2px;">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="paymentAmount" class="form-control form-control-sm text-right font-weight-bold" min="0.01" step="0.01" required>
                        <small class="text-muted" style="font-size:10px;">Enter full remaining balance or partial installment amount.</small>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size:11px;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success font-weight-bold" style="font-size:11px;"><i class="fa fa-check mr-1"></i> Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:3px; overflow:hidden;">
            <div class="modal-header py-2" style="background:#002C54; color:#fff;">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0" style="font-size:13px;"><i class="fa fa-list-alt mr-1"></i> Payment History Breakdown</h5>
                    <div class="small text-light" id="historyStaff" style="font-size:10.5px; opacity:.9;"></div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div id="paymentHistoryListPanel">
                    <div class="row mb-3">
                        <div class="col-4"><div class="small text-muted" style="font-size:10.5px;">Finalized Net Salary</div><h5 class="font-weight-bold mb-0" id="historyPayable" style="color:#002C54; font-size:14px;">₹0.00</h5></div>
                        <div class="col-4"><div class="small text-muted" style="font-size:10.5px;">Total Paid</div><h5 class="text-success font-weight-bold mb-0" id="historyPaid" style="font-size:14px;">₹0.00</h5></div>
                        <div class="col-4"><div class="small text-muted" style="font-size:10.5px;">Remaining Balance</div><h5 class="text-danger font-weight-bold mb-0" id="historyBalance" style="font-size:14px;">₹0.00</h5></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" style="font-size:11.5px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Date & Time</th>
                                    <th>Recorded By</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-center" style="width:60px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyRows"></tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="3" class="text-right">Total Paid:</th>
                                    <th class="text-right text-success font-weight-bold" id="historyTableTotal">₹0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div id="paymentHistoryEditPanel" style="display:none;">
                    <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}" id="editSalaryPaymentForm">
                        @csrf
                        <input type="hidden" name="action" value="update_salary_payment">
                        <input type="hidden" name="payment_id" id="editPaymentId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="text" name="amount" id="editPaymentAmount" class="form-control form-control-sm text-right font-weight-bold" required>
                                    <small class="text-muted" style="font-size:10px;">Maximum allowed: <b id="editPaymentMaximum">0.00</b></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Payment Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="paid_at" id="editPaymentDate" class="form-control form-control-sm" required>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-light border py-2 mb-2">
                            <div class="row small">
                                <div class="col-6">Finalized Net: <b id="editPaymentPayable">0.00</b></div>
                                <div class="col-6">Other Payments: <b id="editOtherPayments">0.00</b></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-sm btn-secondary js-payment-edit-back" style="font-size:11px;"><i class="fa fa-arrow-left mr-1"></i> Back</button>
                            <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="font-size:11px;"><i class="fa fa-save mr-1"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer py-2" id="paymentHistoryFooter" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                <a href="#" id="historyExcelLink" class="btn btn-sm btn-success" style="font-size:11px;"><i class="fa fa-file-excel-o mr-1"></i> Export Excel</a>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size:11px;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payroll Settings Modal -->
<div class="modal fade" id="payrollSettingsModal" tabindex="-1" role="dialog" aria-labelledby="payrollSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:3px; overflow:hidden;">
            <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}">
                @csrf
                <input type="hidden" name="action" value="settings">
                <div class="modal-header py-2" style="background:#002C54; color:#fff;">
                    <h5 class="modal-title font-weight-bold" style="font-size:13px;"><i class="fa fa-sliders mr-1"></i> Payroll & Attendance Rules</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Paid Leave Allowed (Days / Month)</label>
                                <input type="number" min="0" max="31" name="paid_leave_limit" class="form-control form-control-sm"
                                       value="{{ old('paid_leave_limit', $payrollSetting->paid_leave_limit ?? '') }}"
                                       placeholder="Leave blank for Unlimited">
                                <small class="text-muted" style="font-size:10px;">Extra leaves above limit are unpaid (0 credit).</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Early Out Credit (Paid Day)</label>
                                @php $earlyOut = old('early_out_weight', $payrollSetting->early_out_weight ?? 1); $earlyOutVal = (float) $earlyOut; @endphp
                                <select name="early_out_weight" class="form-control form-control-sm">
                                    <option value="1.00" {{ $earlyOutVal == 1.0 ? 'selected' : '' }}>1.00 (No Deduction)</option>
                                    <option value="0.50" {{ $earlyOutVal == 0.5 ? 'selected' : '' }}>0.50 (Half Day Credit)</option>
                                    <option value="0.00" {{ $earlyOutVal == 0.0 ? 'selected' : '' }}>0.00 (Full Deduction)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Late Penalty Frequency (Every N Days)</label>
                                <input type="number" min="1" max="31" name="late_frequency" class="form-control form-control-sm"
                                       value="{{ old('late_frequency', $payrollSetting->late_frequency ?? '') }}"
                                       placeholder="e.g. 3">
                                <small class="text-muted" style="font-size:10px;">Below frequency, late marks give full day credit.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Early Out Penalty Frequency (Every N Days)</label>
                                <input type="number" min="1" max="31" name="early_out_frequency" class="form-control form-control-sm"
                                       value="{{ old('early_out_frequency', $payrollSetting->early_out_frequency ?? '') }}"
                                       placeholder="e.g. 3">
                                <small class="text-muted" style="font-size:10px;">Below frequency, early out gives full day credit.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Leave Credit (Paid Day)</label>
                                @php $leaveW = old('leave_weight', $payrollSetting->leave_weight ?? 1); $leaveVal = (float) $leaveW; @endphp
                                <select name="leave_weight" class="form-control form-control-sm">
                                    <option value="1.00" {{ $leaveVal == 1.0 ? 'selected' : '' }}>1.00 (Paid Leave)</option>
                                    <option value="0.00" {{ $leaveVal == 0.0 ? 'selected' : '' }}>0.00 (Unpaid Leave)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Half Day Credit (Paid Day)</label>
                                @php $halfW = old('halfday_weight', $payrollSetting->halfday_weight ?? 0.5); $halfVal = (float) $halfW; @endphp
                                <select name="halfday_weight" class="form-control form-control-sm">
                                    <option value="0.50" {{ $halfVal == 0.5 ? 'selected' : '' }}>0.50 (Half Day)</option>
                                    <option value="1.00" {{ $halfVal == 1.0 ? 'selected' : '' }}>1.00 (Full Day)</option>
                                    <option value="0.00" {{ $halfVal == 0.0 ? 'selected' : '' }}>0.00 (No Pay)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size:11px;">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="font-size:11px;"><i class="fa fa-save mr-1"></i> Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Salary Modal -->
<div class="modal fade" id="salaryModal" tabindex="-1" role="dialog" aria-labelledby="salaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border-radius:3px; overflow:hidden;">
            <form method="post" action="{{ url('payroll/staff?month='.$month.'&year='.$year) }}">
                @csrf
                <input type="hidden" name="action" value="salary">
                <div class="modal-header py-2" style="background:#002C54; color:#fff;">
                    <h5 class="modal-title font-weight-bold" style="font-size:13px;"><i class="fa fa-cog mr-1"></i> Set Monthly Salary for Staff</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-bordered table-sm mb-0" style="font-size:11.5px;">
                            <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Staff Name & Attendance UID</th>
                                    <th>Designation / Role</th>
                                    <th style="width:200px;" class="text-right">Monthly Salary (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sr = 1; @endphp
                                @foreach($staff as $member)
                                    @php
                                        $roleName = $rolesById[$member->role_id]->name ?? '-';
                                        $old = $member->salary ?? '';
                                    @endphp
                                    <tr>
                                        <td>{{ $sr++ }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')) }}</div>
                                            <div class="small text-muted">{{ trim((string)($member->attendance_unique_id ?? '')) !== '' ? $member->attendance_unique_id : ('USR-' . $member->id) }}</div>
                                        </td>
                                        <td><span class="badge badge-secondary" style="font-size:10px;">{{ $roleName }}</span></td>
                                        <td class="text-right">
                                            <input type="number" step="0.01" min="0" name="salary[{{ $member->id }}]" class="form-control form-control-sm text-right font-weight-bold" value="{{ $old }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="font-size:11px;">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="font-size:11px;"><i class="fa fa-save mr-1"></i> Save Salaries</button>
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
    function fetchPayrollData(page) {
        if (page) currentPage = page;
        var month = $('#monthSelect').val();
        var year = $('#yearSelect').val();
        var roleId = $('#roleSelect').val();
        var status = $('#statusSelect').val();
        var search = $('#liveSearchInput').val();
        var perPageVal = $('#perPageSelect').val();
        var searchUid = $('#col_filter_uid').val();
        var searchName = $('#col_filter_name').val();

        $('#payrollRowsContainer').css('opacity', '0.5');

        $.ajax({
            url: '{{ url("payroll/staff") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                month: month,
                year: year,
                role_id: roleId,
                status: status,
                search: search,
                search_uid: searchUid,
                search_name: searchName,
                per_page: perPageVal,
                page: currentPage
            },
            success: function (res) {
                $('#payrollRowsContainer').html(res.html).css('opacity', '1');

                // Update pagination info
                var p = res.pagination;
                currentPage = p.current_page;
                totalPages = p.total_pages;
                totalRecords = p.total_records;

                $('#paginationInfoText').text('Showing ' + p.from + ' to ' + p.to + ' of ' + p.total_records + ' records');
                renderPagination(p.current_page, p.total_pages);

                // Update KPIs
                if (res.kpis) {
                    $('#kpiStaffCount').text(res.kpis.total_staff);
                    $('#kpiGrossSalary').text('₹' + res.kpis.salary_till_now);
                    $('#kpiDeductions').text('₹' + res.kpis.total_deductions);
                    $('#kpiBalanceAmount').text('₹' + res.kpis.balance_amount);
                    $('#kpiPaidAmount').text('₹' + res.kpis.paid_amount);
                }
            },
            error: function () {
                $('#payrollRowsContainer').css('opacity', '1');
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
        if (page > 0) fetchPayrollData(page);
    });

    // Period Change
    $('#monthSelect, #yearSelect').on('change', function () {
        var m = $('#monthSelect').val();
        var y = $('#yearSelect').val();
        window.location.href = '{{ url("payroll/staff") }}?month=' + m + '&year=' + y;
    });

    $('#roleSelect, #statusSelect, #perPageSelect').on('change', function () {
        currentPage = 1;
        fetchPayrollData(1);
    });

    // KPI Card Click Filter
    $('.admission-kpi-card').on('click', function () {
        var status = $(this).data('filter-status');
        if (status) {
            $('#statusSelect').val(status);
            currentPage = 1;
            fetchPayrollData(1);
        }
    });

    // Debounced Search Inputs
    $('#liveSearchInput, #col_filter_uid, #col_filter_name').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            currentPage = 1;
            fetchPayrollData(1);
        }, 250);
    });

    // Reset In-Column Filters
    $('#btnClearExcelFilters').on('click', function () {
        $('#col_filter_uid, #col_filter_name, #liveSearchInput').val('');
        currentPage = 1;
        fetchPayrollData(1);
    });

    // Modals Data Handlers
    $(document).on('click', '.js-pay-salary', function () {
        var button = $(this);
        var balance = button.data('balance');
        $('#paymentUniqueId').val(button.data('unique-id'));
        $('#paymentStaffName').val(button.data('name'));
        $('#paymentPayable').val('₹' + button.data('payable'));
        $('#paymentAlreadyPaid').val('₹' + button.data('paid'));
        $('#paymentBalance').val('₹' + balance);
        $('#paymentAmount').val(balance).attr('max', balance);
    });

    $(document).on('click', '.js-payment-history', function () {
        var button = $(this);
        var payments = button.data('payments') || [];
        var payable = parseFloat(button.data('payable') || 0);
        var totalPaid = parseFloat(button.data('paid') || 0);
        var staffLabel = button.data('name') + ' (' + button.data('unique-id') + ')';
        var rows = '';
        var total = 0;
        $.each(payments, function (index, payment) {
            var amount = parseFloat(payment.amount || 0);
            total += amount;
            rows += '<tr>' +
                '<td>' + (payment.number || (index + 1)) + '</td>' +
                '<td>' + $('<div>').text(payment.date || '-').html() + '</td>' +
                '<td>' + $('<div>').text(payment.recorded_by || '-').html() + '</td>' +
                '<td class="text-right font-weight-bold">₹' + amount.toFixed(2) + '</td>' +
                '<td class="text-center"><button type="button" class="btn btn-xs btn-primary js-edit-payment" ' +
                'data-payment-id="' + payment.id + '" data-amount="' + amount.toFixed(2) + '" ' +
                'data-paid-at="' + (payment.paid_at_input || '') + '" data-payable="' + payable.toFixed(2) + '" ' +
                'data-total-paid="' + totalPaid.toFixed(2) + '" ' +
                'title="Edit Payment"><i class="fa fa-edit"></i></button></td>' +
                '</tr>';
        });
        if (!rows) {
            rows = '<tr><td colspan="5" class="text-center text-muted py-3">No payments recorded yet.</td></tr>';
        }
        $('#historyStaff').text(staffLabel);
        $('#historyPayable').text('₹' + button.data('payable'));
        $('#historyPaid').text('₹' + button.data('paid'));
        $('#historyBalance').text('₹' + button.data('balance'));
        $('#historyRows').html(rows);
        $('#historyTableTotal').text('₹' + total.toFixed(2));
        $('#historyExcelLink').attr('href', button.data('excel-url'));
        $('#paymentHistoryEditPanel').hide();
        $('#paymentHistoryListPanel, #paymentHistoryFooter').show();
    });

    $(document).on('click', '.js-edit-payment', function (event) {
        event.preventDefault();
        var button = $(this);
        var currentAmount = parseFloat(button.data('amount') || 0);
        var totalPaid = parseFloat(button.data('total-paid') || 0);
        var payable = parseFloat(button.data('payable') || 0);
        var otherPayments = Math.max(0, totalPaid - currentAmount);
        var maximum = Math.max(0, payable - otherPayments);

        $('#editPaymentId').val(button.data('payment-id'));
        $('#editPaymentAmount').val(currentAmount.toFixed(2)).data('maximum', maximum);
        $('#editPaymentDate').val(button.data('paid-at'));
        $('#editPaymentMaximum').text('₹' + maximum.toFixed(2));
        $('#editPaymentPayable').text('₹' + payable.toFixed(2));
        $('#editOtherPayments').text('₹' + otherPayments.toFixed(2));

        $('#paymentHistoryListPanel, #paymentHistoryFooter').hide();
        $('#paymentHistoryEditPanel').show();
    });

    $(document).on('click', '.js-payment-edit-back', function () {
        $('#paymentHistoryEditPanel').hide();
        $('#paymentHistoryListPanel, #paymentHistoryFooter').show();
    });
});
</script>
@endpush