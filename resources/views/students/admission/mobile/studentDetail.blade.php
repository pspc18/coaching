@extends('layout.mobile_app')

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
   ARISE ERP - NATIVE MOBILE STUDENT PROFILE (SHARP CARDS & COMPACT DENSITY)
   Strictly adheres to Arise ERP Mobile Design System (arise-theme.css & mob-hero)
   ============================================================================== */

/* 1. Native Mobile Hero Card */
.native-mob-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #0a4275 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 10px 12px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}
.native-mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.native-mob-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.native-mob-back {
    color: #ffffff;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.12);
    text-decoration: none !important;
}

/* Student Main Info Box */
.native-student-row {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 8px;
}
.native-avatar-wrap {
    position: relative;
    width: 62px;
    height: 72px;
    border-radius: 3px;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: #001f3d;
}
.native-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.native-avatar-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #0284c7;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    border: 1px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.native-student-meta {
    flex: 1;
    min-width: 0;
}
.native-student-name {
    font-size: 15px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.native-tags-wrap {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    margin: 3px 0 4px;
}
.mob-badge-pill {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 2px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.15);
    color: #f1f5f9;
}
.mob-badge-active {
    background: rgba(16, 185, 129, 0.25);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.45);
}
.mob-badge-inactive {
    background: rgba(239, 68, 68, 0.25);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.45);
}

/* 3-Column Metrics Snapshot */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 3px;
    text-align: center;
}
.mob-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-metric-val {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
}
.mob-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 5px;
}
.mob-act-btn {
    flex: 1;
    height: 29px;
    padding: 0 4px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}
