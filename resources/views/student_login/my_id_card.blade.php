
@php
$getUser = Helper::getUser();
$cardWidth  = isset($design['cardWidth']) ? $design['cardWidth'].'mm' : '70mm';
    $cardHeight = isset($design['cardHeight']) ? $design['cardHeight'].'mm' : '100mm';
@endphp
@extends('student_login.layout.app')
@section('title', 'Id Card')
@section('page_title', 'ID CARD')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box d-flex m-2">


<style>
    .id-card {
        position: relative;
        margin: 20px auto;
        background-size: {{ $design['bgWidthPercent'] ?? 100 }}% {{ $design['bgHeightPercent'] ?? 100 }}%;
        background-repeat: no-repeat;
        background-position: center;
        border: 1px solid #ccc;
  padding: 2mm;
    }
    .id-field {
        position: absolute;
       
    }
    .id-image {
        position: absolute;
       
    }
    .seal-img {
        position: absolute;
        object-fit: contain;
    }
    
    .download-btn {
    position: absolute;
    top: 5px;
    left: 5px;
    padding: 5px 10px;
    font-size: 14px;
    background: #004f51;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    opacity: 0;               /* start hidden */
    transform: translateY(-10px); /* optional slide-up effect */
    z-index: 10;
    transition: opacity 0.5s, transform 0.5s;
}
    .download-btn img{
        width:30px;
    }
    /* Show button on hover */
   .id-card-wrapper:hover .download-btn {
    opacity: 1;               /* fade in */
    transform: translateY(0); /* slide into place */
}
.download-overlay {
    position: absolute; /* stays on card only */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
    z-index: 50;
}

.download-overlay.active {
    opacity: 1;
    pointer-events: all;
}

.download-overlay .spinner img {
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

.download-overlay .spinner p {
    color: #fff;
    font-weight: bold;
    font-size: 16px;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<div class="id-card id-card-wrapper" id="studentCard" style="width: {{ $cardWidth }};transform: scale(0.9); height: {{ $cardHeight }}; background-image: url('{{ env('IMAGE_SHOW_PATH') }}/id_template_bg/{{ $template->bg_image }}');">

   
    @php $imgPos = collect($design['positions'])->firstWhere('field', 'image'); @endphp
    @if($imgPos && (collect($design['fields'])->firstWhere('field','image')['visible'] ?? true))
        <img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$student['image'] }}"
             class="id-image"
             style="top: {{ $imgPos['top'] }}; left: {{ $imgPos['left'] }};
                    width: {{ $design['studentImgWidth'] ?? 100 }}px;
                    height: {{ $design['studentImgHeight'] ?? 120 }}px;
                    border-radius: {{ $imgPos['borderRadius'] ?? '0px' }};">
    @endif

    
    @foreach($design['positions'] as $pos)
        @if($pos['field'] !== 'image' && $pos['field'] !== 'seal')
            @php $visible = collect($design['fields'])->firstWhere('field',$pos['field'])['visible'] ?? true; @endphp
            @if($visible)
                <div class="id-field"
                     style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }};
                            font-size: {{ $pos['fontSize'] ?? '14px' }};
                            color: {{ $pos['color'] ?? '#000' }};">
                            @switch($pos['field'])
                                @case('name') {{ $student->first_name }} {{ $student->last_name }} @break
                                @case('srno') {{ $student->admissionNo }} @break
                                @case('father') {{ $student->father_name }} @break
                                @case('mother') {{ $student->mother_name ?? '' }} @break
                                @case('class') {{ $student->class_name }} @break
                                @case('dob') {{ \Carbon\Carbon::parse($student->dob)->format('d-m-Y') }} @break
                                @case('phone') {{ $student->mobile }} @break
                                @case('address') {{ $student->address }} @break
                            @endswitch
                </div>
            @endif
        @endif
    @endforeach

   
    @php $sealPos = collect($design['positions'])->firstWhere('field', 'seal'); @endphp
    @if($sealPos && (collect($design['fields'])->firstWhere('field','seal')['visible'] ?? false))
        <img src="{{ env('IMAGE_SHOW_PATH') }}/setting/seal_sign/{{ $template->seal_sign ?? 'default/seal.png' }}"
             class="seal-img"
             style="top: {{ $sealPos['top'] }}; left: {{ $sealPos['left'] }};
                    width: {{ $design['sealWidth'] ?? 60 }}px;
                    height: {{ $design['sealHeight'] ?? 30 }}px;">
    @endif
    <button class="download-btn" id="downloadBtn"><img src="{{ env('IMAGE_SHOW_PATH').'icons/download_icon.png' }}"></button>
    <div class="download-overlay" id="downloadOverlay">
    <div class="spinner">
        <p>Downloading...</p>
    </div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById("downloadBtn").addEventListener("click", function () {
    const card = document.getElementById("studentCard");
    const overlay = document.getElementById("downloadOverlay");

    // Show overlay on screen
    overlay.classList.add('active');

    // Let the browser render the overlay first
    setTimeout(() => {
        // Temporarily hide overlay for capture
        overlay.style.opacity = 0;

        html2canvas(card, {
            backgroundColor: null,
            scale: 3 // HD
        }).then(canvas => {
            const link = document.createElement("a");
            link.download = "{{ $student->first_name }} ({{ $student->admissionNo }}).jpeg";
            link.href = canvas.toDataURL("image/jpeg", 1.0);
            link.click();

            // Restore overlay visibility and hide after download
            overlay.style.opacity = '';
            overlay.classList.remove('active');
        });
    }, 50); // wait 50ms
});
</script>
@endsection