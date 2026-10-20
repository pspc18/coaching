@extends('layout.app')

@section('title', 'Fees Settings & Policy Configuration')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP MODERN THEME - FEES SETTINGS & POLICY HUB
   Dark Navy Theme (#002C54) - Ergonomic Tabbed Dashboard
   ========================================================================== */

.admission-page {
    background: #f1f5f9;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.admission-page * {
    box-sizing: border-box;
}
.admission-page section.content,
.admission-page .container-fluid {
    height: 100%;
    display: flex;
    flex-direction: column;
}
.admission-page-layout {
    height: calc(100vh - var(--header-height, 48px) - 20px);
    max-height: calc(100vh - var(--header-height, 48px) - 20px);
    display: flex;
    flex-direction: column;
    padding: 0 4px;
    gap: 6px;
    box-sizing: border-box;
    width: 100%;
    overflow: hidden;
}

/* 1. Top Fixed Header & Tabs Container */
.settings-top-fixed {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

/* Hero Header Banner */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0c3866 50%, #17497d 100%);
    color: #fff;
    border-radius: 3px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 4px rgba(0,44,84,.15);
    flex-shrink: 0;
    width: 100%;
}
.admission-hero-text {
    display: flex;
    flex-direction: column;
}
.admission-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #ffd166;
    font-weight: 700;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
}
.admission-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.admission-subtitle {
    font-size: 11px;
    color: #e2e8f0;
    margin-top: 3px;
    margin-bottom: 0;
}
.admission-hero-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.btn-save-hero {
    background: #10b981;
    color: #fff;
    border: 1px solid #059669;
    padding: 5px 16px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 3px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .15s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,.15);
}
.btn-save-hero:hover {
    background: #059669;
    color: #fff;
    transform: translateY(-1px);
}

/* Tabs Navigation Bar */
.settings-tabs-bar {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 5px 8px;
    overflow-x: auto;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.settings-tab-btn {
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid transparent;
    border-radius: 2px;
    background: transparent;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all .15s ease;
    text-decoration: none !important;
}
.settings-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.settings-tab-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 3px rgba(0,44,84,.2);
}
.settings-tab-btn i {
    font-size: 12.5px;
}

/* Main Form & Middle Scrollable Body */
.settings-form-container {
    flex: 1 1 0;
    min-height: 0;
    height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    gap: 6px;
    margin: 0;
}
.settings-scroll-body {
    flex: 1 1 0;
    min-height: 0;
    height: 0;
    overflow-y: auto !important;
    overflow-x: hidden;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.settings-scroll-body::-webkit-scrollbar {
    width: 8px;
}
.settings-scroll-body::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.settings-scroll-body::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 4px;
}
.settings-scroll-body::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

.settings-section-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 10;
}
.section-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-desc {
    font-size: 11px;
    color: #64748b;
    margin: 2px 0 0 0;
}
.settings-card-body {
    padding: 12px 16px 16px 16px;
}

/* Sub-section Divider Block */
.settings-sub-section {
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #fcfdfe;
    padding: 10px 12px;
    margin-bottom: 12px;
}
.settings-sub-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #002C54;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 6px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Live Receipt Preview Showcase Box (Compact) */
.receipt-preview-banner {
    background: linear-gradient(135deg, #051e38 0%, #002C54 100%);
    border: 1px solid #1e40af;
    border-radius: 4px;
    padding: 8px 14px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    color: #ffffff;
}
.preview-text-col {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.preview-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #ffd166;
    font-weight: 700;
}
.preview-number-badge {
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 16px;
    font-weight: 700;
    color: #38bdf8;
    background: rgba(0,0,0,.35);
    border: 1px dashed rgba(56,189,248,.6);
    padding: 2px 10px;
    border-radius: 3px;
    display: inline-block;
    letter-spacing: .05em;
}
.preview-note {
    font-size: 10.5px;
    color: #cbd5e1;
    margin: 0;
}

/* Grid & Form Group Styles (Compact col-3 style) */
.form-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}
.form-grid-3 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}
.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}
@media (max-width: 1199px) {
    .form-grid-4, .form-grid-3, .form-grid-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 767px) {
    .form-grid-4, .form-grid-3, .form-grid-2 {
        grid-template-columns: 1fr;
    }
}
.form-group-erp {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.form-label-erp {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .02em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 4px;
}
.form-label-erp .badge-req {
    color: #ef4444;
}
.form-control-erp {
    height: 30px;
    padding: 3px 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    color: #0f172a;
    transition: all .15s ease;
    box-sizing: border-box;
    width: 100%;
}
.form-control-erp:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 2px rgba(2,132,199,.2);
}
.form-control-erp[readonly] {
    background: #f8fafc;
    color: #64748b;
}
.form-hint {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
    line-height: 1.25;
}

