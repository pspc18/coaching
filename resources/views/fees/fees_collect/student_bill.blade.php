@php
$getFeesType = Helper::feesType();
$getPaymentMode = Helper::getPaymentMode();
$firstAmount = $data['FeesAssign']->total_amount ?? 0;
$fees_assign_details = DB::table('fees_assign_details')
            ->where('session_id',$data['session_id'])
            ->where('branch_id',Session::get('branch_id'))
            ->where('fees_assign_id',$data['FeesAssign']->id)
            ->whereNull('deleted_at')->get();
$session = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();

// Compute stats matching admissionStats
$stat_total_assigned = 0;
$stat_total_discount = 0;
$stat_total_paid = 0;
$stat_total_paid_fine = 0;
$stat_total_pending_fine = 0;
$stat_total_pending = 0;
$headArray = [];

if(!empty($fees_assign_details)) {
    foreach($fees_assign_details as $f) {
        $fg = DB::table('fees_group')->whereNull('deleted_at')->where('id', $f->fees_group_id)->first();
        $disc = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id', $f->admission_id)->where('fees_group_id', $fg->id ?? 0)->whereIn('status',[0,1])->sum('discount');
        $pd = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id', $f->admission_id)->where('fees_group_id', $fg->id ?? 0)->whereIn('status',[0,1])->sum('total_amount');
        $pdf = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id', $f->admission_id)->where('fees_group_id', $fg->id ?? 0)->whereIn('status',[0,1])->sum('installment_fine');
        $p_amt = (($f->fees_group_amount) - ($f->discount)) - ($pd);
        
        $stat_total_assigned += $f->fees_group_amount ?? 0;
        $stat_total_discount += ($f->discount ?? 0) + $disc;
        $stat_total_paid += ($pd ?? 0) - $disc;
        $stat_total_paid_fine += $pdf ?? 0;
        
        $is_overdue = ($p_amt != 0 && isset($f->installment_due_date) && $f->installment_due_date < date('Y-m-d'));
        $fine_val = $is_overdue ? ($p_amt * ($f->installment_fine ?? 0))/100 : 0;
        $stat_total_pending_fine += $fine_val;
        
        if($p_amt > 0) {
            $stat_total_pending += $p_amt;
        }
        $headArray[] = ['pending_by_group_id' => $fg->id ?? '', 'pending' => ($p_amt ?? 0)];
    }
}
$nextSlipNo = $data['runningReceiptNo'] ?? ('REC-' . sprintf('%004s', ($data['BillCounter']['counter'] ?? 0) + 1));
$feesSetting = $data['feesSetting'] ?? \App\Models\FeesSetting::getSetting(Session::get('branch_id'), Session::get('session_id'));
@endphp

<style>
/* ==========================================================================
   Student Bill Desk (100% Height, Fixed Bottom Action Bar, Fast-POS Features)
   Strictly aligned with admissionView design system
   ========================================================================== */
.bill-desk-wrapper {
    font-size: 11.5px;
    color: #0f172a;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.bill-active-student {
    font-size: 11px;
    font-weight: 600;
    color: #f1f5f9;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 2px;
    padding: 2px 7px;
    display: inline-flex;
    align-items: center;
    letter-spacing: .02em;
}

/* 2-Column Split: Col-1 (Student Photo) & Col-11 (3 Rows: Stats + Nav + Fast Pay) */
.bill-header-row {
    margin-bottom: 3px;
    flex-shrink: 0;
}
.student-profile-photo-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.student-avatar-img {
    max-height: 96px;
    max-width: 100%;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 2px;
}
@media (max-width: 768px) {
    .bill-header-row .col-1 {
        flex: 0 0 16.666667%;
        max-width: 16.666667%;
    }
    .bill-header-row .col-11 {
        flex: 0 0 83.333333%;
        max-width: 83.333333%;
    }
}

/* Statistics Bar (High-density 1-line layout) */
.bill-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    margin-bottom: 3px;
    flex-shrink: 0;
}
@media (max-width: 768px) {
    .bill-stats-row {
        grid-template-columns: repeat(1, 1fr);
    }
}
.bill-stat-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 8px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}
.stat-lbl {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 0;
}
.stat-val {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    line-height: 1;
}
.stat-val-paid {
    color: #15803d;
}
.stat-val-due {
    color: #b91c1c;
}

/* Terminal Nav Header Bar */
.bill-nav-bar {
    background: #002342;
    color: #ffffff;
    border-radius: 2px 2px 0 0;
    padding: 3px 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 4px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    flex-shrink: 0;
}
.bill-tabs-group {
    display: flex;
    align-items: center;
    gap: 3px;
}
.bill-tab-btn {
    border: none;
    background: rgba(255,255,255,.08);
    color: #f1f5f9;
    padding: 4px 10px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 3px;
    cursor: pointer;
    line-height: 1.4;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.bill-tab-btn:hover {
    background: rgba(255,255,255,.18);
    color: #ffffff;
}
.bill-tab-btn.active {
    background: #002C54;
    color: #38bdf8;
    border: 1px solid rgba(56, 189, 248, 0.5);
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}

/* Academic Session Switcher */
.tabs_listing {
    display: flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.tabs_listing li.tab {
    border: 1px solid rgba(255,255,255,.25);
    color: #ffffff;
    padding: 2px 8px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 10px;
    cursor: pointer;
    background: rgba(255,255,255,.12);
    transition: all .15s;
    line-height: 1.4;
    display: inline-flex;
    align-items: center;
}
.tabs_listing li.tab:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #38bdf8;
}
#active_li, .tabs_listing li.tab#active_li {
    background: #38bdf8 !important;
    color: #002342 !important;
    border-color: #38bdf8 !important;
    font-weight: 700;
}

/* Fast-Pay Action Strip */
.fastpay-action-strip {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 6px;
    margin-top: 3px;
    margin-bottom: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    flex-shrink: 0;
}

/* Dash Table - Exact 1:1 with admissionView */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead th {
    background: #002C54;
    color: #ffffff;
    padding: 5px 6px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    vertical-align: middle;
}
.dash-table tbody td {
    padding: 4px 6px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11px;
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
.dash-table tbody tr.row-selected td {
    background: #e0f2fe !important;
}
.dash-table tfoot td {
    background: #e2e8f0;
    font-weight: 700;
    font-size: 11px;
    padding: 5px 6px;
    color: #002C54;
    border-top: 2px solid #cbd5e1;
}

/* Badges */
.badge-pending {
    display: inline-block;
    padding: 1px 5px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 2px;
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    white-space: nowrap;
}

/* Form Controls - 1:1 with admissionView */
.dash-form-control {
    height: 25px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    color: #0f172a;
    width: 100%;
    outline: none;
    transition: all .15s;
}
.dash-form-control:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 1px #002C54;
}
.dash-form-lbl {
    font-size: 9.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 1px;
    display: block;
}

/* Standard Buttons - 1:1 match with admissionView */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 26px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    line-height: 1;
    white-space: nowrap;
    gap: 4px;
}
.dash-btn-light {
    background: #fff;
    color: #002C54;
    border-color: #cbd5e1;
}
.dash-btn-light:hover {
    background: #f1f5f9;
}
.dash-btn-outline {
    background: transparent;
    color: #002C54;
    border-color: #002C54;
}
.dash-btn-outline:hover {
    background: #002C54;
    color: #fff;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.dash-btn-primary:hover {
    background: #001f3d;
    color: #ffffff;
}

/* Small Table Action Buttons */
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

/* Payment Mode Chips */
.payment-chips-row {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.pos-mode-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    cursor: pointer;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    line-height: 1;
}
.pos-mode-btn:hover {
    border-color: #002C54;
    background: #f8fafc;
}
.pos-mode-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0,44,84,.25);
}

/* Smart Denomination Quick Chips */
.btn-denom {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 3px 8px;
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    cursor: pointer;
    transition: all .12s;
    line-height: 1.2;
}
.btn-denom:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Settlement Adjustments Card (Theme Aligned) */
.settlement-adjustments-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 7px 10px;
}
.settlement-adjustments-card .input-group {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    width: 100% !important;
}
.settlement-adjustments-card .input-group-prepend {
    display: flex !important;
    flex-shrink: 0 !important;
    margin-right: -1px !important;
    z-index: 2;
}
.settlement-adjustments-card .input-group-append {
    display: flex !important;
    flex-shrink: 0 !important;
    margin-left: -1px !important;
    z-index: 2;
}
.settlement-adjustments-card .input-group-text {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    font-size: 11px !important;
    height: 26px !important;
    padding: 0 8px !important;
    border-radius: 2px 0 0 2px !important;
    line-height: 1 !important;
}
.settlement-adjustments-card .form-control {
    position: relative !important;
    flex: 1 1 auto !important;
    width: 1% !important;
    min-width: 0 !important;
    height: 26px !important;
    padding: 2px 8px !important;
    font-size: 11.5px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    z-index: 1;
}
.settlement-adjustments-card .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 1px #002C54 !important;
    z-index: 3 !important;
}
.btn-quick-disc {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 26px !important;
    padding: 0 8px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-left: none !important;
    color: #475569 !important;
    border-radius: 0 !important;
    cursor: pointer;
    transition: all .12s ease;
    white-space: nowrap;
}
.btn-quick-disc:hover {
    background: #002C54 !important;
    color: #ffffff !important;
    border-color: #002C54 !important;
}
.btn-quick-disc:last-child {
    border-top-right-radius: 2px !important;
    border-bottom-right-radius: 2px !important;
}
.btn-waive-fine {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 26px !important;
    padding: 0 8px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-left: none !important;
    color: #dc2626 !important;
    border-radius: 0 2px 2px 0 !important;
    cursor: pointer;
    transition: all .12s ease;
    white-space: nowrap;
}
.btn-waive-fine:hover {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

/* Cash Change Calculator */
.cash-calc-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 3px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

/* Fixed Bottom Settlement Bar */
.bill-checkout-bar {
    background: #002342;
    color: #ffffff;
    border-radius: 3px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 -2px 8px rgba(0,0,0,.18);
    flex-shrink: 0;
    z-index: 10;
    margin-top: auto;
}

/* Keyboard hint badges */
.kbd-hint {
    font-size: 9px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.28);
    color: #f1f5f9;
    padding: 1px 4px;
    border-radius: 2px;
    font-family: monospace;
    margin-left: 2px;
}

/* Modals */
.arise-modal .modal-content {
    border-radius: 2px;
    border: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    overflow: hidden;
}
.arise-modal .modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 7px 12px;
}
.arise-modal .modal-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}
.arise-modal .close {
    color: #ffffff;
    opacity: 0.85;
}

