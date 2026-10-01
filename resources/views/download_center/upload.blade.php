@extends('layout.app')

@php
    $classType = Helper::ClassType();
    $uploadPermission = Helper::permissioncheck(12);
    $canEdit = $uploadPermission->edit ?? true;
    $canDelete = $uploadPermission->delete ?? true;
    $canDownload = $uploadPermission->print ?? true;

    $dataview = $dataview ?? collect();
    $totalCount = count($dataview);
    $typeCounts = [
        'Assignments' => 0,
        'Study Material' => 0,
        'Syllabus' => 0,
        'Other Downloads' => 0,
    ];
    foreach ($dataview as $row) {
        if (isset($typeCounts[$row['content_type'] ?? ''])) {
            $typeCounts[$row['content_type']]++;
        }
    }
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - DOWNLOAD CENTER / CONTENT LIBRARY
   Same design system as expenseView (grid) & expenseAdd (form)
   ========================================================================== */

.upload-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.upload-page * { box-sizing: border-box; }
.upload-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 1. Hero Header */
.upload-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.upload-hero-text { display: flex; flex-direction: column; }
.upload-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.upload-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.upload-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}
.upload-hero-stats { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
}
.hero-stat-badge b { font-weight: 700; font-size: 12px; }
.hero-stat-badge.badge-green { background: rgba(16,185,129,.2); border-color: rgba(16,185,129,.4); color: #a7f3d0; }
.hero-stat-badge.badge-amber { background: rgba(245,158,11,.2); border-color: rgba(245,158,11,.4); color: #fde68a; }
.hero-stat-badge.badge-blue { background: rgba(14,165,233,.2); border-color: rgba(14,165,233,.4); color: #bae6fd; }
.hero-stat-badge.badge-violet { background: rgba(139,92,246,.2); border-color: rgba(139,92,246,.4); color: #ddd6fe; }
.upload-hero-actions { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light { background: #ffffff; color: #002C54 !important; border-color: #ffffff; }
.dash-btn-light:hover { background: #f1f5f9; color: #001f3d !important; }
.dash-btn-navy { background: #002C54; color: #ffffff !important; padding: 0 16px; }
.dash-btn-navy:hover { background: #001f3d; }
.dash-btn-muted { background: #f1f5f9; color: #475569 !important; border: 1px solid #cbd5e1; }
.dash-btn-muted:hover { background: #e2e8f0; }

/* 2. Table Card */
.upload-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
}
.dash-card-title { font-size: 12px; font-weight: 700; margin: 0; color: #ffffff; line-height: 1.2; }
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 3px 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow: auto;
    position: relative;
    background: #eef2f6;
}
.dash-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 11.5px; }
.dash-table thead { position: sticky; top: 0; z-index: 20; }
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px;
    height: 36px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
}
.excel-filter-row th {
    position: sticky;
    top: 36px;
    background: #08335c;
    color: #ffffff;
    padding: 4px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
}
.fixed_action_head { position: sticky !important; right: 0; z-index: 25 !important; background: #002C54 !important; box-shadow: -3px 0 6px rgba(0,0,0,.15); }
.fixed_action_filter { position: sticky !important; right: 0; z-index: 24 !important; background: #08335c !important; box-shadow: -3px 0 6px rgba(0,0,0,.15); }
.fixed_action_col { position: sticky !important; right: 0; z-index: 5; box-shadow: -3px 0 6px rgba(0,0,0,.08); }
.dash-table tbody tr.row-odd .fixed_action_col { background: #f8fafc !important; }
.dash-table tbody tr.row-even .fixed_action_col { background: #edf2f7 !important; }
.dash-table tbody tr:hover .fixed_action_col { background: #e2e8f0 !important; }

.excel-col-filter {
    width: 100%;
    height: 26px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    color-scheme: dark;
}
.excel-col-filter::placeholder { color: rgba(255,255,255,.6); }
.excel-col-filter:focus { background: #031426 !important; border-color: #38bdf8 !important; box-shadow: 0 0 0 1px #38bdf8 !important; }
select.excel-col-filter { background-color: #051e38 !important; color: #ffffff !important; cursor: pointer; }
select.excel-col-filter option { background-color: #002C54 !important; color: #ffffff !important; }
.btn-reset-filters {
    height: 26px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .15s;
    white-space: nowrap;
}
.btn-reset-filters:hover { background: #002C54; border-color: #38bdf8; color: #38bdf8; }

.dash-table tbody td {
    padding: 5px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
}
.dash-table tbody tr.row-odd td { background: #f8fafc; }
.dash-table tbody tr.row-even td { background: #edf2f7; }
.dash-table tbody tr:hover td { background: #e2e8f0 !important; }

/* Cell components */
.content-title-text { font-weight: 650; color: #0f172a; display: block; line-height: 1.25; }
.class-pill {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.type-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
}
.type-assignments { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
.type-study { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.type-syllabus { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.type-other { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
.date-text { font-weight: 600; white-space: nowrap; }
.desc-text { font-size: 10.5px; color: #64748b; max-width: 280px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.media-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 2px;
    margin: 1px 0;
}
.media-file { color: #0369a1; background: #f0f9ff; border: 1px solid #bae6fd; }
.media-video { color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.media-none { color: #94a3b8; }

.table-actions { display: inline-flex; align-items: center; gap: 3px; }
.table-btn {
    width: 25px;
    height: 23px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    font-size: 11px;
    text-decoration: none !important;
    transition: all .15s;
    padding: 0;
}
.table-btn:hover { background: #002C54; color: #ffffff; border-color: #002C54; }
.btn-action-video:hover { background: #dc2626; border-color: #dc2626; color: #fff; }
.btn-action-download:hover { background: #16a34a; border-color: #16a34a; color: #fff; }
.btn-action-edit:hover { background: #0284c7; border-color: #0284c7; color: #fff; }
.btn-action-delete:hover { background: #dc2626; border-color: #dc2626; color: #fff; }

#empty-state-row td { padding: 0 !important; border: none !important; background: #eef2f6 !important; }
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height) - 220px);
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
}
.dash-empty-state .empty-icon { font-size: 44px; color: #94a3b8; margin-bottom: 12px; line-height: 1; }
.dash-empty-state .empty-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.dash-empty-state .empty-desc { font-size: 12px; color: #64748b; max-width: 380px; line-height: 1.5; }

/* 3. Pagination bar */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 38px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.pagination-info { font-size: 11.5px; color: #cbd5e1; }
.pagination-controls { display: flex; align-items: center; gap: 12px; }
.rows-per-page-selector { display: flex; align-items: center; gap: 5px; }
.rows-per-page-selector label { margin: 0; font-size: 11px; color: #cbd5e1; }
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 2px 5px;
    font-size: 11px;
    outline: none;
    cursor: pointer;
}
.pagination-nav { display: flex; align-items: center; gap: 3px; }
.page-btn {
    width: 26px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 12px;
    transition: all .15s;
}
.page-btn:hover:not(:disabled) { background: #0284c7; border-color: #0284c7; }
.page-btn:disabled { opacity: .4; cursor: not-allowed; }
.page-current-indicator { font-size: 11px; font-weight: 600; padding: 0 6px; color: #f1f5f9; }

/* ==========================================================================
   4. Add Content Modal (expenseAdd form design)
   ========================================================================== */
#uploadContentModal .modal-dialog { max-width: 960px; }
#uploadContentModal .modal-content {
    border: none;
    border-radius: 2px;
    background: #eef2f6;
    overflow: hidden;
    font-size: 12px;
    color: #0f172a;
}
.upload-modal-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.upload-modal-hero .upload-title { font-size: 14px; }
.upload-modal-body { padding: 8px 10px 4px; }

.expense-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
    height: calc(100% - 6px);
}
.expense-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.expense-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.expense-card-body { padding: 10px 12px; flex: 1; }
.form-group-compact { margin-bottom: 8px; }
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
    display: block;
}
.form-label-compact .req-star { color: #ef4444; margin-left: 2px; font-weight: 700; }
.form-control-compact {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 7px;
    width: 100%;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.form-control-compact:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0,44,84,.1) !important;
    outline: none;
}
.form-control-compact.is-invalid { border-color: #ef4444 !important; }
textarea.form-control-compact { height: auto; min-height: 92px; resize: vertical; }
input[type="file"].form-control-compact { padding: 2px 4px; }
.form-hint { font-size: 10px; color: #64748b; margin-top: 2px; }
.upload-form-footer {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

@media(max-width:768px) {
    .table-pagination-bar { flex-direction: column; gap: 6px; height: auto; padding: 6px 8px; }
    .pagination-controls { width: 100%; justify-content: space-between; }
    .upload-hero { flex-direction: column; align-items: stretch; }
}
</style>
@endsection

@section('content')
<div class="content-wrapper upload-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="upload-page-layout">

                {{-- 1. Hero Header --}}
                <div class="upload-hero">
                    <div class="upload-hero-text">
                        <span class="upload-kicker"><i class="fa fa-cloud-download mr-1"></i> Download Center</span>
                        <h1 class="upload-title"><i class="fa fa-folder-open mr-1"></i> {{ __('master.Content List') }}</h1>
                        <p class="upload-subtitle">Upload assignments, study material, syllabus &amp; downloads with in-column filters and instant search</p>
                    </div>

                    <div class="upload-hero-stats">
                        <span class="hero-stat-badge" title="Total Content">
                            <i class="fa fa-files-o text-info"></i> Total: <b>{{ $totalCount }}</b>
                        </span>
                        <span class="hero-stat-badge badge-blue" title="Assignments">
                            <i class="fa fa-pencil-square-o"></i> Assignments: <b>{{ $typeCounts['Assignments'] }}</b>
                        </span>
                        <span class="hero-stat-badge badge-green" title="Study Material">
                            <i class="fa fa-book"></i> Study Material: <b>{{ $typeCounts['Study Material'] }}</b>
                        </span>
                        <span class="hero-stat-badge badge-amber" title="Syllabus">
                            <i class="fa fa-list-ol"></i> Syllabus: <b>{{ $typeCounts['Syllabus'] }}</b>
                        </span>
                        <span class="hero-stat-badge badge-violet" title="Other Downloads">
                            <i class="fa fa-download"></i> Other: <b>{{ $typeCounts['Other Downloads'] }}</b>
                        </span>
                    </div>

                    <div class="upload-hero-actions">
                        <button type="button" class="dash-btn dash-btn-light" id="btn-open-upload-modal" title="{{ __('master.Add Content') }}">
                            <i class="fa fa-plus mr-1"></i> {{ __('master.Add Content') }}
                        </button>
                    </div>
                </div>

                {{-- 2. Full-Height Table Card --}}
                <div class="upload-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <h3 class="dash-card-title"><i class="fa fa-file-text-o text-info mr-1"></i> Content Library Grid</h3>
                        <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Items</span>
                    </div>

                    <div class="table-scroll-container">
                        <table class="dash-table" id="upload-grid-table">
                            <thead>
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th style="min-width: 200px;">{{ __('master.Content Title') }}</th>
                                    <th style="min-width: 130px;">{{ __('master.Class') }}</th>
                                    <th style="min-width: 140px;">{{ __('master.Content Type') }}</th>
                                    <th style="min-width: 120px;">{{ __('master.Date') }}</th>
                                    <th style="min-width: 200px;">{{ __('Description') }}</th>
                                    <th style="min-width: 100px;" class="text-center">Media</th>
                                    <th style="width: 120px;" class="text-center fixed_action_head">{{ __('master.Action') }}</th>
                                </tr>
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters-icon" title="Reset All Filters" style="padding: 0 4px; width: 25px;">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                    <th><input type="text" class="excel-col-filter" id="filter-title" placeholder="Search title..."></th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-class">
                                            <option value="">All Classes</option>
                                            <option value="none">General (All)</option>
                                            @foreach($classType ?? [] as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-type">
                                            <option value="">All Types</option>
                                            <option value="Assignments">Assignments</option>
                                            <option value="Study Material">Study Material</option>
                                            <option value="Syllabus">Syllabus</option>
                                            <option value="Other Downloads">Other Downloads</option>
                                        </select>
                                    </th>
                                    <th><input type="date" class="excel-col-filter" id="filter-date"></th>
                                    <th><input type="text" class="excel-col-filter" id="filter-desc" placeholder="Search description..."></th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-media">
                                            <option value="">All</option>
                                            <option value="file">File</option>
                                            <option value="video">Video</option>
                                            <option value="none">None</option>
                                        </select>
                                    </th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="upload-table-body">
                                @foreach($dataview as $item)
                                    @php
                                        $type = $item['content_type'] ?? '';
                                        $typeClass = [
                                            'Assignments' => 'type-assignments',
                                            'Study Material' => 'type-study',
                                            'Syllabus' => 'type-syllabus',
                                            'Other Downloads' => 'type-other',
                                        ][$type] ?? 'type-other';
                                        $hasFile = ($item['content_file'] ?? '') != '';
                                        $hasVideo = ($item['video_link'] ?? '') != '';
                                        $media = trim(($hasFile ? 'file ' : '') . ($hasVideo ? 'video' : ''));
                                        $uploadDate = !empty($item['upload_date']) ? date('Y-m-d', strtotime($item['upload_date'])) : '';
                                    @endphp
                                    <tr class="content-row"
                                        data-title="{{ mb_strtolower($item['content_title'] ?? '') }}"
                                        data-class="{{ $item['class_type_id'] ?: 'none' }}"
                                        data-type="{{ $type }}"
                                        data-date="{{ $uploadDate }}"
                                        data-desc="{{ mb_strtolower($item['description'] ?? '') }}"
                                        data-media="{{ $media ?: 'none' }}">
                                        <td class="text-center font-weight-bold text-muted serial-index"></td>
                                        <td><span class="content-title-text">{{ $item['content_title'] ?? '' }}</span></td>
                                        <td><span class="class-pill">{{ $item['class_name'] ?? 'All' }}</span></td>
                                        <td><span class="type-badge {{ $typeClass }}">{{ $type }}</span></td>
                                        <td><span class="date-text">{{ $uploadDate ? date('d-m-Y', strtotime($uploadDate)) : '-' }}</span></td>
                                        <td><span class="desc-text" title="{{ $item['description'] ?? '' }}">{{ $item['description'] ?: '-' }}</span></td>
                                        <td class="text-center">
                                            @if($hasFile)
                                                <span class="media-tag media-file"><i class="fa fa-paperclip"></i> File</span>
                                            @endif
                                            @if($hasVideo)
                                                <span class="media-tag media-video"><i class="fa fa-youtube-play"></i> Video</span>
                                            @endif
                                            @if(!$hasFile && !$hasVideo)
                                                <span class="media-none">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center fixed_action_col">
                                            <div class="table-actions">
                                                @if($hasVideo)
                                                    <a href="{{ $item['video_link'] }}" target="_blank" class="table-btn btn-action-video" title="Play Video"><i class="fa fa-play"></i></a>
                                                @endif
                                                @if($hasFile && $canDownload)
                                                    <a href="{{ url('download/'.$item['id']) }}" class="table-btn btn-action-download" title="Download"><i class="fa fa-download"></i></a>
                                                @endif
                                                @if($canEdit)
                                                    <button type="button" class="table-btn btn-action-edit edit-content-trigger"
                                                        data-id="{{ $item['id'] }}"
                                                        data-class="{{ $item['class_type_id'] ?? '' }}"
                                                        data-title="{{ $item['content_title'] ?? '' }}"
                                                        data-type="{{ $type }}"
                                                        data-date="{{ $uploadDate }}"
                                                        data-video="{{ $item['video_link'] ?? '' }}"
                                                        data-desc="{{ $item['description'] ?? '' }}"
                                                        data-file="{{ $item['content_file'] ?? '' }}"
                                                        data-file-url="{{ $hasFile ? env('IMAGE_SHOW_PATH').'download_center/'.$item['content_file'] : '' }}"
                                                        title="Edit"><i class="fa fa-pencil"></i></button>
                                                @endif
                                                @if($canDelete)
                                                    <button type="button" class="table-btn btn-action-delete delete-content-trigger"
                                                        data-id="{{ $item->id }}"
                                                        data-title="{{ $item['content_title'] ?? '' }}"
                                                        data-type="{{ $type }}"
                                                        data-toggle="modal" data-target="#Modal_id"
                                                        data-bs-toggle="modal" data-bs-target="#Modal_id"
                                                        title="Delete"><i class="fa fa-trash"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="empty-state-row" style="display: none;">
                                    <td colspan="8">
                                        <div class="dash-empty-state">
                                            <div class="empty-icon"><i class="fa fa-folder-open-o"></i></div>
                                            <div class="empty-title">No content found</div>
                                            <div class="empty-desc">No uploaded content matches the current filters. Reset the filters or upload new content.</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Pagination Bar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">0</span> to <span id="page-end" class="font-weight-bold text-white">0</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page"><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page"><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page"><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

{{-- 4. Add Content Modal (expenseAdd design) --}}
<div class="modal fade" id="uploadContentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="upload-modal-hero">
                <div class="upload-hero-text">
                    <span class="upload-kicker"><i class="fa fa-cloud-upload mr-1"></i> Download Center</span>
                    <h2 class="upload-title"><i class="fa fa-file-text-o mr-1"></i> <span id="upload-modal-title">{{ __('master.Add Content') }}</span></h2>
                    <span class="upload-subtitle" id="upload-modal-subtitle">Choose class &amp; content type, attach a file or video link and add notes for students.</span>
                </div>
                <button type="button" class="dash-btn dash-btn-light" data-dismiss="modal" data-bs-dismiss="modal" title="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="form-submit" action="{{ url('upload/content') }}" method="post" enctype="multipart/form-data" style="display: contents;">
                @csrf
                <div class="modal-body upload-modal-body">
                    <div class="row">
                        {{-- Step 1: Content Details --}}
                        <div class="col-lg-6">
                            <div class="expense-card">
                                <div class="expense-card-header">
                                    <h3 class="expense-card-title">
                                        <span class="card-step">1</span>
                                        <i class="fa fa-id-card-o text-info mr-1"></i> Content Details
                                    </h3>
                                </div>
                                <div class="expense-card-body">
                                    <div class="form-group form-group-compact">
                                        <label class="form-label-compact">{{ __('common.Class') }}</label>
                                        <select class="form-control-compact" id="class_search_id" name="class_search_id">
                                            @if(Session::get('role_id') != 2)
                                                <option value="">{{ __('common.Select') }}</option>
                                            @endif
                                            @foreach($classType ?? [] as $type)
                                                <option value="{{ $type->id ?? '' }}">{{ $type->name ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group form-group-compact">
                                        <label class="form-label-compact">{{ __('master.Content Title') }}<span class="req-star">*</span></label>
                                        <input class="form-control-compact @error('content_title') is-invalid @enderror" type="text" name="content_title" id="content_title" placeholder="{{ __('master.Content Title') }}" value="{{ old('content_title') }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-compact">
                                                <label class="form-label-compact">{{ __('master.Content Type') }}<span class="req-star">*</span></label>
                                                <select class="form-control-compact @error('content_type') is-invalid @enderror" id="content_type" name="content_type">
                                                    <option value="">Select</option>
                                                    <option value="Assignments">Assignments</option>
                                                    <option value="Study Material">Study Material</option>
                                                    <option value="Syllabus">Syllabus</option>
                                                    <option value="Other Downloads">Other Downloads</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-compact">
                                                <label class="form-label-compact">{{ __('master.Upload Date') }}<span class="req-star">*</span></label>
                                                <input class="form-control-compact @error('upload_date') is-invalid @enderror" type="date" name="upload_date" id="upload_date" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Media & Notes --}}
                        <div class="col-lg-6">
                            <div class="expense-card">
                                <div class="expense-card-header">
                                    <h3 class="expense-card-title">
                                        <span class="card-step">2</span>
                                        <i class="fa fa-paperclip text-info mr-1"></i> File, Video &amp; Notes
                                    </h3>
                                </div>
                                <div class="expense-card-body">
                                    <div class="form-group form-group-compact">
                                        <label class="form-label-compact">{{ __('master.Content File') }} <small class="text-muted">(Image, PDF, Video up to 50MB)</small></label>
                                        <input type="file" class="form-control-compact @error('content_file') is-invalid @enderror" name="content_file" id="content_file"
                                            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv">
                                        <p class="text-danger mb-0" id="image_error"></p>
                                        <div class="form-hint" id="current-file-wrap" style="display: none;">
                                            <i class="fa fa-paperclip"></i> Current file:
                                            <a href="#" target="_blank" id="current-file-link" class="font-weight-bold text-primary"></a>
                                            <span class="d-block">Leave empty to keep the current file.</span>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-compact">
                                        <label class="form-label-compact">{{ __('Video Link') }}</label>
                                        <input class="form-control-compact" type="text" name="video_link" id="video_link" placeholder="e.g. https://youtube.com/watch?v=..." value="{{ old('video_link') }}">
                                    </div>
                                    <div class="form-group form-group-compact mb-0">
                                        <label class="form-label-compact">{{ __('master.Description') }}</label>
                                        <textarea class="form-control-compact" name="description" id="description" rows="3" placeholder="{{ __('master.Description') }}">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="upload-form-footer">
                    <button type="button" class="dash-btn dash-btn-muted" data-dismiss="modal" data-bs-dismiss="modal">
                        <i class="fa fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="dash-btn dash-btn-navy btn-submit" id="upload-submit-btn">{{ __('master.Submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="Modal_id" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title" style="font-size: 14px;"><i class="fa fa-trash mr-1"></i> Delete Content</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{ url('upload_delete') }}" method="post">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-body p-3">
                    <p class="text-dark mb-2" style="font-size: 13px;">Are you sure you want to delete this content?</p>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 10px; border-radius: 2px; font-size: 12px;">
                        <div><strong>Title:</strong> <span id="del_modal_title">-</span></div>
                        <div><strong>Type:</strong> <span id="del_modal_type">-</span></div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // ---- Add Content modal ----
    const addUrl = "{{ url('upload/content') }}";
    const editBaseUrl = "{{ url('upload/content_edit') }}";
    const addTitle = @json(__('master.Add Content'));
    const addSubtitle = $('#upload-modal-subtitle').text();
    const submitText = @json(__('master.Submit'));
    const $uploadForm = $('#form-submit');

    function clearFormErrors() {
        $uploadForm.find('.is-invalid').removeClass('is-invalid');
        $uploadForm.find('.error').remove();
        $('#image_error').text('');
    }

    // Add mode
    $('#btn-open-upload-modal').on('click', function () {
        $uploadForm[0].reset();
        clearFormErrors();
        $uploadForm.attr('action', addUrl);
        $('#upload_date').val("{{ date('Y-m-d') }}");
        $('#upload-modal-title').text(addTitle);
        $('#upload-modal-subtitle').text(addSubtitle);
        $('#upload-submit-btn').text(submitText);
        $('#current-file-wrap').hide();
        $('#uploadContentModal').modal('show');
    });

    // Edit mode (same modal, prefilled)
    $(document).on('click', '.edit-content-trigger', function () {
        const b = $(this);
        $uploadForm[0].reset();
        clearFormErrors();
        $uploadForm.attr('action', editBaseUrl + '/' + b.data('id'));

        const cls = String(b.attr('data-class') || '');
        const $cls = $('#class_search_id');
        if (cls && $cls.find('option[value="' + cls + '"]').length === 0) {
            $cls.append($('<option>').val(cls).text('Class #' + cls));
        }
        $cls.val(cls);
        $('#content_title').val(b.attr('data-title') || '');
        $('#content_type').val(b.attr('data-type') || '');
        $('#upload_date').val(b.attr('data-date') || '');
        $('#video_link').val(b.attr('data-video') || '');
        $('#description').val(b.attr('data-desc') || '');

        const fileName = b.attr('data-file') || '';
        if (fileName) {
            $('#current-file-link').attr('href', b.attr('data-file-url') || '#').text(fileName);
            $('#current-file-wrap').show();
        } else {
            $('#current-file-wrap').hide();
        }

        $('#upload-modal-title').text('Edit Content');
        $('#upload-modal-subtitle').text('Update details, replace the file or video link. Changes apply immediately after saving.');
        $('#upload-submit-btn').text('Update Content');
        $('#uploadContentModal').modal('show');
    });
    @if($errors->any())
        $('#uploadContentModal').modal('show');
    @endif

    // ---- Delete modal ----
    $(document).on('click', '.delete-content-trigger', function () {
        $('#delete_id').val($(this).data('id') || '');
        $('#del_modal_title').text($(this).data('title') || '-');
        $('#del_modal_type').text($(this).data('type') || '-');
    });

    // ---- Client-side filter + pagination ----
    const $allRows = $('#upload-table-body tr.content-row');
    let filteredRows = $allRows.toArray();
    let currentPage = 1;
    let filterTimer = null;

    function getPerPage() {
        const v = $('#rows-per-page-select').val();
        return v === 'all' ? Infinity : parseInt(v, 10) || 25;
    }

    function applyFilters() {
        const title = ($('#filter-title').val() || '').trim().toLowerCase();
        const cls = $('#filter-class').val() || '';
        const type = $('#filter-type').val() || '';
        const date = $('#filter-date').val() || '';
        const desc = ($('#filter-desc').val() || '').trim().toLowerCase();
        const media = $('#filter-media').val() || '';

        filteredRows = $allRows.toArray().filter(function (row) {
            const d = row.dataset;
            if (title && d.title.indexOf(title) === -1) return false;
            if (cls && String(d.class) !== cls) return false;
            if (type && d.type !== type) return false;
            if (date && d.date !== date) return false;
            if (desc && d.desc.indexOf(desc) === -1) return false;
            if (media) {
                if (media === 'none' && d.media !== 'none') return false;
                if (media !== 'none' && d.media.split(' ').indexOf(media) === -1) return false;
            }
            return true;
        });
        currentPage = 1;
        render();
    }

    function render() {
        const perPage = getPerPage();
        const total = filteredRows.length;
        const lastPage = perPage === Infinity ? 1 : Math.max(1, Math.ceil(total / perPage));
        currentPage = Math.min(Math.max(1, currentPage), lastPage);
        const start = perPage === Infinity ? 0 : (currentPage - 1) * perPage;
        const end = perPage === Infinity ? total : Math.min(start + perPage, total);

        $allRows.hide();
        filteredRows.slice(start, end).forEach(function (row, idx) {
            $(row).show()
                .removeClass('row-odd row-even')
                .addClass(idx % 2 === 0 ? 'row-odd' : 'row-even')
                .find('.serial-index').text(start + idx + 1);
        });

        $('#empty-state-row').toggle(total === 0);
        $('#page-start').text(total > 0 ? start + 1 : 0);
        $('#page-end').text(end);
        $('#total-records').text(total);
        $('#header-records-count').text(total);
        $('#current-page').text(currentPage);
        $('#total-pages').text(lastPage);
        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage);
        window.__uploadLastPage = lastPage;
    }

    $('#filter-title, #filter-desc').on('input', function () {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(applyFilters, 250);
    });
    $('#filter-class, #filter-type, #filter-date, #filter-media').on('change', applyFilters);
    $('#rows-per-page-select').on('change', function () { currentPage = 1; render(); });

    $('#btn-first').on('click', function () { currentPage = 1; render(); });
    $('#btn-prev').on('click', function () { currentPage--; render(); });
    $('#btn-next').on('click', function () { currentPage++; render(); });
    $('#btn-last').on('click', function () { currentPage = window.__uploadLastPage || 1; render(); });

    $('#btn-reset-filters, #btn-clear-filters-icon').on('click', function () {
        $('#filter-title, #filter-desc, #filter-date').val('');
        $('#filter-class, #filter-type, #filter-media').val('');
        applyFilters();
    });

    render();
});
</script>
@endsection
