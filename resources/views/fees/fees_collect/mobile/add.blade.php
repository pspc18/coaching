@php
    $classType = Helper::classType();
    $getPaymentMode = Helper::getPaymentMode();
    $currentSessionName = Session::get('session_name') ?? '2026-27';
    $currentSessionId = Session::get('session_id');
    $studentsCount = is_countable($data ?? []) ? count($data ?? []) : 0;
@endphp

@extends('layout.mobile_app')

@section('title', 'Collect Fees')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FEE COLLECTION POS
   Native mobile app design matching Arise ERP mobile design system
   ========================================================================== */
.pos-mob-container {
    padding: 0 0 70px 0;
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
    margin-bottom: 6px;
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
    letter-spacing: .02em;
}
.mob-hero-desc {
    font-size: 11px;
    color: #94a3b8;
    margin-bottom: 8px;
}
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}
.mob-kpi-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 6px 8px;
    text-align: center;
}
.mob-kpi-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-kpi-lbl {
    font-size: 9.5px;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-top: 2px;
}

/* 2. Instant Search Bar & Filter Chips */
.mob-search-box {
    position: relative;
    margin-bottom: 8px;
}
.mob-search-box i.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 13px;
}
.mob-search-input {
    width: 100%;
    height: 38px;
    padding: 6px 36px 6px 34px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    outline: none;
    transition: all 0.2s ease;
}
.mob-search-input:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15);
}
.mob-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #f1f5f9;
    border: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 11px;
    cursor: pointer;
}

/* Horizontal Filter Chips */
.mob-chips-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 6px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.mob-chips-strip::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
}

/* 3. Student Card Feed */
.mob-feed-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding: 0 2px;
}
.mob-feed-count {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
}
.mob-cards-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.mob-student-card {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    padding: 10px 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.15s ease;
    cursor: pointer;
    position: relative;
}
.mob-student-card:active {
    transform: scale(0.99);
    background: #f8fafc;
}
.mob-student-card.is-active {
    border-color: #002C54;
    box-shadow: 0 0 0 1.5px #002C54, 0 3px 8px rgba(0, 44, 84, 0.12);
    background: #f0f7ff;
}
.mob-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-adm-badge {
    font-size: 10px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
    letter-spacing: .02em;
}
.mob-badges-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-class-badge {
    font-size: 10px;
    font-weight: 700;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 2px 6px;
    border-radius: 3px;
}
.mob-rte-badge {
    font-size: 9px;
    font-weight: 800;
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
    padding: 1px 5px;
    border-radius: 3px;
}
.mob-card-name {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
    margin-bottom: 4px;
}
.mob-meta-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 11px;
    color: #64748b;
    margin-bottom: 8px;
}
.mob-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-meta-item i {
    font-size: 11px;
}
.mob-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 6px;
    border-top: 1px dashed #e2e8f0;
}
.btn-mob-collect {
    background: #10b981;
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 800;
    padding: 5px 12px;
    border-radius: 4px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 1px 4px rgba(16, 185, 129, 0.25);
    cursor: pointer;
}
.btn-mob-collect:hover, .btn-mob-collect:active {
    background: #059669;
}
.btn-mob-call {
    font-size: 11px;
    font-weight: 700;
    color: #0284c7;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 4px;
    background: #f0f9ff;
}

/* 4. Full Mobile POS Bottom Sheet / Overlay */
.mob-sheet-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(2px);
    z-index: 1050;
    display: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}
.mob-sheet-overlay.active {
    display: block;
    opacity: 1;
}
.mob-collect-sheet {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    height: 92vh;
    max-height: 92vh;
    background: #f8fafc;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    z-index: 1060;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.2);
    transform: translateY(100%);
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
}
.mob-collect-sheet.active {
    transform: translateY(0);
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
}
.mob-sheet-title-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-sheet-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.3);
}
.mob-sheet-body {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 8px;
    background: #eef2f6;
}

/* Empty State */
.mob-empty-feed {
    background: #ffffff;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    padding: 30px 16px;
    text-align: center;
    margin-top: 10px;
}
.mob-empty-feed i {
    font-size: 36px;
    color: #94a3b8;
    margin-bottom: 8px;
}
.mob-empty-feed h5 {
    font-size: 13px;
    font-weight: 800;
    color: #334155;
    margin-bottom: 4px;
}
.mob-empty-feed p {
    font-size: 11px;
    color: #64748b;
    margin: 0;
}
</style>

