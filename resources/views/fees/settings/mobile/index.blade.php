@php
    $currentSessionName = Session::get('session_name') ?? $sessionName ?? '2026-27';
    $samplePreview = $samplePreview ?? 'REC-2026-0001';
    $allowedModesArray = $allowedModesArray ?? ['1', '2', '3', '4'];
    $currentColor = strtolower($setting->receipt_theme_color ?? '#8b2635');
    $currentLayout = $setting->receipt_layout ?? 'a4_dual';
@endphp

@extends('layout.mobile_app')

@section('title', 'Fees Settings')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE FEES SETTINGS & POLICY CONFIGURATION
   100% aligned with Arise ERP Mobile Design Guidelines (editSetting & feesGroup)
   - Zero double-padding (main already has 6px 8px 16px)
   - Sharp 4px radii, Navy Blue palette (#002C54)
   - Glassmorphic Hero Card with Live Session KPIs
   - Interactive Sticky Step Progress Tracker
   - Touch-optimized Section Cards with Prepended Icons
   - Real-time Receipt Number live generator preview
   - Fixed Bottom Action Dock above mobile dock
   ========================================================================== */

.settings-mob-container {
    padding: 0 0 65px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-hero-badges {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    white-space: nowrap;
}
.mob-status-pill {
    font-size: 9.5px;
    background: rgba(74, 222, 128, 0.18);
    border: 1px solid rgba(74, 222, 128, 0.35);
    color: #4ade80;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 800;
    white-space: nowrap;
}
.mob-hero-desc {
    font-size: 10.5px;
    color: #cbd5e1;
    margin-bottom: 8px;
    line-height: 1.35;
}

/* Hero Quick Glance Grid */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-bottom: 0;
}
.mob-kpi-item {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 6px;
    text-align: center;
}
.mob-kpi-label {
    font-size: 8.5px;
    color: #94a3b8;
    font-weight: 700;
    text-transform: uppercase;
}
.mob-kpi-val {
    font-size: 12px;
    font-weight: 800;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}

/* 2. Interactive Step Tracker */
.mob-tracker-container {
    position: relative;
    width: 100%;
    margin-bottom: 8px;
}
.mob-step-tracker {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-step-nodes {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 5px;
    gap: 4px;
}
.mob-step-node {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    user-select: none;
    padding: 2px 4px;
    border-radius: 3px;
    transition: all .15s ease;
    white-space: nowrap;
}
.mob-step-node:active {
    transform: scale(0.95);
}
.mob-step-node.active {
    color: #002C54;
    font-weight: 800;
}
.mob-step-node.completed {
    color: #16a34a;
}
.mob-step-dot {
    width: 17px;
    height: 17px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 8.5px;
    color: #64748b;
    font-weight: 800;
    transition: all .15s ease;
}
.mob-step-node.active .mob-step-dot {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-step-node.completed .mob-step-dot {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}
.mob-progress-bar-bg {
    width: 100%;
    height: 3px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.mob-progress-bar-fill {
    height: 100%;
    width: 25%;
    background: linear-gradient(90deg, #0284c7 0%, #10b981 100%);
    border-radius: 2px;
    transition: width .25s ease;
}

/* 3. Section Cards */
.mob-section-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-section-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-step-badge {
    background: #002C54;
    color: #ffffff;
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .03em;
}

/* Live Receipt Showcase */
.mob-receipt-showcase {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #86efac;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    text-align: center;
}
.mob-showcase-label {
    font-size: 9px;
    font-weight: 700;
    color: #166534;
    text-transform: uppercase;
    letter-spacing: .04em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.mob-showcase-number {
    font-size: 16px;
    font-weight: 800;
    color: #14532d;
    background: #ffffff;
    border: 1.5px dashed #22c55e;
    padding: 3px 12px;
    border-radius: 4px;
    display: inline-block;
    letter-spacing: .05em;
    margin: 3px 0 2px 0;
    font-family: monospace;
}
.mob-showcase-sub {
    font-size: 9px;
    color: #15803d;
    margin: 0;
}

/* Form Groups & Inputs */
.mob-form-group {
    margin-bottom: 7px;
}
.mob-form-group:last-child {
    margin-bottom: 0;
}
.mob-form-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 3px;
    margin-bottom: 2px;
}
.mob-req-star {
    color: #ef4444;
    font-weight: 800;
}
.mob-form-hint {
    font-size: 9px;
    color: #64748b;
    margin-top: 1px;
    display: block;
}

/* Prepended Input Container */
.mob-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.mob-input-icon {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
    z-index: 2;
}
.mob-form-input, .mob-form-select, .mob-form-textarea {
    width: 100%;
    height: 32px;
    padding: 0 8px 0 26px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: border-color .15s ease, background-color .15s ease;
    font-family: inherit;
}
.mob-form-textarea {
    height: 60px;
    padding: 5px 8px;
    resize: vertical;
}
.mob-form-input:focus, .mob-form-select:focus, .mob-form-textarea:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* Row 2 */
.mob-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 6px;
}

/* Toggle Switch Card */
.mob-switch-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 5px;
}
.mob-switch-info {
    display: flex;
    flex-direction: column;
    max-width: 80%;
}
.mob-switch-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.25;
}
.mob-switch-desc {
    font-size: 8.5px;
    color: #64748b;
    font-weight: 500;
}
.mob-ios-switch {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 20px;
    margin-bottom: 0;
    flex-shrink: 0;
}
.mob-ios-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.mob-ios-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 20px;
}
.mob-ios-slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.mob-ios-switch input:checked + .mob-ios-slider {
    background-color: #0284c7;
}
.mob-ios-switch input:checked + .mob-ios-slider:before {
    transform: translateX(16px);
}

/* Payment Mode Chips Grid */
.mob-payment-chips {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5px;
    margin-top: 3px;
}
.mob-chip-label {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    cursor: pointer;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0;
    transition: all .15s ease;
}
.mob-chip-label.active {
    background: #eff6ff;
    border-color: #0284c7;
    color: #002C54;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.15);
}
.mob-chip-label input[type="checkbox"] {
    width: 14px;
    height: 14px;
    accent-color: #002C54;
}

/* Paper Layout Cards */
.mob-layout-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-layout-card {
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 7px 8px;
    background: #f8fafc;
    cursor: pointer;
    text-align: center;
    transition: all .15s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}
.mob-layout-card.selected {
    border-color: #0284c7;
    background: #eff6ff;
    box-shadow: 0 1px 4px rgba(2, 132, 199, 0.2);
}
.mob-layout-icon {
    font-size: 15px;
    color: #002C54;
}
.mob-layout-name {
    font-size: 10.5px;
    font-weight: 800;
    color: #002C54;
}
.mob-layout-sub {
    font-size: 8.5px;
    color: #64748b;
    line-height: 1.15;
}

/* Color Presets */
.mob-color-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 3px;
    margin-bottom: 6px;
    -webkit-overflow-scrolling: touch;
}
.mob-swatch-btn {
    padding: 2px 7px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    font-size: 9.5px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.mob-swatch-btn.active {
    border-color: #0284c7;
    background: #eff6ff;
    color: #002C54;
    font-weight: 800;
}

/* Fixed Bottom Action Dock above mobile nav dock */
.mob-save-dock {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    z-index: 990;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 7px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
}
.btn-mob-save {
    flex: 1;
    height: 35px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
    cursor: pointer;
    letter-spacing: .02em;
}
.btn-mob-save:active {
    transform: scale(0.98);
}
.mob-form-clearance {
    height: 50px;
}
</style>
@endsection

@section('content')
<div class="settings-mob-container">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-cogs text-warning"></i> Fees Policy &amp; Settings
            </div>
            <div class="mob-hero-badges">
                <span class="mob-session-pill"><i class="fa fa-calendar mr-1"></i> {{ $currentSessionName }}</span>
                <span class="mob-status-pill">Active</span>
            </div>
        </div>
        <div class="mob-hero-desc">
            Configure receipt number generator, collection policies, print letterhead layout &amp; automated fee due reminders.
        </div>
        <div class="mob-kpi-grid">
            <div class="mob-kpi-item">
                <span class="mob-kpi-label">Used Receipts</span>
                <span class="mob-kpi-val text-warning">#{{ $currentCounter ?? 0 }}</span>
            </div>
            <div class="mob-kpi-item">
                <span class="mob-kpi-label">Next Receipt</span>
                <span class="mob-kpi-val" style="color: #4ade80;">#{{ $nextCounter ?? 1 }}</span>
            </div>
            <div class="mob-kpi-item">
                <span class="mob-kpi-label">Reset Cycle</span>
                <span class="mob-kpi-val" style="color: #38bdf8;">Session (FY)</span>
            </div>
        </div>
    </div>

    {{-- 2. Interactive Step Tracker --}}
    <div class="mob-tracker-container">
        <div class="mob-step-tracker" id="mobStepTracker">
            <div class="mob-step-nodes">
                <div class="mob-step-node active" data-step="1" onclick="switchStep(1)">
                    <span class="mob-step-dot">1</span>
                    <span>Format</span>
                </div>
                <div class="mob-step-node" data-step="2" onclick="switchStep(2)">
                    <span class="mob-step-dot">2</span>
                    <span>Modes</span>
                </div>
                <div class="mob-step-node" data-step="3" onclick="switchStep(3)">
                    <span class="mob-step-dot">3</span>
                    <span>Print</span>
                </div>
                <div class="mob-step-node" data-step="4" onclick="switchStep(4)">
                    <span class="mob-step-dot">4</span>
                    <span>Alerts</span>
                </div>
            </div>
            <div class="mob-progress-bar-bg">
                <div class="mob-progress-bar-fill" id="stepProgressFill" style="width: 25%;"></div>
            </div>
        </div>
    </div>

    {{-- Settings Form --}}
    <form action="{{ url('fees/settings/update') }}" method="POST" id="feesSettingsForm">
        @csrf
        <input type="hidden" name="receipt_number_type" value="both">
        <input type="hidden" name="receipt_reset_cycle" value="session">

        {{-- ========================================================
             STEP 1: RECEIPT NUMBER GENERATOR FORMAT
             ======================================================== --}}
        <div class="mob-step-pane" id="step-pane-1">
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <i class="fa fa-file-text-o text-primary"></i> Receipt Number Generator
                    </div>
                    <span class="mob-step-badge">Step 1 of 4</span>
                </div>

                {{-- Live Showcase Box --}}
                <div class="mob-receipt-showcase">
                    <div class="mob-showcase-label">
                        <i class="fa fa-eye"></i> Live Receipt Number Preview
                    </div>
                    <div class="mob-showcase-number" id="liveReceiptPreview">{{ $samplePreview }}</div>
                    <p class="mob-showcase-sub">
                        Updates in real-time as you modify prefix, digit length, or date flags below.
                    </p>
                </div>

                {{-- Prefix & Digits Padding --}}
                <div class="mob-row-2">
                    <div class="mob-form-group">
                        <label class="mob-form-label" for="receipt_prefix">Receipt Prefix</label>
                        <div class="mob-input-wrap">
                            <i class="fa fa-tag mob-input-icon"></i>
                            <input type="text" class="mob-form-input preview-trigger" id="receipt_prefix" name="receipt_prefix" 
                                   value="{{ old('receipt_prefix', $setting->receipt_prefix ?? 'REC-') }}" placeholder="REC-">
                        </div>
                        <span class="mob-form-hint">e.g. REC-, COACH-</span>
                    </div>

                    <div class="mob-form-group">
                        <label class="mob-form-label" for="receipt_digit_padding">
                            Digit Padding <span class="mob-req-star">*</span>
                        </label>
                        <div class="mob-input-wrap">
                            <i class="fa fa-list-ol mob-input-icon"></i>
                            <select class="mob-form-select preview-trigger" id="receipt_digit_padding" name="receipt_digit_padding">
                                <option value="3" {{ (int)($setting->receipt_digit_padding ?? 4) === 3 ? 'selected' : '' }}>3 Digits (001)</option>
                                <option value="4" {{ (int)($setting->receipt_digit_padding ?? 4) === 4 ? 'selected' : '' }}>4 Digits (0001)</option>
                                <option value="5" {{ (int)($setting->receipt_digit_padding ?? 4) === 5 ? 'selected' : '' }}>5 Digits (00001)</option>
                                <option value="6" {{ (int)($setting->receipt_digit_padding ?? 4) === 6 ? 'selected' : '' }}>6 Digits (000001)</option>
                            </select>
                        </div>
                        <span class="mob-form-hint">Serial length</span>
                    </div>
                </div>

                {{-- Date / Session Inclusion Switches --}}
                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Include Academic Session</span>
                        <span class="mob-switch-desc">Adds session string (<code>{{ $sessionName }}</code>)</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="receipt_include_session" value="1" class="preview-trigger" 
                               {{ ($setting->receipt_include_session ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Include Collection Month</span>
                        <span class="mob-switch-desc">Adds 2-digit collection month (<code>{{ date('m') }}</code>)</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="receipt_include_month" value="1" class="preview-trigger" 
                               {{ ($setting->receipt_include_month ?? 0) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 2: PAYMENT MODES & CASHIER LIMITS
             ======================================================== --}}
        <div class="mob-step-pane" id="step-pane-2" style="display: none;">
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <i class="fa fa-credit-card text-success"></i> Accepted Payment Modes
                    </div>
                    <span class="mob-step-badge">Step 2 of 4</span>
                </div>

                <div class="mob-payment-chips">
                    @foreach($paymentModes as $pm)
                        <label class="mob-chip-label {{ in_array((string)$pm->id, $allowedModesArray) ? 'active' : '' }}">
                            <input type="checkbox" name="allowed_payment_modes[]" value="{{ $pm->id }}" 
                                   {{ in_array((string)$pm->id, $allowedModesArray) ? 'checked' : '' }}
                                   onchange="$(this).closest('.mob-chip-label').toggleClass('active', this.checked)">
                            <span>{{ $pm->name }}</span>
                        </label>
                    @endforeach
                </div>
                <span class="mob-form-hint mt-2">Select all payment methods cashiers are allowed to accept.</span>
            </div>

            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <i class="fa fa-sliders text-primary"></i> Partial &amp; Discount Controls
                    </div>
                </div>

                {{-- Partial Payments --}}
                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Allow Partial Payments</span>
                        <span class="mob-switch-desc">Student can deposit installment in parts</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="allow_partial_payment" value="1" {{ ($setting->allow_partial_payment ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Allow Manual Discount</span>
                        <span class="mob-switch-desc">Cashier can grant fee concession</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="allow_manual_discount" value="1" {{ ($setting->allow_manual_discount ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-row-2 mt-2">
                    <div class="mob-form-group">
                        <label class="mob-form-label" for="min_partial_amount">Min Partial (₹)</label>
                        <div class="mob-input-wrap">
                            <i class="fa fa-inr mob-input-icon"></i>
                            <input type="number" step="0.01" min="0" class="mob-form-input" id="min_partial_amount" name="min_partial_amount" 
                                   value="{{ old('min_partial_amount', $setting->min_partial_amount ?? 0) }}" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mob-form-group">
                        <label class="mob-form-label" for="max_discount_percentage">Max Discount (%)</label>
                        <div class="mob-input-wrap">
                            <i class="fa fa-percent mob-input-icon"></i>
                            <input type="number" step="0.01" min="0" max="100" class="mob-form-input" id="max_discount_percentage" name="max_discount_percentage" 
                                   value="{{ old('max_discount_percentage', $setting->max_discount_percentage ?? 20) }}" placeholder="20">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 3: RECEIPT PRINT & LETTERHEAD LAYOUT
             ======================================================== --}}
        <div class="mob-step-pane" id="step-pane-3" style="display: none;">
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <i class="fa fa-print text-info"></i> Receipt Paper Layout
                    </div>
                    <span class="mob-step-badge">Step 3 of 4</span>
                </div>

                {{-- Layout Selection Cards --}}
                <div class="mob-layout-grid">
                    <label class="mob-layout-card {{ $currentLayout !== 'a4_single' ? 'selected' : '' }}">
                        <input type="radio" name="receipt_layout" value="a4_dual" style="display:none;" 
                               {{ $currentLayout !== 'a4_single' ? 'checked' : '' }}
                               onchange="$('.mob-layout-card').removeClass('selected'); $(this).closest('.mob-layout-card').addClass('selected');">
                        <i class="fa fa-columns mob-layout-icon"></i>
                        <span class="mob-layout-name">Dual Copy (A4 Half)</span>
                        <span class="mob-layout-sub">Student + Office Copy. Saves 50% paper!</span>
                    </label>

                    <label class="mob-layout-card {{ $currentLayout === 'a4_single' ? 'selected' : '' }}">
                        <input type="radio" name="receipt_layout" value="a4_single" style="display:none;" 
                               {{ $currentLayout === 'a4_single' ? 'checked' : '' }}
                               onchange="$('.mob-layout-card').removeClass('selected'); $(this).closest('.mob-layout-card').addClass('selected');">
                        <i class="fa fa-file-text-o mob-layout-icon"></i>
                        <span class="mob-layout-name">Full Page A4</span>
                        <span class="mob-layout-sub">Single detailed full page invoice.</span>
                    </label>
                </div>

                {{-- Theme Color Picker --}}
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <i class="fa fa-paint-brush text-primary"></i> Receipt Brand Color
                    </label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <input type="color" id="themeColorPicker" value="{{ old('receipt_theme_color', $setting->receipt_theme_color ?? '#8b2635') }}" 
                               style="width: 36px; height: 30px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px; background: #fff; cursor: pointer;">
                        <input type="text" class="mob-form-input" id="themeColorText" name="receipt_theme_color" 
                               value="{{ old('receipt_theme_color', $setting->receipt_theme_color ?? '#8b2635') }}" 
                               maxlength="7" style="font-family: monospace; font-weight: 700; text-transform: uppercase;">
                    </div>

                    {{-- Presets Strip --}}
                    <div class="mob-color-strip">
                        @php
                            $presets = [
                                ['name' => 'Burgundy', 'hex' => '#8b2635'],
                                ['name' => 'Navy', 'hex' => '#002C54'],
                                ['name' => 'Royal', 'hex' => '#1e40af'],
                                ['name' => 'Emerald', 'hex' => '#065f46'],
                                ['name' => 'Crimson', 'hex' => '#991b1b'],
                                ['name' => 'Maroon', 'hex' => '#7f1d1d'],
                                ['name' => 'Charcoal', 'hex' => '#334155'],
                            ];
                        @endphp
                        @foreach($presets as $p)
                            <button type="button" class="mob-swatch-btn {{ strtolower($p['hex']) === $currentColor ? 'active' : '' }}" 
                                    data-color="{{ $p['hex'] }}" onclick="selectColorPreset('{{ $p['hex'] }}', this)">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $p['hex'] }}; display: inline-block;"></span>
                                {{ $p['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Print Elements Switches --}}
                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Show School Logo</span>
                        <span class="mob-switch-desc">Print logo at top header</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="show_school_logo" value="1" {{ ($setting->show_school_logo ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Show "PAID" Watermark</span>
                        <span class="mob-switch-desc">Subtle authentic watermark stamp</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="show_watermark" value="1" {{ ($setting->show_watermark ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Show Authorized Signature Box</span>
                        <span class="mob-switch-desc">Cashier &amp; Parent signature lines</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="show_signature_box" value="1" {{ ($setting->show_signature_box ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Show Payment Mode &amp; Txn ID</span>
                        <span class="mob-switch-desc">Print cheque # / UPI reference</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="show_payment_mode_details" value="1" {{ ($setting->show_payment_mode_details ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-form-group mt-2">
                    <label class="mob-form-label" for="receipt_header_title">Receipt Title</label>
                    <div class="mob-input-wrap">
                        <i class="fa fa-header mob-input-icon"></i>
                        <input type="text" class="mob-form-input" id="receipt_header_title" name="receipt_header_title" 
                               value="{{ old('receipt_header_title', $setting->receipt_header_title ?? 'FEE RECEIPT') }}" placeholder="FEE RECEIPT">
                    </div>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_terms_conditions">Terms &amp; Conditions / Footer Note</label>
                    <textarea class="mob-form-textarea" id="receipt_terms_conditions" name="receipt_terms_conditions" 
                              placeholder="Printed at receipt bottom...">{{ old('receipt_terms_conditions', $setting->receipt_terms_conditions ?? '1. Fees once deposited is non-refundable. 2. Preserve this receipt for fee audit & examination.') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 4: AUTOMATED DUE ALERTS & REMINDERS
             ======================================================== --}}
        <div class="mob-step-pane" id="step-pane-4" style="display: none;">
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <i class="fa fa-bell-o text-warning"></i> Automated Due Reminders
                    </div>
                    <span class="mob-step-badge">Step 4 of 4</span>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title"><i class="fa fa-whatsapp text-success mr-1"></i> Instant WhatsApp Receipt</span>
                        <span class="mob-switch-desc">Auto WhatsApp receipt to parent on collection</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="send_receipt_whatsapp" value="1" {{ ($setting->send_receipt_whatsapp ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Enable Fee Due Reminders</span>
                        <span class="mob-switch-desc">Queue reminders before due date</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="due_reminder_enabled" value="1" {{ ($setting->due_reminder_enabled ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>

                <div class="mob-form-group mt-2">
                    <label class="mob-form-label" for="due_reminder_days_before">Days Before Due Date</label>
                    <div class="mob-input-wrap">
                        <i class="fa fa-clock-o mob-input-icon"></i>
                        <input type="number" min="1" max="30" class="mob-form-input" id="due_reminder_days_before" name="due_reminder_days_before" 
                               value="{{ old('due_reminder_days_before', $setting->due_reminder_days_before ?? 3) }}" placeholder="3">
                    </div>
                    <span class="mob-form-hint">Dispatch reminder X days prior to installment due date.</span>
                </div>

                <div class="mob-switch-card">
                    <div class="mob-switch-info">
                        <span class="mob-switch-title">Overdue Notice</span>
                        <span class="mob-switch-desc">Send overdue alert after due date passes</span>
                    </div>
                    <label class="mob-ios-switch">
                        <input type="checkbox" name="overdue_notice_enabled" value="1" {{ ($setting->overdue_notice_enabled ?? 1) ? 'checked' : '' }}>
                        <span class="mob-ios-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mob-form-clearance"></div>

        {{-- Fixed Bottom Action Dock above mobile nav dock --}}
        <div class="mob-save-dock">
            <button type="submit" class="btn-mob-save">
                <i class="fa fa-check-circle"></i> Save Fee Settings
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
var CURRENT_SESSION_NAME = "{{ $sessionName ?? '2026-27' }}";

function switchStep(stepNum) {
    $('.mob-step-node').removeClass('active');
    $('.mob-step-node[data-step="' + stepNum + '"]').addClass('active');

    // Mark previous steps as completed
    $('.mob-step-node').each(function() {
        var s = parseInt($(this).data('step'));
        $(this).toggleClass('completed', s < stepNum);
    });

    var progressPercent = (stepNum / 4) * 100;
    $('#stepProgressFill').css('width', progressPercent + '%');

    $('.mob-step-pane').hide();
    $('#step-pane-' + stepNum).show();

    // Smooth scroll to top of section
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectColorPreset(hex, btn) {
    $('#themeColorPicker').val(hex);
    $('#themeColorText').val(hex.toUpperCase());
    $('.mob-swatch-btn').removeClass('active');
    $(btn).addClass('active');
}

function updateLivePreview() {
    var prefix = $('#receipt_prefix').val() || '';
    var padding = parseInt($('#receipt_digit_padding').val()) || 4;
    var incSession = $('input[name="receipt_include_session"]').is(':checked');
    var incMonth = $('input[name="receipt_include_month"]').is(':checked');

    var number = '1'.padStart(padding, '0');
    var preview = prefix;

    if (incSession) {
        preview += CURRENT_SESSION_NAME + '-';
    }
    if (incMonth) {
        var d = new Date();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        preview += m + '-';
    }
    preview += number;

    $('#liveReceiptPreview').text(preview);
}

$(document).ready(function() {
    $('.preview-trigger').on('input change', updateLivePreview);

    $('#themeColorPicker').on('input', function() {
        $('#themeColorText').val($(this).val().toUpperCase());
        $('.mob-swatch-btn').removeClass('active');
    });

    $('#themeColorText').on('input', function() {
        var val = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            $('#themeColorPicker').val(val);
        }
    });

    // Handle AJAX Form Submit with Toastr
    $('#feesSettingsForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('.btn-mob-save');
        var originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                submitBtn.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Fee Settings saved successfully!');
                } else {
                    alert('Fee Settings saved successfully!');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var firstErr = Object.values(errors)[0][0];
                    if (typeof toastr !== 'undefined') {
                        toastr.error(firstErr);
                    } else {
                        alert(firstErr);
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(xhr.responseJSON.message);
                    } else {
                        alert(xhr.responseJSON.message);
                    }
                } else {
                    form.off('submit').submit();
                }
            }
        });
    });
});
</script>
@endsection
