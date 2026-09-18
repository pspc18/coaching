@php
    $getUser = Helper::getUser();
    $formatMarks = function ($value) {
        $formatted = number_format((float) $value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    };
    $reportPercentages = $examReports->map(function ($report) {
        return (float) ($report->result['percentage'] ?? 0);
    });
    $averagePercentage = $reportPercentages->count() ? round($reportPercentages->avg(), 1) : 0;
    $bestPercentage = $reportPercentages->count() ? round($reportPercentages->max(), 1) : 0;
@endphp

@extends('student_login.layout.app')
@section('title', 'Exam Results')
@section('page_title', 'EXAM RESULTS')
@section('page_sub', Session::get('first_name').' · '.($getUser['ClassTypes']['name'] ?? 'Student'))

@section('content')
<style>
  .stu-results{--sr-primary:#3156d3;--sr-purple:#6954c8;--sr-success:#139466;--sr-warning:#d78a00;min-height:calc(100vh - 126px);padding:14px 14px 106px;background:var(--stu-page,#f3f6fb);color:var(--stu-text,#253858)}
  .sr-hero{position:relative;overflow:hidden;padding:18px;border-radius:20px;background:linear-gradient(135deg,#6954c8,#3156d3 58%,#203d9a);color:#fff;box-shadow:0 13px 30px rgba(49,55,154,.24)}.sr-hero:before,.sr-hero:after{content:"";position:absolute;border-radius:50%;border:26px solid rgba(255,255,255,.06)}.sr-hero:before{width:155px;height:155px;right:-62px;top:-76px}.sr-hero:after{width:95px;height:95px;left:-55px;bottom:-66px}.sr-hero-main{position:relative;z-index:1;display:flex;align-items:center;gap:14px}.sr-ring{--score:0deg;position:relative;width:82px;height:82px;flex:0 0 82px;display:grid;place-items:center;border-radius:50%;background:conic-gradient(#8ff0c8 var(--score),rgba(255,255,255,.17) 0)}.sr-ring:before{content:"";position:absolute;width:65px;height:65px;border-radius:50%;background:#4c4db1}.sr-ring-value{position:relative;z-index:1;text-align:center;font-size:19px;font-weight:800;line-height:1}.sr-ring-value small{display:block;margin-top:5px;font-size:7px;text-transform:uppercase;letter-spacing:.08em;opacity:.75}.sr-hero-copy{min-width:0}.sr-hero-copy span{display:block;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;opacity:.72}.sr-hero-copy h1{font-size:20px;font-weight:750;margin:3px 0}.sr-hero-copy p{font-size:10px;line-height:1.4;margin:0;opacity:.8}.sr-hero-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,1fr);margin-top:15px;padding-top:13px;border-top:1px solid rgba(255,255,255,.15)}.sr-hero-stat{text-align:center;border-right:1px solid rgba(255,255,255,.15)}.sr-hero-stat:last-child{border:0}.sr-hero-stat strong{display:block;font-size:16px}.sr-hero-stat span{font-size:8px;text-transform:uppercase;letter-spacing:.06em;opacity:.72}
  .sr-section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:10px;margin:21px 2px 10px}.sr-section-head h2{font-size:15px;font-weight:750;margin:0}.sr-section-head p{font-size:10px;color:var(--stu-muted,#98a2b3);margin:2px 0 0}.sr-count{flex:0 0 auto;padding:5px 8px;border-radius:9px;background:var(--stu-primary-soft,#e8eeff);color:var(--sr-primary);font-size:8px;font-weight:700}
  .sr-exam-card{position:relative;overflow:hidden;margin-bottom:11px;background:var(--stu-surface,#fff);border:1px solid var(--stu-border,#edf0f6);border-radius:18px;box-shadow:var(--stu-shadow,0 7px 24px rgba(42,55,92,.06))}.sr-exam-card:before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(#6954c8,#3156d3)}.sr-exam-toggle{width:100%;display:block;padding:14px 13px 13px 16px;border:0;background:transparent;color:inherit;text-align:left}.sr-exam-top{display:flex;align-items:flex-start;gap:11px}.sr-exam-icon{width:42px;height:42px;flex:0 0 42px;display:grid;place-items:center;border-radius:13px;background:#eeeafd;color:var(--sr-purple);font-size:18px}.sr-exam-info{min-width:0;flex:1}.sr-exam-info h3{display:-webkit-box;overflow:hidden;-webkit-box-orient:vertical;-webkit-line-clamp:2;margin:0 0 5px;font-size:14px;font-weight:730;line-height:1.35}.sr-exam-meta{display:flex;align-items:center;gap:9px;flex-wrap:wrap;color:var(--stu-muted,#98a2b3);font-size:8px}.sr-exam-meta span{display:flex;align-items:center;gap:4px}.sr-percentage{flex:0 0 auto;min-width:52px;padding:7px 6px;border-radius:12px;background:#e4f8ef;color:var(--sr-success);font-size:14px;font-weight:800;text-align:center}.sr-percentage small{display:block;font-size:7px;font-weight:650;text-transform:uppercase;letter-spacing:.04em}.sr-exam-overview{display:grid;grid-template-columns:1.45fr 1fr 26px;align-items:center;gap:8px;margin-top:12px;padding-top:11px;border-top:1px solid var(--stu-border,#edf0f6)}.sr-score-label small,.sr-rank-label small{display:block;margin-bottom:3px;color:var(--stu-muted,#98a2b3);font-size:7px;font-weight:650;text-transform:uppercase;letter-spacing:.06em}.sr-score-label strong,.sr-rank-label strong{font-size:12px}.sr-score-track{height:5px;margin-top:7px;overflow:hidden;border-radius:5px;background:var(--stu-primary-soft,#e8eeff)}.sr-score-track i{display:block;height:100%;border-radius:5px;background:linear-gradient(90deg,#6954c8,#3156d3)}.sr-chevron{width:26px;height:26px;display:grid;place-items:center;border-radius:9px;background:var(--stu-primary-soft,#e8eeff);color:var(--sr-primary);font-size:11px;transition:transform .2s}.sr-exam-card.is-open .sr-chevron{transform:rotate(180deg)}
  .sr-exam-body{display:none;padding:0 13px 14px 16px}.sr-exam-card.is-open .sr-exam-body{display:block;animation:srOpen .2s ease}@keyframes srOpen{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:none}}.sr-summary-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;padding-top:13px;border-top:1px solid var(--stu-border,#edf0f6)}.sr-summary-item{min-width:0;padding:10px;border-radius:13px;background:var(--stu-page,#f3f6fb)}.sr-summary-item span{display:block;margin-bottom:4px;color:var(--stu-muted,#98a2b3);font-size:8px;text-transform:uppercase;letter-spacing:.04em}.sr-summary-item strong{display:block;overflow:hidden;text-overflow:ellipsis;font-size:13px;white-space:nowrap}.sr-subject-heading{margin:16px 1px 8px}.sr-subject-heading h4{font-size:13px;font-weight:720;margin:0}.sr-subject-heading p{font-size:9px;color:var(--stu-muted,#98a2b3);margin:2px 0 0}.sr-subject-list{display:flex;flex-direction:column;gap:7px}.sr-subject{padding:11px;border:1px solid var(--stu-border,#edf0f6);border-radius:14px;background:var(--stu-surface,#fff)}.sr-subject-head{display:flex;align-items:center;gap:9px}.sr-subject-number{width:29px;height:29px;flex:0 0 29px;display:grid;place-items:center;border-radius:9px;background:var(--stu-primary-soft,#e8eeff);color:var(--sr-primary);font-size:9px;font-weight:750}.sr-subject-name{min-width:0;flex:1}.sr-subject-name strong{display:block;overflow:hidden;text-overflow:ellipsis;font-size:11px;white-space:nowrap}.sr-subject-name small{font-size:8px;color:var(--stu-muted,#98a2b3)}.sr-subject-score{text-align:right}.sr-subject-score strong{display:block;color:var(--sr-success);font-size:12px}.sr-subject-score span{font-size:8px;color:var(--stu-muted,#98a2b3)}.sr-subject-progress{height:4px;margin:8px 0 0;border-radius:4px;background:var(--stu-primary-soft,#e8eeff);overflow:hidden}.sr-subject-progress i{display:block;height:100%;border-radius:4px;background:linear-gradient(90deg,#6954c8,#3156d3)}.sr-components{display:flex;gap:5px;flex-wrap:wrap;margin-top:8px}.sr-component{padding:4px 7px;border-radius:7px;background:var(--stu-page,#f3f6fb);font-size:8px;color:var(--stu-muted,#98a2b3)}.sr-component b{color:var(--stu-text,#253858)}
  .sr-empty{padding:42px 20px;text-align:center;background:var(--stu-surface,#fff);border:1px solid var(--stu-border,#edf0f6);border-radius:19px}.sr-empty-icon{width:62px;height:62px;display:grid;place-items:center;margin:0 auto 13px;border-radius:19px;background:#eeeafd;color:var(--sr-purple);font-size:27px}.sr-empty h3{font-size:15px;margin:0 0 5px}.sr-empty p{max-width:290px;margin:0 auto;font-size:10px;line-height:1.5;color:var(--stu-muted,#98a2b3)}
  [data-theme=dark] .stu-results{--stu-page:#0f1728;--stu-surface:#182337;--stu-text:#f2f5fb;--stu-muted:#9da9bd;--stu-border:#29364c;--stu-primary-soft:#24365f}[data-theme=dark] .sr-exam-icon,[data-theme=dark] .sr-empty-icon{background:#302d59;color:#bcb0ff}[data-theme=dark] .sr-percentage{background:#193c34;color:#76dcb4}
</style>

<section class="stu-results">
  <div class="sr-hero">
    <div class="sr-hero-main">
      <div class="sr-ring" style="--score:{{ min(100, $averagePercentage) * 3.6 }}deg"><div class="sr-ring-value">{{ $formatMarks($averagePercentage) }}%<small>Average</small></div></div>
      <div class="sr-hero-copy"><span>Academic performance</span><h1>My results</h1><p>{{ $examReports->count() ? 'Review your published exam performance and subject-wise marks.' : 'Published exam results will appear here.' }}</p></div>
    </div>
    <div class="sr-hero-stats"><div class="sr-hero-stat"><strong>{{ $examReports->count() }}</strong><span>Published</span></div><div class="sr-hero-stat"><strong>{{ $formatMarks($bestPercentage) }}%</strong><span>Best score</span></div><div class="sr-hero-stat"><strong>{{ $getUser['ClassTypes']['name'] ?? '—' }}</strong><span>Class</span></div></div>
  </div>

  <div class="sr-section-head"><div><h2>Published results</h2><p>Tap an exam to see subject details</p></div><span class="sr-count">{{ $examReports->count() }} {{ \Illuminate\Support\Str::plural('EXAM', $examReports->count()) }}</span></div>

  <div class="sr-exam-list">
    @forelse($examReports as $report)
      @php
        $result=$report->result;
        $rank=$report->single_subject_mode?($result['subject_rank']??null):($result['overall_rank']??null);
        $examDate=!empty($report->assigned_exam_date)?\Carbon\Carbon::parse($report->assigned_exam_date)->format('d M Y'):null;
        $obtained=(float)($result['total_obtained']??0); $maximum=(float)($result['total_maximum']??0);
        $percentage=(float)($result['percentage']??($maximum>0?($obtained/$maximum)*100:0)); $subjects=$result['subject_rows']??[];
      @endphp
      <article class="sr-exam-card">
        <button type="button" class="sr-exam-toggle" aria-expanded="false" aria-controls="exam-result-{{ $report->exam->id ?? $loop->index }}">
          <span class="sr-exam-top"><span class="sr-exam-icon"><i class="bi bi-file-earmark-bar-graph"></i></span><span class="sr-exam-info"><h3>{{ $report->exam->name ?? 'Exam' }}</h3><span class="sr-exam-meta"><span><i class="bi bi-calendar3"></i>{{ $examDate ?? 'Date not set' }}</span><span><i class="bi bi-journals"></i>{{ count($subjects) }} {{ \Illuminate\Support\Str::plural('subject', count($subjects)) }}</span></span></span><span class="sr-percentage">{{ $formatMarks($percentage) }}%<small>Score</small></span></span>
          <span class="sr-exam-overview"><span class="sr-score-label"><small>Marks obtained</small><strong>{{ $formatMarks($obtained) }} / {{ $formatMarks($maximum) }}</strong><span class="sr-score-track"><i style="width:{{ min(100,max(0,$percentage)) }}%"></i></span></span><span class="sr-rank-label"><small>Class rank</small><strong>#{{ $rank ?: '—' }}</strong></span><span class="sr-chevron"><i class="bi bi-chevron-down"></i></span></span>
        </button>
        <div class="sr-exam-body" id="exam-result-{{ $report->exam->id ?? $loop->index }}">
          <div class="sr-summary-grid"><div class="sr-summary-item"><span>Total marks</span><strong>{{ $formatMarks($obtained) }}</strong></div><div class="sr-summary-item"><span>Maximum</span><strong>{{ $formatMarks($maximum) }}</strong></div><div class="sr-summary-item"><span>Class rank</span><strong>{{ $rank ?: '—' }} / {{ $report->summary['total_students'] ?? 0 }}</strong></div><div class="sr-summary-item"><span>Top score</span><strong>{{ $formatMarks($report->summary['topper_score'] ?? 0) }}</strong></div></div>
          <div class="sr-subject-heading"><h4>Subject-wise performance</h4><p>Marks and assessment components</p></div>
          <div class="sr-subject-list">
            @foreach($subjects as $subject)
              @php
                $subjectMaximum=(float)($subject['maximum_marks']??0); $numericMarks=(float)($subject['numeric_marks']??0);
                $subjectPercentage=$subjectMaximum>0&&is_numeric($subject['display_marks']??null)?round(($numericMarks/$subjectMaximum)*100,1):null;
              @endphp
              <div class="sr-subject"><div class="sr-subject-head"><span class="sr-subject-number">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><span class="sr-subject-name"><strong>{{ $subject['name'] }}</strong><small>{{ $subjectPercentage!==null?$formatMarks($subjectPercentage).'% performance':'Marks not numeric' }}</small></span><span class="sr-subject-score"><strong>{{ $subject['display_marks'] }}</strong><span>out of {{ $formatMarks($subjectMaximum) }}</span></span></div>@if($subjectPercentage!==null)<div class="sr-subject-progress"><i style="width:{{ min(100,max(0,$subjectPercentage)) }}%"></i></div>@endif<div class="sr-components">@foreach(['R'=>$subject['r_marks']??null,'W'=>$subject['w_marks']??null,'L'=>$subject['l_marks']??null] as $label=>$value)@if($value!==null&&$value!=='')<span class="sr-component">{{ $label }}: <b>{{ $value }}</b></span>@endif @endforeach</div></div>
            @endforeach
          </div>
        </div>
      </article>
    @empty
      <div class="sr-empty"><div class="sr-empty-icon"><i class="bi bi-bar-chart-line"></i></div><h3>Results not published yet</h3><p>Your exam-wise results will appear here after marks are entered and officially published by the school.</p></div>
    @endforelse
  </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.sr-exam-toggle').forEach(function(button){button.addEventListener('click',function(){var card=button.closest('.sr-exam-card'),open=!card.classList.contains('is-open');card.classList.toggle('is-open',open);button.setAttribute('aria-expanded',open?'true':'false')})})});
</script>
@endsection
