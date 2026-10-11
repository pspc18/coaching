@php
    $getUser = Helper::getUser();
    $className = optional($getUser)->ClassTypes->name ?? 'Class';
    $studentName = trim(($getUser->first_name ?? '') . ' ' . ($getUser->last_name ?? ''));
    $currentSessionName = Session::get('session_name') ?? date('Y');

    $totalCount = $metrics['total'] ?? 0;
    $studyMaterialCount = $metrics['study_material'] ?? 0;
    $assignmentsCount = $metrics['assignments'] ?? 0;
    $syllabusCount = $metrics['syllabus'] ?? 0;
    $otherDownloadsCount = $metrics['other_downloads'] ?? 0;
    $videosCount = $metrics['videos_count'] ?? 0;
@endphp

@extends('student_login.layout.app')

@section('title', 'Download Center')
@section('page_title', 'DOWNLOAD CENTER')
@section('page_sub', (!empty($studentName) ? $studentName : 'Student') . ' · ' . $className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT MOBILE DOWNLOAD CENTER
   Exact Layout, Typography & Palette matching Arise Native Mobile Design System
   ========================================================================== */

/* 1. Signature Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 11px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* 4-KPI Metric Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 3px;
    text-align: center;
    transition: transform .15s ease;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
}
.mob-metric-val {
    font-size: 14.5px;
    font-weight: 800;
    line-height: 1.1;
    color: #ffffff;
}

/* Resource Summary Pill Strip */
.mob-hero-summary-strip {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 5px 8px;
    font-size: 10px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-hero-summary-strip b {
    color: #38bdf8;
}

/* 2. Sticky Live Search & Category Filter Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 7px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 48px;
    z-index: 20;
}
.mob-search-input-box {
    position: relative;
    margin-bottom: 6px;
}
.mob-search-input-box i.search-ico {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 31px;
    padding: 4px 28px 4px 26px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: all .2s;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-search-clear {
    position: absolute;
    right: 7px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    cursor: pointer;
    display: none;
    padding: 2px 4px;
}

/* Horizontal Filter Chips */
.mob-chips-track {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 1px;
}
.mob-chips-track::-webkit-scrollbar {
    display: none;
}
.mob-filter-chip {
    flex-shrink: 0;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
    white-space: nowrap;
}
.mob-filter-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
}
.mob-filter-chip .chip-cnt {
    font-size: 9px;
    padding: 0 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-filter-chip.active .chip-cnt {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* 3. Content Cards Feed */
.mob-dl-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 4px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    position: relative;
    transition: box-shadow .2s;
}
.student-mob-card:active {
    background-color: #fafbfc;
}

/* Category Color Borders */
.student-mob-card.cat-study-material {
    border-left-color: #0284c7;
}
.student-mob-card.cat-assignments {
    border-left-color: #f59e0b;
}
.student-mob-card.cat-syllabus {
    border-left-color: #10b981;
}
.student-mob-card.cat-other-downloads {
    border-left-color: #8b5cf6;
}

/* Card Header */
.mob-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-card-badge-wrap {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.mob-cat-pill {
    font-size: 10px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.cat-pill-study-material {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.cat-pill-assignments {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.cat-pill-syllabus {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.cat-pill-other-downloads {
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
}

.mob-class-tag {
    font-size: 9px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-date-pill {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

/* Card Title & Content */
.mob-dl-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    margin-bottom: 4px;
    word-break: break-word;
}
.mob-dl-desc-box {
    font-size: 11px;
    line-height: 1.45;
    color: #475569;
    margin-bottom: 7px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 3px;
    padding: 6px 8px;
    word-break: break-word;
}
.mob-dl-desc-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.mob-read-more-btn {
    display: inline-block;
    color: #0284c7;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 3px;
    text-decoration: underline;
}

/* Resource Section: File Box & Video Box */
.mob-file-box {
    background: #eff6ff;
    border: 1px dashed #bfdbfe;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.mob-file-info {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
}
.mob-file-icon {
    font-size: 16px;
    flex-shrink: 0;
}
.mob-file-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e3a8a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 170px;
}
.mob-file-subtext {
    font-size: 9px;
    color: #64748b;
}
.mob-file-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.btn-file-action {
    height: 25px;
    padding: 0 7px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    line-height: 1;
}
.btn-file-preview {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #0284c7;
}
.btn-file-download {
    background: #0284c7;
    color: #ffffff;
}

/* Video Box */
.mob-video-box {
    background: #fff1f2;
    border: 1px dashed #fecdd3;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.mob-video-info {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
}
.mob-video-info i {
    font-size: 16px;
    color: #e11d48;
    flex-shrink: 0;
}
.mob-video-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #9f1239;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 170px;
}
.btn-video-watch {
    height: 25px;
    padding: 0 8px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 3px;
    background: #e11d48;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

/* Card Footer */
.mob-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 4px;
    border-top: 1px dashed #e2e8f0;
    font-size: 9.5px;
    color: #64748b;
}

/* ==========================================================================
   4. NATIVE MOBILE BOTTOM-SHEET MODAL SYSTEM
   Matches student-notices, admissionView?layout=mobile
   Guaranteed hidden until triggered by user click
   ========================================================================== */

/* Hide any bootstrap modal default display */
.modal:not(.show) {
    display: none !important;
}

/* Backdrop */
.mob-modal-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .22s ease-in-out, visibility .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Slide-Up Sheet Container */
.mob-modal-sheet {
    display: flex;
    position: fixed;
    bottom: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 3px solid #0284c7;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 88vh;
    flex-direction: column;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.4);
    visibility: hidden;
    pointer-events: none;
}
.mob-modal-sheet.show {
    bottom: 0;
    visibility: visible;
    pointer-events: auto;
}

/* Sheet Header */
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 5px 5px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .12s ease;
}
.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.25);
}

/* Moveable Scrollable Body */
.mob-sheet-body {
    padding: 12px 14px 16px 14px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

/* Sheet Footer */
.mob-sheet-footer {
    padding: 9px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-shrink: 0;
}

/* Empty State Card */
.mob-empty-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 36px 16px;
    text-align: center;
    margin: 8px 0;
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    line-height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    font-size: 20px;
    margin: 0 auto 10px auto;
}
.mob-empty-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    max-width: 260px;
    margin: 0 auto 12px auto;
    line-height: 1.4;
}
</style>
@endsection

@section('content')
<div class="container-fluid px-2 py-1">

    {{-- Session Flash Alerts --}}


    {{-- 1. Signature Glassmorphic Navy Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-cloud-download text-warning"></i> Digital Library & Downloads
            </div>
            <span class="mob-session-pill">
                <i class="fa fa-graduation-cap"></i> {{ $currentSessionName }}
            </span>
        </div>

        {{-- 4 Live Glance Metrics (Total, Study Material, Assignments, Syllabus) --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box">
                <div class="mob-metric-tag">Total</div>
                <div class="mob-metric-val">{{ $totalCount }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag" style="color: #60a5fa;">Notes</div>
                <div class="mob-metric-val" style="color: #60a5fa;">{{ $studyMaterialCount }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag" style="color: #fbbf24;">Worksheets</div>
                <div class="mob-metric-val" style="color: #fbbf24;">{{ $assignmentsCount }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag" style="color: #4ade80;">Syllabus</div>
                <div class="mob-metric-val" style="color: #4ade80;">{{ $syllabusCount }}</div>
            </div>
        </div>

        {{-- Summary Banner --}}
        <div class="mob-hero-summary-strip">
            <span><i class="fa fa-folder-open-o text-info me-1"></i> Resources for <b>{{ $className }}</b></span>
            <span><b>{{ $metrics['files_count'] ?? 0 }}</b> Files · <b>{{ $videosCount }}</b> Videos</span>
        </div>
    </div>

    {{-- 2. Sticky Live Search & Horizontal Filter Toolbar --}}
    <div class="mob-search-toolbar" id="downloadSearchToolbar">
        <div class="mob-search-input-box">
            <i class="fa fa-search search-ico"></i>
            <input type="text" id="searchDlInput" class="mob-search-input" placeholder="Search title, description, subject..." autocomplete="off">
            <span class="mob-search-clear" id="clearSearchBtn"><i class="fa fa-times-circle"></i></span>
        </div>

        {{-- Category Filter Chips --}}
        <div class="mob-chips-track">
            <button type="button" class="mob-filter-chip active" data-filter="all">
                All <span class="chip-cnt">{{ $totalCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="study material">
                <i class="fa fa-book text-info"></i> Study Material <span class="chip-cnt">{{ $studyMaterialCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="assignments">
                <i class="fa fa-pencil-square-o text-warning"></i> Assignments <span class="chip-cnt">{{ $assignmentsCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="syllabus">
                <i class="fa fa-list-alt text-success"></i> Syllabus <span class="chip-cnt">{{ $syllabusCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="other downloads">
                <i class="fa fa-download" style="color: #8b5cf6;"></i> Other <span class="chip-cnt">{{ $otherDownloadsCount }}</span>
            </button>
            @if($videosCount > 0)
                <button type="button" class="mob-filter-chip" data-filter="videos">
                    <i class="fa fa-play-circle text-danger"></i> Videos <span class="chip-cnt">{{ $videosCount }}</span>
                </button>
            @endif
        </div>
    </div>

    {{-- 3. Content Cards Feed --}}
    <div class="mob-dl-feed" id="downloadCardsFeed">
        @forelse($dataview as $item)
            @php
                $rawType = trim($item->content_type ?? 'Other Downloads');
                $normType = strtolower($rawType);
                $catSlug = str_replace(' ', '-', $normType);

                $title = $item->content_title ?? 'Untitled Content';
                $classNameLabel = $item->ClassType->name ?? 'All Classes';
                $pubDate = !empty($item->upload_date) ? date('d-m-Y', strtotime($item->upload_date)) : '-';
                $facultyName = trim(($item->User->first_name ?? '') . ' ' . ($item->User->last_name ?? ''));
                if (empty($facultyName)) {
                    $facultyName = 'Faculty';
                }

                $hasFile = !empty($item->content_file);
                $hasVideo = !empty($item->video_link);

                $ext = $item->file_ext ?? '';
                $isPdf = ($ext === 'pdf');
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $fileUrl = $hasFile ? env('IMAGE_SHOW_PATH') . 'download_center/' . $item->content_file : '';
            @endphp

            <div class="student-mob-card cat-{{ $catSlug }}"
                 data-type="{{ $normType }}"
                 data-title="{{ strtolower($title) }}"
                 data-desc="{{ strtolower($item->description ?? '') }}"
                 data-class="{{ strtolower($classNameLabel) }}"
                 data-has-video="{{ $hasVideo ? '1' : '0' }}">

                {{-- Card Header --}}
                <div class="mob-card-head">
                    <div class="mob-card-badge-wrap">
                        <span class="mob-cat-pill cat-pill-{{ $catSlug }}">
                            @if($normType === 'study material')
                                <i class="fa fa-book"></i>
                            @elseif($normType === 'assignments')
                                <i class="fa fa-pencil-square-o"></i>
                            @elseif($normType === 'syllabus')
                                <i class="fa fa-list-alt"></i>
                            @else
                                <i class="fa fa-folder-open-o"></i>
                            @endif
                            {{ $rawType }}
                        </span>
                        <span class="mob-class-tag">{{ $classNameLabel }}</span>
                    </div>

                    <span class="mob-date-pill">
                        <i class="fa fa-calendar-o"></i> {{ $pubDate }}
                    </span>
                </div>

                {{-- Title --}}
                <div class="mob-dl-title">{{ $title }}</div>

                {{-- Description (with 3-line clamp and read more toggle) --}}
                @if(!empty($item->description))
                    <div class="mob-dl-desc-box">
                        <div class="mob-dl-desc-clamp" id="descClamp-{{ $item->id }}">
                            {!! nl2br(e($item->description)) !!}
                        </div>
                        @if(mb_strlen($item->description) > 120)
                            <span class="mob-read-more-btn" onclick="toggleReadMore({{ $item->id }})" id="readMoreBtn-{{ $item->id }}">
                                Read full details <i class="fa fa-angle-down"></i>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Resource Section: Document Attachment Box --}}
                @if($hasFile)
                    <div class="mob-file-box">
                        <div class="mob-file-info">
                            @if($isPdf)
                                <i class="fa fa-file-pdf-o text-danger mob-file-icon"></i>
                            @elseif($isImage)
                                <i class="fa fa-file-image-o text-primary mob-file-icon"></i>
                            @else
                                <i class="fa fa-file-text-o text-info mob-file-icon"></i>
                            @endif
                            <div style="overflow: hidden;">
                                <div class="mob-file-name" title="{{ $item->content_file }}">{{ $item->content_file }}</div>
                                <div class="mob-file-subtext">{{ strtoupper($ext ?: 'FILE') }} Document</div>
                            </div>
                        </div>

                        <div class="mob-file-actions">
                            @if($isPdf || $isImage)
                                <button type="button" class="btn-file-action btn-file-preview" 
                                        onclick="previewFileAttachment('{{ $fileUrl }}', '{{ $isPdf ? 'pdf' : 'image' }}', '{{ addslashes($title) }}', '{{ url('download/'.$item->id) }}')">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            @endif
                            <a href="{{ url('download/'.$item->id) }}" class="btn-file-action btn-file-download" title="Download Document">
                                <i class="fa fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Resource Section: Video Link Box --}}
                @if($hasVideo)
                    <div class="mob-video-box">
                        <div class="mob-video-info">
                            <i class="fa fa-youtube-play"></i>
                            <div style="overflow: hidden;">
                                <div class="mob-video-title">Video Lesson / Tutorial</div>
                                <div style="font-size: 9px; color: #9f1239;">Tap to play video tutorial</div>
                            </div>
                        </div>
                        <button type="button" class="btn-video-watch" onclick="openVideoPlayerSheet('{{ addslashes($item->video_link) }}', '{{ addslashes($title) }}')">
                            <i class="fa fa-play"></i> Watch
                        </button>
                    </div>
                @endif

                {{-- Card Footer --}}
                <div class="mob-card-footer">
                    <span><i class="fa fa-user-circle-o me-1"></i> Uploaded by: <b>{{ $facultyName }}</b></span>
                    @if($hasFile)
                        <span class="text-success"><i class="fa fa-check-circle me-1"></i> Ready to download</span>
                    @elseif($hasVideo)
                        <span class="text-danger"><i class="fa fa-video-camera me-1"></i> Video tutorial</span>
                    @endif
                </div>

            </div>
        @empty
            <div class="mob-empty-card">
                <div class="mob-empty-icon"><i class="fa fa-cloud-download"></i></div>
                <div class="mob-empty-title">No Downloads Available</div>
                <div class="mob-empty-desc">Your teachers have not uploaded any study material or syllabus documents for your class yet.</div>
            </div>
        @endforelse

        {{-- Dynamic Filter Empty State --}}
        <div class="mob-empty-card" id="noFilterMatchesCard" style="display: none;">
            <div class="mob-empty-icon"><i class="fa fa-search"></i></div>
            <div class="mob-empty-title">No Matching Content Found</div>
            <div class="mob-empty-desc">No study resources match your search or category filter. Try clearing filters.</div>
            <button type="button" class="btn-file-action btn-file-preview py-1 px-3" onclick="resetSearchAndFilters()" style="height: 28px; font-size: 11px;">
                <i class="fa fa-refresh me-1"></i> Clear Filters
            </button>
        </div>
    </div>

</div>

{{-- ==========================================================================
     SHARED MODAL BACKDROP (Single Native Backdrop for all bottom sheets)
     ========================================================================== --}}
<div class="mob-modal-backdrop" id="mobModalBackdrop" onclick="closeActiveSheet()"></div>

{{-- ==========================================================================
     MODAL SHEET 1: DOCUMENT / IMAGE / PDF PREVIEWER (NATIVE BOTTOM SHEET)
     ========================================================================== --}}
<div class="mob-modal-sheet" id="previewFileModalSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title" id="previewFileModalTitle">
            <i class="fa fa-eye" style="color: #38bdf8;"></i>
            <span>Document Preview</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeActiveSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- Moveable / Scrollable Body --}}
    <div class="mob-sheet-body text-center p-2" id="previewFileModalBody">
        {{-- Injected dynamically --}}
    </div>
</div>

{{-- ==========================================================================
     MODAL SHEET 2: VIDEO TUTORIAL PLAYER (NATIVE BOTTOM SHEET)
     ========================================================================== --}}
<div class="mob-modal-sheet" id="videoPlayerModalSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title" id="videoPlayerModalTitle">
            <i class="fa fa-youtube-play text-danger"></i>
            <span>Video Lesson</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeActiveSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- Moveable / Scrollable Body --}}
    <div class="mob-sheet-body p-2" id="videoPlayerModalBody">
        {{-- Video iframe injected dynamically --}}
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // =========================================================================
    // 1. Live Instant Search & Category Filter Chips
    // =========================================================================
    let currentFilter = 'all';
    const searchInput = $('#searchDlInput');
    const clearBtn = $('#clearSearchBtn');
    const cards = $('.student-mob-card');
    const emptyState = $('#noFilterMatchesCard');

    function filterCards() {
        const query = searchInput.val().toLowerCase().trim();
        let visibleCount = 0;

        cards.each(function() {
            const card = $(this);
            const type = card.data('type') || '';
            const title = card.data('title') || '';
            const desc = card.data('desc') || '';
            const cls = card.data('class') || '';
            const hasVideo = card.data('has-video') == '1';

            // Category matching
            let matchesCategory = false;
            if (currentFilter === 'all') {
                matchesCategory = true;
            } else if (currentFilter === 'videos') {
                matchesCategory = hasVideo;
            } else if (currentFilter === type) {
                matchesCategory = true;
            }

            // Search query matching
            let matchesSearch = true;
            if (query.length > 0) {
                matchesSearch = (
                    title.includes(query) ||
                    desc.includes(query) ||
                    type.includes(query) ||
                    cls.includes(query)
                );
            }

            if (matchesCategory && matchesSearch) {
                card.show();
                visibleCount++;
            } else {
                card.hide();
            }
        });

        if (query.length > 0) {
            clearBtn.show();
        } else {
            clearBtn.hide();
        }

        if (visibleCount === 0 && cards.length > 0) {
            emptyState.show();
        } else {
            emptyState.hide();
        }
    }

    searchInput.on('input', function() {
        filterCards();
    });

    clearBtn.on('click', function() {
        searchInput.val('');
        filterCards();
        searchInput.focus();
    });

    $('.mob-filter-chip').on('click', function() {
        $('.mob-filter-chip').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        filterCards();
    });

    window.resetSearchAndFilters = function() {
        searchInput.val('');
        currentFilter = 'all';
        $('.mob-filter-chip').removeClass('active');
        $('.mob-filter-chip[data-filter="all"]').addClass('active');
        filterCards();
    };

    // =========================================================================
    // 2. Read More / Show Less Toggle
    // =========================================================================
    window.toggleReadMore = function(itemId) {
        const clamp = $('#descClamp-' + itemId);
        const btn = $('#readMoreBtn-' + itemId);

        if (clamp.hasClass('mob-dl-desc-clamp')) {
            clamp.removeClass('mob-dl-desc-clamp');
            btn.html('Show less <i class="fa fa-angle-up"></i>');
        } else {
            clamp.addClass('mob-dl-desc-clamp');
            btn.html('Read full details <i class="fa fa-angle-down"></i>');
        }
    };

    // =========================================================================
    // 3. Native Mobile Bottom-Sheet Controller
    // =========================================================================
    let currentOpenSheet = null;

    window.openSheet = function(sheetId) {
        closeActiveSheet();
        const sheet = document.getElementById(sheetId);
        const backdrop = document.getElementById('mobModalBackdrop');
        if (!sheet || !backdrop) return;

        currentOpenSheet = sheetId;
        backdrop.classList.add('show');
        sheet.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closeActiveSheet = function() {
        if (currentOpenSheet) {
            const sheet = document.getElementById(currentOpenSheet);
            if (sheet) sheet.classList.remove('show');
            currentOpenSheet = null;
        }
        const backdrop = document.getElementById('mobModalBackdrop');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';

        // Stop video playback when closing sheet
        const videoBody = document.getElementById('videoPlayerModalBody');
        if (videoBody) videoBody.innerHTML = '';
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && currentOpenSheet) {
            closeActiveSheet();
        }
    });

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    // =========================================================================
    // 4. Document / Image Preview Sheet Handler
    // =========================================================================
    window.previewFileAttachment = function(fileUrl, fileType, title, downloadUrl) {
        $('#previewFileModalTitle').html('<i class="fa fa-eye" style="color: #38bdf8;"></i> ' + escapeHtml(title));

        const body = $('#previewFileModalBody');
        body.empty();

        if (fileType === 'pdf') {
            body.html(`
                <div style="height: 65vh; width: 100%;">
                    <iframe src="${fileUrl}" width="100%" height="100%" style="border: none; border-radius: 4px;"></iframe>
                </div>
            `);
        } else {
            body.html(`
                <div style="max-height: 65vh; overflow: auto; text-align: center;">
                    <img src="${fileUrl}" style="max-width: 100%; max-height: 60vh; border-radius: 4px; object-fit: contain;" alt="Document">
                </div>
            `);
        }

        openSheet('previewFileModalSheet');
    };

    // =========================================================================
    // 5. Video Player Sheet Handler (Supports YouTube embed & direct videos)
    // =========================================================================
    function getYouTubeEmbedUrl(url) {
        if (!url) return '';
        // Match standard watch, shorts, or youtu.be links
        let videoId = '';
        const regExp = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
        const match = url.match(regExp);
        if (match && match[1]) {
            videoId = match[1];
            return 'https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&rel=0';
        }
        return url;
    }

    window.openVideoPlayerSheet = function(videoUrl, title) {
        $('#videoPlayerModalTitle').html('<i class="fa fa-youtube-play text-danger"></i> ' + escapeHtml(title));

        const body = $('#videoPlayerModalBody');
        body.empty();

        const embedUrl = getYouTubeEmbedUrl(videoUrl);
        if (embedUrl.includes('youtube') || embedUrl.includes('embed')) {
            body.html(`
                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 4px; background: #000;">
                    <iframe src="${embedUrl}" style="position: absolute; top:0; left: 0; width: 100%; height: 100%; border: none;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            `);
        } else {
            body.html(`
                <div style="text-align: center; background: #000; border-radius: 4px; padding: 4px;">
                    <video src="${videoUrl}" controls autoplay style="max-width: 100%; max-height: 60vh; border-radius: 4px;"></video>
                </div>
            `);
        }

        openSheet('videoPlayerModalSheet');
    };

    // =========================================================================
    // 6. Seamless File Download Handler with Modal Error Display
    // =========================================================================
    $(document).on('click', '.btn-file-download', function(e) {
        e.preventDefault();
        const btn = $(this);
        const downloadUrl = btn.attr('href');
        if (!downloadUrl || downloadUrl === '#' || downloadUrl === 'javascript:void(0);') return;

        const originalHtml = btn.html();
        btn.css('pointer-events', 'none').html('<i class="fa fa-spinner fa-spin"></i> Downloading...');

        fetch(downloadUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json, text/plain, */*'
            }
        })
        .then(async response => {
            btn.css('pointer-events', '').html(originalHtml);
            const contentType = response.headers.get('content-type') || '';

            if (!response.ok || contentType.includes('application/json')) {
                let errorMsg = 'File not found on server.';
                try {
                    const data = await response.json();
                    if (data && data.message) errorMsg = data.message;
                } catch (err) {}

                if (window.StudentModal && typeof window.StudentModal.open === 'function') {
                    StudentModal.open({
                        type: 'error',
                        title: 'File Not Available',
                        message: errorMsg
                    });
                } else {
                    alert(errorMsg);
                }
                return;
            }

            // Download file payload
            const blob = await response.blob();
            const disposition = response.headers.get('content-disposition') || '';
            let filename = 'downloaded_file';
            const filenameMatch = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1].replace(/['"]/g, '').trim();
            }
            const blobUrl = window.URL.createObjectURL(blob);
            const tempLink = document.createElement('a');
            tempLink.style.display = 'none';
            tempLink.href = blobUrl;
            tempLink.download = filename;
            document.body.appendChild(tempLink);
            tempLink.click();
            setTimeout(function() {
                window.URL.revokeObjectURL(blobUrl);
                tempLink.remove();
            }, 800);
        })
        .catch(function(err) {
            btn.css('pointer-events', '').html(originalHtml);
            if (window.StudentModal && typeof window.StudentModal.open === 'function') {
                StudentModal.open({
                    type: 'error',
                    title: 'File Not Available',
                    message: 'File not found on server.'
                });
            } else {
                alert('File not found on server.');
            }
        });
    });

});
</script>
@endsection
