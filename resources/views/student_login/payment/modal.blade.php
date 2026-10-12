@php
    $modalStudent = $dashboardStudent ?? $getUser ?? Helper::getUser();
    $modalStudentName = trim(($modalStudent->first_name ?? '') . ' ' . ($modalStudent->last_name ?? ''));
    if (empty($modalStudentName)) $modalStudentName = 'Student';
    $modalClassName = optional($modalStudent->ClassTypes)->name ?? ($modalStudent->class_name ?? '---');
    $modalAdmNo = $modalStudent->admissionNo ?? '---';
    $modalPendingFees = (float)($pendingFees ?? 0);
    $pFees = (int)$modalPendingFees;
@endphp

{{-- ==========================================================================
     ARISE ERP - NATIVE MOBILE SLIDE-UP MODAL SHEET: PHONEPE FEE PAYMENT
     Exact same design system & classes as Homework, Complaints & Mobile Shell
     ========================================================================== --}}
<div class="mob-pay-backdrop" id="phonePeBackdrop" onclick="closePhonePeModal()"></div>

<div class="mob-pay-sheet" id="phonePePaySheet" role="dialog" aria-modal="true" aria-labelledby="phonePeSheetTitle">
    
    {{-- 1. Sheet Header (Exact Theme Gradient & Close Button) --}}
    <div class="mob-sheet-header">
        <div class="mob-sheet-title" id="phonePeSheetTitle">
            <i class="fa fa-shield" style="color: #38bdf8;"></i>
            <span>Pay School Fees</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closePhonePeModal()" aria-label="Close">
            <i class="fa fa-times"></i>
        </button>
    </div>

    {{-- 2. Pinned Top Meta Strip (Exact Theme Pinned Bar) --}}
    <div class="mob-sheet-fixed-meta">
        <div class="sheet-fixed-meta-row">
            <span class="sheet-meta-pill"><i class="fa fa-id-card-o text-muted"></i> Adm: <b>{{ $modalAdmNo }}</b></span>
            <span class="sheet-meta-pill"><i class="fa fa-graduation-cap text-muted"></i> Class: <b>{{ $modalClassName }}</b></span>
            <span class="sheet-meta-pill" style="color: #0284c7; border-color: #38bdf8; background: #e0f2fe;">
                <i class="fa fa-bolt"></i> PhonePe PG
            </span>
        </div>
        <div class="sheet-student-name">
            {{ strtoupper($modalStudentName) }}
        </div>
    </div>

    {{-- 3. Scrollable Body --}}
    <div class="mob-sheet-body">

        {{-- Outstanding Balance Snapshot Card --}}
        <div class="mob-due-card">
            <div class="mob-due-left">
                <div class="mob-due-sub">OUTSTANDING DUE BALANCE</div>
                <div class="mob-due-amount" id="modalDueAmountDisplay">₹ {{ number_format($modalPendingFees, 2) }}</div>
            </div>
            <span class="mob-due-badge"><i class="fa fa-clock-o"></i> Unpaid</span>
        </div>

        {{-- Payable Amount Input --}}
        <div class="mob-form-group">
            <div class="mob-form-label-row">
                <label for="phonepePayAmountInput" class="mob-form-label">Enter Payable Amount (₹)</label>
                <a href="javascript:void(0);" class="mob-link-full" onclick="setPhonePeAmount({{ $pFees }}, null)">
                    <i class="fa fa-refresh"></i> Pay Full Due
                </a>
            </div>

            <div class="mob-input-wrap">
                <span class="mob-currency">₹</span>
                <input type="number" 
                       id="phonepePayAmountInput" 
                       class="mob-amount-input" 
                       value="{{ $pFees }}" 
                       min="1" 
                       max="{{ $pFees }}" 
                       step="1"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       oninput="onPhonePeAmountChange(this.value)"
                       placeholder="0">
            </div>
            <div class="mob-hint">
                <i class="fa fa-info-circle text-primary"></i> Partial payment allowed. Official receipt generates immediately.
            </div>
        </div>

        {{-- Quick Select Chips --}}
        <div class="mob-chips-group">
            <div class="mob-chips-label">QUICK SELECT:</div>
            <div class="mob-chips-wrap">
                <button type="button" class="mob-chip active" onclick="setPhonePeAmount({{ $pFees }}, this)">
                    <i class="fa fa-check-circle chip-check"></i> Full Due (₹{{ number_format($pFees, 0) }})
                </button>
                @if($pFees > 1000)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(1000, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹1,000
                    </button>
                @endif
                @if($pFees > 2500)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(2500, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹2,500
                    </button>
                @endif
                @if($pFees > 5000)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(5000, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹5,000
                    </button>
                @endif
                @if($pFees > 10000)
                    <button type="button" class="mob-chip" onclick="setPhonePeAmount(10000, this)">
                        <i class="fa fa-check-circle chip-check"></i> ₹10,000
                    </button>
                @endif
            </div>
        </div>

        {{-- Error Banner --}}
        <div id="phonepeModalError" class="mob-alert-error" style="display: none;"></div>

        {{-- Trust & Payment Methods Strip --}}
        <div class="mob-trust-box">
            <div class="mob-trust-icons">
                <span class="mob-trust-pill"><i class="fa fa-qrcode text-primary"></i> PhonePe / UPI</span>
                <span class="mob-trust-pill"><i class="fa fa-credit-card"></i> Cards</span>
                <span class="mob-trust-pill"><i class="fa fa-bank"></i> NetBanking</span>
            </div>
            <div class="mob-trust-secure">
                <i class="fa fa-lock text-success"></i> Instant Fee Receipt &bull; 100% Encrypted &amp; Secure
            </div>
        </div>

    </div>

    {{-- 4. Fixed Sheet Footer (Exact Theme Buttons) --}}
    <div class="mob-sheet-footer">
        <button type="button" class="btn btn-secondary mob-btn-cancel" onclick="closePhonePeModal()">
            Cancel
        </button>
        <button type="button" class="btn btn-primary mob-btn-pay" id="phonepeProceedBtn" onclick="submitPhonePePayment()">
            <i class="fa fa-shield"></i> Proceed to Pay <span id="phonepeBtnAmount">₹{{ number_format($pFees, 0) }}</span>
        </button>
    </div>

</div>

{{-- ==========================================================================
     5. IN-APP MOBILE PHONEPE GATEWAY FRAME (EXACT NATIVE MOBILE SHELL DESIGN)
     Pixel-perfect layout with dynamic responsive scaler to prevent any QR overflow
     ========================================================================== --}}
<div class="mob-phonepe-frame-layer" id="phonePeFrameLayer" style="display: none;" role="dialog" aria-modal="true" aria-label="PhonePe Secure Payment">
    
    {{-- Header: Matching exact Arise Mobile App Shell Header --}}
    <div class="mob-phonepe-frame-header">
        <div class="phonepe-top-left">
            <button type="button" class="phonepe-hdr-btn" onclick="closePhonePeFrame()" title="Back / Cancel Payment" aria-label="Back">
                <i class="fa fa-arrow-left"></i>
            </button>
            <div class="phonepe-hdr-meta">
                <div class="phonepe-hdr-title">
                    <i class="fa fa-shield" style="color: #38bdf8;"></i> PhonePe Secure Pay
                </div>
                <div class="phonepe-hdr-sub">
                    <span class="phonepe-sub-badge"><i class="fa fa-lock text-success"></i> 256-bit Encrypted</span>
                    <span class="phonepe-sub-dot">&bull;</span>
                    <span>Fee Payment</span>
                </div>
            </div>
        </div>

        <div class="phonepe-top-actions">
            <button type="button" class="phonepe-hdr-btn" onclick="reloadPhonePeFrame()" title="Refresh Session" aria-label="Refresh">
                <i class="fa fa-refresh" id="phonePeRefreshIcon"></i>
            </button>
            <a href="javascript:void(0);" id="phonePeExternalPopout" target="_blank" rel="noopener noreferrer" class="phonepe-hdr-btn" title="Open Fullscreen / UPI App" aria-label="Open in new window">
                <i class="fa fa-external-link"></i>
            </a>
            <button type="button" class="phonepe-hdr-btn phonepe-hdr-close" onclick="closePhonePeFrame()" title="Close" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>

    {{-- UAT Sandbox Mode Notice --}}
    <div id="phonePeUatNotice" style="display: none; background: #fffbeb; color: #92400e; padding: 8px 14px; font-size: 11px; text-align: center; border-bottom: 1px solid #fde68a; line-height: 1.4; font-weight: 500;">
        <i class="fa fa-flask" style="color: #d97706;"></i> <strong>Sandbox Test Mode:</strong> Please test via <strong>Test UPI ID (e.g. success@ybl)</strong>, Cards, or NetBanking below. (Real PhonePe App requires Production mode).
    </div>

    {{-- Frame Body & Responsive Viewport Scaler --}}
    <div class="mob-phonepe-frame-body" id="phonePeFrameBody">
        {{-- Loader with Arise / PhonePe branding --}}
        <div class="frame-loader" id="phonePeFrameLoader">
            <div class="frame-loader-brand">
                <div class="frame-loader-icon-wrap">
                    <i class="fa fa-shield frame-loader-shield"></i>
                    <i class="fa fa-lock frame-loader-lock"></i>
                </div>
                <div class="frame-loader-title">Connecting to PhonePe...</div>
                <div class="frame-loader-sub">Preparing secure payment gateway session</div>
            </div>
            <div class="frame-loader-spinner">
                <i class="fa fa-circle-o-notch fa-spin"></i>
            </div>
            <div class="frame-loader-hint">
                <i class="fa fa-info-circle text-info"></i> PhonePe UPI, QR &amp; Cards supported
            </div>
        </div>

        {{-- Dynamic Viewport Scaler Wrap: prevents QR code and outer card overflow --}}
        <div class="phonepe-scaler-viewport" id="phonePeScalerViewport">
            <iframe id="phonePeIframe" 
                    src="about:blank" 
                    class="phonepe-frame-iframe" 
                    allow="payment *; usb *; screen-wake-lock *" 
                    onload="onPhonePeIframeLoaded()"></iframe>
        </div>
    </div>

    {{-- Safe Area Bottom Bar with Direct App Intent Option --}}
    <div class="phonepe-frame-safe-footer">
        <button type="button" class="phonepe-app-switch-btn" onclick="openPhonePeAppDirect()" title="Open directly in PhonePe / UPI App">
            <i class="fa fa-bolt" style="color: #38bdf8;"></i> Pay with PhonePe App (Tap to switch)
        </button>
    </div>

</div>

<style>
/* ==========================================================================
   ARISE ERP - NATIVE SLIDE-UP MODAL SHEET STYLES (MATCHES HOMEWORK / THEME)
   ========================================================================== */
.mob-pay-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .22s ease-in-out, visibility .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-pay-backdrop.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Slide-Up Sheet Container Bound to Mobile Shell */
.mob-pay-sheet {
    display: flex;
    position: fixed;
    bottom: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 3px solid #0284c7;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 88vh;
    flex-direction: column;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.4);
    visibility: hidden;
    pointer-events: none;
}
.mob-pay-sheet.show {
    bottom: 0;
    visibility: visible;
    pointer-events: auto;
}

body.mob-sheet-lock {
    overflow: hidden !important;
}

/* Sheet Header */
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 5px 5px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 14px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .12s ease;
}
.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.25);
}

