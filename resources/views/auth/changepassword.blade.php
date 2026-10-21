@extends('layout.app')

@php
    $userName = trim((string) (Session::get('first_name') ?: (optional($user)->first_name ?? 'Account User')));
    $userInitial = !empty($userName) ? mb_strtoupper(mb_substr($userName, 0, 1)) : 'A';
    $roleName = DB::table('role')->whereNull('deleted_at')->find(Session::get('role_id'));
    $roleTitle = $roleName->name ?? (Session::get('role_id') == 3 ? 'Student' : 'Staff');
    $userAccountName = optional($user)->userName ?: (Session::get('userName') ?: 'User');
@endphp

@section('content')
<div class="content-wrapper change-password-page">
    <div class="cp-page-layout">

        {{-- 1. Signature Dark Navy Top Hero Header --}}
        <header class="cp-hero">
            <div class="cp-hero-text">
                <span class="cp-kicker"><i class="fa fa-shield"></i> ACCOUNT SECURITY & CREDENTIALS</span>
                <h1 class="cp-hero-title">
                    <i class="fa fa-key text-warning"></i> Change Password
                </h1>
                <p class="cp-hero-sub">
                    Update your account password regularly to ensure security and prevent unauthorized access.
                </p>
            </div>

            <div class="cp-hero-actions">
                <a href="{{ url('dashboard') }}" class="dash-btn dash-btn-outline" title="Return to Dashboard">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
                <a href="{{ url('profile/edit/' . (Session::get('id') ?? 1)) }}" class="dash-btn dash-btn-light" title="View Profile">
                    <i class="fa fa-user-circle"></i> Profile Setting
                </a>
            </div>
        </header>

        {{-- 2. Main Scrollable Container with Centered Card --}}
        <main class="cp-main-body">
            <div class="cp-card-wrapper">
                <div class="cp-card">

                    {{-- Card Header --}}
                    <div class="cp-card-header">
                        <div class="cp-header-title">
                            <i class="fa fa-lock"></i>
                            <span>Update Security Credentials</span>
                        </div>
                        <span class="cp-security-badge">
                            <i class="fa fa-shield"></i> SSL Protected
                        </span>
                    </div>

                    {{-- User Account Info Pill --}}
                    <div class="cp-account-strip">
                        <div class="cp-avatar-badge">{{ $userInitial }}</div>
                        <div class="cp-account-meta">
                            <h4 class="cp-user-name">{{ $userName }}</h4>
                            <div class="cp-user-sub">
                                <span class="badge-role">{{ $roleTitle }}</span>
                                <span class="meta-sep">&bull;</span>
                                <span class="cp-user-tag"><i class="fa fa-user-o"></i> {{ $userAccountName }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Alert Banner for AJAX / Live status --}}
                    <div id="ajaxAlertBanner" class="cp-alert-banner" style="display: none;"></div>

                    {{-- Form --}}
                    <form id="changePasswordForm" action="{{ route('change_password') }}" method="POST" autocomplete="off">
                        @csrf

                        {{-- Field 1: Current / Old Password --}}
                        <div class="form-group cp-form-group">
                            <label for="old_password" class="cp-label">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="cp-input-group">
                                <span class="cp-input-icon"><i class="fa fa-lock"></i></span>
                                <input type="password" name="old_password" id="old_password" 
                                       class="form-control cp-input" 
                                       placeholder="Enter your current password" 
                                       autocomplete="current-password" required>
                                <button type="button" class="cp-eye-btn btn-toggle-pw" data-target="#old_password" tabindex="-1" title="Show/Hide Password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback cp-feedback" id="error-old_password"></div>
                        </div>

                        {{-- Field 2: New Password --}}
                        <div class="form-group cp-form-group">
                            <label for="password" class="cp-label">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="cp-input-group">
                                <span class="cp-input-icon"><i class="fa fa-key"></i></span>
                                <input type="password" name="password" id="password" 
                                       class="form-control cp-input" 
                                       placeholder="Enter at least 6 characters" 
                                       autocomplete="new-password" required>
                                <button type="button" class="cp-eye-btn btn-toggle-pw" data-target="#password" tabindex="-1" title="Show/Hide Password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>

                            {{-- Live Password Strength Bar --}}
                            <div class="strength-meter-wrap" id="strengthMeterWrap" style="display: none;">
                                <div class="strength-header">
                                    <span class="strength-label">Password Strength:</span>
                                    <span class="strength-status" id="strengthStatus">Weak</span>
                                </div>
                                <div class="strength-bar-track">
                                    <div class="strength-bar-fill" id="strengthBarFill"></div>
                                </div>
                            </div>

                            {{-- Live Criteria Checklist --}}
                            <div class="criteria-checklist" id="criteriaChecklist">
                                <span class="criteria-chip" id="crit-length"><i class="fa fa-circle-o"></i> Min 6 characters</span>
                                <span class="criteria-chip" id="crit-case"><i class="fa fa-circle-o"></i> Letters</span>
                                <span class="criteria-chip" id="crit-num"><i class="fa fa-circle-o"></i> Number / Symbol</span>
                            </div>

                            <div class="invalid-feedback cp-feedback" id="error-password"></div>
                        </div>

                        {{-- Field 3: Confirm New Password --}}
                        <div class="form-group cp-form-group">
                            <label for="password_confirmation" class="cp-label">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="cp-input-group">
                                <span class="cp-input-icon"><i class="fa fa-check-circle-o"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control cp-input" 
                                       placeholder="Re-type your new password" 
                                       autocomplete="new-password" required>
                                <button type="button" class="cp-eye-btn btn-toggle-pw" data-target="#password_confirmation" tabindex="-1" title="Show/Hide Password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>

                            {{-- Live Password Match Indicator --}}
                            <div class="match-indicator" id="matchIndicator" style="display: none;"></div>

                            <div class="invalid-feedback cp-feedback" id="error-password_confirmation"></div>
                        </div>

                        {{-- Security Tip Callout --}}
                        <div class="cp-security-tip">
                            <i class="fa fa-info-circle text-primary mr-1"></i>
                            <span><b>Tip:</b> Choose a strong, unique password that you don't use for other accounts.</span>
                        </div>

                        {{-- Form Action Buttons --}}
                        <div class="cp-form-actions">
                            <button type="reset" class="dash-btn dash-btn-outline text-muted" id="btnResetForm">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                            <button type="submit" class="dash-btn dash-btn-primary btn-submit-pw" id="btnSubmitPw">
                                <i class="fa fa-check-circle"></i> <span>Update Password</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>

        {{-- 3. Docked Bottom Summary Bar --}}
        <footer class="cp-bottom-bar">
            <div class="bottom-bar-left">
                <i class="fa fa-lock mr-1"></i>
                Account Security Center &bull; Session Active for <b>{{ $userName }}</b>
            </div>
            <div class="bottom-bar-right">
                <span class="bottom-info-pill"><i class="fa fa-clock-o mr-1"></i> {{ date('d M Y') }}</span>
            </div>
        </footer>

    </div>
