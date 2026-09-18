@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $selectedServices = array_filter(array_map('trim', explode(',', (string) ($setting->messaging_services ?? 'firebase'))));
    $timeValue = function ($value) {
        return !empty($value) ? substr((string) $value, 0, 5) : '';
    };
    $attendanceType = (int) old('attendance_type', $setting->attendance_type ?? 1);
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* 1. Mobile Hero Card */
.mob-settings-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-settings-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-settings-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-status-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* Quick Metrics in Hero */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-top: 6px;
}
.mob-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 6px;
    text-align: center;
}
.mob-kpi-tag {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-kpi-val {
    font-size: 12px;
    font-weight: 900;
    line-height: 1;
}

/* Mobile Settings Section Cards */
.mob-set-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.mob-set-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 10px;
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-set-body {
    padding: 10px;
}

/* Mode Options */
.mob-mode-opt {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    margin-bottom: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.mob-mode-opt.selected {
    background: #f0f7ff;
    border-color: #002C54;
}

/* Toggle Switch row */
.mob-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 0;
    border-bottom: 1px dashed #e2e8f0;
}
.mob-toggle-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.mob-toggle-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1px;
}
.mob-toggle-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}

/* Switches */
.switch-mob {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 20px;
    flex-shrink: 0;
}
.switch-mob input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider-mob {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 20px;
}
.slider-mob:before {
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
.switch-mob input:checked + .slider-mob {
    background-color: #002C54;
}
.switch-mob input:checked + .slider-mob:before {
    transform: translateX(16px);
}

/* Input Fields */
.mob-input {
    height: 32px;
    font-size: 12px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    padding: 2px 8px;
    color: #1e293b;
    width: 100%;
}

/* Save Button */
.mob-save-btn {
    width: 100%;
    height: 38px;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3f;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(0,44,84,0.3);
}
.mob-save-btn:hover {
    background: #0f3460;
    color: #ffffff;
}
</style>
@endsection

@section('content')
<div class="px-2 pt-2 pb-5">

    {{-- 1. Mobile Hero Banner --}}
    <div class="mob-settings-hero">
        <div class="mob-settings-top">
            <div class="mob-settings-title">
                <i class="fa fa-sliders"></i> Attendance Settings
            </div>
            <span class="mob-status-pill">
                @if($attendanceType === 1) Biometric @elseif($attendanceType === 2) Manual @else QR Scan @endif
            </span>
        </div>

        <div class="mob-kpi-grid">
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Engine</div>
                <div class="mob-kpi-val" style="color:#60a5fa;">
                    @if($attendanceType === 1) Bio @elseif($attendanceType === 2) Web @else Self @endif
                </div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Auto Absent</div>
                <div class="mob-kpi-val" style="color:#f87171;">
                    {{ old('auto_absent_mark_enabled', $setting->auto_absent_mark_enabled ?? 0) ? 'ON' : 'OFF' }}
                </div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Half-Day</div>
                <div class="mob-kpi-val" style="color:#fde047;">
                    {{ old('half_day_min_minutes', $setting->half_day_min_minutes ?? 240) }}m
                </div>
            </div>
        </div>
    </div>

    {{-- Flash message --}}
    @if(session('message'))
        <div class="alert alert-success py-2 px-3 mb-2" style="border-radius:3px; font-size:11.5px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
        </div>
    @endif
    @if(!empty($errors) && $errors->any())
        <div class="alert alert-danger py-2 px-3 mb-2" style="border-radius:3px; font-size:11.5px;">
            <i class="fa fa-exclamation-triangle mr-1"></i> <strong>Please check errors:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ url('attendance/settings') }}">
        @csrf

        {{-- Section 1: Capture Engine --}}
        <div class="mob-set-card">
            <div class="mob-set-header">
                <span><i class="fa fa-cubes mr-1"></i> Primary Capture Mode</span>
            </div>
            <div class="mob-set-body">
                <label class="mob-mode-opt {{ $attendanceType === 1 ? 'selected' : '' }}">
                    <input type="radio" name="attendance_type" value="1" {{ $attendanceType === 1 ? 'checked' : '' }} style="margin-right:6px;">
                    <div>
                        <div style="font-size:12px; font-weight:800; color:#0f172a;">Biometric (Machine)</div>
                        <div style="font-size:10px; color:#64748b;">Fingerprint / Facial recognition device sync</div>
                    </div>
                </label>

                <label class="mob-mode-opt {{ $attendanceType === 2 ? 'selected' : '' }}">
                    <input type="radio" name="attendance_type" value="2" {{ $attendanceType === 2 ? 'checked' : '' }} style="margin-right:6px;">
                    <div>
                        <div style="font-size:12px; font-weight:800; color:#0f172a;">Manual / Web Portal</div>
                        <div style="font-size:10px; color:#64748b;">Attendance register entry via admin/teacher</div>
                    </div>
                </label>

                <label class="mob-mode-opt {{ $attendanceType === 3 ? 'selected' : '' }}">
                    <input type="radio" name="attendance_type" value="3" {{ $attendanceType === 3 ? 'checked' : '' }} style="margin-right:6px;">
                    <div>
                        <div style="font-size:12px; font-weight:800; color:#0f172a;">Self Attendance (QR Scan)</div>
                        <div style="font-size:10px; color:#64748b;">Mobile camera QR scanning</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Section 2: Notifications & Messaging --}}
        <div class="mob-set-card">
            <div class="mob-set-header">
                <span><i class="fa fa-bell-o mr-1"></i> Alert Dispatchers</span>
            </div>
            <div class="mob-set-body">
                <div class="d-flex flex-wrap" style="gap:6px; margin-bottom:8px;">
                    <label class="btn btn-xs btn-outline-primary" style="font-size:11px; border-radius:3px; display:inline-flex; align-items:center; gap:4px;">
                        <input type="checkbox" name="messaging_services[]" value="firebase" {{ in_array('firebase', old('messaging_services', $selectedServices), true) ? 'checked' : '' }}>
                        Push Alert
                    </label>
                    <label class="btn btn-xs btn-outline-success" style="font-size:11px; border-radius:3px; display:inline-flex; align-items:center; gap:4px;">
                        <input type="checkbox" name="messaging_services[]" value="whatsapp" {{ in_array('whatsapp', old('messaging_services', $selectedServices), true) ? 'checked' : '' }}>
                        WhatsApp
                    </label>
                    <label class="btn btn-xs btn-outline-secondary" style="font-size:11px; border-radius:3px; display:inline-flex; align-items:center; gap:4px;">
                        <input type="checkbox" name="messaging_services[]" value="sms" {{ in_array('sms', old('messaging_services', $selectedServices), true) ? 'checked' : '' }}>
                        SMS Gateway
                    </label>
                </div>

                <div class="mob-toggle-row">
                    <div>
                        <div class="mob-toggle-label">Manual Attendance Alerts</div>
                        <p class="mob-toggle-desc">Send alert on web portal entries</p>
                    </div>
                    <label class="switch-mob">
                        <input type="checkbox" name="manual_attendance_messaging_enabled" value="1" {{ old('manual_attendance_messaging_enabled', $setting->manual_attendance_messaging_enabled ?? 0) ? 'checked' : '' }}>
                        <span class="slider-mob"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Section 3: Automation & Timing Cutoffs --}}
        <div class="mob-set-card">
            <div class="mob-set-header">
                <span><i class="fa fa-magic mr-1"></i> Automation &amp; Timing Rules</span>
            </div>
            <div class="mob-set-body">
                {{-- Auto Absent --}}
                <div class="mob-toggle-row mb-2">
                    <div>
                        <div class="mob-toggle-label">Auto Absent Marking</div>
                        <p class="mob-toggle-desc">Auto-mark unpunched as absent</p>
                    </div>
                    <label class="switch-mob">
                        <input type="checkbox" name="auto_absent_mark_enabled" value="1" {{ old('auto_absent_mark_enabled', $setting->auto_absent_mark_enabled ?? 0) ? 'checked' : '' }}>
                        <span class="slider-mob"></span>
                    </label>
                </div>

                <div class="form-group mb-2">
                    <label style="font-size:11px; font-weight:700; color:#475569; margin-bottom:2px;">Auto Absent Time</label>
                    <input type="time" name="auto_absent_mark_time" class="mob-input" value="{{ old('auto_absent_mark_time', $timeValue($setting->auto_absent_mark_time ?? null)) }}">
                </div>

                {{-- Back date --}}
                <div class="mob-toggle-row mb-2">
                    <div>
                        <div class="mob-toggle-label">Allow Back-Date Entry</div>
                        <p class="mob-toggle-desc">Enable previous dates editing</p>
                    </div>
                    <label class="switch-mob">
                        <input type="checkbox" name="allow_back_date_attendance" value="1" {{ old('allow_back_date_attendance', $setting->allow_back_date_attendance ?? 0) ? 'checked' : '' }}>
                        <span class="slider-mob"></span>
                    </label>
                </div>

                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label style="font-size:11px; font-weight:700; color:#475569; margin-bottom:2px;">Half-Day (Mins)</label>
                        <input type="number" min="0" max="9999" name="half_day_min_minutes" class="mob-input" value="{{ old('half_day_min_minutes', $setting->half_day_min_minutes ?? 240) }}" required>
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label style="font-size:11px; font-weight:700; color:#475569; margin-bottom:2px;">QR Validity (Mins)</label>
                        <input type="number" min="0" max="9999" name="qr_validity_minutes" class="mob-input" value="{{ old('qr_validity_minutes', $setting->qr_validity_minutes ?? 5) }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Lunch & Season Checkout Timings --}}
        <div class="mob-set-card">
            <div class="mob-set-header">
                <span><i class="fa fa-cutlery mr-1"></i> Lunch Break Windows</span>
            </div>
            <div class="mob-set-body">
                <div class="row">
                    <div class="col-6 form-group mb-2">
                        <label style="font-size:10.5px; font-weight:700; color:#d97706; margin-bottom:2px;">Summer From</label>
                        <input type="time" name="summer_lunch_from_time" class="mob-input" value="{{ old('summer_lunch_from_time', $timeValue($setting->summer_lunch_from_time ?? null)) }}">
                    </div>
                    <div class="col-6 form-group mb-2">
                        <label style="font-size:10.5px; font-weight:700; color:#d97706; margin-bottom:2px;">Summer To</label>
                        <input type="time" name="summer_lunch_to_time" class="mob-input" value="{{ old('summer_lunch_to_time', $timeValue($setting->summer_lunch_to_time ?? null)) }}">
                    </div>
                    <div class="col-6 form-group mb-0">
                        <label style="font-size:10.5px; font-weight:700; color:#0284c7; margin-bottom:2px;">Winter From</label>
                        <input type="time" name="winter_lunch_from_time" class="mob-input" value="{{ old('winter_lunch_from_time', $timeValue($setting->winter_lunch_from_time ?? null)) }}">
                    </div>
                    <div class="col-6 form-group mb-0">
                        <label style="font-size:10.5px; font-weight:700; color:#0284c7; margin-bottom:2px;">Winter To</label>
                        <input type="time" name="winter_lunch_to_time" class="mob-input" value="{{ old('winter_lunch_to_time', $timeValue($setting->winter_lunch_to_time ?? null)) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Policy Remarks --}}
        <div class="mob-set-card">
            <div class="mob-set-header">
                <span><i class="fa fa-sticky-note-o mr-1"></i> Policy Notes</span>
            </div>
            <div class="mob-set-body">
                <textarea name="notes" rows="2" maxlength="1000" class="mob-input" style="height:auto;" placeholder="Enter internal guidelines or notes...">{{ old('notes', $setting->notes ?? '') }}</textarea>
            </div>
        </div>

        {{-- Save Button --}}
        <button type="submit" class="mob-save-btn mt-2">
            <i class="fa fa-save"></i> Save Attendance Settings
        </button>
    </form>

</div>

@section('scripts')
<script>
    $(document).ready(function(){
        $('input[name="attendance_type"]').on('change', function(){
            $('.mob-mode-opt').removeClass('selected');
            $(this).closest('.mob-mode-opt').addClass('selected');
        });
    });
</script>
@endsection
@endsection