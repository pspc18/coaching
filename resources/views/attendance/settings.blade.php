@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ATTENDANCE SETTINGS (SIGNATURE THEME)
   Aligned with editSetting, viewUser, and Arise ERP design guidelines:
   - Font family: Segoe UI, -apple-system, Roboto, sans-serif
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Compact 29px-32px inputs with #cbd5e1 border
   - High performance, responsive layout
   ========================================================================== */

.settings-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - 56px);
}
.settings-page-wrapper * {
    box-sizing: border-box;
}

/* Top Hero Banner */
.settings-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 7px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 10px;
}
.settings-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.settings-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.settings-subtitle {
    font-size: 11px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.settings-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 11.5px;
    font-weight: 600;
    text-decoration: none !important;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid transparent;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3f;
}
.dash-btn-outline {
    background: rgba(255,255,255,0.12);
    color: #ffffff;
    border-color: rgba(255,255,255,0.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.22);
    color: #ffffff;
}

/* Quick KPI / Summary Strip */
.settings-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}
@media (max-width: 992px) {
    .settings-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
.kpi-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 7px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.kpi-box-title {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}
.kpi-box-val {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
}

/* Card Sections */
.settings-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    overflow: hidden;
}
.settings-card-header {
    background: #002C54;
    color: #ffffff;
    padding: 7px 12px;
    font-size: 12.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.settings-card-header .header-badge {
    background: rgba(255,255,255,0.18);
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,0.25);
}
.settings-card-body {
    padding: 12px;
}

/* Custom Interactive Radio Cards */
.engine-radio-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
@media (max-width: 768px) {
    .engine-radio-grid {
        grid-template-columns: 1fr;
    }
}
.engine-radio-label {
    position: relative;
    display: block;
    margin-bottom: 0;
    cursor: pointer;
}
.engine-radio-label input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.engine-radio-card {
    border: 1.5px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 10px;
    background: #f8fafc;
    transition: all 0.15s ease;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    height: 100%;
}
.engine-radio-label:hover .engine-radio-card {
    border-color: #94a3b8;
    background: #f1f5f9;
}
.engine-radio-label input:checked + .engine-radio-card {
    border-color: #002C54;
    background: #f0f7ff;
    box-shadow: 0 0 0 1px #002C54;
}
.engine-icon {
    width: 28px;
    height: 28px;
    border-radius: 2px;
    background: #e2e8f0;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.engine-radio-label input:checked + .engine-radio-card .engine-icon {
    background: #002C54;
    color: #ffffff;
}

/* Form Controls */
.form-group label {
    font-size: 11.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.form-control-compact {
    height: 30px !important;
    font-size: 12px !important;
    border-radius: 2px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 2px 8px !important;
    color: #1e293b;
}
.form-control-compact:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12) !important;
}

/* Custom Checkbox / Switch Badges */
.service-pill-box {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.service-check-label {
    margin-bottom: 0;
    cursor: pointer;
}
.service-check-label input {
    display: none;
}
.service-check-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    color: #475569;
    transition: all 0.15s ease;
}
.service-check-label input:checked + .service-check-pill {
    background: #002C54;
    border-color: #001f3f;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0,44,84,0.18);
}
.service-check-label input:checked + .service-check-pill.service-whatsapp {
    background: #15803d;
    border-color: #166534;
}

/* iOS-style toggle switches */
.toggle-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    margin-bottom: 6px;
}
.toggle-title {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1px;
}
.toggle-desc {
    font-size: 10.5px;
    color: #64748b;
    margin: 0;
}
.switch-box {
    position: relative;
    display: inline-block;
    width: 38px;
    height: 20px;
    flex-shrink: 0;
    margin-left: 10px;
}
.switch-box input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider-round {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 20px;
}
.slider-round:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.switch-box input:checked + .slider-round {
    background-color: #002C54;
}
.switch-box input:checked + .slider-round:before {
    transform: translateX(18px);
}

/* Time Input Addon */
.time-input-wrap {
    display: flex;
    align-items: center;
}
.time-input-wrap .time-addon {
    height: 30px;
    padding: 0 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-right: none;
    border-radius: 2px 0 0 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 11px;
}
.time-input-wrap .form-control-compact {
    border-radius: 0 2px 2px 0 !important;
}