<div class="pos-mob-container">
    {{-- 1. Mobile Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-calculator text-warning"></i> Collect Fees
            </div>
            <span class="mob-session-pill">{{ $currentSessionName }}</span>
        </div>
        <div class="mob-hero-desc">
            Fast Point-of-Sale Fee Collection Desk. Search student &amp; tap to collect fees.
        </div>
        <div class="mob-kpi-grid">
            <div class="mob-kpi-box">
                <div class="mob-kpi-val" id="statLoadedStudents">{{ $studentsCount }}</div>
                <div class="mob-kpi-lbl">Students Loaded</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val">{{ count($classType ?? []) }}</div>
                <div class="mob-kpi-lbl">Total Classes</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val"><i class="fa fa-bolt text-warning"></i> POS</div>
                <div class="mob-kpi-lbl">Instant Receipt</div>
            </div>
        </div>
    </div>

    {{-- 2. Search Box --}}
    <div class="mob-search-box">
        <i class="fa fa-search search-icon"></i>
        <input type="text" id="mobStudentSearch" class="mob-search-input" placeholder="Search by Student Name, Adm No, Mobile, Father..." autocomplete="off">
        <button type="button" id="mobSearchClear" class="mob-search-clear"><i class="fa fa-times"></i></button>
    </div>

    {{-- Class Filter Chips --}}
    <div class="mob-chips-strip" id="mobClassChips">
        <div class="mob-chip active" data-class="">All Classes</div>
        @if(!empty($classType))
            @foreach($classType as $cls)
                <div class="mob-chip" data-class="{{ $cls->id }}">{{ $cls->name }}</div>
            @endforeach
        @endif
    </div>

    {{-- RTE / Mode Filter Chips --}}
    <div class="mob-chips-strip" id="mobRteChips" style="padding-top: 0; margin-top: -2px;">
        <div class="mob-chip active" data-rte="">All Categories</div>
        <div class="mob-chip" data-rte="1">Non-RTE Regular</div>
        <div class="mob-chip" data-rte="2">RTE Students</div>
    </div>

    {{-- 3. Student Card Feed --}}
    <div class="mob-feed-header">
        <span class="mob-feed-count" id="mobListCount"><i class="fa fa-users mr-1"></i> Showing {{ $studentsCount }} Students</span>
        <span class="text-muted" style="font-size: 10.5px;">Tap card to open fee desk</span>
    </div>

    <div class="mob-cards-feed" id="mobStudentsContainer">
        @forelse($data ?? [] as $stu)
            @php
                $isRte = ($stu->admission_type_id == 2);
                $safeName = trim(($stu->first_name ?? '') . ' ' . ($stu->last_name ?? ''));
                $safeAdm = $stu->admissionNo ?? ($stu->id ?? '—');
                $safeClass = $stu->ClassTypes->name ?? '—';
                $safeFather = $stu->father_name ?? '—';
                $safeMobile = (!empty($stu->mobile) && $stu->mobile !== '-') ? $stu->mobile : '';
                $safeUnique = $stu->unique_system_id ?? '';
            @endphp
            <div class="mob-student-card" 
                 data-id="{{ $stu->id }}"
                 data-unique="{{ $safeUnique }}"
                 data-name="{{ strtolower($safeName) }}"
                 data-adm="{{ strtolower($safeAdm) }}"
                 data-father="{{ strtolower($safeFather) }}"
                 data-mobile="{{ $safeMobile }}"
                 data-class-id="{{ $stu->class_type_id ?? '' }}"
                 data-rte="{{ $stu->admission_type_id ?? '' }}"
                 onclick="openMobFeeDesk({{ $stu->id }}, '{{ $safeUnique }}', '{{ addslashes($safeName) }}', '{{ $safeAdm }}')">
                <div class="mob-card-head">
                    <span class="mob-adm-badge">ADM #{{ $safeAdm }}</span>
                    <div class="mob-badges-wrap">
                        <span class="mob-class-badge">{{ $safeClass }}</span>
                        @if($isRte)
                            <span class="mob-rte-badge">RTE</span>
                        @endif
                    </div>
                </div>
                <div class="mob-card-name">{{ $safeName }}</div>
                <div class="mob-meta-row">
                    <div class="mob-meta-item">
                        <i class="fa fa-user text-muted"></i> F: {{ $safeFather }}
                    </div>
                    @if(!empty($safeMobile))
                        <div class="mob-meta-item">
                            <i class="fa fa-phone text-success"></i> {{ $safeMobile }}
                        </div>
                    @endif
                </div>
                <div class="mob-card-footer">
                    @if(!empty($safeMobile))
                        <a href="tel:{{ $safeMobile }}" class="btn-mob-call" onclick="event.stopPropagation();">
                            <i class="fa fa-phone"></i> Call
                        </a>
                    @else
                        <span></span>
                    @endif
                    <button type="button" class="btn-mob-collect">
                        <i class="fa fa-inr"></i> Collect Fees <i class="fa fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="mob-empty-feed">
                <i class="fa fa-user-circle-o"></i>
                <h5>No Students Found</h5>
                <p>Type in the search box to find students by name, mobile, or admission number.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- 4. Mobile Fee Collection Terminal Overlay / Bottom Sheet --}}