/* Radio Cards (Selectable Policy Cards - Compact col-3 sizing) */
.radio-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
}
@media (max-width: 1199px) {
    .radio-cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
.radio-card {
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 8px 10px;
    background: #ffffff;
    cursor: pointer;
    transition: all .15s ease;
    display: flex;
    gap: 8px;
    position: relative;
    user-select: none;
    margin-bottom: 0;
}
.radio-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
}
.radio-card.selected {
    border-color: #002C54;
    background: #f0f7ff;
    box-shadow: 0 0 0 1px #002C54;
}
.radio-card input[type="radio"] {
    margin-top: 2px;
    cursor: pointer;
    accent-color: #002C54;
}
.radio-card-content {
    display: flex;
    flex-direction: column;
}
.radio-card-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 1px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.radio-card-desc {
    font-size: 10px;
    color: #64748b;
    line-height: 1.25;
    margin: 0;
}

/* Toggle Switch Styling (Compact) */
.switch-group-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    background: #ffffff;
    margin-bottom: 0;
    gap: 8px;
}
.switch-label-wrap {
    display: flex;
    flex-direction: column;
}
.switch-label-title {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    line-height: 1.2;
}
.switch-label-sub {
    font-size: 9.5px;
    color: #64748b;
    margin: 1px 0 0 0;
    line-height: 1.2;
}
.switch-ctrl {
    position: relative;
    display: inline-block;
    width: 38px;
    height: 20px;
    flex-shrink: 0;
    margin: 0;
}
.switch-ctrl input {
    opacity: 0;
    width: 0;
    height: 0;
}
.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 20px;
}
.switch-slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.switch-ctrl input:checked + .switch-slider {
    background-color: #10b981;
}
.switch-ctrl input:checked + .switch-slider:before {
    transform: translateX(18px);
}

/* Checkbox Chips Grid for Payment Modes */
.chips-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 6px;
}
.chip-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
    margin: 0;
}
.chip-label:hover {
    background: #f1f5f9;
}
.chip-label.active {
    background: #eff6ff;
    border-color: #0284c7;
    color: #0369a1;
}
.chip-label input[type="checkbox"] {
    accent-color: #0284c7;
    margin: 0;
}

/* Paper Layout Showcase Cards */
.layout-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.layout-card {
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    padding: 14px;
    background: #ffffff;
    cursor: pointer;
    transition: all .15s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 8px;
}
.layout-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
}
.layout-card.selected {
    border-color: #002C54;
    background: #f0f7ff;
    box-shadow: 0 0 0 1px #002C54;
}
.layout-icon-box {
    font-size: 28px;
    color: #002C54;
}
.layout-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.layout-desc {
    font-size: 10.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.35;
}

