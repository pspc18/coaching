@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $getUser = Helper::getUser();
    $studentName = trim(($getUser->first_name ?? '') . ' ' . ($getUser->last_name ?? ''));
    if (empty($studentName)) $studentName = 'Student';
    $className = optional($getUser->ClassTypes)->name ?? '-';
@endphp

@extends('student_login.layout.app')

@section('title', 'Edit Leave Application')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT EDIT LEAVE (SHARP CARDS + NAVY THEME)
   ========================================================================== */

/* Top Sub-Header Navigation */
.native-page-nav-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 0 2px;
}
.page-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    color: #0284c7;
    text-decoration: none !important;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.page-back-link:active {
    background: #f1f5f9;
}

/* Sharp Card */
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
    background: #fef3c7 !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
}

/* Form Controls */
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

/* Form Action Buttons */
.form-actions-row {
    display: flex;
    gap: 8px;
    margin-top: 14px;
}
.btn-submit-leave {
    flex: 1;
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
    transition: transform .12s ease;
}
.btn-cancel-leave {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 14px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;
}
</style>

{{-- Sub-Header Navigation --}}
<div class="native-page-nav-strip">
    <a href="{{ url('applyLeaveStudent') }}" class="page-back-link">
        <i class="fa fa-arrow-left"></i> Back to Leave Desk
    </a>
    <span class="badge" style="background:#ffffff;border:1px solid #cbd5e1;color:#64748b;font-size:9.5px;padding:4px 8px;">
        {{ $className }}
    </span>
</div>

{{-- Edit Form Card --}}
<div class="sharp-leave-card">
    <div class="sharp-card-head">
        <h3 class="sharp-card-title">
            <i class="fa fa-pencil text-primary"></i> Edit Leave Application
        </h3>
        <span class="sharp-card-badge">
            <i class="fa fa-clock-o" style="margin-right: 4px;"></i> Pending
        </span>
    </div>

    <form id="leaveEditForm" action="{{ url('updateLeaveStudent/' . $data->id) }}" method="post">
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
                   value="{{ old('subject', $data->subject) }}" 
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
                           value="{{ old('from_date', $data->from_date) }}" 
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
                           value="{{ old('to_date', $data->to_date) }}" 
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
                      rows="4" 
                      required>{{ old('reason', $data->reason) }}</textarea>
            @error('reason')
                <div style="color: #ef4444; font-size: 9.5px; font-weight: 700; margin-top: 2px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="form-actions-row">
            <a href="{{ url('applyLeaveStudent') }}" class="btn-cancel-leave">
                Cancel
            </a>
            <button type="submit" class="btn-submit-leave">
                <i class="fa fa-check" style="margin-right: 4px;"></i> Update Leave Application
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
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
});
</script>
@endsection