</div>

{{-- =========================================================================
     THEME-BASED SUCCESS MODAL (Student Promote Reference)
     ========================================================================= --}}
<div class="modal fade" id="cpSuccessModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none; box-shadow: 0 10px 25px rgba(0,0,0,.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #ffffff; padding: 10px 14px; border: none;">
                <h5 class="modal-title" style="font-size: 13px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 6px;">
                    <i class="fa fa-check-circle text-success"></i> Password Updated
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: .85; font-size: 18px; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="padding: 22px 18px;">
                <div style="width: 54px; height: 54px; border-radius: 50%; background: #ecfdf5; color: #059669; font-size: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto; border: 1px solid #a7f3d0;">
                    <i class="fa fa-check"></i>
                </div>
                <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Success!</h4>
                <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0;" id="cpSuccessMessage">
                    Your password has been changed successfully. You can use your new password next time you log in.
                </p>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 8px 14px; display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">
                    Close
                </button>
                <a href="{{ url('dashboard') }}" class="btn btn-sm btn-primary" style="background: #002C54; border-color: #001f3d;">
                    <i class="fa fa-home mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Base Viewport & Page Layout */
.change-password-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.change-password-page * {
    box-sizing: border-box;
}
.cp-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 1. Signature Dark Navy Top Hero Header (#002C54) */
.cp-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.cp-hero-text {
    display: flex;
    flex-direction: column;
}
.cp-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    color: #93c5fd;
    font-weight: 600;
}
.cp-hero-title {
    font-size: 16px;
    font-weight: 700;
    margin: 2px 0 0 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.cp-hero-sub {
    font-size: 11px;
    margin: 2px 0 0 0;
    color: #cbd5e1;
}
.cp-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Button Standards */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 28px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #e2e8f0 !important;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #ffffff;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.2) !important;
    color: #ffffff !important;
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.dash-btn-primary:hover:not(:disabled) {
    background: #0369a1 !important;
}
.dash-btn:disabled {
    opacity: .55;
    cursor: not-allowed;
}

/* 2. Main Body & Card */
.cp-main-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 12px 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
}
.cp-card-wrapper {
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
}
.cp-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 4px 15px rgba(0, 44, 84, 0.08);
    overflow: hidden;
}

