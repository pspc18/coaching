@php
    $getSetting = Helper::getSetting();
    $schoolName = is_array($getSetting) ? ($getSetting['name'] ?? '') : ($getSetting->name ?? 'School');
    $schoolLogo = is_array($getSetting) ? ($getSetting['left_logo'] ?? '') : ($getSetting->left_logo ?? '');
    $logoUrl = !empty($schoolLogo) ? env('IMAGE_SHOW_PATH') . '/setting/left_logo/' . $schoolLogo : '';
    
    $students = $data ?? collect();
    $users = $data2 ?? collect();
    $totalStudents = count($students);
    $totalUsers = count($users);
    $totalCelebrants = $totalStudents + $totalUsers;
    $targetDate = $targetDate ?? date('Y-m-d');
    $formattedDate = date('d-m-Y', strtotime($targetDate));
    $isToday = ($targetDate === date('Y-m-d'));
    
    $sentStudentIds = $sentStudentIds ?? [];
    $sentUserIds = $sentUserIds ?? [];
@endphp

@extends('layout.app') 

@section('title', "Birthday Celebrations - $formattedDate")

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP MODERN THEME - BIRTHDAY SHOWCASE & GREETING HUB
   Delightful, Card-Based Festive Layout (Non-Tabular, User-Friendly)
   ========================================================================== */

.admission-page {
    background: #f1f5f9;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 0 !important;
    overflow-x: hidden;
}
.admission-page-layout {
    min-height: calc(100vh - var(--header-height, 62px) - 16px);
    display: flex;
    flex-direction: column;
    padding: 6px 12px 16px 12px;
    gap: 8px;
    box-sizing: border-box;
    width: 100%;
}

/* Top Hero Celebration Banner */
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
    box-shadow: 0 2px 6px rgba(0,44,84,.15);
    flex-shrink: 0;
    width: 100%;
    position: relative;
    overflow: hidden;
}
.admission-hero::after {
    content: "🎈🎂🎉";
    position: absolute;
    right: 220px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 28px;
    opacity: 0.18;
    pointer-events: none;
}
.admission-hero-text {
    display: flex;
    flex-direction: column;
    z-index: 2;
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
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.admission-subtitle {
    font-size: 11px;
    color: #e2e8f0;
    margin-top: 2px;
}

/* Hero Actions & Date Controls */
.admission-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    z-index: 2;
}
.bday-date-picker-box {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 2px;
    padding: 1px 6px;
    height: 26px;
}
.bday-date-lbl {
    font-size: 9px;
    font-weight: 700;
    color: rgba(255,255,255,.8);
    text-transform: uppercase;
}
.bday-date-input {
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 10.5px;
    font-weight: 600;
    outline: none;
    color-scheme: dark;
    height: 100%;
}
.bday-date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: .9;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 3px 9px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    height: 26px;
    white-space: nowrap;
}
.dash-btn-outline {
    background: rgba(255,255,255,.12);
    border-color: rgba(255,255,255,.3);
    color: #ffffff;
}
.dash-btn-outline:hover, .dash-btn-outline.active {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}

/* Category Filter Bar */
.bday-filter-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.bday-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
}
.bday-tab-btn {
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.bday-tab-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.bday-tab-btn.active {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.bday-count-badge {
    background: rgba(0,0,0,.08);
    color: inherit;
    font-size: 9.5px;
    padding: 1px 5px;
    border-radius: 10px;
    font-weight: 700;
}
.bday-tab-btn.active .bday-count-badge {
    background: rgba(255,255,255,.2);
    color: #ffffff;
}

/* Select All & Multi-action Bar */
.bday-batch-ctrls {
    display: flex;
    align-items: center;
    gap: 8px;
}
.select-all-wrap {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    user-select: none;
    margin: 0;
}
.select-all-wrap input[type="checkbox"] {
    width: 15px;
    height: 15px;
    cursor: pointer;
}
.btn-send-batch {
    background: #10b981;
    color: #ffffff;
    border: 1px solid #059669;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 26px;
}
.btn-send-batch:hover:not(:disabled) {
    background: #059669;
}
.btn-send-batch:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ==========================================================================
   CELEBRATION SHOWCASE GRID - THE UNIQUE NON-TABULAR CARDS
   ========================================================================== */
.bday-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 12px;
    width: 100%;
}

/* Individual Celebration Card */
.bday-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    position: relative;
}
.bday-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0,44,84,.1);
    border-color: #93c5fd;
}
.bday-card.selected {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2,132,199,.25);
    background: #fcfdfe;
}

