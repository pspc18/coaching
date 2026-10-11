@php
    $getSetting = Helper::getSetting();
    $getUser = Helper::getUser();
    $siblingsList = Helper::getSiblings();
    $currentSession = Session::get('session_id');
    $currentBranch = Session::get('branch_id');
    $studentId = Session::get('id');
    $studentName = trim(($getUser->first_name ?? '') . ' ' . ($getUser->last_name ?? ''));
    if (empty($studentName)) $studentName = 'Student';
    $className = optional($getUser)->ClassTypes->name ?? '-';
    $sessionName = Session::get('session_name') ?? date('Y');
    $studentAvatar = !empty($getUser->image)
        ? env('IMAGE_SHOW_PATH') . '/profile/' . $getUser->image
        : null;

    $notificationCount = 0;
    if (!empty($studentId)) {
        $notificationCount = (int) DB::table('notifications')
            ->where('admission_id', $studentId)
            ->where('show_status', 1)
            ->count();
    }
    $unreadAlerts = $notificationCount;

    $mobileLogoFile = $getSetting->left_logo ?? '';
    $mobileLogoUrl = !empty($mobileLogoFile)
        ? rtrim((string) env('IMAGE_SHOW_PATH'), '/') . '/setting/left_logo/' . rawurlencode($mobileLogoFile)
        : '';
    $firmInitial = strtoupper(mb_substr(trim($getSetting->name ?? 'A'), 0, 1, 'UTF-8')) ?: 'A';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#001833">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Student Portal') - {{ $getSetting->name ?? 'ARISE ERP' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ !empty($mobileLogoUrl) ? $mobileLogoUrl : asset('public/assets/school/img/logo.png') }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Typography & Icons matching Admin Mobile Layout --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
    /* ==========================================================================
       ARISE ERP - NATIVE STUDENT MOBILE APP FRAMEWORK
       Same Layout & Design System as Admin Panel Mobile View
       ========================================================================== */
    :root {
        --app-navy-900: #001428;
        --app-navy-800: #001f3f;
        --app-navy-700: #002C54;
        --app-navy-600: #0a4275;
        --app-cyan: #06b6d4;
        --app-cyan-glow: rgba(6, 182, 212, 0.25);
        --app-sky: #0284c7;
        --app-emerald: #10b981;
        --app-amber: #f59e0b;
        --app-rose: #f43f5e;
        --app-purple: #8b5cf6;
        --app-bg: #f8fafc;
        --app-card: #ffffff;
        --app-border: #cbd5e1;
        --app-text: #0f172a;
        --app-text-muted: #64748b;
        --header-height: 48px;
        --bottom-nav-height: 52px;
        --safe-bottom: env(safe-area-inset-bottom, 0px);
    }

    * {
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
        margin: 0;
        padding: 0;
    }

    body {
        background-color: #0b1727;
        color: var(--app-text);
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        display: flex;
        justify-content: center;
    }

    /* Mobile Viewport Wrapper - Centers on Desktop, 100% on Mobile */
    .mobile-app-shell {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        position: relative;
        background: linear-gradient(180deg, #001f3f 0%, #002C54 100px, #f8fafc 200px, #f8fafc 100%);
        box-shadow: 0 0 50px rgba(0, 15, 30, 0.45);
        padding-top: var(--header-height);
        padding-bottom: calc(var(--bottom-nav-height) + var(--safe-bottom) + 12px);
    }

    /* Main Content Area */
    .mobile-app-main {
        flex: 1;
        width: 100%;
        padding: 6px 8px 16px;
    }

    /* Common Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 11.5px;
        line-height: 1.4;
        border-radius: 4px;
        transition: all .15s ease-in-out;
        cursor: pointer;
        text-decoration: none !important;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 11px;
        border-radius: 3px;
    }
    .btn-primary {
        color: #fff !important;
        background-color: #0284c7;
        border-color: #0284c7;
    }
    .btn-secondary {
        color: #334155 !important;
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }

    /* Switch User Bottom Sheet */
    .switch-bottom-sheet {
        position: fixed;
        inset: 0;
        background: rgba(0, 15, 30, 0.72);
        z-index: 2200;
        display: none;
        align-items: flex-end;
        justify-content: center;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .switch-sheet-content {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: 12px 12px 0 0;
        padding: 14px 16px calc(16px + var(--safe-bottom));
        transform: translateY(100%);
        transition: transform .25s ease-in-out;
        box-shadow: 0 -4px 25px rgba(0, 20, 40, 0.35);
        border: 1px solid #cbd5e1;
    }
    .sheet-handle {
        width: 36px;
        height: 4px;
        background: #cbd5e1;
        border-radius: 2px;
        margin: 0 auto 10px;
    }
    .sheet-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #002C54;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .switch-option-list {
        list-style: none;
        padding: 0;
        margin: 0 0 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .switch-option-list li {
        padding: 10px 12px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        transition: all .15s ease;
    }
    .switch-option-list li.active {
        background: #e0f2fe;
        border-color: #38bdf8;
        color: #0284c7;
    }
    .cancel-btn {
        width: 100%;
        padding: 8px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 11.5px;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
    }
    </style>

    @yield('styles')
</head>
<body>

<div class="mobile-app-shell">

    {{-- Top App Bar (Header) --}}
    @include('student_login.layout.header')

    {{-- Main Page Content --}}
    <main class="mobile-app-main">
        @yield('content')
    </main>

    {{-- Floating Glassmorphic Bottom Dock Navigation (Footer) --}}
    @include('student_login.layout.footer')

</div>

{{-- Slide-over Mobile App Drawer (Sidebar) --}}
@include('student_login.layout.sidebar')

{{-- Switch User Bottom Sheet (Sibling Switch) --}}
<div class="switch-bottom-sheet" id="switchBottomSheet">
    <div class="switch-sheet-content">
        <div class="sheet-handle"></div>
        <h5 class="sheet-title"><i class="fa fa-users text-primary"></i> Switch Student Profile</h5>
        <ul class="switch-option-list">
            <li class="active">
                <span>{{ $studentName }} - {{ $className }}</span>
                <span class="badge bg-success" style="background:#16a34a;color:#fff;padding:2px 6px;border-radius:3px;font-size:9.5px;">Current</span>
            </li>
            @foreach($siblingsList as $sib)
                <li onclick="window.location.href='{{ url('sibling/login/'.$sib->id) }}'">
                    <span>{{ $sib->first_name }} {{ $sib->last_name }} - {{ $sib['ClassTypes']['name'] ?? 'Student' }}</span>
                    <i class="fa fa-chevron-right text-muted"></i>
                </li>
            @endforeach
        </ul>
        <div class="cancel-btn" id="cancelSwitch">Cancel</div>
    </div>
</div>

@include('student_login.layout.student_modal')
@include('student_login.layout.message')

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Mobile Drawer Open / Close
    const drawer = document.getElementById("mobileDrawer");
    const backdrop = document.getElementById("mobileDrawerBackdrop");
    const openBtn = document.getElementById("btnOpenMobileDrawer");
    const closeBtn = document.getElementById("btnCloseMobileDrawer");
    const bottomMoreBtn = document.getElementById("btnBottomMore");

    function openDrawer() {
        if (drawer && backdrop) {
            drawer.classList.add("show");
            backdrop.classList.add("show");
            document.body.style.overflow = "hidden";
        }
    }

    function closeDrawer() {
        if (drawer && backdrop) {
            drawer.classList.remove("show");
            backdrop.classList.remove("show");
            document.body.style.overflow = "";
        }
    }

    if (openBtn) openBtn.addEventListener("click", openDrawer);
    if (bottomMoreBtn) bottomMoreBtn.addEventListener("click", openDrawer);
    if (closeBtn) closeBtn.addEventListener("click", closeDrawer);
    if (backdrop) backdrop.addEventListener("click", closeDrawer);

    // Hard Refresh Action
    const refreshBtn = document.getElementById("hardRefreshBtn");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", function() {
            const icon = this.querySelector("i");
            if (icon) icon.classList.add("fa-spin");
            fetch("{{ url('hard-refresh') }}")
                .then(() => window.location.reload(true))
                .catch(() => window.location.reload(true));
        });
    }

    // Switch User Bottom Sheet Handlers
    const switchBtn = document.getElementById("openSwitchSheet");
    const bottomSheet = document.getElementById("switchBottomSheet");
    const cancelSwitchBtn = document.getElementById("cancelSwitch");

    if (switchBtn && bottomSheet) {
        switchBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            bottomSheet.style.display = "flex";
            setTimeout(() => {
                const sheet = bottomSheet.querySelector(".switch-sheet-content");
                if (sheet) sheet.style.transform = "translateY(0)";
            }, 10);
        });
    }

    function closeSwitchSheet() {
        if (!bottomSheet) return;
        const sheet = bottomSheet.querySelector(".switch-sheet-content");
        if (sheet) sheet.style.transform = "translateY(100%)";
        setTimeout(() => {
            bottomSheet.style.display = "none";
        }, 250);
    }

    if (cancelSwitchBtn) cancelSwitchBtn.addEventListener("click", closeSwitchSheet);
    if (bottomSheet) {
        bottomSheet.addEventListener("click", function(e) {
            if (e.target === bottomSheet) closeSwitchSheet();
        });
    }

    // Live Search Filter inside Drawer
    const searchInput = document.getElementById("mobDrawerSearchInput");
    const menuList = document.getElementById("mobDrawerMenuList");
    if (searchInput && menuList) {
        searchInput.addEventListener("input", function() {
            const query = this.value.toLowerCase().trim();
            const items = menuList.querySelectorAll(".mob-menu-item-wrap");
            items.forEach(function(item) {
                const text = (item.getAttribute("data-name") || item.textContent).toLowerCase();
                item.style.display = text.includes(query) ? "" : "none";
            });
        });
    }
});
</script>

@yield('scripts')
@include('layout.csrf_refresh')
</body>
</html>