/* Bottom Action Bar */
.bottom-action-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.btn-save-settings {
    height: 32px;
    padding: 0 18px;
    font-size: 12.5px;
    font-weight: 700;
    border-radius: 2px;
    background: #002C54;
    border: 1px solid #001f3f;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
}
.btn-save-settings:hover {
    background: #0f3460;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0,44,84,0.25);
}
</style>
@endsection

@section('content')

@php
    $selectedServices = array_filter(array_map('trim', explode(',', (string) ($setting->messaging_services ?? 'firebase'))));
    $timeValue = function ($value) {
        return !empty($value) ? substr((string) $value, 0, 5) : '';
    };
    $attendanceType = (int) old('attendance_type', $setting->attendance_type ?? 1);
@endphp

<div class="content-wrapper settings-page-wrapper">
    
    {{-- 1. Signature Hero Banner --}}
    <div class="settings-hero">
        <div class="settings-hero-text">
            <span class="settings-kicker"><i class="fa fa-cogs mr-1"></i> System Configuration</span>
            <h1 class="settings-title">
                <i class="fa fa-sliders"></i> Attendance Engine Settings
            </h1>
            <p class="settings-subtitle">
                Configure biometric & mobile sync rules, auto-absent cutoffs, lunch break windows, and alert dispatchers.
            </p>
        </div>
        <div class="settings-hero-actions">
            <a href="{{ url('attendance/marking-window') }}" class="dash-btn dash-btn-outline" title="Manage Marking Windows">
                <i class="fa fa-clock-o"></i> Marking Window
            </a>
            <a href="{{ url('attendance/report') }}" class="dash-btn dash-btn-outline" title="View Reports">
                <i class="fa fa-bar-chart"></i> Report
            </a>
            <a href="{{ url('monthlyReport') }}" class="dash-btn dash-btn-outline" title="Monthly Attendance Matrix">
                <i class="fa fa-th"></i> Monthly Matrix
            </a>
            <a href="{{ url('attendance/view') }}" class="dash-btn dash-btn-light" title="Attendance Master View">
                <i class="fa fa-calendar"></i> Attendance View
            </a>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
            <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(!empty($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-exclamation-triangle mr-1"></i> <strong>Please resolve the following errors:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- 2. KPI Summary Strip --}}
    <div class="settings-kpi-strip">
        <div class="kpi-box">
            <div>
                <div class="kpi-box-title">Active Engine</div>
                <div class="kpi-box-val text-primary" id="kpiActiveEngine">
                    @if($attendanceType === 1) Biometric Engine @elseif($attendanceType === 2) Web / Manual @else Self Attendance @endif
                </div>
            </div>
            <div style="font-size:18px; color:#002C54;"><i class="fa fa-microchip"></i></div>
        </div>
        <div class="kpi-box">
            <div>
                <div class="kpi-box-title">Auto Absent Cutoff</div>
                <div class="kpi-box-val text-danger">
                    {{ old('auto_absent_mark_enabled', $setting->auto_absent_mark_enabled ?? 0) ? ($timeValue($setting->auto_absent_mark_time ?? null) ?: 'Enabled') : 'Disabled' }}
                </div>
            </div>
            <div style="font-size:18px; color:#ef4444;"><i class="fa fa-bell-slash-o"></i></div>
        </div>
        <div class="kpi-box">
            <div>
                <div class="kpi-box-title">Half-Day Threshold</div>
                <div class="kpi-box-val text-warning">
                    {{ old('half_day_min_minutes', $setting->half_day_min_minutes ?? 240) }} mins ({{ round((old('half_day_min_minutes', $setting->half_day_min_minutes ?? 240) / 60), 1) }} hrs)
                </div>
            </div>
            <div style="font-size:18px; color:#f59e0b;"><i class="fa fa-hourglass-half"></i></div>
        </div>
        <div class="kpi-box">
            <div>
                <div class="kpi-box-title">QR Validity</div>
                <div class="kpi-box-val text-success">
                    {{ old('qr_validity_minutes', $setting->qr_validity_minutes ?? 5) }} Minutes
                </div>
            </div>
            <div style="font-size:18px; color:#10b981;"><i class="fa fa-qrcode"></i></div>
        </div>
    </div>

    {{-- 3. Main Form --}}
    <form method="post" action="{{ url('attendance/settings') }}" id="attendanceSettingsForm">
        @csrf

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-6">
                
                {{-- Card 1: Attendance Mode Engine --}}
                <div class="settings-card">
                    <div class="settings-card-header">
                        <span><i class="fa fa-cubes mr-1"></i> Attendance Capture Engine</span>
                        <span class="header-badge">Core Engine</span>
                    </div>
                    <div class="settings-card-body">
                        <label style="font-size:11.5px; font-weight:700; color:#334155; margin-bottom:6px;">Select Primary Mode</label>
                        <div class="engine-radio-grid mb-3">
                            {{-- Biometric --}}
                            <label class="engine-radio-label">
                                <input type="radio" name="attendance_type" value="1" {{ $attendanceType === 1 ? 'checked' : '' }} onchange="updateEngineKpi('Biometric Engine')">
                                <div class="engine-radio-card">
                                    <div class="engine-icon"><i class="fa fa-fingerprint fa-lg"></i></div>
                                    <div>
                                        <div style="font-size:12px; font-weight:800; color:#0f172a;">Biometric</div>
                                        <div style="font-size:10px; color:#64748b; line-height:1.2;">Thumb / Face device sync</div>
                                    </div>
                                </div>
                            </label>

                            {{-- Normal / Manual --}}
                            <label class="engine-radio-label">
                                <input type="radio" name="attendance_type" value="2" {{ $attendanceType === 2 ? 'checked' : '' }} onchange="updateEngineKpi('Web / Manual')">
                                <div class="engine-radio-card">
                                    <div class="engine-icon"><i class="fa fa-pencil-square-o fa-lg"></i></div>
                                    <div>
                                        <div style="font-size:12px; font-weight:800; color:#0f172a;">Manual / Web</div>
                                        <div style="font-size:10px; color:#64748b; line-height:1.2;">Portal marking sheet</div>
                                    </div>
                                </div>
                            </label>

                            {{-- Self Attendance --}}
                            <label class="engine-radio-label">
                                <input type="radio" name="attendance_type" value="3" {{ $attendanceType === 3 ? 'checked' : '' }} onchange="updateEngineKpi('Self Attendance')">
                                <div class="engine-radio-card">
                                    <div class="engine-icon"><i class="fa fa-qrcode fa-lg"></i></div>
                                    <div>
                                        <div style="font-size:12px; font-weight:800; color:#0f172a;">Self / QR</div>
                                        <div style="font-size:10px; color:#64748b; line-height:1.2;">Staff mobile QR scan</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- Messaging Services --}}
                        <div class="form-group mb-2">
                            <label><i class="fa fa-paper-plane text-primary"></i> Notification Dispatch Services</label>
                            <div class="service-pill-box">
                                <label class="service-check-label">
                                    <input type="checkbox" name="messaging_services[]" value="firebase" {{ in_array('firebase', old('messaging_services', $selectedServices), true) ? 'checked' : '' }}>
                                    <span class="service-check-pill"><i class="fa fa-bell"></i> Firebase Push</span>
                                </label>
                                <label class="service-check-label">
                                    <input type="checkbox" name="messaging_services[]" value="whatsapp" {{ in_array('whatsapp', old('messaging_services', $selectedServices), true) ? 'checked' : '' }}>
                                    <span class="service-check-pill service-whatsapp"><i class="fa fa-whatsapp"></i> WhatsApp API</span>
                                </label>
                                <label class="service-check-label">
                                    <input type="checkbox" name="messaging_services[]" value="sms" {{ in_array('sms', old('messaging_services', $selectedServices), true) ? 'checked' : '' }}>
                                    <span class="service-check-pill"><i class="fa fa-commenting-o"></i> SMS Gateway</span>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size:10.5px;">
                                <i class="fa fa-info-circle mr-1"></i> Android push alerts trigger channel keys: <code>attendance_in</code>, <code>attendance_out</code> &amp; <code>attendance_absent_v2</code>.
                            </small>
                        </div>

                        {{-- Manual messaging toggle --}}
                        <div class="toggle-item mt-2">
                            <div>
                                <div class="toggle-title"><i class="fa fa-envelope-o mr-1"></i> Manual Marking Notification</div>
                                <p class="toggle-desc">Automatically dispatch SMS/Push when attendance is filled manually via web portal.</p>
                            </div>
                            <label class="switch-box">
                                <input type="checkbox" name="manual_attendance_messaging_enabled" value="1" {{ old('manual_attendance_messaging_enabled', $setting->manual_attendance_messaging_enabled ?? 0) ? 'checked' : '' }}>
                                <span class="slider-round"></span>
                            </label>
                        </div>

                    </div>
                </div>

                {{-- Card 2: Automation & Security Rules --}}
                <div class="settings-card">
                    <div class="settings-card-header">
                        <span><i class="fa fa-magic mr-1"></i> Automation &amp; Back-Date Rules</span>
                        <span class="header-badge">Automation</span>
                    </div>
                    <div class="settings-card-body">
                        
                        {{-- Auto Absent Toggle & Cutoff Time --}}
                        <div class="toggle-item">
                            <div>
                                <div class="toggle-title"><i class="fa fa-user-times mr-1 text-danger"></i> Automatic Absent Marking</div>
                                <p class="toggle-desc">Automatically mark un-punched members as absent at the specified time.</p>
                            </div>
                            <label class="switch-box">
                                <input type="checkbox" id="toggleAutoAbsent" name="auto_absent_mark_enabled" value="1" {{ old('auto_absent_mark_enabled', $setting->auto_absent_mark_enabled ?? 0) ? 'checked' : '' }}>
                                <span class="slider-round"></span>
                            </label>
                        </div>

                        <div class="form-group mt-2" id="autoAbsentTimeGroup">
                            <label><i class="fa fa-clock-o text-danger"></i> Auto Absent Execution Time (24-Hour)</label>
                            <div class="time-input-wrap" style="max-width:240px;">
                                <span class="time-addon"><i class="fa fa-clock-o"></i></span>
                                <input type="time" name="auto_absent_mark_time" class="form-control form-control-compact"
                                       value="{{ old('auto_absent_mark_time', $timeValue($setting->auto_absent_mark_time ?? null)) }}">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size:10.5px;">Members without IN punches by this time will be marked absent by the cron system.</small>
                        </div>

                        {{-- Back-date attendance toggle --}}
                        <div class="toggle-item mt-3">
                            <div>
                                <div class="toggle-title"><i class="fa fa-history mr-1 text-warning"></i> Allow Back-Date Attendance</div>
                                <p class="toggle-desc">Permits authorized teachers &amp; admins to edit attendance for past dates.</p>
                            </div>
                            <label class="switch-box">
                                <input type="checkbox" name="allow_back_date_attendance" value="1" {{ old('allow_back_date_attendance', $setting->allow_back_date_attendance ?? 0) ? 'checked' : '' }}>
                                <span class="slider-round"></span>
                            </label>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-lg-6">
                
                {{-- Card 3: Attendance Thresholds & Timing Parameters --}}
                <div class="settings-card">
                    <div class="settings-card-header">
                        <span><i class="fa fa-sliders mr-1"></i> Thresholds &amp; Duration Rules</span>
                        <span class="header-badge">Timing Rules</span>
                    </div>
                    <div class="settings-card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><i class="fa fa-hourglass-half text-warning"></i> Half-Day Minimum</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="9999" name="half_day_min_minutes" id="inputHalfDayMins" class="form-control form-control-compact" required
                                           value="{{ old('half_day_min_minutes', $setting->half_day_min_minutes ?? 240) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="font-size:11px; border-radius:0 2px 2px 0; background:#f1f5f9; border-color:#cbd5e1;">Mins</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1" id="halfDayHrsHint" style="font-size:10.5px;">= 4.0 Hours</small>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label><i class="fa fa-qrcode text-success"></i> Dynamic QR Validity</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="9999" name="qr_validity_minutes" class="form-control form-control-compact" required
                                           value="{{ old('qr_validity_minutes', $setting->qr_validity_minutes ?? 5) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="font-size:11px; border-radius:0 2px 2px 0; background:#f1f5f9; border-color:#cbd5e1;">Mins</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size:10.5px;">Self-attendance dynamic QR expiration window.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Lunch & Season Checkout Timings --}}
                <div class="settings-card">
                    <div class="settings-card-header">
                        <span><i class="fa fa-cutlery mr-1"></i> Lunch &amp; Break Checkout Windows</span>
                        <span class="header-badge">Break Windows</span>
                    </div>
                    <div class="settings-card-body">
                        <div class="row">
                            {{-- Summer Lunch --}}
                            <div class="col-sm-6 form-group">
                                <label><i class="fa fa-sun-o text-warning"></i> Summer Lunch From</label>
                                <div class="time-input-wrap">
                                    <span class="time-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="time" name="summer_lunch_from_time" class="form-control form-control-compact"
                                           value="{{ old('summer_lunch_from_time', $timeValue($setting->summer_lunch_from_time ?? null)) }}">
                                </div>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><i class="fa fa-sun-o text-warning"></i> Summer Lunch To</label>
                                <div class="time-input-wrap">
                                    <span class="time-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="time" name="summer_lunch_to_time" class="form-control form-control-compact"
                                           value="{{ old('summer_lunch_to_time', $timeValue($setting->summer_lunch_to_time ?? null)) }}">
                                </div>
                            </div>

                            {{-- Winter Lunch --}}
                            <div class="col-sm-6 form-group mb-0">
                                <label><i class="fa fa-snowflake-o text-info"></i> Winter Lunch From</label>
                                <div class="time-input-wrap">
                                    <span class="time-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="time" name="winter_lunch_from_time" class="form-control form-control-compact"
                                           value="{{ old('winter_lunch_from_time', $timeValue($setting->winter_lunch_from_time ?? null)) }}">
                                </div>
                            </div>
                            <div class="col-sm-6 form-group mb-0">
                                <label><i class="fa fa-snowflake-o text-info"></i> Winter Lunch To</label>
                                <div class="time-input-wrap">
                                    <span class="time-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="time" name="winter_lunch_to_time" class="form-control form-control-compact"
                                           value="{{ old('winter_lunch_to_time', $timeValue($setting->winter_lunch_to_time ?? null)) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Operational Notes & Guidelines --}}
                <div class="settings-card">
                    <div class="settings-card-header">
                        <span><i class="fa fa-sticky-note-o mr-1"></i> Policy Notes &amp; Remarks</span>
                        <span class="header-badge">Documentation</span>
                    </div>
                    <div class="settings-card-body">
                        <div class="form-group mb-0">
                            <textarea name="notes" rows="3" maxlength="1000" id="settingNotesInput" class="form-control" style="font-size:12px; border-radius:2px; border-color:#cbd5e1;" placeholder="Enter internal remarks, policy notes, or exception guidelines...">{{ old('notes', $setting->notes ?? '') }}</textarea>
                            <div class="d-flex justify-content-between mt-1 text-muted" style="font-size:10px;">
                                <span>Shown to administrators when editing attendance policies.</span>
                                <span><span id="charCount">0</span> / 1000</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- 4. Bottom Sticky Action Card --}}
        <div class="bottom-action-card mt-2">
            <div class="d-flex align-items-center" style="gap:8px;">
                <span class="badge badge-light border text-muted" style="font-size:11px; padding:5px 8px; border-radius:2px;">
                    <i class="fa fa-shield text-success mr-1"></i> Changes apply immediately to all attendance calculations
                </span>
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <a href="{{ url('attendance/settings') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:2px; font-size:12px; height:32px; display:inline-flex; align-items:center;">
                    <i class="fa fa-refresh mr-1"></i> Reset
                </a>
                <button type="submit" class="btn-save-settings">
                    <i class="fa fa-save"></i> Save Settings
                </button>
            </div>
        </div>

    </form>
</div>

<script>
    function updateEngineKpi(name) {
        document.getElementById('kpiActiveEngine').innerText = name;
    }

    document.addEventListener('DOMContentLoaded', function(){
        // Half Day Hours preview
        var halfDayInput = document.getElementById('inputHalfDayMins');
        var halfDayHint = document.getElementById('halfDayHrsHint');
        if (halfDayInput && halfDayHint) {
            function updateHours() {
                var mins = parseFloat(halfDayInput.value) || 0;
                var hrs = (mins / 60).toFixed(1);
                halfDayHint.innerText = '= ' + hrs + ' Hours';
            }
            halfDayInput.addEventListener('input', updateHours);
            updateHours();
        }

        // Notes Character Counter
        var notesInput = document.getElementById('settingNotesInput');
        var charCount = document.getElementById('charCount');
        if (notesInput && charCount) {
            function updateCount() {
                charCount.innerText = notesInput.value.length;
            }
            notesInput.addEventListener('input', updateCount);
            updateCount();
        }
    });
</script>
@endsection