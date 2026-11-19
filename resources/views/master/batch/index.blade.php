@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exactly Matching admissionView & Dashboard */
.admission-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.admission-page * {
    box-sizing: border-box;
}

/* Top Hero Banner - Exact Arise ERP Style from admissionView / dash-hero */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 6px;
}
.admission-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #ffffff;
}
.admission-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #ffffff;
}
.admission-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
    color: #ffffff;
}
.admission-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}

/* Arise Unified Button System */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 26px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    line-height: 1;
    gap: 5px;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
    border-color: #f1f5f9;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255,255,255,0.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.15);
    color: #ffffff;
    border-color: #ffffff;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.dash-btn-primary:hover {
    background: #001f3d;
    color: #ffffff;
    border-color: #001f3d;
}

/* Sharp Compact KPI Metric Tiles - Matching admin_dashboard */
.dash-kpi {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    flex-direction: column;
    text-decoration: none !important;
    color: #0f172a !important;
    height: 100%;
    transition: border-color .15s, box-shadow .15s;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
.dash-kpi:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 5px rgba(0,0,0,.05);
}
.kpi-primary { border-top: 3px solid #002C54; }
.kpi-warning { border-top: 3px solid #f59e0b; }
.kpi-info    { border-top: 3px solid #0284c7; }
.kpi-danger  { border-top: 3px solid #ef4444; }
.kpi-success { border-top: 3px solid #10b981; }

.kpi-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
}
.kpi-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: .03em;
}
.kpi-icon {
    font-size: 13px;
    color: #94a3b8;
}
.kpi-primary .kpi-icon { color: #002C54; }
.kpi-warning .kpi-icon { color: #f59e0b; }
.kpi-info .kpi-icon    { color: #0284c7; }
.kpi-danger .kpi-icon  { color: #ef4444; }
.kpi-success .kpi-icon { color: #10b981; }

.kpi-value {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.15;
    color: #0f172a;
    margin-bottom: 2px;
}
.kpi-meta {
    font-size: 10px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Sharp Compact Cards - Arise ERP Style */
.dash-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
    border-radius: 2px 2px 0 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}
.badge-total-records {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 22px;
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.14);
    color: #f1f5f9;
    padding: 0 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.18);
    white-space: nowrap;
    line-height: 1;
}

/* Table Design - Exact Dark Navy Unified Header from admissionView */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
}
.header-titles-row th {
    background: #002C54;
    color: #ffffff;
    padding: 7px 8px;
    height: 34px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    vertical-align: middle;
    box-sizing: border-box;
}
.dash-table tbody td {
    padding: 6px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    font-size: 11.5px;
}
.dash-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.dash-table tbody tr:hover {
    background: #f1f5f9;
}

/* Badges for Financial Year, Shifts & Status */
.badge-fy-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 20px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 700;
    color: #065f46;
    background: #ecfdf5;
    border-radius: 2px;
    border: 1px solid #a7f3d0;
    white-space: nowrap;
}
.badge-course-tag {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    background: #eff6ff;
    border-radius: 2px;
    border: 1px solid #bfdbfe;
    white-space: nowrap;
}
.badge-shift-morning {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 20px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    white-space: nowrap;
}
.badge-shift-evening {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 20px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #ede9fe;
    color: #5b21b6;
    border: 1px solid #ddd6fe;
    white-space: nowrap;
}
.badge-shift-afternoon {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 20px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #ffedd5;
    color: #9a3412;
    border: 1px solid #fed7aa;
    white-space: nowrap;
}
.badge-shift-special {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    height: 20px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    white-space: nowrap;
}
.timing-tag {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 5px;
    font-size: 10.5px;
    color: #475569;
    background: #f1f5f9;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    font-family: monospace;
}
.badge-status-active {
    display: inline-flex;
    align-items: center;
    height: 18px;
    padding: 0 6px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
}
.badge-status-inactive {
    display: inline-flex;
    align-items: center;
    height: 18px;
    padding: 0 6px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* Action Icon Buttons */
.btn-action-icon {
    width: 24px;
    height: 24px;
    min-width: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s;
    line-height: 1;
    padding: 0;
}
.btn-action-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-action-edit:hover {
    background: #2563eb;
    color: #ffffff;
}
.btn-action-delete {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #ffffff;
}

/* Shift Preset Buttons */
.shift-preset-btn {
    padding: 2px 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    line-height: 1.4;
}
.shift-preset-btn:hover, .shift-preset-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.shift-preset-btn.active i {
    color: #38bdf8 !important;
}

/* Form Styles */
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
}
.form-control-compact {
    height: 28px;
    font-size: 11.5px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    padding: 3px 8px;
    color: #0f172a;
}
.form-control-compact:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* Modal Theme */
.modal-theme-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 8px 12px;
    border-radius: 2px 2px 0 0;
}
.modal-theme-header .modal-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="px-2 pt-1 pb-2">

                {{-- Alert Messages --}}
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show m-0 mb-2 py-1 px-3" role="alert" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-0 mb-2 py-1 px-3" role="alert" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                {{-- Top Hero Welcome Banner --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-users mr-1"></i> MASTER &middot; FINANCIAL YEAR BATCHES SETUP</span>
                        <h1 class="admission-title">Batches &amp; Financial Year Management</h1>
                        <p class="admission-subtitle">Track and configure Morning, Evening, and Afternoon batches separated by Financial Year (Session)</p>
                    </div>
                    <div class="admission-hero-actions">
                        <a href="{{ url('courses') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-graduation-cap mr-1"></i> Courses Master
                        </a>
                        <a href="{{ url('course_subjects') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-book mr-1"></i> Course Subjects
                        </a>
                        <a href="{{ url('admissionView') }}" class="dash-btn dash-btn-light">
                            <i class="fa fa-user-plus mr-1"></i> Student Admissions
                        </a>
                    </div>
                </div>

                {{-- Sharp Compact Metric KPI Tiles (Row of 5) --}}
                <div class="row row-compact mb-2">
                    {{-- 1. Total Batches --}}
                    <div class="col-6 col-sm-6 col-md-4 col-xl mb-1">
                        <div class="dash-kpi kpi-primary">
                            <div class="kpi-header">
                                <span class="kpi-label">Total Batches</span>
                                <span class="kpi-icon"><i class="fa fa-layer-group"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['total_batches'] ?? 0 }}</div>
                            <div class="kpi-meta">
                                @if($selectedSessionId === 'all')
                                    All Financial Years
                                @else
                                    FY {{ $sessions->firstWhere('id', $selectedSessionId)->from_year ?? '' }}-{{ $sessions->firstWhere('id', $selectedSessionId)->to_year ?? '' }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Morning Shifts --}}
                    <div class="col-6 col-sm-6 col-md-4 col-xl mb-1">
                        <div class="dash-kpi kpi-warning">
                            <div class="kpi-header">
                                <span class="kpi-label">Morning Shifts</span>
                                <span class="kpi-icon"><i class="fa fa-sun-o"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['morning_batches'] ?? 0 }}</div>
                            <div class="kpi-meta">08:00 AM - 12:00 PM</div>
                        </div>
                    </div>

                    {{-- 3. Evening Shifts --}}
                    <div class="col-6 col-sm-6 col-md-4 col-xl mb-1">
                        <div class="dash-kpi kpi-info">
                            <div class="kpi-header">
                                <span class="kpi-label">Evening Shifts</span>
                                <span class="kpi-icon"><i class="fa fa-moon-o"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['evening_batches'] ?? 0 }}</div>
                            <div class="kpi-meta">02:00 PM - 06:00 PM</div>
                        </div>
                    </div>

                    {{-- 4. Afternoon Shifts --}}
                    <div class="col-6 col-sm-6 col-md-4 col-xl mb-1">
                        <div class="dash-kpi kpi-danger">
                            <div class="kpi-header">
                                <span class="kpi-label">Afternoon Shifts</span>
                                <span class="kpi-icon"><i class="fa fa-clock-o"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['afternoon_batches'] ?? 0 }}</div>
                            <div class="kpi-meta">12:00 PM - 04:00 PM</div>
                        </div>
                    </div>

                    {{-- 5. Enrolled Students --}}
                    <div class="col-12 col-sm-12 col-md-4 col-xl mb-1">
                        <a class="dash-kpi kpi-success" href="{{ url('admissionView') }}">
                            <div class="kpi-header">
                                <span class="kpi-label">Enrolled Students</span>
                                <span class="kpi-icon"><i class="fa fa-user-check"></i></span>
                            </div>
                            <div class="kpi-value">{{ number_format($kpis['total_students'] ?? 0) }}</div>
                            <div class="kpi-meta">Allocated to Batches</div>
                        </a>
                    </div>
                </div>

                {{-- Main Content Grid: Form (Left) + Table (Right) --}}
                <div class="row">
                    {{-- Left Column: Add New Batch Form --}}
                    <div class="col-12 col-lg-4 mb-2">
                        <div class="dash-card">
                            <div class="dash-card-header d-flex align-items-center justify-content-between">
                                <h3 class="dash-card-title">
                                    <i class="fa fa-plus-circle text-info mr-1"></i> Add New Batch
                                </h3>
                                <span class="badge badge-success" style="font-size: 10px; border-radius: 2px;">
                                    <i class="fa fa-calendar-check-o mr-1"></i> FY Setup
                                </span>
                            </div>
                            <div class="p-2">
                                <form action="{{ url('batch_add') }}" method="POST" id="addBatchForm">
                                    @csrf

                                    {{-- Financial Year / Session Selector --}}
                                    <div class="form-group mb-2">
                                        <label class="form-label-compact">Financial Year (Session) <span class="text-danger">*</span></label>
                                        <select name="session_id" id="session_id_select" class="form-control form-control-compact" required>
                                            @foreach($sessions as $s)
                                                @php
                                                    $fyStr = $s->from_year . '-' . $s->to_year;
                                                    $isCurrent = ($selectedSessionId == $s->id || ($selectedSessionId === 'all' && $s->id == (Session::get('session_id') ?? 3)));
                                                @endphp
                                                <option value="{{ $s->id }}" {{ $isCurrent ? 'selected' : '' }}>
                                                    FY {{ $fyStr }} {{ ($s->id == Session::get('session_id')) ? '• (Active System Session)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="form-label-compact">Target Coaching Course <span class="text-danger">*</span></label>
                                        <select name="course_id" id="course_id_select" class="form-control form-control-compact" required>
                                            <option value="">-- Select Coaching Course --</option>
                                            @foreach($courses as $c)
                                                <option value="{{ $c->id }}" {{ ($selectedCourseId == $c->id) ? 'selected' : '' }}>
                                                    {{ $c->name }} ({{ $c->code ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="form-label-compact">Batch Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="batch_name_input" class="form-control form-control-compact" placeholder="e.g. Batch 1 (Morning), Alpha Batch" required>
                                    </div>

                                    {{-- Shift Selection with Quick Presets --}}
                                    <div class="form-group mb-2">
                                        <label class="form-label-compact d-block">Shift Preset <span class="text-danger">*</span></label>
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            <button type="button" class="shift-preset-btn active" data-shift="Morning" data-start="08:00 AM" data-end="12:00 PM">
                                                <i class="fa fa-sun-o text-warning"></i> Morning
                                            </button>
                                            <button type="button" class="shift-preset-btn" data-shift="Evening" data-start="02:00 PM" data-end="06:00 PM">
                                                <i class="fa fa-moon-o text-info"></i> Evening
                                            </button>
                                            <button type="button" class="shift-preset-btn" data-shift="Afternoon" data-start="12:00 PM" data-end="04:00 PM">
                                                <i class="fa fa-clock-o text-danger"></i> Afternoon
                                            </button>
                                            <button type="button" class="shift-preset-btn" data-shift="Special" data-start="09:00 AM" data-end="05:00 PM">
                                                <i class="fa fa-star text-primary"></i> Special
                                            </button>
                                        </div>
                                        <input type="hidden" name="shift" id="shift_input" value="Morning">
                                    </div>

                                    {{-- Timings --}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label-compact">Start Time</label>
                                                <input type="text" name="start_time" id="start_time_input" class="form-control form-control-compact" value="08:00 AM" placeholder="08:00 AM">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label-compact">End Time</label>
                                                <input type="text" name="end_time" id="end_time_input" class="form-control form-control-compact" value="12:00 PM" placeholder="12:00 PM">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label-compact">Room / Hall</label>
                                                <input type="text" name="room_no" class="form-control form-control-compact" placeholder="e.g. Room-101">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label-compact">Max Capacity</label>
                                                <input type="number" name="capacity" class="form-control form-control-compact" value="60" min="1">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-compact">Status</label>
                                        <select name="status" class="form-control form-control-compact">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="dash-btn dash-btn-primary w-100" style="height: 30px;">
                                        <i class="fa fa-save mr-1"></i> Save Batch Setup
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Batches Directory Table with Financial Year Filter --}}
                    <div class="col-12 col-lg-8 mb-2">
                        <div class="dash-card">
                            <div class="dash-card-header">
                                <form method="GET" action="{{ url('batches') }}" class="row align-items-center m-0">
                                    {{-- 1. Financial Year Filter --}}
                                    <div class="col-12 col-sm-6 col-md-3 p-1">
                                        <select name="session_id" class="form-control form-control-sm" onchange="this.form.submit()" style="height: 25px; font-size: 11px; border-radius: 2px; font-weight: 600; color: #002C54;">
                                            <option value="all" {{ ($selectedSessionId === 'all') ? 'selected' : '' }}>-- All Financial Years --</option>
                                            @foreach($sessions as $s)
                                                @php
                                                    $fy = $s->from_year . '-' . $s->to_year;
                                                @endphp
                                                <option value="{{ $s->id }}" {{ ($selectedSessionId == $s->id) ? 'selected' : '' }}>
                                                    FY {{ $fy }} {{ ($s->id == Session::get('session_id')) ? '•' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 2. Course Filter --}}
                                    <div class="col-6 col-sm-6 col-md-3 p-1">
                                        <select name="course_id" class="form-control form-control-sm" onchange="this.form.submit()" style="height: 25px; font-size: 11px; border-radius: 2px;">
                                            <option value="">-- All Courses --</option>
                                            @foreach($courses as $c)
                                                <option value="{{ $c->id }}" {{ ($selectedCourseId == $c->id) ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 3. Shift Filter --}}
                                    <div class="col-6 col-sm-4 col-md-2 p-1">
                                        <select name="shift" class="form-control form-control-sm" onchange="this.form.submit()" style="height: 25px; font-size: 11px; border-radius: 2px;">
                                            <option value="">-- All Shifts --</option>
                                            <option value="Morning" {{ ($selectedShift == 'Morning') ? 'selected' : '' }}>Morning</option>
                                            <option value="Evening" {{ ($selectedShift == 'Evening') ? 'selected' : '' }}>Evening</option>
                                            <option value="Afternoon" {{ ($selectedShift == 'Afternoon') ? 'selected' : '' }}>Afternoon</option>
                                            <option value="Special" {{ ($selectedShift == 'Special') ? 'selected' : '' }}>Special</option>
                                        </select>
                                    </div>

                                    {{-- 4. Search & Reset --}}
                                    <div class="col-12 col-sm-8 col-md-4 p-1 d-flex align-items-center justify-content-end gap-1">
                                        <div class="input-group input-group-sm flex-nowrap" style="max-width: 170px;">
                                            <input type="text" name="search" class="form-control" placeholder="Search batch/room..." value="{{ $search ?? '' }}" style="height: 25px; font-size: 11px; border-radius: 2px 0 0 2px;">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit" style="height: 25px; padding: 0 8px; font-size: 10.5px; border-radius: 0 2px 2px 0; background: #002C54; border-color: #002C54;">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                @if(!empty($search) || !empty($selectedCourseId) || !empty($selectedShift) || ($selectedSessionId !== (string)(Session::get('session_id') ?? 3)))
                                                    <a href="{{ url('batches') }}" class="btn btn-danger" style="height: 25px; padding: 0 8px; font-size: 10.5px; border-radius: 2px; margin-left: 2px;" title="Reset Filters">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge-total-records ml-1">{{ $batches->count() }} Batches</span>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive m-0" style="max-height: 540px; overflow-y: auto;">
                                <table class="dash-table">
                                    <thead>
                                        <tr class="header-titles-row">
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th style="width: 85px;" class="text-center">Fin. Year</th>
                                            <th>Course</th>
                                            <th>Batch Name</th>
                                            <th style="width: 170px;">Shift &amp; Timings</th>
                                            <th style="width: 75px;" class="text-center">Room</th>
                                            <th style="width: 95px;" class="text-center">Enrolled / Cap</th>
                                            <th style="width: 70px;" class="text-center">Status</th>
                                            <th style="width: 70px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($batches as $idx => $batch)
                                            @php
                                                $enrolled = $studentsByBatch[$batch->id] ?? 0;
                                                $shiftBadgeClass = 'badge-shift-special';
                                                $shiftIcon = 'fa-star';
                                                if ($batch->shift === 'Morning') {
                                                    $shiftBadgeClass = 'badge-shift-morning';
                                                    $shiftIcon = 'fa-sun-o';
                                                } elseif ($batch->shift === 'Evening') {
                                                    $shiftBadgeClass = 'badge-shift-evening';
                                                    $shiftIcon = 'fa-moon-o';
                                                } elseif ($batch->shift === 'Afternoon') {
                                                    $shiftBadgeClass = 'badge-shift-afternoon';
                                                    $shiftIcon = 'fa-clock-o';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center font-weight-bold text-muted">{{ $idx + 1 }}</td>
                                                <td class="text-center">
                                                    <span class="badge-fy-tag" title="Financial Year / Session">
                                                        <i class="fa fa-calendar-check-o"></i> {{ $batch->financial_year_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge-course-tag">{{ $batch->course->name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <strong style="color: #0f172a; font-size: 12px;">{{ $batch->name }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center gap-1 flex-wrap">
                                                        <span class="{{ $shiftBadgeClass }}">
                                                            <i class="fa {{ $shiftIcon }}"></i> {{ $batch->shift }}
                                                        </span>
                                                        @if(!empty($batch->start_time) && !empty($batch->end_time))
                                                            <span class="timing-tag">{{ $batch->start_time }} - {{ $batch->end_time }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center font-weight-bold text-secondary">
                                                    {{ $batch->room_no ?: '-' }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info" style="font-size: 11px; border-radius: 2px;">{{ $enrolled }}</span>
                                                    <small class="text-muted">/ {{ $batch->capacity ?? 60 }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($batch->status == 1)
                                                        <span class="badge-status-active"><i class="fa fa-check mr-1"></i>Active</span>
                                                    @else
                                                        <span class="badge-status-inactive">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <button type="button" class="btn-action-icon btn-action-edit edit-batch-btn"
                                                            data-id="{{ $batch->id }}"
                                                            data-course-id="{{ $batch->course_id }}"
                                                            data-session-id="{{ $batch->session_id }}"
                                                            data-financial-year="{{ $batch->financial_year_label }}"
                                                            data-name="{{ $batch->name }}"
                                                            data-shift="{{ $batch->shift }}"
                                                            data-start="{{ $batch->start_time }}"
                                                            data-end="{{ $batch->end_time }}"
                                                            data-capacity="{{ $batch->capacity }}"
                                                            data-room="{{ $batch->room_no }}"
                                                            data-status="{{ $batch->status }}"
                                                            title="Edit Batch">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn-action-icon btn-action-delete delete-batch-btn" data-id="{{ $batch->id }}" data-name="{{ $batch->name }}" title="Delete Batch">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">
                                                    <div style="font-size: 24px; color: #cbd5e1; margin-bottom: 4px;"><i class="fa fa-calendar-times-o"></i></div>
                                                    <div style="font-size: 12px; font-weight: 600; color: #475569;">No batches found for selected filter</div>
                                                    <div style="font-size: 11px; color: #94a3b8;">Try selecting "All Financial Years" or create a batch using the form on the left.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

{{-- Edit Batch Modal with Financial Year Selector --}}
<div class="modal fade" id="editBatchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 2px; border: 1px solid #cbd5e1;">
            <form id="editBatchForm" method="POST">
                @csrf
                <div class="modal-header modal-theme-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title m-0"><i class="fa fa-edit mr-1"></i> Edit Batch Configuration</h5>
                    <button type="button" class="close text-white p-0 m-0" data-dismiss="modal" style="font-size: 18px; line-height: 1;">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="edit_batch_id">

                    <div class="form-group mb-2">
                        <label class="form-label-compact">Financial Year (Session) <span class="text-danger">*</span></label>
                        <select name="session_id" id="edit_session_id" class="form-control form-control-compact" required>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}">FY {{ $s->from_year }}-{{ $s->to_year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-compact">Target Coaching Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="edit_course_id" class="form-control form-control-compact" required>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-compact">Batch Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-compact" required>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-compact">Shift <span class="text-danger">*</span></label>
                        <select name="shift" id="edit_shift" class="form-control form-control-compact">
                            <option value="Morning">Morning</option>
                            <option value="Evening">Evening</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Special">Special / Weekend</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="form-label-compact">Start Time</label>
                                <input type="text" name="start_time" id="edit_start_time" class="form-control form-control-compact">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="form-label-compact">End Time</label>
                                <input type="text" name="end_time" id="edit_end_time" class="form-control form-control-compact">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="form-label-compact">Room / Hall</label>
                                <input type="text" name="room_no" id="edit_room_no" class="form-control form-control-compact">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="form-label-compact">Capacity</label>
                                <input type="number" name="capacity" id="edit_capacity" class="form-control form-control-compact">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label-compact">Status</label>
                        <select name="status" id="edit_status" class="form-control form-control-compact">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="dash-btn dash-btn-outline text-secondary" data-dismiss="modal" style="border-color: #cbd5e1;">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn-primary"><i class="fa fa-save mr-1"></i> Update Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Quick Shift presets
    $('.shift-preset-btn').on('click', function() {
        $('.shift-preset-btn').removeClass('active');
        $(this).addClass('active');

        var shift = $(this).data('shift');
        var start = $(this).data('start');
        var end = $(this).data('end');

        $('#shift_input').val(shift);
        $('#start_time_input').val(start);
        $('#end_time_input').val(end);

        // Auto update placeholder name if empty
        var currentName = $('#batch_name_input').val();
        if (!currentName || currentName.indexOf('(') !== -1) {
            $('#batch_name_input').val('Batch 1 (' + shift + ')');
        }
    });

    // Edit Batch Modal
    $('.edit-batch-btn').on('click', function() {
        var id = $(this).data('id');
        $('#edit_batch_id').val(id);
        $('#edit_course_id').val($(this).data('course-id'));
        $('#edit_session_id').val($(this).data('session-id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_shift').val($(this).data('shift'));
        $('#edit_start_time').val($(this).data('start'));
        $('#edit_end_time').val($(this).data('end'));
        $('#edit_capacity').val($(this).data('capacity'));
        $('#edit_room_no').val($(this).data('room'));
        $('#edit_status').val($(this).data('status'));
        $('#editBatchForm').attr('action', '{{ url("batch_edit") }}/' + id);
        $('#editBatchModal').modal('show');
    });

    // Delete Batch
    $('.delete-batch-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        if (confirm('Are you sure you want to delete batch "' + name + '"?')) {
            $.ajax({
                url: '{{ url("batch_delete") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(resp) {
                    if (resp.status === 'success') {
                        location.reload();
                    } else {
                        alert(resp.message || 'Error deleting batch');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting batch';
                    alert(msg);
                }
            });
        }
    });
});
</script>
@endsection