<div class="mob-sheet-overlay" id="mobSheetOverlay" onclick="closeMobFeeDesk()"></div>
<div class="mob-collect-sheet" id="mobFeeDeskSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title-wrap">
            <span class="mob-adm-badge" id="sheetAdmBadge">ADM</span>
            <h4 class="mob-sheet-title" id="sheetStudentTitle">Student Fee Desk</h4>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeMobFeeDesk()" title="Close Fee Desk">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <div class="mob-sheet-body" id="student_fees_detail">
        {{-- Filled via AJAX from /student_fees_onclick --}}
    </div>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
var CURRENT_SESSION_ID = "{{ $currentSessionId }}";
var BASEURL = "{{ url('/') }}";
var selectedClassId = '';
var selectedRte = '';
var activeSearchAjax = null;
var targetStudentId = "{{ $targetStudentId ?? '' }}";

function filterClientCards() {
    var query = $('#mobStudentSearch').val().toLowerCase().trim();
    var cards = $('#mobStudentsContainer .mob-student-card');
    var visible = 0;

    cards.each(function() {
        var card = $(this);
        var name = card.data('name') || '';
        var adm = card.data('adm') || '';
        var father = card.data('father') || '';
        var mobile = card.data('mobile') || '';
        var classId = card.data('class-id');
        var rte = card.data('rte');

        var matchQuery = !query || name.includes(query) || adm.includes(query) || father.includes(query) || mobile.includes(query);
        var matchClass = !selectedClassId || (classId == selectedClassId);
        var matchRte = !selectedRte || (rte == selectedRte);

        if (matchQuery && matchClass && matchRte) {
            card.show();
            visible++;
        } else {
            card.hide();
        }
    });

    $('#mobListCount').html('<i class="fa fa-users mr-1"></i> Showing ' + visible + ' Students');
    if (visible === 0 && query.length >= 2) {
        // Trigger server search if no client-side matches
        performServerLiveSearch(query);
    }
}

// Server-side AJAX Search for deep lookup
function performServerLiveSearch(query) {
    if (activeSearchAjax) activeSearchAjax.abort();

    activeSearchAjax = $.ajax({
        url: BASEURL + '/Fees/add',
        type: 'GET',
        data: {
            ajax_search: 1,
            name: query,
            class_type_id: selectedClassId,
            admission_type_id: selectedRte
        },
        dataType: 'json',
        success: function(res) {
            if (res && res.status === 'success' && res.students && res.students.length > 0) {
                renderServerStudents(res.students);
            }
        }
    });
}

function renderServerStudents(students) {
    var container = $('#mobStudentsContainer');
    container.empty();
    $('#mobListCount').html('<i class="fa fa-users mr-1"></i> Showing ' + students.length + ' Students');
    $('#statLoadedStudents').text(students.length);

    students.forEach(function(stu) {
        var safeName = (stu.first_name || '') + ' ' + (stu.last_name || '');
        var safeAdm = stu.admissionNo || stu.id || '—';
        var safeClass = stu.class_name || '—';
        var safeFather = stu.father_name || '—';
        var safeMobile = (stu.mobile && stu.mobile !== '-') ? stu.mobile : '';
        var safeUnique = stu.unique_system_id || '';
        var isRte = (stu.admission_type_id == 2);

        var cardHtml = `
            <div class="mob-student-card"
                 data-id="${stu.id}"
                 data-unique="${safeUnique}"
                 data-name="${safeName.toLowerCase()}"
                 data-adm="${String(safeAdm).toLowerCase()}"
                 data-father="${safeFather.toLowerCase()}"
                 data-mobile="${safeMobile}"
                 data-class-id="${stu.class_type_id || ''}"
                 data-rte="${stu.admission_type_id || ''}"
                 onclick="openMobFeeDesk(${stu.id}, '${safeUnique}', '${safeName.replace(/'/g, "\\'")}', '${safeAdm}')">
                <div class="mob-card-head">
                    <span class="mob-adm-badge">ADM #${safeAdm}</span>
                    <div class="mob-badges-wrap">
                        <span class="mob-class-badge">${safeClass}</span>
                        ${isRte ? '<span class="mob-rte-badge">RTE</span>' : ''}
                    </div>
                </div>
                <div class="mob-card-name">${safeName}</div>
                <div class="mob-meta-row">
                    <div class="mob-meta-item">
                        <i class="fa fa-user text-muted"></i> F: ${safeFather}
                    </div>
                    ${safeMobile ? `<div class="mob-meta-item"><i class="fa fa-phone text-success"></i> ${safeMobile}</div>` : ''}
                </div>
                <div class="mob-card-footer">
                    ${safeMobile ? `<a href="tel:${safeMobile}" class="btn-mob-call" onclick="event.stopPropagation();"><i class="fa fa-phone"></i> Call</a>` : '<span></span>'}
                    <button type="button" class="btn-mob-collect">
                        <i class="fa fa-inr"></i> Collect Fees <i class="fa fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        `;
        container.append(cardHtml);
    });
}

