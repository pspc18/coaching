@php
    $getUser = Helper::getUser();
    $studentName = Session::get('first_name') ?: ($getUser->first_name ?? 'Student');
    $className = optional($getUser)->ClassTypes->name ?? 'Student';
@endphp
@extends('student_login.layout.app')

@section('title', 'Raise New Complaint')
@section('page_title', 'NEW COMPLAINT')
@section('page_sub', $studentName . ' · ' . $className)

@section('styles')
<style>
.mob-complaint-create-page {
    padding: 0 0 calc(var(--bottom-nav-height, 52px) + 24px + var(--safe-bottom, 0px));
    color: #1e293b;
    width: 100%;
    box-sizing: border-box;
}

/* Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002244 55%, #002C54 100%);
    border-radius: 4px;
    padding: 14px 14px 12px;
    color: #ffffff;
    box-shadow: 0 6px 20px -2px rgba(0, 24, 51, 0.28);
    position: relative;
    overflow: hidden;
    margin-bottom: 10px;
}

.mob-hero-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.mob-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}

.mob-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    text-decoration: none;
    font-size: 13px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.mob-back-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

.mob-hero-title {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    line-height: 1.25;
}

.mob-hero-sub {
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 2px;
}

/* Form Container Card */
.mob-form-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.mob-form-group {
    margin-bottom: 12px;
}

.mob-form-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}

.mob-form-label .req {
    color: #ef4444;
}

.mob-form-control, .mob-form-select {
    width: 100%;
    height: 34px;
    padding: 0 10px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 12px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    box-sizing: border-box;
}

.mob-form-control:focus, .mob-form-select:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

.mob-form-textarea {
    width: 100%;
    min-height: 110px;
    padding: 9px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 12px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    resize: vertical;
    box-sizing: border-box;
    line-height: 1.55;
}

.mob-form-textarea:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

.mob-radio-toggle {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.mob-radio-opt {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 10px;
    text-align: center;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #f8fafc;
    transition: all 0.15s ease;
}

.mob-radio-opt input[type="radio"] {
    display: none;
}

.mob-radio-opt.active {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}

.mob-file-box {
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 9px 12px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mob-file-box input[type="file"] {
    font-size: 11px;
    color: #64748b;
    width: 100%;
}

.mob-form-actions {
    display: grid;
    grid-template-columns: 1fr 1.6fr;
    gap: 8px;
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.mob-btn {
    height: 36px;
    padding: 0 14px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
}

.mob-btn.secondary {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #475569;
}

.mob-btn.primary {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}

.mob-btn.primary:hover {
    background: #001833;
}

/* Dark Theme Adjustments */
:root[data-theme="dark"] .mob-form-box { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-form-control, :root[data-theme="dark"] .mob-form-select, :root[data-theme="dark"] .mob-form-textarea { background: #0f172a; border-color: #334155; color: #f8fafc; }
:root[data-theme="dark"] .mob-form-label { color: #cbd5e1; }
:root[data-theme="dark"] .mob-radio-opt { background: #0f172a; border-color: #334155; color: #cbd5e1; }
:root[data-theme="dark"] .mob-radio-opt.active { background: #0284c7; border-color: #0284c7; color: #ffffff; }
:root[data-theme="dark"] .mob-file-box { background: #0f172a; border-color: #334155; }
:root[data-theme="dark"] .mob-btn.secondary { background: #1e293b; border-color: #334155; color: #cbd5e1; }
</style>
@endsection

@section('content')
<div class="mob-complaint-create-page">

    {{-- Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-head">
            <div class="mob-hero-title-wrap">
                <a href="{{ url('student-complaints') }}" class="mob-back-btn" title="Back to Complaints">
                    <i class="fa fa-chevron-left"></i>
                </a>
                <div>
                    <h1 class="mob-hero-title">Submit a Complaint</h1>
                    <div class="mob-hero-sub">Direct channel to school administration</div>
                </div>
            </div>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size:10px; border-radius:3px;">
                <i class="fa fa-shield"></i> Official Ticket
            </span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small mb-2" style="border-radius:4px; font-size:11px;">
            <i class="fa fa-exclamation-circle me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- Form Box --}}
    <div class="mob-form-box">
        <form id="complaintCreateForm" method="POST" action="{{ url('student-complaints') }}" enctype="multipart/form-data">
            @csrf

            {{-- Submitting As --}}
            <div class="mob-form-group">
                <label class="mob-form-label">Submitting As <span class="req">*</span></label>
                <div class="mob-radio-toggle">
                    <label class="mob-radio-opt {{ old('submitted_as', 'parent') === 'parent' ? 'active' : '' }}" id="lblSubmitParent">
                        <input type="radio" name="submitted_as" value="parent" {{ old('submitted_as', 'parent') === 'parent' ? 'checked' : '' }} onchange="toggleSubmitAs(this)">
                        <i class="fa fa-users"></i> Parent / Guardian
                    </label>
                    <label class="mob-radio-opt {{ old('submitted_as') === 'student' ? 'active' : '' }}" id="lblSubmitStudent">
                        <input type="radio" name="submitted_as" value="student" {{ old('submitted_as') === 'student' ? 'checked' : '' }} onchange="toggleSubmitAs(this)">
                        <i class="fa fa-user"></i> Student
                    </label>
                </div>
            </div>

            {{-- Subject --}}
            <div class="mob-form-group">
                <label class="mob-form-label">Concern Subject <span class="req">*</span></label>
                <input type="text"
                       name="subject"
                       class="mob-form-control"
                       placeholder="Brief summary of your concern..."
                       maxlength="255"
                       value="{{ old('subject') }}"
                       required>
            </div>

            {{-- 2 Columns: Category & Priority --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div class="mob-form-group">
                    <label class="mob-form-label">Category <span class="req">*</span></label>
                    <select name="category" class="mob-form-select" required>
                        @foreach(\App\Models\SupportComplaint::CATEGORIES as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label">Priority <span class="req">*</span></label>
                    <select name="priority" class="mob-form-select" required>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </div>

            {{-- Message Details --}}
            <div class="mob-form-group">
                <label class="mob-form-label">Complaint Details <span class="req">*</span></label>
                <textarea name="message"
                          class="mob-form-textarea"
                          rows="5"
                          placeholder="Explain what happened, when it occurred, and what resolution you expect..."
                          maxlength="10000"
                          required>{{ old('message') }}</textarea>
            </div>

            {{-- Attachment --}}
            <div class="mob-form-group">
                <label class="mob-form-label">
                    Attachment <span style="font-weight: normal; color: #94a3b8;">(Optional, PDF / Image max 10MB)</span>
                </label>
                <div class="mob-file-box">
                    <i class="fa fa-paperclip text-muted"></i>
                    <input type="file" name="attachment" id="complaintAttachment" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="mob-form-actions">
                <a href="{{ url('student-complaints') }}" class="mob-btn secondary">
                    Cancel
                </a>
                <button type="submit" class="mob-btn primary" id="btnSubmitComplaint">
                    <i class="fa fa-paper-plane"></i> Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
function toggleSubmitAs(radio) {
    document.getElementById('lblSubmitParent').classList.toggle('active', radio.value === 'parent');
    document.getElementById('lblSubmitStudent').classList.toggle('active', radio.value === 'student');
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('complaintCreateForm');
    const submitBtn = document.getElementById('btnSubmitComplaint');
    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
        });
    }
});
</script>
@endsection
