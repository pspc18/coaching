@php
    $getSetting = Helper::getSetting();
    $sidebarStudent = $getUser ?? Helper::getUser();
    $sidebarData = DB::table('students_sidebar')->whereNull('deleted_at')->orderBy('id', 'ASC')->get();
    $sidebarName = trim(($sidebarStudent->first_name ?? '').' '.($sidebarStudent->last_name ?? ''));
    $sidebarClass = optional($sidebarStudent->ClassTypes)->name ?? '';
    $sidebarAvatar = !empty($sidebarStudent->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$sidebarStudent->image
        : null;
    $schoolLogo = !empty($getSetting->left_logo)
        ? env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting->left_logo
        : null;
    $menuIcons = [
        'bi-house-door' => ['dashboard', 'home'],
        'bi-calendar2-check' => ['attendance'],
        'bi-journal-check' => ['homework', 'assignment'],
        'bi-calendar3' => ['time table', 'timetable'],
        'bi-receipt' => ['fee'],
        'bi-file-earmark-bar-graph' => ['result', 'exam'],
        'bi-book' => ['book', 'subject', 'syllabus', 'study material'],
        'bi-person-video3' => ['teacher'],
        'bi-images' => ['gallery'],
        'bi-megaphone' => ['notice', 'notification'],
        'bi-download' => ['download'],
        'bi-chat-square-text' => ['complaint', 'support'],
        'bi-calendar2-minus' => ['leave'],
        'bi-person-badge' => ['id card'],
        'bi-shield-check' => ['rule'],
        'bi-building' => ['school desk'],
        'bi-door-open' => ['gate pass'],
        'bi-bag' => ['uniform'],
        'bi-flower1' => ['prayer'],
    ];
@endphp

<nav id="sidebar" class="app-sidebar student-drawer" aria-label="Student menu">
    <div class="drawer-top">
        <div class="drawer-brand">
            @if($schoolLogo)
                <span class="drawer-logo-photo" style="background-image:url('{{ $schoolLogo }}')" aria-hidden="true"></span>
            @else
                <span class="drawer-logo-fallback"><i class="bi bi-mortarboard-fill"></i></span>
            @endif
            <div class="drawer-brand-copy"><small>Student portal</small><strong>{{ $getSetting->name ?? 'School' }}</strong></div>
        </div>
        <button type="button" id="closeSidebar" class="drawer-close" aria-label="Close navigation menu"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="drawer-scroll">
        <div class="drawer-section-label">Main menu</div>
        <ul class="drawer-menu">
            @foreach($sidebarData as $item)
                @php
                    $itemUrl = trim((string) ($item->url ?? ''), '/');
                    $itemKey = strtolower(trim(($item->name ?? '').' '.$itemUrl));
                    $itemIcon = 'bi-grid';
                    foreach($menuIcons as $icon => $keywords) {
                        if(\Illuminate\Support\Str::contains($itemKey, $keywords)) { $itemIcon = $icon; break; }
                    }
                    $itemActive = url($itemUrl) === URL::current();
                @endphp
                @continue($itemUrl === 'profileStudent')
                <li>
                    <a href="{{ url($itemUrl) }}" class="drawer-link {{ $itemActive ? 'active' : '' }}" @if($itemActive) aria-current="page" @endif>
                        <span class="drawer-link-icon"><i class="bi {{ $itemIcon }}"></i></span>
                        <span class="drawer-link-label">{{ $item->name ?? 'Menu' }}</span>
                        <i class="bi bi-chevron-right drawer-chevron"></i>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="drawer-section-label settings-label">Account</div>
        <ul class="drawer-menu drawer-settings">
            <li><a href="javascript:void(0)" id="logoutBtn" class="drawer-link logout-link"><span class="drawer-link-icon"><i class="bi bi-box-arrow-right"></i></span><span class="drawer-link-label">Logout</span><i class="bi bi-chevron-right drawer-chevron"></i></a></li>
        </ul>
        <div class="drawer-version">Secure student access</div>
    </div>
</nav>

<style>
/* Self-contained drawer styles prevent stale shared CSS from breaking the navigation. */
.student-drawer{
    position:fixed!important;top:0!important;bottom:0!important;left:0!important;z-index:1100!important;
    width:min(84vw,320px)!important;max-width:320px!important;height:100dvh!important;min-height:100vh!important;
    margin:0!important;padding:0!important;display:flex!important;flex-direction:column!important;
    color:#253858!important;background:#f6f8fc!important;border:0!important;border-right:1px solid #e3e8f2!important;
    box-shadow:20px 0 55px rgba(20,34,72,.2)!important;overflow:hidden!important;
    transform:translateX(-105%);transition:transform .28s cubic-bezier(.22,.8,.25,1)!important;
}
.student-drawer.active{transform:translateX(0)!important;left:0!important}
.drawer-top{flex:0 0 auto!important;min-height:72px!important;padding:12px 13px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:10px!important;background:linear-gradient(145deg,#4169e1,#294bb4)!important}
.drawer-brand{display:flex!important;align-items:center!important;gap:10px!important;min-width:0!important;flex:1!important}
.drawer-logo-photo,.drawer-logo-fallback{width:40px!important;height:40px!important;min-width:40px!important;max-width:40px!important;min-height:40px!important;max-height:40px!important;border-radius:12px!important;display:grid!important;place-items:center!important;background-color:#fff!important;border:2px solid rgba(255,255,255,.72)!important;box-shadow:0 6px 14px rgba(17,38,99,.2)!important}
.drawer-logo-photo{background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important}.drawer-logo-fallback{color:#3156d3!important;font-size:21px!important}
.drawer-brand-copy{min-width:0!important;display:block!important}.drawer-brand-copy small{display:block!important;margin:0 0 2px!important;color:rgba(255,255,255,.68)!important;font-size:8px!important;line-height:1.1!important;font-weight:650!important;text-transform:uppercase!important;letter-spacing:.08em!important}.drawer-brand-copy strong{display:block!important;overflow:hidden!important;color:#fff!important;font-size:13px!important;line-height:1.25!important;font-weight:700!important;text-overflow:ellipsis!important;white-space:nowrap!important}
.drawer-close{width:42px!important;height:42px!important;min-width:42px!important;max-width:42px!important;margin:0!important;padding:0!important;border:1px solid rgba(255,255,255,.2)!important;border-radius:13px!important;display:grid!important;place-items:center!important;color:#fff!important;background:rgba(255,255,255,.1)!important;cursor:pointer!important}.drawer-close i{display:block!important;font-size:19px!important;line-height:1!important}.drawer-close:active{transform:scale(.94)!important}.drawer-close:focus-visible{outline:3px solid rgba(255,255,255,.3)!important;outline-offset:2px!important}
.drawer-student{flex:0 0 auto!important;margin:12px 12px 4px!important;padding:11px!important;display:flex!important;align-items:center!important;gap:10px!important;color:#253858!important;text-decoration:none!important;background:#fff!important;border:1px solid #e5eaf3!important;border-radius:16px!important;box-shadow:0 7px 20px rgba(42,55,92,.07)!important}.drawer-avatar{width:44px!important;height:44px!important;min-width:44px!important;max-width:44px!important;border-radius:14px!important;display:grid!important;place-items:center!important;overflow:hidden!important;color:#3156d3!important;background:linear-gradient(145deg,#e8eeff,#d8e3ff)!important;font-size:24px!important}.drawer-avatar-photo{width:44px!important;height:44px!important;display:block!important;background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important}.drawer-student-copy{min-width:0!important;flex:1!important}.drawer-student-copy strong{display:block!important;overflow:hidden!important;color:#253858!important;font-size:12px!important;line-height:1.3!important;font-weight:700!important;text-overflow:ellipsis!important;white-space:nowrap!important}.drawer-student-copy small{display:block!important;margin-top:2px!important;color:#8a94a6!important;font-size:9px!important}.drawer-profile-arrow{width:28px!important;height:28px!important;display:grid!important;place-items:center!important;border-radius:9px!important;color:#3156d3!important;background:#e8eeff!important;font-size:11px!important}
.drawer-scroll{flex:1!important;min-height:0!important;overflow-x:hidden!important;overflow-y:auto!important;padding:6px 10px calc(22px + env(safe-area-inset-bottom))!important;scrollbar-width:thin!important;scrollbar-color:#cad3e4 transparent!important}.drawer-section-label{margin:10px 8px 7px!important;color:#98a2b3!important;font-size:8px!important;line-height:1!important;font-weight:700!important;text-transform:uppercase!important;letter-spacing:.1em!important}.settings-label{margin-top:17px!important}
.student-drawer ul.drawer-menu{list-style:none!important;margin:0!important;padding:0!important}.student-drawer .drawer-menu li{display:block!important;margin:0 0 4px!important;padding:0!important}.student-drawer .drawer-link{width:100%!important;min-height:50px!important;margin:0!important;padding:6px 8px!important;border:0!important;border-radius:14px!important;display:flex!important;align-items:center!important;gap:10px!important;color:#536176!important;text-decoration:none!important;background:transparent!important;transform:none!important;transition:background .18s ease,color .18s ease,transform .16s ease!important}.student-drawer .drawer-link:active{transform:scale(.985)!important}.student-drawer .drawer-link:hover{color:#3156d3!important;background:#edf2ff!important;transform:none!important}.drawer-link-icon{width:36px!important;height:36px!important;min-width:36px!important;max-width:36px!important;border-radius:11px!important;display:grid!important;place-items:center!important;color:#65738a!important;background:#fff!important;border:1px solid #e8ecf3!important;font-size:16px!important}.drawer-link-label{min-width:0!important;flex:1!important;overflow:hidden!important;font-size:11px!important;line-height:1.2!important;font-weight:600!important;text-overflow:ellipsis!important;white-space:nowrap!important}.drawer-chevron{color:#a7b0bf!important;font-size:10px!important}
.student-drawer .drawer-link.active{color:#2349bd!important;background:linear-gradient(100deg,#e9efff,#f4f6ff)!important;box-shadow:inset 3px 0 #3156d3!important;transform:none!important}.student-drawer .drawer-link.active .drawer-link-icon{color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4)!important;border-color:transparent!important;box-shadow:0 5px 12px rgba(49,86,211,.22)!important}.student-drawer .drawer-link.active .drawer-chevron{color:#3156d3!important}
.theme-switch{width:34px!important;height:20px!important;min-width:34px!important;padding:2px!important;border-radius:99px!important;display:block!important;background:#dfe5ef!important;transition:background .2s ease!important}.theme-switch span{display:block!important;width:16px!important;height:16px!important;border-radius:50%!important;background:#fff!important;box-shadow:0 2px 5px rgba(0,0,0,.18)!important;transition:transform .2s ease!important}:root[data-theme="dark"] .theme-switch{background:#5b7fff!important}:root[data-theme="dark"] .theme-switch span{transform:translateX(14px)!important}.logout-link{color:#c74444!important}.logout-link .drawer-link-icon{color:#d14d4d!important;background:#fff0f0!important;border-color:#ffe0e0!important}.drawer-version{text-align:center!important;margin:17px 0 3px!important;color:#a5aebb!important;font-size:8px!important;letter-spacing:.04em!important}
.sidebar-overlay{position:fixed!important;inset:0!important;z-index:1090!important;width:100%!important;height:100%!important;background:rgba(12,20,39,.54)!important;backdrop-filter:blur(3px)!important;-webkit-backdrop-filter:blur(3px)!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;transition:opacity .25s ease,visibility .25s ease!important}.sidebar-overlay.show{opacity:1!important;visibility:visible!important;pointer-events:auto!important}
:root[data-theme="dark"] .student-drawer{color:#e7ecf4!important;background:#111827!important;border-right-color:#2c3748!important}:root[data-theme="dark"] .drawer-student{color:#e7ecf4!important;background:#1b2433!important;border-color:#2c3748!important;box-shadow:none!important}:root[data-theme="dark"] .drawer-student-copy strong{color:#e7ecf4!important}:root[data-theme="dark"] .drawer-avatar{background:#222e42!important}:root[data-theme="dark"] .student-drawer .drawer-link{color:#aab4c3!important}:root[data-theme="dark"] .student-drawer .drawer-link:hover{color:#b8c7ff!important;background:#1d2940!important}:root[data-theme="dark"] .drawer-link-icon{color:#aab4c3!important;background:#1b2433!important;border-color:#2c3748!important}:root[data-theme="dark"] .student-drawer .drawer-link.active{color:#b8c7ff!important;background:#1d2940!important}:root[data-theme="dark"] .logout-link{color:#ff8b8b!important}:root[data-theme="dark"] .logout-link .drawer-link-icon{color:#ff8b8b!important;background:#3a2429!important;border-color:#513038!important}
@media(max-width:359px){.student-drawer{width:88vw!important}.drawer-top{padding-left:10px!important;padding-right:10px!important}}
</style>

<script>
document.getElementById('logoutBtn').addEventListener('click', function () {
    if (document.getElementById('closeSidebar')) document.getElementById('closeSidebar').click();
    setTimeout(function () {
        StudentModal.confirm({
            title: 'Logout?',
            text: 'You will need to sign in again to access your student account.',
            type: 'warning',
            danger: true,
            confirmText: 'Yes, logout',
            cancelText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) window.location.href = @json(url('logout'));
        });
    }, 220);
});
</script>