/* Pinned Top Meta Strip */
.mob-sheet-fixed-meta {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    flex-shrink: 0;
    z-index: 2;
}
.sheet-fixed-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.sheet-meta-pill {
    font-size: 9.5px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.sheet-student-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    margin-top: 4px;
    letter-spacing: -0.2px;
}

/* Scrollable Body */
.mob-sheet-body {
    padding: 12px 14px 16px 14px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

/* Due Balance Snapshot Card */
.mob-due-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-left: 3px solid #f59e0b;
    border-radius: 4px;
    padding: 9px 12px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-due-sub {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.mob-due-amount {
    font-size: 18px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.2;
    margin-top: 1px;
}
.mob-due-badge {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #b45309;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Form Group & Input */
.mob-form-group {
    margin-bottom: 12px;
}
.mob-form-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-form-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin: 0;
}
.mob-link-full {
    font-size: 10px;
    font-weight: 700;
    color: #0284c7;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-link-full:hover {
    text-decoration: underline;
}
.mob-input-wrap {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 10px;
    transition: all .15s ease;
}
.mob-input-wrap:focus-within {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-currency {
    font-size: 16px;
    font-weight: 800;
    color: #002C54;
    margin-right: 6px;
}
.mob-amount-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 17px;
    font-weight: 800;
    color: #002C54;
    width: 100%;
    padding: 0;
    font-family: inherit;
    -moz-appearance: textfield;
}
.mob-amount-input::-webkit-outer-spin-button,
.mob-amount-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.mob-hint {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Quick Chips */
.mob-chips-group {
    margin-bottom: 12px;
}
.mob-chips-label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.4px;
    margin-bottom: 5px;
}
.mob-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.mob-chip {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 4px 9px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    user-select: none;
}
.chip-check {
    display: none;
    font-size: 9px;
}
.mob-chip:hover {
    background: #f1f5f9;
}
.mob-chip.active {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0284c7;
    font-weight: 800;
}
.mob-chip.active .chip-check {
    display: inline-block;
}

/* Error Banner */
.mob-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 10.5px;
    font-weight: 600;
    padding: 6px 10px;
    border-radius: 3px;
    margin-bottom: 10px;
    line-height: 1.4;
}

/* Trust Strip */
.mob-trust-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
}
.mob-trust-icons {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 5px;
}
.mob-trust-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-trust-secure {
    text-align: center;
    font-size: 9px;
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

/* Sheet Footer */
.mob-sheet-footer {
    padding: 9px 14px calc(9px + env(safe-area-inset-bottom, 0px));
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-shrink: 0;
}
.mob-btn-cancel {
    flex: 1;
    padding: 7px 12px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    text-align: center;
}
.mob-btn-cancel:hover {
    background: #e2e8f0;
}
.mob-btn-pay {
    flex: 2;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 800;
    border-radius: 4px;
    background: #0284c7;
    border: 1px solid #0284c7;
    color: #ffffff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all .15s ease;
}
.mob-btn-pay:hover {
    background: #0369a1;
    border-color: #0369a1;
}
.mob-btn-pay:active {
    transform: scale(0.98);
}
.mob-btn-pay:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}

