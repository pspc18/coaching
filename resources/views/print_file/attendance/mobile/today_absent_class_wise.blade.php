@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $reportDate = $reportDate ?? Carbon::now();
    $selectedClassIds = (array) ($selectedClassIds ?? []);
    $absentCount = $absentCount ?? (isset($rows) ? count($rows) : 0);
    $rows = $rows ?? collect();
    $classes = $classes ?? collect();
    $isHoliday = $isHoliday ?? false;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* Absent Students - Mobile Layout */
.mob-absent-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-absent-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-absent-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Stats Row */
.mob-absent-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    margin-top: 6px;
}
.mob-stat-box {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 3px;
    padding: 4px 6px;
    text-align: center;
}
.mob-stat-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
}
.mob-stat-val {
    font-size: 12.5px;
    font-weight: 900;
    color: #ffffff;
}

/* Cards & Forms */
.mob-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 10px 12px;
    margin-bottom: 8px;
}
.mob-card-head {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}

.mob-form-group {
    margin-bottom: 8px;
}
.mob-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.mob-input, .mob-select {
    width: 100%;
    height: 32px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    padding: 0 8px;
    font-size: 11.5px;
    background: #ffffff;
    color: #0f172a;
}

/* Date Preset Pills */
.mob-date-presets {
    display: flex;
    gap: 4px;
    margin-bottom: 6px;
}
.mob-date-btn {
    padding: 3px 8px;
    font-size: 10px;
    font-weight: 700;
    background: #e2e8f0;
    color: #334155;
    border-radius: 2px;
    border: none;
    cursor: pointer;
}
.mob-date-btn:hover {
    background: #cbd5e1;
}

/* Action Buttons */
.mob-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 32px;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    width: 100%;
}
.mob-btn-primary {
    background: #002C54;
    color: #ffffff;
}
.mob-btn-danger {
    background: #e11d48;
    color: #ffffff;
}
.mob-btn-secondary {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}

/* Student Record Card */
.mob-student-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.mob-student-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-student-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
}
.mob-class-badge {
    font-size: 9.5px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0369a1;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #bae6fd;
}
.mob-student-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10.5px;
    color: #64748b;
}
.mob-phone-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #0f172a;
    font-weight: 600;
    text-decoration: none !important;
}
.mob-wa-btn {
    width: 20px;
    height: 20px;
    border-radius: 2px;
    background: #22c55e;
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    margin-left: 4px;
    text-decoration: none !important;
}
</style>
@endsection