/* Card Decorative Top Header */
.bday-card-banner {
    height: 36px;
    background: linear-gradient(135deg, #002C54 0%, #17497d 100%);
    position: relative;
    padding: 0 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.bday-card-banner.staff-banner {
    background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
}
.bday-card-select {
    display: flex;
    align-items: center;
}
.bday-select-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    margin: 0;
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    user-select: none;
}
.bday-select-label input[type="checkbox"] {
    width: 15px;
    height: 15px;
    cursor: pointer;
    accent-color: #38bdf8;
    margin: 0;
}
.bday-status-pill {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.status-pill-sent {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.status-pill-pending {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}

/* Card Body & Profile Layout */
.bday-card-body {
    padding: 12px 14px 10px 14px;
    display: flex;
    flex-direction: column;
    flex: 1;
    background: #ffffff;
}
.bday-avatar-row {
    display: flex;
    align-items: center;
    margin-top: 0 !important;
    margin-bottom: 8px;
    gap: 12px;
}
.bday-avatar-wrap {
    position: relative;
    width: 52px;
    height: 52px;
    flex-shrink: 0;
}
.bday-avatar-img {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #cbd5e1;
    box-shadow: 0 2px 5px rgba(0,0,0,.08);
    background: #e2e8f0;
}
.bday-party-hat {
    position: absolute;
    top: -8px;
    right: -4px;
    font-size: 18px;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,.25));
    line-height: 1;
    transform: rotate(15deg);
}

/* Celebrant Name & Sub-details */
.bday-celebrant-meta {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.bday-name {
    font-size: 14px;
    font-weight: 700;
    color: #002C54;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0 0 3px 0;
}
.bday-role-badge {
    font-size: 9.5px;
    font-weight: 600;
    color: #1d4ed8;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    padding: 2px 7px;
    border-radius: 2px;
    display: inline-block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
.bday-role-badge.staff-badge {
    color: #047857;
    background: #ecfdf5;
    border-color: #a7f3d0;
}

/* Milestone Ribbon */
.bday-milestone-pill {
    margin: 6px 0 8px 0;
    background: #fdf4ff;
    border: 1px solid #f0abfc;
    color: #a21caf;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Key Attributes Grid inside Card */
.bday-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 8px;
    font-size: 10.5px;
}
.bday-info-item {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.bday-info-lbl {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    line-height: 1;
    margin-bottom: 2px;
}
.bday-info-val {
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Card Action Buttons Toolbar */
.bday-card-actions {
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr;
    gap: 4px;
    margin-top: auto;
}
.btn-action-wa {
    background: #25D366;
    color: #ffffff !important;
    border: 1px solid #20bd5a;
    border-radius: 2px;
    padding: 4px 6px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    transition: background .15s;
    height: 26px;
}
.btn-action-wa:hover {
    background: #1ea952;
}
.btn-action-call {
    background: #f1f5f9;
    color: #334155 !important;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 4px 6px;
    font-size: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    transition: all .15s;
    height: 26px;
}
.btn-action-call:hover {
    background: #e2e8f0;
    color: #0f172a !important;
}
.btn-action-greeting {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    border-radius: 2px;
    padding: 4px 6px;
    font-size: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: all .15s;
    height: 26px;
}
.btn-action-greeting:hover {
    background: #dbeafe;
    color: #1e40af;
}

/* ==========================================================================
   FESTIVE EMPTY STATE
   ========================================================================== */
.bday-empty-state {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 50px 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.bday-empty-icon {
    font-size: 54px;
    margin-bottom: 12px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,.1));
}
.bday-empty-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
}
.bday-empty-desc {
    font-size: 11.5px;
    color: #64748b;
    max-width: 440px;
    margin-bottom: 16px;
    line-height: 1.5;
}

/* ==========================================================================
   PRINTABLE BIRTHDAY GREETING CARD MODAL
   ========================================================================== */
#greetingCardModal .modal-content {
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #cbd5e1;
}
#greetingCardModal .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 8px 14px;
}
#greetingCardModal .modal-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #ffffff;
}
#greetingCardModal .close {
    color: #ffffff;
    opacity: 0.8;
}
.greeting-canvas {
    background: #ffffff;
    border: 8px double #d4af37;
    padding: 24px 20px;
    text-align: center;
    position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.greeting-header-logo {
    max-height: 55px;
    margin-bottom: 8px;
}
.greeting-school-name {
    font-size: 20px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 4px;
}
.greeting-ribbon {
    display: inline-block;
    background: #002C54;
    color: #ffd166;
    padding: 4px 16px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    border-radius: 2px;
    margin-bottom: 14px;
}
.greeting-photo-wrap {
    width: 86px;
    height: 86px;
    border-radius: 50%;
    margin: 0 auto 10px auto;
    border: 3px solid #d4af37;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
}
.greeting-photo-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.greeting-student-name {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}
.greeting-class-text {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 12px;
}
.greeting-quote {
    font-size: 12.5px;
    font-style: italic;
    color: #334155;
    line-height: 1.5;
    max-width: 480px;
    margin: 0 auto 14px auto;
}
.greeting-footer-msg {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    border-top: 1px solid #e2e8f0;
    padding-top: 10px;
}

@media print {
    body * {
        visibility: hidden;
    }
    .greeting-canvas, .greeting-canvas * {
        visibility: visible;
    }
    .greeting-canvas {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: 4px double #d4af37 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                {{-- 1. Top Hero Header (Arise ERP Pattern) --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-birthday-cake mr-1"></i> Birthday Celebrations Hub</span>
                        <h1 class="admission-title">
                            <span>Today's Birthday Celebrations</span>
                            <span style="font-size: 18px;">🎂🎈</span>
                        </h1>
                        <p class="admission-subtitle">
                            Viewing celebrants for <strong>{{ $formattedDate }}</strong> 
                            &bull; <strong>{{ $totalCelebrants }}</strong> total celebrants ({{ $totalStudents }} Students, {{ $totalUsers }} Staff)
                        </p>
                    </div>

                    <div class="admission-hero-actions">
                        {{-- Date Quick Filters --}}
                        <a href="{{ url('happy_birthday') }}" class="dash-btn dash-btn-outline {{ $isToday ? 'active' : '' }}" title="View Today's Birthdays">
                            Today
                        </a>
                        <a href="{{ url('happy_birthday?date=' . date('Y-m-d', strtotime('+1 day'))) }}" class="dash-btn dash-btn-outline {{ $targetDate === date('Y-m-d', strtotime('+1 day')) ? 'active' : '' }}" title="View Tomorrow's Birthdays">
                            Tomorrow
                        </a>

                        {{-- Date Picker Form --}}
                        <form action="{{ url('happy_birthday') }}" method="GET" class="d-inline-flex m-0">
                            <div class="bday-date-picker-box">
                                <span class="bday-date-lbl">Date</span>
                                <input type="date" name="date" class="bday-date-input" value="{{ $targetDate }}" onchange="this.form.submit()" title="Select Custom Date">
                            </div>
                        </form>

                        @if(Session::get('role_id') !== 3)
                            <a href="{{ url('send_message_terminal') }}" class="dash-btn dash-btn-light ml-1" title="Back to Message Terminal">
                                <i class="fa fa-arrow-left mr-1"></i> {{ __('common.Back') }}
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Batch Form enclosing cards for sending official ERP wishes --}}
                <form id="birthdayWishesForm" action="{{ url('send_wishes') }}" method="POST">
                    @csrf

                    {{-- 2. Category Filter & Batch Actions Toolbar --}}
                    <div class="bday-filter-strip mb-2">
                        <div class="bday-tabs">
                            <button type="button" class="bday-tab-btn active" data-filter="all">
                                <span>All Celebrants</span>
                                <span class="bday-count-badge">{{ $totalCelebrants }}</span>
                            </button>
                            <button type="button" class="bday-tab-btn" data-filter="student">
                                <span>🎓 Students</span>
                                <span class="bday-count-badge">{{ $totalStudents }}</span>
                            </button>
                            <button type="button" class="bday-tab-btn" data-filter="staff">
                                <span>👨‍🏫 Staff & Faculty</span>
                                <span class="bday-count-badge">{{ $totalUsers }}</span>
                            </button>
                        </div>

                        <div class="bday-batch-ctrls">
                            @if($totalCelebrants > 0)
                                <label class="select-all-wrap" title="Select or deselect all celebrants">
                                    <input type="checkbox" id="selectAllCelebrants">
                                    <span>Select All</span>
                                </label>
                                <button type="submit" class="btn-send-batch" id="btnSubmitBatch" disabled title="Send official birthday wishes via WhatsApp & notifications">
                                    <i class="fa fa-paper-plane"></i>
                                    <span>Send Wishes (<span id="selectedCount">0</span>)</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Non-Tabular Celebration Cards Showcase --}}
                    @if($totalCelebrants > 0)
                        <div class="bday-cards-grid" id="celebrantsGrid">

                            {{-- Student Celebrant Cards --}}
                            @foreach($students as $item)
                                @php
                                    $studentName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
                                    $studentPhoto = !empty($item['image']) 
                                        ? env('IMAGE_SHOW_PATH') . 'profile/' . $item['image'] 
                                        : env('IMAGE_SHOW_PATH') . 'default/user_image.jpg';
                                    
                                    $dob = !empty($item['dob']) ? $item['dob'] : null;
                                    $age = $dob ? \Carbon\Carbon::parse($dob)->age : null;
                                    $dobFormatted = $dob ? date('d-m-Y', strtotime($dob)) : '-';
                                    
                                    $isSent = in_array($item['id'], $sentStudentIds);
                                    
                                    // Pre-crafted heartfelt WhatsApp greeting message
                                    $waText = "Dear {$item['first_name']},\n\nWishing you a very Happy Birthday from all of us at {$schoolName}! 🎂🎉🎈\n\nMay this year bring you great joy, good health, and wonderful achievements in your studies!\n\nBest Wishes,\n{$schoolName}";
                                    $waLink = !empty($item['mobile']) 
                                        ? 'https://wa.me/91' . preg_replace('/[^0-9]/', '', $item['mobile']) . '?text=' . urlencode($waText) 
                                        : '#';
                                @endphp
                                <div class="bday-card celebrant-card card-type-student" data-type="student" data-id="{{ $item['id'] }}">
                                    {{-- Card Top Banner --}}
                                    <div class="bday-card-banner">
                                        <div class="bday-card-select">
                                            <label class="bday-select-label" title="Select this student">
                                                <input type="checkbox" 
                                                       name="checkbox_student[]" 
                                                       value="{{ $item['id'] }}" 
                                                       class="celebrant-checkbox student-checkbox">
                                                <span>Select</span>
                                            </label>
                                            <input type="hidden" name="first_name_student[]" value="{{ $item['first_name'] }}">
                                            <input type="hidden" name="mobile_student[]" value="{{ $item['mobile'] }}">
                                            <input type="hidden" name="role_id_student[]" value="{{ $item['role_id'] ?? 3 }}">
                                        </div>
                                        <div class="bday-card-status">
                                            @if($isSent)
                                                <span class="bday-status-pill status-pill-sent" title="Birthday wish delivered today">
                                                    <i class="fa fa-check-circle"></i> Sent
                                                </span>
                                            @else
                                                <span class="bday-status-pill status-pill-pending" title="Wish pending">
                                                    <i class="fa fa-clock-o"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="bday-card-body">
                                        <div class="bday-avatar-row">
                                            <div class="bday-avatar-wrap">
                                                <img src="{{ $studentPhoto }}" alt="{{ $studentName }}" class="bday-avatar-img" onerror="this.src='{{ env('IMAGE_SHOW_PATH') . 'default/user_image.jpg' }}'">
                                                <span class="bday-party-hat" title="Birthday Celebrant!">🎉</span>
                                            </div>
                                            <div class="bday-celebrant-meta">
                                                <h3 class="bday-name" title="{{ $studentName }}">{{ $studentName }}</h3>
                                                <span class="bday-role-badge" title="Class">
                                                    🎓 {{ $item['class_name'] ?: 'Student' }} 
                                                    @if(!empty($item['admissionNo'])) &bull; #{{ $item['admissionNo'] }} @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Milestone Ribbon --}}
                                        <div class="bday-milestone-pill">
                                            <span>🎂</span>
                                            @if($age)
                                                <span>Turning <strong>{{ $age }}</strong> Today!</span>
                                            @else
                                                <span>Happy Birthday!</span>
                                            @endif
                                        </div>

                                        {{-- Key Attributes Grid --}}
                                        <div class="bday-info-grid">
                                            <div class="bday-info-item">
                                                <span class="bday-info-lbl">Date of Birth</span>
                                                <span class="bday-info-val">{{ $dobFormatted }}</span>
                                            </div>
                                            <div class="bday-info-item">
                                                <span class="bday-info-lbl">Father's Name</span>
                                                <span class="bday-info-val" title="{{ $item['father_name'] }}">{{ $item['father_name'] ?: '-' }}</span>
                                            </div>
                                            <div class="bday-info-item" style="grid-column: span 2;">
                                                <span class="bday-info-lbl">Contact Number</span>
                                                <span class="bday-info-val">
                                                    @if(!empty($item['mobile']))
                                                        <i class="fa fa-phone text-muted mr-1"></i>{{ $item['mobile'] }}
                                                    @else
                                                        <span class="text-muted">Not Provided</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Quick Interactive Actions --}}
                                        <div class="bday-card-actions">
                                            @if(!empty($item['mobile']))
                                                <a href="{{ $waLink }}" target="_blank" class="btn-action-wa" title="Send personalized WhatsApp wish">
                                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                                </a>
                                                <a href="tel:{{ $item['mobile'] }}" class="btn-action-call" title="Call directly">
                                                    <i class="fa fa-phone"></i> Call
                                                </a>
                                            @else
                                                <button type="button" class="btn-action-wa" disabled style="opacity: 0.4;">
                                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                                </button>
                                                <button type="button" class="btn-action-call" disabled style="opacity: 0.4;">
                                                    <i class="fa fa-phone"></i> Call
                                                </button>
                                            @endif

                                            <button type="button" 
                                                    class="btn-action-greeting btn-preview-greeting" 
                                                    data-name="{{ $studentName }}"
                                                    data-photo="{{ $studentPhoto }}"
                                                    data-role="Student &bull; {{ $item['class_name'] ?? '' }}"
                                                    title="View and print personalized greeting card">
                                                <i class="fa fa-id-card-o"></i> Card
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Staff Celebrant Cards --}}
                            @foreach($users as $user)
                                @php
                                    $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                    $userPhoto = !empty($user['photo']) 
                                        ? env('IMAGE_SHOW_PATH') . 'profile/' . $user['photo'] 
                                        : env('IMAGE_SHOW_PATH') . 'default/user_image.jpg';
                                    
                                    $dob = !empty($user['dob']) ? $user['dob'] : null;
                                    $dobFormatted = $dob ? date('d-m-Y', strtotime($dob)) : '-';
                                    
                                    $isSent = in_array($user['id'], $sentUserIds);
                                    
                                    $waText = "Dear {$user['first_name']},\n\nWishing you a very Happy Birthday from all of us at {$schoolName}! 🎂🎉🎈\n\nThank you for your dedicated contribution. May you have a healthy, prosperous, and joyful year ahead!\n\nWarm Regards,\n{$schoolName}";
                                    $waLink = !empty($user['mobile']) 
                                        ? 'https://wa.me/91' . preg_replace('/[^0-9]/', '', $user['mobile']) . '?text=' . urlencode($waText) 
                                        : '#';
                                @endphp
                                <div class="bday-card celebrant-card card-type-staff" data-type="staff" data-id="{{ $user['id'] }}">
                                    {{-- Card Top Banner --}}
                                    <div class="bday-card-banner staff-banner">
                                        <div class="bday-card-select">
                                            <label class="bday-select-label" title="Select this staff member">
                                                <input type="checkbox" 
                                                       name="checkbox_user[]" 
                                                       value="{{ $user['id'] }}" 
                                                       class="celebrant-checkbox user-checkbox">
                                                <span>Select</span>
                                            </label>
                                            <input type="hidden" name="first_name_user[]" value="{{ $user['first_name'] }}">
                                            <input type="hidden" name="mobile_user[]" value="{{ $user['mobile'] }}">
                                            <input type="hidden" name="role_id_user[]" value="{{ $user['role_id'] ?? 1 }}">
                                        </div>
                                        <div class="bday-card-status">
                                            @if($isSent)
                                                <span class="bday-status-pill status-pill-sent" title="Birthday wish delivered today">
                                                    <i class="fa fa-check-circle"></i> Sent
                                                </span>
                                            @else
                                                <span class="bday-status-pill status-pill-pending" title="Wish pending">
                                                    <i class="fa fa-clock-o"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="bday-card-body">
                                        <div class="bday-avatar-row">
                                            <div class="bday-avatar-wrap">
                                                <img src="{{ $userPhoto }}" alt="{{ $userName }}" class="bday-avatar-img" onerror="this.src='{{ env('IMAGE_SHOW_PATH') . 'default/user_image.jpg' }}'">
                                                <span class="bday-party-hat" title="Staff Birthday!">🎉</span>
                                            </div>
                                            <div class="bday-celebrant-meta">
                                                <h3 class="bday-name" title="{{ $userName }}">{{ $userName }}</h3>
                                                <span class="bday-role-badge staff-badge" title="Staff Member">
                                                    👨‍🏫 Faculty / Staff Member
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Milestone Ribbon --}}
                                        <div class="bday-milestone-pill" style="background: #ecfdf5; border-color: #a7f3d0; color: #047857;">
                                            <span>🎂</span>
                                            <span>Happy Birthday Colleague!</span>
                                        </div>

                                        {{-- Key Attributes Grid --}}
                                        <div class="bday-info-grid">
                                            <div class="bday-info-item">
                                                <span class="bday-info-lbl">Date of Birth</span>
                                                <span class="bday-info-val">{{ $dobFormatted }}</span>
                                            </div>
                                            <div class="bday-info-item">
                                                <span class="bday-info-lbl">Role</span>
                                                <span class="bday-info-val">Staff</span>
                                            </div>
                                            <div class="bday-info-item" style="grid-column: span 2;">
                                                <span class="bday-info-lbl">Contact Number</span>
                                                <span class="bday-info-val">
                                                    @if(!empty($user['mobile']))
                                                        <i class="fa fa-phone text-muted mr-1"></i>{{ $user['mobile'] }}
                                                    @else
                                                        <span class="text-muted">Not Provided</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Quick Interactive Actions --}}
                                        <div class="bday-card-actions">
                                            @if(!empty($user['mobile']))
                                                <a href="{{ $waLink }}" target="_blank" class="btn-action-wa" title="Send personalized WhatsApp wish">
                                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                                </a>
                                                <a href="tel:{{ $user['mobile'] }}" class="btn-action-call" title="Call directly">
                                                    <i class="fa fa-phone"></i> Call
                                                </a>
                                            @else
                                                <button type="button" class="btn-action-wa" disabled style="opacity: 0.4;">
                                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                                </button>
                                                <button type="button" class="btn-action-call" disabled style="opacity: 0.4;">
                                                    <i class="fa fa-phone"></i> Call
                                                </button>
                                            @endif

                                            <button type="button" 
                                                    class="btn-action-greeting btn-preview-greeting" 
                                                    data-name="{{ $userName }}"
                                                    data-photo="{{ $userPhoto }}"
                                                    data-role="Faculty &bull; Staff Member"
                                                    title="View and print personalized greeting card">
                                                <i class="fa fa-id-card-o"></i> Card
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        {{-- 4. Festive Empty State --}}
                        <div class="bday-empty-state">
                            <div class="bday-empty-icon">🎂🎈✨</div>
                            <h2 class="bday-empty-title">No Birthdays on {{ $formattedDate }}</h2>
                            <p class="bday-empty-desc">
                                There are no student or staff birthdays registered for this date.
                                You can check tomorrow's birthdays or select another date using the date switcher above.
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ url('happy_birthday?date=' . date('Y-m-d', strtotime('+1 day'))) }}" class="dash-btn dash-btn-outline" style="background: #002C54; color: #fff;">
                                    <i class="fa fa-calendar-check-o mr-1"></i> Check Tomorrow's Birthdays
                                </a>
                                @if(!$isToday)
                                    <a href="{{ url('happy_birthday') }}" class="dash-btn dash-btn-light" style="border: 1px solid #cbd5e1;">
                                        Back to Today
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                </form>

            </div>
        </div>
    </section>
