@php
    $m = $studentMetrics[$student->id] ?? [];
    $gLabel = $m['grade_label'] ?? 'Grade 5';
    $scorePct = isset($m['grade_percentage']) ? (float)$m['grade_percentage'] : null;
    $attStr = $m['attendance'] ?? 'N/A';
    $attVal = floatval(str_replace('%', '', $attStr));
    $isLowAtt = ($attStr !== 'N/A' && $attVal < 75);

    $stName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    $admNo = $student->admissionNo ?: 'N/A';
    $clsName = $student->class_name ?: ($selectedClass->name ?? '-');
    $fatherName = $student->father_name ?: '';
    $initial = strtoupper(mb_substr($stName ?: 'S', 0, 1, 'UTF-8'));
    $topperRank = $topperMap[$student->id] ?? 0;

    // Determine Grade Category
    $gradeCat = 'needs_help';
    $badgeClass = 'badge-g5';
    $borderClass = 'border-help';
    if (strpos($gLabel, '1') !== false) {
        $gradeCat = 'g1';
        $badgeClass = 'badge-g1';
        $borderClass = 'border-g1';
    } elseif (strpos($gLabel, '2') !== false) {
        $gradeCat = 'g2';
        $badgeClass = 'badge-g2';
        $borderClass = 'border-g2';
    } elseif (strpos($gLabel, '3') !== false) {
        $gradeCat = 'g3';
        $badgeClass = 'badge-g3';
        $borderClass = 'border-g3';
    } elseif (strpos($gLabel, '4') !== false) {
        $gradeCat = 'needs_help';
        $badgeClass = 'badge-g4';
        $borderClass = 'border-help';
    }

    if ($isLowAtt) {
        $borderClass = 'border-low';
    }

    $scoreProgressClass = 'poor';
    if ($scorePct !== null) {
        if ($scorePct >= 85) $scoreProgressClass = 'high';
        elseif ($scorePct >= 70) $scoreProgressClass = 'mid';
        elseif ($scorePct >= 50) $scoreProgressClass = 'low';
    }

    $phone = $student->mobile ?: ($student->father_mobile ?: '');
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

    $searchText = strtolower($stName . ' ' . $admNo . ' ' . $clsName . ' ' . $fatherName . ' ' . $phone);
@endphp

<div class="student-mob-card {{ $borderClass }}" 
     data-grade-cat="{{ $gradeCat }}" 
     data-low-att="{{ $isLowAtt ? '1' : '0' }}" 
     data-search="{{ $searchText }}">
    
    {{-- Card Header --}}
    <div class="student-card-header">
        <div class="student-card-profile-wrap">
            {{-- Avatar & Topper Badge --}}
            @if(!empty($student->image))
                <img src="{{ $imageBase ? $imageBase . '/profile/' . $student->image : asset('upload/profile/' . $student->image) }}" 
                     alt="{{ $stName }}" 
                     class="student-avatar"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="student-avatar-fallback" style="display: none;">
                    {{ $initial }}
                    @if($topperRank === 1)<span class="topper-rank-crown">🥇</span>@endif
                    @if($topperRank === 2)<span class="topper-rank-crown">🥈</span>@endif
                    @if($topperRank === 3)<span class="topper-rank-crown">🥉</span>@endif
                </div>
            @else
                <div class="student-avatar-fallback">
                    {{ $initial }}
                    @if($topperRank === 1)<span class="topper-rank-crown">🥇</span>@endif
                    @if($topperRank === 2)<span class="topper-rank-crown">🥈</span>@endif
                    @if($topperRank === 3)<span class="topper-rank-crown">🥉</span>@endif
                </div>
            @endif

            {{-- Name & Badges --}}
            <div class="student-primary-info">
                <div class="student-name-text" title="{{ $stName }}">{{ $stName ?: 'Student' }}</div>
                <div class="student-badges-row">
                    <span class="student-adm-badge">ADM: {{ $admNo }}</span>
                    <span class="student-class-badge">{{ $clsName }}</span>
                </div>
            </div>
        </div>

        {{-- Grade & Score --}}
        <div class="student-grade-badge-wrap">
            <span class="mob-grade-badge {{ $badgeClass }}">{{ $gLabel }}</span>
            <div class="student-score-num">
                {{ $scorePct !== null ? number_format($scorePct, 1) . '%' : 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Details Meta Grid --}}
    <div class="student-meta-grid">
        <div class="student-meta-item">
            <i class="fa fa-user-o"></i>
            <span>{{ !empty($fatherName) ? 'S/o ' . $fatherName : 'Father: -' }}</span>
        </div>
        <div class="student-meta-item">
            <i class="fa fa-calendar-check-o"></i>
            <span>Att: <b class="{{ ($attStr !== 'N/A' && $attVal >= 75) ? 'text-success' : ($attStr !== 'N/A' ? 'text-danger' : 'text-muted') }}">{{ $attStr }}</b></span>
        </div>
    </div>

    {{-- Score Progress Bar --}}
    @if($scorePct !== null)
        <div class="student-progress-wrap">
            <div class="student-progress-bar">
                <div class="student-progress-fill {{ $scoreProgressClass }}" style="width: {{ min(100, max(5, $scorePct)) }}%;"></div>
            </div>
        </div>
    @endif

    {{-- Card Actions Row --}}
    <div class="student-card-actions">
        @if(!empty($phone))
            <a href="tel:{{ $phone }}" class="mob-btn-action btn-act-phone" title="Call Parent">
                <i class="fa fa-phone"></i>
            </a>
            <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="student-wa-btn" title="Chat on WhatsApp">
                <i class="fa fa-whatsapp"></i>
            </a>
        @endif

        <button type="button" 
                class="mob-btn-action btn-act-snapshot" 
                onclick="openMobSnapshot({{ $student->id }}, '{{ addslashes($stName) }}', '{{ addslashes($admNo) }}', '{{ addslashes($clsName) }}', '{{ addslashes($fatherName) }}', '{{ addslashes($gLabel) }}', {{ $scorePct !== null ? $scorePct : 'null' }}, '{{ addslashes($attStr) }}', {{ $topperRank ?: 'null' }}, '{{ addslashes($phone) }}')">
            <i class="fa fa-eye"></i> Snapshot
        </button>

        <a href="{{ url('studentParticularPerformance/' . $student->id . '?layout=mobile') }}" 
           class="mob-btn-action btn-act-profile"
           title="Open Full Academic Analytics">
            <i class="fa fa-line-chart"></i> View Analytics
        </a>
    </div>

</div>