@section('content')
<div class="content-wrapper" style="background: #f4f6f9; padding: 8px;">

    <!-- 1. Hero Header -->
    <div class="mob-absent-hero">
        <div class="mob-absent-top">
            <div class="mob-absent-title">
                <i class="fa fa-user-times text-warning"></i>
                <span>Absent Students</span>
            </div>
            <span class="badge badge-light" style="font-size: 9px; font-weight: 800; color: #002C54;">
                {{ $reportDate->format('d M Y') }}
            </span>
        </div>
        <div style="font-size: 10px; color: #cbd5e1; line-height: 1.2;">
            Class-wise absent students for {{ $reportDate->format('D, d F Y') }}.
        </div>

        <div class="mob-absent-stats">
            <div class="mob-stat-box">
                <div class="mob-stat-lbl">Total Absent</div>
                <div class="mob-stat-val" style="color:#fecdd3;">{{ $absentCount }}</div>
            </div>
            <div class="mob-stat-box">
                <div class="mob-stat-lbl">Classes</div>
                <div class="mob-stat-val" style="font-size:10px;">{{ count($selectedClassIds) ? count($selectedClassIds).' Sel' : 'All' }}</div>
            </div>
            <div class="mob-stat-box">
                <div class="mob-stat-lbl">Date</div>
                <div class="mob-stat-val" style="font-size:10.5px;">{{ $reportDate->format('d/m') }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Filter Card with Date Picker -->
    <form method="GET" action="{{ route('attendance.today.absent-class-wise') }}" id="mobAbsentForm">
        <div class="mob-card">
            <div class="mob-card-head">
                <span><i class="fa fa-filter text-primary"></i> Date &amp; Class Filter</span>
            </div>

            <div class="mob-form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mob-label mb-0"><i class="fa fa-calendar text-primary"></i> Select Date</label>
                    <div class="mob-date-presets">
                        <button type="button" class="mob-date-btn" onclick="setMobDate('{{ now()->toDateString() }}')">Today</button>
                        <button type="button" class="mob-date-btn" onclick="setMobDate('{{ now()->subDay()->toDateString() }}')">Yesterday</button>
                    </div>
                </div>
                <input type="date" name="date" id="mobDateInput" value="{{ $reportDate->format('Y-m-d') }}" class="mob-input">
            </div>

            <div class="mob-form-group">
                <label class="mob-label"><i class="fa fa-graduation-cap text-primary"></i> Target Classes</label>
                <select name="class_type_id[]" id="mob_class_type_id" class="mob-select" multiple style="height:60px;">
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ in_array($class->id, $selectedClassIds) ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="button" class="mob-btn mob-btn-secondary" id="mobSelectAllClasses" style="flex: 1;">
                    <i class="fa fa-check-square-o"></i> All Classes
                </button>
                <button type="submit" class="mob-btn mob-btn-primary" style="flex: 1.5;">
                    <i class="fa fa-search"></i> Filter
                </button>
            </div>
        </div>
    </form>

    <!-- 3. Download & Export Bar -->
    @if(count($rows) > 0)
        <div style="display: flex; gap: 6px; margin-bottom: 8px;">
            <a href="{{ route('attendance.today.absent-class-wise.pdf', ['date' => $reportDate->format('Y-m-d'), 'class_type_id' => $selectedClassIds]) }}" 
               target="_blank" 
               class="mob-btn mob-btn-danger" style="flex: 1;">
                <i class="fa fa-file-pdf-o"></i> Download PDF
            </a>
        </div>
    @endif

    <!-- 4. Absent Students List -->
    <div class="mob-card" style="padding: 6px;">
        <div class="mob-card-head" style="margin-bottom: 6px;">
            <span><i class="fa fa-users text-danger"></i> Absent Students ({{ count($rows) }})</span>
        </div>

        @forelse($rows as $index => $row)
            <div class="mob-student-card">
                <div class="mob-student-head">
                    <div>
                        <span style="font-weight: 800; color: #475569; font-size: 11px;">{{ $index + 1 }}.</span>
                        <span class="mob-student-name">{{ $row['name'] ?: '-' }}</span>
                        <span style="font-size: 9.5px; color: #64748b;">(Adm: {{ $row['admission_no'] ?: '-' }})</span>
                    </div>
                    <span class="mob-class-badge">{{ $row['class_name'] ?: 'N/A' }}</span>
                </div>

                <div class="mob-student-body">
                    <div>Father: <strong>{{ $row['father_name'] ?: '-' }}</strong></div>
                    <div>
                        @if(!empty($row['mobile']))
                            <a href="tel:{{ $row['mobile'] }}" class="mob-phone-link">
                                <i class="fa fa-phone text-muted"></i> {{ $row['mobile'] }}
                            </a>
                            <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $row['mobile']) }}" target="_blank" class="mob-wa-btn">
                                <i class="fa fa-whatsapp"></i>
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4" style="color: #64748b; font-size: 11.5px;">
                @if($isHoliday)
                    <div style="font-weight: 700; color: #0284c7; margin-bottom: 3px;">
                        <i class="fa fa-calendar-check-o mr-1"></i> School Holiday
                    </div>
                    <div>{{ $reportDate->format('d F Y') }} is marked as a holiday.</div>
                @else
                    <div style="font-weight: 700; color: #16a34a; margin-bottom: 3px;">
                        <i class="fa fa-check-circle mr-1"></i> No Absent Students!
                    </div>
                    <div>All students are present for {{ $reportDate->format('d F Y') }}.</div>
                @endif
            </div>
        @endforelse
    </div>

</div>
@endsection

@section('scripts')
<script>
function setMobDate(dateStr) {
    document.getElementById('mobDateInput').value = dateStr;
    document.getElementById('mobAbsentForm').submit();
}

$(document).ready(function() {
    $('#mobSelectAllClasses').on('click', function() {
        $('#mob_class_type_id option').prop('selected', true);
        $('#mobAbsentForm').submit();
    });
});
</script>
@endsection