@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $getUser = Helper::getUser();
    $studentName = trim(($getUser->first_name ?? '') . ' ' . ($getUser->last_name ?? ''));
    if (empty($studentName)) $studentName = 'Student';
    $className = optional($getUser->ClassTypes)->name ?? '-';

    $totalLeaves = count($dataview ?? []);
    $approvedCount = 0;
    $pendingCount = 0;
    $deniedCount = 0;

    foreach ($dataview ?? [] as $lv) {
        if ($lv->status == '1') {
            $approvedCount++;
        } elseif ($lv->status == '0') {
            $deniedCount++;
        } else {
            $pendingCount++;
        }
    }
@endphp

@extends('student_login.layout.app')

@section('title', 'Apply Student Leave')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT LEAVE MANAGEMENT (SHARP CARDS + NAVY THEME)
   ========================================================================== */

/* 1. Flash Success Notification */
.leave-flash-banner {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 4px;
    color: #065f46;
    padding: 8px 12px;
    margin-bottom: 8px;
    font-size: 11.5px;
    font-weight: 700;
}
.leave-flash-banner i {
    font-size: 14px;
    color: #10b981;
}

/* 2. Top Hero Glance Card */
.native-leave-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #0a4275 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 12px 14px;
    color: #ffffff;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 24, 51, 0.22);
}
.hero-leave-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.hero-leave-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.01em;
}
.hero-leave-title i {
    color: #38bdf8;
    font-size: 14px;
}
.hero-leave-sub {
    font-size: 9px;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .03em;
}

/* Hero 4-tile Metrics Matrix */
.hero-metrics-matrix {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
}
.hero-matrix-box {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
}
.hero-matrix-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
}
.hero-matrix-num {
    font-size: 15px;
    font-weight: 800;
    line-height: 1.2;
    margin-top: 2px;
}
.num-total { color: #f8fafc; }
.num-approved { color: #4ade80; }
.num-pending { color: #facc15; }
.num-denied { color: #f87171; }

/* 3. Sharp Section Cards */
.sharp-leave-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 12px 14px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.sharp-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.sharp-card-title {
    font-size: 12px !important;
    font-weight: 800 !important;
    color: #002C54 !important;
    margin: 0 !important;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.25 !important;
}
.sharp-card-title i {
    margin-right: 6px;
    font-size: 13px;
}
.sharp-card-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 3px 8px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    border-radius: 4px !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
}

/* 4. Quick Suggestion Chips */
.quick-chips-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 6px;
    margin-bottom: 10px;
    scrollbar-width: none;
}
.quick-chips-wrap::-webkit-scrollbar {
    display: none;
}
.quick-chip-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    color: #002C54;
    white-space: nowrap;
    cursor: pointer;
    transition: all .15s ease;
}
.quick-chip-btn:hover, .quick-chip-btn:active {
    background: #e0f2fe;
    border-color: #38bdf8;
    color: #0369a1;
}

/* 5. Clean Form Controls */
.leave-field-group {
    margin-bottom: 10px;
}
.leave-field-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
}
.leave-field-label span.req {
    color: #ef4444;
}
.leave-form-control {
    width: 100%;
    padding: 7px 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    transition: border-color .15s ease, box-shadow .15s ease;
    outline: none;
}
.leave-form-control:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.leave-dates-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.duration-indicator-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 9.5px;
    font-weight: 700;
    color: #0284c7;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 3px;
    padding: 2px 6px;
    margin-top: 4px;
}

/* Form Action Button */
.btn-submit-leave {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 14px;
    background: linear-gradient(135deg, #001833 0%, #002C54 70%, #0284c7 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 24, 51, 0.18);
    transition: transform .12s ease, opacity .12s ease;
}
.btn-submit-leave:active {
    transform: scale(0.99);
    opacity: 0.95;
}

/* 6. Leave Applications Filter Tabs */
.leave-filter-tabs {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    margin-bottom: 10px;
    padding-bottom: 2px;
    scrollbar-width: none;
}
.leave-filter-tabs::-webkit-scrollbar {
    display: none;
}
.leave-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 9px;
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .15s ease;
}
.leave-tab-btn.is-active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 7. Leave Card in History List */
.leave-history-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.leave-item-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    transition: background .12s ease, border-color .12s ease;
}
.leave-item-card:hover {
    background: #ffffff;
    border-color: #94a3b8;
}
.leave-item-card.is-hidden {
    display: none !important;
}