/* ==========================================================================
   IN-APP MOBILE PHONEPE GATEWAY FRAME STYLES
   Exact Native Mobile App Shell & Responsive Scaler System
   ========================================================================== */
.mob-phonepe-frame-layer {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    height: 100dvh;
    background: #001428;
    z-index: 9999999;
    display: flex;
    flex-direction: column;
    box-shadow: 0 0 50px rgba(0, 15, 30, 0.65);
    border-left: 1px solid rgba(255, 255, 255, 0.12);
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    overflow: hidden;
}
.mob-phonepe-frame-header {
    height: var(--header-height, 48px);
    background: rgba(0, 20, 40, 0.96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    flex-shrink: 0;
    z-index: 10;
}
.phonepe-top-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.phonepe-hdr-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.15s ease;
    flex-shrink: 0;
    padding: 0;
}
.phonepe-hdr-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.28);
    transform: translateY(-1px);
}
.phonepe-hdr-btn:active {
    transform: scale(0.95);
}
.phonepe-hdr-close:hover {
    background: rgba(239, 68, 68, 0.35);
    border-color: #ef4444;
    color: #fecaca !important;
}
.phonepe-hdr-meta {
    display: flex;
    flex-direction: column;
    min-width: 0;
    line-height: 1.25;
}
.phonepe-hdr-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    gap: 5px;
}
.phonepe-hdr-sub {
    font-size: 9.5px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 4px;
}
.phonepe-sub-badge {
    color: #38bdf8;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.phonepe-sub-dot {
    color: #475569;
}
.phonepe-top-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

