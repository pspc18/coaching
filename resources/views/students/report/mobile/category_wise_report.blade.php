@extends('layout.mobile_app')

@php
    $session = DB::table('sessions')->where('id', Session::get('session_id'))->whereNull('deleted_at')->first();
    $sessionLabel = trim(($session->from_year ?? '') . '-' . ($session->to_year ?? ''));
    if (empty($sessionLabel)) {
        $sessionLabel = date('Y') . '-' . (date('Y') + 1);
    }

    $totalStudents = (int) ($grandTotals['total'] ?? 0);
    $totalBoys = (int) ($grandTotals['boys'] ?? 0);
    $totalGirls = (int) ($grandTotals['girls'] ?? 0);
    $boysPercent = $totalStudents > 0 ? round(($totalBoys / $totalStudents) * 100, 1) : 0;
    $girlsPercent = $totalStudents > 0 ? round(($totalGirls / $totalStudents) * 100, 1) : 0;
    $genderRatio = $totalBoys > 0 ? round(($totalGirls / $totalBoys) * 1000) : 0;
    $classCount = count($classType ?? []);

    // Compute category level totals across all classes for top insights
    $categoryTotals = [];
    foreach ($categories as $cat) {
        $cBoys = 0;
        $cGirls = 0;
        foreach ($classType as $type) {
            $cBoys += (int) ($classTotals[$type->id]['categories'][$cat]['boys'] ?? 0);
            $cGirls += (int) ($classTotals[$type->id]['categories'][$cat]['girls'] ?? 0);
        }
        $cTotal = $cBoys + $cGirls;
        $categoryTotals[$cat] = [
            'boys'    => $cBoys,
            'girls'   => $cGirls,
            'total'   => $cTotal,
            'percent' => $totalStudents > 0 ? round(($cTotal / $totalStudents) * 100, 1) : 0,
        ];
    }

    // Color definitions for each category badge
    $catColors = [
        'GENERAL' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'bar' => '#6366f1', 'border' => '#c7d2fe'],
        'OBC'     => ['bg' => '#fef3c7', 'text' => '#92400e', 'bar' => '#f59e0b', 'border' => '#fde68a'],
        'SC'      => ['bg' => '#f3e8ff', 'text' => '#6b21a8', 'bar' => '#a855f7', 'border' => '#e9d5ff'],
        'ST'      => ['bg' => '#dcfce7', 'text' => '#166534', 'bar' => '#22c55e', 'border' => '#bbf7d0'],
        'BC'      => ['bg' => '#e0f2fe', 'text' => '#075985', 'bar' => '#0284c7', 'border' => '#bae6fd'],
        'SBC'     => ['bg' => '#ccfbf1', 'text' => '#115e59', 'bar' => '#14b8a6', 'border' => '#99f6e4'],
        'OTHER'   => ['bg' => '#f1f5f9', 'text' => '#334155', 'bar' => '#64748b', 'border' => '#cbd5e1'],
    ];
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE CATEGORY WISE REPORT THEME
   Strictly matched with Admission, Enquiry & Credentials Mobile Views
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
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
}