.mob-act-btn-whatsapp { background: #10b981; color: #ffffff !important; }
.mob-act-btn-call { background: #0284c7; color: #ffffff !important; }
.mob-act-btn-fees { background: #f59e0b; color: #ffffff !important; }
.mob-act-btn-light { background: rgba(255, 255, 255, 0.14); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.22); }
.mob-act-btn:active { transform: scale(0.97); }

/* 2. Segmented Navigation Bar */
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
    overflow-x: auto;
    scrollbar-width: none;
}
.mob-seg-bar::-webkit-scrollbar { display: none; }
.mob-seg-btn {
    flex: 0 0 auto;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    white-space: nowrap;
}
.mob-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.mob-seg-pill {
    display: inline-block;
    padding: 1px 4px;
    font-size: 9px;
    border-radius: 2px;
    background: rgba(0,0,0,0.08);
    margin-left: 2px;
}
.mob-seg-btn.active .mob-seg-pill {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}

/* 3. Segment Panes */
.mob-pane {
    display: none;
}
.mob-pane.active {
    display: block;
}

/* Sharp Mobile Cards */
.mob-card {
    background: #ffffff;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    margin-bottom: 8px;
    overflow: hidden;
}
.mob-card-head {
    padding: 7px 10px;
    background: #fafbfc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-card-title {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-card-body {
    padding: 8px 10px;
}

/* Portal Credentials Mobile Strip */
.mob-cred-card {
    background: #002C54;
    border-radius: 3px;
    padding: 8px 10px;
    color: #ffffff;
    margin-bottom: 8px;
}
.mob-cred-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    font-size: 11.5px;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.15);
}
.mob-cred-row:last-child { border-bottom: none; }
.mob-cred-lbl { color: #94a3b8; font-size: 10.5px; font-weight: 600; }
.mob-cred-val { font-family: monospace; font-size: 12px; font-weight: 800; color: #ffffff; }

/* Data Rows */
.mob-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 11.5px;
    border-bottom: 1px dashed #f1f5f9;
}
.mob-info-row:last-child { border-bottom: none; }
.mob-info-label { color: #64748b; font-weight: 600; }
.mob-info-val { color: #0f172a; font-weight: 700; text-align: right; max-width: 60%; }

/* Uniform Grid in Mobile */
.mob-kit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
.mob-kit-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
}
.mob-kit-tag { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px; }
.mob-kit-val { font-size: 11.5px; font-weight: 800; color: #0f172a; }

/* Fee Item Card */
.mob-fee-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px 10px;
    margin-bottom: 6px;
}
.mob-fee-item-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.mob-fee-name { font-size: 12.5px; font-weight: 700; color: #0f172a; }
.mob-fee-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    background: #f8fafc;
    padding: 5px 6px;
    border-radius: 3px;
    font-size: 10.5px;
}
.mob-fee-col { text-align: center; }
.mob-fee-col span { font-size: 8.5px; color: #64748b; display: block; text-transform: uppercase; }
.mob-fee-col b { font-size: 11.5px; }

/* Exam Item Card */
.mob-exam-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px 10px;
    margin-bottom: 6px;
}
.mob-exam-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.mob-exam-title { font-size: 12.5px; font-weight: 700; color: #0f172a; }
.mob-exam-sub { font-size: 10px; color: #64748b; }

/* Document Item Card */
.mob-doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    background: #ffffff;
    margin-bottom: 6px;
}
.mob-doc-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-doc-icon {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    background: #fee2e2;
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.mob-doc-name {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-doc-meta { font-size: 9.5px; color: #64748b; }

/* ==============================================================================
   NATIVE MOBILE BOTTOM SHEET DRAWER ARCHITECTURE
   ============================================================================== */
.mob-sheet-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 15, 30, 0.72);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: opacity .2s ease, visibility .2s ease;
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.35);
    z-index: 99995;
    transform: translateY(105%);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 520px;
    margin: 0 auto;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    max-height: 85vh;
}
.mob-bottom-sheet.show {
    transform: translateY(0);
}
.mob-sheet-drag-handle {
    width: 36px;
    height: 4px;
    background: rgba(255, 255, 255, 0.45);
    border-radius: 2px;
    margin: 6px auto 4px;
}
.mob-sheet-head {
    flex-shrink: 0;
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 0 12px 8px;
    display: flex;
    flex-direction: column;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-sheet-head-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 18px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    opacity: 0.85;
}
.mob-sheet-body {
    flex: 1 1 auto;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
    padding: 12px;
}
.mob-sheet-footer {
    flex-shrink: 0;
    padding: 8px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
}
.mob-sheet-submit-btn {
    flex: 1;
    height: 34px;
    border-radius: 3px;
    background: #0284c7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}
.mob-sheet-cancel-btn {
    width: 75px;
    height: 34px;
    border-radius: 3px;
    background: #e2e8f0;
    color: #475569;
    font-size: 11.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}
</style>
@endsection

@section('content')
<div class="p-2" style="padding-bottom: 60px !important;">

    <!-- 1. NATIVE MOBILE HERO CARD -->
    <div class="native-mob-hero">
        <div class="native-mob-hero-top">
            <a href="{{ url('admissionView?layout=mobile&from_profile=1') }}" class="native-mob-back" title="Back to Students">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div class="native-mob-title">
                <i class="fa fa-graduation-cap" style="color:#38bdf8;"></i>
                <span>Student Dossier</span>
            </div>
            <button type="button" class="btn btn-xs mob-badge-pill {{ $isActive ? 'mob-badge-active' : 'mob-badge-inactive' }}" onclick="openStatusSheet()">
                <i class="fa {{ $isActive ? 'fa-check' : 'fa-ban' }}"></i> {{ $isActive ? 'ACTIVE' : 'INACTIVE' }}
            </button>
        </div>

        <div class="native-student-row">
            <!-- Avatar with Instant Photo Upload Overlay -->
            <div class="native-avatar-wrap" onclick="openPhotoSheet()">
                <img src="{{ $photo }}" id="mobAvatarImg" class="native-avatar-img" alt="{{ $studentName }}" onerror="this.src='{{ $defaultPhoto }}'">
                <div class="native-avatar-badge"><i class="fa fa-camera"></i></div>
            </div>

            <!-- Details -->
            <div class="native-student-meta">
                <div class="native-student-name">{{ $studentName ?: 'Student' }}</div>
                <div class="native-tags-wrap">
                    <span class="mob-badge-pill"><i class="fa fa-hashtag"></i> {{ $student->admissionNo ?? '-' }}</span>
                    @if(!empty($student->roll_no))
                        <span class="mob-badge-pill"><i class="fa fa-id-badge"></i> Roll {{ $student->roll_no }}</span>
                    @endif
                    <span class="mob-badge-pill" style="background:rgba(56, 189, 248, 0.2); color:#38bdf8;"><i class="fa fa-book"></i> {{ $student->class_name ?? '-' }}</span>
                </div>
                <div style="font-size:10px; color:#cbd5e1;">
                    <span>Bio: <b>{{ $student->attendance_unique_id ?: ($student->unique_system_id ?: '-') }}</b></span>
                    <span class="mx-1">•</span>
                    <span>Session: <b>{{ $sessionLabel ?: 'Current' }}</b></span>
                </div>
            </div>
        </div>

        <!-- 3 Metrics Glance Grid -->
        <div class="mob-metrics-grid">
            <div class="mob-metric-box">
                <div class="mob-metric-tag"><i class="fa fa-inr"></i> Fees</div>
                <div class="mob-metric-val" style="color:#34d399;">₹{{ number_format($paidFees) }}</div>
                <div class="mob-metric-sub {{ $dueFees > 0 ? 'text-warning' : 'text-success' }}">Due: ₹{{ number_format($dueFees) }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag"><i class="fa fa-calendar-check-o"></i> Attend</div>
                <div class="mob-metric-val" style="color:#38bdf8;">{{ number_format($attendancePercentage, 0) }}%</div>
                <div class="mob-metric-sub">{{ $attendancePresent }}P · {{ $attendanceAbsent }}A</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag"><i class="fa fa-trophy"></i> Exams</div>
                <div class="mob-metric-val" style="color:#fbbf24;">{{ $examResults->count() ? number_format($overallExamPercentage, 0).'%' : 'N/A' }}</div>
                <div class="mob-metric-sub">{{ $examResults->count() }} Tests</div>
            </div>
        </div>

        <!-- Fast Actions Bar -->
        <div class="mob-actions-bar">
            @if(!empty($fatherContact))
                <a href="https://api.whatsapp.com/send?phone=91{{ $fatherContact }}&text=Hello%20{{ urlencode($studentName) }}" target="_blank" class="mob-act-btn mob-act-btn-whatsapp">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </a>
                <a href="tel:{{ $fatherContact }}" class="mob-act-btn mob-act-btn-call">
                    <i class="fa fa-phone"></i> Call
                </a>
            @endif
            <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo . '&layout=mobile') }}" class="mob-act-btn mob-act-btn-fees">
                <i class="fa fa-inr"></i> Collect
            </a>
            <a href="{{ url('admissionStudentIdPrint/' . $student->id) }}" target="_blank" class="mob-act-btn mob-act-btn-light" title="Print ID Card">
                <i class="fa fa-id-card-o"></i> ID
            </a>
            <a href="{{ url('admissionEdit/' . $student->id . '?layout=mobile') }}" class="mob-act-btn mob-act-btn-light" title="Edit">
                <i class="fa fa-pencil"></i> Edit
            </a>
        </div>
    </div>

    <!-- 2. SEGMENTED TABS -->
    <div class="mob-seg-bar">
        <button type="button" class="mob-seg-btn active" data-pane="pane-overview">Overview</button>
        <button type="button" class="mob-seg-btn" data-pane="pane-fees">Fees <span class="mob-seg-pill">{{ $feeHeadLedger->count() }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-attendance">Attendance <span class="mob-seg-pill">{{ $attendanceTotal }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-exams">Exams <span class="mob-seg-pill">{{ $examResults->count() }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-docs">Docs <span class="mob-seg-pill">{{ collect($getDocuments)->count() }}</span></button>
        <button type="button" class="mob-seg-btn" data-pane="pane-family">Siblings & Hist</button>
    </div>

    <!-- ==========================================
         PANE 1: OVERVIEW & KIT
         ========================================== -->
    <div class="mob-pane active" id="pane-overview">
        <!-- Portal Credentials Strip -->
        <div class="mob-cred-card">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold" style="font-size:11.5px; color:#38bdf8;"><i class="fa fa-shield mr-1"></i> Portal Access</span>
                <div>
                    <button type="button" class="btn btn-xs btn-light py-0 px-2 font-weight-bold" onclick="copyMobCredentials()" style="font-size:9.5px;">
                        <i class="fa fa-copy"></i> Copy
                    </button>
                    @if(!empty($fatherContact) && !empty($student->userName))
                        @php
                            $portalMsg = "Dear Parent, Login credentials for {$studentName} at Arise Portal:\nUsername: {$student->userName}\nPassword: {$student->confirm_password}\nPortal: " . url('/');
                        @endphp
                        <a href="https://api.whatsapp.com/send?phone=91{{ $fatherContact }}&text={{ urlencode($portalMsg) }}" target="_blank" class="btn btn-xs btn-success py-0 px-2 font-weight-bold ml-1" style="font-size:9.5px;">
                            <i class="fa fa-whatsapp"></i> Share
                        </a>
                    @endif
                </div>
            </div>
            <div class="mob-cred-row">
                <span class="mob-cred-lbl">Username</span>
                <span class="mob-cred-val">{{ $student->userName ?: 'Not Generated' }}</span>
            </div>
            <div class="mob-cred-row">
                <span class="mob-cred-lbl">Password</span>
                <span class="mob-cred-val">
                    <span id="mobPassMasked">••••••••</span>
                    <span id="mobPassPlain" style="display:none;">{{ $student->confirm_password ?: 'Not Set' }}</span>
                    <i class="fa fa-eye ml-1 text-sky" id="mobPassEye" onclick="toggleMobPassword()" style="font-size:11px; cursor:pointer; color:#38bdf8;"></i>
                </span>
            </div>
        </div>

        <!-- Uniform & Material Kit -->
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
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-cubes text-primary"></i> Uniform & Study Material Kit</h4>
            </div>
            <div class="mob-card-body">
                <div class="mob-kit-grid">
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-tag text-primary"></i> Shirt 1</div>
                        @if($studentInventory && !empty($studentInventory->shirt1_color))
                            <div class="mob-kit-val text-success">{{ $studentInventory->shirt1_color }}</div>
                            <div style="font-size:9px; color:#64748b;">Sz: {{ $studentInventory->shirt1_size }}</div>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-tags text-danger"></i> Shirt 2</div>
                        @if($studentInventory && !empty($studentInventory->shirt2_color))
                            <div class="mob-kit-val text-success">{{ $studentInventory->shirt2_color }}</div>
                            <div style="font-size:9px; color:#64748b;">Sz: {{ $studentInventory->shirt2_size }}</div>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-shopping-bag text-purple" style="color:#8b5cf6;"></i> Bag</div>
                        @if($studentInventory && (int)($studentInventory->bag_issued ?? 0) === 1)
                            <span class="badge badge-success" style="font-size:9px;"><i class="fa fa-check"></i> Issued</span>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                    <div class="mob-kit-box">
                        <div class="mob-kit-tag"><i class="fa fa-book text-info"></i> Modules</div>
                        @if($studentInventory && (int)($studentInventory->material_issued ?? 0) === 1)
                            <span class="badge badge-success" style="font-size:9px;"><i class="fa fa-check"></i> Issued</span>
                        @else
                            <span class="badge badge-secondary" style="font-size:9px;">Not Issued</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Personal & Family Information -->
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-user text-info"></i> Personal & Family Details</h4>
            </div>
            <div class="mob-card-body p-0 px-2">
                <div class="mob-info-row">
                    <span class="mob-info-label">Father's Name</span>
                    <span class="mob-info-val">{{ $student->father_name ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Father Mobile</span>
                    <span class="mob-info-val">
                        @if(!empty($student->father_mobile))
                            <a href="tel:{{ $student->father_mobile }}">{{ $student->father_mobile }}</a>
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Mother's Name</span>
                    <span class="mob-info-val">{{ $student->mother_name ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Date of Birth</span>
                    <span class="mob-info-val">{{ $dobFormatted }} @if($ageFormatted)<small class="text-muted">({{ $ageFormatted }})</small>@endif</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Admission Date</span>
                    <span class="mob-info-val">{{ $admissionDateFormatted }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Blood Group / Gender</span>
                    <span class="mob-info-val">{{ $student->blood_group ?: '-' }} / {{ $student->genderName ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Caste / Religion</span>
                    <span class="mob-info-val">{{ $student->caste_category ?: ($student->category ?: '-') }} / {{ $student->religion ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Aadhaar / APAAR</span>
                    <span class="mob-info-val">{{ $student->aadhaar ?: '-' }} / {{ $student->apaar_id ?: '-' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Transport Availed</span>
                    <span class="mob-info-val">{{ in_array(strtolower((string)$student->transport), ['1','yes','true'], true) ? 'Yes' : 'No' }}</span>
                </div>
                <div class="mob-info-row">
                    <span class="mob-info-label">Address</span>
                    <span class="mob-info-val" style="font-size:10.5px;">
                        {{ collect([$student->address, $student->village_city, $student->city_name, $student->state_name])->filter()->implode(', ') ?: '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         PANE 2: FEE HEAD-WISE LEDGER
         ========================================== -->
    <div class="mob-pane" id="pane-fees">
        <!-- Fees Summary Banner -->
        <div class="mob-card" style="background:#fafbfc; border-left:3px solid #002C54;">
            <div class="mob-card-body p-2 d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:10px; color:#64748b; font-weight:700;">ASSIGNED / PAID / DUE</div>
                    <div style="font-size:13.5px; font-weight:800; color:#0f172a;">
                        ₹{{ number_format($assignedFees) }} / <span class="text-success">₹{{ number_format($paidFees) }}</span> / <span class="text-danger">₹{{ number_format($dueFees) }}</span>
                    </div>
                </div>
                <a href="{{ url('Fees/add?admission_no=' . $student->admissionNo . '&layout=mobile') }}" class="btn btn-xs btn-success font-weight-bold">
                    <i class="fa fa-plus-circle"></i> Pay
                </a>
            </div>
        </div>

        @forelse($feeHeadLedger as $head)
            <div class="mob-fee-item">
                <div class="mob-fee-item-head">
                    <span class="mob-fee-name">{{ $head->name }}</span>
                    @if($head->due_amount <= 0)
                        <span class="badge badge-success" style="font-size:9px;"><i class="fa fa-check"></i> Paid</span>
                    @elseif($head->paid_amount > 0 || $head->discount > 0)
                        <span class="badge badge-warning" style="font-size:9px;"><i class="fa fa-clock-o"></i> Partial</span>
                    @else
                        <span class="badge badge-danger" style="font-size:9px;"><i class="fa fa-exclamation"></i> Unpaid</span>
                    @endif
                </div>

                <div class="mob-fee-grid">
                    <div class="mob-fee-col">
                        <span>Assigned</span>
                        <b>₹{{ number_format($head->assigned_amount) }}</b>
                    </div>
                    <div class="mob-fee-col">
                        <span>Paid</span>
                        <b class="text-success">₹{{ number_format($head->paid_amount) }}</b>
                    </div>
                    <div class="mob-fee-col">
                        <span>Due</span>
                        <b class="text-danger">₹{{ number_format($head->due_amount) }}</b>
                    </div>
                </div>

                @if($head->payments->isNotEmpty())
                    <div class="mt-2 pt-1 border-top" style="font-size:10px;">
                        <span class="text-muted font-weight-bold"><i class="fa fa-history mr-1"></i> Receipts:</span>
                        @foreach($head->payments as $payment)
                            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:10px;">
                                <span>{{ !empty($payment->date) ? date('d M Y', strtotime($payment->date)) : '-' }} ({{ $payment->payment_mode ?: 'Cash' }})</span>
                                <span class="text-success font-weight-bold">₹{{ number_format((float)$payment->paid_amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="fa fa-inr fa-2x mb-1 text-primary d-block"></i>
                No fee heads are assigned to this student.
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         PANE 3: ATTENDANCE LOG
         ========================================== -->
    <div class="mob-pane" id="pane-attendance">
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-calendar-check-o text-primary"></i> Attendance Summary</h4>
                <span class="badge badge-primary">{{ $attendanceTotal }} Days</span>
            </div>
            <div class="mob-card-body p-2">
                <div class="d-flex justify-content-between mb-2" style="font-size:11px; font-weight:700;">
                    <span class="text-success"><i class="fa fa-check-circle"></i> {{ $attendancePresent }} Present</span>
                    <span class="text-danger"><i class="fa fa-times-circle"></i> {{ $attendanceAbsent }} Absent</span>
                    <span class="text-primary">{{ number_format($attendancePercentage, 1) }}% Rate</span>
                </div>
                <div class="kpi-progress">
                    <div class="kpi-progress-bar bg-success" style="width:{{ min(100, $attendancePercentage) }}%"></div>
                </div>
            </div>
        </div>

        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-history text-info"></i> Recent Punch Records</h4>
            </div>
            <div class="mob-card-body p-0 px-2">
                @forelse($recentAttendance as $attendance)
                    @php
                        $attDate = \Carbon\Carbon::parse($attendance->date);
                        $attendanceName = $attendance->profile_status ?? '-';
                        $attendanceKey = $attendance->profile_status_key ?? '';
                        $badgeClass = $attendanceKey === 'absent' ? 'badge-danger'
                            : (in_array($attendanceKey, ['late','early_out','halfday'], true) ? 'badge-warning'
                            : (in_array($attendanceKey, ['holiday','event','leave'], true) ? 'badge-info' : 'badge-success'));
                    @endphp
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:11px;">
                        <div>
                            <div class="font-weight-bold text-dark">{{ $attDate->format('d M Y') }} ({{ $attDate->format('D') }})</div>
                            <div class="text-muted" style="font-size:9.5px;">In: {{ $attendance->time ?: '--:--' }} · Out: {{ $attendance->out_time ?: '--:--' }}</div>
                        </div>
                        <span class="badge {{ $badgeClass }}" style="font-size:9.5px; padding:2px 5px;">{{ $attendanceName }}</span>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted">No attendance records found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==========================================
         PANE 4: EXAMINATIONS
         ========================================== -->
    <div class="mob-pane" id="pane-exams">
        @forelse($examResults as $result)
            <div class="mob-exam-item">
                <div class="mob-exam-top">
                    <div>
                        <div class="mob-exam-title">{{ $result->name }}</div>
                        <div class="mob-exam-sub">{{ !empty($result->date) ? date('d M Y', strtotime($result->date)) : '-' }}</div>
                    </div>
                    <span class="badge {{ $result->percentage >= 60 ? 'badge-success' : ($result->percentage >= 33 ? 'badge-warning' : 'badge-danger') }}" style="font-size:10.5px;">
                        {{ number_format($result->percentage, 1) }}%
                    </span>
                </div>

                <div class="p-2 bg-light rounded mb-2" style="font-size:10.5px;">
                    <div class="d-flex justify-content-between font-weight-bold mb-1">
                        <span>Total Score:</span>
                        <span>{{ number_format($result->obtained, 1) }} / {{ number_format($result->maximum, 1) }}</span>
                    </div>
                    @foreach($result->subjects as $subject)
                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:10px;">
                            <span>{{ $subject->name }}</span>
                            <span class="text-muted"><b>{{ $subject->is_numeric ? number_format((float) $subject->marks, 1) : $subject->marks }}</b> / {{ number_format((float) $subject->maximum, 1) }}</span>
                        </div>
                    @endforeach
                </div>

                <form action="{{ url('exam_wise_report') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $result->exam_id }}">
                    <input type="hidden" name="class_type_id" value="{{ $student->class_type_id }}">
                    <input type="hidden" name="admission_id" value="{{ $student->id }}">
                    @foreach($result->subjects->pluck('id')->filter()->unique() as $subjectId)
                        <input type="hidden" name="subject_id[]" value="{{ $subjectId }}">
                    @endforeach
                    <button type="submit" class="btn btn-xs btn-outline-primary btn-block font-weight-bold">
                        <i class="fa fa-eye mr-1"></i> Full Scorecard
                    </button>
                </form>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="fa fa-trophy fa-2x mb-1 text-primary d-block"></i>
                No examination marks recorded for this student.
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         PANE 5: DOCUMENTS VAULT
         ========================================== -->
    <div class="mob-pane" id="pane-docs">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="font-weight-bold text-dark" style="font-size:12.5px;"><i class="fa fa-folder-open text-danger mr-1"></i> Student Vault</span>
            <button type="button" class="btn btn-xs btn-primary font-weight-bold" onclick="openDocUploadSheet()">
                <i class="fa fa-upload mr-1"></i> Add File
            </button>
        </div>

        @forelse($getDocuments as $document)
            <div class="mob-doc-item">
                <div class="mob-doc-left">
                    <div class="mob-doc-icon"><i class="fa fa-file-text-o"></i></div>
                    <div style="min-width:0;">
                        <div class="mob-doc-name">{{ $document->title ?: 'Document' }}</div>
                        <div class="mob-doc-meta">{{ $document->created_at ? date('d M Y', strtotime($document->created_at)) : '-' }} · {{ $document->remark ?: 'No remark' }}</div>
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ asset('schoolimage/student_document/' . $document->file) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="View">
                        <i class="fa fa-eye"></i>
                    </a>
                    <button type="button" class="btn btn-xs btn-outline-warning" onclick="openDocEditSheet('{{ $document->id }}', '{{ addslashes($document->title) }}', '{{ addslashes($document->remark) }}')" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <a href="{{ url('document_delete/' . $document->id) }}" onclick="return confirm('Delete this document?')" class="btn btn-xs btn-outline-danger" title="Delete">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="fa fa-file-text-o fa-2x mb-1 text-primary d-block"></i>
                No documents uploaded yet. Tap "Add File" above to upload.
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         PANE 6: SIBLINGS & PROMOTION HISTORY
         ========================================== -->
    <div class="mob-pane" id="pane-family">
        <!-- Siblings -->
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-users text-warning"></i> Sibling Information</h4>
                <span class="badge badge-warning">{{ collect($siblings)->count() }}</span>
            </div>
            <div class="mob-card-body p-0 px-2">
                @forelse($siblings as $sibling)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:11.5px;">
                        <div>
                            <div class="font-weight-bold text-dark">{{ trim(($sibling->first_name ?? '').' '.($sibling->last_name ?? '')) }}</div>
                            <div class="text-muted" style="font-size:9.5px;">Adm: {{ $sibling->admissionNo ?? '-' }} · Class: {{ $sibling->class_name ?? '-' }}</div>
                        </div>
                        <a href="{{ url('studentDetail/'.$sibling->id.'?layout=mobile') }}" class="btn btn-xs btn-primary font-weight-bold">
                            <i class="fa fa-eye"></i> View
                        </a>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted">No sibling records registered.</div>
                @endforelse
            </div>
        </div>

        <!-- Promotion History -->
        <div class="mob-card">
            <div class="mob-card-head">
                <h4 class="mob-card-title"><i class="fa fa-history text-primary"></i> Promotion History</h4>
                <span class="badge badge-info">{{ $promotion_history->count() }}</span>
            </div>
            <div class="mob-card-body p-0 px-2">
                @forelse($promotion_history as $history)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:11.5px;">
                        <div>
                            <div class="font-weight-bold text-dark">{{ collect([$history->from_year, $history->to_year])->filter()->implode('-') ?: '-' }}</div>
                            <div class="text-muted" style="font-size:9.5px;">Class: {{ $history->class_name ?? '-' }} · Roll: {{ $history->roll_no ?? '-' }}</div>
                        </div>
                        <span class="badge {{ (int)$history->status === 1 ? 'badge-success' : 'badge-secondary' }}">
                            {{ (int)$history->status === 1 ? 'Active' : 'Promoted' }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted">No promotion history found.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Hidden File Input for Avatar Photo -->
<input type="file" id="mobPhotoInput" style="display:none;" accept="image/jpeg,image/png,image/jpg,image/webp">

<!-- ==========================================
     NATIVE BOTTOM SHEET MODALS
     ========================================== -->

<!-- Backdrop -->
<div class="mob-sheet-backdrop" id="mobSheetBackdrop" onclick="closeAllSheets()"></div>

<!-- 1. Upload Document Bottom Sheet -->
<form action="{{ url('document_upload/' . ($student->id ?? 0)) }}" method="POST" enctype="multipart/form-data" class="mob-bottom-sheet" id="mobDocUploadSheet">
    @csrf
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-upload text-sky"></i> Upload Student Document</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body">
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Document Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="title" placeholder="e.g. Aadhaar Card, Previous Marksheet" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Remark / Description</label>
            <textarea class="form-control form-control-sm" name="remark" rows="2" placeholder="Optional remark"></textarea>
        </div>
        <div class="form-group mb-0">
            <label class="font-weight-bold" style="font-size:11.5px;">Select File <span class="text-danger">*</span></label>
            <input type="file" class="form-control form-control-sm border p-1" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf" required>
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-cancel-btn" onclick="closeAllSheets()">Cancel</button>
        <button type="submit" class="mob-sheet-submit-btn"><i class="fa fa-cloud-upload mr-1"></i> Upload</button>
    </div>
</form>

<!-- 2. Edit Document Bottom Sheet -->
<form action="{{ url('document_update') }}" method="POST" enctype="multipart/form-data" class="mob-bottom-sheet" id="mobDocEditSheet">
    @csrf
    <input type="hidden" name="document_id" id="mob_edit_doc_id">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-pencil text-sky"></i> Edit Document</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body">
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Document Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="title" id="mob_edit_doc_title" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold" style="font-size:11.5px;">Remark / Description</label>
            <textarea class="form-control form-control-sm" name="remark" id="mob_edit_doc_remark" rows="2"></textarea>
        </div>
        <div class="form-group mb-0">
            <label class="font-weight-bold" style="font-size:11.5px;">Replace File <small class="text-muted">(Optional)</small></label>
            <input type="file" class="form-control form-control-sm border p-1" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf">
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-cancel-btn" onclick="closeAllSheets()">Cancel</button>
        <button type="submit" class="mob-sheet-submit-btn"><i class="fa fa-save mr-1"></i> Save Changes</button>
    </div>
</form>

<!-- 3. Student Status Bottom Sheet -->
<div class="mob-bottom-sheet" id="mobStatusSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-power-off text-sky"></i> Student Status</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body text-center py-3">
        <div id="mobStatusPromptText" class="font-weight-bold text-dark mb-1" style="font-size:13px;"></div>
        <p class="text-muted small mb-0">Changing status will adjust student's enrollment and portal access.</p>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="mob-sheet-cancel-btn" onclick="closeAllSheets()">Cancel</button>
        <button type="button" id="mobConfirmStatusBtn" class="mob-sheet-submit-btn">Confirm Change</button>
    </div>
</div>

<!-- 4. Photo Picker Sheet -->
<div class="mob-bottom-sheet" id="mobPhotoSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h3 class="mob-sheet-title"><i class="fa fa-camera text-sky"></i> Profile Photo</h3>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-body p-3">
        <button type="button" class="btn btn-primary btn-block mb-2 font-weight-bold" onclick="$('#mobPhotoInput').click(); closeAllSheets();">
            <i class="fa fa-image mr-1"></i> Upload / Change Photo
        </button>
        @if(!empty($student->image))
            <button type="button" class="btn btn-outline-danger btn-block font-weight-bold" onclick="deleteStudentPhoto(); closeAllSheets();">
                <i class="fa fa-trash mr-1"></i> Remove Current Photo
            </button>
        @endif
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="btn btn-secondary btn-block" onclick="closeAllSheets()">Cancel</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // 1. Mobile Segmented Tab Switching
    $('.mob-seg-btn').on('click', function () {
        var targetPane = $(this).data('pane');
        $('.mob-seg-btn').removeClass('active');
        $(this).addClass('active');

        $('.mob-pane').removeClass('active');
        $('#' + targetPane).addClass('active');
    });

    // 2. Photo Upload AJAX
    $('#mobPhotoInput').on('change', function () {
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
            $('#mobAvatarImg').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        if (window.toastr) toastr.info('Uploading photo...');

        $.ajax({
            url: '{{ url("imageRotateSave") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res.success || res.status === 'success') {
                    if (window.toastr) toastr.success('Profile photo updated!');
                    if (res.image_url) $('#mobAvatarImg').attr('src', res.image_url);
                } else {
                    if (window.toastr) toastr.error(res.message || 'Upload failed');
                }
            },
            error: function () {
                if (window.toastr) toastr.error('Error uploading photo.');
            }
        });
    });
});

// Bottom Sheet Open / Close Helpers
function closeAllSheets() {
    $('.mob-bottom-sheet').removeClass('show');
    $('#mobSheetBackdrop').removeClass('show');
}
function openDocUploadSheet() {
    closeAllSheets();
    $('#mobSheetBackdrop').addClass('show');
    $('#mobDocUploadSheet').addClass('show');
}
function openDocEditSheet(id, title, remark) {
    closeAllSheets();
    $('#mob_edit_doc_id').val(id);
    $('#mob_edit_doc_title').val(title);
    $('#mob_edit_doc_remark').val(remark);
    $('#mobSheetBackdrop').addClass('show');
    $('#mobDocEditSheet').addClass('show');
}
function openPhotoSheet() {
    closeAllSheets();
    $('#mobSheetBackdrop').addClass('show');
    $('#mobPhotoSheet').addClass('show');
}

// Student Status in Mobile
var currentMobStatus = {{ $isActive ? 1 : 0 }};
function openStatusSheet() {
    var nextStatus = currentMobStatus === 1 ? 0 : 1;
    $('#mobStatusPromptText').text(
        nextStatus === 1
            ? 'Activate student "{{ addslashes($studentName) }}"?'
            : 'Deactivate student "{{ addslashes($studentName) }}"?'
    );
    $('#mobConfirmStatusBtn').data('next-status', nextStatus);
    closeAllSheets();
    $('#mobSheetBackdrop').addClass('show');
    $('#mobStatusSheet').addClass('show');
}

$('#mobConfirmStatusBtn').on('click', function () {
    var nextStatus = $(this).data('next-status');
    var btn = $(this);
    btn.prop('disabled', true).text('Updating...');

    $.ajax({
        type: 'POST',
        url: '{{ url("stu_status") }}',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        data: {id: '{{ $student->id }}', status: nextStatus},
        success: function () {
            currentMobStatus = nextStatus;
            closeAllSheets();
            if (window.toastr) toastr.success('Status updated successfully!');
            location.reload();
        },
        error: function () {
            if (window.toastr) toastr.error('Failed to update status.');
        },
        complete: function () {
            btn.prop('disabled', false).text('Confirm Change');
        }
    });
});

// Delete Photo
function deleteStudentPhoto() {
    if (!confirm('Remove profile photo?')) return;
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
                $('#mobAvatarImg').attr('src', '{{ $defaultPhoto }}');
                if (window.toastr) toastr.success('Photo removed.');
            }
        }
    });
}

// Password Reveal
function toggleMobPassword() {
    var masked = $('#mobPassMasked');
    var plain = $('#mobPassPlain');
    var eye = $('#mobPassEye');

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

// Copy Credentials
function copyMobCredentials() {
    var user = '{{ $student->userName }}';
    var pass = '{{ $student->confirm_password }}';
    var text = "Student: {{ addslashes($studentName) }}\nPortal: {{ url('/') }}\nUsername: " + user + "\nPassword: " + pass;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            if (window.toastr) toastr.success('Credentials copied!');
            else alert('Credentials copied!');
        });
    } else {
        var tempInput = document.createElement("textarea");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        if (window.toastr) toastr.success('Credentials copied!');
        else alert('Credentials copied!');
    }
}
</script>
@endsection