/* Body & Scaler */
.mob-phonepe-frame-body {
    flex: 1;
    position: relative;
    width: 100%;
    height: calc(100% - 48px - 28px);
    overflow: hidden;
    background: #f8fafc;
}
.phonepe-scaler-viewport {
    width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
    background: #f8fafc;
}
.phonepe-frame-iframe {
    position: absolute;
    top: 0;
    left: 0;
    border: none;
    display: block;
    background: #ffffff;
}

/* Loader */
.frame-loader {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, #001f3f 0%, #002C54 60%, #001833 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 24px;
    z-index: 20;
    transition: opacity 0.25s ease, visibility 0.25s ease;
    color: #ffffff;
    text-align: center;
}
.frame-loader-brand {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
.frame-loader-icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: rgba(2, 132, 199, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 4px;
}
.frame-loader-shield {
    font-size: 26px;
    color: #38bdf8;
}
.frame-loader-lock {
    position: absolute;
    bottom: 9px;
    right: 11px;
    font-size: 11px;
    color: #22c55e;
}
.frame-loader-title {
    font-size: 14.5px;
    font-weight: 800;
    color: #ffffff;
}
.frame-loader-sub {
    font-size: 11px;
    color: #94a3b8;
}
.frame-loader-spinner i {
    font-size: 24px;
    color: #38bdf8;
}
.frame-loader-hint {
    font-size: 10px;
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.05);
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

/* Safe Footer Strip */
.phonepe-frame-safe-footer {
    height: 28px;
    background: #001428;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    flex-shrink: 0;
}
.safe-footer-pill {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.2px;
}
.phonepe-app-switch-btn {
    background: transparent;
    border: none;
    color: #38bdf8;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 4px;
    transition: all 0.15s ease;
    text-decoration: none;
}
.phonepe-app-switch-btn:hover {
    color: #7dd3fc;
    background: rgba(255, 255, 255, 0.08);
}
.phonepe-app-switch-btn:active {
    transform: scale(0.97);
}
</style>

<script>
var maxDueFees = {{ (float)($modalPendingFees) }};

function openPhonePeModal(due) {
    if (due !== undefined && due !== null) {
        maxDueFees = parseFloat(due) || 0;
    }
    var backdrop = document.getElementById('phonePeBackdrop');
    var sheet = document.getElementById('phonePePaySheet');
    var input = document.getElementById('phonepePayAmountInput');
    var displayDue = document.getElementById('modalDueAmountDisplay');
    var err = document.getElementById('phonepeModalError');

    if (err) err.style.display = 'none';

    if (displayDue) {
        displayDue.innerText = '₹ ' + maxDueFees.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    if (input) {
        input.value = Math.round(maxDueFees);
        input.max = Math.round(maxDueFees);
        onPhonePeAmountChange(input.value);
    }

    // Set first chip (Full Due) active
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(c, idx) {
        if (idx === 0) c.classList.add('active');
        else c.classList.remove('active');
    });

    if (backdrop && sheet) {
        backdrop.classList.add('show');
        sheet.classList.add('show');
        document.body.classList.add('mob-sheet-lock');
    }
}

function closePhonePeModal() {
    var backdrop = document.getElementById('phonePeBackdrop');
    var sheet = document.getElementById('phonePePaySheet');
    if (backdrop && sheet) {
        backdrop.classList.remove('show');
        sheet.classList.remove('show');
        document.body.classList.remove('mob-sheet-lock');
    }
}

function onPhonePeAmountChange(val) {
    var amt = parseFloat(val) || 0;
    var btnAmt = document.getElementById('phonepeBtnAmount');
    if (btnAmt) {
        btnAmt.innerText = '₹' + Math.round(amt).toLocaleString('en-IN');
    }

    // Highlight matching chip or unhighlight all
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(c) {
        var text = c.innerText.replace(/[^0-9]/g, '');
        if (parseInt(text, 10) === Math.round(amt)) {
            c.classList.add('active');
        } else {
            c.classList.remove('active');
        }
    });
}

