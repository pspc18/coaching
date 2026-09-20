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
    padding: 16px 20px 24px 20px;
}

/* Live Receipt Preview Showcase Box */
.receipt-preview-banner {
    background: linear-gradient(135deg, #051e38 0%, #002C54 100%);
    border: 1px solid #1e40af;
    border-radius: 4px;
    padding: 12px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    color: #ffffff;
}
.preview-text-col {
    display: flex;
    flex-direction: column;
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
    font-size: 20px;
    font-weight: 700;
    color: #38bdf8;
    background: rgba(0,0,0,.35);
    border: 1px dashed rgba(56,189,248,.6);
    padding: 4px 14px;
    border-radius: 4px;
    display: inline-block;
    letter-spacing: .05em;
    margin-top: 4px;
}
.preview-note {
    font-size: 10.5px;
    color: #cbd5e1;
    margin: 4px 0 0 0;
}

/* Grid & Form Group Styles */
.form-grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
}
.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
}
.form-group-erp {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.form-label-erp {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .02em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 5px;
}
.form-label-erp .badge-req {
    color: #ef4444;
}
.form-control-erp {
    height: 32px;
    padding: 4px 10px;
    font-size: 12px;
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
    font-size: 10.5px;
    color: #64748b;
    margin-top: 2px;
    line-height: 1.35;
}

/* Radio Cards (Selectable Policy Cards) */
.radio-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.radio-card {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 12px 14px;
    background: #ffffff;
    cursor: pointer;
    transition: all .15s ease;
    display: flex;
    gap: 10px;
    position: relative;
    user-select: none;
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
    margin-top: 3px;
    cursor: pointer;
    accent-color: #002C54;
}
.radio-card-content {
    display: flex;
    flex-direction: column;
}
.radio-card-title {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.radio-card-desc {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.35;
    margin: 0;
}

/* Toggle Switch Styling */
.switch-group-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    background: #f8fafc;
    margin-bottom: 10px;
}
.switch-label-wrap {
    display: flex;
    flex-direction: column;
}
.switch-label-title {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}
.switch-label-sub {
    font-size: 10.5px;
    color: #64748b;
    margin: 1px 0 0 0;
}
.switch-ctrl {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
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
    border-radius: 24px;
}
.switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
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
    transform: translateX(20px);
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
                                Configure automated receipt number generation, late fine rules, coaching monthly due dates, and print letterhead formats.
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
                        <a href="#tab-fine" class="settings-tab-btn" data-tab="tab-fine">
                            <i class="fa fa-balance-scale"></i> 2. Late Fine Rules
                        </a>
                        <a href="#tab-duedate" class="settings-tab-btn" data-tab="tab-duedate">
                            <i class="fa fa-calendar-check-o"></i> 3. Coaching Due Date
                        </a>
                        <a href="#tab-payment" class="settings-tab-btn" data-tab="tab-payment">
                            <i class="fa fa-credit-card"></i> 4. Payment Modes & Cashier
                        </a>
                        <a href="#tab-print" class="settings-tab-btn" data-tab="tab-print">
                            <i class="fa fa-print"></i> 5. Receipt Print & Letterhead
                        </a>
                        <a href="#tab-alerts" class="settings-tab-btn" data-tab="tab-alerts">
                            <i class="fa fa-bell-o"></i> 6. Automated Alerts
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
                                {{-- Live Receipt Preview Showcase Box --}}
                                <div class="receipt-preview-banner">
                                    <div class="preview-text-col">
                                        <span class="preview-kicker"><i class="fa fa-eye"></i> Live Interactive Receipt Preview</span>
                                        <div>
                                            <span class="preview-number-badge" id="liveReceiptPreview">{{ $samplePreview }}</span>
                                        </div>
                                        <p class="preview-note">This simulated receipt number updates in real-time as you modify the prefix, digits, or session options below.</p>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-light" id="btnTestPreview" style="font-size: 11px;">
                                            <i class="fa fa-refresh"></i> Refresh Preview
                                        </button>
                                    </div>
                                </div>

                                {{-- Number Generation Mode --}}
                                <label class="form-label-erp mb-2">Receipt Numbering Mode <span class="badge-req">*</span></label>
                                <div class="radio-cards-grid">
                                    <label class="radio-card {{ $setting->receipt_number_type === 'both' ? 'selected' : '' }}">
                                        <input type="radio" name="receipt_number_type" value="both" {{ $setting->receipt_number_type === 'both' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-magic"></i> Auto + Manual Override (Recommended)</span>
                                            <p class="radio-card-desc">System automatically suggests next sequential number, but allows cashier to override if needed.</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->receipt_number_type === 'auto' ? 'selected' : '' }}">
                                        <input type="radio" name="receipt_number_type" value="auto" {{ $setting->receipt_number_type === 'auto' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-lock"></i> Strict Automatic Only</span>
                                            <p class="radio-card-desc">Receipt numbers are 100% system-generated. Cashiers cannot modify the number, eliminating duplicates.</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->receipt_number_type === 'manual' ? 'selected' : '' }}">
                                        <input type="radio" name="receipt_number_type" value="manual" {{ $setting->receipt_number_type === 'manual' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-pencil"></i> Manual Entry Only</span>
                                            <p class="radio-card-desc">Cashier enters offline manual receipt book number for every collection transaction.</p>
                                        </div>
                                    </label>
                                </div>

                                {{-- Format Parameters Grid --}}
                                <div class="form-grid-3">
                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="receipt_prefix">Receipt Prefix</label>
                                        <input type="text" class="form-control-erp preview-trigger" id="receipt_prefix" name="receipt_prefix" 
                                               value="{{ old('receipt_prefix', $setting->receipt_prefix) }}" placeholder="e.g. REC-, COACH-, FEE/">
                                        <span class="form-hint">Text before the number (e.g. <code>REC-</code> or <code>ARISE/</code>)</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="receipt_suffix">Receipt Suffix (Optional)</label>
                                        <input type="text" class="form-control-erp preview-trigger" id="receipt_suffix" name="receipt_suffix" 
                                               value="{{ old('receipt_suffix', $setting->receipt_suffix) }}" placeholder="e.g. /HQ, -A">
                                        <span class="form-hint">Appended at the end (e.g. <code>/HQ</code>)</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="receipt_digit_padding">Digits Padding Length <span class="badge-req">*</span></label>
                                        <select class="form-control-erp preview-trigger" id="receipt_digit_padding" name="receipt_digit_padding">
                                            <option value="3" {{ (int) $setting->receipt_digit_padding === 3 ? 'selected' : '' }}>3 Digits (e.g. 001)</option>
                                            <option value="4" {{ (int) $setting->receipt_digit_padding === 4 ? 'selected' : '' }}>4 Digits (e.g. 0001) - Standard</option>
                                            <option value="5" {{ (int) $setting->receipt_digit_padding === 5 ? 'selected' : '' }}>5 Digits (e.g. 00001)</option>
                                            <option value="6" {{ (int) $setting->receipt_digit_padding === 6 ? 'selected' : '' }}>6 Digits (e.g. 000001)</option>
                                        </select>
                                        <span class="form-hint">Minimum number of numeric digits with leading zeros</span>
                                    </div>
                                </div>

                                {{-- Date/Session Inclusion Switches --}}
                                <div class="form-grid-2">
                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Include Academic Session in Receipt Number</span>
                                            <span class="switch-label-sub">Adds session year (e.g. <code>{{ $sessionName }}</code>) inside the receipt string</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="receipt_include_session" value="1" class="preview-trigger" 
                                                   {{ $setting->receipt_include_session ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Include Month in Receipt Number</span>
                                            <span class="switch-label-sub">Adds 2-digit collection month (e.g. <code>{{ date('m') }}</code>)</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="receipt_include_month" value="1" class="preview-trigger" 
                                                   {{ $setting->receipt_include_month ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Sequence Counter & Reset Policy --}}
                                <div class="form-grid-2">
                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="receipt_reset_cycle">Counter Reset Cycle</label>
                                        <select class="form-control-erp" id="receipt_reset_cycle" name="receipt_reset_cycle">
                                            <option value="session" {{ $setting->receipt_reset_cycle === 'session' ? 'selected' : '' }}>Reset on New Academic Session (Recommended)</option>
                                            <option value="financial_year" {{ $setting->receipt_reset_cycle === 'financial_year' ? 'selected' : '' }}>Reset on Financial Year (1st April)</option>
                                            <option value="never" {{ $setting->receipt_reset_cycle === 'never' ? 'selected' : '' }}>Never Reset (Continuous Increment)</option>
                                        </select>
                                        <span class="form-hint">Controls when the sequence counter resets back to 1</span>
                                    </div>

                                    <div class="form-group-erp" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 3px; padding: 8px 12px;">
                                        <label class="form-label-erp" for="new_counter_value">
                                            <i class="fa fa-database"></i> Current Counter: <strong>#{{ $currentCounter }}</strong> (Next: <strong>#{{ $nextCounter }}</strong>)
                                        </label>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <input type="number" class="form-control-erp preview-trigger" id="new_counter_value" name="new_counter_value" 
                                                   value="{{ $nextCounter }}" min="1" style="max-width: 140px;">
                                            <label class="d-flex align-items-center gap-1 mb-0" style="font-size: 11px; cursor: pointer; user-select: none;">
                                                <input type="checkbox" name="sync_bill_counter" value="1">
                                                <span>Apply as Next Number</span>
                                            </label>
                                        </div>
                                        <span class="form-hint">You can adjust the starting sequence if continuing from an existing paper book.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 2: LATE FINE CONFIGURATION
                             ============================================================ --}}
                        <div class="tab-pane-content" id="tab-fine" style="display: none;">
                            <div class="settings-section-header">
                                <div>
                                    <h2 class="section-title"><i class="fa fa-balance-scale"></i> Late Fine Rules & Penalty Policy</h2>
                                    <p class="section-desc">Define automated late fine calculations, daily rates, grace periods, and cashier waiver policies.</p>
                                </div>
                            </div>

                            <div class="settings-card-body">
                                {{-- Late Fine Mode Selection --}}
                                <label class="form-label-erp mb-2">Late Fine Calculation Mode <span class="badge-req">*</span></label>
                                <div class="radio-cards-grid">
                                    <label class="radio-card {{ $setting->fine_mode === 'fixed' ? 'selected' : '' }}">
                                        <input type="radio" name="fine_mode" value="fixed" {{ $setting->fine_mode === 'fixed' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-tag"></i> Fixed Flat Fine (₹)</span>
                                            <p class="radio-card-desc">One-time flat late fine applied once the due date passes.</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->fine_mode === 'daily' ? 'selected' : '' }}">
                                        <input type="radio" name="fine_mode" value="daily" {{ $setting->fine_mode === 'daily' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-calendar-times-o"></i> Daily Per-Day Rate (₹/day)</span>
                                            <p class="radio-card-desc">Calculates late fine per day delayed after due date (e.g. ₹10 per day).</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->fine_mode === 'percentage' ? 'selected' : '' }}">
                                        <input type="radio" name="fine_mode" value="percentage" {{ $setting->fine_mode === 'percentage' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-percent"></i> Percentage of Overdue (%)</span>
                                            <p class="radio-card-desc">Calculates fine as a percentage of unpaid fees amount.</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->fine_mode === 'manual' ? 'selected' : '' }}">
                                        <input type="radio" name="fine_mode" value="manual" {{ $setting->fine_mode === 'manual' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-hand-pointer-o"></i> Cashier Manual Entry</span>
                                            <p class="radio-card-desc">Fine is entered manually by the cashier on the payment screen as per case discretion.</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->fine_mode === 'disabled' ? 'selected' : '' }}">
                                        <input type="radio" name="fine_mode" value="disabled" {{ $setting->fine_mode === 'disabled' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-ban"></i> Disabled (No Fine)</span>
                                            <p class="radio-card-desc">No late fine will be calculated or enforced for students.</p>
                                        </div>
                                    </label>
                                </div>

                                {{-- Fine Details Grid --}}
                                <div class="form-grid-3">
                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="fine_amount">
                                            <span id="fineAmountLabel">Fine Amount (₹)</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" class="form-control-erp" id="fine_amount" name="fine_amount" 
                                               value="{{ old('fine_amount', $setting->fine_amount) }}" placeholder="0.00">
                                        <span class="form-hint" id="fineAmountHint">Flat late fee amount or daily rate in INR</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="fine_grace_days">Grace Period (Days)</label>
                                        <input type="number" min="0" max="90" class="form-control-erp" id="fine_grace_days" name="fine_grace_days" 
                                               value="{{ old('fine_grace_days', $setting->fine_grace_days) }}" placeholder="e.g. 5">
                                        <span class="form-hint">Number of days after due date before fine starts counting</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="fine_max_cap">Maximum Fine Cap / Ceiling (₹)</label>
                                        <input type="number" step="0.01" min="0" class="form-control-erp" id="fine_max_cap" name="fine_max_cap" 
                                               value="{{ old('fine_max_cap', $setting->fine_max_cap) }}" placeholder="Optional limit (e.g. 500)">
                                        <span class="form-hint">Upper limit to avoid runaway fines for coaching students</span>
                                    </div>
                                </div>

                                {{-- Fine Waiver & Remark Policy --}}
                                <div class="form-grid-2">
                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Allow Cashier Fine Waiver</span>
                                            <span class="switch-label-sub">Cashier can waive or reduce late fine during fee collection</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="allow_fine_waiver" value="1" {{ $setting->allow_fine_waiver ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>

                                    <div class="switch-group-row">
                                        <div class="switch-label-wrap">
                                            <span class="switch-label-title">Require Mandatory Reason/Remark for Fine Waiver</span>
                                            <span class="switch-label-sub">Cashier must input a reason before fine can be reduced</span>
                                        </div>
                                        <label class="switch-ctrl">
                                            <input type="checkbox" name="fine_waiver_requires_remark" value="1" {{ $setting->fine_waiver_requires_remark ? 'checked' : '' }}>
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 3: DUE DATE POLICY (COACHING & INSTITUTE SPECIFIC)
                             ============================================================ --}}
                        <div class="tab-pane-content" id="tab-duedate" style="display: none;">
                            <div class="settings-section-header">
                                <div>
                                    <h2 class="section-title"><i class="fa fa-calendar-check-o"></i> Coaching Due Date Policy</h2>
                                    <p class="section-desc">Tailor fee due dates for coaching batches, monthly fees, or enrollment anniversaries.</p>
                                </div>
                            </div>

                            <div class="settings-card-body">
                                <label class="form-label-erp mb-2">Due Date Schedule Policy <span class="badge-req">*</span></label>
                                <div class="radio-cards-grid">
                                    <label class="radio-card {{ $setting->due_date_policy === 'fixed_day_monthly' ? 'selected' : '' }}">
                                        <input type="radio" name="due_date_policy" value="fixed_day_monthly" {{ $setting->due_date_policy === 'fixed_day_monthly' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-calendar"></i> Fixed Monthly Day (Standard Coaching)</span>
                                            <p class="radio-card-desc">Fee is due on a fixed calendar date of every month (e.g. 5th or 10th of every month).</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->due_date_policy === 'admission_days' ? 'selected' : '' }}">
                                        <input type="radio" name="due_date_policy" value="admission_days" {{ $setting->due_date_policy === 'admission_days' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-clock-o"></i> Enrollment Anniversary</span>
                                            <p class="radio-card-desc">Due date is calculated as X days from the student's admission or batch start date.</p>
                                        </div>
                                    </label>

                                    <label class="radio-card {{ $setting->due_date_policy === 'custom_schedule' ? 'selected' : '' }}">
                                        <input type="radio" name="due_date_policy" value="custom_schedule" {{ $setting->due_date_policy === 'custom_schedule' ? 'checked' : '' }}>
                                        <div class="radio-card-content">
                                            <span class="radio-card-title"><i class="fa fa-th-list"></i> Assigned Structure Schedule</span>
                                            <p class="radio-card-desc">Follows the exact custom installment dates assigned in the student's fee structure.</p>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-grid-3">
                                    <div class="form-group-erp" id="group_due_day_of_month">
                                        <label class="form-label-erp" for="due_day_of_month">Due Day of Every Month</label>
                                        <select class="form-control-erp" id="due_day_of_month" name="due_day_of_month">
                                            @for($d = 1; $d <= 28; $d++)
                                                <option value="{{ $d }}" {{ (int) $setting->due_day_of_month === $d ? 'selected' : '' }}>
                                                    {{ $d }}{{ date('S', mktime(0,0,0,1,$d)) }} of every month
                                                </option>
                                            @endfor
                                        </select>
                                        <span class="form-hint">E.g., 10th of every month for recurring coaching fees</span>
                                    </div>

                                    <div class="form-group-erp" id="group_due_days_after_admission">
                                        <label class="form-label-erp" for="due_days_after_admission">Days After Admission</label>
                                        <input type="number" min="1" max="365" class="form-control-erp" id="due_days_after_admission" name="due_days_after_admission" 
                                               value="{{ old('due_days_after_admission', $setting->due_days_after_admission) }}" placeholder="e.g. 15">
                                        <span class="form-hint">Number of days allowed for payment after student enrollment</span>
                                    </div>

                                    <div class="form-group-erp">
                                        <label class="form-label-erp" for="advance_payment_allowed_days">Advance Payment Window (Days)</label>
                                        <input type="number" min="0" max="365" class="form-control-erp" id="advance_payment_allowed_days" name="advance_payment_allowed_days" 
                                               value="{{ old('advance_payment_allowed_days', $setting->advance_payment_allowed_days) }}" placeholder="e.g. 30">
                                        <span class="form-hint">Allow students to deposit next month fees up to X days in advance</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 4: PAYMENT MODES & CASHIER CONTROLS
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

        // Fine mode dynamic label & hint
        if (name === 'fine_mode') {
            updateFineModeUI($(this).val());
        }

        // Due date policy dynamic fields
        if (name === 'due_date_policy') {
            updateDueDatePolicyUI($(this).val());
        }
    });

    function updateFineModeUI(mode) {
        if (mode === 'daily') {
            $('#fineAmountLabel').text('Daily Fine Rate (₹/day)');
            $('#fineAmountHint').text('Penalty amount charged for each day delayed after due date');
            $('#fine_amount').prop('disabled', false);
        } else if (mode === 'percentage') {
            $('#fineAmountLabel').text('Fine Percentage (%)');
            $('#fineAmountHint').text('Percentage rate calculated on overdue installment balance');
            $('#fine_amount').prop('disabled', false);
        } else if (mode === 'fixed') {
            $('#fineAmountLabel').text('Fixed Flat Fine (₹)');
            $('#fineAmountHint').text('Flat late penalty applied once due date passes');
            $('#fine_amount').prop('disabled', false);
        } else if (mode === 'manual') {
            $('#fineAmountLabel').text('Default Suggested Fine (₹)');
            $('#fineAmountHint').text('Cashier can override during collection');
            $('#fine_amount').prop('disabled', false);
        } else if (mode === 'disabled') {
            $('#fineAmountLabel').text('Fine Disabled');
            $('#fineAmountHint').text('No late fine charged');
            $('#fine_amount').prop('disabled', true);
        }
    }

    function updateDueDatePolicyUI(policy) {
        if (policy === 'fixed_day_monthly') {
            $('#group_due_day_of_month').show();
            $('#group_due_days_after_admission').hide();
        } else if (policy === 'admission_days') {
            $('#group_due_day_of_month').hide();
            $('#group_due_days_after_admission').show();
        } else {
            $('#group_due_day_of_month').hide();
            $('#group_due_days_after_admission').hide();
        }
    }

    // Initialize fine mode & due date UI on load
    updateFineModeUI($('input[name="fine_mode"]:checked').val());
    updateDueDatePolicyUI($('input[name="due_date_policy"]:checked').val());

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
        const suffix = $('#receipt_suffix').val();
        const padding = parseInt($('#receipt_digit_padding').val()) || 4;
        const includeSession = $('input[name="receipt_include_session"]').is(':checked') ? 1 : 0;
        const includeMonth = $('input[name="receipt_include_month"]').is(':checked') ? 1 : 0;
        const counter = parseInt($('#new_counter_value').val()) || {{ $nextCounter }};
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
                $('#liveReceiptPreview').text(prefix + sessionPart + monthPart + numStr + suffix);
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
