@php
    $getUser = Helper::getUser();
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    if (empty($studentName)) {
        $studentName = 'Student';
    }
    $firstLetter = strtoupper(mb_substr(trim($student->first_name ?? 'S'), 0, 1, 'UTF-8')) ?: 'S';
    $className = $student->class_name ?? ($getUser['ClassTypes']['name'] ?? '-');
    $currentSessionName = Session::get('session_name') ?? date('Y');

    $cardWidth  = isset($design['cardWidth']) ? $design['cardWidth'].'mm' : '54mm';
    $cardHeight = isset($design['cardHeight']) ? $design['cardHeight'].'mm' : '86mm';
    $bgWidthPercent  = $design['bgWidthPercent'] ?? 100;
    $bgHeightPercent = $design['bgHeightPercent'] ?? 100;

    $imageShowPath = rtrim(env('IMAGE_SHOW_PATH') ?: config('app.image_show_path') ?: '', '/');
    $hasPhoto = !empty($student->image);
    $studentPhotoUrl = $hasPhoto ? $imageShowPath . '/profile/' . $student->image : '';

    $bgImageUrl = !empty($template->bg_image) ? $imageShowPath . '/id_template_bg/' . $template->bg_image : '';
    $sealUrl = !empty($template->seal_sign)
        ? $imageShowPath . '/setting/seal_sign/' . $template->seal_sign
        : $imageShowPath . '/setting/seal_sign/default/seal.png';

    // Calculate optimal font size for name to stay centered and never wrap
    $nameLen = mb_strlen($studentName);
    $nameFontSize = ($nameLen > 24) ? '10.5px' : (($nameLen > 18) ? '11.5px' : '13px');

    // Values to display matching template fields
    $rollNoVal = !empty($student->roll_no) ? $student->roll_no : ($student->admissionNo ?? '');
    $phoneVal = $student->mobile ?? '';
    $addressVal = $student->address ?? '';
@endphp

@extends('student_login.layout.app')

@section('title', 'Identity Card')
@section('page_title', 'Identity Card')
@section('page_sub', $studentName . ' - ' . $className)

@section('content')
<style>
/* ==========================================================================
   STUDENT ID CARD - NATIVE MOBILE THEME REDESIGN
   Centered Card Stage, Pixel-Matched Background Text & Dynamic Avatar
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

/* 2. Centered ID Card Stage */
.mob-id-stage {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 6px 4px 16px;
    margin: 0 auto;
    box-sizing: border-box;
}

.mob-id-actions {
    margin-top: 14px;
    display: flex;
    justify-content: center;
    width: 100%;
}

.mob-id-btn-download {
    height: 36px;
    padding: 0 20px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none;
    border: 1px solid #0284c7;
    cursor: pointer;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    box-shadow: 0 3px 10px rgba(2, 132, 199, 0.35);
    transition: all 0.18s ease;
}
.mob-id-btn-download:hover,
.mob-id-btn-download:active {
    background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
    transform: translateY(-1px);
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
    background-color: #ffffff;
    box-sizing: border-box;
}

/* Photo Frame - Horizontally Centered inside Circular Frame */
.id-photo-frame {
    position: absolute;
    top: 26.8%;
    left: 50%;
    transform: translateX(-50%);
    width: 81px;
    height: 81px;
    border-radius: 50%;
    overflow: hidden;
    z-index: 5;
    box-sizing: border-box;
}
.id-photo-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}

/* Letter Avatar Fallback Box */
.id-letter-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7 0%, #002C54 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 34px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    text-transform: uppercase;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.25);
    user-select: none;
    box-sizing: border-box;
}

/* Student Name - Perfectly Centered Horizontally between Photo Circle & STUDENT badge */
.id-student-name {
    position: absolute;
    top: 53.6%;
    left: 0;
    width: 100%;
    text-align: center;
    font-size: {{ $nameFontSize }};
    font-weight: 800;
    letter-spacing: 0.3px;
    color: #0b1727;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0 8px;
    line-height: 1.15;
    z-index: 10;
    box-sizing: border-box;
}