/* Card Header */
.cp-card-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #001f3d;
}
.cp-header-title {
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 7px;
}
.cp-security-badge {
    font-size: 10px;
    font-weight: 600;
    background: rgba(16, 185, 129, 0.2);
    color: #a7f3d0;
    border: 1px solid rgba(16, 185, 129, 0.4);
    padding: 2px 7px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Account Strip */
.cp-account-strip {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.cp-avatar-badge {
    width: 36px;
    height: 36px;
    border-radius: 3px;
    background: linear-gradient(135deg, #002C54 0%, #0a4d8c 100%);
    color: #ffffff;
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #001f3d;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    flex-shrink: 0;
}
.cp-account-meta {
    display: flex;
    flex-direction: column;
}
.cp-user-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.2;
}
.cp-user-sub {
    font-size: 10.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 2px;
}
.badge-role {
    background: #e0e7ff;
    color: #3730a3;
    padding: 1px 6px;
    border-radius: 2px;
    font-weight: 600;
    font-size: 9.5px;
    text-transform: uppercase;
}
.meta-sep {
    color: #cbd5e1;
}

/* Alert Banner */
.cp-alert-banner {
    padding: 8px 14px;
    margin: 14px 16px 0 16px;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}
.cp-alert-banner.alert-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.cp-alert-banner.alert-danger {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Form Styles */
#changePasswordForm {
    padding: 16px 18px;
}
.cp-form-group {
    margin-bottom: 14px;
}
.cp-label {
    display: block;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
}
.cp-input-group {
    position: relative;
    display: flex;
    align-items: center;
}
.cp-input-icon {
    position: absolute;
    left: 10px;
    font-size: 12px;
    color: #94a3b8;
    pointer-events: none;
    z-index: 2;
}
.cp-input {
    height: 34px;
    padding: 0 34px 0 32px !important;
    font-size: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 3px !important;
    transition: all .15s ease-in-out;
    background: #ffffff;
    width: 100%;
}
.cp-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
    outline: none;
}
.cp-input.is-invalid {
    border-color: #dc2626 !important;
    background-color: #fffbfa;
}
.cp-eye-btn {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 6px 8px;
    font-size: 13px;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 2px;
    transition: color .15s;
    z-index: 2;
}
.cp-eye-btn:hover {
    color: #334155;
}
.cp-feedback {
    display: none;
    font-size: 10.5px;
    font-weight: 600;
    color: #dc2626;
    margin-top: 3px;
}
.cp-feedback.is-visible {
    display: block;
}

/* Strength Meter */
.strength-meter-wrap {
    margin-top: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 8px;
}
.strength-header {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    font-weight: 600;
    margin-bottom: 4px;
}
.strength-label {
    color: #64748b;
}
.strength-status {
    font-weight: 700;
    text-transform: uppercase;
}
.strength-bar-track {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.strength-bar-fill {
    height: 100%;
    width: 0;
    transition: width 0.25s ease, background-color 0.25s ease;
    border-radius: 2px;
}

/* Criteria Checklist */
.criteria-checklist {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 5px;
}
.criteria-chip {
    font-size: 9.5px;
    font-weight: 600;
    color: #94a3b8;
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 10px;
    transition: all .2s ease;
}
.criteria-chip.is-valid {
    background: #ecfdf5;
    color: #059669;
}
.criteria-chip.is-valid i:before {
    content: "\f058"; /* check-circle */
}

/* Match Indicator */
.match-indicator {
    font-size: 10.5px;
    font-weight: 600;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.match-indicator.is-matching {
    color: #059669;
}
.match-indicator.is-mismatch {
    color: #dc2626;
}

/* Security Tip */
.cp-security-tip {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    padding: 6px 10px;
    font-size: 11px;
    color: #0369a1;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
}

/* Form Actions */
.cp-form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    border-top: 1px solid #e2e8f0;
    padding-top: 12px;
}
.btn-submit-pw {
    background: linear-gradient(135deg, #002C54 0%, #0a4d8c 100%) !important;
    border-color: #001f3d !important;
    color: #ffffff !important;
    min-width: 140px;
}
.btn-submit-pw:hover:not(:disabled) {
    background: linear-gradient(135deg, #001f3d 0%, #002C54 100%) !important;
}

/* 3. Docked Bottom Bar */
.cp-bottom-bar {
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    padding: 6px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 -2px 6px rgba(0,44,84,.12);
}
.bottom-bar-left {
    font-size: 11px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
}
.bottom-bar-left b {
    color: #ffffff;
    margin-left: 3px;
}
.bottom-info-pill {
    font-size: 10.5px;
    color: #93c5fd;
    background: rgba(255,255,255,.1);
    padding: 2px 7px;
    border-radius: 2px;
}

@media(max-width: 576px) {
    .cp-hero {
        flex-direction: column;
        align-items: flex-start;
    }
    .cp-hero-actions {
        width: 100%;
        justify-content: space-between;
    }
    .cp-card-wrapper {
        padding: 0 4px;
    }
    .cp-bottom-bar {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var $form = $('#changePasswordForm');
    var $oldPw = $('#old_password');
    var $newPw = $('#password');
    var $confirmPw = $('#password_confirmation');
    var $btnSubmit = $('#btnSubmitPw');
    var $alertBanner = $('#ajaxAlertBanner');

    // 1. Password Visibility Eye Toggle
    $(document).on('click', '.btn-toggle-pw', function() {
        var targetSel = $(this).data('target');
        var input = $(targetSel);
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash text-primary');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash text-primary').addClass('fa-eye');
        }
    });

    // 2. Real-Time Password Strength Meter
    $newPw.on('input', function() {
        var val = $(this).val();
        if (!val) {
            $('#strengthMeterWrap').hide();
            $('#crit-length, #crit-case, #crit-num').removeClass('is-valid');
            checkPasswordMatch();
            return;
        }

        $('#strengthMeterWrap').show();

        var score = 0;
        var hasLength = val.length >= 6;
        var hasCase = /[a-zA-Z]/.test(val);
        var hasNum = /[0-9!@#$%^&*(),.?":{}|<>]/.test(val);

        // Update criteria checklist
        $('#crit-length').toggleClass('is-valid', hasLength);
        $('#crit-case').toggleClass('is-valid', hasCase);
        $('#crit-num').toggleClass('is-valid', hasNum);

        if (hasLength) score += 35;
        if (hasCase) score += 30;
        if (hasNum) score += 35;

        var statusText = 'Weak';
        var fillColor = '#ef4444'; // Red

        if (score >= 70 && score < 100) {
            statusText = 'Medium';
            fillColor = '#f59e0b'; // Amber
        } else if (score >= 100) {
            statusText = 'Strong';
            fillColor = '#10b981'; // Green
        }

        $('#strengthStatus').text(statusText).css('color', fillColor);
        $('#strengthBarFill').css({
            'width': score + '%',
            'background-color': fillColor
        });

        checkPasswordMatch();
    });

    // 3. Real-Time Password Match Indicator
    function checkPasswordMatch() {
        var newPass = $newPw.val();
        var confirmPass = $confirmPw.val();

        if (!confirmPass) {
            $('#matchIndicator').hide();
            return;
        }

        $('#matchIndicator').show();
        if (newPass === confirmPass) {
            $('#matchIndicator')
                .removeClass('is-mismatch')
                .addClass('is-matching')
                .html('<i class="fa fa-check-circle"></i> Passwords match');
        } else {
            $('#matchIndicator')
                .removeClass('is-matching')
                .addClass('is-mismatch')
                .html('<i class="fa fa-times-circle"></i> Passwords do not match');
        }
    }

    $confirmPw.on('input', checkPasswordMatch);

    // 4. Clear Inline Errors on user typing
    $('.cp-input').on('input', function() {
        $(this).removeClass('is-invalid');
        var errId = '#error-' + $(this).attr('name');
        $(errId).removeClass('is-visible').text('');
        $alertBanner.slideUp(150);
    });

    // 5. Reset Form Helper
    $('#btnResetForm').on('click', function() {
        $('.cp-input').removeClass('is-invalid');
        $('.cp-feedback').removeClass('is-visible').text('');
        $('#strengthMeterWrap, #matchIndicator, #ajaxAlertBanner').hide();
        $('#crit-length, #crit-case, #crit-num').removeClass('is-valid');
    });

    // 6. Real-Time AJAX Form Submission
    $form.on('submit', function(e) {
        e.preventDefault();

        // Clear previous state
        $('.cp-input').removeClass('is-invalid');
        $('.cp-feedback').removeClass('is-visible').text('');
        $alertBanner.hide().removeClass('alert-success alert-danger').text('');

        var oldPass = $oldPw.val().trim();
        var newPass = $newPw.val();
        var confirmPass = $confirmPw.val();
        var hasClientError = false;

        if (!oldPass) {
            $oldPw.addClass('is-invalid');
            $('#error-old_password').text('Current password is required.').addClass('is-visible');
            hasClientError = true;
        }

        if (!newPass) {
            $newPw.addClass('is-invalid');
            $('#error-password').text('New password is required.').addClass('is-visible');
            hasClientError = true;
        } else if (newPass.length < 6) {
            $newPw.addClass('is-invalid');
            $('#error-password').text('New password must be at least 6 characters.').addClass('is-visible');
            hasClientError = true;
        } else if (newPass === oldPass) {
            $newPw.addClass('is-invalid');
            $('#error-password').text('New password must be different from current password.').addClass('is-visible');
            hasClientError = true;
        }

        if (!confirmPass) {
            $confirmPw.addClass('is-invalid');
            $('#error-password_confirmation').text('Please confirm your new password.').addClass('is-visible');
            hasClientError = true;
        } else if (newPass !== confirmPass) {
            $confirmPw.addClass('is-invalid');
            $('#error-password_confirmation').text('Passwords do not match.').addClass('is-visible');
            hasClientError = true;
        }

        if (hasClientError) return;

        // Button Loading State
        $btnSubmit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Updating...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btnSubmit.prop('disabled', false).html('<i class="fa fa-check-circle"></i> <span>Update Password</span>');
                
                // Reset form values
                $form[0].reset();
                $('#strengthMeterWrap, #matchIndicator').hide();
                $('#crit-length, #crit-case, #crit-num').removeClass('is-valid');

                // Trigger Theme-Based Success Modal
                if (res && res.message) {
                    $('#cpSuccessMessage').text(res.message);
                }
                $('#cpSuccessModal').modal('show');
            },
            error: function(xhr) {
                $btnSubmit.prop('disabled', false).html('<i class="fa fa-check-circle"></i> <span>Update Password</span>');

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, msgs) {
                        var input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        var msgText = Array.isArray(msgs) ? msgs[0] : msgs;
                        $('#error-' + field).text(msgText).addClass('is-visible');
                    });

                    // First error focus
                    $('.cp-input.is-invalid').first().focus();
                } else {
                    var errMessage = (xhr.responseJSON && xhr.responseJSON.message) 
                        ? xhr.responseJSON.message 
                        : 'Failed to update password. Please check your credentials and try again.';
                    
                    $alertBanner.addClass('alert-danger')
                        .html('<i class="fa fa-exclamation-triangle mr-1"></i> ' + errMessage)
                        .slideDown(200);
                }
            }
        });
    });
});
</script>
@endsection