function setPhonePeAmount(val, btn) {
    var input = document.getElementById('phonepePayAmountInput');
    if (input) {
        input.value = val;
        onPhonePeAmountChange(val);
    }
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(c) { c.classList.remove('active'); });
    if (btn) {
        btn.classList.add('active');
    } else {
        if (chips.length > 0) chips[0].classList.add('active');
    }
}

function isMobileDevice() {
    var ua = navigator.userAgent || navigator.vendor || window.opera || '';
    var isMobileUA = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);
    var isTouchDevice = ('ontouchstart' in window || (navigator.maxTouchPoints && navigator.maxTouchPoints > 0));
    var isNarrow = window.innerWidth <= 768;
    return isMobileUA || (isTouchDevice && isNarrow);
}

var currentPhonePeUrl = '';
var currentIsUat = false;

function openPhonePeAppDirect() {
    if (currentIsUat) {
        var proceed = confirm("⚠️ UAT Sandbox Test Mode Notice:\n\nPhonePe's real consumer app (from Play Store) only processes Live/Production payments and will reject sandbox test transactions with 'Close and Retry'.\n\nFor Sandbox testing, please complete payment directly on screen using Test UPI ID (e.g. success@ybl) or Cards/NetBanking.\n\nDo you still want to attempt opening the PhonePe app?");
        if (!proceed) return;
    }
    if (currentPhonePeUrl) {
        window.top.location.href = currentPhonePeUrl;
    }
}