</div>

{{-- 5. Personalized Birthday Greeting Card Preview Modal --}}
<div class="modal fade" id="greetingCardModal" tabindex="-1" role="dialog" aria-labelledby="greetingCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title mb-0" id="greetingCardModalLabel">
                    <i class="fa fa-birthday-cake mr-1 text-warning"></i> Birthday Greeting Card
                </h5>
                <div>
                    <button type="button" class="btn btn-success btn-xs mr-2" onclick="window.print()">
                        <i class="fa fa-print mr-1"></i> Print Card
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="greeting-canvas" id="printableGreetingCard">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="Logo" class="greeting-header-logo">
                    @endif
                    <div class="greeting-school-name">{{ $schoolName }}</div>
                    <div class="greeting-ribbon">HAPPY BIRTHDAY</div>

                    <div class="greeting-photo-wrap">
                        <img id="cardModalPhoto" src="" alt="Celebrant" onerror="this.src='{{ env('IMAGE_SHOW_PATH') . 'default/user_image.jpg' }}'">
                    </div>

                    <div class="greeting-student-name" id="cardModalName">Celebrant Name</div>
                    <div class="greeting-class-text" id="cardModalRole">Student &bull; Class</div>

                    <div class="greeting-quote">
                        "Wishing you a day filled with laughter, love, and your favorite things. May all your dreams take flight this year! Happy Birthday!"
                    </div>

                    <div class="greeting-footer-msg">
                        &bull; Warm Wishes from all Teachers, Staff & Management &bull;
                    </div>
                </div>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    // 1. Tab Switching (All / Students / Staff)
    $('.bday-tab-btn').on('click', function() {
        $('.bday-tab-btn').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.celebrant-card').show();
        } else if (filter === 'student') {
            $('.celebrant-card').hide();
            $('.card-type-student').show();
        } else if (filter === 'staff') {
            $('.celebrant-card').hide();
            $('.card-type-staff').show();
        }
    });

    // 2. Checkbox & Card Selection Tracking
    function updateSelectedState() {
        const checkedBoxes = $('.celebrant-checkbox:checked');
        const count = checkedBoxes.length;
        $('#selectedCount').text(count);
        $('#btnSubmitBatch').prop('disabled', count === 0);

        // Update card highlight
        $('.celebrant-card').each(function() {
            const isChecked = $(this).find('.celebrant-checkbox').prop('checked');
            $(this).toggleClass('selected', isChecked);
        });

        // Update select all checkbox state
        const totalVisibleBoxes = $('.celebrant-checkbox').length;
        $('#selectAllCelebrants').prop('checked', count > 0 && count === totalVisibleBoxes);
    }

    $('.celebrant-checkbox').on('change', function() {
        updateSelectedState();
    });

    // 3. Select All Toggle
    $('#selectAllCelebrants').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.celebrant-checkbox').prop('checked', isChecked);
        updateSelectedState();
    });

    // 4. Form Validation before Batch Submission
    $('#birthdayWishesForm').on('submit', function(e) {
        if ($('.celebrant-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one celebrant to send wishes.');
            return false;
        }
    });

    // 5. Greeting Card Modal Preview
    $('.btn-preview-greeting').on('click', function() {
        const name = $(this).data('name');
        const photo = $(this).data('photo');
        const role = $(this).data('role');

        $('#cardModalName').text(name);
        $('#cardModalRole').html(role);
        $('#cardModalPhoto').attr('src', photo);

        $('#greetingCardModal').modal('show');
    });
});
</script>
@endsection