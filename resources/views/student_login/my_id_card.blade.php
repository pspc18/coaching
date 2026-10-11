@php
    $getUser = Helper::getUser();
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    if (empty($studentName)) {
        $studentName = 'Student';
    }
    $firstLetter = strtoupper(mb_substr(trim($student->first_name ?? 'S'), 0, 1, 'UTF-8')) ?: 'S';
    $className = $student->class_name ?? ($getUser['ClassTypes']['name'] ?? '-');
    $currentSessionName = Session::get('session_name') ?? date('Y');

    $cardWidth  = isset($design['cardWidth']) ? $design['cardWidth'].'mm' : '70mm';
    $cardHeight = isset($design['cardHeight']) ? $design['cardHeight'].'mm' : '100mm';
    $bgWidthPercent  = $design['bgWidthPercent'] ?? 100;
    $bgHeightPercent = $design['bgHeightPercent'] ?? 100;

    $imageShowPath = rtrim(env('IMAGE_SHOW_PATH') ?: config('app.image_show_path') ?: '', '/');
    $hasPhoto = !empty($student->image);
    $studentPhotoUrl = $hasPhoto ? $imageShowPath . '/profile/' . $student->image : '';

    $bgImageUrl = !empty($template->bg_image) ? $imageShowPath . '/id_template_bg/' . $template->bg_image : '';
    $sealUrl = !empty($template->seal_sign)
        ? $imageShowPath . '/setting/seal_sign/' . $template->seal_sign
        : $imageShowPath . '/setting/seal_sign/default/seal.png';
@endphp

@extends('student_login.layout.app')

@section('title', 'Identity Card')
@section('page_title', 'Identity Card')
@section('page_sub', $studentName . ' - ' . $className)

@section('content')
<style>
/* ==========================================================================
   STUDENT ID CARD - NATIVE MOBILE THEME REDESIGN
   Centered Card Stage, Letter Avatar Fallback & Action Toolbar
   ========================================================================== */

/* 1. Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001f3f 0%, #002C54 55%, #0a4275 100%);
    border-radius: 8px;
    padding: 10px 12px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0, 20, 40, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #ffffff;
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 700;
    letter-spacing: -0.2px;
    display: flex;
    align-items: center;
    gap: 7px;
    color: #ffffff;
}
.mob-session-pill {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    color: #e2e8f0;
    white-space: nowrap;
}
.mob-hero-meta-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 6px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 11px;
    color: #cbd5e1;
}

/* 2. Action Toolbar */
.mob-id-toolbar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.mob-id-btn {
    height: 32px;
    padding: 0 16px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.18s ease;
}
.mob-id-btn-download {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    border-color: #0284c7;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
}
.mob-id-btn-download:hover,
.mob-id-btn-download:active {
    background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
    transform: translateY(-1px);
}
.mob-id-btn-print {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #cbd5e1;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.mob-id-btn-print:hover {
    background: #f8fafc;
}

/* 3. Centered ID Card Viewport */
.mob-id-viewport {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 220px);
    width: 100%;
    padding: 8px 4px 24px;
    box-sizing: border-box;
    overflow-x: auto;
}

.id-card-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    filter: drop-shadow(0 12px 28px rgba(0, 20, 40, 0.32));
}

/* The Exact ID Card Layout */
.id-card {
    position: relative;
    margin: 0 auto;
    background-size: {{ $bgWidthPercent }}% {{ $bgHeightPercent }}%;
    background-repeat: no-repeat;
    background-position: center;
    border: 1px solid rgba(0, 0, 0, 0.14);
    border-radius: 8px;
    overflow: hidden;
    padding: 2mm;
    background-color: #ffffff;
    box-sizing: border-box;
}

.id-field {
    position: absolute;
    line-height: 1.25;
    white-space: nowrap;
}

.id-image {
    position: absolute;
    object-fit: cover;
}

.seal-img {
    position: absolute;
    object-fit: contain;
}

/* Letter Avatar Fallback Box */
.id-letter-avatar {
    position: absolute;
    background: linear-gradient(135deg, #0284c7 0%, #002C54 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    text-transform: uppercase;
    border: 1.5px solid rgba(255, 255, 255, 0.5);
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0, 0, 0, 0.18);
    user-select: none;
    z-index: 5;
    box-sizing: border-box;
}