function submitPhonePePayment() {
    var input = document.getElementById('phonepePayAmountInput');
    var errBox = document.getElementById('phonepeModalError');
    var btn = document.getElementById('phonepeProceedBtn');
    var amount = parseFloat(input ? input.value : 0);

    if (errBox) errBox.style.display = 'none';

    if (!amount || amount <= 0) {
        showPhonePeError('Please enter a valid amount greater than ₹0.');
        return;
    }
    if (amount > maxDueFees) {
        showPhonePeError('Amount cannot exceed your total pending fee of ₹' + maxDueFees.toLocaleString('en-IN'));
        return;
    }

    // Set loading state
    btn.disabled = true;
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Connecting to PhonePe...';

    fetch('{{ route("student.payment.initiate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ amount: amount })
    })
    .then(function(res) {
        return res.json().then(function(data) {
            return { ok: res.ok, data: data };
        });
    })
    .then(function(response) {
        if (response.ok && response.data.status && response.data.redirect_url) {
            var redirectUrl = response.data.redirect_url;
            var isUat = (response.data.env === 'UAT');

            btn.disabled = false;
            btn.innerHTML = originalHtml;

            // Open inside unified in-app mobile frame so user stays inside Arise ERP
            // and mobile intent hijacking ('Close and Retry') is prevented in Sandbox
            openPhonePeMobileFrame(redirectUrl, isUat);
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            showPhonePeError(response.data.message || 'Unable to initiate PhonePe payment. Please try again.');
        }
    })
    .catch(function(error) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        showPhonePeError('Network error connecting to payment gateway: ' + error.message);
    });
}

