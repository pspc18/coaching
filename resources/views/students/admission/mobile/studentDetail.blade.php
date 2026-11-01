@extends('layout.mobile_app')

@php
    $student = $data;
    $studentName = trim(($student->first_name ?? '').' '.($student->last_name ?? ''));
    $sessionLabel = trim(($student->from_year ?? '').'-'.($student->to_year ?? ''), '-');
    $imageBase = rtrim((string) env('IMAGE_SHOW_PATH'), '/');
    $photo = !empty($student->image) ? $imageBase.'/profile/'.$student->image : $imageBase.'/default/user_image.jpg';
    $defaultPhoto = $imageBase.'/default/user_image.jpg';
    $primaryContact = $student->mobile ?: ($student->father_mobile ?: ($student->mother_mob ?: ''));
    $fatherContact = $student->father_mobile ?: ($student->mobile ?: '');
    $isActive = (int)($student->status ?? 1) === 1;

    $dobFormatted = !empty($student->dob) ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-';
    $ageFormatted = !empty($student->dob) ? \Carbon\Carbon::parse($student->dob)->age . ' yrs' : '';
    $admissionDateFormatted = !empty($student->admission_date) ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : '-';
@endphp

@section('styles')
<style>
/* ==============================================================================
   ARISE ERP - NATIVE MOBILE STUDENT PROFILE (SHARP CARDS & COMPACT DENSITY)
   Strictly adheres to Arise ERP Mobile Design System (arise-theme.css & mob-hero)
   ============================================================================== */

/* 1. Native Mobile Hero Card */
.native-mob-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #0a4275 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 10px 12px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}
.native-mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.native-mob-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.native-mob-back {
    color: #ffffff;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.12);
    text-decoration: none !important;
}

/* Student Main Info Box */
.native-student-row {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 8px;
}
.native-avatar-wrap {
    position: relative;
    width: 62px;
    height: 72px;
    border-radius: 3px;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: #001f3d;
}
.native-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.native-avatar-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #0284c7;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    border: 1px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.native-student-meta {
    flex: 1;
    min-width: 0;
}
.native-student-name {
    font-size: 15px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.native-tags-wrap {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    margin: 3px 0 4px;
}
.mob-badge-pill {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 2px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.15);
    color: #f1f5f9;
}
.mob-badge-active {
    background: rgba(16, 185, 129, 0.25);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.45);
}
.mob-badge-inactive {
    background: rgba(239, 68, 68, 0.25);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.45);
}

/* 3-Column Metrics Snapshot */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 3px;
    text-align: center;
}
.mob-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-metric-val {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
}
.mob-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 5px;
}
.mob-act-btn {
    flex: 1;
    height: 29px;
    padding: 0 4px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}
