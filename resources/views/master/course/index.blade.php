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

/* Sharp Compact KPI Metric Tiles - Exactly Matching admin_dashboard */
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
.kpi-success { border-top: 3px solid #10b981; }
.kpi-warning { border-top: 3px solid #f59e0b; }
.kpi-info    { border-top: 3px solid #0284c7; }

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
.kpi-success .kpi-icon { color: #10b981; }
.kpi-warning .kpi-icon { color: #f59e0b; }
.kpi-info .kpi-icon    { color: #0284c7; }

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

/* Badges & Chips */
.badge-code {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    background: #e2e8f0;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    line-height: 1;
}
.badge-chip-batch {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    text-decoration: none !important;
    line-height: 1;
    white-space: nowrap;
}
.badge-chip-batch:hover {
    background: #dcfce7;
    color: #166534;
}
.badge-chip-subject {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    text-decoration: none !important;
    line-height: 1;
    white-space: nowrap;
}
.badge-chip-subject:hover {
    background: #dbeafe;
    color: #1e3a8a;
}
.badge-chip-students {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 2px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
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
.btn-action-link {
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
}
.btn-action-link:hover {
    background: #d97706;
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
textarea.form-control-compact {
    height: auto;
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
                        <span class="admission-kicker"><i class="fa fa-graduation-cap mr-1"></i> MASTER &middot; COACHING SETUP</span>
                        <h1 class="admission-title">Courses Master Directory</h1>
                        <p class="admission-subtitle">Manage coaching courses (SSC, Airforce, Raj Level, Foundation) with linked batches &amp; subjects</p>
                    </div>
                    <div class="admission-hero-actions">
                        <a href="{{ url('batches') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-users mr-1"></i> Batches Setup
                        </a>
                        <a href="{{ url('course_subjects') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-book mr-1"></i> Course Subjects
                        </a>
                        <a href="{{ url('create_subject') }}" class="dash-btn dash-btn-light">
                            <i class="fa fa-plus mr-1"></i> Master Subjects
                        </a>
                    </div>
                </div>

                {{-- Sharp Compact Metric KPI Tiles (Row of 4) --}}
                <div class="row row-compact mb-2">
                    {{-- 1. Total Courses --}}
                    <div class="col-6 col-sm-6 col-md-3 mb-1">
                        <div class="dash-kpi kpi-primary">
                            <div class="kpi-header">
                                <span class="kpi-label">Total Courses</span>
                                <span class="kpi-icon"><i class="fa fa-graduation-cap"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['total_courses'] ?? 0 }}</div>
                            <div class="kpi-meta">Active Coaching Streams</div>
                        </div>
                    </div>

                    {{-- 2. Active Batches --}}
                    <div class="col-6 col-sm-6 col-md-3 mb-1">
                        <a class="dash-kpi kpi-success" href="{{ url('batches') }}">
                            <div class="kpi-header">
                                <span class="kpi-label">Active Batches</span>
                                <span class="kpi-icon"><i class="fa fa-users"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['total_batches'] ?? 0 }}</div>
                            <div class="kpi-meta">Morning &amp; Evening Batches</div>
                        </a>
                    </div>

                    {{-- 3. Assigned Subjects --}}
                    <div class="col-6 col-sm-6 col-md-3 mb-1">
                        <a class="dash-kpi kpi-warning" href="{{ url('course_subjects') }}">
                            <div class="kpi-header">
                                <span class="kpi-label">Assigned Subjects</span>
                                <span class="kpi-icon"><i class="fa fa-book"></i></span>
                            </div>
                            <div class="kpi-value">{{ $kpis['total_subjects_assigned'] ?? 0 }}</div>
                            <div class="kpi-meta">Syllabus Mappings</div>
                        </a>
                    </div>

                    {{-- 4. Enrolled Students --}}
                    <div class="col-6 col-sm-6 col-md-3 mb-1">
                        <a class="dash-kpi kpi-info" href="{{ url('admissionView') }}">
                            <div class="kpi-header">
                                <span class="kpi-label">Enrolled Students</span>
                                <span class="kpi-icon"><i class="fa fa-user-plus"></i></span>
                            </div>
                            <div class="kpi-value">{{ number_format($kpis['total_students'] ?? 0) }}</div>
                            <div class="kpi-meta">Active Student Admissions</div>
                        </a>
                    </div>
                </div>

                {{-- Main Content Grid: Form (Left) + Table (Right) --}}
                <div class="row">
                    {{-- Left Column: Add New Course Form --}}
                    <div class="col-12 col-lg-4 mb-2">
                        <div class="dash-card">
                            <div class="dash-card-header d-flex align-items-center justify-content-between">
                                <h3 class="dash-card-title">
                                    <i class="fa fa-plus-circle text-info mr-1"></i> Add New Course
                                </h3>
                                <span class="badge badge-secondary" style="font-size: 10px; border-radius: 2px;">Session {{ Session::get('session_id') ?? 1 }}</span>
                            </div>
                            <div class="p-2">
                                <form action="{{ url('course_add') }}" method="POST" id="addCourseForm">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <label class="form-label-compact">Course Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control form-control-compact" placeholder="e.g. SSC, Airforce X, Raj Level" required autofocus>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="form-label-compact">Course Code <small class="text-muted">(Optional)</small></label>
                                        <input type="text" name="code" class="form-control form-control-compact" placeholder="e.g. SSC-01, AF-X">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="form-label-compact">Description / Target Exam <small class="text-muted">(Optional)</small></label>
                                        <textarea name="description" rows="2" class="form-control form-control-compact" placeholder="Target competitive exam / board syllabus"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label-compact">Display Order</label>
                                                <input type="number" name="orderBy" class="form-control form-control-compact" value="{{ ($kpis['total_courses'] ?? 0) + 1 }}" min="1">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label-compact">Status</label>
                                                <select name="status" class="form-control form-control-compact">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-1 px-2 my-2" style="font-size: 10.5px; border-radius: 2px; line-height: 1.4;">
                                        <i class="fa fa-info-circle mr-1"></i> <strong>Automatic Readiness:</strong> Creating a course generates default <strong>Morning (08:00 - 12:00)</strong> and <strong>Evening (03:00 - 07:00)</strong> batches automatically!
                                    </div>

                                    <button type="submit" class="dash-btn dash-btn-primary w-100 mt-1" style="height: 30px;">
                                        <i class="fa fa-save mr-1"></i> Save Course &amp; Batches
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Courses Directory Table --}}
                    <div class="col-12 col-lg-8 mb-2">
                        <div class="dash-card">
                            <div class="dash-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="dash-card-title">
                                        <i class="fa fa-graduation-cap text-info mr-1"></i> Courses Directory
                                    </h3>
                                    <span class="badge-total-records ml-2">{{ $courses->count() }} Total Courses</span>
                                </div>
                                <form method="GET" action="{{ url('courses') }}" class="form-inline m-0">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Search course..." value="{{ $search ?? '' }}" style="height: 24px; font-size: 11px; border-radius: 2px 0 0 2px;">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit" style="height: 24px; padding: 0 8px; font-size: 10.5px; border-radius: 0 2px 2px 0; background: #002C54; border-color: #002C54;">
                                                <i class="fa fa-search"></i>
                                            </button>
                                            @if(!empty($search))
                                                <a href="{{ url('courses') }}" class="btn btn-danger" style="height: 24px; padding: 0 8px; font-size: 10.5px; border-radius: 2px; margin-left: 3px;">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive m-0" style="max-height: 540px; overflow-y: auto;">
                                <table class="dash-table">
                                    <thead>
                                        <tr class="header-titles-row">
                                            <th style="width: 45px;" class="text-center">#</th>
                                            <th>Course Name</th>
                                            <th style="width: 100px;">Code</th>
                                            <th style="width: 110px;" class="text-center">Batches</th>
                                            <th style="width: 110px;" class="text-center">Subjects</th>
                                            <th style="width: 80px;" class="text-center">Students</th>
                                            <th style="width: 80px;" class="text-center">Status</th>
                                            <th style="width: 100px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($courses as $idx => $course)
                                            @php
                                                $cBatchCount = $course->batches->count();
                                                $cSubCount = $course->courseSubjects->count();
                                                $cStudentCount = ($studentsByCourse[$course->id] ?? 0) + ($studentsByClass[$course->class_type_id] ?? 0);
                                            @endphp
                                            <tr>
                                                <td class="text-center font-weight-bold text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    <strong style="color: #002C54; font-size: 12px;">{{ $course->name }}</strong>
                                                    @if(!empty($course->description))
                                                        <div class="text-muted" style="font-size: 10.5px; line-height: 1.2;">{{ Str::limit($course->description, 45) }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge-code">{{ $course->code ?? 'N/A' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ url('batches?course_id=' . $course->id) }}" class="badge-chip-batch" title="View Batches for {{ $course->name }}">
                                                        <i class="fa fa-users"></i> {{ $cBatchCount }} Batches
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ url('course_subjects?course_id=' . $course->id) }}" class="badge-chip-subject" title="Manage Subjects for {{ $course->name }}">
                                                        <i class="fa fa-book"></i> {{ $cSubCount }} Subjects
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge-chip-students">{{ $cStudentCount }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($course->status == 1)
                                                        <span class="badge-status-active"><i class="fa fa-check mr-1"></i>Active</span>
                                                    @else
                                                        <span class="badge-status-inactive">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <button type="button" class="btn-action-icon btn-action-edit edit-course-btn" 
                                                            data-id="{{ $course->id }}"
                                                            data-name="{{ $course->name }}"
                                                            data-code="{{ $course->code }}"
                                                            data-description="{{ $course->description }}"
                                                            data-order="{{ $course->orderBy }}"
                                                            data-status="{{ $course->status }}"
                                                            title="Edit Course">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <a href="{{ url('course_subjects?course_id=' . $course->id) }}" class="btn-action-icon btn-action-link" title="Assign Subjects">
                                                            <i class="fa fa-book"></i>
                                                        </a>
                                                        <button type="button" class="btn-action-icon btn-action-delete delete-course-btn" data-id="{{ $course->id }}" data-name="{{ $course->name }}" title="Delete Course">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <div style="font-size: 24px; color: #cbd5e1; margin-bottom: 4px;"><i class="fa fa-folder-open-o"></i></div>
                                                    <div style="font-size: 12px; font-weight: 600; color: #475569;">No courses found</div>
                                                    <div style="font-size: 11px; color: #94a3b8;">Create your first coaching course using the form on the left.</div>
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

{{-- Edit Course Modal --}}
<div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 2px; border: 1px solid #cbd5e1;">
            <form id="editCourseForm" method="POST">
                @csrf
                <div class="modal-header modal-theme-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title m-0"><i class="fa fa-edit mr-1"></i> Edit Coaching Course</h5>
                    <button type="button" class="close text-white p-0 m-0" data-dismiss="modal" style="font-size: 18px; line-height: 1;">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="edit_course_id">
                    <div class="form-group mb-2">
                        <label class="form-label-compact">Course Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-compact" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label-compact">Course Code</label>
                        <input type="text" name="code" id="edit_code" class="form-control form-control-compact">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label-compact">Description</label>
                        <textarea name="description" id="edit_description" rows="2" class="form-control form-control-compact"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="form-label-compact">Display Order</label>
                                <input type="number" name="orderBy" id="edit_orderBy" class="form-control form-control-compact" min="1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="form-label-compact">Status</label>
                                <select name="status" id="edit_status" class="form-control form-control-compact">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="dash-btn dash-btn-outline text-secondary" data-dismiss="modal" style="border-color: #cbd5e1;">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn-primary"><i class="fa fa-save mr-1"></i> Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Open edit modal
    $('.edit-course-btn').on('click', function() {
        var id = $(this).data('id');
        $('#edit_course_id').val(id);
        $('#edit_name').val($(this).data('name'));
        $('#edit_code').val($(this).data('code'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_orderBy').val($(this).data('order'));
        $('#edit_status').val($(this).data('status'));
        $('#editCourseForm').attr('action', '{{ url("course_edit") }}/' + id);
        $('#editCourseModal').modal('show');
    });

    // Delete course confirmation
    $('.delete-course-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        if (confirm('Are you sure you want to delete course "' + name + '"? Associated batches and course subjects will also be removed.')) {
            $.ajax({
                url: '{{ url("course_delete") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(resp) {
                    if (resp.status === 'success') {
                        location.reload();
                    } else {
                        alert(resp.message || 'Error deleting course');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting course';
                    alert(msg);
                }
            });
        }
    });
});
</script>
@endsection