/* Details Rows - Aligned Right after the Colons on Background Image */
.id-row-value {
    position: absolute;
    left: 36.5%;
    max-width: 58%;
    font-size: 8.5px;
    font-weight: 700;
    color: #111827;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    z-index: 10;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Precise Baseline Heights Matching Pre-Printed Labels (Shifted 1px up) */
.id-row-class {
    top: 65.1%;
}
.id-row-srno {
    top: 69.1%;
}
.id-row-phone {
    top: 73.1%;
}
.id-row-address {
    top: 77.2%;
}

.seal-img {
    position: absolute;
    object-fit: contain;
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
    .mob-id-actions,
    .mobile-header,
    .mobile-bottom-nav,
    .mob-drawer {
        display: none !important;
    }
    .mob-id-stage {
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

    {{-- 2. Center Screen ID Card Stage --}}
    <div class="mob-id-stage">
        <div class="id-card-wrapper">
            <div class="id-card" id="studentCard" style="width: {{ $cardWidth }}; height: {{ $cardHeight }}; background-image: url('{{ $bgImageUrl }}');">

                {{-- Student Photo or First Letter Avatar Fallback (Centered in Circular Frame) --}}
                <div class="id-photo-frame">
                    @if($hasPhoto)
                        <img src="{{ $studentPhotoUrl }}"
                             id="studentPhotoElement"
                             alt="{{ $studentName }}"
                             crossorigin="anonymous"
                             onerror="this.style.display='none'; var fa=document.getElementById('studentLetterAvatar'); if(fa) fa.style.display='flex';">
                        <div id="studentLetterAvatar" class="id-letter-avatar" style="display: none;">
                            <span>{{ $firstLetter }}</span>
                        </div>
                    @else
                        <div id="studentLetterAvatar" class="id-letter-avatar" style="display: flex;">
                            <span>{{ $firstLetter }}</span>
                        </div>
                    @endif
                </div>

                {{-- Student Name: Perfectly Centered Horizontally Across Card --}}
                <div class="id-student-name" title="{{ $studentName }}">
                    {{ $studentName }}
                </div>

                {{-- Field Row 1: Class (Aligned exactly with "Class :" in Background Image) --}}
                <div class="id-row-value id-row-class" title="{{ $className }}">
                    {{ $className }}
                </div>

                {{-- Field Row 2: Roll No (Aligned exactly with "Roll No :" in Background Image) --}}
                <div class="id-row-value id-row-srno" title="{{ $rollNoVal }}">
                    {{ $rollNoVal }}
                </div>

                {{-- Field Row 3: Mobile (Aligned exactly with "Mobile :" in Background Image) --}}
                <div class="id-row-value id-row-phone" title="{{ $phoneVal }}">
                    {{ $phoneVal }}
                </div>

                {{-- Field Row 4: Address (Aligned exactly with "Address :" in Background Image) --}}
                <div class="id-row-value id-row-address" title="{{ $addressVal }}">
                    {{ $addressVal }}
                </div>

                {{-- Authorized Seal / Signature (if configured in template) --}}
                @php $sealPos = collect($design['positions'] ?? [])->firstWhere('field', 'seal'); @endphp
                @if($sealPos && (collect($design['fields'] ?? [])->firstWhere('field', 'seal')['visible'] ?? false))
                    <img src="{{ $sealUrl }}"
                         class="seal-img"
                         alt="Seal"
                         crossorigin="anonymous"
                         style="top: {{ $sealPos['top'] ?? '85%' }}; left: {{ $sealPos['left'] ?? '70%' }};
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

        {{-- Download ID Card Button below ID Card --}}
        <div class="mob-id-actions">
            <button type="button" class="mob-id-btn-download" id="downloadIdCardBtn">
                <i class="fa fa-download"></i> Download ID Card
            </button>
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