.leave-card-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 5px;
}
.leave-card-subject {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
}

/* Status Chips */
.leave-status-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .02em;
    flex-shrink: 0;
}
.chip-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.chip-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.chip-denied { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

/* Date & Duration Row */
.leave-card-dates-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    color: #475569;
    font-weight: 600;
    margin-bottom: 6px;
}
.leave-dates-txt {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.leave-dates-txt i {
    color: #64748b;
    font-size: 11px;
}
.leave-days-badge {
    font-size: 9px;
    font-weight: 700;
    background: #e2e8f0;
    color: #334155;
    padding: 1px 5px;
    border-radius: 3px;
}

/* Reason Box */
.leave-card-reason-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    font-size: 10.5px;
    color: #334155;
    line-height: 1.4;
    word-break: break-word;
}

/* Actions Footer */
.leave-card-actions-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed #e2e8f0;
}
.leave-act-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 3px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    line-height: 1.2;
}
.btn-act-edit {
    background: #e0f2fe;
    color: #0369a1;
    border-color: #bae6fd;
}
.btn-act-edit:hover {
    background: #bae6fd;
    color: #0284c7;
}
.btn-act-delete {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fecaca;
}
.btn-act-delete:hover {
    background: #fecaca;
    color: #991b1b;
}

/* Empty State */
.leave-empty-state {
    text-align: center;
    padding: 24px 10px;
    color: #94a3b8;
    font-size: 11px;
}
.leave-empty-state i {
    font-size: 26px;
    margin-bottom: 6px;
    display: block;
    color: #cbd5e1;
}