/* Download Overlay */
.download-overlay {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 20, 40, 0.75);
    backdrop-filter: blur(2px);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
    border-radius: 8px;
    z-index: 50;
    color: #ffffff;
}
.download-overlay.active {
    opacity: 1;
    pointer-events: all;
}
.download-overlay .spinner-box {
    text-align: center;
}
.download-overlay .spinner-box i {
    font-size: 26px;
    color: #38bdf8;
    margin-bottom: 8px;
}
.download-overlay .spinner-box p {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

/* Print Handling */
@media print {
    body {
        background: #ffffff !important;
    }
    .mob-hero-card,
    .mob-id-toolbar,
    .mobile-header,
    .mobile-bottom-nav,
    .mob-drawer {
        display: none !important;
    }
    .mob-id-viewport {
        min-height: auto !important;
        padding: 0 !important;
        display: block !important;
    }
    .id-card-wrapper {
        filter: none !important;
        margin: 20px auto !important;
    }
}
</style>

<div class="container-fluid px-2 py-1">

    {{-- 1. Signature Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-id-card-o text-warning"></i> Student Identity Card
            </div>
            <span class="mob-session-pill">
                <i class="fa fa-graduation-cap"></i> {{ $currentSessionName }}
            </span>
        </div>
        <div class="mob-hero-meta-row">
            <span><i class="fa fa-user me-1 text-info"></i> <b>{{ $studentName }}</b></span>
            <span><i class="fa fa-graduation-cap me-1 text-success"></i> Class: <b>{{ $className }}</b></span>
            <span><i class="fa fa-hashtag me-1 text-warning"></i> SR: <b>{{ $student->admissionNo }}</b></span>
        </div>
    </div>

    {{-- 2. Action Toolbar --}}
    <div class="mob-id-toolbar">
        <button type="button" class="mob-id-btn mob-id-btn-download" id="downloadIdCardBtn">
            <i class="fa fa-download"></i> Download ID Card
        </button>
        <button type="button" class="mob-id-btn mob-id-btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Print
        </button>
    </div>

    {{-- 3. Center Screen ID Card Stage --}}
    <div class="mob-id-viewport">
        <div class="id-card-wrapper">
            <div class="id-card" id="studentCard" style="width: {{ $cardWidth }}; height: {{ $cardHeight }}; background-image: url('{{ $bgImageUrl }}');">

                {{-- Student Photo or First Letter Avatar Fallback --}}
                @php
                    $imgPos = collect($design['positions'])->firstWhere('field', 'image');
                    $imgVisible = collect($design['fields'])->firstWhere('field', 'image')['visible'] ?? true;
                    $imgWidth = $design['studentImgWidth'] ?? 100;
                    $imgHeight = $design['studentImgHeight'] ?? 120;
                    $imgRadius = $imgPos['borderRadius'] ?? '0px';
                    $letterFontSize = round($imgHeight * 0.42);
                @endphp

                @if($imgPos && $imgVisible)
                    @if($hasPhoto)
                        <img src="{{ $studentPhotoUrl }}"
                             class="id-image"
                             id="studentPhotoElement"
                             alt="{{ $studentName }}"
                             crossorigin="anonymous"
                             onerror="this.style.display='none'; var fa=document.getElementById('studentLetterAvatar'); if(fa) fa.style.display='flex';"
                             style="top: {{ $imgPos['top'] }}; left: {{ $imgPos['left'] }};
                                    width: {{ $imgWidth }}px;
                                    height: {{ $imgHeight }}px;
                                    border-radius: {{ $imgRadius }};">
                        {{-- Fallback Letter Avatar (Hidden by default, shown if image 404s/fails) --}}
                        <div id="studentLetterAvatar"
                             class="id-letter-avatar"
                             style="display: none; top: {{ $imgPos['top'] }}; left: {{ $imgPos['left'] }};
                                    width: {{ $imgWidth }}px;
                                    height: {{ $imgHeight }}px;
                                    border-radius: {{ $imgRadius }};
                                    font-size: {{ $letterFontSize }}px;">
                            <span>{{ $firstLetter }}</span>
                        </div>
                    @else
                        {{-- No Photo in DB: Show First Letter Avatar Directly --}}
                        <div id="studentLetterAvatar"
                             class="id-letter-avatar"
                             style="display: flex; top: {{ $imgPos['top'] }}; left: {{ $imgPos['left'] }};
                                    width: {{ $imgWidth }}px;
                                    height: {{ $imgHeight }}px;
                                    border-radius: {{ $imgRadius }};
                                    font-size: {{ $letterFontSize }}px;">
                            <span>{{ $firstLetter }}</span>
                        </div>
                    @endif
                @endif

                {{-- Dynamic Fields Positioned as per Template Design --}}
                @foreach($design['positions'] as $pos)
                    @if($pos['field'] !== 'image' && $pos['field'] !== 'seal')
                        @php $visible = collect($design['fields'])->firstWhere('field', $pos['field'])['visible'] ?? true; @endphp
                        @if($visible)
                            <div class="id-field"
                                 style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }};
                                        font-size: {{ $pos['fontSize'] ?? '14px' }};
                                        color: {{ $pos['color'] ?? '#000000' }};
                                        font-weight: 600;">
                                @switch($pos['field'])
                                    @case('name') {{ $student->first_name }} {{ $student->last_name }} @break
                                    @case('srno') {{ $student->admissionNo }} @break
                                    @case('father') {{ $student->father_name }} @break
                                    @case('mother') {{ $student->mother_name ?? '' }} @break
                                    @case('class') {{ $student->class_name }} @break
                                    @case('dob') {{ !empty($student->dob) ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : '' }} @break
                                    @case('phone') {{ $student->mobile }} @break
                                    @case('address') {{ $student->address }} @break
                                    @default {{ $student->{$pos['field']} ?? '' }}
                                @endswitch
                            </div>
                        @endif
                    @endif
                @endforeach

                {{-- Authorized Seal / Signature --}}
                @php $sealPos = collect($design['positions'])->firstWhere('field', 'seal'); @endphp
                @if($sealPos && (collect($design['fields'])->firstWhere('field', 'seal')['visible'] ?? false))
                    <img src="{{ $sealUrl }}"
                         class="seal-img"
                         alt="Seal"
                         crossorigin="anonymous"
                         style="top: {{ $sealPos['top'] }}; left: {{ $sealPos['left'] }};
                                width: {{ $design['sealWidth'] ?? 60 }}px;
                                height: {{ $design['sealHeight'] ?? 30 }}px;">
                @endif

                {{-- Download HD Capture Overlay --}}
                <div class="download-overlay" id="downloadOverlay">
                    <div class="spinner-box">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p>Generating ID Card...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
$(document).ready(function() {
    const downloadBtn = document.getElementById("downloadIdCardBtn");
    const card = document.getElementById("studentCard");
    const overlay = document.getElementById("downloadOverlay");

    if (downloadBtn && card) {
        downloadBtn.addEventListener("click", function() {
            const originalBtnHtml = downloadBtn.innerHTML;
            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Downloading...';

            if (overlay) overlay.classList.add("active");

            setTimeout(function() {
                if (overlay) overlay.style.opacity = "0";

                html2canvas(card, {
                    backgroundColor: null,
                    scale: 3, // Crisp HD export
                    useCORS: true,
                    allowTaint: true,
                    logging: false
                }).then(function(canvas) {
                    const link = document.createElement("a");
                    link.download = "{{ addslashes($student->first_name) }}_ID_Card_{{ $student->admissionNo }}.jpeg";
                    link.href = canvas.toDataURL("image/jpeg", 0.95);
                    link.click();

                    if (overlay) {
                        overlay.style.opacity = "";
                        overlay.classList.remove("active");
                    }
                    downloadBtn.disabled = false;
                    downloadBtn.innerHTML = originalBtnHtml;
                }).catch(function(err) {
                    if (overlay) {
                        overlay.style.opacity = "";
                        overlay.classList.remove("active");
                    }
                    downloadBtn.disabled = false;
                    downloadBtn.innerHTML = originalBtnHtml;

                    if (window.StudentModal && typeof window.StudentModal.open === "function") {
                        StudentModal.open({
                            type: "error",
                            title: "Download Failed",
                            message: "Unable to generate image of ID card. Please use Print option instead."
                        });
                    } else {
                        alert("Unable to generate image of ID card. Please use Print option.");
                    }
                });
            }, 100);
        });
    }
});
</script>
@endsection