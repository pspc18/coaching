@php
    $m = $studentMetrics[$student->id] ?? [];
    $gLabel = $m['grade_label'] ?? 'Grade 5';
    $scorePct = isset($m['grade_percentage']) ? (float)$m['grade_percentage'] : null;
    $attStr = $m['attendance'] ?? 'N/A';
    $attVal = floatval(str_replace('%', '', $attStr));
    $stName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    $admNo = $student->admissionNo ?: 'N/A';
    $clsName = $student->class_name ?: ($selectedClass->name ?? '-');
    $fatherName = $student->father_name ?: '';
    $initial = strtoupper(mb_substr($stName ?: 'S', 0, 1, 'UTF-8'));
    $topperRank = $topperMap[$student->id] ?? 0;

    $gClass = 'g5';
    if (strpos($gLabel, '1') !== false) $gClass = 'g1';
    elseif (strpos($gLabel, '2') !== false) $gClass = 'g2';
    elseif (strpos($gLabel, '3') !== false) $gClass = 'g3';
    elseif (strpos($gLabel, '4') !== false) $gClass = 'g4';

    $scoreProgressClass = 'poor';
    if ($scorePct !== null) {
        if ($scorePct >= 85) $scoreProgressClass = 'high';
        elseif ($scorePct >= 70) $scoreProgressClass = 'mid';
        elseif ($scorePct >= 50) $scoreProgressClass = 'low';
    }

    $attClass = 'na';
    if ($attStr !== 'N/A') {
        $attClass = ($attVal >= 75) ? 'good' : 'low';
    }

    $phone = $student->mobile ?: ($student->father_mobile ?: '');
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

    $searchText = strtolower($stName . ' ' . $admNo . ' ' . $clsName . ' ' . $fatherName . ' ' . $phone);
    $cardTopperClass = $topperRank > 0 ? ('is-topper-' . $topperRank) : '';
@endphp

<div class="mob-student-card {{ $cardTopperClass }}" data-search="{{ $searchText }}">
    <div class="mob-st-main">
        {{-- Avatar & Rank --}}
        <div class="mob-st-avatar-wrap">
            @if(!empty($student->image))
                <img src="{{ $imageBase ? $imageBase . '/profile/' . $student->image : asset('upload/profile/' . $student->image) }}" 
                     alt="{{ $stName }}" 
                     class="mob-st-avatar"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="mob-st-avatar-fallback" style="display: none;">{{ $initial }}</div>
            @else
                <div class="mob-st-avatar-fallback">{{ $initial }}</div>
            @endif

            @if($topperRank === 1)
                <span class="mob-st-topper-badge" title="Rank 1 Topper">🥇</span>
            @elseif($topperRank === 2)
                <span class="mob-st-topper-badge" title="Rank 2">🥈</span>
            @elseif($topperRank === 3)
                <span class="mob-st-topper-badge" title="Rank 3">🥉</span>
            @endif
        </div>

        {{-- Info Box --}}
        <div class="mob-st-info">
            <div class="mob-st-name-row">
                <div class="mob-st-name" title="{{ $stName }}">{{ $stName ?: 'Student' }}</div>
            </div>
            <div class="mob-st-meta-row">
                <b>ADM:</b> {{ $admNo }} &bull; {{ $clsName }}
            </div>
            @if(!empty($fatherName))
                <div class="mob-st-father">
                    <i class="fa fa-user-o mr-1"></i>S/o {{ $fatherName }}
                </div>
            @endif
        </div>

        {{-- Grade & Score --}}
        <div class="mob-st-grade-box">
            <span class="mob-grade-pill {{ $gClass }}">{{ $gLabel }}</span>
            <div class="mob-st-score">
                {{ $scorePct !== null ? number_format($scorePct, 1) . '%' : 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Score Mini Progress Bar --}}
    @if($scorePct !== null)
        <div class="mob-st-progress-row">
            <div class="mob-st-progress-bar">
                <div class="mob-st-progress-fill {{ $scoreProgressClass }}" style="width: {{ min(100, max(5, $scorePct)) }}%;"></div>
            </div>
        </div>
    @endif

    {{-- Bottom Bar: Attendance & Action Buttons --}}
    <div class="mob-st-action-bar">
        <div class="mob-st-att-badge {{ $attClass }}">
            <span class="mob-st-att-dot"></span>
            <span>Att: {{ $attStr }}</span>
        </div>

        <div class="mob-st-btn-group">
            <button type="button" 
                    class="mob-btn-card-action btn-snapshot" 
                    onclick="openMobSnapshot({{ $student->id }}, '{{ addslashes($stName) }}', '{{ addslashes($admNo) }}', '{{ addslashes($clsName) }}', '{{ addslashes($fatherName) }}', '{{ addslashes($gLabel) }}', {{ $scorePct !== null ? $scorePct : 'null' }}, '{{ addslashes($attStr) }}', {{ $topperRank ?: 'null' }}, '{{ addslashes($phone) }}')">
                <i class="fa fa-eye"></i> Snapshot
            </button>

            <a href="{{ url('studentParticularPerformance/' . $student->id . '?layout=mobile') }}" 
               class="mob-btn-card-action btn-analytics"
               title="View Complete Academic Analytics">
                <i class="fa fa-line-chart"></i> Analytics
            </a>

            @if(!empty($phone))
                <a href="tel:{{ $phone }}" class="mob-icon-circle-btn" title="Call Parent">
                    <i class="fa fa-phone"></i>
                </a>
                <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="mob-icon-circle-btn whatsapp" title="Chat on WhatsApp">
                    <i class="fa fa-whatsapp"></i>
                </a>
            @endif
        </div>
    </div>
</div>