/* Docked Bottom Action Toolbar (Permanently fixed at bottom of page layout) */
.settings-bottom-bar {
    flex-shrink: 0;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 3px;
    padding: 7px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: #ffffff;
    box-shadow: 0 -2px 8px rgba(0,44,84,.18);
}
.settings-pinned-info {
    font-size: 11px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
}
.settings-pinned-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-reset-form {
    background: #ffffff;
    color: #002C54;
    border: 1px solid #cbd5e1;
    padding: 5px 14px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all .15s ease;
}
.btn-reset-form:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.btn-save-main {
    background: #10b981;
    color: #ffffff;
    border: 1px solid #059669;
    padding: 5px 16px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 3px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.btn-save-main:hover {
    background: #059669;
    color: #ffffff;
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                {{-- TOP FIXED REGION: Hero Header + Alerts + Tabs (Never Scrolls) --}}
                <div class="settings-top-fixed">
                    {{-- 1. Top Hero Header Banner --}}
                    <div class="admission-hero">
                        <div class="admission-hero-text">
                            <span class="admission-kicker">
                                <i class="fa fa-sliders mr-1"></i> Fees Management &bull; Institute Configuration Hub
                            </span>
                            <h1 class="admission-title">
                                <span>Fees Settings & Policy Configuration</span>
                            </h1>
                            <p class="admission-subtitle">
                                Configure automated receipt number generation, payment modes, print letterhead formats, and automated alerts.
                                &bull; Academic Session: <strong>{{ $sessionName }}</strong> &bull; Current Receipt Counter: <strong>#{{ $currentCounter }}</strong>
                            </p>
                        </div>

                        <div class="admission-hero-actions">
                            <button type="button" class="btn-save-hero" onclick="$('#feesSettingsForm').submit();">
                                <i class="fa fa-check-circle"></i> Save All Settings
                            </button>
                        </div>
                    </div>

                    {{-- Flash Message Alerts --}}
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show p-2 mb-0" role="alert" style="font-size: 12px; border-radius: 2px;">
                            <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                            <button type="button" class="close p-2" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(!empty($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show p-2 mb-0" role="alert" style="font-size: 12px; border-radius: 2px;">
                            <i class="fa fa-exclamation-triangle mr-1"></i> Please check the form errors below.
                            <button type="button" class="close p-2" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- 2. Section Navigation Tabs --}}
                    <div class="settings-tabs-bar">
                        <a href="#tab-receipt" class="settings-tab-btn active" data-tab="tab-receipt">
                            <i class="fa fa-file-text-o"></i> 1. Receipt Numbering
                        </a>
                        <a href="#tab-payment" class="settings-tab-btn" data-tab="tab-payment">
                            <i class="fa fa-credit-card"></i> 2. Payment Modes & Cashier
                        </a>
                        <a href="#tab-print" class="settings-tab-btn" data-tab="tab-print">
                            <i class="fa fa-print"></i> 3. Receipt Print & Letterhead
                        </a>
                        <a href="#tab-alerts" class="settings-tab-btn" data-tab="tab-alerts">
                            <i class="fa fa-bell-o"></i> 4. Automated Alerts
                        </a>
                    </div>
                </div>

                {{-- 3. Settings Form: Scrollable Middle Body & Docked Bottom Bar --}}
                <form action="{{ url('fees/settings/update') }}" method="POST" id="feesSettingsForm" class="settings-form-container">
                    @csrf

                    {{-- Middle Scrollable Area: Only this body scrolls --}}
                    <div class="settings-scroll-body">

                        {{-- ============================================================
                             TAB 1: RECEIPT NUMBER GENERATOR SETTINGS
                             ============================================================ --}}
                        <div class="tab-pane-content" id="tab-receipt">
                            <div class="settings-section-header">
                                <div>
                                    <h2 class="section-title"><i class="fa fa-file-text-o"></i> Receipt Number Generation Format</h2>
                                    <p class="section-desc">Customize automated receipt numbering, prefix, suffix, digits padding, and starting sequence.</p>
                                </div>
                            </div>

                            <div class="settings-card-body">
                                {{-- Live Receipt Preview Showcase Box (Compact Single Row) --}}
                                <div class="receipt-preview-banner">
                                    <div class="preview-text-col">
                                        <span class="preview-kicker"><i class="fa fa-eye"></i> Live Receipt Preview:</span>
                                        <span class="preview-number-badge" id="liveReceiptPreview">{{ $samplePreview }}</span>
                                        <span class="preview-note">Updates in real-time as you change prefix, padding, or session options.</span>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-light py-1 px-2" id="btnTestPreview" style="font-size: 10.5px;">
                                            <i class="fa fa-refresh"></i> Refresh Preview
                                        </button>
                                    </div>
                                </div>

                                {{-- Section 1: Mode, Prefix, Padding & Auto Counter (4 Columns: col-3 - 3 - 3 - 3) --}}
                                <div class="settings-sub-section">
                                    <div class="settings-sub-title">
                                        <i class="fa fa-sliders"></i> 1. Numbering Mode & Format Configuration
                                    </div>
                                    <div class="row">
                                        {{-- Col 1: Receipt Numbering Mode (col-3) --}}
                                        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                                            <div class="form-group-erp">
                                                <label class="form-label-erp">Receipt Numbering Mode</label>
                                                <label class="radio-card selected" style="cursor: default; padding: 6px 10px;">
                                                    <input type="radio" name="receipt_number_type" value="both" checked>
                                                    <div class="radio-card-content">
                                                        <span class="radio-card-title"><i class="fa fa-magic"></i> Auto + Manual Mode</span>
                                                        <p class="radio-card-desc">Auto session sequence + manual entry allowed</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Col 2: Receipt Prefix (col-3) --}}
                                        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                                            <div class="form-group-erp">
                                                <label class="form-label-erp" for="receipt_prefix">Receipt Prefix</label>
                                                <input type="text" class="form-control-erp preview-trigger" id="receipt_prefix" name="receipt_prefix" 
                                                       value="{{ old('receipt_prefix', $setting->receipt_prefix) }}" placeholder="e.g. REC-, COACH-">
                                                <span class="form-hint">Prefix before number (e.g. <code>REC-</code>)</span>
                                            </div>
                                        </div>

                                        {{-- Col 3: Digits Padding Length (col-3) --}}
                                        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                                            <div class="form-group-erp">
                                                <label class="form-label-erp" for="receipt_digit_padding">Digits Padding Length <span class="badge-req">*</span></label>
                                                <select class="form-control-erp preview-trigger" id="receipt_digit_padding" name="receipt_digit_padding">
                                                    <option value="3" {{ (int) $setting->receipt_digit_padding === 3 ? 'selected' : '' }}>3 Digits (e.g. 001)</option>
                                                    <option value="4" {{ (int) $setting->receipt_digit_padding === 4 ? 'selected' : '' }}>4 Digits (e.g. 0001) - Standard</option>
                                                    <option value="5" {{ (int) $setting->receipt_digit_padding === 5 ? 'selected' : '' }}>5 Digits (e.g. 00001)</option>
                                                    <option value="6" {{ (int) $setting->receipt_digit_padding === 6 ? 'selected' : '' }}>6 Digits (e.g. 000001)</option>
                                                </select>
                                                <span class="form-hint">Leading zeros padding length</span>
                                            </div>
                                        </div>

                                        {{-- Col 4: Auto Session Counter Status (col-3) --}}
                                        <div class="col-lg-3 col-md-6">
                                            <input type="hidden" name="receipt_reset_cycle" value="session">
                                            <div class="form-group-erp">
                                                <label class="form-label-erp">Session Counter ({{ $sessionName }})</label>
                                                <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 3px; padding: 6px 10px; display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                                    <div style="font-size: 10.5px; color: #166534; font-weight: 700; line-height: 1.25;">
                                                        <i class="fa fa-check-circle"></i> Auto FY Reset<br>
                                                        <span style="font-weight: 500; font-size: 9.5px; color: #15803d;">From Fees Table</span>
                                                    </div>
                                                    <div style="text-align: right; line-height: 1.25;">
                                                        <span class="badge badge-light" style="border: 1px solid #bbf7d0; font-size: 10px; padding: 2px 6px; color: #166534;">
                                                            Used: #{{ $currentCounter }}
                                                        </span>
                                                        <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px; display: inline-block; margin-top: 2px;">
                                                            Next: #{{ $nextCounter }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section 2: Date & Session Inclusion Options (col-3 - 3) --}}
                                <div class="settings-sub-section mb-0">
                                    <div class="settings-sub-title">
                                        <i class="fa fa-calendar"></i> 2. Date & Session Inclusion Options
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                                            <div class="switch-group-row">
                                                <div class="switch-label-wrap">
                                                    <span class="switch-label-title">Include Academic Session</span>
                                                    <span class="switch-label-sub">Adds session (<code>{{ $sessionName }}</code>)</span>
                                                </div>
                                                <label class="switch-ctrl">
                                                    <input type="checkbox" name="receipt_include_session" value="1" class="preview-trigger" 
                                                           {{ $setting->receipt_include_session ? 'checked' : '' }}>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <div class="switch-group-row">
                                                <div class="switch-label-wrap">
                                                    <span class="switch-label-title">Include Collection Month</span>
                                                    <span class="switch-label-sub">Adds 2-digit month (<code>{{ date('m') }}</code>)</span>
                                                </div>
                                                <label class="switch-ctrl">
                                                    <input type="checkbox" name="receipt_include_month" value="1" class="preview-trigger" 
                                                           {{ $setting->receipt_include_month ? 'checked' : '' }}>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 2: PAYMENT MODES & CASHIER CONTROLS
                             ============================================================ --}}
                        <div class="tab-pane-content" id="tab-payment" style="display: none;">
                            <div class="settings-section-header">
                                <div>
                                    <h2 class="section-title"><i class="fa fa-credit-card"></i> Payment Modes & Cashier Controls</h2>
                                    <p class="section-desc">Manage enabled payment methods, partial payment limits, and discount authorization ceilings.</p>
                                </div>
                            </div>

                            <div class="settings-card-body">
                                {{-- Enabled Payment Modes --}}
                                <div class="form-group-erp mb-3">
                                    <label class="form-label-erp">Accepted Payment Modes at Counter</label>
                                    <div class="chips-grid">
                                        @foreach($paymentModes as $pm)
                                            <label class="chip-label {{ in_array((string)$pm->id, $allowedModesArray) ? 'active' : '' }}">
                                                <input type="checkbox" name="allowed_payment_modes[]" value="{{ $pm->id }}" 
                                                       {{ in_array((string)$pm->id, $allowedModesArray) ? 'checked' : '' }}>
                                                <span>{{ $pm->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <span class="form-hint mt-1">Select the payment methods visible to cashiers during collection</span>
                                </div>

                                {{-- Partial Payment Policies --}}
                                <div class="form-grid-2">
                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Allow Partial / Split Payments</span>
                                            <span class="switch-label-sub">Allow students to deposit less than the full installment amount</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="allow_partial_payment" value="1" {{ $setting->allow_partial_payment ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Allow Manual Discount / Concession</span>
                                            <span class="switch-label-sub">Allow cashier to enter fee discounts at the collection counter</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="allow_manual_discount" value="1" {{ $setting->allow_manual_discount ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-grid-3">
                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="min_partial_amount">Minimum Partial Payment (₹)</label>
                                        <input type="number" step="0.01" min="0" class="form-control-erp" id="min_partial_amount" name="min_partial_amount" 
                                               value="{{ old('min_partial_amount', $setting->min_partial_amount) }}" placeholder="0.00">
                                        <span class="form-hint">Minimum payment amount accepted in a single receipt</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="min_deposit_percentage">Minimum Deposit at Admission (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control-erp" id="min_deposit_percentage" name="min_deposit_percentage" 
                                               value="{{ old('min_deposit_percentage', $setting->min_deposit_percentage) }}" placeholder="e.g. 25">
                                        <span class="form-hint">Mandatory upfront deposit percentage for new admissions</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="max_discount_percentage">Maximum Discount Limit (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control-erp" id="max_discount_percentage" name="max_discount_percentage" 
                                               value="{{ old('max_discount_percentage', $setting->max_discount_percentage) }}" placeholder="e.g. 20">
                                        <span class="form-hint">Ceiling limit for discount without super-admin authorization</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 5: RECEIPT PRINT & LETTERHEAD FORMAT
                             ============================================================ --}}
                        <div class="tab-pane-content" id="tab-print" style="display: none;">
                            <div class="settings-section-header">
                                <div>
                                    <h2 class="section-title"><i class="fa fa-print"></i> Receipt Print & Letterhead Format</h2>
                                    <p class="section-desc">Choose paper layout (Full A4, Dual Student/Office Copy, Thermal POS slip), logos, and terms.</p>
                                </div>
                            </div>

                            <div class="settings-card-body">
                                {{-- Layout Selection Cards --}}
                                <label class="form-label-erp mb-2">Receipt Paper Layout <span class="badge-req">*</span></label>
                                <div class="layout-cards-grid">
                                    <label class="layout-card {{ $setting->receipt_layout === 'a4_dual' ? 'selected' : '' }}">
                                        <input type="radio" name="receipt_layout" value="a4_dual" style="display:none;" 
                                               {{ $setting->receipt_layout === 'a4_dual' ? 'checked' : '' }}>
                                        <div class="layout-icon-box"><i class="fa fa-columns"></i></div>
                                        <span class="layout-title">Dual Copy (A4 Half Sheet)</span>
                                        <p class="layout-desc">Prints Student Copy & Office Copy side-by-side on single A4 sheet. Saves 50% paper!</p>
                                    </label>

                                    <label class="layout-card {{ $setting->receipt_layout === 'a4_single' ? 'selected' : '' }}">
                                        <input type="radio" name="receipt_layout" value="a4_single" style="display:none;" 
                                               {{ $setting->receipt_layout === 'a4_single' ? 'checked' : '' }}>
                                        <div class="layout-icon-box"><i class="fa fa-file-text-o"></i></div>
                                        <span class="layout-title">Standard Full A4</span>
                                        <p class="layout-desc">Full page detailed invoice with itemized breakdown and institutional letterhead.</p>
                                    </label>

                                    <label class="layout-card {{ $setting->receipt_layout === 'thermal_pos' ? 'selected' : '' }}">
                                        <input type="radio" name="receipt_layout" value="thermal_pos" style="display:none;" 
                                               {{ $setting->receipt_layout === 'thermal_pos' ? 'checked' : '' }}>
                                        <div class="layout-icon-box"><i class="fa fa-ticket"></i></div>
                                        <span class="layout-title">Thermal POS Slip (80mm)</span>
                                        <p class="layout-desc">Compact 3-inch roll format for fast thermal receipt printers at busy counters.</p>
                                    </label>
                                </div>

                                {{-- Print Elements Toggles --}}
                                <div class="form-grid-2">
                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Show School / Coaching Logo</span>
                                            <span class="switch-label-sub">Display official logo at top of printed receipt</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="show_school_logo" value="1" {{ $setting->show_school_logo ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Show "PAID" Watermark / Stamp</span>
                                            <span class="switch-label-sub">Subtle green authentic paid stamp in receipt background</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="show_watermark" value="1" {{ $setting->show_watermark ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Show Authorized Signature Box</span>
                                            <span class="switch-label-sub">Designated cashier and parent signature line at bottom</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="show_signature_box" value="1" {{ $setting->show_signature_box ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Show Payment Mode & Transaction ID</span>
                                            <span class="switch-label-sub">Display UPI reference, cheque number, or bank details</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="show_payment_mode_details" value="1" {{ $setting->show_payment_mode_details ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Header Title & Terms --}}
                                <div class="form-group-erp mb-3">
                                    <label class="form-label-erp" for="receipt_header_title">Receipt Header Title</label>
                                    <input type="text" class="form-control-erp" id="receipt_header_title" name="receipt_header_title" 
                                           value="{{ old('receipt_header_title', $setting->receipt_header_title) }}" placeholder="FEE RECEIPT">
                                </div>

                                <div class="form-group-erp">
                                    <label class="form-label-erp" for="receipt_terms_conditions">Terms & Conditions / Footer Note</label>
                                    <textarea class="form-control-erp" id="receipt_terms_conditions" name="receipt_terms_conditions" 
                                              rows="3" style="height: auto; font-size: 11.5px;">{{ old('receipt_terms_conditions', $setting->receipt_terms_conditions) }}</textarea>
                                    <span class="form-hint">Printed at the bottom of student receipts (e.g. non-refundable policies, preserve for hall ticket)</span>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 6: AUTOMATED ALERTS & NOTIFICATIONS
                             ============================================================ --}}
                        <div class="tab-pane-content" id="tab-alerts" style="display: none;">
                            <div class="settings-section-header">
                                <div>
                                    <h2 class="section-title"><i class="fa fa-bell-o"></i> Automated Alerts & Reminders</h2>
                                    <p class="section-desc">Manage automated digital receipts via WhatsApp, SMS, and scheduled fee due reminders.</p>
                                </div>
                            </div>

                            <div class="settings-card-body">
                                <div class="form-grid-2">
                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title"><i class="fa fa-whatsapp text-success"></i> Instant WhatsApp Receipt</span>
                                            <span class="switch-label-sub">Send automated receipt with fee breakdown to parent mobile upon collection</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="send_receipt_whatsapp" value="1" {{ $setting->send_receipt_whatsapp ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title"><i class="fa fa-commenting-o text-primary"></i> Instant SMS Fee Receipt</span>
                                            <span class="switch-label-sub">Dispatch SMS confirmation via integrated SMS gateway</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="send_receipt_sms" value="1" {{ $setting->send_receipt_sms ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title"><i class="fa fa-clock-o text-warning"></i> Scheduled Due Fee Reminders</span>
                                            <span class="switch-label-sub">Send reminder alert before upcoming due date</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="due_reminder_enabled" value="1" {{ $setting->due_reminder_enabled ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title"><i class="fa fa-exclamation-circle text-danger"></i> Overdue Notice Alert</span>
                                            <span class="switch-label-sub">Send overdue reminder notice once due date passes</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="overdue_notice_enabled" value="1" {{ $setting->overdue_notice_enabled ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-grid-3">
                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="due_reminder_days_before">Days Before Due Date for Reminder</label>
                                        <input type="number" min="1" max="30" class="form-control-erp" id="due_reminder_days_before" name="due_reminder_days_before" 
                                               value="{{ old('due_reminder_days_before', $setting->due_reminder_days_before) }}" placeholder="e.g. 3">
                                        <span class="form-hint">E.g., send reminder 3 days prior to the 10th of the month</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- End .settings-scroll-body --}}

                    {{-- Docked Bottom Action Toolbar (Permanently fixed at bottom of page layout) --}}
                    <div class="settings-bottom-bar">
                        <div class="settings-pinned-info">
                            <i class="fa fa-info-circle mr-1"></i>
                            <span>All settings are applied immediately across fee collection, ledger, and receipts.</span>
                        </div>
                        <div class="settings-pinned-actions">
                            <a href="{{ url('fees/settings') }}" class="btn-reset-form">
                                <i class="fa fa-undo"></i> Reset Changes
                            </a>
                            <button type="submit" class="btn-save-main">
                                <i class="fa fa-check-circle"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </section>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {

    // 1. Tab Switching
    $('.settings-tab-btn').on('click', function(e) {
        e.preventDefault();
        const tabId = $(this).data('tab');
        
        $('.settings-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.tab-pane-content').hide();
        $('#' + tabId).show();

        // Scroll middle container to top on tab change
        $('.settings-scroll-body').scrollTop(0);

        // Update URL hash without scroll
        if (history.pushState) {
            history.pushState(null, null, '#' + tabId);
        }
    });

    // Check hash on page load
    if (window.location.hash) {
        const hash = window.location.hash.replace('#', '');
        const targetBtn = $('.settings-tab-btn[data-tab="' + hash + '"]');
        if (targetBtn.length) {
            targetBtn.trigger('click');
        }
    }

    // 2. Radio Card Highlighting
    $('.radio-card input[type="radio"]').on('change', function() {
        const name = $(this).attr('name');
        $('input[name="' + name + '"]').closest('.radio-card').removeClass('selected');
        $(this).closest('.radio-card').addClass('selected');
    });

    // 3. Layout Cards Selection
    $('.layout-card').on('click', function() {
        $('.layout-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // 4. Payment Modes Chip Active State
    $('.chip-label input[type="checkbox"]').on('change', function() {
        $(this).closest('.chip-label').toggleClass('active', $(this).prop('checked'));
    });

    // 5. Dynamic Live Receipt Preview
    function updateLivePreview() {
        const prefix = $('#receipt_prefix').val();
        const suffix = '';
        const padding = parseInt($('#receipt_digit_padding').val()) || 4;
        const includeSession = $('input[name="receipt_include_session"]').is(':checked') ? 1 : 0;
        const includeMonth = $('input[name="receipt_include_month"]').is(':checked') ? 1 : 0;
        const counter = {{ $nextCounter }};
        const sessionName = "{{ $sessionName }}";

        $.ajax({
            url: "{{ url('fees/settings/preview-receipt') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                prefix: prefix,
                suffix: suffix,
                padding: padding,
                include_session: includeSession,
                include_month: includeMonth,
                counter: counter,
                session_name: sessionName
            },
            success: function(res) {
                if (res && res.formatted) {
                    $('#liveReceiptPreview').text(res.formatted);
                }
            },
            error: function() {
                // Fallback client-side generation if offline
                let sessionPart = includeSession ? (sessionName.replace(/[^0-9\-]/g, '') + '-') : '';
                let monthPart = includeMonth ? ("{{ date('m') }}" + '-') : '';
                let numStr = String(counter).padStart(padding, '0');
                $('#liveReceiptPreview').text(prefix + sessionPart + monthPart + numStr);
            }
        });
    }

    $('.preview-trigger').on('input change', function() {
        updateLivePreview();
    });

    $('#btnTestPreview').on('click', function() {
        updateLivePreview();
    });
});
</script>
@endsection