// Open Bottom Sheet Fee Terminal
function openMobFeeDesk(admissionId, uniqueId, studentName, admNo) {
    $('#sheetAdmBadge').text('ADM #' + admNo);
    $('#sheetStudentTitle').text(studentName || 'Collect Fees');
    $('#mobSheetOverlay').addClass('active');
    $('#mobFeeDeskSheet').addClass('active');
    $('body').css('overflow', 'hidden');

    $('#student_fees_detail').html(`
        <div class="p-4 text-center my-4">
            <i class="fa fa-spinner fa-spin fa-2x mb-2" style="color: #002C54;"></i>
            <h5 class="font-weight-bold" style="font-size: 13.5px; color: #002C54; margin-bottom: 4px;">Loading Fee Ledger...</h5>
            <p class="text-muted small mb-0">Computing fee heads, dues &amp; receipt records</p>
        </div>
    `);

    $.ajax({
        headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') },
        type: 'post',
        url: BASEURL + '/student_fees_onclick',
        data: {
            admission_id: admissionId,
            unique_system_id: (uniqueId && uniqueId !== 'null') ? uniqueId : '',
            session_id: CURRENT_SESSION_ID
        },
        success: function(data) {
            if (data == 0) {
                $('#student_fees_detail').html(`
                    <div class="mob-empty-feed">
                        <i class="fa fa-exclamation-triangle text-warning"></i>
                        <h5>No Fees Assigned</h5>
                        <p>Please assign fee structure in Fees Master before collecting payment.</p>
                    </div>
                `);
            } else {
                $('#student_fees_detail').html(data);
            }
        },
        error: function() {
            $('#student_fees_detail').html(`
                <div class="mob-empty-feed">
                    <i class="fa fa-times-circle text-danger"></i>
                    <h5>Error Loading Ledger</h5>
                    <p>Failed to fetch fee details. Please check connection and try again.</p>
                </div>
            `);
        }
    });
}

function closeMobFeeDesk() {
    $('#mobSheetOverlay').removeClass('active');
    $('#mobFeeDeskSheet').removeClass('active');
    $('body').css('overflow', '');
}

$(document).ready(function() {
    // Search input handlers
    $('#mobStudentSearch').on('input', function() {
        var val = $(this).val();
        $('#mobSearchClear').toggle(val.length > 0);
        filterClientCards();
    });

    $('#mobSearchClear').on('click', function() {
        $('#mobStudentSearch').val('').trigger('input');
        $(this).hide();
    });

    // Class chips toggle
    $('#mobClassChips .mob-chip').on('click', function() {
        $('#mobClassChips .mob-chip').removeClass('active');
        $(this).addClass('active');
        selectedClassId = $(this).data('class');
        filterClientCards();
    });

    // RTE chips toggle
    $('#mobRteChips .mob-chip').on('click', function() {
        $('#mobRteChips .mob-chip').removeClass('active');
        $(this).addClass('active');
        selectedRte = $(this).data('rte');
        filterClientCards();
    });

    // Auto-open if targetStudentId supplied
    if (targetStudentId) {
        var $targetCard = $('#mobStudentsContainer .mob-student-card[data-id="' + targetStudentId + '"]');
        if ($targetCard.length) {
            $targetCard.trigger('click');
        } else {
            openMobFeeDesk(targetStudentId, '', 'Student Fee Desk', targetStudentId);
        }
    }
});
</script>
@endsection
