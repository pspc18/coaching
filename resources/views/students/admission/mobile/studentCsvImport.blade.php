@extends('layout.mobile_app')

@section('title', 'Student CSV/Excel Import - Desktop Only')

@php
    $setting = Helper::getSetting();
    $currentSessionName = Session::get('session_name') ?? date('Y');
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE STUDENT CSV IMPORT NOTICE THEME
   Strictly aligned with Arise Mobile Design System (Navy Gradient & High Contrast)
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 10px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 4px;
}
.mob-hero-title {
    font-size: 13px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
}
.mob-hero-title i {
    color: #38bdf8;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    letter-spacing: .02em;
    white-space: nowrap;
    flex-shrink: 0;
}
.mob-hero-subtitle {
    font-size: 10.5px;
    color: #cbd5e1;
    margin: 0;
    line-height: 1.35;
}

/* 2. Main Notice Card */
.mob-notice-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 18px 14px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    text-align: center;
}

/* Device Illustration Badge */
.device-badge-wrap {
    position: relative;
    width: 76px;
    height: 76px;
    margin: 4px auto 12px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
}
.device-badge-main {
    font-size: 34px;
    color: #002C54;
}
.device-badge-sub {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 26px;
    height: 26px;
    background: #fee2e2;
    border: 2px solid #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc2626;
    font-size: 11px;
}

/* Status Pill */
.status-unsupported-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 8px;
}

.notice-heading {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px 0;
    line-height: 1.25;
}
.notice-lead {
    font-size: 12px;
    color: #475569;
    line-height: 1.5;
    margin: 0 auto 14px auto;
    max-width: 95%;
}

/* 3. Reason Breakdown Cards */
.reasons-header {
    font-size: 11px;
    font-weight: 800;
    color: #002C54;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 16px 0 8px 0;
    display: flex;
    align-items: center;
    gap: 6px;
    text-align: left;
}
.reasons-grid {
    display: flex;
    flex-direction: column;
    gap: 7px;
    text-align: left;
    margin-bottom: 12px;
}
.reason-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #002C54;
    border-radius: 4px;
    padding: 9px 11px;
    display: flex;
    align-items: flex-start;
    gap: 9px;
}
.reason-icon {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    margin-top: 1px;
}
.reason-text {
    flex: 1;
}
.reason-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}
.reason-desc {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.4;
    margin: 0;
}

/* 4. Pro Tip Box */
.desktop-tip-box {
    background: #fffbeb;
    border: 1px solid #fef08a;
    border-radius: 4px;
    padding: 9px 11px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 16px;
    text-align: left;
}
.desktop-tip-box i {
    color: #d97706;
    font-size: 13px;
    margin-top: 2px;
    flex-shrink: 0;
}
.desktop-tip-text {
    font-size: 10.5px;
    color: #92400e;
    line-height: 1.45;
    margin: 0;
}
.desktop-tip-text strong {
    color: #78350f;
}

