@php $getUser = Helper::getUser(); @endphp

@extends('student_login.layout.app')

@section('title', 'Attendance')
@section('page_title', 'ATTENDANCE')
@section('page_sub', Session::get('first_name') . ' · ' . ($getUser['ClassTypes']['name'] ?? 'Student'))

@section('content')
<style>
  .stu-attendance{--ap:#3156d3;--as:#139466;--ad:#d14d4d;--aw:#d78a00;min-height:calc(100vh - 126px);padding:14px 14px 104px;background:var(--stu-page,#f3f6fb);color:var(--stu-text,#253858)}
  .attendance-hero{position:relative;overflow:hidden;padding:18px;border-radius:20px;background:linear-gradient(135deg,#355dde,#203d9a);color:#fff;box-shadow:0 12px 30px rgba(32,61,154,.22)}
  .attendance-hero:after{content:"";position:absolute;width:135px;height:135px;border:25px solid rgba(255,255,255,.07);border-radius:50%;right:-45px;top:-54px}
  .hero-main{position:relative;z-index:1;display:flex;align-items:center;gap:16px}.progress-ring{--progress:0deg;width:88px;height:88px;flex:0 0 88px;display:grid;place-items:center;border-radius:50%;background:conic-gradient(#8ff0c8 var(--progress),rgba(255,255,255,.18) 0)}
  .progress-ring:before{content:"";position:absolute;width:70px;height:70px;border-radius:50%;background:#294ab0}.progress-value{position:relative;z-index:1;text-align:center;font-size:20px;font-weight:800;line-height:1}.progress-value small{display:block;margin-top:5px;font-size:8px;letter-spacing:.08em;text-transform:uppercase;opacity:.75}
  .hero-copy{min-width:0}.hero-copy small{font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;opacity:.75}.hero-copy h2{font-size:19px;margin:3px 0 4px;font-weight:750}.hero-copy p{margin:0;font-size:11px;opacity:.8}.hero-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,1fr);margin-top:15px;padding-top:13px;border-top:1px solid rgba(255,255,255,.15)}.hero-stat{text-align:center;border-right:1px solid rgba(255,255,255,.15)}.hero-stat:last-child{border:0}.hero-stat strong{display:block;font-size:16px}.hero-stat span{font-size:8px;text-transform:uppercase;letter-spacing:.06em;opacity:.7}
  .attendance-card{margin-top:12px;padding:1px;background:var(--stu-surface,#fff);border:1px solid var(--stu-border,#edf0f6);border-radius:18px;box-shadow:var(--stu-shadow,0 7px 24px rgba(42,55,92,.06))}.month-nav{display:flex;align-items:center;justify-content:space-between;margin-bottom:13px}.month-nav button{width:44px;height:44px;border:0;border-radius:13px;background:var(--stu-primary-soft,#e8eeff);color:var(--ap);font-size:18px}.month-nav button:disabled{opacity:.35}.month-title{text-align:center}.month-title strong{display:block;font-size:14px}.month-title span{font-size:9px;color:var(--stu-muted,#98a2b3)}
  .weekdays,.calendar-days{display:grid;grid-template-columns:repeat(7,1fr);gap:5px}.weekdays span{text-align:center;font-size:8px;font-weight:700;color:var(--stu-muted,#98a2b3);text-transform:uppercase}.calendar-days{margin-top:7px}.calendar-day{position:relative;aspect-ratio:1;display:grid;place-items:center;border-radius:10px;font-size:11px;font-weight:650;background:rgba(152,162,179,.07)}.calendar-day.empty{background:transparent}.calendar-day.today{outline:2px solid var(--ap);outline-offset:1px}.calendar-day.future{opacity:.42}.calendar-day[data-status]{color:#fff}.calendar-day[data-status=present],.calendar-day[data-status=late],.calendar-day[data-status=early_out]{background:#139466}.calendar-day[data-status=absent]{background:#d14d4d}.calendar-day[data-status=halfday],.calendar-day[data-status=leave]{background:#d78a00}.calendar-day[data-status=holiday]{background:#7d8ba1}.calendar-day[data-status=event],.calendar-day[data-status=exam]{background:#6954c8}.calendar-day[data-status=late]:after,.calendar-day[data-status=early_out]:after,.calendar-day[data-status=halfday]:after{content:"";position:absolute;right:4px;top:4px;width:4px;height:4px;background:#fff;border-radius:50%}
  .attendance-key{width:100%;height:auto;display:flex;align-items:center;gap:9px 13px;flex-wrap:wrap;margin:13px 0 0;padding:12px 10px 1px;border-top:1px solid var(--stu-border,#edf0f6)}.attendance-key span{width:auto;height:auto;display:inline-flex;align-items:center;gap:5px;margin:0;font-size:9px;line-height:1.3;color:var(--stu-muted,#98a2b3);white-space:nowrap}.attendance-key i{display:block;flex:0 0 8px;width:8px;height:8px;border-radius:3px}.summary-title{position:relative;clear:both;margin:21px 2px 9px}.summary-title h3{font-size:15px;line-height:1.3;font-weight:750;margin:0}.summary-title p{font-size:10px;line-height:1.4;color:var(--stu-muted,#98a2b3);margin:3px 0 0}.summary-grid{clear:both;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.summary-item{min-width:0;padding:11px 8px;border-radius:14px;background:var(--stu-surface,#fff);border:1px solid var(--stu-border,#edf0f6);text-align:center}.summary-item i{display:block;width:8px;height:8px;margin:0 auto 6px;border-radius:3px}.summary-item strong{display:block;font-size:15px}.summary-item span{display:block;overflow:hidden;text-overflow:ellipsis;font-size:8px;text-transform:uppercase;color:var(--stu-muted,#98a2b3);letter-spacing:.05em;white-space:nowrap}
  .recent-list{padding:3px 14px}.recent-row{display:flex;align-items:center;gap:11px;padding:11px 0;border-bottom:1px solid var(--stu-border,#edf0f6)}.recent-row:last-child{border:0}.date-box{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;background:var(--stu-primary-soft,#e8eeff);color:var(--ap);font-size:11px;font-weight:750}.recent-info{flex:1}.recent-info strong{display:block;font-size:12px}.recent-info small{font-size:9px;color:var(--stu-muted,#98a2b3)}.status-pill{padding:5px 8px;border-radius:99px;font-size:8px;font-weight:700;background:#edf1f5;color:#5a6878}.status-pill.present,.status-pill.late,.status-pill.early_out{background:#e4f8ef;color:#139466}.status-pill.absent{background:#ffeceb;color:#d14d4d}.status-pill.leave,.status-pill.halfday{background:#fff4dc;color:#b87300}.empty-state{text-align:center;padding:25px 10px;color:var(--stu-muted,#98a2b3);font-size:11px}.empty-state i{display:block;font-size:25px;margin-bottom:6px}.loading{animation:pulse 1s ease-in-out infinite}@keyframes pulse{50%{opacity:.45}}
  [data-theme=dark] .stu-attendance{--stu-page:#0f1728;--stu-surface:#182337;--stu-text:#f2f5fb;--stu-muted:#9da9bd;--stu-border:#29364c;--stu-primary-soft:#24365f}.error-note{margin-top:10px;padding:10px;border-radius:11px;background:#ffeceb;color:#d14d4d;font-size:10px;display:none}
</style>

<section class="stu-attendance">
  <div class="attendance-hero">
    <div class="hero-main">
      <div class="progress-ring" id="progressRing"><div class="progress-value"><span id="attendancePercent">0%</span><small>Attendance</small></div></div>
      <div class="hero-copy"><small>Monthly overview</small><h2 id="heroMonth">Loading…</h2><p>Only scheduled attendance days are counted.</p></div>
    </div>
    <div class="hero-stats"><div class="hero-stat"><strong id="heroPresent">0</strong><span>Attended</span></div><div class="hero-stat"><strong id="heroAbsent">0</strong><span>Absent</span></div><div class="hero-stat"><strong id="heroWorking">0</strong><span>Marked days</span></div></div>
  </div>

  <div class="attendance-card">
    <div class="month-nav" style="padding: 14px;"><button id="prevMonth" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button><div class="month-title"><strong id="monthYear">—</strong><span>Attendance calendar</span></div><button id="nextMonth" aria-label="Next month"><i class="bi bi-chevron-right"></i></button></div>
    <div class="weekdays"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
    <div class="calendar-days loading" id="attendanceDays"></div>
    <div class="attendance-key" aria-label="Attendance status guide"><span><i style="background:#139466"></i>Present</span><span><i style="background:#d14d4d"></i>Absent</span><span><i style="background:#d78a00"></i>Leave / half day</span><span><i style="background:#7d8ba1"></i>Holiday</span><span><i style="background:#6954c8"></i>Event / exam</span></div>
    <div class="error-note" id="errorNote">Attendance could not be loaded. Please try again.</div>
  </div>

  <div class="summary-title"><h3>Monthly breakdown</h3><p>Synced with the new attendance management module</p></div>
  <div class="summary-grid" id="summaryGrid"></div>
  <div class="summary-title"><h3>Recent activity</h3><p>Latest marked days in this month</p></div>
  <div class="attendance-card recent-list" id="recentList"><div class="empty-state loading"><i class="bi bi-hourglass-split"></i>Loading attendance…</div></div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const endpoint = @json(url('getAttendanceDatesStudent'));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const now = new Date(); let selected = new Date(now.getFullYear(), now.getMonth(), 1); let requestNo = 0;
  const $ = id => document.getElementById(id);
  const labels = {'Present':'#139466','Late':'#139466','Early Out':'#139466','Half Day':'#d78a00','Absent':'#d14d4d','Leave':'#d78a00','Holiday':'#7d8ba1','Event':'#6954c8','Exam':'#6954c8'};
  const key = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  const sameDay = (a,b) => a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();

  function renderCalendar(data) {
    const year=selected.getFullYear(), month=selected.getMonth(), first=new Date(year,month,1), days=new Date(year,month+1,0).getDate();
    $('monthYear').textContent=selected.toLocaleDateString('en-IN',{month:'long',year:'numeric'}); $('attendanceDays').classList.remove('loading'); $('attendanceDays').innerHTML='';
    for(let i=0;i<first.getDay();i++) $('attendanceDays').insertAdjacentHTML('beforeend','<div class="calendar-day empty"></div>');
    for(let day=1;day<=days;day++){const date=new Date(year,month,day), status=data[key(date)]||''; const el=document.createElement('div'); el.className='calendar-day'+(sameDay(date,now)?' today':'')+(date>now?' future':''); if(status){el.dataset.status=status;el.title=status.replace('_',' ')} el.textContent=day;$('attendanceDays').appendChild(el)}
    $('nextMonth').disabled = year===now.getFullYear() && month===now.getMonth();
  }
  function renderSummary(total) {$('summaryGrid').innerHTML=Object.entries(labels).map(([label,color])=>`<div class="summary-item"><i style="background:${color}"></i><strong>${Number(total[label]||0)}</strong><span>${label}</span></div>`).join('')}
  function renderRecent(rows) {const visible=(rows||[]).filter(r=>!['holiday','event','exam'].includes(r.status)).slice(0,6); $('recentList').innerHTML=visible.length?visible.map(r=>{const d=new Date(r.date+'T00:00:00');const time=[r.in_time&&`In ${r.in_time}`,r.out_time&&`Out ${r.out_time}`].filter(Boolean).join(' · ')||r.day;return `<div class="recent-row"><div class="date-box">${String(d.getDate()).padStart(2,'0')}<br>${d.toLocaleDateString('en-IN',{month:'short'})}</div><div class="recent-info"><strong>${r.label}</strong><small>${time}</small></div><span class="status-pill ${r.status}">${r.label}</span></div>`}).join(''):'<div class="empty-state"><i class="bi bi-calendar2-check"></i>No marked attendance found for this month.</div>'}
  async function load(){const current=++requestNo;$('attendanceDays').classList.add('loading');$('errorNote').style.display='none';try{const response=await fetch(endpoint,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({month:selected.getMonth()+1,year:selected.getFullYear()})});if(!response.ok)throw new Error();const res=await response.json();if(current!==requestNo)return;const pct=Number(res.metrics?.percentage||0), credit=Number(res.metrics?.attendance_credit||0);$('heroMonth').textContent=res.month;$('attendancePercent').textContent=`${pct}%`;$('progressRing').style.setProperty('--progress',`${Math.min(100,pct)*3.6}deg`);$('heroPresent').textContent=credit%1?credit.toFixed(1):credit;$('heroAbsent').textContent=Number(res.total?.Absent||0);$('heroWorking').textContent=Number(res.metrics?.scheduled_days||0);renderCalendar(res.data||{});renderSummary(res.total||{});renderRecent(res.details||[])}catch(e){if(current!==requestNo)return;renderCalendar({});renderSummary({});renderRecent([]);$('errorNote').style.display='block'}}
  $('prevMonth').addEventListener('click',()=>{selected.setMonth(selected.getMonth()-1);load()});$('nextMonth').addEventListener('click',()=>{if(!$('nextMonth').disabled){selected.setMonth(selected.getMonth()+1);load()}});load();
});
</script>
@endsection