/* 8. Modern Delete Modal */
.modal-content.leave-modal-skin {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.modal-header.leave-modal-head {
    background: #001833;
    color: #ffffff;
    padding: 10px 14px;
    border-bottom: none;
}
.modal-header.leave-modal-head .modal-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.modal-body.leave-modal-body {
    padding: 14px;
    font-size: 11.5px;
    color: #334155;
}
.modal-footer.leave-modal-foot {
    padding: 8px 14px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}
</style>

{{-- Flash Success Message --}}
@if(session('message'))
    <div class="leave-flash-banner">
        <i class="fa fa-check-circle"></i>
        <span>{{ session('message') }}</span>
    </div>
@endif

{{-- 1. Top Hero Glance Card --}}
<div class="native-leave-hero">
    <div class="hero-leave-head">
        <div>
            <div class="hero-leave-title">
                <i class="fa fa-calendar-minus-o"></i> Student Leave Desk
            </div>
            <span class="hero-leave-sub">Track & Apply Attendance Exemption</span>
        </div>
        <span class="badge" style="background: rgba(255,255,255,0.12); color:#e2e8f0; font-size:9.5px; padding:3px 7px;">
            {{ $className }}
        </span>
    </div>

    {{-- 4-Metric Glance Matrix --}}
    <div class="hero-metrics-matrix">
        <div class="hero-matrix-box">
            <span class="hero-matrix-lbl">Total</span>
            <div class="hero-matrix-num num-total">{{ $totalLeaves }}</div>
        </div>
        <div class="hero-matrix-box">
            <span class="hero-matrix-lbl">Approved</span>
            <div class="hero-matrix-num num-approved">{{ $approvedCount }}</div>
        </div>
        <div class="hero-matrix-box">
            <span class="hero-matrix-lbl">Pending</span>
            <div class="hero-matrix-num num-pending">{{ $pendingCount }}</div>
        </div>
        <div class="hero-matrix-box">
            <span class="hero-matrix-lbl">Denied</span>
            <div class="hero-matrix-num num-denied">{{ $deniedCount }}</div>
        </div>
    </div>
</div>

{{-- 2. Apply New Leave Card --}}
<div class="sharp-leave-card">
    <div class="sharp-card-head">
        <h3 class="sharp-card-title">
            <i class="fa fa-pencil-square-o text-primary"></i> Apply For Leave
        </h3>
        <span class="sharp-card-badge">
            <i class="fa fa-user-circle-o" style="margin-right: 4px;"></i> {{ $studentName }}
        </span>
    </div>

    {{-- Fast Quick Subject Chips --}}
    <div style="font-size: 9.5px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">
        Quick Reasons:
    </div>
    <div class="quick-chips-wrap">
        <button type="button" class="quick-chip-btn" data-subj="Sick Leave">🤒 Sick Leave</button>
        <button type="button" class="quick-chip-btn" data-subj="Family Function">👨‍👩‍👧 Family Function</button>
        <button type="button" class="quick-chip-btn" data-subj="Medical Checkup">🏥 Medical Checkup</button>
        <button type="button" class="quick-chip-btn" data-subj="Urgent Work">💼 Urgent Work</button>
        <button type="button" class="quick-chip-btn" data-subj="Out of Station">🚗 Out of Station</button>
    </div>

    <form id="leaveApplyForm" action="{{ url('applyLeaveStudent') }}" method="post">
        @csrf

        {{-- Subject Field --}}
        <div class="leave-field-group">
            <label class="leave-field-label" for="subject">
                <span>Subject / Title <span class="req">*</span></span>
            </label>
            <input type="text" 
                   class="leave-form-control @error('subject') is-invalid @enderror" 
                   id="subject" 
                   name="subject" 
                   placeholder="e.g. Leave application for viral fever" 
                   value="{{ old('subject') }}" 
                   required>
            @error('subject')
                <div style="color: #ef4444; font-size: 9.5px; font-weight: 700; margin-top: 2px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Date Range Fields --}}
        <div class="leave-field-group">
            <div class="leave-dates-row">
                <div>
                    <label class="leave-field-label" for="from_date">
                        <span>From Date <span class="req">*</span></span>
                    </label>
                    <input type="date" 
                           class="leave-form-control @error('from_date') is-invalid @enderror" 
                           id="from_date" 
                           name="from_date" 
                           value="{{ old('from_date', date('Y-m-d')) }}" 
                           required>
                    @error('from_date')
                        <div style="color: #ef4444; font-size: 9.5px; font-weight: 700; margin-top: 2px;">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="leave-field-label" for="to_date">
                        <span>To Date <span class="req">*</span></span>
                    </label>
                    <input type="date" 
                           class="leave-form-control @error('to_date') is-invalid @enderror" 
                           id="to_date" 
                           name="to_date" 
                           value="{{ old('to_date', date('Y-m-d')) }}" 
                           required>
                    @error('to_date')
                        <div style="color: #ef4444; font-size: 9.5px; font-weight: 700; margin-top: 2px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="duration-indicator-pill" id="durationIndicator">
                <i class="fa fa-clock-o"></i> <span>Duration: 1 Day</span>
            </div>
        </div>

        {{-- Reason Field --}}
        <div class="leave-field-group">
            <label class="leave-field-label" for="reason">
                <span>Reason / Explanation <span class="req">*</span></span>
            </label>
            <textarea class="leave-form-control @error('reason') is-invalid @enderror" 
                      id="reason" 
                      name="reason" 
                      rows="3" 
                      placeholder="Please mention brief details for your leave request..." 
                      required>{{ old('reason') }}</textarea>
            @error('reason')
                <div style="color: #ef4444; font-size: 9.5px; font-weight: 700; margin-top: 2px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-submit-leave">
            <i class="fa fa-paper-plane" style="margin-right: 6px;"></i> Submit Leave Application
        </button>
    </form>
</div>

{{-- 3. Leave Applications History Card --}}
<div class="sharp-leave-card">
    <div class="sharp-card-head">
        <h3 class="sharp-card-title">
            <i class="fa fa-history text-primary"></i> Applied Leaves History
        </h3>
        <span class="sharp-card-badge" id="historyCounterBadge">
            {{ $totalLeaves }} Records
        </span>
    </div>

    {{-- Filter Tabs --}}
    <div class="leave-filter-tabs">
        <button type="button" class="leave-tab-btn is-active" data-filter="all">
            All ({{ $totalLeaves }})
        </button>
        <button type="button" class="leave-tab-btn" data-filter="pending">
            Pending ({{ $pendingCount }})
        </button>
        <button type="button" class="leave-tab-btn" data-filter="approved">
            Approved ({{ $approvedCount }})
        </button>
        <button type="button" class="leave-tab-btn" data-filter="denied">
            Denied ({{ $deniedCount }})
        </button>
    </div>

    {{-- History Cards List --}}
    <div class="leave-history-list" id="leaveHistoryList">
        @forelse($dataview as $item)
            @php
                $statusKey = 'pending';
                $statusLabel = 'Pending';
                $chipClass = 'chip-pending';
                $statusIcon = 'fa-clock-o';

                if ($item->status == '1') {
                    $statusKey = 'approved';
                    $statusLabel = 'Approved';
                    $chipClass = 'chip-approved';
                    $statusIcon = 'fa-check-circle';
                } elseif ($item->status == '0') {
                    $statusKey = 'denied';
                    $statusLabel = 'Denied';
                    $chipClass = 'chip-denied';
                    $statusIcon = 'fa-times-circle';
                }

                $fromFormatted = !empty($item->from_date) ? date('d-M-Y', strtotime($item->from_date)) : '-';
                $toFormatted = !empty($item->to_date) ? date('d-M-Y', strtotime($item->to_date)) : '-';

                $daysCount = 1;
                if (!empty($item->from_date) && !empty($item->to_date)) {
                    $d1 = \Carbon\Carbon::parse($item->from_date);
                    $d2 = \Carbon\Carbon::parse($item->to_date);
                    $daysCount = max(1, $d1->diffInDays($d2) + 1);
                }
            @endphp
            <div class="leave-item-card" data-status="{{ $statusKey }}">
                <div class="leave-card-top-row">
                    <span class="leave-card-subject">{{ $item->subject ?? 'Leave Request' }}</span>
                    <span class="leave-status-chip {{ $chipClass }}">
                        <i class="fa {{ $statusIcon }}"></i> {{ $statusLabel }}
                    </span>
                </div>

                <div class="leave-card-dates-row">
                    <div class="leave-dates-txt">
                        <i class="fa fa-calendar-o"></i>
                        <span>{{ $fromFormatted }} &rarr; {{ $toFormatted }}</span>
                    </div>
                    <span class="leave-days-badge">{{ $daysCount }} {{ $daysCount == 1 ? 'Day' : 'Days' }}</span>
                </div>

                @if(!empty($item->reason))
                    <div class="leave-card-reason-box">
                        {{ $item->reason }}
                    </div>
                @endif

                @if($item->status == '2')
                    <div class="leave-card-actions-row">
                        <a href="{{ url('updateLeaveStudent/' . $item->id) }}" class="leave-act-btn btn-act-edit">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <button type="button" 
                                class="leave-act-btn btn-act-delete btnDeleteTrigger" 
                                data-id="{{ $item->id }}" 
                                data-subject="{{ $item->subject }}">
                            <i class="fa fa-trash"></i> Cancel
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="leave-empty-state">
                <i class="fa fa-calendar-check-o"></i>
                <span>No leave applications submitted yet.</span>
            </div>
        @endforelse
    </div>
</div>

{{-- Modern Delete Confirmation Modal --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 360px; margin: auto;">
        <div class="modal-content leave-modal-skin">
            <div class="modal-header leave-modal-head">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle text-warning"></i> Cancel Leave Application
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('deleteLeaveStudent') }}" method="post">
                @csrf
                <div class="modal-body leave-modal-body">
                    <input type="hidden" id="modalDeleteId" name="delete_id">
                    <p style="margin-bottom: 6px; font-weight: 600; color: #0f172a;">
                        Are you sure you want to cancel this leave application?
                    </p>
                    <div id="modalLeaveSubject" style="font-size: 11px; color: #64748b; font-style: italic;"></div>
                </div>
                <div class="modal-footer leave-modal-foot">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal" style="font-size:11px;font-weight:700;">
                        Keep It
                    </button>
                    <button type="submit" class="btn btn-sm btn-danger" style="font-size:11px;font-weight:700;">
                        Yes, Cancel Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Quick Chips Click Event
    const subjectInput = document.getElementById("subject");
    const reasonInput = document.getElementById("reason");
    const chips = document.querySelectorAll(".quick-chip-btn");

    chips.forEach(btn => {
        btn.addEventListener("click", function() {
            const subj = this.getAttribute("data-subj");
            if (subjectInput) {
                subjectInput.value = subj;
                subjectInput.focus();
            }
        });
    });

    // 2. Dynamic Duration Indicator Calculation
    const fromDateInput = document.getElementById("from_date");
    const toDateInput = document.getElementById("to_date");
    const durationIndicator = document.getElementById("durationIndicator");

    function updateDuration() {
        if (!fromDateInput || !toDateInput || !durationIndicator) return;
        const fVal = fromDateInput.value;
        const tVal = toDateInput.value;

        if (fVal && tVal) {
            const d1 = new Date(fVal);
            const d2 = new Date(tVal);
            const diffTime = d2.getTime() - d1.getTime();
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays < 1) {
                durationIndicator.innerHTML = `<i class="fa fa-exclamation-circle text-danger"></i> <span style="color:#ef4444;">To Date must be on or after From Date</span>`;
            } else {
                durationIndicator.innerHTML = `<i class="fa fa-clock-o"></i> <span>Duration: ${diffDays} ${diffDays === 1 ? 'Day' : 'Days'}</span>`;
            }
        }
    }

    if (fromDateInput && toDateInput) {
        fromDateInput.addEventListener("change", function() {
            if (toDateInput.value < this.value) {
                toDateInput.value = this.value;
            }
            updateDuration();
        });
        toDateInput.addEventListener("change", updateDuration);
        updateDuration();
    }

    // 3. Tab Filter Interactivity
    const filterTabs = document.querySelectorAll(".leave-tab-btn");
    const leaveCards = document.querySelectorAll(".leave-item-card");

    filterTabs.forEach(tab => {
        tab.addEventListener("click", function() {
            filterTabs.forEach(t => t.classList.remove("is-active"));
            this.classList.add("is-active");

            const filter = this.getAttribute("data-filter");
            let visibleCount = 0;

            leaveCards.forEach(card => {
                const status = card.getAttribute("data-status");
                if (filter === "all" || status === filter) {
                    card.classList.remove("is-hidden");
                    visibleCount++;
                } else {
                    card.classList.add("is-hidden");
                }
            });
        });
    });

    // 4. Delete Modal Trigger
    const deleteButtons = document.querySelectorAll(".btnDeleteTrigger");
    const modalDeleteId = document.getElementById("modalDeleteId");
    const modalLeaveSubject = document.getElementById("modalLeaveSubject");
    const deleteModalEl = document.getElementById("deleteConfirmModal");
    let deleteModal = null;
    if (deleteModalEl && window.bootstrap) {
        deleteModal = new bootstrap.Modal(deleteModalEl);
    }

    deleteButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.getAttribute("data-id");
            const subj = this.getAttribute("data-subject") || "Leave Application";

            if (modalDeleteId) modalDeleteId.value = id;
            if (modalLeaveSubject) modalLeaveSubject.textContent = `"${subj}"`;

            if (deleteModal) {
                deleteModal.show();
            } else if ($ && $('#deleteConfirmModal').modal) {
                $('#deleteConfirmModal').modal('show');
            }
        });
    });
});
</script>
@endsection