/* Floating Quick POS Draggable Widget & Blurred Backdrop */
.quick-pos-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 35, 66, 0.35);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 1050;
    display: none;
}
.pos-terminal-blurred {
    filter: blur(4px);
    pointer-events: none;
    user-select: none;
    transition: filter 0.2s ease;
}
.quick-pos-widget {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    max-width: 95vw;
    background: #ffffff;
    border-radius: 4px;
    box-shadow: 0 15px 45px rgba(0, 44, 84, 0.4), 0 0 0 1px rgba(0, 44, 84, 0.15);
    border: 2px solid #002C54;
    z-index: 1055;
    overflow: hidden;
    display: none;
}
.quick-pos-header {
    background: #002342;
    color: #ffffff;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #38bdf8;
}
.quick-pos-title {
    font-size: 12.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
    letter-spacing: .02em;
}
.quick-pos-body {
    padding: 14px 16px;
    background: #ffffff;
}
.quick-pos-due-box {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    border-radius: 3px;
    padding: 8px 12px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.quick-pos-due-lbl {
    font-size: 10px;
    font-weight: 700;
    color: #991b1b;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.quick-pos-due-val {
    font-size: 18px;
    font-weight: 800;
    color: #b91c1c;
}
.quick-pos-input-group {
    position: relative;
    margin-bottom: 6px;
}
.quick-pos-input-group .input-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 800;
    font-size: 16px;
    color: #002C54;
}
.quick-pos-input {
    width: 100%;
    height: 42px;
    padding-left: 28px;
    padding-right: 12px;
    font-size: 19px;
    font-weight: 800;
    color: #002C54;
    border: 1.5px solid #94a3b8;
    border-radius: 3px;
    text-align: right;
    transition: border-color .15s, box-shadow .15s;
}
.quick-pos-input:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 3px rgba(0, 44, 84, 0.2);
    outline: none;
}
.quick-pos-footer {
    padding: 10px 16px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.quick-pos-confirm-btn {
    width: 100%;
    height: 40px;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    border-radius: 3px;
    font-size: 13.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    cursor: pointer;
    transition: background .15s, transform .05s;
    box-shadow: 0 2px 6px rgba(0, 44, 84, .3);
}
.quick-pos-confirm-btn:hover {
    background: #001f3d;
    color: #ffffff;
}
.quick-pos-confirm-btn:active {
    transform: scale(0.99);
}
</style>

<div class="bill-desk-wrapper">
    <!-- Top Header Strip: Col-1 (Student Photo Merged across 3 Rows) + Col-11 (Stats, Nav & Fast Pay) -->
    <div class="row no-gutters bill-header-row align-items-stretch">
        <!-- Col-1: Student Profile Image -->
        <div class="col-1 pr-1 d-flex">
            <div class="student-profile-photo-card" title="{{ $data['stuData']['first_name'] ?? '' }} {{ $data['stuData']['last_name'] ?? '' }} (#{{ $data['stuData']['admissionNo'] ?? '' }})">
                @php
                    $userImage = !empty($data['stuData']['image']) 
                        ? env('IMAGE_SHOW_PATH').'profile/'.$data['stuData']['image'] 
                        : env('IMAGE_SHOW_PATH').'default/user_image.jpg';
                @endphp
                <img src="{{ $userImage }}" class="student-avatar-img" alt="Student Photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH') }}default/user_image.jpg'">
            </div>
        </div>

        <!-- Col-11: 3 Merged Rows (Stats Row + Terminal Nav Header Bar + Fast Pay Action Strip) -->
        <div class="col-11 d-flex flex-column justify-content-start">
            <!-- Row 1: Top Statistics Bar (Ultra-compact 1-line high-density layout) -->
            <div class="bill-stats-row">
                <div class="bill-stat-card">
                    <div class="stat-lbl">
                        <i class="fa fa-folder-open-o text-muted"></i>
                        <span>Total Assigned</span>
                    </div>
                    <div class="stat-val">₹ {{ number_format($stat_total_assigned, 2) }}</div>
                </div>
                <div class="bill-stat-card">
                    <div class="stat-lbl">
                        <i class="fa fa-check-circle text-success"></i>
                        <span>Paid to Date</span>
                    </div>
                    <div class="stat-val stat-val-paid">₹ {{ number_format($stat_total_paid, 2) }}</div>
                </div>
                <div class="bill-stat-card">
                    <div class="stat-lbl">
                        <i class="fa fa-exclamation-circle text-danger"></i>
                        <span>Net Outstanding Due</span>
                    </div>
                    <div class="stat-val stat-val-due">₹ {{ number_format($stat_total_pending, 2) }}</div>
                </div>
            </div>

            <!-- Row 2: Terminal Nav Header Bar -->
            <div class="bill-nav-bar">
                <div class="bill-tabs-group">
                    <button type="button" class="bill-tab-btn active" data-tab="tab_payment_desk">
                        <i class="fa fa-credit-card mr-1"></i> 1. Payment Counter
                    </button>
                    <button type="button" class="bill-tab-btn" data-tab="tab_installments">
                        <i class="fa fa-calendar-check-o mr-1"></i> 2. Installments
                    </button>
                    <button type="button" class="bill-tab-btn" data-tab="tab_history">
                        <i class="fa fa-history mr-1"></i> 3. Invoices ({{ count($data['FeesDetailsInvoices'] ?? []) }})
                    </button>
                    <button type="button" class="bill-tab-btn" id="btn_open_quick_pos" style="background: #16a34a; color: #ffffff; border: 1px solid #15803d; font-weight: 700;" title="Quick POS Settlement Modal (Alt+Q)">
                        <i class="fa fa-bolt text-warning mr-1"></i> Quick POS <kbd class="kbd-hint" style="background: rgba(0,0,0,0.25); color: #fff; font-size: 8.5px; padding: 1px 3px;">Alt+Q</kbd>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="bill-active-student" title="Active Student">
                        <i class="fa fa-user-circle mr-1 text-info"></i>
                        <span>{{ $data['stuData']['first_name'] ?? '' }} {{ $data['stuData']['last_name'] ?? '' }}</span>
                        <span class="badge badge-secondary ml-1" style="font-size: 9px; font-weight: 600;">#{{ $data['stuData']['admissionNo'] ?? '' }}</span>
                    </span>

                    <!-- Academic Session Switcher -->
                    <div>
                        <ul class="tabs_listing mb-0">
                            @if(count($data['sessions']) != 0)
                                @php
                                    $sessions = $data['sessions'];
                                @endphp
                                @foreach($sessions as $item)
                                    <li class="tab" id="{{ $data['session_id'] == $item->id ? 'active_li' : '' }}" data-id="{{ $item->id ?? '' }}" data-unique_system_id="{{ $data['stuData']['unique_system_id'] ?? '' }}">
                                        {{ $item->from_year ?? '' }}-20{{ $item->to_year ?? '' }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Row 3: Fast-Pay Action Strip -->
            <div class="fastpay-action-strip" id="header_fastpay_strip">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <span class="dash-form-lbl mb-0 mr-1"><i class="fa fa-bolt text-warning mr-1"></i> Fast Pay:</span>
                    @if($stat_total_pending > 0)
                        <button type="button" class="dash-btn" id="btn_pay_full_due" data-amount="{{ $stat_total_pending }}" style="height: 25px; font-size: 10px; background: #dc2626; color: #ffffff; border-color: #b91c1c; font-weight: 700;" title="Shortcut: Alt+F">
                            <i class="fa fa-check-square-o mr-1"></i> Pay Full: ₹{{ number_format($stat_total_pending, 2) }} <kbd class="kbd-hint">Alt+F</kbd>
                        </button>
                    @endif
                    <button type="button" class="dash-btn dash-btn-light border" id="btn_select_all_heads" style="height: 25px; font-size: 10px;">
                        <i class="fa fa-check mr-1"></i> Select All
                    </button>
                    <button type="button" class="dash-btn dash-btn-light border" id="btn_clear_all_heads" style="height: 25px; font-size: 10px;">
                        <i class="fa fa-times mr-1"></i> Clear
                    </button>
                </div>

                <!-- Aggregate Amount Input & Auto Allocate Button -->
                <div class="d-flex align-items-center gap-1">
                    <span class="dash-form-lbl mb-0 font-weight-bold" title="Enter any custom amount to auto-distribute across pending fee heads">
                        <i class="fa fa-magic text-primary mr-1"></i> Lumpsum:
                    </span>
                    <div style="position: relative; width: 120px;">
                        <span style="position: absolute; left: 7px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b; font-size: 11px;">₹</span>
                        <input type="number" step="any" value="" id="aggregate_amount" class="dash-form-control" style="padding-left: 18px; font-weight: 700; height: 25px;" placeholder="0.00" autocomplete="off" />
                    </div>
                    <button type="button" id="btn_auto_allocate" class="dash-btn dash-btn-primary" style="height: 25px; font-size: 10px; padding: 0 8px; background: #002C54; border-color: #002C54; font-weight: 600;" title="Auto distribute entered amount across oldest pending heads (Alt+A)">
                        <i class="fa fa-bolt mr-1 text-warning"></i> Auto Allocate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fallback when no fees assigned -->
    <div class="col-md-12 not_found_div" style="display:none;">
        <div class="text-center py-4 text-muted">
            <i class="fa fa-exclamation-triangle fa-2x text-warning mb-2 d-block"></i>
            Please Assign the Fees for this Student!
        </div>
    </div>

    <div id="notfound" style="flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden;">
        <!-- ================= TAB 1: PAYMENT COLLECTION DESK ================= -->
        <div class="pos-tab-content" id="tab_payment_desk" style="height: 100%; display: flex; flex-direction: column; overflow: hidden;">
            <form id="myForm" method="post" enctype="multipart/form-data" style="height: 100%; display: flex; flex-direction: column; overflow: hidden;">
                @csrf
                <input type="hidden" id="admission_id" name="admission_id" value="{{$data['stuData']['id']}}" />
                <input type="hidden" id="advance_payment" name="advance_payment" value="no" />
                <input type="hidden" id="session_id" name="session_id" value="{{ $data['session_id'] ?? Session::get('session_id') }}" />
                <input type="hidden" id="email" name="email" value="{{$data['stuData']['email']}}" />
                <input type="hidden" id="mobile" name="mobile" value="{{$data['stuData']['mobile']}}" />
                <input type="hidden" id="name" name="name" value="{{$data['stuData']['first_name']}}" />
                <input type="hidden" id="class_type_id1" name="class_type_id" value="{{$data['stuData']['class_type_id']}}" />
                <input type="hidden" name="slip_no" value="{{sprintf('%004s', $data['BillCounter']['counter']+1) ?? ''}}" >

                <!-- Scrollable Body (Only middle content scrolls, checkout bar is fixed at bottom!) -->
                <div class="bill-scrollable-body" style="flex: 1; min-height: 0; overflow-y: auto; padding-right: 2px;">

                    <!-- Fee Heads Matrix Table -->
                    <div class="border rounded mb-2 overflow-hidden" id="add_head_row" style="border-radius: 2px;">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th style="width: 38%;">Fee Head Description</th>
                                    <th style="width: 15%; text-align: right;">Assigned (₹)</th>
                                    <th style="width: 15%; text-align: right;">Paid Earlier (₹)</th>
                                    <th style="width: 16%; text-align: right;">Balance Due (₹)</th>
                                    <th style="width: 16%; text-align: right;">Pay Amount (₹) *</th>
                                </tr>
                            </thead>
                            <tbody id="head_row">
                                @if(!empty($fees_assign_details))
                                    @foreach($fees_assign_details as $key=>$fees)
                                        @php
                                            $feesGroup = DB::table('fees_group')->whereNull('deleted_at')->where('id',$fees->fees_group_id)->first();
                                            $result = DB::table('fees_detail')
                                                    ->selectRaw('SUM(total_amount) AS paid, SUM(installment_fine) AS paid_fine')
                                                    ->whereNull('deleted_at')
                                                    ->where('admission_id', $fees->admission_id)
                                                    ->where('fees_group_id', $feesGroup->id)
                                                    ->whereIn('status',[0,1])
                                                    ->first();
                                            $paid = $result->paid ?? 0;
                                            $paid_fine = $result->paid_fine ?? 0;
                                            $paids = $paid + $paid_fine;
                                            $pending_amount = (($fees->fees_group_amount) - ($fees->discount)) - ($paid);
                                            $dueDate = $fees->installment_due_date ?? null;
                                            $is_overdue = ($pending_amount > 0 && !empty($dueDate) && $dueDate < date('Y-m-d'));
                                            $days_overdue = 0;
                                            $billable_days = 0;
                                            if ($is_overdue) {
                                                $days_overdue = max(0, (int) floor((strtotime(date('Y-m-d')) - strtotime($dueDate)) / 86400));
                                                $graceDays = (int) ($feesSetting->fine_grace_days ?? 0);
                                                $billable_days = max(0, $days_overdue - $graceDays);
                                            }
                                        @endphp
                                        @if($fees->fees_group_amount > $paids)
                                            <tr id="group_{{ $feesGroup->id }}" class="group_group">
                                                <td>
                                                    <label class="d-flex align-items-center mb-0 pointer" for="checkbox_{{ $key }}">
                                                        <input type="checkbox" class="selected_head pointer" id="checkbox_{{ $key }}" name="selected_head[]" 
                                                            data-fees_assign_detail_id="{{ $fees->id }}" 
                                                            data-pending_amount="{{ $pending_amount ?? 0 }}"
                                                            data-is_overdue="{{ $is_overdue ? 1 : 0 }}"
                                                            data-days_overdue="{{ $days_overdue }}"
                                                            data-billable_days="{{ $billable_days }}"
                                                            data-due_date="{{ !empty($dueDate) ? date('d-m-Y', strtotime($dueDate)) : '' }}"
                                                            value="{{ $feesGroup->id }}" style="margin-right: 6px;">
                                                        <span class="font-weight-bold" style="font-size: 11.5px; color: #002C54;">{{ $feesGroup->name ?? '' }}</span>
                                                    </label>
                                                    @if($is_overdue)
                                                        <div class="ml-4">
                                                            <span class="badge badge-danger p-0 px-1" style="font-size: 9px;">
                                                                Overdue (Due: {{ date('d-m-Y', strtotime($dueDate)) }}) &bull; {{ $days_overdue }}d ago
                                                            </span>
                                                        </div>
                                                    @elseif(!empty($dueDate))
                                                        <div class="ml-4 text-muted" style="font-size: 9.5px;">
                                                            Due: {{ date('d-m-Y', strtotime($dueDate)) }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="text-align: right; font-weight: 600;">
                                                    ₹ {{ number_format($fees->fees_group_amount ?? 0, 2) }}
                                                </td>
                                                <td style="text-align: right; color: #15803d; font-weight: 600;">
                                                    ₹ {{ number_format(($paid - ($fees->discount ?? 0)) > 0 ? ($paid - ($fees->discount ?? 0)) : $paid, 2) }}
                                                </td>
                                                <td style="text-align: right;">
                                                    <span class="badge-pending" id="pending_by_group_id_{{ $fees->id ?? '' }}" data-pending_amount="{{ $pending_amount ?? '0' }}">
                                                        ₹ {{ number_format($pending_amount ?? 0, 2) }}
                                                    </span>
                                                </td>
                                                <td style="text-align: right;">
                                                    <input type="tel" class="dash-form-control amount_get aggregate_{{ $feesGroup->id }}" placeholder="0.00" id="amount_{{ $fees->id }}" name="amount[]" onkeypress="javascript:return isNumber(event)" required style="height: 25px; font-weight: 700; text-align: right;">
                                                    <!-- Hidden inputs for head-level discount & fine to preserve backend data integrity -->
                                                    <input type="hidden" class="head_discount_input" id="discount_{{ $fees->id }}" name="discount_amount[]" value="0" disabled>
                                                    <input type="hidden" class="head_fine_input" id="fine_{{ $fees->id }}" name="fine[]" value="0" disabled>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Policy-Driven Settlement Adjustments: Late Fine & Concession/Discount -->
                    <!-- Policy-Driven Settlement Adjustment: Late Fine -->
                    <div class="settlement-adjustments-card mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="dash-form-lbl mb-0 font-weight-bold text-danger">
                                <i class="fa fa-balance-scale mr-1"></i> Late Fine (₹)
                            </label>
                            <span class="badge badge-light border text-danger" id="fine_policy_badge" style="font-size: 9px; text-transform: uppercase;">
                                Policy: {{ $feesSetting->fine_mode ?? 'fixed' }}
                            </span>
                        </div>
                        <div class="input-group input-group-sm flex-nowrap" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: stretch !important; width: 100% !important;">
                            <div class="input-group-prepend" style="display: flex !important; flex-shrink: 0 !important; margin-right: -1px !important;">
                                <span class="input-group-text font-weight-bold text-danger" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; height: 26px !important; padding: 0 8px !important;">₹</span>
                            </div>
                            <input type="number" step="any" min="0" id="settlement_fine" class="form-control font-weight-bold text-danger" 
                                   style="flex: 1 1 auto !important; width: 1% !important; min-width: 0 !important; height: 26px !important;"
                                   value="0.00" autocomplete="off" {{ !$feesSetting->allow_fine_waiver ? 'readonly' : '' }}>
                            @if($feesSetting->allow_fine_waiver)
                                <div class="input-group-append" style="display: flex !important; flex-shrink: 0 !important; margin-left: -1px !important;">
                                    <button type="button" id="btn_waive_fine" class="btn-waive-fine" title="Waive late fine to ₹0.00">
                                        <i class="fa fa-times-circle mr-1"></i> Waive Fine
                                    </button>
                                </div>
                            @else
                                <div class="input-group-append" style="display: flex !important; flex-shrink: 0 !important; margin-left: -1px !important;">
                                    <span class="input-group-text bg-light text-muted" style="border-radius: 0 2px 2px 0; font-size: 9.5px;" title="Fine waiver disabled in settings">
                                        <i class="fa fa-lock mr-1"></i> Locked
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 9.5px; min-height: 16px;">
                            <span id="fine_calc_hint">Select fee heads to evaluate due date &amp; late fine</span>
                            <span id="fine_waived_badge" class="badge badge-warning" style="display: none; font-size: 8.5px;">Waived</span>
                        </div>
                        <input type="hidden" id="fine_was_waived" name="fine_was_waived" value="0">
                        <input type="hidden" id="settlement_discount" value="0">
                    </div>

                    <!-- Payment Mode Selection (Segmented POS Buttons) -->
                    <div class="mb-1">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="dash-form-lbl mb-0">Select Payment Mode * <kbd class="kbd-hint">Alt+1-4</kbd></span>
                            <button type="button" class="dash-btn dash-btn-light border" id="btn_show_upi_qr" style="height: 22px; font-size: 9.5px; padding: 0 6px; display: none;" title="Show dynamic QR code for parent to scan and pay">
                                <i class="fa fa-qrcode text-primary mr-1"></i> Show Parent QR
                            </button>
                        </div>
                        <div class="payment-chips-row">
                            <button type="button" class="pos-mode-btn active" data-mode="1"><i class="fa fa-money text-success mr-1"></i> 💵 Cash <kbd class="kbd-hint">Alt+1</kbd></button>
                            <button type="button" class="pos-mode-btn" data-mode="3"><i class="fa fa-qrcode text-primary mr-1"></i> 📱 UPI / Online <kbd class="kbd-hint">Alt+2</kbd></button>
                            <button type="button" class="pos-mode-btn" data-mode="4"><i class="fa fa-bank text-info mr-1"></i> 🏦 Bank Transfer <kbd class="kbd-hint">Alt+3</kbd></button>
                            <button type="button" class="pos-mode-btn" data-mode="2"><i class="fa fa-file-text-o text-warning mr-1"></i> 📝 Cheque <kbd class="kbd-hint">Alt+4</kbd></button>
                        </div>

                        <!-- Hidden Payment Mode Select for backend submission -->
                        <select class="dash-form-control" id="payment_mode_id" name="payment_mode_id" style="display:none;" required>
                            @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                                    <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Cash Tendered & Smart Currency Denominations -->
                    <div id="cash_calc_row" class="cash-calc-box mb-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap" style="flex: 1;">
                            <span class="dash-form-lbl mb-0 font-weight-bold"><i class="fa fa-calculator mr-1"></i> Tendered:</span>
                            <div style="position: relative; width: 110px;">
                                <span style="position: absolute; left: 7px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b; font-size: 11px;">₹</span>
                                <input type="number" id="cash_tendered" class="dash-form-control" style="padding-left: 18px; font-weight: 700; height: 25px;" placeholder="0.00" autocomplete="off">
                            </div>
                            <!-- Denomination quick chips -->
                            <div class="d-inline-flex align-items-center gap-1">
                                <button type="button" class="btn-denom" data-amount="exact">Exact</button>
                                <button type="button" class="btn-denom" data-amount="500">₹500</button>
                                <button type="button" class="btn-denom" data-amount="1000">₹1k</button>
                                <button type="button" class="btn-denom" data-amount="2000">₹2k</button>
                                <button type="button" class="btn-denom" data-amount="5000">₹5k</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1 ml-auto">
                            <span class="dash-form-lbl mb-0">Change:</span>
                            <span id="cash_change_display" class="badge badge-success px-2 py-1" style="font-size: 11.5px; font-weight: 700;">₹ 0.00</span>
                        </div>
                    </div>

                    <!-- Form Controls Grid -->
                    <div class="p-2 border rounded bg-white mb-2" style="border-radius: 2px;">
                        <div class="row" style="margin: 0 -3px;">
                            <div class="col-6 col-md-3 px-1 mb-1">
                                <label class="dash-form-lbl">Payment Status</label>
                                <select name="payment_status" class="dash-form-control" id="payment_status">
                                    <option value="0">Payment Received</option>
                                    <option value="1">Payment Pending</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-3 px-1 mb-1">
                                <label class="dash-form-lbl">Payment Date *</label>
                                <input type="date" class="dash-form-control" name="date" value="{{ date('Y-m-d') }}" />
                            </div>

                            <div class="col-6 col-md-3 px-1 mb-1">
                                <label class="dash-form-lbl text-primary font-weight-bold">
                                    <i class="fa fa-barcode mr-1"></i> Running Receipt No
                                </label>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #eff6ff; border: 1px solid #93c5fd; border-radius: 2px; padding: 0 8px; height: 25px;">
                                    <span id="running_receipt_display" style="font-family: 'Consolas', 'Courier New', monospace; font-size: 11.5px; font-weight: 700; color: #002C54; letter-spacing: .04em;">
                                        <i class="fa fa-tag text-primary mr-1"></i>{{ $nextSlipNo }}
                                    </span>
                                    <span class="badge badge-primary" style="font-size: 9px; padding: 2px 5px; text-transform: uppercase;">Next #</span>
                                </div>
                                <input type="hidden" id="offline_receipt_no" name="offline_receipt_no" value="{{ $nextSlipNo }}">
                            </div>

                            <div class="col-6 col-md-3 px-1 mb-1">
                                <label class="dash-form-lbl">Transaction Remark</label>
                                <input type="text" id="other_fee_remark" class="dash-form-control" name="other_fee_remark" placeholder="Optional remark...">
                            </div>

                            <!-- Dynamic Cheque Fields -->
                            <div class="col-6 col-md-3 px-1 mb-1" id="cheque_number_id" style="display: none;">
                                <label class="dash-form-lbl">Cheque Number</label>
                                <input type="text" name="cheque_number" id="cheque_number" class="dash-form-control">
                            </div>

                            <div class="col-6 col-md-3 px-1 mb-1" id="cheque_date_id" style="display: none;">
                                <label class="dash-form-lbl">Cheque Date</label>
                                <input type="date" name="cheque_date" id="cheque_date" class="dash-form-control">
                            </div>

                            <!-- Dynamic Bank / Transaction Fields -->
                            <div class="col-6 col-md-3 px-1 mb-1" id="transition_id_input" style="display: none;">
                                <label class="dash-form-lbl">Transaction / UTR ID</label>
                                <input type="text" class="dash-form-control" id="transition_id" name="transition_id" placeholder="Bank ref / UTR">
                            </div>

                            <div class="col-6 col-md-3 px-1 mb-1" id="bank_name_input" style="display: none;">
                                <label class="dash-form-lbl">Bank Name</label>
                                <input type="text" class="dash-form-control" id="bank_name" name="bank_name" placeholder="Bank name">
                            </div>

                            <div class="col-12 col-md-6 px-1 mb-1" id="payment_receipt_id" style="display: none;">
                                <label class="dash-form-lbl">Payment Receipt / Cheque File</label>
                                <input type="file" accept=".gif, .jpg, .jpeg, .png, .pdf, .doc" class="dash-form-control" name="payment_receipt" id="payment_receipt" style="padding: 2px 4px; font-size: 10px;">
                            </div>
                        </div>

                        <!-- Hidden inputs required for calculations -->
                        <input type="hidden" id="total_amount" name="total_amount" value="0">
                        <input type="hidden" id="total_fine" name="total_fine" value="0">
                        <input type="hidden" id="discount_given" name="discount_given" value="" readonly />
                    </div>

                </div><!-- End .bill-scrollable-body -->

                <!-- Fixed Bottom Checkout Bar (Always Visible at Bottom of Card!) -->
                <div class="bill-checkout-bar">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <div style="font-size: 11px; color: #cbd5e1; font-weight: 500; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span id="selected_heads_count_badge" class="badge" style="font-size: 10px; background: rgba(255, 255, 255, 0.18); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35); font-weight: 700; padding: 2px 8px; border-radius: 2px;">0 Heads</span>
                                <span>Subtotal: <strong style="color: #ffffff; font-size: 11.5px;">₹<span id="aggregate">0.00</span></strong></span>
                                <span style="opacity: 0.5;">|</span>
                                <span>Fine (+): <strong style="color: #f87171; font-size: 11.5px;">₹<span id="f_given">0.00</span></strong></span>
                            </div>
                            <div style="font-size: 16px; font-weight: 800; color: #ffffff; letter-spacing: -.02em; margin-top: 2px;">
                                Total Payable: <span style="color: #4ade80;">₹<span id="g_total">0.00</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if(Helper::getPermisnByBranch()->whatsapp_srvc == 1)
                            <div class="form-check mr-2">
                                <input type="checkbox" class="form-check-input pointer" id="checkbox_whatsapp" name="checkbox_whatsapp" value="1">
                                <label for="checkbox_whatsapp" class="form-check-label pointer font-weight-bold text-white small" style="font-size: 10.5px;">
                                    <i class="fa fa-whatsapp text-success mr-1"></i> WhatsApp
                                </label>
                            </div>
                        @endif
                        <button type="submit" id="collect_btn" class="dash-btn dash-btn-outline text-white border-white collect_btn_hide" style="height: 30px; font-size: 11px; padding: 0 10px;" title="Shortcut: Alt+C or F4">
                            <i class="fa fa-check mr-1"></i> {{ __('fees.Collect') }} <kbd class="kbd-hint">Alt+C</kbd>
                        </button>
                        <button type="submit" id="collect_btn" name="print" class="dash-btn collect_btn collect_btn_hide" style="height: 30px; font-size: 11.5px; background: #16a34a; color: #ffffff; border-color: #15803d; font-weight: 700; padding: 0 14px;" title="Shortcut: Alt+P or F2">
                            <i class="fa fa-print mr-1"></i> {{ __('Collect & Print') }} <kbd class="kbd-hint" style="background: rgba(0,0,0,.25);">Alt+P</kbd>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ================= TAB 2: INSTALLMENT BREAKDOWN & DUE DATES ================= -->
        <div class="pos-tab-content" id="tab_installments" style="display: none; height: 100%; overflow-y: auto;">
            <div class="border rounded mt-1 overflow-hidden" style="border-radius: 2px;">
                <table id="fee_structure" class="dash-table" style="white-space:nowrap;">
                    <thead>
                        <tr>
                            <th>Fee Head</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Paid</th>
                            <th>Paid Fine</th>
                            <th>Pending</th>
                            <th>Due Date</th>
                            <th>Fine</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $t_amount = 0;
                            $t_discount = 0;
                            $t_paid = 0;
                            $t_paid_fine = 0;
                            $t_pending_fine = 0;
                        @endphp
                        @foreach($fees_assign_details as $fees)
                            @php
                                $feesGroup = DB::table('fees_group')->whereNull('deleted_at')->where('id',$fees->fees_group_id)->first();
                                $discount = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id',$fees->admission_id)->where('fees_group_id',$feesGroup->id)->whereIn('status',[0,1])->sum('discount');
                                $paid = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id',$fees->admission_id)->where('fees_group_id',$feesGroup->id)->whereIn('status',[0,1])->sum('total_amount');
                                $paid_fine = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id',$fees->admission_id)->where('fees_group_id',$feesGroup->id)->whereIn('status',[0,1])->sum('installment_fine');
                                $pending_amount = (($fees->fees_group_amount) - ($fees->discount)) - ($paid);
                                $is_overdue = ($pending_amount != 0 && isset($fees->installment_due_date) && $fees->installment_due_date < date('Y-m-d'));
                                $fine_calc = $is_overdue ? ($pending_amount * ($fees->installment_fine ?? 0))/100 : 0;
                            @endphp
                            <tr id="group_{{ $feesGroup->id ?? '' }}" class="group_group">
                                <td class="font-weight-bold">{{ $feesGroup->name ?? '' }}</td>
                                <td class="font-weight-bold">₹ {{ number_format($fees->fees_group_amount ?? 0, 2) }}</td>
                                <td class="text-muted">₹ {{ number_format(($fees->discount ?? 0) + $discount, 2) }}</td>
                                <td class="text-success font-weight-bold">₹ {{ number_format(($paid - $discount) ?? 0, 2) }}</td>
                                <td class="text-danger">₹ {{ number_format($paid_fine ?? 0, 2) }}</td>
                                <td id="pending_by_group_id_tab2_{{ $fees->id ?? '' }}"
                                    class="{{ $pending_amount == 0 ? 'text-success font-weight-bold' : 'text-danger font-weight-bold' }}"
                                    data-pending_amount="{{ $pending_amount ?? '0' }}"
                                    data-fine="{{ $is_overdue ? $fees->installment_fine : 0 }}">
                                    ₹ {{ number_format($pending_amount ?? 0, 2) }}
                                </td>
                                <td>
                                    <input type="date"
                                           class="dash-form-control p-1 {{ $pending_amount == 0 ? 'fees_assign_detail' : '' }}"
                                           style="height: 23px; font-size: 10px; width: 120px; {{ $is_overdue ? 'border-color: #ef4444; background: #fef2f2;' : '' }}"
                                           name="installment_due_date"
                                           data-detail_id="{{ $fees->id ?? '' }}"
                                           data-old_value="{{ $fees->installment_due_date ?? '' }}"
                                           {{ $pending_amount == 0 ? 'disabled' : '' }}
                                           value="{{ $fees->installment_due_date ?? '' }}">
                                </td>
                                <td class="text-danger font-weight-bold">₹ {{ number_format($fine_calc, 2) }}</td>
                            </tr>
                            @php
                                $t_amount += $fees->fees_group_amount ?? 0;
                                $t_discount += ($fees->discount ?? 0) + $discount;
                                $t_paid += ($paid ?? 0) - $discount;
                                $t_paid_fine += $paid_fine ?? 0;
                                $t_pending_fine += $fine_calc;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>₹ {{ number_format($t_amount ?? 0, 2) }}</td>
                            <td>₹ {{ number_format($t_discount ?? 0, 2) }}</td>
                            <td>₹ {{ number_format($t_paid ?? 0, 2) }}</td>
                            <td class="text-danger">₹ {{ number_format($t_paid_fine ?? 0, 2) }}</td>
                            <td id="validate_pending" class="text-danger font-weight-bold" data-pending="{{ (($t_amount ?? 0) - ($t_discount ?? 0)) - ($t_paid ?? 0) }}">
                                ₹ {{ number_format((($t_amount ?? 0) - ($t_discount ?? 0)) - ($t_paid ?? 0), 2) }}
                            </td>
                            <td></td>
                            <td class="text-danger font-weight-bold">₹ {{ number_format($t_pending_fine ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ================= TAB 3: RECEIPTS & PAYMENT HISTORY ================= -->
        <div class="pos-tab-content" id="tab_history" style="display: none; height: 100%; overflow-y: auto;">
            <div class="border rounded mt-1 overflow-hidden" style="border-radius: 2px;">
                <table class="dash-table" id="trColor">
                    <thead>
                        <tr>
                            <th style="width: 22%;">Fee Head(s)</th>
                            <th style="width: 17%;">Receipt / Slip No.</th>
                            <th style="width: 12%;">Payment Date</th>
                            <th style="width: 16%;">Paid Amount (₹)</th>
                            <th style="width: 17%;">Payment Mode</th>
                            <th style="width: 8%; text-align: center;">Status</th>
                            <th style="width: 8%; text-align: center;">{{ __('common.Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data['FeesDetailsInvoices']) && count($data['FeesDetailsInvoices']) > 0)
                            @foreach($data['FeesDetailsInvoices'] as $val)
                                @php
                                    $fee_detail_id = explode(',', $val->fees_details_id);
                                    $head_names = '';
                                    $head_total = 0;
                                    $discount = 0;
                                    $head_fine_total = 0;
                                    if(!empty($fee_detail_id)) {
                                        $head_names = DB::table('fees_detail')
                                            ->leftJoin('fees_group', 'fees_detail.fees_group_id', '=', 'fees_group.id')
                                            ->whereIn('fees_detail.id', $fee_detail_id)
                                            ->whereNull('fees_detail.deleted_at')
                                            ->pluck('fees_group.name')
                                            ->implode(', ');
                                        $head_total = DB::table('fees_detail')
                                            ->whereIn('fees_detail.id', $fee_detail_id)
                                            ->whereNull('fees_detail.deleted_at')->sum('paid_amount');
                                        $discount = DB::table('fees_detail')
                                            ->whereIn('fees_detail.id', $fee_detail_id)
                                            ->whereNull('fees_detail.deleted_at')->sum('discount');
                                        $head_fine_total = DB::table('fees_detail')
                                            ->whereIn('fees_detail.id', $fee_detail_id)
                                            ->whereNull('fees_detail.deleted_at')->sum('installment_fine');
                                    }
                                @endphp
                                <tr>
                                    <td class="font-weight-bold" style="color: #002C54;">{{ $head_names ?: '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <form target="_blank" action="{{ url('printFeesInvoice') }}" method="post" style="display:inline-block; margin:0;">
                                                @csrf
                                                <input type="hidden" name="fees_details_invoice_id" value="{{ $val->id }}" />
                                                <button type="submit" class="dash-btn dash-btn-primary" style="height: 22px; font-size: 10px; padding: 0 6px;" title="Print Receipt #{{ $val->invoice_no ?? '' }}">
                                                    <i class="fa fa-print mr-1"></i> {{ $val->invoice_no ?? '' }}
                                                </button>
                                            </form>
                                            @if(!empty($val->offline_receipt_no) && $val->offline_receipt_no !== '-')
                                                <span class="badge badge-light border text-dark font-weight-bold" title="Offline Slip No." style="font-size: 9.5px;">#{{ $val->offline_receipt_no }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ !empty($val->payment_date) ? date('d-m-Y', strtotime($val->payment_date)) : '-' }}</td>
                                    <td>
                                        <div class="font-weight-bold text-success" style="font-size: 11.5px;">₹ {{ number_format($head_total ?? 0, 2) }}</div>
                                        @if($discount > 0 || $head_fine_total > 0)
                                            <div class="small" style="font-size: 9.5px; line-height: 1.2;">
                                                @if($discount > 0)<span class="text-muted mr-1">Disc: ₹{{ number_format($discount, 2) }}</span>@endif
                                                @if($head_fine_total > 0)<span class="text-danger font-weight-bold">Fine: ₹{{ number_format($head_fine_total, 2) }}</span>@endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @if(!empty($getPaymentMode))
                                                @foreach($getPaymentMode as $value)
                                                    @if($value->id == $val->payment_mode)
                                                        <span class="badge badge-light border font-weight-bold text-dark" style="font-size: 9.5px;">{{ $value->name ?? '' }}</span>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if((!empty($val->bank_name) && $val->bank_name !== '-') || (!empty($val->transaction_id) && $val->transaction_id !== '-'))
                                                <div class="small text-muted" style="font-size: 9.5px; margin-top: 2px;">
                                                    @if(!empty($val->bank_name) && $val->bank_name !== '-')<span>{{ $val->bank_name }}</span>@endif
                                                    @if(!empty($val->transaction_id) && $val->transaction_id !== '-')<code class="ml-1" style="font-size: 9px;">{{ $val->transaction_id }}</code>@endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($val->status == 0)
                                            <span class="badge badge-success p-1 px-2" style="font-size: 9.5px; font-weight: 700;">Received</span>
                                        @elseif($val->status == 1)
                                            <span class="badge badge-warning p-1 px-2" style="font-size: 9.5px; font-weight: 700;">Pending</span>
                                        @else
                                            <span class="badge badge-danger p-1 px-2" style="font-size: 9.5px; font-weight: 700;">Cancelled</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        @if(Session::get('role_id') == 1)
                                            @if($val->status != 2)
                                                <button class="table-btn btn-action-print whatsapp_reciept"
                                                        data-session_id="{{ $val->session_id ?? '' }}"
                                                        data-admission_id="{{ $val->admission_id ?? ''}}"
                                                        data-fees_details_invoice_id="{{ $val->id }}"
                                                        data-toggle="modal"
                                                        data-target="#whatsapp_modal"
                                                        title="Send WhatsApp Receipt"
                                                        style="background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; width: 24px; height: 24px;">
                                                    <i class="fa fa-whatsapp"></i>
                                                </button>
                                                <button class="table-btn btn-action-delete revert_fees"
                                                        data-session_id="{{ $val->session_id ?? '' }}"
                                                        data-admission_id="{{ $val->admission_id ?? ''}}"
                                                        data-id="{{ $val->id }}"
                                                        data-toggle="modal"
                                                        data-target="#revert_modal"
                                                        title="Revert Fees"
                                                        style="background: #fef2f2; color: #dc2626; border-color: #fecaca; width: 24px; height: 24px;">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No prior fee receipts found for this student.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Dynamic UPI QR Modal for Parent Scan -->
<div class="modal fade arise-modal" id="upi_qr_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 320px;">
        <div class="modal-content text-center">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);">
                <h5 class="modal-title"><i class="fa fa-qrcode mr-1"></i> Scan &amp; Pay via UPI</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-2">
                    <span class="small text-muted d-block">Student: <strong>{{ $data['stuData']['first_name'] ?? '' }} (Adm #{{ $data['stuData']['admissionNo'] ?? '' }})</strong></span>
                    <div style="font-size: 18px; font-weight: 800; color: #002C54; margin: 4px 0;">
                        ₹ <span id="qr_payable_display">0.00</span>
                    </div>
                </div>
                <div class="p-2 border rounded bg-light d-inline-block mb-2">
                    <img id="dynamic_upi_qr_img" src="" alt="UPI QR Code" style="width: 170px; height: 170px; display: block; margin: 0 auto;">
                </div>
                <p class="text-muted small mb-0" style="font-size: 10.5px;">
                    Scan using any UPI App (GPay, PhonePe, Paytm, BHIM).<br>
                    Enter the generated UTR number into Transaction ID.
                </p>
            </div>
            <div class="modal-footer p-2 justify-content-center">
                <button type="button" class="dash-btn dash-btn-primary" data-dismiss="modal">
                    <i class="fa fa-check mr-1"></i> Done / Enter UTR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Revert Fees Confirmation Modal -->
<div class="modal fade arise-modal" id="revert_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-undo mr-1"></i> Revert Fees Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="revert_fees_form" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="admissionId" name="admission_id">
                    <input type="hidden" id="fees_invoice_id" name="fees_invoice_id">
                    <input type="hidden" id="sessionID_" name="session_id">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa fa-exclamation-triangle text-danger mr-2 fa-2x"></i>
                        <p class="mb-0 font-weight-bold text-danger">
                            {{ __('fees.Are you sure you want to revert fees ? This action is irreversible.') }}
                        </p>
                    </div>
                    <p class="text-muted small mb-0">Reverting will cancel this payment slip and restore the outstanding fee balances to the student ledger.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="hide_modal" class="dash-btn dash-btn-light border" data-dismiss="modal">Close</button>
                    <button type="submit" class="dash-btn dash-btn-primary" style="background: #dc2626; border-color: #dc2626;"><i class="fa fa-trash mr-1"></i> Confirm Revert</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Spinner Modal -->
<div class="modal" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 260px;">
        <div class="modal-content" style="background: rgba(0, 44, 84, 0.96); border-radius: 2px; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-body text-center p-3">
                <div class="spinner-border text-light mb-2" role="status" style="width: 2.2rem; height: 2.2rem;">
                    <span class="sr-only">Processing...</span>
                </div>
                <h6 class="text-white font-weight-bold mb-1" style="font-size: 12.5px;">Saving Payment...</h6>
                <p class="text-light small mb-0" style="opacity: 0.85; font-size: 10.5px;">Updating student fee ledger</p>
            </div>
        </div>
    </div>
</div>

<!-- Due Date Confirmation Modal -->
<div class="modal fade arise-modal" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel"><i class="fa fa-calendar mr-1"></i> Confirm Due Date Change</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Do you really want to change the installment due date? This may update fine calculations for this fee head.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="dash-btn dash-btn-light border" id="cancelChange" data-dismiss="modal">Cancel</button>
                <button type="button" class="dash-btn dash-btn-primary" id="confirmChange">Confirm Change</button>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Receipt Modal -->
<div class="modal fade arise-modal" id="whatsapp_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);">
                <h5 class="modal-title"><i class="fa fa-whatsapp mr-1"></i> Send Receipt on WhatsApp</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="Send_whatsapp_reciept" method="POST" action="{{ url('sendReceiptOnWhatsapp') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="fees_details_invoice_id" id="whatsapp_fees_details_invoice_id">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-paper-plane text-success mr-2 fa-2x"></i>
                        <p class="mb-0 font-weight-bold">Are you sure you want to send the fee receipt to the parent's registered WhatsApp number?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dash-btn dash-btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn-primary btn-submit" style="background: #16a34a; border-color: #15803d;"><i class="fa fa-whatsapp mr-1"></i> Send Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Number Error Modal (422 Catch) -->
<div id="offlineReceiptErrorModal" class="offline-receipt-modal">
    <div class="offline-receipt-modal-box">
        <div class="offline-receipt-modal-header">
            <h5><i class="fa fa-exclamation-circle mr-1"></i> Receipt Number Error</h5>
            <span id="offlineReceiptErrorClose" class="offline-receipt-modal-close">&times;</span>
        </div>
        <div class="offline-receipt-modal-body">
            <div id="offlineReceiptErrorMessage" style="margin-bottom: 10px; font-weight: bold;"></div>
            <div id="offlineReceiptStudentDetails" style="display: none; color: #333; font-size: 11.5px;">
                <p class="mb-1"><strong>Admission No. :</strong> <span id="err_admission_no"></span></p>
                <p class="mb-1"><strong>Student Name :</strong> <span id="err_student_name"></span></p>
                <p class="mb-1"><strong>Father Name :</strong> <span id="err_father_name"></span></p>
                <p class="mb-1"><strong>Mobile :</strong> <span id="err_mobile"></span></p>
                <p class="mb-1"><strong>Class :</strong> <span id="err_class"></span></p>
                <p class="mb-1"><strong>Offline Receipt No. :</strong> <span id="err_receipt_no"></span></p>
            </div>
        </div>
        <div class="offline-receipt-modal-footer">
            <button type="button" id="offlineReceiptErrorCloseBtn" class="dash-btn dash-btn-light border">Close</button>
        </div>
    </div>
</div>

<style>
.offline-receipt-modal {
    display: none;
    position: fixed;
    z-index: 99999999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.60);
    align-items: center;
    justify-content: center;
}
.offline-receipt-modal-box {
    width: 480px;
    max-width: 90%;
    background: #ffffff;
    border-radius: 2px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    overflow: hidden;
}
.offline-receipt-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 12px;
    background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
    color: #ffffff;
}
.offline-receipt-modal-header h5 {
    margin: 0;
    font-size: 12.5px;
    font-weight: 700;
}
.offline-receipt-modal-close {
    font-size: 20px;
    line-height: 16px;
    cursor: pointer;
    color: #ffffff;
}
.offline-receipt-modal-body {
    padding: 12px 14px;
    font-size: 11.5px;
    color: #b91c1c;
}
.offline-receipt-modal-footer {
    padding: 6px 12px;
    border-top: 1px solid #e2e8f0;
    text-align: right;
    background: #f8fafc;
}
</style>

<!-- Quick POS Backdrop for Blurring Terminal -->
<div class="quick-pos-backdrop" id="quickPosBackdrop"></div>

<!-- ================= DRAGGABLE QUICK POS ALLOCATION WIDGET ================= -->
<div class="quick-pos-widget" id="quickPosModal">
    <div class="quick-pos-header" id="quickPosHeader">
        <div class="quick-pos-title">
            <i class="fa fa-bolt text-warning"></i>
            <span>Quick POS - Fee Allocation</span>
            <span class="badge badge-light text-dark ml-1" style="font-size: 9.5px; font-weight: 700;">#{{ $data['stuData']['admissionNo'] ?? '' }}</span>
        </div>
        <div>
            <button type="button" class="close text-white" id="btn_close_quick_pos" style="font-size: 18px; line-height: 1; opacity: 0.85; padding: 0 4px; border: none; background: transparent; cursor: pointer;">&times;</button>
        </div>
    </div>

    <div class="quick-pos-body">
        <!-- Student Info Strip -->
        <div class="d-flex align-items-center gap-2 mb-2 p-1.5 rounded" style="background: #f1f5f9; border: 1px solid #e2e8f0;">
            @php
                $quickUserImage = !empty($data['stuData']['image']) 
                    ? env('IMAGE_SHOW_PATH').'profile/'.$data['stuData']['image'] 
                    : env('IMAGE_SHOW_PATH').'default/user_image.jpg';
            @endphp
            <img src="{{ $quickUserImage }}" style="width: 36px; height: 36px; border-radius: 2px; object-fit: cover; border: 1px solid #cbd5e1;" onerror="this.src='{{ env('IMAGE_SHOW_PATH') }}default/user_image.jpg'">
            <div style="min-width: 0; flex: 1;">
                <div class="text-truncate" style="font-weight: 700; font-size: 12px; color: #002C54;">
                    {{ $data['stuData']['first_name'] ?? '' }} {{ $data['stuData']['last_name'] ?? '' }}
                </div>
                <div class="text-truncate" style="font-size: 10px; color: #64748b;">
                    Class: {{ $data['stuData']['ClassTypes']['name'] ?? '—' }} | F: {{ $data['stuData']['father_name'] ?? '—' }}
                </div>
            </div>
        </div>

        <!-- Outstanding Amount Box -->
        <div class="quick-pos-due-box">
            <div>
                <div class="quick-pos-due-lbl">
                    <i class="fa fa-exclamation-circle mr-1"></i> Net Outstanding Due:
                </div>
                <div style="font-size: 9.5px; color: #991b1b; opacity: 0.85;">Total unpaid fee heads</div>
            </div>
            <div class="quick-pos-due-val" id="quick_pos_due_display">
                ₹ {{ number_format($stat_total_pending, 2) }}
            </div>
        </div>

        <!-- Lumpsum Allocation Input -->
        <div class="mb-1">
            <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 11px;">
                <label class="mb-0 font-weight-bold text-dark" for="quick_lumpsum_amount">
                    <i class="fa fa-inr text-success mr-1"></i> Lumpsum Amount to Allocate:
                </label>
                @if($stat_total_pending > 0)
                    <a href="javascript:void(0)" id="btn_quick_pay_full" class="font-weight-bold text-primary" style="font-size: 10.5px; text-decoration: underline;">
                        <i class="fa fa-check-circle mr-0.5"></i> Full Due (₹{{ number_format($stat_total_pending, 2) }})
                    </a>
                @endif
            </div>
            <div class="quick-pos-input-group mb-0">
                <span class="input-icon">₹</span>
                <input type="number" step="any" id="quick_lumpsum_amount" class="quick-pos-input" placeholder="0.00" autocomplete="off" />
            </div>
            <div class="text-muted mt-1" style="font-size: 9.5px;">
                <i class="fa fa-info-circle mr-1 text-primary"></i> Amount will auto-distribute across oldest pending fee heads.
            </div>
        </div>
    </div>

    <!-- Modal Footer / Confirm Button -->
    <div class="quick-pos-footer">
        <button type="button" class="quick-pos-confirm-btn" id="btn_quick_pos_allocate">
            <i class="fa fa-bolt text-warning"></i> Auto Allocate Fees
        </button>
        <div class="d-flex align-items-center justify-content-between" style="font-size: 9.5px; color: #64748b; margin-top: 2px;">
            <span><kbd style="font-size: 9px; padding: 1px 4px; background: #e2e8f0; color: #334155;">Enter</kbd> Auto Allocate</span>
            <a href="javascript:void(0)" id="btn_quick_pos_dismiss" class="text-muted font-weight-bold">Cancel (<kbd style="font-size: 9px; padding: 1px 4px; background: #e2e8f0; color: #334155;">Esc</kbd>)</a>
        </div>
    </div>
</div>

<!-- External Scripts -->
<script src="{{ URL::asset('public/assets/school/js/form/form_save.js') }}"></script>

<!-- Fast POS Script Handlers -->
<script>
var feesSettingConfig = {
    fine_mode: "{{ $feesSetting->fine_mode ?? 'fixed' }}",
    fine_amount: {{ (float) ($feesSetting->fine_amount ?? 0) }},
    fine_grace_days: {{ (int) ($feesSetting->fine_grace_days ?? 0) }},
    fine_max_cap: {{ !empty($feesSetting->fine_max_cap) ? (float) $feesSetting->fine_max_cap : 'null' }},
    allow_fine_waiver: {{ (int) ($feesSetting->allow_fine_waiver ?? 1) }},
    fine_waiver_requires_remark: {{ (int) ($feesSetting->fine_waiver_requires_remark ?? 0) }},
    allow_manual_discount: {{ (int) ($feesSetting->allow_manual_discount ?? 1) }},
    max_discount_percentage: {{ (float) ($feesSetting->max_discount_percentage ?? 20) }},
    discount_requires_remark: {{ (int) ($feesSetting->discount_requires_remark ?? 0) }}
};
var userManualFineEdited = false;
var lastCalculatedPolicyFine = 0;
var triggerAggregateAllocation;

function openQuickPosModal() {
    $('.bill-desk-wrapper').addClass('pos-terminal-blurred');
    $('#quickPosBackdrop').fadeIn(150);
    $('#quickPosModal').fadeIn(150, function() {
        $('#quick_lumpsum_amount').focus().select();
    });
}

function closeQuickPosModal() {
    $('#quickPosBackdrop').fadeOut(120);
    $('#quickPosModal').fadeOut(120);
    $('.bill-desk-wrapper').removeClass('pos-terminal-blurred');
}

/* Quick POS Auto Allocation Engine */
function executeQuickPosAllocation() {
    let amount = parseFloat($('#quick_lumpsum_amount').val()) || 0;
    let totalPending = parseFloat("{{ $stat_total_pending }}") || 0;

    if (amount <= 0) {
        toastr.error('Please enter an amount greater than 0 to allocate.');
        $('#quick_lumpsum_amount').focus();
        return;
    }

    if (amount > totalPending) {
        toastr.warning('Amount cannot exceed total pending (₹' + totalPending.toFixed(2) + '). Setting to full due.');
        amount = totalPending;
        $('#quick_lumpsum_amount').val(amount.toFixed(2));
    }

    // Put amount into fastpay strip lumpsum input as well for visibility
    $('#aggregate_amount').val(amount.toFixed(2));

    // Auto allocate across fee heads
    triggerAggregateAllocation(amount);

    // Close modal & restore terminal focus
    closeQuickPosModal();

    toastr.success('₹ ' + amount.toFixed(2) + ' auto-allocated across pending fee heads.');
}

$(document).ready(function () {
    // Auto-open Quick POS modal with focus on lumpsum amount if pending > 0
    let studentTotalPending = parseFloat("{{ $stat_total_pending }}") || 0;
    if (studentTotalPending > 0) {
        setTimeout(function() {
            openQuickPosModal();
        }, 250);
    }

    // Quick Pay Full button
    $('#btn_quick_pay_full').on('click', function(e) {
        e.preventDefault();
        let totalPending = parseFloat("{{ $stat_total_pending }}") || 0;
        $('#quick_lumpsum_amount').val(totalPending.toFixed(2)).focus().select();
    });

    // Auto Allocate button & Enter key handlers
    $('#btn_quick_pos_allocate').on('click', function(e) {
        e.preventDefault();
        executeQuickPosAllocation();
    });

    $('#quick_lumpsum_amount').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            executeQuickPosAllocation();
        }
    });

    // Close & Dismiss (via button, footer cancel link, or clicking blurred backdrop)
    $('#btn_close_quick_pos, #btn_quick_pos_dismiss, #quickPosBackdrop').on('click', function(e) {
        e.preventDefault();
        closeQuickPosModal();
    });

    // Global Key shortcuts: Esc to close, Alt+Q to reopen
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#quickPosModal').is(':visible')) {
            closeQuickPosModal();
        }
        if (e.altKey && (e.key === 'q' || e.key === 'Q')) {
            e.preventDefault();
            openQuickPosModal();
        }
    });

    // Reopen button in nav bar
    $('#btn_open_quick_pos').on('click', function(e) {
        e.preventDefault();
        openQuickPosModal();
    });
    /* Tab Switching */
    $('.bill-tab-btn').on('click', function () {
        var targetTab = $(this).data('tab');
        $(this).addClass('active').siblings().removeClass('active');
        $('.pos-tab-content').hide();
        $('#' + targetTab).show();
        if (targetTab === 'tab_payment_desk') {
            $('#header_fastpay_strip').show();
        } else {
            $('#header_fastpay_strip').hide();
        }
    });

    /* Visual Payment Mode Chips click */
    $('.pos-mode-btn').on('click', function () {
        var mode = $(this).data('mode');
        $(this).addClass('active').siblings().removeClass('active');
        $('#payment_mode_id').val(mode).trigger('change');
        
        // Show/hide Cash calculator
        if (mode == 1) {
            $('#cash_calc_row').show();
            $('#btn_show_upi_qr').hide();
        } else if (mode == 3) {
            $('#cash_calc_row').hide();
            $('#btn_show_upi_qr').show();
        } else {
            $('#cash_calc_row').hide();
            $('#btn_show_upi_qr').hide();
        }
    });

    /* Auto Generate Receipt Slip Helper */
    $('#btn_auto_slip').on('click', function() {
        $('#offline_receipt_no').val('{{ $nextSlipNo }}');
        toastr.info('Auto slip number assigned: {{ $nextSlipNo }}');
    });

    /* Dynamic UPI QR Code Generation */
    $('#btn_show_upi_qr').on('click', function() {
        let payable = parseFloat($('#total_amount').val()) || 0;
        if (payable <= 0) {
            toastr.warning('Please select at least 1 fee head first.');
            return;
        }
        $('#qr_payable_display').text(payable.toFixed(2));
        
        let studentName = encodeURIComponent('{{ $data['stuData']['first_name'] ?? '' }}');
        let admNo = '{{ $data['stuData']['admissionNo'] ?? '' }}';
        let upiString = `upi://pay?pa=schoolfees@upi&pn=Arise%20School&am=${payable.toFixed(2)}&tn=Fee%20Adm${admNo}&cu=INR`;
        let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=170x170&data=${encodeURIComponent(upiString)}`;
        
        $('#dynamic_upi_qr_img').attr('src', qrUrl);
        $('#upi_qr_modal').modal('show');
    });

    /* Smart Currency Denomination Buttons */
    $('.btn-denom').on('click', function() {
        let amount = $(this).data('amount');
        let totalPayable = parseFloat($('#total_amount').val()) || 0;
        
        if (amount === 'exact') {
            $('#cash_tendered').val(totalPayable.toFixed(2)).trigger('input');
        } else {
            $('#cash_tendered').val(parseFloat(amount).toFixed(2)).trigger('input');
        }
    });

    /* Tendered Cash Calculator */
    $('#cash_tendered').on('input', function() {
        let tendered = parseFloat($(this).val()) || 0;
        let totalPayable = parseFloat($('#total_amount').val()) || 0;
        let change = tendered - totalPayable;
        if (tendered > 0 && change >= 0) {
            $('#cash_change_display').removeClass('badge-secondary').addClass('badge-success').text('₹ ' + change.toFixed(2));
        } else if (tendered > 0 && change < 0) {
            $('#cash_change_display').removeClass('badge-success').addClass('badge-secondary').text('Short by ₹ ' + Math.abs(change).toFixed(2));
        } else {
            $('#cash_change_display').removeClass('badge-secondary').addClass('badge-success').text('₹ 0.00');
        }
    });

    /* Shortcut: Pay Full Outstanding Due */
    $('#btn_pay_full_due').on('click', function () {
        var fullDue = parseFloat($(this).data('amount')) || 0;
        $('#aggregate_amount').val(fullDue);
        triggerAggregateAllocation(fullDue);
    });

    /* Shortcut: Select All Heads */
    $('#btn_select_all_heads').on('click', function () {
        $('.selected_head').prop('checked', true).trigger('change');
    });

    /* Shortcut: Clear All Heads */
    $('#btn_clear_all_heads').on('click', function () {
        $('#aggregate_amount').val('');
        triggerAggregateAllocation(0);
    });

    /* Lumpsum Real-Time Debounce & Button Handler */
    var lumpsumTimer = null;

    $('#aggregate_amount').on('input', function () {
        clearTimeout(lumpsumTimer);
        let rawVal = $(this).val();
        if (rawVal === '') {
            triggerAggregateAllocation(0);
            return;
        }
        let aggregateAmount = parseFloat(rawVal);
        if (!isNaN(aggregateAmount) && aggregateAmount >= 0) {
            lumpsumTimer = setTimeout(function () {
                triggerAggregateAllocation(aggregateAmount);
            }, 250);
        }
    });

    $('#aggregate_amount').on('keyup', function (event) {
        if (event.key === "Enter") {
            clearTimeout(lumpsumTimer);
            let aggregateAmount = parseFloat($(this).val()) || 0;
            triggerAggregateAllocation(aggregateAmount);
        }
    });

    $('#btn_auto_allocate').on('click', function () {
        clearTimeout(lumpsumTimer);
        let aggregateAmount = parseFloat($('#aggregate_amount').val()) || 0;
        if (aggregateAmount <= 0) {
            toastr.warning('Please enter a lumpsum amount first.');
            $('#aggregate_amount').focus();
            return;
        }
        triggerAggregateAllocation(aggregateAmount);
        toastr.success('₹ ' + aggregateAmount.toFixed(2) + ' auto-allocated across pending fee heads.');
    });

    /* Auto Allocate Function across Fee Head Rows */
    triggerAggregateAllocation = function(aggregateAmount) {
        aggregateAmount = parseFloat(aggregateAmount) || 0;

        let total_pending = parseFloat($("#validate_pending").data('pending')) || 0;
        if (aggregateAmount > total_pending) {
            toastr.error('Amount cannot be greater than total pending (₹ ' + total_pending.toFixed(2) + ').');
            aggregateAmount = total_pending;
            $('#aggregate_amount').val(aggregateAmount);
        }

        // If zero or negative, uncheck all heads
        if (aggregateAmount <= 0) {
            $('.selected_head').prop('checked', false);
            $('.group_group').removeClass('row-selected');
            $('.amount_get').prop('disabled', true).val('');
            $('.head_discount_input').prop('disabled', true).val('0');
            $('.head_fine_input').prop('disabled', true).val('0');
            recomputePolicyFine();
            updateTotals();
            return;
        }

        let remaining = aggregateAmount;

        // Reset all rows first
        $('.selected_head').prop('checked', false);
        $('.group_group').removeClass('row-selected');
        $('.amount_get').prop('disabled', true).val('');
        $('.head_discount_input').prop('disabled', true).val('0');
        $('.head_fine_input').prop('disabled', true).val('0');

        // Walk through each fee head row in DOM order and allocate
        $('#head_row tr.group_group').each(function () {
            if (remaining <= 0) return false;

            let row = $(this);
            let checkbox = row.find('.selected_head');
            let detailId = checkbox.data('fees_assign_detail_id');
            let pendingEl = row.find('[data-pending_amount]');
            let pending = parseFloat(pendingEl.attr('data-pending_amount')) || 0;

            if (pending > 0) {
                let allocate = Math.min(remaining, pending);
                remaining -= allocate;

                checkbox.prop('checked', true);
                row.addClass('row-selected');

                let amtInput = $('#amount_' + detailId);
                amtInput.prop('disabled', false).val(allocate.toFixed(2));
                $('#discount_' + detailId).prop('disabled', false).val('0');
                $('#fine_' + detailId).prop('disabled', false).val('0');
            }
        });

        recomputePolicyFine();
        updateTotals();
    }

    /* Head Checkbox Change */
    $(".selected_head").on("change", function () {
        let fees_assign_detail_id = $(this).data("fees_assign_detail_id");
        let parentRow = $(this).closest('tr');

        if ($(this).is(":checked")) {
            parentRow.addClass('row-selected');
            $("#amount_" + fees_assign_detail_id).prop("disabled", false);
            $("#discount_" + fees_assign_detail_id).prop("disabled", false);
            $("#fine_" + fees_assign_detail_id).prop("disabled", false);

            var pending_amount = Number($('#pending_by_group_id_' + fees_assign_detail_id).attr('data-pending_amount'));

            if (pending_amount > 0) {
                $('#amount_' + fees_assign_detail_id).val(pending_amount);
            }
        } else {
            parentRow.removeClass('row-selected');
            $("#amount_" + fees_assign_detail_id).prop("disabled", true).val('');
            $("#discount_" + fees_assign_detail_id).prop("disabled", true).val('0');
            $("#fine_" + fees_assign_detail_id).prop("disabled", true).val('0');
        }
        recomputePolicyFine();
        updateTotals();
    });

    /* Amount Input */
    $(".amount_get").on("input", function () {
        let fees_assign_detail_id = $(this).attr("id").split("_")[1];
        let pendingAmount = Number($('#pending_by_group_id_' + fees_assign_detail_id).attr('data-pending_amount'));
        let currentValue = Number($(this).val());

        if (currentValue < 0 || isNaN(currentValue)) {
            $(this).val(0);
        }

        if (currentValue > pendingAmount) {
            $(this).val(pendingAmount);
            toastr.error("Amount can't be greater than pending amount");
        }

        if (feesSettingConfig.fine_mode === 'percentage') {
            recomputePolicyFine();
        }
        updateTotals();
    });

    /* Fine Waiver Button */
    $('#btn_waive_fine').on('click', function() {
        userManualFineEdited = true;
        $('#settlement_fine').val('0.00');
        $('#fine_waived_badge').show();
        $('#fine_was_waived').val('1');
        updateTotals();
        toastr.info('Late fine waived to ₹0.00');
        if (feesSettingConfig.fine_waiver_requires_remark) {
            $('#other_fee_remark').focus();
        }
    });

    /* Fine Input in Settlement Card */
    $('#settlement_fine').on('input', function() {
        userManualFineEdited = true;
        let entered = parseFloat($(this).val()) || 0;
        if (lastCalculatedPolicyFine > 0 && entered < lastCalculatedPolicyFine) {
            $('#fine_waived_badge').show();
            $('#fine_was_waived').val('1');
        } else {
            $('#fine_waived_badge').hide();
            $('#fine_was_waived').val('0');
        }
        updateTotals();
    });



    /* Initial state disable */
    $(".selected_head").each(function () {
        let key = $(this).data("fees_assign_detail_id");
        if (!$(this).is(":checked")) {
            $("#amount_" + key).prop("disabled", true);
            $("#discount_" + key).prop("disabled", true);
            $("#fine_" + key).prop("disabled", true);
        }
    });

    /* Payment Mode Field Dynamics */
    $('#payment_mode_id').on('change', function() {
        var payment_mode_id = $(this).val();
        $("#other_fee_remark").val('');
        $("#payment_status").val(0);
        $("#cheque_number_id").hide();
        $("#cheque_date_id").hide();
        $("#payment_receipt_id").hide();

        if (payment_mode_id == 1) {
            $('#transition_id_input').hide();
            $('#bank_name_input').hide();
            $('#bank_name').val('');
            $('#transition_id').val('');
        } else if (payment_mode_id == 2) {
            $("#cheque_number_id").show();
            $("#cheque_date_id").show();
            $("#payment_receipt_id").show();
            $("#bank_name_input").show();
            $("#payment_status").val(1);
            $("#other_fee_remark").val('This cheque is pending realisation');
        } else {
            $('#transition_id_input').show();
            $('#bank_name_input').show();
            $('#payment_receipt_id').show();
            $('#bank_name').val('');
            $('#transition_id').val('');
        }
    });

    /* Global Cashier Keyboard Hotkeys */
    $(document).on('keydown', function(e) {
        // Only run if not inside a standard text input (except for specific Alt combinations)
        if (e.altKey) {
            if (e.key === 'p' || e.key === 'P') {
                e.preventDefault();
                $('button[name="print"]').click();
            } else if (e.key === 'c' || e.key === 'C') {
                e.preventDefault();
                $('#collect_btn:not([name="print"])').click();
            } else if (e.key === 'f' || e.key === 'F') {
                e.preventDefault();
                $('#btn_pay_full_due').click();
            } else if (e.key === 'a' || e.key === 'A') {
                e.preventDefault();
                $('#aggregate_amount').focus().select();
            } else if (e.key === '1') {
                e.preventDefault();
                $('.pos-mode-btn[data-mode="1"]').click();
            } else if (e.key === '2') {
                e.preventDefault();
                $('.pos-mode-btn[data-mode="3"]').click();
            } else if (e.key === '3') {
                e.preventDefault();
                $('.pos-mode-btn[data-mode="4"]').click();
            } else if (e.key === '4') {
                e.preventDefault();
                $('.pos-mode-btn[data-mode="2"]').click();
            }
        } else if (e.key === 'F2') {
            e.preventDefault();
            $('button[name="print"]').click();
        } else if (e.key === 'F4') {
            e.preventDefault();
            $('#collect_btn:not([name="print"])').click();
        }
    });

    /* AJAX 422 Error Catch */
    $(document).ajaxError(function (event, xhr, settings) {
        if (
            settings.url &&
            (
                settings.url.indexOf('student_pay_submit') !== -1 ||
                settings.url.indexOf('studentPaySubmit') !== -1
            )
        ) {
            if (xhr.status === 422) {
                let message = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#offlineReceiptErrorMessage').text(message);

                if (xhr.responseJSON && xhr.responseJSON.data) {
                    let sData = xhr.responseJSON.data;
                    $('#err_admission_no').text(sData.admission_no);
                    $('#err_student_name').text(sData.student_name);
                    $('#err_father_name').text(sData.father_name);
                    $('#err_mobile').text(sData.mobile);
                    $('#err_class').text(sData.class_name);
                    $('#err_receipt_no').text(sData.offline_receipt_no);
                    $('#offlineReceiptStudentDetails').show();
                } else {
                    $('#offlineReceiptStudentDetails').hide();
                }

                $('#offlineReceiptErrorModal').css({'display': 'flex'});
            }
        }
    });

    $(document).on('click', '#offlineReceiptErrorClose, #offlineReceiptErrorCloseBtn', function () {
        $('#offlineReceiptErrorModal').css({'display': 'none'});
    });

    /* WhatsApp Modal Receipt handler */
    $(document).on('click', '.whatsapp_reciept', function() {
        $('#whatsapp_fees_details_invoice_id').val($(this).data('fees_details_invoice_id'));
    });

    /* Due Date Change Modal handler */
    var currentTd, fees_assign_detail_id, value, old_value, field;

    $('#fee_structure').on('change', '[name="installment_due_date"]', function() {
        currentTd = $(this);
        fees_assign_detail_id = currentTd.data('detail_id');
        value = currentTd.val();
        old_value = currentTd.data('old_value');
        field = currentTd.attr('name');

        function compareValues(value1, value2) {
            return String(value1 || '') !== String(value2 || '');
        }
        if (compareValues(value, old_value)) {
            $('#confirmationModal').modal('show');
        }
    });

    $('#confirmChange').on('click', function() {
        $('#confirmationModal').modal('hide');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('updateAssignedFees') }}",
            method: 'POST',
            data: {
                fees_assign_detail_id: fees_assign_detail_id,
                value: value,
                field: field,
                session_id: $('#session_id').val()
            },
            success: function(response) {
                toastr.success('Due date has been changed successfully.');
                currentTd.data('old_value', value);
                currentTd.val(value);
                $('#active_li').click();
            },
            error: function(xhr) {
                currentTd.val(old_value);
                toastr.error(xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Due date could not be changed.');
            }
        });
    });

    $('#cancelChange').on('click', function() {
        currentTd.val(old_value);
        $('#confirmationModal').modal('hide');
    });

    /* Revert Fees Form handler */
    $('#revert_fees_form').submit(function(event){
        event.preventDefault();
        var formData = $('#revert_fees_form').serialize();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: BASEURL + '/collect_fees_delete',
            data: formData,
            success: function(data) {
                if(data.status == 'success'){
                    $('#hide_modal').click();
                    toastr.success('Fee Revert Successfully');
                    setTimeout(function() {
                         showData(data.unique_system_id, data.session_id);
                    }, 800);
                }
            }
        });
    });

    $(document).on('click', '.collect_btn', function(){
        $('.collect_btn').val('print');
    });

    /* Payment Collection Submit */
    $("#myForm").submit(function(event){
        event.preventDefault();
        
        let payableAmount = parseFloat($('#total_amount').val()) || 0;
        if (payableAmount <= 0) {
            toastr.error('Please select at least 1 fee head with a payment amount greater than zero.');
            return false;
        }

        let fineWasWaived = $('#fine_was_waived').val() === '1';
        let remark = $('#other_fee_remark').val().trim();

        if (fineWasWaived && feesSettingConfig.fine_waiver_requires_remark && remark === '') {
            toastr.error('Transaction remark is mandatory when waiving or reducing late fine.');
            $('#other_fee_remark').focus();
            return false;
        }

        $('#loadingModal').modal('show');
        $('.collect_btn_hide').hide();
        var buttonValue = $('.collect_btn').val();
        var formData = new FormData($('#myForm')[0]);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: BASEURL + '/student_pay_submit',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status == 'success') {
                    $('#loadingModal').modal('hide');
                    $('#collect_btn').show();
                    $('.collect_btn').val('');
                    toastr.success('Fee Collected Successfully');
                    showData(data.unique_system_id, data.session_id);
                    if(buttonValue == 'print'){
                        var fees_details_invoice_id = data.fees_details_invoice_id;
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'post',
                            url: BASEURL + '/printFeesInvoice',
                            data: { fees_details_invoice_id: fees_details_invoice_id },
                            success: function(response) {
                                var printWindow = window.open('', '_blank');
                                printWindow.document.open();
                                printWindow.document.write('<html><head><title>Print Invoice</title>');
                                printWindow.document.write('<style>body{font-size:12px;} table{font-size:12px;}</style>');
                                printWindow.document.write('</head><body>');
                                printWindow.document.write(response);
                                printWindow.document.write('</body></html>');
                                printWindow.document.close();
                                printWindow.onload = function() {
                                    printWindow.focus();
                                    printWindow.print();
                                    printWindow.close();
                                };
                            }
                        });
                    }
                } else {
                    $('#collect_btn').show();
                    $('#loadingModal').modal('hide');
                    $('.collect_btn').val('');
                    toastr.error('Something Went Wrong');
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                $('.collect_btn_hide').show();
                $('.collect_btn').val('');
                let message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Fee could not be saved. Please try again.';
                toastr.error(message);
            }
        });
    });

    /* Academic Session Switcher */
    $('.tab').click(function(){
        $('.tab').removeAttr('id');
        $(this).attr('id', 'active_li');
        var session_id = $(this).data('id');
        var unique_system_id = $(this).data('unique_system_id');

        if(session_id != ""){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: BASEURL + '/student_fees_onclick',
                data: {unique_system_id: unique_system_id, session_id: session_id},
                success: function(data) {
                    if(data != 0){
                        $('#student_fees_detail').html(data);
                    } else {
                        $('#notfound').hide();
                        $('.not_found_div').show();
                    }
                }
            });
        }
    });
});

/* Revert Fees Trigger */
$(document).on('click', '.revert_fees', function(){
    var fees_invoice_id = $(this).data('id');
    var admission_id = $(this).data('admission_id');
    var sessionID_ = $(this).data('session_id');

    $('#fees_invoice_id').val(fees_invoice_id);
    $('#admissionId').val(admission_id);
    $('#sessionID_').val(sessionID_);
});

/* Recompute Late Fine Based on FeesSetting Policy */
function recomputePolicyFine() {
    let checkedBoxes = $('.selected_head:checked');
    if (checkedBoxes.length === 0) {
        lastCalculatedPolicyFine = 0;
        if (!userManualFineEdited) {
            $('#settlement_fine').val('0.00');
        }
        $('#fine_calc_hint').text('Select fee heads to evaluate due date & late fine');
        $('#fine_waived_badge').hide();
        $('#fine_was_waived').val('0');
        return;
    }

    let overdueCount = 0;
    let maxBillableDays = 0;
    let overduePendingSum = 0;

    checkedBoxes.each(function () {
        let isOverdue = parseInt($(this).data('is_overdue')) || 0;
        if (isOverdue === 1) {
            overdueCount++;
            let pending = parseFloat($(this).data('pending_amount')) || 0;
            let billableDays = parseInt($(this).data('billable_days')) || 0;
            overduePendingSum += pending;
            if (billableDays > maxBillableDays) {
                maxBillableDays = billableDays;
            }
        }
    });

    let fine = 0;
    let hint = '';

    if (overdueCount === 0) {
        fine = 0;
        hint = 'All selected heads are within due date (No fine)';
    } else {
        if (feesSettingConfig.fine_mode === 'disabled') {
            fine = 0;
            hint = 'Late fine is disabled in institute settings';
        } else if (feesSettingConfig.fine_mode === 'fixed') {
            fine = feesSettingConfig.fine_amount;
            hint = overdueCount + ' overdue head(s). Flat fine: ₹' + fine.toFixed(2);
        } else if (feesSettingConfig.fine_mode === 'daily') {
            fine = maxBillableDays * feesSettingConfig.fine_amount;
            hint = maxBillableDays + ' billable overdue day(s) (after ' + feesSettingConfig.fine_grace_days + 'd grace) × ₹' + feesSettingConfig.fine_amount + '/day';
        } else if (feesSettingConfig.fine_mode === 'percentage') {
            fine = (overduePendingSum * feesSettingConfig.fine_amount) / 100;
            hint = feesSettingConfig.fine_amount + '% of overdue fees (₹' + overduePendingSum.toFixed(2) + ')';
        } else if (feesSettingConfig.fine_mode === 'manual') {
            hint = 'Manual cashier fine entry allowed';
        }

        if (feesSettingConfig.fine_max_cap !== null && fine > feesSettingConfig.fine_max_cap) {
            fine = feesSettingConfig.fine_max_cap;
            hint += ' (Capped at max ₹' + feesSettingConfig.fine_max_cap.toFixed(2) + ')';
        }
    }

    lastCalculatedPolicyFine = fine;

    if (!userManualFineEdited) {
        $('#settlement_fine').val(fine.toFixed(2));
    }
    $('#fine_calc_hint').text(hint);

    let currentEnteredFine = parseFloat($('#settlement_fine').val()) || 0;
    if (lastCalculatedPolicyFine > 0 && currentEnteredFine < lastCalculatedPolicyFine) {
        $('#fine_waived_badge').show();
        $('#fine_was_waived').val('1');
    } else {
        $('#fine_waived_badge').hide();
        $('#fine_was_waived').val('0');
    }
}

/* Calculate Totals and Settle Policy Adjustments */
function updateTotals() {
    let subtotal = 0;
    let checkedCount = 0;
    let checkedDetails = [];

    $(".selected_head:checked").each(function () {
        checkedCount++;
        let detailId = $(this).data("fees_assign_detail_id");
        let amount = Number($("#amount_" + detailId).val()) || 0;
        subtotal += amount;
        checkedDetails.push(detailId);
    });

    let enteredFine = parseFloat($('#settlement_fine').val()) || 0;

    // Distribute fine and set hidden discount to 0 across checked heads for backend submission
    $('.head_discount_input').prop('disabled', true).val('0');
    $('.head_fine_input').prop('disabled', true).val('0');

    if (checkedDetails.length > 0) {
        for (let i = 0; i < checkedDetails.length; i++) {
            let id = checkedDetails[i];
            $('#discount_' + id).prop('disabled', false).val('0');
            $('#fine_' + id).prop('disabled', false).val(i === 0 ? enteredFine.toFixed(2) : '0');
        }
    }

    let netPayable = subtotal + enteredFine;

    $("#total_amount").val(netPayable.toFixed(2));
    $("#total_fine").val(enteredFine.toFixed(2));
    $("#discount_given").val('0.00');

    // Update display values
    $("#aggregate").text(subtotal.toFixed(2));
    $("#f_given").text(enteredFine.toFixed(2));
    $("#g_total").text(netPayable.toFixed(2));
    $("#selected_heads_count_badge").text(checkedCount + ' Head' + (checkedCount === 1 ? '' : 's'));

    // Update change calculator if cash
    let tendered = parseFloat($('#cash_tendered').val()) || 0;
    if (tendered > 0) {
        let change = tendered - netPayable;
        if (change >= 0) {
            $('#cash_change_display').removeClass('badge-secondary').addClass('badge-success').text('₹ ' + change.toFixed(2));
        } else {
            $('#cash_change_display').removeClass('badge-success').addClass('badge-secondary').text('Short by ₹ ' + Math.abs(change).toFixed(2));
        }
    }
}
</script>
