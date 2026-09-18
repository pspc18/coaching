@php
    $getUser = Helper::getUser();
    $totalNotices = (int) ($noticeCounts->total_count ?? 0);
    $activeNotices = (int) ($noticeCounts->active_count ?? 0);
    $upcomingNotices = (int) ($noticeCounts->upcoming_count ?? 0);
@endphp
@extends('student_login.layout.app')
@section('title', 'Notices')
@section('page_title', 'NOTICES')
@section('page_sub', Session::get('first_name').' · '.($getUser['ClassTypes']['name'] ?? 'Student'))

@section('content')
<style>
  .stu-notice-page{--sn-primary:#3156d3;--sn-success:#139466;--sn-warning:#d78a00;min-height:calc(100vh - 126px);padding:14px 14px 106px;background:var(--stu-page,#f3f6fb);color:var(--stu-text,#253858)}
  .sn-hero{position:relative;overflow:hidden;padding:18px;border-radius:20px;background:linear-gradient(135deg,#355dde,#203d9a);color:#fff;box-shadow:0 12px 30px rgba(32,61,154,.2)}.sn-hero:before,.sn-hero:after{content:"";position:absolute;border-radius:50%;border:24px solid rgba(255,255,255,.06)}.sn-hero:before{width:145px;height:145px;right:-55px;top:-65px}.sn-hero:after{width:90px;height:90px;left:-48px;bottom:-60px}
  .sn-hero-top{position:relative;z-index:1;display:flex;align-items:center;gap:13px}.sn-hero-icon{width:50px;height:50px;flex:0 0 50px;display:grid;place-items:center;border-radius:15px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.18);font-size:23px}.sn-hero-copy{min-width:0}.sn-hero-copy span{display:block;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;opacity:.72}.sn-hero-copy h1{font-size:20px;font-weight:750;margin:2px 0}.sn-hero-copy p{font-size:10px;margin:0;opacity:.8}.sn-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,1fr);margin-top:16px;padding-top:13px;border-top:1px solid rgba(255,255,255,.15)}.sn-stat{text-align:center;border-right:1px solid rgba(255,255,255,.15)}.sn-stat:last-child{border:0}.sn-stat strong{display:block;font-size:16px}.sn-stat span{font-size:8px;text-transform:uppercase;letter-spacing:.07em;opacity:.7}
  .sn-filter-wrap{position:sticky;z-index:5;top:74px;margin:12px -14px 0;padding:6px 14px 8px;background:linear-gradient(var(--stu-page,#f3f6fb) 75%,transparent)}.sn-filters{display:flex;gap:7px;overflow-x:auto;scrollbar-width:none}.sn-filters::-webkit-scrollbar{display:none}.sn-filter{flex:0 0 auto;min-height:38px;display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border:1px solid var(--stu-border,#edf0f6);border-radius:12px;background:var(--stu-surface,#fff);color:var(--stu-muted,#98a2b3);font-size:10px;font-weight:650;text-decoration:none;box-shadow:0 4px 14px rgba(42,55,92,.04)}.sn-filter b{display:grid;place-items:center;min-width:18px;height:18px;padding:0 5px;border-radius:8px;background:var(--stu-primary-soft,#e8eeff);color:var(--sn-primary);font-size:8px}.sn-filter.active{border-color:transparent;background:linear-gradient(135deg,#3156d3,#2646b5);color:#fff}.sn-filter.active b{background:rgba(255,255,255,.18);color:#fff}
  .sn-section-head{margin:11px 2px 9px}.sn-section-head h2{font-size:15px;font-weight:750;margin:0}.sn-section-head p{font-size:10px;color:var(--stu-muted,#98a2b3);margin:2px 0 0}.sn-card{position:relative;overflow:hidden;margin-bottom:10px;background:var(--stu-surface,#fff);border:1px solid var(--stu-border,#edf0f6);border-radius:17px;box-shadow:var(--stu-shadow,0 7px 24px rgba(42,55,92,.06))}.sn-card:before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--sn-primary)}.sn-card.upcoming:before{background:var(--sn-warning)}.sn-card.expired:before{background:#8792a5}.sn-card-head{width:100%;display:flex;align-items:flex-start;gap:10px;padding:13px 12px 13px 15px;border:0;background:transparent;color:inherit;text-align:left}.sn-icon{width:38px;height:38px;flex:0 0 38px;display:grid;place-items:center;border-radius:12px;background:var(--stu-primary-soft,#e8eeff);color:var(--sn-primary);font-size:16px}.sn-card.upcoming .sn-icon{background:#fff4dc;color:var(--sn-warning)}.sn-card.expired .sn-icon{background:#edf1f5;color:#718096}.sn-title-wrap{min-width:0;flex:1}.sn-title-wrap h3{display:-webkit-box;overflow:hidden;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin:0 0 5px;font-size:13px;font-weight:720;line-height:1.35}.sn-date{display:flex;align-items:center;gap:5px;font-size:9px;color:var(--stu-muted,#98a2b3)}.sn-head-side{flex:0 0 auto;display:flex;align-items:center;gap:6px}.sn-status{padding:5px 7px;border-radius:9px;background:#e4f8ef;color:var(--sn-success);font-size:8px;font-weight:700;text-transform:capitalize}.sn-status.upcoming{background:#fff4dc;color:var(--sn-warning)}.sn-status.expired{background:#edf1f5;color:#718096}.sn-chevron{font-size:13px;color:var(--stu-muted,#98a2b3);transition:transform .2s}.sn-card.is-open .sn-chevron{transform:rotate(180deg)}
  .sn-card-body{display:none;padding:0 14px 14px 63px}.sn-card.is-open .sn-card-body{display:block;animation:snOpen .2s ease}@keyframes snOpen{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:none}}.sn-message{font-size:11px;line-height:1.55;color:var(--stu-muted,#98a2b3);white-space:pre-line;word-break:break-word}.sn-message.is-truncated{display:-webkit-box;overflow:hidden;-webkit-line-clamp:4;-webkit-box-orient:vertical}.sn-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:11px}.sn-text-btn,.sn-attachment{min-height:36px;display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border:0;border-radius:10px;background:var(--stu-primary-soft,#e8eeff);color:var(--sn-primary);font-size:9px;font-weight:700;text-decoration:none}.sn-attachment{background:#ffeceb;color:#d14d4d}.sn-meta{display:flex;flex-direction:column;gap:5px;margin-top:11px;padding-top:10px;border-top:1px solid var(--stu-border,#edf0f6);font-size:8px;color:var(--stu-muted,#98a2b3)}.sn-meta span{display:flex;align-items:center;gap:6px}
  .sn-empty{padding:38px 18px;text-align:center;background:var(--stu-surface,#fff);border:1px solid var(--stu-border,#edf0f6);border-radius:18px}.sn-empty-icon{width:58px;height:58px;display:grid;place-items:center;margin:0 auto 11px;border-radius:18px;background:var(--stu-primary-soft,#e8eeff);color:var(--sn-primary);font-size:25px}.sn-empty h3{font-size:14px;margin:0 0 4px}.sn-empty p{font-size:10px;color:var(--stu-muted,#98a2b3);margin:0}.sn-pagination{margin-top:15px}.sn-pagination .pagination{justify-content:center;gap:4px;flex-wrap:wrap}.sn-pagination .page-link{min-width:34px;height:34px;display:grid;place-items:center;padding:0;border-radius:10px!important;border-color:var(--stu-border,#edf0f6);font-size:10px;background:var(--stu-surface,#fff);color:var(--sn-primary)}.sn-pagination .page-item.active .page-link{border-color:var(--sn-primary);background:var(--sn-primary);color:#fff}
  [data-theme=dark] .stu-notice-page{--stu-page:#0f1728;--stu-surface:#182337;--stu-text:#f2f5fb;--stu-muted:#9da9bd;--stu-border:#29364c;--stu-primary-soft:#24365f}[data-theme=dark] .sn-card.expired .sn-icon,[data-theme=dark] .sn-status.expired{background:#263248;color:#aab5c8}
</style>

<section class="stu-notice-page">
  <div class="sn-hero">
    <div class="sn-hero-top"><div class="sn-hero-icon"><i class="bi bi-megaphone-fill"></i></div><div class="sn-hero-copy"><span>School communication</span><h1>Notice board</h1><p>{{ $activeNotices ? $activeNotices.' active '.Illuminate\Support\Str::plural('notice', $activeNotices).' available for you.' : 'You are up to date with school notices.' }}</p></div></div>
    <div class="sn-stats"><div class="sn-stat"><strong>{{ $totalNotices }}</strong><span>Total</span></div><div class="sn-stat"><strong>{{ $activeNotices }}</strong><span>Active</span></div><div class="sn-stat"><strong>{{ $upcomingNotices }}</strong><span>Upcoming</span></div></div>
  </div>

  <nav class="sn-filter-wrap" aria-label="Filter school notices"><div class="sn-filters">
    @foreach(['all'=>['All','bi-grid', $totalNotices], 'active'=>['Active','bi-lightning-charge', $activeNotices], 'upcoming'=>['Upcoming','bi-calendar2-event', $upcomingNotices], 'expired'=>['Past','bi-archive', (int) ($noticeCounts->expired_count ?? 0)]] as $key=>$item)
      <a href="{{ url('student-notices?filter='.$key) }}" class="sn-filter {{ $filter===$key?'active':'' }}" @if($filter===$key) aria-current="page" @endif><i class="bi {{ $item[1] }}"></i>{{ $item[0] }}<b>{{ $item[2] }}</b></a>
    @endforeach
  </div></nav>

  <div class="sn-section-head"><h2>{{ ['all'=>'All notices','active'=>'Active notices','upcoming'=>'Upcoming notices','expired'=>'Past notices'][$filter] }}</h2><p>{{ $notices->total() }} {{ Illuminate\Support\Str::plural('update', $notices->total()) }} found</p></div>

  <div class="sn-list">
    @forelse($notices as $notice)
      @php
        $fromDate=\Carbon\Carbon::parse($notice->from_date); $toDate=\Carbon\Carbon::parse($notice->to_date);
        $status=$fromDate->toDateString()>$today?'upcoming':($toDate->toDateString()<$today?'expired':'active');
        $message=trim(preg_replace('/\s+/', ' ', strip_tags((string) $notice->message))); $isLong=mb_strlen($message)>220;
      @endphp
      <article class="sn-card {{ $status }}" id="notice-{{ $notice->id }}">
        <button type="button" class="sn-card-head" aria-expanded="false" aria-controls="notice-body-{{ $notice->id }}">
          <span class="sn-icon"><i class="bi {{ $status==='upcoming'?'bi-calendar2-event':($status==='expired'?'bi-archive':'bi-megaphone') }}"></i></span>
          <span class="sn-title-wrap"><h3>{{ $notice->title }}</h3><span class="sn-date"><i class="bi bi-calendar3"></i>{{ $fromDate->format('d M') }} – {{ $toDate->format('d M Y') }}</span></span>
          <span class="sn-head-side"><span class="sn-status {{ $status }}">{{ $status==='expired'?'Past':ucfirst($status) }}</span><i class="bi bi-chevron-down sn-chevron"></i></span>
        </button>
        <div class="sn-card-body" id="notice-body-{{ $notice->id }}">
          <div class="sn-message {{ $isLong?'is-truncated':'' }}">{{ $message ?: 'No notice details are available.' }}</div>
          <div class="sn-actions">
            @if($isLong)<button type="button" class="sn-text-btn" data-expand-message><i class="bi bi-arrows-expand"></i><span>Read more</span></button>@endif
            @if($notice->attachment_path)<a href="{{ url('notice-management/'.$notice->id.'/attachment') }}" class="sn-attachment" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf"></i>{{ $notice->attachment_name ?: 'Open attachment' }}</a>@endif
          </div>
          <div class="sn-meta"><span><i class="bi bi-person"></i>{{ trim(optional($notice->creator)->first_name.' '.optional($notice->creator)->last_name) ?: 'School administration' }}</span>@if($notice->published_at)<span><i class="bi bi-clock"></i>Published {{ $notice->published_at->format('d M Y, h:i A') }}</span>@endif</div>
        </div>
      </article>
    @empty
      <div class="sn-empty"><div class="sn-empty-icon"><i class="bi bi-bell-slash"></i></div><h3>No notices here</h3><p>New school updates will appear in this section.</p></div>
    @endforelse
  </div>
  @if($notices->hasPages())<div class="sn-pagination">{{ $notices->links() }}</div>@endif
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.sn-card-head').forEach(function(button){button.addEventListener('click',function(){var card=button.closest('.sn-card'),open=!card.classList.contains('is-open');card.classList.toggle('is-open',open);button.setAttribute('aria-expanded',open?'true':'false')})});
  document.querySelectorAll('[data-expand-message]').forEach(function(button){button.addEventListener('click',function(){var message=button.closest('.sn-card-body').querySelector('.sn-message'),expanded=!message.classList.contains('is-truncated');message.classList.toggle('is-truncated',expanded);button.querySelector('i').className=expanded?'bi bi-arrows-expand':'bi bi-arrows-collapse';button.querySelector('span').textContent=expanded?'Read more':'Show less'})});
});
</script>
@endsection
