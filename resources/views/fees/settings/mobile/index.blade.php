@php
    $currentSessionName = Session::get('session_name') ?? $sessionName ?? '2026-27';
    $samplePreview = $samplePreview ?? 'REC-2026-0001';
    $allowedModesArray = $allowedModesArray ?? ['1', '2', '3', '4'];
@endphp

@extends('layout.mobile_app')

@section('title', 'Fees Settings')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FEES SETTINGS & POLICY CONFIGURATION
   Native mobile app design matching Arise ERP mobile design system
   ========================================================================== */
.settings-mob-container {
    padding: 8px 10px 90px 10px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}

/* 1. Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 6px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 3px 10px rgba(0, 44, 84, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-hero-title {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 2px 7px;
    border-radius: 10px;
    color: #38bdf8;
}

/* 2. Horizontal Tab Chips */
.mob-tabs-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 6px;
    margin-bottom: 10px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.mob-tabs-strip::-webkit-scrollbar {
    display: none;
}
.mob-tab-btn {
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 16px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
}
.mob-tab-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
}

/* 3. Settings Cards */
.mob-setting-card {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    padding: 12px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-card-section-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-form-group {
    margin-bottom: 10px;
}
.mob-form-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}
.mob-form-input, .mob-form-select, .mob-form-textarea {
    width: 100%;
    height: 36px;
    padding: 6px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease;
}
.mob-form-textarea {
    height: 70px;
    resize: vertical;
}
.mob-form-input:focus, .mob-form-select:focus, .mob-form-textarea:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
}
.mob-form-hint {
    font-size: 10px;
    color: #64748b;
    margin-top: 3px;
    display: block;
}

/* Preview Box */
.mob-preview-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 12px;
    text-align: center;
}
.mob-preview-lbl {
    font-size: 9.5px;
    font-weight: 700;
    color: #1e40af;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 2px;
}
.mob-preview-badge {
    font-size: 16px;
    font-weight: 800;
    color: #1d4ed8;
    background: #ffffff;
    border: 1px dashed #3b82f6;
    padding: 4px 12px;
    border-radius: 4px;
    display: inline-block;
    letter-spacing: .05em;
    margin-top: 4px;
}

/* Custom Checkbox Grid */
.mob-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-top: 6px;
}
.mob-checkbox-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
}
.mob-checkbox-card input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #002C54;
}

/* Toggle Switch Row */
.mob-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}
.mob-toggle-info {
    max-width: 75%;
}
.mob-toggle-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}
.mob-toggle-desc {
    font-size: 10px;
    color: #64748b;
    margin: 2px 0 0 0;
}
.mob-switch {
    position: relative;
    display: inline-block;
    width: 42px;
    height: 24px;
}
.mob-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.mob-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 24px;
}
.mob-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
}
input:checked + .mob-slider {
    background-color: #10b981;
}
input:checked + .mob-slider:before {
    transform: translateX(18px);
}

/* Pinned Bottom Save Bar */
.mob-save-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-top: 1px solid #cbd5e1;
    padding: 8px 12px;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.btn-mob-save-all {
    width: 100%;
    height: 40px;
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
    cursor: pointer;
}
.btn-mob-save-all:active {
    background: #059669;
}
</style>

