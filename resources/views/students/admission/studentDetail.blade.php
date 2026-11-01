@extends('layout.app')

@section('content')
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
   ARISE ERP - STUDENT PROFILE DOSSIER (DESKTOP)
   Standardized with Arise Theme (arise-theme.css & admin_dashboard)
   Executive Contrast, Sharp-Edge (3px-4px), Balanced Spacing & Ultra-High Speed
   ============================================================================== */
.student-profile-page {
    background: #f8fafc;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12.5px;
    padding: 10px 12px 24px;
}
.student-profile-page * {
    box-sizing: border-box;
}
.student-profile-page .container-fluid {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* 1. Top Hero Banner (Arise Navy Executive Standard) */
.profile-dash-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.16);
    margin-bottom: 10px;
}
.profile-hero-info {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}
.profile-hero-avatar-icon {
    width: 38px;
    height: 38px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #38bdf8;
    flex-shrink: 0;
}
.profile-hero-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #93c5fd;
    display: block;
    margin-bottom: 2px;
    font-weight: 700;
}
.profile-hero-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.profile-hero-subtitle {
    font-size: 11.5px;
    color: #cbd5e1;
    margin: 3px 0 0 0;
}
.profile-hero-subtitle b {
    color: #ffffff;
}
.profile-hero-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
}

/* Hero Action Buttons - High Contrast ERP Standard */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 5px 11px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 3px;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    line-height: 1.3;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff !important;
    color: #002C54 !important;
    border: 1px solid #ffffff !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}
.dash-btn-light:hover {
    background: #f1f5f9 !important;
    color: #001f3d !important;
    transform: translateY(-1px);
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.14) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
    transform: translateY(-1px);
}

/* 2. Top Grid Layout & Spacing */
.profile-row-compact {
    margin-left: -5px;
    margin-right: -5px;
}
.profile-row-compact > [class*="col-"] {
    padding-left: 5px;
    padding-right: 5px;
    margin-bottom: 10px !important;
}

