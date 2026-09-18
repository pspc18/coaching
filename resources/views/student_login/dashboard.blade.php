@php
    $getSetting = Helper::getSetting();
    $dashboardStudent = $getUser ?? Helper::getUser();
    $studentName = trim(($dashboardStudent->first_name ?? '').' '.($dashboardStudent->last_name ?? ''));
    $firstName = $dashboardStudent->first_name ?? 'Student';
    $className = optional($dashboardStudent->ClassTypes)->name ?? '-';
    $admissionNo = $dashboardStudent->admissionNo ?? '-';
    $hour = (int) date('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $dashboardAvatar = !empty($dashboardStudent->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$dashboardStudent->image
        : null;
    $notificationCounts = collect($notificationCounts ?? []);
    $modules = [
        ['icon' => 'bi-calendar2-check', 'label' => 'Attendance', 'text' => 'View your daily attendance', 'url' => 'AttendanceView_student', 'tone' => 'blue', 'category' => 'attendance'],
        ['icon' => 'bi-wallet2', 'label' => 'Fees', 'text' => 'Payments and receipts', 'url' => 'fees_history', 'tone' => 'green', 'category' => 'fees'],
        ['icon' => 'bi-file-earmark-bar-graph', 'label' => 'Exam Results', 'text' => 'Check published results', 'url' => 'student/result-card', 'tone' => 'purple', 'category' => 'result'],
        ['icon' => 'bi-megaphone', 'label' => 'Notices', 'text' => 'Latest school updates', 'url' => 'student-notices', 'tone' => 'amber', 'category' => 'notice'],
        ['icon' => 'bi-chat-square-text', 'label' => 'Complaints', 'text' => 'Ask for help and support', 'url' => 'student-complaints', 'tone' => 'red', 'category' => 'complaint'],
    ];
@endphp

@extends('student_login.layout.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_sub', $firstName.' - '.$className)

@section('content')
<section class="student-dashboard-app">
    <div class="dashboard-hero">
        <div class="dashboard-hero-grid"></div>
        <div class="dashboard-orb dashboard-orb-one"></div>
        <div class="dashboard-orb dashboard-orb-two"></div>
        <div class="dashboard-hero-top">
            <div class="dashboard-greeting">
                <span>{{ $greeting }}</span>
                <h1>{{ $firstName }} <span aria-hidden="true">👋</span></h1>
                <p>{{ date('l, d F') }}</p>
            </div>
            <div class="dashboard-profile-visual">
                <div class="dashboard-avatar-halo"></div>
                <div class="dashboard-avatar-frame">
                @if($dashboardAvatar)
                    <span class="dashboard-avatar-photo" style="background-image:url('{{ $dashboardAvatar }}')" role="img" aria-label="{{ $studentName ?: 'Student' }}"></span>
                @else
                    <i class="bi bi-person-fill"></i>
                @endif
                    <span class="dashboard-avatar-status"></span>
                </div>
                <div class="dashboard-avatar-caption"><i class="bi bi-patch-check-fill"></i> Student</div>
            </div>
        </div>
        <div class="dashboard-student-summary">
            <div><span><i class="bi bi-people"></i> Class</span><strong>{{ $className }}</strong></div>
            <div><span><i class="bi bi-person-vcard"></i> Admission No.</span><strong>{{ $admissionNo }}</strong></div>
        </div>
    </div>

    <div class="dashboard-content">
        <!-- <div class="dashboard-section-heading"> 
            <div> 
                <span>Fee Reminder</span> 
               <div style="text-align: center; margin-top: 15px;">
            <marquee behavior="scroll"
                    direction="left"
                    scrollamount="5"
                    style="
                        color: red;
                        font-size: 26px;
                        font-weight: 500;
                    ">
                Fee Reminder: Please check your pending fees and complete your payment on time.
            </marquee>
        </div>
            </div>
        </div> -->
        @php
          //dd(Session::all());
           use Carbon\Carbon;
            $today = Carbon::today();
            $reminderEndDate = Carbon::today()->addDays(5);

            $duefees = DB::table('fees_assign_details')
                ->where('admission_id', Session::get('id'))
                ->whereNotNull('installment_due_date')
                ->whereDate('installment_due_date', '>=', $today)
                ->whereDate('installment_due_date', '<=', $reminderEndDate)
                ->get();
        @endphp
        @if($duefees->count() > 0)
        <div class="fee-reminder">
            <marquee behavior="scroll" direction="left" scrollamount="5">
                🔔 <strong>Fee Reminder:</strong>
                Please check your pending fees and complete your payment on time.
                Don't miss your fee due date!
            </marquee>
        </div>
        @endif

        <style>
            .fee-reminder {
                text-align: center;
                color: #ff0000;
                font-size: 24px;
                font-weight: 600;
                margin: 15px 0;
            }
        </style>


        <div class="dashboard-section-heading">
            <div><span>Quick access</span><small>Everything you need for school</small></div>
        </div>

        <div class="dashboard-actions">
            @foreach($modules as $item)
                @php
                    $badgeCount = (int) data_get($notificationCounts, $item['category'], 0);
                    $badgeUrl = url('notificationFatchStudent?category='.$item['category']);
                @endphp
                <div class="dashboard-action {{ $loop->last ? 'dashboard-action-wide' : '' }}">
                    <a href="{{ url($item['url']) }}" class="dashboard-action-link" aria-label="Open {{ $item['label'] }}"></a>
                    <span class="dashboard-action-icon tone-{{ $item['tone'] }}"><i class="bi {{ $item['icon'] }}"></i></span>
                    <span class="dashboard-action-copy"><strong>{{ $item['label'] }}</strong><small>{{ $item['text'] }}</small></span>
                    <span class="dashboard-action-arrow"><i class="bi bi-arrow-up-right"></i></span>
                    @if($badgeCount > 0)
                        <a href="{{ $badgeUrl }}" class="dashboard-action-badge" aria-label="{{ $badgeCount }} unread {{ strtolower($item['label']) }} notifications">
                            {{ $badgeCount > 99 ? '99+' : $badgeCount }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="dashboard-section-heading school-heading">
            <div><span>Your school</span><small>Institution information</small></div>
        </div>

        <div class="dashboard-school-card">
            <div class="school-card-head">
                <span class="school-card-icon"><i class="bi bi-building"></i></span>
                <div><strong>{{ $getSetting->name ?? 'School' }}</strong><small>Learning today, leading tomorrow</small></div>
            </div>
            @if(!empty($getSetting->address))
                <div class="school-detail-row"><i class="bi bi-geo-alt"></i><span>{{ $getSetting->address }}</span></div>
            @endif
            @if(!empty($getSetting->gmail))
                <div class="school-detail-row"><i class="bi bi-envelope"></i><span>{{ $getSetting->gmail }}</span></div>
            @endif
        </div>
    </div>
</section>

<style>
.student-dashboard-app{min-height:calc(100vh - 154px)!important;padding:0 0 28px!important;color:#253858!important;background:#f3f6fb!important;overflow:hidden!important}
.dashboard-hero{position:relative!important;min-height:202px!important;margin:0!important;padding:15px 15px 13px!important;overflow:hidden!important;color:#fff!important;background:linear-gradient(145deg,#4169e1 0%,#294bb4 53%,#172d73 100%)!important;border-radius:0 0 27px 27px!important;box-shadow:0 14px 30px rgba(31,56,139,.19)!important}
.dashboard-hero-grid{position:absolute!important;inset:0!important;opacity:.12!important;background-image:linear-gradient(rgba(255,255,255,.3) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.3) 1px,transparent 1px)!important;background-size:28px 28px!important;mask-image:linear-gradient(to bottom,#000,transparent 94%)!important}.dashboard-orb{position:absolute!important;border-radius:50%!important;background:rgba(255,255,255,.08)!important}.dashboard-orb-one{width:190px!important;height:190px!important;right:-78px!important;top:-72px!important}.dashboard-orb-two{width:115px!important;height:115px!important;left:-45px!important;bottom:-40px!important}
.dashboard-hero-top{position:relative!important;z-index:1!important;min-height:103px!important;display:grid!important;grid-template-columns:minmax(0,1fr) 46%!important;align-items:center!important;gap:8px!important}.dashboard-greeting{min-width:0!important;align-self:center!important;justify-self:stretch!important;padding:0!important;text-align:center!important}.dashboard-greeting>span{display:block!important;margin:0 0 2px!important;color:rgba(255,255,255,.7)!important;font-size:8px!important;font-weight:650!important;letter-spacing:.07em!important;text-transform:uppercase!important}.dashboard-greeting h1{margin:0!important;overflow:hidden!important;color:#fff!important;font-size:22px!important;line-height:1.2!important;font-weight:750!important;letter-spacing:-.025em!important;text-overflow:ellipsis!important;white-space:nowrap!important}.dashboard-greeting h1 span{font-size:18px!important}.dashboard-greeting p{margin:4px 0 0!important;color:rgba(255,255,255,.72)!important;font-size:9px!important}.dashboard-profile-visual{position:relative!important;min-width:0!important;height:101px!important;display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;align-self:center!important;justify-self:stretch!important}.dashboard-avatar-halo{position:absolute!important;top:0!important;width:94px!important;height:94px!important;border-radius:50%!important;background:radial-gradient(circle,rgba(255,255,255,.19),rgba(255,255,255,.035) 68%,transparent 70%)!important}.dashboard-avatar-frame{position:relative!important;z-index:1!important;width:78px!important;height:78px!important;min-width:78px!important;min-height:78px!important;max-width:78px!important;max-height:78px!important;border:3px solid rgba(255,255,255,.94)!important;border-radius:50%!important;display:grid!important;place-items:center!important;overflow:visible!important;color:#3156d3!important;background:linear-gradient(145deg,#eef3ff,#dce6ff)!important;box-shadow:0 10px 23px rgba(12,30,81,.28)!important;font-size:37px!important}.dashboard-avatar-photo{position:absolute!important;inset:0!important;width:72px!important;height:72px!important;min-width:72px!important;min-height:72px!important;max-width:72px!important;max-height:72px!important;border-radius:50%!important;display:block!important;background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important}.dashboard-avatar-status{position:absolute!important;right:0!important;bottom:4px!important;width:13px!important;height:13px!important;border:2px solid #fff!important;border-radius:50%!important;background:#2bb673!important}.dashboard-avatar-caption{position:relative!important;z-index:2!important;margin-top:-3px!important;padding:3px 8px!important;border:1px solid rgba(255,255,255,.22)!important;border-radius:99px!important;display:flex!important;align-items:center!important;gap:4px!important;color:#fff!important;background:rgba(17,39,100,.72)!important;box-shadow:0 4px 11px rgba(12,30,81,.22)!important;font-size:7px!important;line-height:1!important;font-weight:650!important;letter-spacing:.03em!important}.dashboard-avatar-caption i{color:#7ce3ac!important;font-size:8px!important}
.dashboard-student-summary{position:absolute!important;right:15px!important;bottom:13px!important;left:15px!important;z-index:1!important;display:grid!important;grid-template-columns:1fr 1fr!important;padding:10px 4px!important;border:1px solid rgba(255,255,255,.17)!important;border-radius:15px!important;background:rgba(15,35,90,.25)!important;backdrop-filter:blur(10px)!important;-webkit-backdrop-filter:blur(10px)!important}.dashboard-student-summary>div{min-width:0!important;padding:0 11px!important}.dashboard-student-summary>div+div{border-left:1px solid rgba(255,255,255,.16)!important}.dashboard-student-summary span{display:flex!important;align-items:center!important;gap:5px!important;color:rgba(255,255,255,.66)!important;font-size:7px!important;line-height:1.2!important;text-transform:uppercase!important;letter-spacing:.055em!important}.dashboard-student-summary strong{display:block!important;margin-top:4px!important;overflow:hidden!important;color:#fff!important;font-size:11px!important;line-height:1.2!important;font-weight:700!important;text-overflow:ellipsis!important;white-space:nowrap!important}
.dashboard-content{padding:0 14px!important}.dashboard-section-heading{display:flex!important;align-items:center!important;justify-content:space-between!important;margin:21px 2px 9px!important}.dashboard-section-heading span{display:block!important;color:#26354e!important;font-size:14px!important;font-weight:750!important}.dashboard-section-heading small{display:block!important;margin-top:1px!important;color:#98a2b3!important;font-size:9px!important}.dashboard-actions{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:10px!important}.dashboard-action{position:relative!important;min-width:0!important;min-height:125px!important;padding:13px!important;border:1px solid #e8ecf3!important;border-radius:18px!important;display:flex!important;flex-direction:column!important;align-items:flex-start!important;color:#253858!important;text-decoration:none!important;background:#fff!important;box-shadow:0 7px 22px rgba(42,55,92,.06)!important;overflow:hidden!important;transition:transform .16s ease,box-shadow .18s ease!important}.dashboard-action:active{transform:scale(.975)!important}.dashboard-action:before{content:""!important;position:absolute!important;right:-24px!important;bottom:-28px!important;width:75px!important;height:75px!important;border-radius:50%!important;background:#f5f7fc!important}.dashboard-action-wide{grid-column:1/-1!important;min-height:91px!important;display:grid!important;grid-template-columns:46px minmax(0,1fr) 31px!important;align-items:center!important;gap:11px!important}.dashboard-action-link{position:absolute!important;inset:0!important;z-index:0!important;display:block!important}.dashboard-action-icon{position:relative!important;z-index:1!important;width:43px!important;height:43px!important;min-width:43px!important;max-width:43px!important;border-radius:13px!important;display:grid!important;place-items:center!important;font-size:19px!important}.tone-blue{color:#3156d3!important;background:#e8eeff!important}.tone-green{color:#139466!important;background:#e4f8ef!important}.tone-purple{color:#7445c7!important;background:#f0e9ff!important}.tone-amber{color:#d78a00!important;background:#fff4dc!important}.tone-red{color:#d14d4d!important;background:#ffeceb!important}.dashboard-action-copy{position:relative!important;z-index:1!important;min-width:0!important;margin-top:auto!important}.dashboard-action-wide .dashboard-action-copy{margin-top:0!important}.dashboard-action-copy strong{display:block!important;color:#253858!important;font-size:12px!important;line-height:1.3!important;font-weight:700!important}.dashboard-action-copy small{display:block!important;margin-top:3px!important;color:#98a2b3!important;font-size:8px!important;line-height:1.35!important}.dashboard-action-arrow{position:absolute!important;z-index:1!important;right:11px!important;top:11px!important;width:27px!important;height:27px!important;border-radius:9px!important;display:grid!important;place-items:center!important;color:#7c8aa1!important;background:#f3f6fa!important;font-size:11px!important}.dashboard-action-wide .dashboard-action-arrow{position:relative!important;right:auto!important;top:auto!important;width:31px!important;height:31px!important}.dashboard-action-badge{position:absolute!important;z-index:2!important;right:10px!important;bottom:10px!important;min-width:28px!important;height:28px!important;padding:0 8px!important;border:2px solid #fff!important;border-radius:999px!important;display:grid!important;place-items:center!important;color:#fff!important;background:linear-gradient(145deg,#e34850,#c92d40)!important;box-shadow:0 8px 18px rgba(227,72,80,.23)!important;font-size:11px!important;line-height:1!important;font-weight:800!important;text-decoration:none!important}.dashboard-action-wide .dashboard-action-badge{bottom:auto!important;top:11px!important}
.school-heading{margin-top:23px!important}.dashboard-school-card{padding:14px!important;border:1px solid #e8ecf3!important;border-radius:18px!important;color:#253858!important;background:linear-gradient(135deg,#fff,#f8faff)!important;box-shadow:0 7px 22px rgba(42,55,92,.055)!important}.school-card-head{display:flex!important;align-items:center!important;gap:11px!important;padding-bottom:12px!important}.school-card-icon{width:42px!important;height:42px!important;min-width:42px!important;border-radius:13px!important;display:grid!important;place-items:center!important;color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4)!important;font-size:19px!important;box-shadow:0 6px 14px rgba(49,86,211,.22)!important}.school-card-head>div{min-width:0!important}.school-card-head strong{display:block!important;overflow:hidden!important;color:#253858!important;font-size:12px!important;line-height:1.3!important;font-weight:700!important;text-overflow:ellipsis!important;white-space:nowrap!important}.school-card-head small{display:block!important;margin-top:2px!important;color:#98a2b3!important;font-size:8px!important}.school-detail-row{display:flex!important;align-items:flex-start!important;gap:9px!important;padding:9px 2px!important;border-top:1px solid #eef1f6!important;color:#69768a!important;font-size:9px!important;line-height:1.45!important}.school-detail-row i{margin-top:1px!important;color:#3156d3!important;font-size:12px!important}.school-detail-row span{min-width:0!important;overflow-wrap:anywhere!important}
:root[data-theme="dark"] .student-dashboard-app{color:#e7ecf4!important;background:#111827!important}:root[data-theme="dark"] .dashboard-section-heading span,:root[data-theme="dark"] .dashboard-action,:root[data-theme="dark"] .dashboard-action-copy strong,:root[data-theme="dark"] .dashboard-school-card,:root[data-theme="dark"] .school-card-head strong{color:#e7ecf4!important}:root[data-theme="dark"] .dashboard-action,:root[data-theme="dark"] .dashboard-school-card{background:#1b2433!important;border-color:#2c3748!important;box-shadow:none!important}:root[data-theme="dark"] .dashboard-action:before,:root[data-theme="dark"] .dashboard-action-arrow{background:#222e42!important}:root[data-theme="dark"] .school-detail-row{border-color:#303b4c!important;color:#aab4c3!important}
@media(max-width:359px){.dashboard-hero{padding-left:12px!important;padding-right:12px!important}.dashboard-hero-top{grid-template-columns:minmax(0,1fr) 43%!important}.dashboard-avatar-frame{width:72px!important;height:72px!important;min-width:72px!important;min-height:72px!important;max-width:72px!important;max-height:72px!important}.dashboard-avatar-photo{width:66px!important;height:66px!important;min-width:66px!important;min-height:66px!important;max-width:66px!important;max-height:66px!important}.dashboard-avatar-halo{width:88px!important;height:88px!important}.dashboard-student-summary{left:12px!important;right:12px!important}.dashboard-content{padding-left:11px!important;padding-right:11px!important}.dashboard-actions{gap:8px!important}.dashboard-action{padding:11px!important}}
</style>

<script>
window.addEventListener('load', function () {
    localStorage.setItem('user', JSON.stringify({
        id: @json((string) Session::get('id')),
        modal: @json((string) (Session::get('modal_name') ?: 'User'))
    }));
});
</script>
@endsection