function applyPhonePeIframeScale() {
    var viewport = document.getElementById('phonePeScalerViewport');
    var iframe = document.getElementById('phonePeIframe');
    if (!viewport || !iframe) return;

    var cw = viewport.clientWidth;
    var ch = viewport.clientHeight;
    if (cw <= 0 || ch <= 0) return;

    // PhonePe Standard Checkout hosted paypage requires at least ~500px internal width
    // to cleanly display the white QR code card inside the outer card with proper margins
    // without overflowing or clipping.
    var baseVirtualWidth = 500;

    if (cw < baseVirtualWidth) {
        var scale = cw / baseVirtualWidth;
        iframe.style.width = baseVirtualWidth + 'px';
        iframe.style.height = (ch / scale) + 'px';
        iframe.style.transform = 'scale(' + scale + ')';
        iframe.style.transformOrigin = 'top left';
    } else {
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        iframe.style.transform = 'none';
        iframe.style.transformOrigin = 'top left';
    }
}

window.addEventListener('resize', applyPhonePeIframeScale);
window.addEventListener('orientationchange', function() {
    setTimeout(applyPhonePeIframeScale, 200);
});

function openPhonePeMobileFrame(url, isUat) {
    closePhonePeModal();
    currentPhonePeUrl = url;
    currentIsUat = !!isUat;

    var frameLayer = document.getElementById('phonePeFrameLayer');
    var iframe = document.getElementById('phonePeIframe');
    var loader = document.getElementById('phonePeFrameLoader');
    var popout = document.getElementById('phonePeExternalPopout');
    var uatNotice = document.getElementById('phonePeUatNotice');

    if (uatNotice) {
        uatNotice.style.display = currentIsUat ? 'block' : 'none';
    }

    if (loader) {
        loader.style.display = 'flex';
        loader.style.opacity = '1';
        loader.style.visibility = 'visible';
    }
    if (popout) {
        popout.href = url;
    }
    if (iframe) {
        iframe.src = url;
    }
    if (frameLayer) {
        frameLayer.style.display = 'flex';
        document.body.classList.add('mob-sheet-lock');
    }

    applyPhonePeIframeScale();
    setTimeout(applyPhonePeIframeScale, 150);
}

function onPhonePeIframeLoaded() {
    applyPhonePeIframeScale();
    var loader = document.getElementById('phonePeFrameLoader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(function() {
            loader.style.visibility = 'hidden';
            loader.style.display = 'none';
            applyPhonePeIframeScale();
        }, 220);
    }
}

function reloadPhonePeFrame() {
    var icon = document.getElementById('phonePeRefreshIcon');
    if (icon) icon.classList.add('fa-spin');

    var loader = document.getElementById('phonePeFrameLoader');
    if (loader) {
        loader.style.display = 'flex';
        loader.style.opacity = '1';
        loader.style.visibility = 'visible';
    }

    var iframe = document.getElementById('phonePeIframe');
    if (iframe && currentPhonePeUrl) {
        iframe.src = currentPhonePeUrl;
    }

    setTimeout(function() {
        if (icon) icon.classList.remove('fa-spin');
    }, 1000);
}

function closePhonePeFrame() {
    if (confirm('Are you sure you want to cancel and exit the payment process?')) {
        var frameLayer = document.getElementById('phonePeFrameLayer');
        var iframe = document.getElementById('phonePeIframe');
        currentPhonePeUrl = '';
        if (iframe) iframe.src = 'about:blank';
        if (frameLayer) {
            frameLayer.style.display = 'none';
            document.body.classList.remove('mob-sheet-lock');
        }
    }
}

function showPhonePeError(msg) {
    var errBox = document.getElementById('phonepeModalError');
    if (errBox) {
        errBox.innerText = msg;
        errBox.style.display = 'block';
    } else {
        alert(msg);
    }
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhonePeModal();
    }
});
</script>