/* Sharp Card Standard */
.dash-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}
.dash-card-header {
    padding: 8px 12px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dash-card-title {
    font-size: 13px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 7px;
}
.dash-card-body {
    padding: 10px 12px;
    flex: 1;
}

/* Sharp KPI Metric Tiles (Matched exactly with admin_dashboard) */
.dash-kpi {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    text-decoration: none !important;
    color: #0f172a !important;
    height: 100%;
    transition: border-color .15s, box-shadow .15s;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.dash-kpi:hover {
    border-color: #cbd5e1;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
}
.kpi-primary { border-top: 3px solid #002C54; }
.kpi-success { border-top: 3px solid #10b981; }
.kpi-warning { border-top: 3px solid #f59e0b; }
.kpi-info    { border-top: 3px solid #0284c7; }
.kpi-danger  { border-top: 3px solid #ef4444; }
.kpi-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3px;
}
.kpi-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: .04em;
}
.kpi-icon {
    font-size: 15px;
    color: #94a3b8;
}
.kpi-primary .kpi-icon { color: #002C54; }
.kpi-success .kpi-icon { color: #10b981; }
.kpi-warning .kpi-icon { color: #f59e0b; }
.kpi-info .kpi-icon    { color: #0284c7; }
.kpi-danger .kpi-icon  { color: #ef4444; }
.kpi-value {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.2;
    color: #0f172a;
    margin-bottom: 3px;
}
.kpi-meta {
    font-size: 11px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-progress {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 6px;
}
.kpi-progress-bar {
    height: 100%;
    border-radius: 3px;
}

/* Left Spotlight Card (Photo, Status, Portal Credentials) */
.profile-avatar-box {
    position: relative;
    width: 100%;
    height: 165px;
    border-radius: 4px;
    overflow: hidden;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    margin-bottom: 10px;
}
.profile-avatar-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.profile-avatar-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 44, 84, 0.92), transparent);
    padding: 8px 6px 6px;
    display: flex;
    justify-content: center;
    gap: 8px;
}
.avatar-action-btn {
    background: #ffffff;
    border: none;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    padding: 3px 10px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    transition: all .15s ease;
}
.avatar-action-btn:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}
.avatar-action-btn.btn-del-photo {
    color: #ef4444;
}

/* Portal Login Mini Strip */
.portal-cred-strip {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.15);
}
.portal-cred-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    padding: 3px 0;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.2);
}
.portal-cred-row:last-child {
    border-bottom: none;
}
.portal-cred-label {
    color: #93c5fd;
    font-size: 10.5px;
    font-weight: 700;
}
.portal-cred-val {
    font-family: 'Consolas', 'Courier New', monospace;
    font-weight: 700;
    color: #ffffff;
    font-size: 12px;
}

/* Key-Value Matrix Table */
.matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.matrix-table th {
    width: 18%;
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    padding: 7px 10px;
    border: 1px solid #e2e8f0;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.matrix-table td {
    width: 32%;
    padding: 7px 10px;
    border: 1px solid #e2e8f0;
    color: #0f172a;
    font-weight: 600;
}

/* 3. Sharp Tab Navigation Bar */
.profile-tabs-bar {
    display: flex;
    gap: 4px;
    background: #ffffff;
    padding: 4px;
    border-radius: 4px;
    margin-bottom: 10px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}
.profile-tab-link {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 3px;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
}
.profile-tab-link:hover {
    color: #002C54;
    background: #f8fafc;
    border-color: #cbd5e1;
}
.profile-tab-link.active {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
    box-shadow: 0 2px 4px rgba(0, 44, 84, 0.2);
}
.profile-tab-badge {
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 3px;
    background: #e2e8f0;
    color: #334155;
}
.profile-tab-link.active .profile-tab-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* Tab Panes */
.profile-tab-content-pane {
    display: none;
}
.profile-tab-content-pane.active {
    display: block;
}

/* Standard Arise Table */
.arise-profile-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.arise-profile-table thead th {
    background: #002C54;
    color: #ffffff !important;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 9px 12px;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
}
.arise-profile-table tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #0f172a;
}
.arise-profile-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.arise-profile-table tbody tr:hover {
    background: #f1f5f9;
}
.arise-profile-table tfoot td {
    padding: 9px 12px;
    font-weight: 700;
    border-top: 2px solid #cbd5e1;
    background: #f1f5f9;
    color: #0f172a;
}

/* Uniform & Inventory Boxes */
.inventory-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 10px 12px;
    height: 100%;
}
.inventory-tile-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 3px;
}
.inventory-tile-value {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
}
</style>
@endsection

<div class="content-wrapper student-profile-page">
    <div class="container-fluid p-0">

        <!-- 1. TOP HERO BANNER (Arise Navy Executive Standard) -->
        <div class="profile-dash-hero">
            <div class="profile-hero-info">
                <div class="profile-hero-avatar-icon">
                    <i class="fa fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="profile-hero-kicker">
                        <i class="fa fa-users mr-1"></i> Student Profile Dossier · ADM: {{ $student->admissionNo ?? '-' }}
                    </span>
                    <h1 class="profile-hero-title">
                        {{ $studentName ?: 'Student Profile' }}
                        <span id="studentStatusBadge" class="badge {{ $isActive ? 'badge-success' : 'badge-danger' }}" style="font-size:10px; padding:3px 8px; cursor:pointer; font-weight:700; letter-spacing:0.04em;" onclick="toggleStudentStatus()" title="Click to change active/inactive status">
                            {{ $isActive ? 'ACTIVE' : 'INACTIVE' }} <i class="fa fa-pencil ml-1" style="font-size:9px;"></i>
                        </span>
                    </h1>
                    <div class="profile-hero-subtitle">
                        <span>Class: <b>{{ $student->class_name ?? 'N/A' }}</b></span>
                        <span class="mx-1">•</span>
                        <span>Session: <b>{{ $sessionLabel ?: 'Current' }}</b></span>
                        @if(!empty($student->roll_no))
                            <span class="mx-1">•</span>
                            <span>Roll: <b>{{ $student->roll_no }}</b></span>
                        @endif
                        <span class="mx-1">•</span>
                        <span>Bio/Sys ID: <b>{{ $student->attendance_unique_id ?: ($student->unique_system_id ?: '-') }}</b></span>
                    </div>
                </div>
            </div>

            <div class="profile-hero-actions">
                <a href="{{ url('admissionView?from_profile=1') }}" class="dash-btn dash-btn-outline" title="Back to Students Directory">
                    <i class="fa fa-arrow-left mr-1"></i> Back
                </a>
                @if(!empty($fatherContact))
                    <a href="tel:{{ $fatherContact }}" class="dash-btn dash-btn-light" title="Call Parent">
                        <i class="fa fa-phone mr-1"></i> Call
                    </a>
                    <a href="https://api.whatsapp.com/send?phone=91{{ $fatherContact }}&text=Hello%20{{ urlencode($studentName) }}" target="_blank" class="dash-btn dash-btn-outline" title="WhatsApp Parent">
                        <i class="fa fa-whatsapp text-success mr-1"></i> WhatsApp
                    </a>
                @endif
                <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo) }}" class="dash-btn dash-btn-outline" title="Collect Fees">
                    <i class="fa fa-inr text-warning mr-1"></i> Collect Fees
                </a>
                <a href="{{ url('admissionStudentIdPrint/' . $student->id) }}" target="_blank" class="dash-btn dash-btn-outline" title="Print Student ID Card">
                    <i class="fa fa-id-card-o mr-1"></i> ID Card
                </a>
                <button type="button" onclick="window.print()" class="dash-btn dash-btn-outline" title="Print Dossier">
                    <i class="fa fa-print mr-1"></i> Print
                </button>
                <a href="{{ url('admissionEdit/' . $student->id) }}" class="dash-btn dash-btn-outline" title="Edit Admission Record">
                    <i class="fa fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('student.profile.excel', $student->id) }}" class="dash-btn dash-btn-light {{ Helper::permissioncheck(3)->print ? '' : 'd-none' }}" title="Download Excel Dossier">
                    <i class="fa fa-file-excel-o text-success mr-1"></i> Excel
                </a>
            </div>
        </div>

        <!-- 2. TOP SPOTLIGHT ROW: AVATAR & CREDENTIALS (LEFT) + KPIS & MATRIX (RIGHT) -->
        <div class="row profile-row-compact">
            
            <!-- LEFT COLUMN: PHOTO, STATUS, QUICK PORTAL ACCESS (Col-md-3) -->
            <div class="col-md-3 col-lg-3 col-xl-3">
                <div class="dash-card h-100">
                    <div class="dash-card-header">
                        <h3 class="dash-card-title"><i class="fa fa-user-circle text-primary"></i> Student Identity</h3>
                        <span class="badge badge-primary">{{ $student->admissionNo ?? '-' }}</span>
                    </div>
                    <div class="dash-card-body p-2">
                        <!-- Student Photo Box with AJAX Upload Trigger -->
                        <div class="profile-avatar-box">
                            <img src="{{ $photo }}" id="profileAvatarImg" alt="{{ $studentName }}" onerror="this.src='{{ $defaultPhoto }}'">
                            <div class="profile-avatar-actions">
                                <button type="button" class="avatar-action-btn" onclick="$('#studentPhotoFileInput').click()" title="Change / Upload Photo">
                                    <i class="fa fa-camera mr-1"></i> Upload
                                </button>
                                @if(!empty($student->image))
                                    <button type="button" class="avatar-action-btn btn-del-photo" onclick="deleteStudentPhoto()" title="Remove Photo">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                            <input type="file" id="studentPhotoFileInput" style="display:none;" accept="image/jpeg,image/png,image/jpg,image/webp">
                        </div>

                        <!-- Web & App Portal Credentials -->
                        <div class="portal-cred-strip">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size:10px; font-weight:700; text-transform:uppercase; color:#93c5fd;">
                                    <i class="fa fa-shield mr-1"></i> Portal Access
                                </span>
                                <div>
                                    <button type="button" class="btn btn-xs btn-light py-0 px-2 font-weight-bold" onclick="copyCredentials()" title="Copy Credentials" style="font-size:10px; color:#002C54 !important;">
                                        <i class="fa fa-copy mr-1"></i> Copy
                                    </button>
                                    @if(!empty($fatherContact) && !empty($student->userName))
                                        @php
                                            $portalMsg = "Dear Parent, Login credentials for {$studentName} at Arise Portal:\nUsername: {$student->userName}\nPassword: {$student->confirm_password}\nPortal: " . url('/');
                                        @endphp
                                        <a href="https://api.whatsapp.com/send?phone=91{{ $fatherContact }}&text={{ urlencode($portalMsg) }}" target="_blank" class="btn btn-xs btn-success py-0 px-2 ml-1 font-weight-bold" title="Send on WhatsApp" style="font-size:10px;">
                                            <i class="fa fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="portal-cred-row">
                                <span class="portal-cred-label">Username:</span>
                                <span class="portal-cred-val" id="portalUsernameText">{{ $student->userName ?: 'Not Generated' }}</span>
                            </div>
                            <div class="portal-cred-row">
                                <span class="portal-cred-label">Password:</span>
                                <span class="portal-cred-val">
                                    <span id="portalPasswordMasked">••••••••</span>
                                    <span id="portalPasswordPlain" style="display:none;">{{ $student->confirm_password ?: 'Not Set' }}</span>
                                    <i class="fa fa-eye ml-1" id="togglePassEye" onclick="togglePasswordVisibility()" style="cursor:pointer; color:#38bdf8; font-size:11px;"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Fast Direct Contact Shortcuts -->
                        <div class="p-2 border rounded bg-light" style="font-size:11.5px;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted font-weight-bold">Father Mobile:</span>
                                <b class="text-dark">{{ $student->father_mobile ?: '-' }}</b>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted font-weight-bold">Student Mobile:</span>
                                <b class="text-dark">{{ $student->mobile ?: '-' }}</b>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted font-weight-bold">Admission Type:</span>
                                <span class="badge {{ (int)($student->admission_type_id ?? 1) === 2 ? 'badge-warning' : 'badge-secondary' }} font-weight-bold">
                                    {{ (int)($student->admission_type_id ?? 1) === 2 ? 'RTE' : 'Non-RTE' }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: 4 KPIS + MATRIX DETAILS TABLE (Col-md-9) -->
            <div class="col-md-9 col-lg-9 col-xl-9">
                
                <!-- 4 COMPACT SHARP KPI TILES (Matching admin_dashboard) -->
                <div class="row profile-row-compact mb-2">
                    <!-- Fee Collection Tile -->
                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="dash-kpi kpi-primary">
                            <div class="kpi-header">
                                <span class="kpi-label">Fee Balance</span>
                                <span class="kpi-icon"><i class="fa fa-inr"></i></span>
                            </div>
                            <div class="kpi-value text-primary">₹{{ number_format($dueFees, 0) }}</div>
                            <div class="kpi-meta">Paid ₹{{ number_format($paidFees, 0) }} of ₹{{ number_format($assignedFees, 0) }}</div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar bg-primary" style="width:{{ min(100, $feePaidPercentage) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Tile -->
                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="dash-kpi kpi-success">
                            <div class="kpi-header">
                                <span class="kpi-label">Attendance Rate</span>
                                <span class="kpi-icon"><i class="fa fa-check-circle"></i></span>
                            </div>
                            <div class="kpi-value text-success">{{ number_format($attendancePercentage, 0) }}%</div>
                            <div class="kpi-meta">{{ $attendancePresent }} Present · {{ $attendanceAbsent }} Absent</div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar bg-success" style="width:{{ min(100, $attendancePercentage) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Examination Tile -->
                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="dash-kpi kpi-warning">
                            <div class="kpi-header">
                                <span class="kpi-label">Exams Average</span>
                                <span class="kpi-icon"><i class="fa fa-trophy"></i></span>
                            </div>
                            <div class="kpi-value text-warning">{{ $examResults->count() ? number_format($overallExamPercentage, 0).'%' : 'N/A' }}</div>
                            <div class="kpi-meta">{{ $examResults->count() }} Tests · {{ $examResults->sum('subject_count') }} Entries</div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar bg-warning" style="width:{{ min(100, $overallExamPercentage) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Records & Inventory Tile -->
                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="dash-kpi kpi-info">
                            <div class="kpi-header">
                                <span class="kpi-label">Records Vault</span>
                                <span class="kpi-icon"><i class="fa fa-folder-open"></i></span>
                            </div>
                            <div class="kpi-value text-info">{{ collect($getDocuments)->count() }} Files</div>
                            <div class="kpi-meta">{{ collect($siblings)->count() }} Siblings · {{ $assignedSubjects->count() }} Subjects</div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar bg-info" style="width:100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KEY-VALUE MATRIX IDENTITY CARD -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3 class="dash-card-title"><i class="fa fa-address-card text-primary"></i> Academic & Personal Overview</h3>
                        <span class="badge badge-secondary" style="font-size:10.5px;">Session {{ $sessionLabel ?: 'Current' }}</span>
                    </div>
                    <div class="dash-card-body p-0 table-responsive">
                        <table class="matrix-table mb-0">
                            <tr>
                                <th>Class / Stream</th>
                                <td><b>{{ $student->class_name ?? '-' }}</b> @if(!empty($student->stream_subject)) ({{ $student->stream_subject }}) @endif</td>
                                <th>Medium / Gender</th>
                                <td>{{ $student->medium ?? 'Hindi' }} / {{ $student->genderName ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Father's Name</th>
                                <td>{{ $student->father_name ?? '-' }}</td>
                                <th>Mother's Name</th>
                                <td>{{ $student->mother_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ $dobFormatted }} @if($ageFormatted)<span class="text-muted">({{ $ageFormatted }})</span>@endif</td>
                                <th>Admission Date</th>
                                <td>{{ $admissionDateFormatted }}</td>
                            </tr>
                            <tr>
                                <th>Category / Religion</th>
                                <td>{{ $student->caste_category ?: ($student->category ?: '-') }} / {{ $student->religion ?: '-' }}</td>
                                <th>Blood Group</th>
                                <td><span class="badge badge-light border text-danger font-weight-bold">{{ $student->blood_group ?: '-' }}</span></td>
                            </tr>
                            <tr>
                                <th>Aadhaar Number</th>
                                <td>{{ $student->aadhaar ?: '-' }}</td>
                                <th>APAAR / Jan Aadhaar</th>
                                <td>{{ collect([$student->apaar_id, $student->jan_aadhaar])->filter()->implode(' / ') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Address & City</th>
                                <td colspan="3">
                                    {{ collect([$student->address, $student->village_city, $student->city_name, $student->state_name, $student->pincode])->filter()->implode(', ') ?: '-' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. TABBED SECTIONS NAVIGATION (Sharp ERP Tabs) -->
        <div class="profile-tabs-bar" id="profileTabsNav">
            <button type="button" class="profile-tab-link active" data-tab="tab-overview">
                <i class="fa fa-user"></i> Full Profile & Kit
            </button>
            <button type="button" class="profile-tab-link" data-tab="tab-fees">
                <i class="fa fa-inr"></i> Fee Ledger & Receipts <span class="profile-tab-badge">{{ collect($getPaidFees)->count() }}</span>
            </button>
            <button type="button" class="profile-tab-link" data-tab="tab-attendance">
                <i class="fa fa-calendar-check-o"></i> Daily Attendance <span class="profile-tab-badge">{{ $attendanceTotal }}</span>
            </button>
            <button type="button" class="profile-tab-link" data-tab="tab-exams">
                <i class="fa fa-bar-chart"></i> Examination Scorecards <span class="profile-tab-badge">{{ $examResults->count() }}</span>
            </button>
            <button type="button" class="profile-tab-link" data-tab="tab-documents">
                <i class="fa fa-file-text-o"></i> Documents Vault <span class="profile-tab-badge">{{ collect($getDocuments)->count() }}</span>
            </button>
            <button type="button" class="profile-tab-link" data-tab="tab-family">
                <i class="fa fa-users"></i> Siblings <span class="profile-tab-badge">{{ collect($siblings)->count() }}</span>
            </button>
        </div>

        <!-- ==========================================
             TAB 1: FULL PROFILE & UNIFORM KIT
             ========================================== -->
        <div class="profile-tab-content-pane active" id="tab-overview">
            <!-- Uniform & Study Material Kit Section -->
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
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title"><i class="fa fa-cubes text-primary"></i> Uniform & Study Material Kit</h3>
                    <a href="{{ url('student_inventory/bulk_issue?class_type_id='.$student->class_type_id) }}" class="btn btn-xs btn-outline-primary">
                        <i class="fa fa-list mr-1"></i> Bulk Issue Record
                    </a>
                </div>
                <div class="dash-card-body p-2">
                    <div class="row profile-row-compact">
                        <div class="col-md-3">
                            <div class="inventory-tile">
                                <div class="inventory-tile-label"><i class="fa fa-tag text-primary mr-1"></i> Shirt 1 (Color & Size)</div>
                                @if($studentInventory && !empty($studentInventory->shirt1_color))
                                    <div class="inventory-tile-value text-success">{{ $studentInventory->shirt1_color }}</div>
                                    <div class="text-muted" style="font-size:10.5px;">Size: {{ $studentInventory->shirt1_size }} · {{ !empty($studentInventory->shirt1_date) ? date('d M Y', strtotime($studentInventory->shirt1_date)) : '-' }}</div>
                                @else
                                    <span class="badge badge-secondary">Not Issued</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="inventory-tile">
                                <div class="inventory-tile-label"><i class="fa fa-tags text-danger mr-1"></i> Shirt 2 (Color & Size)</div>
                                @if($studentInventory && !empty($studentInventory->shirt2_color))
                                    <div class="inventory-tile-value text-success">{{ $studentInventory->shirt2_color }}</div>
                                    <div class="text-muted" style="font-size:10.5px;">Size: {{ $studentInventory->shirt2_size }} · {{ !empty($studentInventory->shirt2_date) ? date('d M Y', strtotime($studentInventory->shirt2_date)) : '-' }}</div>
                                @else
                                    <span class="badge badge-secondary">Not Issued</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="inventory-tile">
                                <div class="inventory-tile-label"><i class="fa fa-shopping-bag text-purple mr-1" style="color:#8b5cf6;"></i> School / Coaching Bag</div>
                                @if($studentInventory && (int)($studentInventory->bag_issued ?? 0) === 1)
                                    <div class="inventory-tile-value text-success"><i class="fa fa-check mr-1"></i> Issued</div>
                                    <div class="text-muted" style="font-size:10.5px;">Date: {{ !empty($studentInventory->bag_date) ? date('d M Y', strtotime($studentInventory->bag_date)) : '-' }}</div>
                                @else
                                    <span class="badge badge-secondary">Not Issued</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="inventory-tile">
                                <div class="inventory-tile-label"><i class="fa fa-book text-info mr-1"></i> Study Material / Modules</div>
                                @if($studentInventory && (int)($studentInventory->material_issued ?? 0) === 1)
                                    <div class="inventory-tile-value text-success"><i class="fa fa-check mr-1"></i> Issued</div>
                                    <div class="text-muted" style="font-size:10.5px;">Date: {{ !empty($studentInventory->material_date) ? date('d M Y', strtotime($studentInventory->material_date)) : '-' }}</div>
                                @else
                                    <span class="badge badge-secondary">Not Issued</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Detailed Personal, Guardian, Transport, Bank Grid -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title"><i class="fa fa-id-card text-primary"></i> Extended Guardian, Transport & Official Profile</h3>
                    <a href="{{ url('admissionEdit/' . $student->id) }}" class="btn btn-xs btn-outline-secondary">
                        <i class="fa fa-pencil mr-1"></i> Edit Details
                    </a>
                </div>
                <div class="dash-card-body p-0 table-responsive">
                    <table class="matrix-table mb-0">
                        <tr>
                            <th>Father Mobile</th>
                            <td>{{ $student->father_mobile ?: '-' }}</td>
                            <th>Father Occupation</th>
                            <td>{{ $student->father_occupation ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mother Mobile</th>
                            <td>{{ $student->mother_mob ?: '-' }}</td>
                            <th>Mother Occupation</th>
                            <td>{{ $student->mother_occupation ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Guardian Name & Contact</th>
                            <td>{{ collect([$student->guardian_name, $student->guardian_mobile])->filter()->implode(' / ') ?: '-' }}</td>
                            <th>Family Annual Income</th>
                            <td>{{ $student->family_annual_income ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Transport Facility</th>
                            <td>{{ in_array(strtolower((string)$student->transport), ['1','yes','true'], true) ? 'Availed (Yes)' : 'Not Availed (No)' }}</td>
                            <th>Bus / Route / Stop</th>
                            <td>{{ collect([$student->bus_number, $student->bus_route, $student->stoppage])->filter()->implode(' · ') ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bank Name & Branch</th>
                            <td>{{ $student->bank_name ?: '-' }}</td>
                            <th>Account No & IFSC</th>
                            <td>{{ collect([$student->bank_account, $student->ifsc])->filter()->implode(' / ') ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TAB 2: FEES & PAYMENT RECEIPTS (UNIFIED)
             ========================================== -->
        <div class="profile-tab-content-pane" id="tab-fees">

            <!-- 1. Fee Metrics Summary Strip -->
            <div class="row profile-row-compact mb-2">
                <div class="col-6 col-md-3 col-xl">
                    <div class="dash-kpi kpi-primary">
                        <div class="kpi-header">
                            <span class="kpi-label">Total Assigned</span>
                            <span class="kpi-icon"><i class="fa fa-calculator"></i></span>
                        </div>
                        <div class="kpi-value text-primary">₹{{ number_format($assignedFees, 2) }}</div>
                        <div class="kpi-meta">{{ $feeHeadLedger->count() }} Fee Heads Assigned</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="dash-kpi kpi-info">
                        <div class="kpi-header">
                            <span class="kpi-label">Total Discount</span>
                            <span class="kpi-icon"><i class="fa fa-tag"></i></span>
                        </div>
                        <div class="kpi-value text-info">₹{{ number_format($feeDiscount, 2) }}</div>
                        <div class="kpi-meta">Concessions / Waivers</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="dash-kpi kpi-success">
                        <div class="kpi-header">
                            <span class="kpi-label">Total Paid</span>
                            <span class="kpi-icon"><i class="fa fa-check-circle"></i></span>
                        </div>
                        <div class="kpi-value text-success">₹{{ number_format($paidFees, 2) }}</div>
                        <div class="kpi-meta">{{ collect($getPaidFees)->count() }} Receipts Issued</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="dash-kpi kpi-warning">
                        <div class="kpi-header">
                            <span class="kpi-label">Fine / Penalty</span>
                            <span class="kpi-icon"><i class="fa fa-exclamation-triangle"></i></span>
                        </div>
                        <div class="kpi-value text-warning">₹{{ number_format($feeFine, 2) }}</div>
                        <div class="kpi-meta">Late Fees Applied</div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-xl">
                    <div class="dash-kpi {{ $dueFees > 0 ? 'kpi-danger' : 'kpi-success' }}">
                        <div class="kpi-header">
                            <span class="kpi-label">Outstanding Due</span>
                            <span class="kpi-icon"><i class="fa {{ $dueFees > 0 ? 'fa-clock-o text-danger' : 'fa-check text-success' }}"></i></span>
                        </div>
                        <div class="kpi-value {{ $dueFees > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($dueFees, 2) }}</div>
                        <div class="kpi-meta">{{ $dueFees <= 0 ? 'All fees cleared' : 'Balance payment pending' }}</div>
                    </div>
                </div>
            </div>

            <!-- 2. SECTION A: Fee Head Allocation & Balance Ledger Table -->
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3 class="dash-card-title"><i class="fa fa-list-ol text-primary"></i> Fee Head Allocation & Balance Ledger</h3>
                    <div>
                        <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo) }}" class="btn btn-xs btn-success font-weight-bold">
                            <i class="fa fa-plus-circle mr-1"></i> Collect Fees
                        </a>
                        <a href="{{ url('fees_ledger_view?admission_no=' . $student->admissionNo) }}" class="btn btn-xs btn-outline-primary ml-1 font-weight-bold">
                            <i class="fa fa-book mr-1"></i> Full Ledger Statement
                        </a>
                    </div>
                </div>
                <div class="dash-card-body p-0 table-responsive">
                    <table class="arise-profile-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:35px;" class="text-center">#</th>
                                <th style="min-width:200px;">Fee Head Name</th>
                                <th class="text-right" style="width:120px;">Assigned</th>
                                <th class="text-right" style="width:110px;">Discount</th>
                                <th class="text-right" style="width:120px;">Paid Amount</th>
                                <th class="text-right" style="width:100px;">Fine</th>
                                <th class="text-right" style="width:120px;">Due Balance</th>
                                <th class="text-center" style="width:130px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($feeHeadLedger as $head)
                            @php
                                $headDue = (float)$head->due_amount;
                                $headPaid = (float)$head->paid_amount;
                                $headDiscount = (float)$head->discount;
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="font-weight-bold text-dark" style="font-size:12.5px;">{{ $head->name }}</span>
                                </td>
                                <td class="text-right font-weight-bold text-dark">₹{{ number_format($head->assigned_amount, 2) }}</td>
                                <td class="text-right text-info font-weight-bold">
                                    {{ $headDiscount > 0 ? '₹'.number_format($headDiscount, 2) : '-' }}
                                </td>
                                <td class="text-right text-success font-weight-bold">
                                    {{ $headPaid > 0 ? '₹'.number_format($headPaid, 2) : '-' }}
                                </td>
                                <td class="text-right text-warning font-weight-bold">
                                    {{ (float)$head->fine_amount > 0 ? '₹'.number_format($head->fine_amount, 2) : '-' }}
                                </td>
                                <td class="text-right font-weight-bold {{ $headDue > 0 ? 'text-danger' : 'text-success' }}">
                                    ₹{{ number_format($headDue, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($headDue <= 0)
                                        <span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i> Paid Fully</span>
                                    @elseif($headPaid > 0 || $headDiscount > 0)
                                        <span class="badge badge-warning px-2 py-1"><i class="fa fa-clock-o mr-1"></i> Partial</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1"><i class="fa fa-exclamation mr-1"></i> Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fa fa-info-circle fa-2x mb-2 text-primary d-block"></i>
                                    No fee heads are assigned to this student in the current session.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if($feeHeadLedger->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right font-weight-bold">Total Allocation:</td>
                                    <td class="text-right text-dark font-weight-bold">₹{{ number_format($feeHeadLedger->sum('assigned_amount'), 2) }}</td>
                                    <td class="text-right text-info font-weight-bold">₹{{ number_format($feeHeadLedger->sum('discount'), 2) }}</td>
                                    <td class="text-right text-success font-weight-bold">₹{{ number_format($feeHeadLedger->sum('paid_amount'), 2) }}</td>
                                    <td class="text-right text-warning font-weight-bold">₹{{ number_format($feeHeadLedger->sum('fine_amount'), 2) }}</td>
                                    <td class="text-right text-danger font-weight-bold">₹{{ number_format($feeHeadLedger->sum('due_amount'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- 3. SECTION B: Fee Payment Receipts & Invoices Register (UNIFIED LISTING) -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-file-text-o text-success"></i> Payment Receipts & Invoices Register (Unified)
                    </h3>
                    <span class="badge badge-success font-weight-bold" style="font-size:11px;">
                        {{ collect($getPaidFees)->count() }} Receipts Issued
                    </span>
                </div>
                <div class="dash-card-body p-0 table-responsive">
                    <table class="arise-profile-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:35px;" class="text-center">#</th>
                                <th style="width:120px;">Payment Date</th>
                                <th style="width:130px;">Invoice No</th>
                                <th style="width:130px;">Receipt No</th>
                                <th style="width:120px;">Payment Mode</th>
                                <th class="text-right" style="width:130px;">Amount Paid</th>
                                <th class="text-right" style="width:110px;">Discount</th>
                                <th class="text-right" style="width:100px;">Fine</th>
                                <th class="text-center" style="min-width:160px;">Receipt Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse(collect($getPaidFees) as $invoice)
                            @php
                                $rawInvDate = $invoice->payment_date ?: ($invoice->date ?: $invoice->created_at);
                                $invDate = !empty($rawInvDate) ? \Carbon\Carbon::parse($rawInvDate) : null;
                                $invNo = !empty($invoice->invoice_no) ? $invoice->invoice_no : '-';
                                $receiptNo = !empty($invoice->receipt_no) ? $invoice->receipt_no : '-';
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    @if($invDate)
                                        <span class="font-weight-bold text-dark"><i class="fa fa-calendar-o text-muted mr-1"></i>{{ $invDate->format('d M Y') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light border text-primary font-weight-bold px-2 py-1" style="font-size:11.5px;">
                                        {{ $invNo }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="font-size:11.5px;">
                                        {{ $receiptNo }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary px-2 py-1 font-weight-bold">
                                        <i class="fa fa-credit-card mr-1"></i>{{ $invoice->payment_mode ?: 'Cash' }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold text-success" style="font-size:13px;">
                                    ₹{{ number_format((float)$invoice->amount, 2) }}
                                </td>
                                <td class="text-right text-info font-weight-bold">
                                    {{ (float)($invoice->discount ?? 0) > 0 ? '₹'.number_format((float)$invoice->discount, 2) : '-' }}
                                </td>
                                <td class="text-right text-warning font-weight-bold">
                                    {{ (float)($invoice->total_fine ?? 0) > 0 ? '₹'.number_format((float)$invoice->total_fine, 2) : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('fees_receipt/' . $invoice->id . '/view') }}" target="_blank" class="btn btn-xs btn-primary mr-1 font-weight-bold" title="View Official Receipt">
                                        <i class="fa fa-eye mr-1"></i> View
                                    </a>
                                    <a href="{{ url('fees_receipt/' . $invoice->id . '/download') }}" target="_blank" class="btn btn-xs btn-outline-success font-weight-bold" title="Download Receipt PDF">
                                        <i class="fa fa-download mr-1"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fa fa-file-text-o fa-2x mb-2 text-primary d-block"></i>
                                    No payment receipts or invoice records found for this student in the current session.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if(collect($getPaidFees)->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Total Collected ({{ collect($getPaidFees)->count() }} Receipts):</td>
                                    <td class="text-right text-success font-weight-bold" style="font-size:13px;">₹{{ number_format(collect($getPaidFees)->sum('amount'), 2) }}</td>
                                    <td class="text-right text-info font-weight-bold">₹{{ number_format(collect($getPaidFees)->sum('discount'), 2) }}</td>
                                    <td class="text-right text-warning font-weight-bold">₹{{ number_format(collect($getPaidFees)->sum('total_fine'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>

        <!-- ==========================================
             TAB 3: DAILY ATTENDANCE PUNCHES
             ========================================== -->
        <div class="profile-tab-content-pane" id="tab-attendance">
            
            <!-- Attendance Summary Mini Strip -->
            <div class="row profile-row-compact mb-2">
                <div class="col-6 col-sm-6 col-md-3">
                    <div class="dash-kpi kpi-success">
                        <div class="kpi-header">
                            <span class="kpi-label">Present Days</span>
                            <span class="kpi-icon"><i class="fa fa-check-circle"></i></span>
                        </div>
                        <div class="kpi-value text-success">{{ $attendancePresent }}</div>
                        <div class="kpi-meta">Attended sessions</div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <div class="dash-kpi kpi-danger">
                        <div class="kpi-header">
                            <span class="kpi-label">Absent Days</span>
                            <span class="kpi-icon"><i class="fa fa-times-circle"></i></span>
                        </div>
                        <div class="kpi-value text-danger">{{ $attendanceAbsent }}</div>
                        <div class="kpi-meta">Missed sessions</div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <div class="dash-kpi kpi-primary">
                        <div class="kpi-header">
                            <span class="kpi-label">Total Marked</span>
                            <span class="kpi-icon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="kpi-value text-primary">{{ $attendanceTotal }}</div>
                        <div class="kpi-meta">Recorded school days</div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <div class="dash-kpi kpi-info">
                        <div class="kpi-header">
                            <span class="kpi-label">Overall Rate</span>
                            <span class="kpi-icon"><i class="fa fa-percent"></i></span>
                        </div>
                        <div class="kpi-value text-info">{{ number_format($attendancePercentage, 1) }}%</div>
                        <div class="kpi-progress">
                            <div class="kpi-progress-bar bg-info" style="width:{{ min(100, $attendancePercentage) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title"><i class="fa fa-calendar-check-o text-primary"></i> Daily Attendance Records</h3>
                    <div>
                        <span class="badge badge-success mr-1"><i class="fa fa-check"></i> {{ $attendancePresent }} Present</span>
                        <span class="badge badge-danger"><i class="fa fa-times"></i> {{ $attendanceAbsent }} Absent</span>
                    </div>
                </div>
                <div class="dash-card-body p-0 table-responsive">
                    <table class="arise-profile-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:35px;" class="text-center">#</th>
                                <th style="width:140px;">Date</th>
                                <th style="width:120px;">Day</th>
                                <th style="width:110px;">In Time</th>
                                <th style="width:110px;">Out Time</th>
                                <th class="text-center" style="width:130px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentAttendance as $attendance)
                            @php
                                $attDate = \Carbon\Carbon::parse($attendance->date);
                                $attendanceName = $attendance->profile_status ?? '-';
                                $attendanceKey = $attendance->profile_status_key ?? '';
                                $badgeClass = $attendanceKey === 'absent' ? 'badge-danger'
                                    : (in_array($attendanceKey, ['late','early_out','halfday'], true) ? 'badge-warning'
                                    : (in_array($attendanceKey, ['holiday','event','leave'], true) ? 'badge-info' : 'badge-success'));
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold text-dark"><i class="fa fa-calendar-o text-muted mr-1"></i>{{ $attDate->format('d M Y') }}</td>
                                <td class="text-muted font-weight-bold">{{ $attDate->format('l') }}</td>
                                <td class="font-weight-bold text-dark">{{ $attendance->time ?: '--:--' }}</td>
                                <td class="font-weight-bold text-dark">{{ $attendance->out_time ?: '--:--' }}</td>
                                <td class="text-center"><span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold">{{ $attendanceName }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fa fa-calendar-o fa-2x mb-2 text-primary d-block"></i>
                                    No attendance records found for this student.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TAB 4: EXAMINATIONS & SCORECARDS
             ========================================== -->
        <div class="profile-tab-content-pane" id="tab-exams">
            <div class="row profile-row-compact">
                <div class="col-lg-8">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3 class="dash-card-title"><i class="fa fa-trophy text-warning"></i> Examination Results</h3>
                            <span class="badge badge-primary font-weight-bold">{{ $examResults->count() }} Tests</span>
                        </div>
                        <div class="dash-card-body p-0 table-responsive">
                            <table class="arise-profile-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width:140px;">Exam Name</th>
                                        <th style="width:110px;">Date</th>
                                        <th style="min-width:200px;">Subject Marks</th>
                                        <th class="text-right" style="width:110px;">Total Score</th>
                                        <th class="text-center" style="width:110px;">Percentage</th>
                                        <th class="text-center" style="width:90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($examResults as $result)
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ $result->name }}</td>
                                        <td>{{ !empty($result->date) ? \Carbon\Carbon::parse($result->date)->format('d M Y') : '-' }}</td>
                                        <td>
                                            @forelse($result->subjects as $subject)
                                                <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                                    <span class="text-dark font-weight-bold">{{ $subject->name }}</span>
                                                    <span class="text-muted"><b>{{ $subject->is_numeric ? number_format((float) $subject->marks, 1) : $subject->marks }}</b> / {{ number_format((float) $subject->maximum, 1) }}</span>
                                                </div>
                                            @empty
                                                <span class="text-muted">No marks</span>
                                            @endforelse
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($result->obtained, 1) }} / {{ number_format($result->maximum, 1) }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $result->percentage >= 60 ? 'badge-success' : ($result->percentage >= 33 ? 'badge-warning' : 'badge-danger') }} px-2 py-1 font-weight-bold">
                                                {{ number_format($result->percentage, 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ url('exam_wise_report') }}" method="POST" target="_blank" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $result->exam_id }}">
                                                <input type="hidden" name="class_type_id" value="{{ $student->class_type_id }}">
                                                <input type="hidden" name="admission_id" value="{{ $student->id }}">
                                                @foreach($result->subjects->pluck('id')->filter()->unique() as $subjectId)
                                                    <input type="hidden" name="subject_id[]" value="{{ $subjectId }}">
                                                @endforeach
                                                <button type="submit" class="btn btn-xs btn-primary font-weight-bold" title="View Full Report">
                                                    <i class="fa fa-eye mr-1"></i> View
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fa fa-bar-chart fa-2x mb-2 text-primary d-block"></i>
                                            No marks recorded for this student in the current session.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Subject Mastery -->
                <div class="col-lg-4">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3 class="dash-card-title"><i class="fa fa-book text-info"></i> Subject Mastery</h3>
                        </div>
                        <div class="dash-card-body p-2">
                            @forelse($subjectPerformance as $subject)
                                <div class="mb-2 pb-1 border-bottom">
                                    <div class="d-flex justify-content-between font-weight-bold" style="font-size:11.5px;">
                                        <span class="text-dark">{{ $subject->name }}</span>
                                        <span class="{{ $subject->percentage >= 60 ? 'text-success' : ($subject->percentage >= 33 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($subject->percentage, 1) }}%
                                        </span>
                                    </div>
                                    <div class="kpi-progress">
                                        <div class="kpi-progress-bar {{ $subject->percentage >= 60 ? 'bg-success' : ($subject->percentage >= 33 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width:{{ min(100, $subject->percentage) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted text-center py-4" style="font-size:11.5px;">
                                    <i class="fa fa-book fa-2x mb-1 text-muted d-block"></i>
                                    Subject performance analytics will appear after exam marks are entered.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TAB 5: DOCUMENTS VAULT
             ========================================== -->
        <div class="profile-tab-content-pane" id="tab-documents">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title"><i class="fa fa-folder-open text-danger"></i> Student Documents Vault</h3>
                    <button type="button" class="btn btn-xs btn-primary font-weight-bold" data-toggle="modal" data-target="#uploadStudentDocModal">
                        <i class="fa fa-upload mr-1"></i> Upload Document
                    </button>
                </div>
                <div class="dash-card-body p-0 table-responsive">
                    <table class="arise-profile-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:35px;" class="text-center">#</th>
                                <th style="min-width:200px;">Document Title</th>
                                <th style="min-width:180px;">Remark / Description</th>
                                <th style="width:130px;">Upload Date</th>
                                <th class="text-center" style="width:190px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($getDocuments as $document)
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold text-dark">
                                    <i class="fa fa-file-pdf-o text-danger mr-1"></i> {{ $document->title ?? 'Document' }}
                                </td>
                                <td>{{ $document->remark ?: '-' }}</td>
                                <td>{{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ asset('schoolimage/student_document/' . $document->file) }}" target="_blank" class="btn btn-xs btn-primary mr-1 font-weight-bold" title="View Document">
                                        <i class="fa fa-eye mr-1"></i> View
                                    </a>
                                    <button type="button" class="btn btn-xs btn-warning mr-1 edit-doc-btn font-weight-bold"
                                            style="color:#0f172a;"
                                            data-id="{{ $document->id }}"
                                            data-title="{{ $document->title }}"
                                            data-remark="{{ $document->remark }}"
                                            title="Edit Document">
                                        <i class="fa fa-pencil mr-1"></i> Edit
                                    </button>
                                    <a href="{{ url('document_delete/' . $document->id) }}" onclick="return confirm('Are you sure you want to delete this document?')" class="btn btn-xs btn-danger font-weight-bold" title="Delete Document">
                                        <i class="fa fa-trash mr-1"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa fa-file-text-o fa-2x mb-2 text-primary d-block"></i>
                                    No documents uploaded for this student yet. Click "Upload Document" above.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TAB 6: SIBLINGS
             ========================================== -->
        <div class="profile-tab-content-pane" id="tab-family">
            <div class="row profile-row-compact">
                <!-- Siblings Table -->
                <div class="col-md-12">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3 class="dash-card-title"><i class="fa fa-users text-warning"></i> Sibling Information</h3>
                            <span class="badge badge-warning font-weight-bold">{{ collect($siblings)->count() }} Siblings</span>
                        </div>
                        <div class="dash-card-body p-0 table-responsive">
                            <table class="arise-profile-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:35px;" class="text-center">#</th>
                                        <th style="min-width:200px;">Sibling Name</th>
                                        <th style="width:140px;">Admission No</th>
                                        <th style="width:160px;">Class</th>
                                        <th class="text-center" style="width:120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($siblings as $sibling)
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold text-dark">{{ trim(($sibling->first_name ?? '').' '.($sibling->last_name ?? '')) }}</td>
                                        <td><span class="badge badge-light border text-primary font-weight-bold">{{ $sibling->admissionNo ?? '-' }}</span></td>
                                        <td><b>{{ $sibling->class_name ?? '-' }}</b></td>
                                        <td class="text-center">
                                            <a href="{{ url('studentDetail/'.$sibling->id) }}" class="btn btn-xs btn-primary font-weight-bold">
                                                <i class="fa fa-eye mr-1"></i> View Profile
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-users fa-2x mb-2 text-warning d-block"></i>
                                        No siblings registered for this student.
                                    </td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ==========================================
     BOOTSTRAP MODALS (Global Unified arise-modals.css)
     ========================================== -->

<!-- 1. Upload Student Document Modal -->
<div class="modal fade" id="uploadStudentDocModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ url('document_upload/' . ($student->id ?? 0)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="background:#002C54; color:#ffffff;">
                    <h5 class="modal-title font-weight-bold text-white" id="uploadDocTitle"><i class="fa fa-upload mr-1"></i> Upload Student Document</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold" style="font-size:12px;">Document Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="title" placeholder="e.g. Marksheet, TC, Certificate" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold" style="font-size:12px;">Remark / Description</label>
                        <textarea class="form-control form-control-sm" name="remark" rows="2" placeholder="Optional remark"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold" style="font-size:12px;">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-sm" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf" required>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fa fa-upload mr-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Edit Student Document Modal -->
<div class="modal fade" id="editStudentDocModal" tabindex="-1" role="dialog" aria-labelledby="editDocTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ url('document_update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="document_id" id="edit_document_id">
                <div class="modal-header" style="background:#002C54; color:#ffffff;">
                    <h5 class="modal-title font-weight-bold text-white" id="editDocTitle"><i class="fa fa-pencil mr-1"></i> Edit Student Document</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold" style="font-size:12px;">Document Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="title" id="edit_document_title" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold" style="font-size:12px;">Remark / Description</label>
                        <textarea class="form-control form-control-sm" name="remark" id="edit_document_remark" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold" style="font-size:12px;">Replace File <small class="text-muted">(Optional)</small></label>
                        <input type="file" class="form-control form-control-sm" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fa fa-save mr-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Student Status Toggle Modal -->
<div class="modal fade" id="studentStatusModal" tabindex="-1" role="dialog" aria-labelledby="statusTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#002C54; color:#ffffff;">
                <h5 class="modal-title font-weight-bold text-white" id="statusTitle"><i class="fa fa-power-off mr-1"></i> Student Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3 text-center">
                <p id="studentStatusConfirmText" class="mb-1 font-weight-bold text-dark" style="font-size:13px;"></p>
                <small class="text-muted">This will change the student's active enrollment and portal access status.</small>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmStudentStatusBtn" class="btn btn-primary btn-sm font-weight-bold">Confirm</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function () {
    // 1. Tab Switching
    $('.profile-tab-link').on('click', function () {
        var targetTab = $(this).data('tab');
        $('.profile-tab-link').removeClass('active');
        $(this).addClass('active');

        $('.profile-tab-content-pane').removeClass('active');
        $('#' + targetTab).addClass('active');

        if (history.pushState) {
            history.pushState(null, null, '#' + targetTab);
        } else {
            location.hash = targetTab;
        }
    });

    if (window.location.hash) {
        var hashTab = window.location.hash.substring(1);
        var activeBtn = $('.profile-tab-link[data-tab="' + hashTab + '"]');
        if (activeBtn.length) {
            activeBtn.trigger('click');
        }
    }

    // 2. Edit Document Modal Populator
    $(document).on('click', '.edit-doc-btn', function () {
        var id = $(this).attr('data-id');
        var title = $(this).attr('data-title');
        var remark = $(this).attr('data-remark');

        $('#edit_document_id').val(id);
        $('#edit_document_title').val(title);
        $('#edit_document_remark').val(remark);
        $('#editStudentDocModal').modal('show');
    });

    // 3. Instant Photo Upload via AJAX
    $('#studentPhotoFileInput').on('change', function () {
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
            $('#profileAvatarImg').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        if (window.toastr) toastr.info('Uploading profile photo...');

        $.ajax({
            url: '{{ url("imageRotateSave") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res.success || res.status === 'success') {
                    if (window.toastr) toastr.success('Profile photo updated successfully!');
                    if (res.image_url) $('#profileAvatarImg').attr('src', res.image_url);
                } else {
                    if (window.toastr) toastr.error(res.message || 'Photo upload failed');
                }
            },
            error: function () {
                if (window.toastr) toastr.error('An error occurred while uploading photo.');
            }
        });
    });
});

// 4. Delete Student Photo
function deleteStudentPhoto() {
    if (!confirm('Are you sure you want to remove this profile photo?')) return;

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
                $('#profileAvatarImg').attr('src', '{{ $defaultPhoto }}');
                $('.btn-del-photo').remove();
                if (window.toastr) toastr.success('Profile photo removed.');
            } else {
                if (window.toastr) toastr.error(res.message || 'Failed to remove photo.');
            }
        },
        error: function () {
            if (window.toastr) toastr.error('Error removing photo.');
        }
    });
}

// 5. Password Visibility Toggle
function togglePasswordVisibility() {
    var masked = $('#portalPasswordMasked');
    var plain = $('#portalPasswordPlain');
    var eye = $('#togglePassEye');

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

// 6. Copy Credentials to Clipboard
function copyCredentials() {
    var user = '{{ $student->userName }}';
    var pass = '{{ $student->confirm_password }}';
    var name = '{{ addslashes($studentName) }}';
    var portalUrl = '{{ url("/") }}';

    var text = "Student: " + name + "\nPortal: " + portalUrl + "\nUsername: " + user + "\nPassword: " + pass;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            if (window.toastr) toastr.success('Credentials copied to clipboard!');
            else alert('Credentials copied!');
        });
    } else {
        var tempInput = document.createElement("textarea");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        if (window.toastr) toastr.success('Credentials copied to clipboard!');
        else alert('Credentials copied!');
    }
}

// 7. Toggle Student Status
var currentStudentStatus = {{ $isActive ? 1 : 0 }};
function toggleStudentStatus() {
    var nextStatus = currentStudentStatus === 1 ? 0 : 1;
    $('#studentStatusConfirmText').text(
        nextStatus === 1
            ? 'Activate student "{{ addslashes($studentName) }}"?'
            : 'Deactivate student "{{ addslashes($studentName) }}"?'
    );
    $('#confirmStudentStatusBtn').data('next-status', nextStatus);
    $('#studentStatusModal').modal('show');
}

$('#confirmStudentStatusBtn').on('click', function () {
    var nextStatus = $(this).data('next-status');
    var btn = $(this);
    btn.prop('disabled', true).text('Updating...');

    $.ajax({
        type: 'POST',
        url: '{{ url("stu_status") }}',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        data: {id: '{{ $student->id }}', status: nextStatus},
        success: function () {
            currentStudentStatus = nextStatus;
            var isActive = nextStatus === 1;

            $('#studentStatusBadge')
                .toggleClass('badge-success', isActive)
                .toggleClass('badge-danger', !isActive)
                .html((isActive ? 'ACTIVE' : 'INACTIVE') + ' <i class="fa fa-pencil ml-1" style="font-size:9px;"></i>');

            $('#studentStatusModal').modal('hide');
            if (window.toastr) toastr.success('Student status updated successfully.');
        },
        error: function () {
            if (window.toastr) toastr.error('Failed to update student status.');
            else alert('Failed to update student status.');
        },
        complete: function () {
            btn.prop('disabled', false).text('Confirm');
        }
    });
});
</script>
@endsection
@endsection