/* 5. Action Buttons Stack */
.mob-actions-stack {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.btn-mob-desktop {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff !important;
    border: 1px solid #001f3d;
    height: 40px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.2);
    transition: all .15s ease;
}
.btn-mob-desktop:active {
    transform: scale(0.98);
}
.btn-mob-single {
    background: #0284c7;
    color: #ffffff !important;
    border: none;
    height: 38px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none !important;
    box-shadow: 0 1px 4px rgba(2, 132, 199, 0.2);
}
.btn-mob-single:active {
    transform: scale(0.98);
}
.btn-mob-back {
    background: #ffffff;
    color: #334155 !important;
    border: 1px solid #cbd5e1;
    height: 36px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none !important;
}
.btn-mob-back:active {
    background: #f1f5f9;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Navy Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-file-excel-o"></i> Bulk Student CSV Import
        </div>
        <div class="mob-session-pill">
            Session {{ $currentSessionName }}
        </div>
    </div>
    <p class="mob-hero-subtitle">
        High-volume student admissions via Microsoft Excel / CSV spreadsheets.
    </p>
</div>

{{-- 2. Main Desktop Requirement Notice Card --}}
<div class="mob-notice-card">
    {{-- Device Illustration Badge --}}
    <div class="device-badge-wrap">
        <i class="fa fa-desktop device-badge-main"></i>
        <div class="device-badge-sub" title="Mobile view not supported">
            <i class="fa fa-mobile"></i>
        </div>
    </div>

    {{-- Status Badge --}}
    <div>
        <span class="status-unsupported-pill">
            <i class="fa fa-exclamation-triangle"></i> Desktop View Required
        </span>
    </div>

    {{-- Main Notice Heading & Content --}}
    <h2 class="notice-heading">Mobile View Not Supported</h2>
    <p class="notice-lead">
        This bulk import utility requires multi-column spreadsheet editing, live column validation, and large file mapping that are not supported on mobile devices.
        <strong>Please open this page in desktop view or on a computer to complete this task.</strong>
    </p>

    {{-- Reason Breakdown --}}
    <div class="reasons-header">
        <i class="fa fa-info-circle text-primary"></i> Why Desktop Is Required:
    </div>

    <div class="reasons-grid">
        <div class="reason-item">
            <div class="reason-icon">
                <i class="fa fa-table"></i>
            </div>
            <div class="reason-text">
                <div class="reason-title">Wide Multi-Column Dataset (30+ Fields)</div>
                <p class="reason-desc">
                    The student import sheet includes admission numbers, names, class sections, parent records, fees, and address fields requiring a wide screen.
                </p>
            </div>
        </div>

        <div class="reason-item">
            <div class="reason-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fa fa-check-square-o"></i>
            </div>
            <div class="reason-text">
                <div class="reason-title">Excel Data Formatting &amp; Dropdowns</div>
                <p class="reason-desc">
                    Dropdown lists (Gender, Blood Group, Class) and date formatting (<code>DD/MM/YYYY</code>) must be configured in desktop spreadsheet applications.
                </p>
            </div>
        </div>

        <div class="reason-item">
            <div class="reason-icon" style="background: #ede9fe; color: #7c3aed;">
                <i class="fa fa-cloud-upload"></i>
            </div>
            <div class="reason-text">
                <div class="reason-title">Drag &amp; Drop Upload &amp; Error Logs</div>
                <p class="reason-desc">
                    Full-screen workspace provides real-time row validation, error review tables, and sample XLSX template downloads.
                </p>
            </div>
        </div>
    </div>

    {{-- Browser Desktop Mode Tip --}}
    <div class="desktop-tip-box">
        <i class="fa fa-lightbulb-o"></i>
        <p class="desktop-tip-text">
            <strong>Quick Mobile Tip:</strong> If you are using Google Chrome or Safari on your phone/tablet, tap your browser menu (<strong>&#8942;</strong> or <strong>Share</strong>) and enable <strong>"Desktop site"</strong> to access the full import panel.
        </p>
    </div>

    {{-- Action Buttons --}}
    <div class="mob-actions-stack">
        <a href="{{ url('studentCsvImport?layout=desktop') }}" class="btn-mob-desktop">
            <i class="fa fa-desktop"></i> Switch to Desktop View
        </a>

        <a href="{{ url('admissionAdd?layout=mobile') }}" class="btn-mob-single">
            <i class="fa fa-user-plus"></i> Add Single Student (Mobile Friendly)
        </a>

        <a href="{{ url('admissionView?layout=mobile') }}" class="btn-mob-back">
            <i class="fa fa-users"></i> Back to Student Directory
        </a>

        <a href="{{ url('dashboard?layout=mobile') }}" class="btn-mob-back" style="border-color: transparent; background: #f8fafc;">
            <i class="fa fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
</div>

@endsection