.mob-act-btn-whatsapp { background: #10b981; color: #ffffff !important; }
.mob-act-btn-call { background: #0284c7; color: #ffffff !important; }
.mob-act-btn-fees { background: #f59e0b; color: #ffffff !important; }
.mob-act-btn-light { background: rgba(255, 255, 255, 0.14); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.22); }
.mob-act-btn:active { transform: scale(0.97); }

/* 2. Segmented Navigation Bar */
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
    overflow-x: auto;
    scrollbar-width: none;
}
.mob-seg-bar::-webkit-scrollbar { display: none; }
.mob-seg-btn {
    flex: 0 0 auto;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    white-space: nowrap;
}
.mob-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.mob-seg-pill {
    display: inline-block;
    padding: 1px 4px;
    font-size: 9px;
    border-radius: 2px;
    background: rgba(0,0,0,0.08);
    margin-left: 2px;
}
.mob-seg-btn.active .mob-seg-pill {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}

/* 3. Segment Panes */
.mob-pane {
    display: none;
}
.mob-pane.active {
    display: block;
}

/* Sharp Mobile Cards */
.mob-card {
    background: #ffffff;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    margin-bottom: 8px;
    overflow: hidden;
}
.mob-card-head {
    padding: 7px 10px;
    background: #fafbfc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-card-title {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-card-body {
    padding: 8px 10px;
}

/* Portal Credentials Mobile Strip */
.mob-cred-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 10px 12px;
    color: #ffffff;
    margin-bottom: 8px;
    box-shadow: 0 2px 8px rgba(0, 20, 40, 0.2);
}
.mob-cred-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-cred-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #38bdf8;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.mob-cred-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.mob-cred-btn {
    height: 24px;
    padding: 0 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    line-height: 1;
    white-space: nowrap;
    transition: transform .12s ease;
}
.mob-cred-btn:active { transform: scale(0.95); }
.mob-cred-btn-copy {
    background: #ffffff;
    color: #002C54 !important;
    border: 1px solid #cbd5e1;
}
.mob-cred-btn-share {
    background: #10b981;
    color: #ffffff !important;
}
.mob-cred-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 11.5px;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.12);
}
.mob-cred-row:last-child { border-bottom: none; }
.mob-cred-lbl { color: #94a3b8; font-size: 11px; font-weight: 600; }
.mob-cred-val { font-family: monospace; font-size: 12.5px; font-weight: 800; color: #ffffff; }

/* Data Rows */
.mob-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 11.5px;
    border-bottom: 1px dashed #f1f5f9;
}
.mob-info-row:last-child { border-bottom: none; }
.mob-info-label { color: #64748b; font-weight: 600; }
.mob-info-val { color: #0f172a; font-weight: 700; text-align: right; max-width: 60%; }

/* Uniform Grid in Mobile */
.mob-kit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
.mob-kit-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
}
.mob-kit-tag { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px; }
.mob-kit-val { font-size: 11.5px; font-weight: 800; color: #0f172a; }

/* ==============================================================================
   ARISE ERP - NATIVE FEES LEDGER STYLES (MATCHING fees_history.blade.php)
   ============================================================================== */
.mob-fee-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-fee-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-fee-hero-title {
    font-size: 13px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-fee-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}
.mob-radar-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-radar-tag {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-radar-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin: 1px 0;
}
.mob-radar-meta {
    font-size: 9px;
    color: #cbd5e1;
    font-weight: 600;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.hero-due-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin: 1px 0 2px 0;
}
.hero-due-row .mob-radar-val {
    margin: 0;
    line-height: 1.2;
}
.hero-due-pay-btn {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff !important;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    line-height: 1.2;
    white-space: nowrap;
    flex-shrink: 0;
    box-shadow: 0 1px 6px rgba(16, 185, 129, 0.35);
    cursor: pointer;
    transition: transform .12s ease;
}
.hero-due-pay-btn:active {
    transform: scale(0.95);
}
.mob-hero-progress-strip {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 8px;
}
.progress-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 9.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-bottom: 4px;
}
.progress-meta-row b {
    color: #38bdf8;
    font-weight: 800;
}
.progress-track-bg {
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 2px;
    overflow: hidden;
}
.progress-track-fill {
    height: 100%;
    background: #10b981;
    border-radius: 2px;
    transition: width .4s ease;
}

/* Sub-tab segment toolbar for Fees (Fee Heads vs Receipts) */
.mob-subtab-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-subtab-btn {
    flex: 1;
    height: 30px;
    padding: 0 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    white-space: nowrap;
    transition: all .12s ease;
}
.mob-subtab-btn.active-subtab {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-subtab-btn:active {
    transform: scale(0.96);
}

/* Feed List & Card Item */
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    margin-bottom: 8px;
    transition: transform .1s ease, border-color .1s ease;
}
.card-paid { border-left: 3px solid #16a34a !important; }
.card-partial { border-left: 3px solid #f59e0b !important; }
.card-due { border-left: 3px solid #ef4444 !important; }
.card-receipt { border-left: 3px solid #0284c7 !important; }
.card-receipt .student-card-header {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 2px !important;
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
}
.card-receipt .student-card-actions {
    border-top: 1px solid #e2e8f0 !important;
    padding-top: 8px !important;
    margin-top: 6px !important;
}
.receipt-main-col {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.receipt-amount-col {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: center;
}
.receipt-disc-badge {
    font-size: 9.5px;
    font-weight: 700;
    color: #b45309;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 3px;
    padding: 1px 5px;
    margin-top: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    line-height: 1.2;
}

.student-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.student-avatar-box {
    width: 34px;
    height: 34px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
}
.student-avatar-box.avatar-paid {
    background: #dcfce7;
    color: #16a34a;
    border-color: #86efac;
}
.student-avatar-box.avatar-partial {
    background: #fef3c7;
    color: #d97706;
    border-color: #fde68a;
}
.student-avatar-box.avatar-due {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
}
.student-header-info {
    flex: 1;
    overflow: hidden;
}
.student-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.student-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 1px;
}
.student-adm-badge {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.student-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}
.student-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    white-space: nowrap;
}
.status-badge-active { background: #dcfce7; color: #16a34a; }
.status-badge-warning { background: #fef3c7; color: #b45309; }
.status-badge-inactive { background: #fee2e2; color: #dc2626; }

.student-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 8px;
}
.student-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-meta-item > i {
    width: 13px;
    font-size: 10.5px;
    color: #64748b;
    flex-shrink: 0;
    text-align: center;
}

.student-card-actions {
    display: flex;
    gap: 5px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-btn-action {
    flex: 1;
    height: 29px;
    padding: 0 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-btn-action:active { transform: scale(0.96); }
.btn-act-fee { background: #16a34a; color: #ffffff !important; }
.btn-act-edit { background: #f8fafc; border-color: #cbd5e1; color: #334155 !important; }

/* Expandable Transactions Drawer */
.tx-drawer-box {
    display: none;
    margin-top: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
}
.tx-drawer-box.show { display: block; }
.tx-drawer-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 6px;
    margin-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 9.5px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.tx-item-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px solid #edf2f7;
}
.tx-item-card:last-child {
    border-bottom: none;
    padding-bottom: 1px;
}
.tx-item-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.tx-icon-pill {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    line-height: 1;
    text-align: center;
    padding: 2px;
}
.tx-date-day {
    font-size: 11px;
    font-weight: 800;
    color: #0369a1;
    letter-spacing: -0.2px;
}
.tx-date-mon {
    font-size: 7.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #0284c7;
    margin-top: 1px;
}
.tx-item-details {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.tx-receipt-title {
    font-size: 11px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.tx-meta-sub {
    font-size: 9.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
    line-height: 1.2;
}
.tx-mode-tag {
    font-size: 8.5px;
    font-weight: 700;
    background: #e2e8f0;
    color: #334155;
    padding: 1px 4px;
    border-radius: 2px;
    text-transform: uppercase;
}
.tx-item-right {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.tx-amount-badge {
    font-size: 12px;
    font-weight: 800;
    color: #16a34a;
    letter-spacing: -0.01em;
    white-space: nowrap;
}
.tx-btn-view {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #002C54 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.tx-btn-view:active {
    transform: scale(0.92);
    background: #f1f5f9;
}

/* ==============================================================================
   ARISE ERP - NATIVE ATTENDANCE STYLES (MATCHING student_login/mobile/attendence)
   ============================================================================== */
.matrix-pills-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.matrix-status-cell {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 3px;
    text-align: center;
}
.matrix-status-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.matrix-status-val {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.15;
    margin-top: 2px;
}
.cell-present { background: #f0fdf4 !important; border-color: #bbf7d0 !important; }
.cell-present .matrix-status-val { color: #16a34a !important; }
.cell-absent { background: #fef2f2 !important; border-color: #fecaca !important; }
.cell-absent .matrix-status-val { color: #dc2626 !important; }
.cell-rate { background: #e0f2fe !important; border-color: #bae6fd !important; }
.cell-rate .matrix-status-val { color: #0284c7 !important; }
.cell-total { background: #f8fafc !important; border-color: #cbd5e1 !important; }
.cell-total .matrix-status-val { color: #334155 !important; }

/* Attendance Punch Activity List */
.native-list-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 7px 4px;
    border-bottom: 1px solid #f1f5f9;
}
.native-list-item:last-child {
    border-bottom: none;
    padding-bottom: 2px;
}
.item-date-badge {
    width: 36px;
    height: 36px;
    border-radius: 4px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.date-badge-day {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
    line-height: 1;
}
.date-badge-wday {
    font-size: 8px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-top: 1px;
}
.item-info-col {
    flex: 1;
    overflow: hidden;
}
.item-primary-txt {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.item-secondary-txt {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.punch-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #f1f5f9;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    font-size: 9px;
    font-weight: 600;
}
.status-chip {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 8.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .02em;
    display: inline-block;
    white-space: nowrap;
}
.chip-present { background: #dcfce7; color: #15803d; }
.chip-absent { background: #fee2e2; color: #b91c1c; }
.chip-halfday { background: #f3e8ff; color: #7e22ce; }
.chip-holiday { background: #e0f2fe; color: #0369a1; }
.chip-leave { background: #fef3c7; color: #b45309; }

/* ==============================================================================
   ARISE ERP - NATIVE EXAMS STYLES (MATCHING student_login/exams/view.blade.php)
   ============================================================================== */
.exam-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
}
.exam-title-cluster {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.exam-icon-circle {
    width: 34px;
    height: 34px;
    border-radius: 4px;
    background: linear-gradient(135deg, #001833, #002C54);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    border: 1px solid rgba(56, 189, 248, 0.3);
}
.exam-title-text {
    min-width: 0;
    flex: 1;
}
.exam-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
    margin: 0 0 2px;
    word-break: break-word;
}
.exam-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    flex-wrap: wrap;
}
.exam-meta-row span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.exam-score-badge {
    text-align: right;
    flex-shrink: 0;
}
.score-pill {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 13px;
    font-weight: 800;
    line-height: 1.1;
    display: inline-block;
    border: 1px solid;
}
.score-pill-sub {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    margin-top: 2px;
    display: block;
}
.exam-metrics-ribbon {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.ribbon-item {
    text-align: center;
}
.ribbon-item:not(:last-child) {
    border-right: 1px solid #e2e8f0;
}
.ribbon-label {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    line-height: 1.2;
}
.ribbon-value {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-top: 1px;
}
.ribbon-value.highlight-green { color: #16a34a; }
.ribbon-value.highlight-blue { color: #0284c7; }

.subject-marks-wrap {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 4px 8px;
    margin-bottom: 8px;
}
.subject-mark-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 10.5px;
}
.subject-mark-row:last-child {
    border-bottom: none;
}
.subject-mark-name {
    color: #334155;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 65%;
}
.subject-mark-val {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 700;
}
.subject-mark-val b {
    color: #002C54;
}

/* Document Item Card */
.mob-doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    background: #ffffff;
    margin-bottom: 6px;
}
.mob-doc-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-doc-icon {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    background: #fee2e2;
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.mob-doc-name {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-doc-meta { font-size: 9.5px; color: #64748b; }

/* ==============================================================================
   NATIVE MOBILE BOTTOM SHEET DRAWER ARCHITECTURE
   ============================================================================== */
.mob-sheet-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 15, 30, 0.72);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: opacity .2s ease, visibility .2s ease;
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.35);
    z-index: 99995;
    transform: translateY(105%);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 520px;
    margin: 0 auto;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    max-height: 85vh;
}
.mob-bottom-sheet.show {
    transform: translateY(0);
}
.mob-sheet-drag-handle {
    width: 36px;
    height: 4px;
    background: rgba(255, 255, 255, 0.45);
    border-radius: 2px;
    margin: 6px auto 4px;
}
.mob-sheet-head {
    flex-shrink: 0;
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 0 12px 8px;
    display: flex;
    flex-direction: column;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-sheet-head-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 18px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    opacity: 0.85;
}
.mob-sheet-body {
    flex: 1 1 auto;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
    padding: 12px;
}
.mob-sheet-footer {
    flex-shrink: 0;
    padding: 8px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
}
.mob-sheet-submit-btn {
    flex: 1;
    height: 34px;
    border-radius: 3px;
    background: #0284c7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}
.mob-sheet-cancel-btn {
    width: 75px;
    height: 34px;
    border-radius: 3px;
    background: #e2e8f0;
    color: #475569;
    font-size: 11.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}

/* ==========================================
   APP-STYLE DOCS & SIBLINGS POLISH & EMPTY STATES
   ========================================== */
.mob-sub-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-sub-bar-left {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-badge-counter {
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    color: #0284c7;
    font-size: 9.5px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 10px;
}
.mob-sub-btn-primary {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #001f3f;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    text-decoration: none !important;
}
.mob-sub-btn-primary:active {
    background: #001f3f;
    transform: scale(0.97);
}

/* Native App Empty State */
.mob-app-empty-state {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 24px 16px;
    text-align: center;
    margin: 8px 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.mob-empty-glyph {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 10px;
}
.glyph-blue {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}
.glyph-sky {
    background: #f0f9ff;
    color: #0284c7;
    border: 1px solid #bae6fd;
}
.mob-empty-heading {
    font-size: 13.5px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 4px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    max-width: 280px;
    margin: 0 auto 12px;
}
.mob-empty-action-btn {
    background: #0284c7;
    color: #ffffff !important;
    border: none;
    border-radius: 4px;
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
}
.mob-empty-action-btn:active {
    transform: scale(0.96);
    background: #0369a1;
}

/* Document Item Card */
.mob-doc-tile {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3.5px solid #0284c7;
    border-radius: 4px;
    padding: 9px 10px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-doc-top {
    display: flex;
    gap: 9px;
    align-items: flex-start;
    margin-bottom: 7px;
}
.mob-doc-icon {
    width: 34px;
    height: 34px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.doc-pdf {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.doc-img {
    background: #f0f9ff;
    color: #0284c7;
    border: 1px solid #bae6fd;
}
.doc-generic {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.mob-doc-body {
    flex: 1;
    min-width: 0;
}
.mob-doc-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 2px;
}
.mob-doc-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-doc-ext {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 3px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.mob-doc-meta {
    font-size: 10px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mob-doc-remark {
    font-size: 10px;
    color: #334155;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 3px;
    padding: 4px 8px;
    margin-bottom: 7px;
}
.mob-doc-actions {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 6px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-doc-act-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 5px 6px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155 !important;
    text-decoration: none !important;
}
.mob-doc-act-btn:active {
    transform: scale(0.96);
}
.mob-doc-act-btn.btn-act-view {
    background: #f0f9ff;
    border-color: #bae6fd;
    color: #0284c7 !important;
}
.mob-doc-act-btn.btn-act-del {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626 !important;
}

/* Sibling Item Card */
.mob-sibling-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3.5px solid #002C54;
    border-radius: 4px;
    padding: 9px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-sib-top {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 8px;
}
.mob-sib-avatar-wrap {
    width: 42px;
    height: 48px;
    border-radius: 3px;
    overflow: hidden;
    background: #001f3f;
    border: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.mob-sib-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.mob-sib-meta {
    flex: 1;
    min-width: 0;
}
.mob-sib-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 2px;
}
.mob-sib-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-sib-pills {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.mob-sib-link-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 5px 10px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #0284c7 !important;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
}
.mob-sib-link-btn:active {
    background: #e0f2fe;
    transform: scale(0.98);
}
</style>
@endsection

@section('content')
<div class="p-2" style="padding-bottom: 60px !important;">

    <!-- 1. NATIVE MOBILE HERO CARD -->
    <div class="native-mob-hero">
        <div class="native-student-row">
            <!-- Avatar with Instant Photo Upload Overlay -->
            <div class="native-avatar-wrap" onclick="openPhotoSheet()">
                <img src="{{ $photo }}" id="mobAvatarImg" class="native-avatar-img" alt="{{ $studentName }}" onerror="this.src='{{ $defaultPhoto }}'">
                <div class="native-avatar-badge"><i class="fa fa-camera"></i></div>
            </div>

            <!-- Details -->
            <div class="native-student-meta">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                    <div class="native-student-name" title="{{ $studentName }}">{{ $studentName ?: 'Student' }}</div>
                    <button type="button" class="btn btn-xs mob-badge-pill {{ $isActive ? 'mob-badge-active' : 'mob-badge-inactive' }}" onclick="openStatusSheet()" style="cursor: pointer; flex-shrink: 0; padding: 2px 7px; font-size: 9.5px; border-radius: 3px; font-weight: 700;">
                        <i class="fa {{ $isActive ? 'fa-check' : 'fa-ban' }}"></i> {{ $isActive ? 'ACTIVE' : 'INACTIVE' }}
                    </button>
                </div>
                <div class="native-tags-wrap">
                    <span class="mob-badge-pill"><i class="fa fa-hashtag"></i> {{ $student->admissionNo ?? '-' }}</span>
                    @if(!empty($student->roll_no))
                        <span class="mob-badge-pill"><i class="fa fa-id-badge"></i> Roll {{ $student->roll_no }}</span>
                    @endif
                    <span class="mob-badge-pill" style="background:rgba(56, 189, 248, 0.2); color:#38bdf8;"><i class="fa fa-book"></i> {{ $student->class_name ?? '-' }}</span>
                </div>
                <div style="font-size:10px; color:#cbd5e1;">
                    <span>Bio: <b>{{ $student->attendance_unique_id ?: ($student->unique_system_id ?: '-') }}</b></span>
                    <span class="mx-1">•</span>
                    <span>Session: <b>{{ $sessionLabel ?: 'Current' }}</b></span>
                </div>
            </div>
        </div>

        <!-- 3 Metrics Glance Grid -->
        <div class="mob-metrics-grid">
            <div class="mob-metric-box">
                <div class="mob-metric-tag"><i class="fa fa-inr"></i> Fees</div>
                <div class="mob-metric-val" style="color:#34d399;">₹{{ number_format($paidFees) }}</div>
                <div class="mob-metric-sub {{ $dueFees > 0 ? 'text-warning' : 'text-success' }}">Due: ₹{{ number_format($dueFees) }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag"><i class="fa fa-calendar-check-o"></i> Attend</div>
                <div class="mob-metric-val" style="color:#38bdf8;">{{ number_format($attendancePercentage, 0) }}%</div>
                <div class="mob-metric-sub">{{ $attendancePresent }}P · {{ $attendanceAbsent }}A</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag"><i class="fa fa-trophy"></i> Exams</div>
                <div class="mob-metric-val" style="color:#fbbf24;">{{ $examResults->count() ? number_format($overallExamPercentage, 0).'%' : 'N/A' }}</div>
                <div class="mob-metric-sub">{{ $examResults->count() }} Tests</div>
            </div>
        </div>

        <!-- Fast Actions Bar -->
        <div class="mob-actions-bar">
            @if(!empty($fatherContact))
                <a href="https://api.whatsapp.com/send?phone=91{{ $fatherContact }}&text=Hello%20{{ urlencode($studentName) }}" target="_blank" class="mob-act-btn mob-act-btn-whatsapp">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </a>
                <a href="tel:{{ $fatherContact }}" class="mob-act-btn mob-act-btn-call">
                    <i class="fa fa-phone"></i> Call
                </a>
            @endif
            <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo . '&layout=mobile') }}" class="mob-act-btn mob-act-btn-fees">
                <i class="fa fa-inr"></i> Collect
            </a>
            <a href="{{ url('admissionStudentIdPrint/' . $student->id) }}" target="_blank" class="mob-act-btn mob-act-btn-light" title="Print ID Card">
                <i class="fa fa-id-card-o"></i> ID
            </a>
            <a href="{{ url('admissionEdit/' . $student->id . '?layout=mobile') }}" class="mob-act-btn mob-act-btn-light" title="Edit">
                <i class="fa fa-pencil"></i> Edit
            </a>
        </div>
    </div>

    <!-- 2. SEGMENTED TABS -->
    <div class="mob-seg-bar">
        <button type="button" class="mob-seg-btn active" data-pane="pane-overview">Overview</button>
        <button type="button" class="mob-seg-btn" data-pane="pane-fees">Fees <span class="mob-seg-pill">{{ $feeHeadLedger->count() }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-attendance">Attendance <span class="mob-seg-pill">{{ $attendanceTotal }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-exams">Exams <span class="mob-seg-pill">{{ $examResults->count() }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-docs">Docs <span class="mob-seg-pill">{{ collect($getDocuments)->count() }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-family">Siblings <span class="mob-seg-pill">{{ collect($siblings)->count() }}</span></button>
    </div>

    <!-- ==========================================
         PANE 1: OVERVIEW & KIT
         ========================================== -->
    <div class="mob-pane active" id="pane-overview">
        <!-- Portal Credentials Strip -->
        <div class="mob-cred-card">
            <div class="mob-cred-header">
                <span class="mob-cred-title"><i class="fa fa-shield"></i> Portal Access</span>
                <div class="mob-cred-actions">
                    <button type="button" class="mob-cred-btn mob-cred-btn-copy" onclick="copyMobCredentials()">
                        <i class="fa fa-copy"></i> Copy
                    </button>
                    @if(!empty($fatherContact) && !empty($student->userName))
                        @php
                            $portalMsg = "Dear Parent, Login credentials for {$studentName} at Arise Portal:\nUsername: {$student->userName}\nPassword: {$student->confirm_password}\nPortal: " . url('/');
                        @endphp
                        <a href="https://api.whatsapp.com/send?phone=91{{ $fatherContact }}&text={{ urlencode($portalMsg) }}" target="_blank" class="mob-cred-btn mob-cred-btn-share">
                            <i class="fa fa-whatsapp"></i> Share
                        </a>
                    @endif
                </div>
            </div>
            <div class="mob-cred-row">
                <span class="mob-cred-lbl">Username</span>
                <span class="mob-cred-val">{{ $student->userName ?: 'Not Generated' }}</span>
            </div>
            <div class="mob-cred-row">
                <span class="mob-cred-lbl">Password</span>
                <span class="mob-cred-val">
                    <span id="mobPassMasked">••••••••</span>
                    <span id="mobPassPlain" style="display:none;">{{ $student->confirm_password ?: 'Not Set' }}</span>
                    <i class="fa fa-eye" id="mobPassEye" onclick="toggleMobPassword()" style="font-size:12px; cursor:pointer; color:#38bdf8; margin-left:6px;"></i>
                </span>
            </div>
        </div>

        <!-- Uniform & Material Kit -->
        @php
            $hasInventoryTable = false;
            $studentInventory = null;
            try {
                $hasInventoryTable = \Illuminate\Support\Facades\Schema::hasTable('student_inventories');
                if ($hasInventoryTable) {
                    $studentInventory = \Illuminate\Support\Facades\DB::table('student_inventories')
                        ->where('admission_id', $student->id)
                        ->whereNull('deleted_at')
                        ->first();
                }
            } catch (\Exception $e) {
                $hasInventoryTable = false;
                $studentInventory = null;
            }
        @endphp
        @if($hasInventoryTable)
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-cubes text-primary"></i> Uniform & Study Material Kit</h4>
            </div>
            <div class="mob-card-body">
                <div class="mob-kit-grid">
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-tag text-primary"></i> Shirt 1</div>
                        @if($studentInventory && !empty($studentInventory->shirt1_color))
                            <div class="mob-kit-val text-success">{{ $studentInventory->shirt1_color }}</div>
                            <div style="font-size:9px; color:#64748b;">Sz: {{ $studentInventory->shirt1_size }}</div>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-tags text-danger"></i> Shirt 2</div>
                        @if($studentInventory && !empty($studentInventory->shirt2_color))
                            <div class="mob-kit-val text-success">{{ $studentInventory->shirt2_color }}</div>
                            <div style="font-size:9px; color:#64748b;">Sz: {{ $studentInventory->shirt2_size }}</div>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-shopping-bag text-purple" style="color:#8b5cf6;"></i> Bag</div>
                        @if($studentInventory && (int)($studentInventory->bag_issued ?? 0) === 1)
                            <span class="badge badge-success" style="font-size:9px;"><i class="fa fa-check"></i> Issued</span>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-book text-info"></i> Modules</div>
                        @if($studentInventory && (int)($studentInventory->material_issued ?? 0) === 1)
                            <span class="badge badge-success" style="font-size:9px;"><i class="fa fa-check"></i> Issued</span>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Personal & Family Information -->
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-user text-info"></i> Personal & Family Details</h4>
            </div>
            <div class="mob-card-body p-0 px-2">
                <div class="mob-info-row">
                    <span class="mob-info-label">Father's Name</span>
                    <span class="mob-info-val">{{ $student->father_name ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Father Mobile</span>
                    <span class="mob-info-val">
                        @if(!empty($student->father_mobile))
                            <a href="tel:{{ $student->father_mobile }}">{{ $student->father_mobile }}</a>
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Mother's Name</span>
                    <span class="mob-info-val">{{ $student->mother_name ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Date of Birth</span>
                    <span class="mob-info-val">{{ $dobFormatted }} @if($ageFormatted)<small class="text-muted">({{ $ageFormatted }})</small>@endif</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Admission Date</span>
                    <span class="mob-info-val">{{ $admissionDateFormatted }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Blood Group / Gender</span>
                    <span class="mob-info-val">{{ $student->blood_group ?: '-' }} / {{ $student->genderName ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Caste / Religion</span>
                    <span class="mob-info-val">{{ $student->caste_category ?: ($student->category ?: '-') }} / {{ $student->religion ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Aadhaar / APAAR</span>
                    <span class="mob-info-val">{{ $student->aadhaar ?: '-' }} / {{ $student->apaar_id ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Transport Availed</span>
                    <span class="mob-info-val">{{ in_array(strtolower((string)$student->transport), ['1','yes','true'], true) ? 'Yes' : 'No' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Address</span>
                    <span class="mob-info-val" style="font-size:10.5px;">
                        {{ collect([$student->address, $student->village_city, $student->city_name, $student->state_name])->filter()->implode(', ') ?: '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         PANE 2: FEE HEAD-WISE LEDGER
         ========================================== -->
    <div class="mob-pane" id="pane-fees">
        <!-- Fees Hero Overview Card -->
        <div class="mob-fee-hero">
            <div class="mob-fee-hero-top">
                <div class="mob-fee-hero-title">
                    <i class="fa fa-inr text-warning"></i> Fee Ledger Overview
                </div>
                <div class="mob-fee-session-pill">
                    Session {{ $sessionLabel ?: 'Current' }}
                </div>
            </div>

            <div class="mob-dual-grid">
                <!-- Radar 1: Assigned Fee -->
                <div class="mob-radar-col">
                    <span class="mob-radar-tag">
                        <i class="fa fa-calculator text-primary"></i> Assigned Fee
                    </span>
                    <div class="mob-radar-val">
                        ₹{{ number_format($assignedFees, 2) }}
                    </div>
                    <span class="mob-radar-meta">
                        Net: ₹{{ number_format(max(0, $assignedFees - $feeDiscount), 2) }}
                    </span>
                </div>

                <!-- Radar 2: Concession / Discount -->
                <div class="mob-radar-col">
                    <span class="mob-radar-tag">
                        <i class="fa fa-tag text-warning"></i> Scholarship/Disc.
                    </span>
                    <div class="mob-radar-val" style="color: #fbbf24;">
                        ₹{{ number_format($feeDiscount, 2) }}
                    </div>
                    <span class="mob-radar-meta">
                        @if($feeFine > 0)
                            +₹{{ number_format($feeFine, 2) }} Fine
                        @else
                            No Fine Added
                        @endif
                    </span>
                </div>

                <!-- Radar 3: Received Fees -->
                <div class="mob-radar-col">
                    <span class="mob-radar-tag">
                        <i class="fa fa-check-circle text-success"></i> Received Fees
                    </span>
                    <div class="mob-radar-val" style="color: #34d399;">
                        ₹{{ number_format($paidFees, 2) }}
                    </div>
                    <span class="mob-radar-meta">
                        {{ $feePaidPercentage }}% of Net Fees
                    </span>
                </div>

                <!-- Radar 4: Outstanding Due -->
                <div class="mob-radar-col">
                    <span class="mob-radar-tag">
                        <i class="fa fa-clock-o {{ $dueFees > 0 ? 'text-danger' : 'text-success' }}"></i> Outstanding Due
                    </span>
                    <div class="hero-due-row">
                        <div class="mob-radar-val" style="color: {{ $dueFees > 0 ? '#f87171' : '#34d399' }};">
                            ₹{{ number_format($dueFees, 2) }}
                        </div>
                        @if($dueFees > 0)
                            <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo . '&layout=mobile') }}" class="hero-due-pay-btn" title="Collect Fees">
                                <i class="fa fa-plus-circle" style="font-size: 9px; margin-right: 3px;"></i> Collect
                            </a>
                        @endif
                    </div>
                    <span class="mob-radar-meta">
                        {{ $dueFees <= 0 ? 'All dues cleared ✓' : 'Payment Pending' }}
                    </span>
                </div>
            </div>

            <!-- Clearance Progress Track -->
            <div class="mob-hero-progress-strip">
                <div class="progress-meta-row">
                    <span>Course Fee Clearance</span>
                    <b>{{ $feePaidPercentage }}% Cleared</b>
                </div>
                <div class="progress-track-bg">
                    <div class="progress-track-fill" style="width: {{ $feePaidPercentage }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Segment Toolbar for Fees (Heads vs Invoices) -->
        <div class="mob-subtab-bar">
            <button type="button" class="mob-subtab-btn active-subtab" id="btnFeeSubHeads" onclick="switchFeeSubTab('heads', this)">
                <i class="fa fa-list-ol"></i> Fee Heads ({{ $feeHeadLedger->count() }})
            </button>
            <button type="button" class="mob-subtab-btn" id="btnFeeSubReceipts" onclick="switchFeeSubTab('receipts', this)">
                <i class="fa fa-file-text-o"></i> Receipts ({{ collect($getPaidFees)->count() }})
            </button>
        </div>

        <!-- Panel 1: Fee Heads Ledger -->
        <div id="panel-fee-heads">
            @forelse($feeHeadLedger as $head)
                @php
                    $isPaid = $head->due_amount <= 0;
                    $isPartial = !$isPaid && ($head->paid_amount > 0 || $head->discount > 0);
                    $cardStatusClass = $isPaid ? 'card-paid' : ($isPartial ? 'card-partial' : 'card-due');
                    $avatarStatusClass = $isPaid ? 'avatar-paid' : ($isPartial ? 'avatar-partial' : 'avatar-due');
                    $badgeStatusClass = $isPaid ? 'status-badge-active' : ($isPartial ? 'status-badge-warning' : 'status-badge-inactive');
                    $statusText = $isPaid ? 'PAID' : ($isPartial ? 'PARTIAL' : 'DUE');
                @endphp
                <div class="student-mob-card {{ $cardStatusClass }}">
                    <!-- Card Header -->
                    <div class="student-card-header">
                        <div class="student-avatar-box {{ $avatarStatusClass }}">
                            @if($isPaid)
                                <i class="fa fa-check"></i>
                            @elseif($isPartial)
                                <i class="fa fa-hourglass-half"></i>
                            @else
                                <i class="fa fa-exclamation"></i>
                            @endif
                        </div>
                        <div class="student-header-info">
                            <div class="student-name-row">
                                <span class="student-mob-name">{{ $head->name }}</span>
                                <span class="student-status-badge {{ $badgeStatusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            <div class="student-pills-wrap">
                                <span class="student-adm-badge">Head #{{ $head->fees_group_id }}</span>
                                @if($head->payments->count() > 0)
                                    <span class="student-class-badge">
                                        <i class="fa fa-check-circle"></i> {{ $head->payments->count() }} {{ Str::plural('Payment', $head->payments->count()) }}
                                    </span>
                                @else
                                    <span class="student-class-badge" style="background:#f1f5f9; color:#64748b;">
                                        No Payments
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2-Column Meta Grid -->
                    <div class="student-meta-grid">
                        <div class="student-meta-item">
                            <i class="fa fa-calculator text-primary"></i>
                            <span>Assigned: <b>₹{{ number_format($head->assigned_amount, 2) }}</b></span>
                        </div>
                        <div class="student-meta-item">
                            <i class="fa fa-tag text-warning"></i>
                            <span>Disc: <b>₹{{ number_format($head->discount, 2) }}</b></span>
                        </div>
                        <div class="student-meta-item">
                            <i class="fa fa-check-circle text-success"></i>
                            <span>Received: <b class="text-success">₹{{ number_format($head->paid_amount, 2) }}</b></span>
                        </div>
                        <div class="student-meta-item">
                            <i class="fa fa-clock-o {{ $head->due_amount > 0 ? 'text-danger' : '' }}"></i>
                            <span>Due: <b class="{{ $head->due_amount > 0 ? 'text-danger' : 'text-muted' }}">₹{{ number_format($head->due_amount, 2) }}</b></span>
                        </div>
                    </div>

                    <!-- Action Row -->
                    <div class="student-card-actions">
                        @if($head->payments->isNotEmpty())
                            <button type="button" class="mob-btn-action btn-act-edit" onclick="toggleFeeTxDrawer('{{ $head->fees_group_id }}')">
                                <i class="fa fa-list-alt"></i> Receipts ({{ $head->payments->count() }}) <i class="fa fa-chevron-down ml-1" id="fee-arrow-{{ $head->fees_group_id }}" style="font-size: 9px;"></i>
                            </button>
                        @endif
                        @if($head->due_amount > 0)
                            <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo . '&layout=mobile') }}" class="mob-btn-action btn-act-fee">
                                <i class="fa fa-plus-circle"></i> Collect Fee
                            </a>
                        @endif
                    </div>

                    <!-- Expandable Drawer for Payments -->
                    @if($head->payments->isNotEmpty())
                        <div class="tx-drawer-box" id="fee-tx-drawer-{{ $head->fees_group_id }}">
                            <div class="tx-drawer-head">
                                <span><i class="fa fa-history text-primary"></i> Payment Receipts</span>
                                <span>{{ $head->payments->count() }} {{ Str::plural('Transaction', $head->payments->count()) }}</span>
                            </div>
                            @foreach($head->payments as $payment)
                                @php
                                    $rawPayDate = $payment->date ?: ($payment->payment_date ?: $payment->created_at);
                                    $payDate = !empty($rawPayDate) ? \Carbon\Carbon::parse($rawPayDate) : null;
                                    $payTitle = !empty($payment->receipt_no) ? 'Receipt #'.$payment->receipt_no : (!empty($payment->invoice_id) ? 'Invoice #'.$payment->invoice_id : 'Fee Payment');
                                @endphp
                                <div class="tx-item-card">
                                    <div class="tx-item-left">
                                        <div class="tx-icon-pill" title="{{ $payDate ? $payDate->format('d-m-Y') : '' }}">
                                            @if($payDate)
                                                <span class="tx-date-day">{{ $payDate->format('d') }}</span>
                                                <span class="tx-date-mon">{{ $payDate->format('M') }}</span>
                                            @else
                                                <i class="fa fa-calendar-check-o" style="font-size: 11px;"></i>
                                            @endif
                                        </div>
                                        <div class="tx-item-details">
                                            <span class="tx-receipt-title">{{ $payTitle }}</span>
                                            <div class="tx-meta-sub">
                                                <span><i class="fa fa-calendar-o"></i> {{ $payDate ? $payDate->format('d M Y') : '-' }}</span>
                                                <span class="tx-mode-tag">{{ $payment->payment_mode ?: 'Cash' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tx-item-right">
                                        <span class="tx-amount-badge">+₹{{ number_format((float) $payment->paid_amount, 2) }}</span>
                                        @if(!empty($payment->invoice_id))
                                            <a href="{{ url('fees_receipt/' . $payment->invoice_id . '/view') }}" target="_blank" class="tx-btn-view" title="Print Receipt">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted bg-white border rounded">
                    <i class="fa fa-inr fa-2x mb-2 text-primary d-block"></i>
                    No fee heads are assigned to this student.
                </div>
            @endforelse
        </div>

        <!-- Panel 2: Fee Receipts & Invoices -->
        <div id="panel-fee-receipts" style="display: none;">
            @forelse(collect($getPaidFees) as $invoice)
                @php
                    $rawInvDate = $invoice->payment_date ?: ($invoice->date ?: $invoice->created_at);
                    $invDate = !empty($rawInvDate) ? \Carbon\Carbon::parse($rawInvDate) : null;
                    $invTitle = !empty($invoice->invoice_no) ? 'Invoice #'.$invoice->invoice_no : (!empty($invoice->receipt_no) ? 'Receipt #'.$invoice->receipt_no : 'Invoice #'.$invoice->id);
                @endphp
                <div class="student-mob-card card-receipt">
                    <div class="student-card-header">
                        <div class="tx-icon-pill">
                            @if($invDate)
                                <span class="tx-date-day">{{ $invDate->format('d') }}</span>
                                <span class="tx-date-mon">{{ $invDate->format('M') }}</span>
                            @else
                                <i class="fa fa-file-text-o" style="font-size: 12px;"></i>
                            @endif
                        </div>
                        <div class="receipt-main-col">
                            <span class="student-mob-name" style="line-height: 1.25; display: block;">{{ $invTitle }}</span>
                            <div class="student-pills-wrap" style="margin-top: 4px;">
                                <span class="tx-mode-tag">{{ $invoice->payment_mode ?: 'Cash' }}</span>
                                <span class="student-adm-badge"><i class="fa fa-calendar-o"></i> {{ $invDate ? $invDate->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                        <div class="receipt-amount-col">
                            <span class="tx-amount-badge">+₹{{ number_format((float)$invoice->amount, 2) }}</span>
                            @if((float)($invoice->discount ?? 0) > 0)
                                <span class="receipt-disc-badge">
                                    <i class="fa fa-tag"></i> Disc: ₹{{ number_format((float)$invoice->discount, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="student-card-actions">
                        <a href="{{ url('fees_receipt/' . $invoice->id . '/view') }}" target="_blank" class="mob-btn-action btn-act-edit">
                            <i class="fa fa-eye"></i> View Receipt
                        </a>
                        <a href="{{ url('fees_receipt/' . $invoice->id . '/download') }}" target="_blank" class="mob-btn-action btn-act-fee">
                            <i class="fa fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="mob-app-empty-state">
                    <div class="mob-empty-glyph glyph-sky">
                        <i class="fa fa-file-text-o"></i>
                    </div>
                    <h5 class="mob-empty-heading">No Fee Receipts</h5>
                    <p class="mob-empty-desc">No payment receipts or invoice records recorded for this student in the current session.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ==========================================
         PANE 3: ATTENDANCE LOG
         ========================================== -->
    <div class="mob-pane" id="pane-attendance">
        <!-- Attendance Overview Card -->
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-calendar-check-o text-primary"></i> Attendance Snapshot</h4>
                <span class="badge badge-primary">{{ $attendanceTotal }} Days Total</span>
            </div>
            <div class="mob-card-body p-2">
                <!-- 4-Column Matrix Grid matching attendence.blade.php -->
                <div class="matrix-pills-row">
                    <div class="matrix-status-cell cell-present">
                        <div class="matrix-status-tag"><i class="fa fa-check-circle text-success"></i> Present</div>
                        <div class="matrix-status-val">{{ $attendancePresent }}</div>
                    </div>
                    <div class="matrix-status-cell cell-absent">
                        <div class="matrix-status-tag"><i class="fa fa-times-circle text-danger"></i> Absent</div>
                        <div class="matrix-status-val">{{ $attendanceAbsent }}</div>
                    </div>
                    <div class="matrix-status-cell cell-rate">
                        <div class="matrix-status-tag"><i class="fa fa-pie-chart text-info"></i> Rate</div>
                        <div class="matrix-status-val">{{ number_format($attendancePercentage, 1) }}%</div>
                    </div>
                    <div class="matrix-status-cell cell-total">
                        <div class="matrix-status-tag"><i class="fa fa-calendar"></i> Days</div>
                        <div class="matrix-status-val">{{ $attendanceTotal }}</div>
                    </div>
                </div>

                <!-- Attendance Rate Progress Track -->
                <div class="progress-meta-row" style="color: #64748b; font-size: 10px; margin-top: 4px;">
                    <span>Presence Ratio</span>
                    <b class="text-primary">{{ number_format($attendancePercentage, 1) }}% Overall</b>
                </div>
                <div class="progress-track-bg" style="height: 6px; background: #e2e8f0;">
                    <div class="progress-track-fill bg-success" style="width: {{ min(100, $attendancePercentage) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Daily Activity Punch Records matching native attendance list -->
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-history text-info"></i> Daily Punch History</h4>
                <span class="badge badge-light border" style="font-size: 9.5px; font-weight: 700;">{{ $recentAttendance->count() }} Punches</span>
            </div>
            <div class="mob-card-body p-2">
                @forelse($recentAttendance as $attendance)
                    @php
                        $attDate = \Carbon\Carbon::parse($attendance->date);
                        $attendanceName = $attendance->profile_status ?? '-';
                        $attendanceKey = $attendance->profile_status_key ?? '';
                        $chipClass = $attendanceKey === 'absent' ? 'chip-absent'
                            : (in_array($attendanceKey, ['late','early_out','halfday'], true) ? 'chip-halfday'
                            : (in_array($attendanceKey, ['holiday','event','leave'], true) ? 'chip-holiday' : 'chip-present'));
                    @endphp
                    <div class="native-list-item">
                        <div class="item-date-badge">
                            <span class="date-badge-day">{{ $attDate->format('d') }}</span>
                            <span class="date-badge-wday">{{ $attDate->format('D') }}</span>
                        </div>
                        <div class="item-info-col">
                            <div class="item-primary-txt">
                                <span>{{ $attDate->format('d M Y') }}</span>
                                <span class="status-chip {{ $chipClass }}">{{ $attendanceName }}</span>
                            </div>
                            <div class="item-secondary-txt">
                                <span class="punch-pill">
                                    <i class="fa fa-sign-in text-success"></i> In: <b>{{ $attendance->time ?: '--:--' }}</b>
                                </span>
                                <span class="punch-pill">
                                    <i class="fa fa-sign-out text-danger"></i> Out: <b>{{ $attendance->out_time ?: '--:--' }}</b>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-calendar-times-o fa-2x mb-2 text-primary d-block"></i>
                        No attendance records logged for this session.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==========================================
         PANE 4: EXAMINATIONS
         ========================================== -->
    <div class="mob-pane" id="pane-exams">
        @if($examResults->isNotEmpty())
            <!-- Exams Snapshot Ribbon -->
            <div class="mob-card" style="background: linear-gradient(135deg, #001833 0%, #002C54 100%); color: #ffffff; border: 1px solid rgba(56, 189, 248, 0.28); border-radius: 4px; padding: 12px 14px; margin-bottom: 12px; box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-size: 13.5px; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 7px; line-height: 1.2;">
                        <i class="fa fa-trophy text-warning" style="font-size: 14px;"></i> Exam Performance Glance
                    </span>
                    <span style="font-size: 10px; font-weight: 800; color: #38bdf8; background: rgba(56, 189, 248, 0.18); border: 1px solid rgba(56, 189, 248, 0.35); padding: 3px 8px; border-radius: 3px; line-height: 1.2; display: inline-flex; align-items: center;">
                        {{ $examResults->count() }} {{ Str::plural('Test', $examResults->count()) }}
                    </span>
                </div>
                <div class="mob-dual-grid" style="margin-bottom: 0; gap: 8px;">
                    <div class="mob-radar-col" style="padding: 8px 10px; border-radius: 4px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14);">
                        <span class="mob-radar-tag" style="font-size: 9px;"><i class="fa fa-line-chart text-info"></i> OVERALL AVG</span>
                        <div class="mob-radar-val" style="color: #38bdf8; font-size: 18px; margin: 3px 0 2px;">{{ number_format($overallExamPercentage, 1) }}%</div>
                        <span class="mob-radar-meta" style="font-size: 9px;">Across all tests</span>
                    </div>
                    <div class="mob-radar-col" style="padding: 8px 10px; border-radius: 4px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14);">
                        <span class="mob-radar-tag" style="font-size: 9px;"><i class="fa fa-star text-warning"></i> HIGHEST SCORE</span>
                        <div class="mob-radar-val" style="color: #34d399; font-size: 18px; margin: 3px 0 2px;">{{ number_format($examResults->max('percentage'), 1) }}%</div>
                        <span class="mob-radar-meta" style="font-size: 9px;">Best performance</span>
                    </div>
                </div>
            </div>
        @endif

        @forelse($examResults as $result)
            @php
                $pct = (float)$result->percentage;
                $gradeLabel = $pct >= 75 ? 'Distinction' : ($pct >= 60 ? '1st Division' : ($pct >= 33 ? 'Passed' : 'Needs Work'));
                $gradeBorder = $pct >= 75 ? '#16a34a' : ($pct >= 60 ? '#0284c7' : ($pct >= 33 ? '#f59e0b' : '#ef4444'));
                $gradeBg = $pct >= 75 ? '#dcfce7' : ($pct >= 60 ? '#e0f2fe' : ($pct >= 33 ? '#fef3c7' : '#fee2e2'));
                $gradeColor = $pct >= 75 ? '#15803d' : ($pct >= 60 ? '#0369a1' : ($pct >= 33 ? '#b45309' : '#dc2626'));
            @endphp
            <div class="student-mob-card" style="border-left: 3px solid {{ $gradeBorder }} !important;">
                <!-- Exam Card Header -->
                <div class="exam-card-header">
                    <div class="exam-title-cluster">
                        <div class="exam-icon-circle">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div class="exam-title-text">
                            <h4 class="exam-title">{{ $result->name }}</h4>
                            <div class="exam-meta-row">
                                <span><i class="fa fa-calendar-o text-primary"></i> {{ !empty($result->date) ? date('d M Y', strtotime($result->date)) : '-' }}</span>
                                <span><i class="fa fa-book text-primary"></i> {{ count($result->subjects) }} Subjects</span>
                            </div>
                        </div>
                    </div>
                    <div class="exam-score-badge">
                        <div class="score-pill" style="border-color: {{ $gradeBorder }}; background: {{ $gradeBg }}; color: {{ $gradeColor }};">
                            {{ number_format($pct, 1) }}%
                        </div>
                        <span class="score-pill-sub" style="color: {{ $gradeColor }};">{{ $gradeLabel }}</span>
                    </div>
                </div>

                <!-- Score Metrics Ribbon -->
                <div class="exam-metrics-ribbon">
                    <div class="ribbon-item">
                        <span class="ribbon-label">Obtained</span>
                        <span class="ribbon-value highlight-green">{{ number_format($result->obtained, 1) }}</span>
                    </div>
                    <div class="ribbon-item">
                        <span class="ribbon-label">Total Max</span>
                        <span class="ribbon-value">{{ number_format($result->maximum, 1) }}</span>
                    </div>
                    <div class="ribbon-item">
                        <span class="ribbon-label">Percentage</span>
                        <span class="ribbon-value highlight-blue">{{ number_format($pct, 1) }}%</span>
                    </div>
                </div>

                <!-- Subject Scores Breakdown -->
                @if(count($result->subjects) > 0)
                    <div class="subject-marks-wrap">
                        @foreach($result->subjects as $subject)
                            <div class="subject-mark-row">
                                <span class="subject-mark-name">{{ $subject->name }}</span>
                                <span class="subject-mark-val">
                                    <b>{{ $subject->is_numeric ? number_format((float) $subject->marks, 1) : $subject->marks }}</b> / {{ number_format((float) $subject->maximum, 1) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Full Scorecard Form Button -->
                <form action="{{ url('exam_wise_report') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $result->exam_id }}">
                    <input type="hidden" name="class_type_id" value="{{ $student->class_type_id }}">
                    <input type="hidden" name="admission_id" value="{{ $student->id }}">
                    @foreach($result->subjects->pluck('id')->filter()->unique() as $subjectId)
                        <input type="hidden" name="subject_id[]" value="{{ $subjectId }}">
                    @endforeach
                    <button type="submit" class="btn btn-xs btn-outline-primary btn-block font-weight-bold" style="height: 30px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa fa-file-text-o"></i> View Full Scorecard
                    </button>
                </form>
            </div>
        @empty
            <div class="text-center py-4 text-muted bg-white border rounded">
                <i class="fa fa-trophy fa-2x mb-2 text-primary d-block"></i>
                No examination marks recorded for this student.
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         PANE 5: DOCUMENTS VAULT
         ========================================== -->
    <div class="mob-pane" id="pane-docs">
        <!-- Sub Bar -->
        <div class="mob-sub-bar">
            <div class="mob-sub-bar-left">
                <i class="fa fa-folder-open text-primary"></i>
                <span>Document Vault</span>
                <span class="mob-badge-counter">{{ collect($getDocuments)->count() }}</span>
            </div>
            <button type="button" class="mob-sub-btn-primary" onclick="openDocUploadSheet()">
                <i class="fa fa-upload"></i> Upload Doc
            </button>
        </div>

        @forelse($getDocuments as $document)
            @php
                $ext = strtolower(pathinfo($document->file ?? '', PATHINFO_EXTENSION));
                $isPdf = $ext === 'pdf';
                $isImg = in_array($ext, ['jpg','jpeg','png','webp'], true);
            @endphp
            <div class="mob-doc-tile">
                <div class="mob-doc-top">
                    <div class="mob-doc-icon {{ $isPdf ? 'doc-pdf' : ($isImg ? 'doc-img' : 'doc-generic') }}">
                        <i class="fa {{ $isPdf ? 'fa-file-pdf-o' : ($isImg ? 'fa-file-image-o' : 'fa-file-text-o') }}"></i>
                    </div>
                    <div class="mob-doc-body">
                        <div class="mob-doc-name-row">
                            <span class="mob-doc-title" title="{{ $document->title }}">{{ $document->title ?: 'Document' }}</span>
                            <span class="mob-doc-ext">{{ strtoupper($ext ?: 'FILE') }}</span>
                        </div>
                        <div class="mob-doc-meta">
                            <span><i class="fa fa-calendar-o"></i> {{ $document->created_at ? date('d M Y', strtotime($document->created_at)) : '-' }}</span>
                            <span class="student-status-badge status-badge-active" style="font-size: 8.5px; padding: 1px 5px;"><i class="fa fa-check"></i> Verified</span>
                        </div>
                    </div>
                </div>

                @if(!empty($document->remark))
                    <div class="mob-doc-remark">
                        <i class="fa fa-comment-o text-muted mr-1"></i> {{ $document->remark }}
                    </div>
                @endif

                <div class="mob-doc-actions">
                    <a href="{{ asset('schoolimage/student_document/' . $document->file) }}" target="_blank" class="mob-doc-act-btn btn-act-view">
                        <i class="fa fa-eye"></i> View
                    </a>
                    <button type="button" class="mob-doc-act-btn" onclick="openDocEditSheet('{{ $document->id }}', '{{ addslashes($document->title) }}', '{{ addslashes($document->remark) }}')">
                        <i class="fa fa-pencil"></i> Edit
                    </button>
                    <a href="{{ url('document_delete/' . $document->id) }}" onclick="return confirm('Delete this document?')" class="mob-doc-act-btn btn-act-del">
                        <i class="fa fa-trash-o"></i> Delete
                    </a>
                </div>
            </div>
        @empty
            <div class="mob-app-empty-state">
                <div class="mob-empty-glyph glyph-blue">
                    <i class="fa fa-folder-open-o"></i>
                </div>
                <h5 class="mob-empty-heading">No Documents Uploaded</h5>
                <p class="mob-empty-desc">Attach Aadhaar, marksheets, transfer certificate, or other student verification documents.</p>
                <button type="button" class="mob-empty-action-btn" onclick="openDocUploadSheet()">
                    <i class="fa fa-upload"></i> Upload First Document
                </button>
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         PANE 6: SIBLINGS
         ========================================== -->
    <div class="mob-pane" id="pane-family">
        <!-- Sub Bar -->
        <div class="mob-sub-bar">
            <div class="mob-sub-bar-left">
                <i class="fa fa-users text-primary"></i>
                <span>Sibling Information</span>
                <span class="mob-badge-counter">{{ collect($siblings)->count() }}</span>
            </div>
            <span style="font-size: 10px; color: #64748b; font-weight: 700;">Enrolled in Institute</span>
        </div>

        @forelse($siblings as $sibling)
            @php
                $sibPhoto = !empty($sibling->image) ? $imageBase.'/profile/'.$sibling->image : $defaultPhoto;
                $sibName = trim(($sibling->first_name ?? '').' '.($sibling->last_name ?? ''));
                $sibActive = (int)($sibling->status ?? 1) === 1;
            @endphp
            <div class="mob-sibling-card">
                <div class="mob-sib-top">
                    <div class="mob-sib-avatar-wrap">
                        <img src="{{ $sibPhoto }}" class="mob-sib-avatar-img" alt="{{ $sibName }}" onerror="this.src='{{ $defaultPhoto }}'">
                    </div>
                    <div class="mob-sib-meta">
                        <div class="mob-sib-name-row">
                            <span class="mob-sib-name" title="{{ $sibName }}">{{ $sibName ?: 'Sibling' }}</span>
                            <span class="mob-badge-pill {{ $sibActive ? 'mob-badge-active' : 'mob-badge-inactive' }}">
                                <i class="fa {{ $sibActive ? 'fa-check' : 'fa-ban' }}"></i> {{ $sibActive ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </div>
                        <div class="mob-sib-pills">
                            <span class="student-adm-badge"><i class="fa fa-hashtag"></i> Adm: {{ $sibling->admissionNo ?? '-' }}</span>
                            <span class="student-class-badge"><i class="fa fa-book"></i> {{ $sibling->class_name ?? '-' }}</span>
                            @if(!empty($sibling->roll_no))
                                <span class="student-adm-badge">Roll {{ $sibling->roll_no }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mob-sib-action-row">
                    <a href="{{ url('studentDetail/'.$sibling->id.'?layout=mobile') }}" class="mob-sib-link-btn">
                        <i class="fa fa-id-card-o"></i> View Sibling Profile <i class="fa fa-angle-right ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="mob-app-empty-state">
                <div class="mob-empty-glyph glyph-sky">
                    <i class="fa fa-users"></i>
                </div>
                <h5 class="mob-empty-heading">No Sibling Records</h5>
                <p class="mob-empty-desc">No other family members or siblings found matching parent mobile numbers in this institute.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- Hidden File Input for Avatar Photo -->
<input type="file" id="mobPhotoInput" style="display:none;" accept="image/jpeg,image/png,image/jpg,image/webp">

<!-- ==========================================
     NATIVE BOTTOM SHEET MODALS
     ========================================== -->

<!-- Backdrop -->
<div class="mob-sheet-backdrop" id="mobSheetBackdrop" onclick="closeAllSheets()"></div>

<!-- 1. Upload Document Bottom Sheet -->
<form action="{{ url('document_upload/' . ($student->id ?? 0)) }}" method="POST" enctype="multipart/form-data" class="mob-bottom-sheet" id="mobDocUploadSheet">
    @csrf
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-upload text-sky"></i> Upload Student Document</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body">
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Document Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="title" placeholder="e.g. Aadhaar Card, Previous Marksheet" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Remark / Description</label>
            <textarea class="form-control form-control-sm" name="remark" rows="2" placeholder="Optional remark"></textarea>
        </div>
        <div class="form-group mb-0">
            <label class="font-weight-bold" style="font-size:11.5px;">Select File <span class="text-danger">*</span></label>
            <input type="file" class="form-control form-control-sm border p-1" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf" required>
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-cancel-btn" onclick="closeAllSheets()">Cancel</button>
        <button type="submit" class="mob-sheet-submit-btn"><i class="fa fa-cloud-upload mr-1"></i> Upload</button>
    </div>
</form>

<!-- 2. Edit Document Bottom Sheet -->
<form action="{{ url('document_update') }}" method="POST" enctype="multipart/form-data" class="mob-bottom-sheet" id="mobDocEditSheet">
    @csrf
    <input type="hidden" name="document_id" id="mob_edit_doc_id">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-pencil text-sky"></i> Edit Document</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body">
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Document Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="title" id="mob_edit_doc_title" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Remark / Description</label>
            <textarea class="form-control form-control-sm" name="remark" id="mob_edit_doc_remark" rows="2"></textarea>
        </div>
        <div class="form-group mb-0">
            <label class="font-weight-bold" style="font-size:11.5px;">Replace File <small class="text-muted">(Optional)</small></label>
            <input type="file" class="form-control form-control-sm border p-1" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf">
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-cancel-btn" onclick="closeAllSheets()">Cancel</button>
        <button type="submit" class="mob-sheet-submit-btn"><i class="fa fa-save mr-1"></i> Save Changes</button>
    </div>
</form>

<!-- 3. Student Status Bottom Sheet -->
<div class="mob-bottom-sheet" id="mobStatusSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-power-off text-sky"></i> Student Status</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body text-center py-3">
        <div id="mobStatusPromptText" class="font-weight-bold text-dark mb-1" style="font-size:13px;"></div>
        <p class="text-muted small mb-0">Changing status will adjust student's enrollment and portal access.</p>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-cancel-btn" onclick="closeAllSheets()">Cancel</button>
        <button type="button" id="mobConfirmStatusBtn" class="mob-sheet-submit-btn">Confirm Change</button>
    </div>
</div>

<!-- 4. Photo Picker Sheet -->
<div class="mob-bottom-sheet" id="mobPhotoSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-camera text-sky"></i> Profile Photo</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body p-3">
        <button type="button" class="btn btn-primary btn-block mb-2 font-weight-bold" onclick="$('#mobPhotoInput').click(); closeAllSheets();">
            <i class="fa fa-image mr-1"></i> Upload / Change Photo
        </button>
        @if(!empty($student->image))
            <button type="button" class="btn btn-outline-danger btn-block font-weight-bold" onclick="deleteStudentPhoto(); closeAllSheets();">
                <i class="fa fa-trash mr-1"></i> Remove Current Photo
            </button>
        @endif
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="btn btn-secondary btn-block" onclick="closeAllSheets()">Cancel</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // 1. Mobile Segmented Tab Switching
    $('.mob-seg-btn').on('click', function () {
        var targetPane = $(this).data('pane');
        $('.mob-seg-btn').removeClass('active');
        $(this).addClass('active');

        $('.mob-pane').removeClass('active');
        $('#' + targetPane).addClass('active');
    });

    // 2. Photo Upload AJAX
    $('#mobPhotoInput').on('change', function () {
        var fileInput = this;
        if (!fileInput.files || !fileInput.files[0]) return;

        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append('admission_id', '{{ $student->id }}');
        formData.append('action_type', 'upload');
        formData.append('student_img', file);
        formData.append('_token', '{{ csrf_token() }}');

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#mobAvatarImg').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        if (window.toastr) toastr.info('Uploading photo...');

        $.ajax({
            url: '{{ url("imageRotateSave") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res.success || res.status === 'success') {
                    if (window.toastr) toastr.success('Profile photo updated!');
                    if (res.image_url) $('#mobAvatarImg').attr('src', res.image_url);
                } else {
                    if (window.toastr) toastr.error(res.message || 'Upload failed');
                }
            },
            error: function () {
                if (window.toastr) toastr.error('Error uploading photo.');
            }
        });
    });
});

// Fee Drawer Toggle
function toggleFeeTxDrawer(groupId) {
    var drawer = $('#fee-tx-drawer-' + groupId);
    var arrow = $('#fee-arrow-' + groupId);
    if (drawer.hasClass('show')) {
        drawer.removeClass('show');
        arrow.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        drawer.addClass('show');
        arrow.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
}

// Fee Sub-Tab Switcher (Fee Heads vs Receipts)
function switchFeeSubTab(type, btn) {
    $('.mob-subtab-btn').removeClass('active-subtab');
    $(btn).addClass('active-subtab');
    if (type === 'heads') {
        $('#panel-fee-heads').show();
        $('#panel-fee-receipts').hide();
    } else {
        $('#panel-fee-heads').hide();
        $('#panel-fee-receipts').show();
    }
}

// Bottom Sheet Open / Close Helpers
function closeAllSheets() {
    $('.mob-bottom-sheet').removeClass('show');
    $('#mobSheetBackdrop').removeClass('show');
}
function openDocUploadSheet() {
    closeAllSheets();
    $('#mobSheetBackdrop').addClass('show');
    $('#mobDocUploadSheet').addClass('show');
}
function openDocEditSheet(id, title, remark) {
    closeAllSheets();
    $('#mob_edit_doc_id').val(id);
    $('#mob_edit_doc_title').val(title);
    $('#mob_edit_doc_remark').val(remark);
    $('#mobSheetBackdrop').addClass('show');
    $('#mobDocEditSheet').addClass('show');
}
function openPhotoSheet() {
    closeAllSheets();
    $('#mobSheetBackdrop').addClass('show');
    $('#mobPhotoSheet').addClass('show');
}

// Student Status in Mobile
var currentMobStatus = {{ $isActive ? 1 : 0 }};
function openStatusSheet() {
    var nextStatus = currentMobStatus === 1 ? 0 : 1;
    $('#mobStatusPromptText').text(
        nextStatus === 1
            ? 'Activate student "{{ addslashes($studentName) }}"?'
            : 'Deactivate student "{{ addslashes($studentName) }}"?'
    );
    $('#mobConfirmStatusBtn').data('next-status', nextStatus);
    closeAllSheets();
    $('#mobSheetBackdrop').addClass('show');
    $('#mobStatusSheet').addClass('show');
}

$('#mobConfirmStatusBtn').on('click', function () {
    var nextStatus = $(this).data('next-status');
    var btn = $(this);
    btn.prop('disabled', true).text('Updating...');

    $.ajax({
        type: 'POST',
        url: '{{ url("stu_status") }}',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        data: {id: '{{ $student->id }}', status: nextStatus},
        success: function () {
            currentMobStatus = nextStatus;
            closeAllSheets();
            if (window.toastr) toastr.success('Status updated successfully!');
            location.reload();
        },
        error: function () {
            if (window.toastr) toastr.error('Failed to update status.');
        },
        complete: function () {
            btn.prop('disabled', false).text('Confirm Change');
        }
    });
});

// Delete Photo
function deleteStudentPhoto() {
    if (!confirm('Remove profile photo?')) return;
    $.ajax({
        url: '{{ url("imageRotateSave") }}',
        type: 'POST',
        data: {
            admission_id: '{{ $student->id }}',
            action_type: 'delete',
            _token: '{{ csrf_token() }}'
        },
        success: function (res) {
            if (res.success) {
                $('#mobAvatarImg').attr('src', '{{ $defaultPhoto }}');
                if (window.toastr) toastr.success('Photo removed.');
            }
        }
    });
}

// Password Reveal
function toggleMobPassword() {
    var masked = $('#mobPassMasked');
    var plain = $('#mobPassPlain');
    var eye = $('#mobPassEye');

    if (plain.is(':visible')) {
        plain.hide();
        masked.show();
        eye.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
        masked.hide();
        plain.show();
        eye.removeClass('fa-eye').addClass('fa-eye-slash');
    }
}

// Copy Credentials
function copyMobCredentials() {
    var user = '{{ $student->userName }}';
    var pass = '{{ $student->confirm_password }}';
    var text = "Student: {{ addslashes($studentName) }}\nPortal: {{ url('/') }}\nUsername: " + user + "\nPassword: " + pass;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            if (window.toastr) toastr.success('Credentials copied!');
            else alert('Credentials copied!');
        });
    } else {
        var tempInput = document.createElement("textarea");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        if (window.toastr) toastr.success('Credentials copied!');
        else alert('Credentials copied!');
    }
}
</script>
@endsection