/* 4 KPI Metrics Glance */
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
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-metric-val {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
}
.mob-metric-sub {
    font-size: 8px;
    color: #cbd5e1;
    font-weight: 600;
}
.mob-metric-box.metric-sky .mob-metric-val { color: #38bdf8; }
.mob-metric-box.metric-blue .mob-metric-val { color: #60a5fa; }
.mob-metric-box.metric-pink .mob-metric-val { color: #f472b6; }
.mob-metric-box.metric-green .mob-metric-val { color: #34d399; }

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 31px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    white-space: nowrap;
}
.mob-act-btn-light {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-primary {
    background: #0284c7;
    color: #ffffff !important;
}

/* 2. Category Distribution Chips Bar */
.mob-cat-chips-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-cat-chips-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-cat-chips-title {
    font-size: 10.5px;
    font-weight: 800;
    color: #002C54;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-cat-chips-total {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
}

/* Progress bar strip */
.mob-progress-strip {
    display: flex;
    height: 7px;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 7px;
    background: #e2e8f0;
}
.mob-progress-segment {
    height: 100%;
    transition: width .3s ease;
}

/* Category Pills Row */
.mob-cat-pills-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.mob-cat-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 6px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    border: 1px solid transparent;
}
.mob-cat-pill b {
    font-weight: 800;
}

/* 3. Search & Filter Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 6px;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 24px 0 26px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-clear-search {
    position: absolute;
    right: 6px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    font-size: 11px;
}
.mob-counter-pill {
    font-size: 10px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 4px 8px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
}

/* 4. Class Breakdown Feed Cards */
.mob-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.mob-class-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: transform .1s ease, border-color .1s ease;
}
.mob-class-card.has-zero {
    opacity: 0.7;
    background: #fafafa;
}

/* Class Card Header */
.mob-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-class-name {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
}
.mob-class-total-badge {
    font-size: 10px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
    padding: 2px 8px;
    border-radius: 3px;
}

/* Class Gender Bar */
.mob-class-gender-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 10px;
    font-weight: 700;
}
.gender-boys-txt { color: #2563eb; }
.gender-girls-txt { color: #db2777; }
.mob-class-gender-bar {
    display: flex;
    height: 5px;
    border-radius: 3px;
    overflow: hidden;
    background: #e2e8f0;
    margin: 3px 0 8px;
}
.gender-bar-boys { background: #3b82f6; }
.gender-bar-girls { background: #ec4899; }

/* Category Matrix Grid */
.mob-cat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
}
.mob-cat-cell {
    border-radius: 3px;
    padding: 4px 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    border: 1px solid transparent;
}
.mob-cat-name {
    font-weight: 800;
    font-size: 9.5px;
}
.mob-cat-split {
    font-size: 9px;
    font-weight: 600;
}
.mob-cat-total-num {
    font-weight: 800;
    font-size: 10.5px;
}

/* Empty State */
.mob-empty-feed {
    text-align: center;
    padding: 36px 16px;
    color: #64748b;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    margin: 8px 0;
}
</style>
@endsection

@section('content')
<div class="container-fluid p-2" style="max-width: 600px; margin: 0 auto;">

    {{-- 1. Hero Summary Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-pie-chart text-warning"></i> Category Wise Report
            </div>
            <div class="mob-session-pill">
                <i class="fa fa-calendar mr-1"></i> {{ $sessionLabel }}
            </div>
        </div>

        {{-- 4 KPI Metrics --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box metric-sky">
                <div class="mob-metric-tag"><i class="fa fa-users"></i> Students</div>
                <div class="mob-metric-val">{{ number_format($totalStudents) }}</div>
                <div class="mob-metric-sub">{{ $classCount }} Classes</div>
            </div>
            <div class="mob-metric-box metric-blue">
                <div class="mob-metric-tag"><i class="fa fa-male"></i> Boys</div>
                <div class="mob-metric-val">{{ number_format($totalBoys) }}</div>
                <div class="mob-metric-sub">{{ $boysPercent }}%</div>
            </div>
            <div class="mob-metric-box metric-pink">
                <div class="mob-metric-tag"><i class="fa fa-female"></i> Girls</div>
                <div class="mob-metric-val">{{ number_format($totalGirls) }}</div>
                <div class="mob-metric-sub">{{ $girlsPercent }}%</div>
            </div>
            <div class="mob-metric-box metric-green">
                <div class="mob-metric-tag"><i class="fa fa-balance-scale"></i> Ratio</div>
                <div class="mob-metric-val">{{ $genderRatio }}</div>
                <div class="mob-metric-sub">/1k Boys</div>
            </div>
        </div>

        {{-- Fast Action Buttons --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-light" onclick="exportCategoryCSV()">
                <i class="fa fa-file-excel-o text-success mr-1"></i> Export CSV
            </button>
            <button type="button" class="mob-act-btn mob-act-btn-light" onclick="window.print()">
                <i class="fa fa-print mr-1"></i> Print
            </button>
            <a href="{{ url('category_wise_report?layout=mobile') }}" class="mob-act-btn mob-act-btn-primary">
                <i class="fa fa-refresh mr-1"></i> Refresh
            </a>
        </div>
    </div>

    {{-- 2. Category Distribution Overview Card --}}
    <div class="mob-cat-chips-card">
        <div class="mob-cat-chips-header">
            <span class="mob-cat-chips-title"><i class="fa fa-bar-chart mr-1 text-primary"></i> Demographic Distribution</span>
            <span class="mob-cat-chips-total">{{ count($categories) }} Categories</span>
        </div>

        {{-- Segmented distribution bar --}}
        <div class="mob-progress-strip">
            @foreach($categories as $cat)
                @php
                    $pct = $categoryTotals[$cat]['percent'] ?? 0;
                    $bColor = $catColors[$cat]['bar'] ?? '#64748b';
                @endphp
                @if($pct > 0)
                    <div class="mob-progress-segment" style="width: {{ $pct }}%; background: {{ $bColor }};" title="{{ $cat }}: {{ $pct }}%"></div>
                @endif
            @endforeach
        </div>

        {{-- Category Pills --}}
        <div class="mob-cat-pills-wrap">
            @foreach($categories as $cat)
                @php
                    $cData = $categoryTotals[$cat] ?? ['total' => 0, 'percent' => 0];
                    $style = $catColors[$cat] ?? ['bg' => '#f1f5f9', 'text' => '#334155', 'border' => '#cbd5e1'];
                @endphp
                <div class="mob-cat-pill" style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; border-color: {{ $style['border'] }};">
                    <span>{{ $cat }}:</span>
                    <b>{{ number_format($cData['total']) }}</b>
                    <small>({{ $cData['percent'] }}%)</small>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. Search & Filter Toolbar --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="mobClassSearch" class="mob-search-input" placeholder="Search class (e.g. 10th, 1st, Nursery)...">
            <button type="button" id="mobClearSearch" class="mob-clear-search" onclick="clearSearch()">&times;</button>
        </div>
        <span class="mob-counter-pill" id="mobClassCount">{{ $classCount }} Classes</span>
    </div>

    {{-- 4. Class Breakdown Feed Cards --}}
    <div class="mob-feed-list" id="classCardsContainer">
        @if(!empty($classType) && count($classType) > 0)
            @foreach($classType as $type)
                @php
                    $cTotals = $classTotals[$type->id] ?? ['boys' => 0, 'girls' => 0, 'total' => 0, 'categories' => []];
                    $cTotal = (int) ($cTotals['total'] ?? 0);
                    $cBoys = (int) ($cTotals['boys'] ?? 0);
                    $cGirls = (int) ($cTotals['girls'] ?? 0);
                    $cBoysPct = $cTotal > 0 ? round(($cBoys / $cTotal) * 100) : 50;
                    $cGirlsPct = $cTotal > 0 ? (100 - $cBoysPct) : 50;
                    $className = $type->name ?? ('Class #' . $type->id);
                @endphp
                <div class="mob-class-card {{ $cTotal === 0 ? 'has-zero' : '' }}" data-class-name="{{ strtolower($className) }}">
                    {{-- Card Header --}}
                    <div class="mob-card-head">
                        <span class="mob-class-name">{{ $className }}</span>
                        <span class="mob-class-total-badge">{{ number_format($cTotal) }} Students</span>
                    </div>

                    {{-- Gender Split Bar --}}
                    <div class="mob-class-gender-row">
                        <span class="gender-boys-txt"><i class="fa fa-male"></i> Boys: {{ $cBoys }} ({{ $cTotal > 0 ? round(($cBoys / $cTotal) * 100, 1) : 0 }}%)</span>
                        <span class="gender-girls-txt"><i class="fa fa-female"></i> Girls: {{ $cGirls }} ({{ $cTotal > 0 ? round(($cGirls / $cTotal) * 100, 1) : 0 }}%)</span>
                    </div>
                    @if($cTotal > 0)
                        <div class="mob-class-gender-bar">
                            <div class="gender-bar-boys" style="width: {{ $cBoysPct }}%;"></div>
                            <div class="gender-bar-girls" style="width: {{ $cGirlsPct }}%;"></div>
                        </div>
                    @endif

                    {{-- Category Matrix Grid --}}
                    <div class="mob-cat-grid">
                        @foreach($categories as $cat)
                            @php
                                $cell = $cTotals['categories'][$cat] ?? ['boys' => 0, 'girls' => 0, 'total' => 0];
                                $cStyle = $catColors[$cat] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0'];
                            @endphp
                            <div class="mob-cat-cell" style="background: {{ $cStyle['bg'] }}; border-color: {{ $cStyle['border'] }}; color: {{ $cStyle['text'] }};">
                                <span class="mob-cat-name">{{ $cat }}</span>
                                <span class="mob-cat-split">B:{{ $cell['boys'] }} G:{{ $cell['girls'] }}</span>
                                <span class="mob-cat-total-num">{{ $cell['total'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="mob-empty-feed">
                <i class="fa fa-graduation-cap fa-2x text-muted mb-2"></i>
                <h6>No Classes Found</h6>
                <p>No class records configured for this session.</p>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
// Live Class Search with Debounce
var searchTimeout = null;
$('#mobClassSearch').on('input', function() {
    var val = $.trim($(this).val()).toLowerCase();
    if (val.length > 0) {
        $('#mobClearSearch').show();
    } else {
        $('#mobClearSearch').hide();
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        filterClasses(val);
    }, 120);
});

function clearSearch() {
    $('#mobClassSearch').val('').trigger('input');
}

function filterClasses(kw) {
    var visible = 0;
    $('.mob-class-card').each(function() {
        var clsName = $(this).attr('data-class-name') || '';
        if (!kw || clsName.indexOf(kw) !== -1) {
            $(this).show();
            visible++;
        } else {
            $(this).hide();
        }
    });
    $('#mobClassCount').text(visible + ' Classes');
}

// Client-side CSV Export
function exportCategoryCSV() {
    var rows = [
        ['Class Name', 'Total Students', 'Total Boys', 'Total Girls', 'Category', 'Cat Boys', 'Cat Girls', 'Cat Total']
    ];

    $('.mob-class-card').each(function() {
        var clsName = $(this).find('.mob-class-name').text().trim();
        var total = $(this).find('.mob-class-total-badge').text().replace(/\D/g, '');
        var boys = $(this).find('.gender-boys-txt').text().replace(/[^0-9]/g, '');
        var girls = $(this).find('.gender-girls-txt').text().replace(/[^0-9]/g, '');

        $(this).find('.mob-cat-cell').each(function() {
            var catName = $(this).find('.mob-cat-name').text().trim();
            var split = $(this).find('.mob-cat-split').text().trim();
            var catTotal = $(this).find('.mob-cat-total-num').text().trim();

            rows.push([
                '"' + clsName + '"',
                total,
                boys,
                girls,
                '"' + catName + '"',
                split,
                '',
                catTotal
            ]);
        });
    });

    var csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + rows.map(function(e) { return e.join(','); }).join('\n');
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'Category_Wise_Report_' + new Date().toISOString().slice(0, 10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