<div class="settings-mob-container">
    {{-- 1. Mobile Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-cogs text-warning"></i> Fees Policy &amp; Settings
            </div>
            <span class="mob-session-pill">{{ $currentSessionName }}</span>
        </div>
        <p class="text-white-50 mb-0 mt-1" style="font-size: 11px;">
            Configure receipt generator format, payment methods, print preferences &amp; reminders.
        </p>
    </div>

    {{-- Form container --}}
    <form action="{{ url('fees/settings/update') }}" method="POST" id="mobFeesSettingsForm">
        @csrf

        {{-- 2. Horizontal Tab Chips --}}
        <div class="mob-tabs-strip">
            <button type="button" class="mob-tab-btn active" data-tab="tab-receipt">
                <i class="fa fa-file-text-o"></i> 1. Receipt Format
            </button>
            <button type="button" class="mob-tab-btn" data-tab="tab-payment">
                <i class="fa fa-credit-card"></i> 2. Payment Modes
            </button>
            <button type="button" class="mob-tab-btn" data-tab="tab-print">
                <i class="fa fa-print"></i> 3. Print &amp; PDF
            </button>
            <button type="button" class="mob-tab-btn" data-tab="tab-alerts">
                <i class="fa fa-bell"></i> 4. Reminders
            </button>
        </div>

        {{-- TAB 1: Receipt Format --}}
        <div class="mob-tab-pane" id="tab-receipt">
            <div class="mob-setting-card">
                <div class="mob-preview-box">
                    <div class="mob-preview-lbl"><i class="fa fa-eye mr-1"></i> Live Receipt Preview</div>
                    <div class="mob-preview-badge" id="mobReceiptPreview">{{ $samplePreview }}</div>
                </div>

                <div class="mob-card-section-title">
                    <i class="fa fa-sliders text-primary"></i> Numbering Mode &amp; Digits
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_prefix">Receipt Prefix</label>
                    <input type="text" class="mob-form-input" id="receipt_prefix" name="receipt_prefix" value="{{ old('receipt_prefix', $setting->receipt_prefix ?? 'REC-') }}" placeholder="e.g. REC-">
                    <span class="mob-form-hint">Text prefixed before serial number (e.g. REC-)</span>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_digit_padding">Digits Padding Length</label>
                    <select class="mob-form-select" id="receipt_digit_padding" name="receipt_digit_padding">
                        <option value="3" {{ (int)($setting->receipt_digit_padding ?? 4) === 3 ? 'selected' : '' }}>3 Digits (e.g. 001)</option>
                        <option value="4" {{ (int)($setting->receipt_digit_padding ?? 4) === 4 ? 'selected' : '' }}>4 Digits (e.g. 0001)</option>
                        <option value="5" {{ (int)($setting->receipt_digit_padding ?? 4) === 5 ? 'selected' : '' }}>5 Digits (e.g. 00001)</option>
                        <option value="6" {{ (int)($setting->receipt_digit_padding ?? 4) === 6 ? 'selected' : '' }}>6 Digits (e.g. 000001)</option>
                    </select>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label">Session Counter</label>
                    <div class="badge badge-light border p-2 w-100 text-left font-weight-bold" style="font-size: 11px; color: #166534; background: #f0fdf4;">
                        <i class="fa fa-check-circle mr-1"></i> Current Counter: #{{ $currentCounter ?? 0 }} (Next: #{{ $nextCounter ?? 1 }})
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: Payment Policy --}}
        <div class="mob-tab-pane" id="tab-payment" style="display: none;">
            <div class="mob-setting-card">
                <div class="mob-card-section-title">
                    <i class="fa fa-credit-card text-success"></i> Allowed Payment Modes
                </div>
                <div class="mob-checkbox-grid">
                    @foreach($paymentModes ?? [] as $pm)
                        <label class="mob-checkbox-card">
                            <input type="checkbox" name="allowed_payment_modes[]" value="{{ $pm->id }}" {{ in_array((string)$pm->id, $allowedModesArray) ? 'checked' : '' }}>
                            <span>{{ $pm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mob-setting-card">
                <div class="mob-card-section-title">
                    <i class="fa fa-balance-scale text-primary"></i> Partial &amp; Advance Collection
                </div>

                <div class="mob-toggle-row">
                    <div class="mob-toggle-info">
                        <div class="mob-toggle-title">Allow Partial Payments</div>
                        <div class="mob-toggle-desc">Students can pay partial installments</div>
                    </div>
                    <label class="mob-switch">
                        <input type="checkbox" name="allow_partial_payment" value="1" {{ ($setting->allow_partial_payment ?? 1) ? 'checked' : '' }}>
                        <span class="mob-slider"></span>
                    </label>
                </div>

                <div class="mob-toggle-row">
                    <div class="mob-toggle-info">
                        <div class="mob-toggle-title">Allow Advance Payments</div>
                        <div class="mob-toggle-desc">Collect excess payments as advance balance</div>
                    </div>
                    <label class="mob-switch">
                        <input type="checkbox" name="allow_advance_payment" value="1" {{ ($setting->allow_advance_payment ?? 0) ? 'checked' : '' }}>
                        <span class="mob-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- TAB 3: Print & PDF --}}
        <div class="mob-tab-pane" id="tab-print" style="display: none;">
            <div class="mob-setting-card">
                <div class="mob-card-section-title">
                    <i class="fa fa-print text-info"></i> Receipt Header &amp; Footer Notes
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_header_title">Receipt Header Title</label>
                    <input type="text" class="mob-form-input" id="receipt_header_title" name="receipt_header_title" value="{{ old('receipt_header_title', $setting->receipt_header_title ?? '') }}" placeholder="e.g. FEE DEPOSIT RECEIPT">
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_footer_note">Footer Note</label>
                    <input type="text" class="mob-form-input" id="receipt_footer_note" name="receipt_footer_note" value="{{ old('receipt_footer_note', $setting->receipt_footer_note ?? 'Thank you for your timely payment!') }}">
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_terms">Terms &amp; Conditions</label>
                    <textarea class="mob-form-textarea" id="receipt_terms" name="receipt_terms" placeholder="Terms printed on receipt">{{ old('receipt_terms', $setting->receipt_terms ?? 'Fees once paid is non-refundable.') }}</textarea>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="receipt_copies_count">Print Copies</label>
                    <select class="mob-form-select" id="receipt_copies_count" name="receipt_copies_count">
                        <option value="1" {{ ($setting->receipt_copies_count ?? 1) == 1 ? 'selected' : '' }}>1 Copy (Student Copy)</option>
                        <option value="2" {{ ($setting->receipt_copies_count ?? 1) == 2 ? 'selected' : '' }}>2 Copies (Student + Office)</option>
                        <option value="3" {{ ($setting->receipt_copies_count ?? 1) == 3 ? 'selected' : '' }}>3 Copies (Student + Office + Bank)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TAB 4: Due Reminders --}}
        <div class="mob-tab-pane" id="tab-alerts" style="display: none;">
            <div class="mob-setting-card">
                <div class="mob-card-section-title">
                    <i class="fa fa-bell text-warning"></i> Due Date Reminder Days
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="reminder_before_days">Days Before Due Date</label>
                    <input type="number" class="mob-form-input" id="reminder_before_days" name="reminder_before_days" value="{{ old('reminder_before_days', $setting->reminder_before_days ?? 3) }}" min="0" max="30">
                    <span class="mob-form-hint">Send reminder X days prior to due date</span>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label" for="reminder_after_days">Days After Due Date (Overdue)</label>
                    <input type="number" class="mob-form-input" id="reminder_after_days" name="reminder_after_days" value="{{ old('reminder_after_days', $setting->reminder_after_days ?? 7) }}" min="0" max="30">
                    <span class="mob-form-hint">Follow-up reminder X days after due date</span>
                </div>

                <div class="mob-toggle-row">
                    <div class="mob-toggle-info">
                        <div class="mob-toggle-title">Auto WhatsApp Reminders</div>
                        <div class="mob-toggle-desc">Enable automated WhatsApp reminder queue</div>
                    </div>
                    <label class="mob-switch">
                        <input type="checkbox" name="auto_whatsapp_reminder" value="1" {{ ($setting->auto_whatsapp_reminder ?? 0) ? 'checked' : '' }}>
                        <span class="mob-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Pinned Bottom Save Bar --}}
        <div class="mob-save-bar">
            <button type="submit" class="btn-mob-save-all">
                <i class="fa fa-check-circle"></i> Save Fee Settings
            </button>
        </div>
    </form>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Tab switching
    $('.mob-tab-btn').on('click', function() {
        var targetTab = $(this).data('tab');
        $('.mob-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.mob-tab-pane').hide();
        $('#' + targetTab).show();
    });

    // Real-time preview update
    function updatePreview() {
        var prefix = $('#receipt_prefix').val() || '';
        var padding = parseInt($('#receipt_digit_padding').val()) || 4;
        var seq = '1'.padStart(padding, '0');
        var preview = prefix + "{{ $currentSessionName }}-" + seq;
        $('#mobReceiptPreview').text(preview);
    }

    $('#receipt_prefix, #receipt_digit_padding').on('input change', updatePreview);
});
</script>
@endsection
