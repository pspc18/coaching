@php
    $setting = Helper::getSetting();
    $currentSession = Session::get('session_id');
    $currentBranch = Session::get('branch_id');
    $userRole = Session::get('role_id');
    $userName = Session::get('first_name') ?? 'Admin';
    $userFullName = trim((Session::get('first_name') ?? '') . ' ' . (Session::get('last_name') ?? ''));
    if (empty($userFullName)) $userFullName = 'Administrator';
    $userImage = Session::get('image');
    $noticesData = Helper::noticeBoard();
    $unreadNoticesCount = is_countable($noticesData) ? count($noticesData) : 0;

    // Fetch dynamic sidebar permissions exactly like desktop layout/sidebar.blade.php
    $branch = \App\Models\Master\Branch::find(Session::get('branch_id'));
    $branchSidebarIds = !empty($branch->branch_sidebar_id) ? explode(',', $branch->branch_sidebar_id) : [];
    $Permisn = Helper::getPermisn();

    $sidebar = DB::table('sidebars')->whereNull('deleted_at')->whereIn('id', $Permisn)->orderBy('order_by','ASC')->get();
    $subSidebar = DB::table('sidebar_sub')->where('sub_sidebar','yes')->whereIn('sidebar_id', $branchSidebarIds)->whereNull('deleted_at')->orderBy('orderBy','ASC')->get();

    if ((int) Session::get('role_id') === 1) {
        $sidebar = $sidebar->reject(function ($item) {
            return trim((string) ($item->url ?? ''), '/') === 'attendance/self';
        })->values();
        $subSidebar = $subSidebar->reject(function ($item) {
            return trim((string) ($item->url ?? ''), '/') === 'attendance/self';
        })->values();
    }

    if ((int) Session::get('role_id') === 1) {
        $academicCalendarSubmenu = DB::table('sidebar_sub')
            ->where('sidebar_id', 9)
            ->where('url', 'add_weekend')
            ->whereNull('deleted_at')
            ->first();

        if ($academicCalendarSubmenu && !$subSidebar->contains('id', $academicCalendarSubmenu->id)) {
            $subSidebar->push($academicCalendarSubmenu);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#001833">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $setting->name ?? 'ARISE ERP' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset($setting->left_logo ?? '') }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Core Fonts & Assets --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-modals.css') }}?v=1789536729">
    
    <script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('public/assets/school/js/bootstrap.bundle.min.js') }}"></script>

    <style>
    /* ==========================================================================
       ARISE ERP - NEXT-GEN STYLISH MOBILE APP FRAMEWORK
       - Dynamic Sidebar Integration Matching Desktop Menus
       - Smooth Accordions & Fast Mobile Drawer
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
        --app-border: #e2e8f0;
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

    html, body {
        background-color: var(--app-bg);
        color: var(--app-text);
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        width: 100%;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Utility classes */
    .d-none { display: none !important; }
    .d-block { display: block !important; }
    .d-flex { display: flex !important; }
    .text-center { text-align: center !important; }
    .text-right { text-align: right !important; }
    .text-left { text-align: left !important; }
    .text-muted { color: #64748b !important; }
    .text-primary { color: #0284c7 !important; }
    .text-success { color: #16a34a !important; }
    .text-danger { color: #dc2626 !important; }
    .text-warning { color: #d97706 !important; }
    .mr-1 { margin-right: 0.25rem !important; }
    .ml-1 { margin-left: 0.25rem !important; }
    .mb-1 { margin-bottom: 0.25rem !important; }
    .mt-1 { margin-top: 0.25rem !important; }
    .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    .py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }

    /* Core Buttons */
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
    .btn-xs {
        padding: 0.15rem 0.45rem;
        font-size: 10px;
        border-radius: 2px;
        line-height: 1.2;
    }
    .btn-primary {
        color: #fff !important;
        background-color: #0284c7;
        border-color: #0284c7;
    }
    .btn-primary:active, .btn-primary:hover {
        background-color: #0369a1;
        border-color: #0369a1;
    }
    .btn-secondary {
        color: #334155 !important;
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }
    .btn-secondary:active, .btn-secondary:hover {
        background-color: #e2e8f0;
    }
    .btn-danger {
        color: #fff !important;
        background-color: #dc2626;
        border-color: #dc2626;
    }
    .btn-danger:active, .btn-danger:hover {
        background-color: #b91c1c;
    }
    .btn-light {
        color: #0f172a !important;
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }

    /* Core Form Controls */
    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.4;
        color: #0f172a;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        outline: none;
    }
    .form-control:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
    }
    .form-control-sm {
        height: 30px;
        padding: 0.2rem 0.5rem;
        font-size: 11.5px;
    }

    /* Core Badges */
    .badge {
        display: inline-block;
        padding: 0.25em 0.5em;
        font-size: 9.5px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 3px;
    }
    .bg-success { background-color: #16a34a !important; color: #fff !important; }
    .bg-light { background-color: #f1f5f9 !important; color: #334155 !important; }
    .bg-warning { background-color: #f59e0b !important; color: #000 !important; }
    .bg-danger { background-color: #dc2626 !important; color: #fff !important; }
    .border { border: 1px solid #cbd5e1 !important; }

    /* App Shell */
    .mobile-app-shell {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
        padding-top: var(--header-height);
        padding-bottom: calc(var(--bottom-nav-height) + var(--safe-bottom) + 12px);
        background: linear-gradient(180deg, #001f3f 0%, #002C54 100px, #f8fafc 200px, #f8fafc 100%);
    }

    /* 1. Compact Header Bar (Glassmorphic Dark Navy) */
    .mobile-top-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height);
        background: rgba(0, 20, 40, 0.94);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 10px;
        z-index: 1000;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .top-bar-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        color: #ffffff !important;
    }
    .top-bar-logo {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        background: #ffffff;
        padding: 2px;
        object-fit: contain;
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
    }
    .top-bar-info {
        display: flex;
        flex-direction: column;
    }
    .top-bar-name {
        font-size: 12.5px;
        font-weight: 800;
        line-height: 1.15;
        color: #ffffff;
        letter-spacing: -0.01em;
        max-width: 170px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .top-bar-tag {
        font-size: 8.5px;
        color: #38bdf8;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 3px;
        line-height: 1.1;
    }

    .top-bar-actions {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .top-icon-pill {
        width: 31px;
        height: 31px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12.5px;
        cursor: pointer;
        text-decoration: none !important;
        transition: all .12s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .top-icon-pill:active {
        transform: scale(0.93);
        background: rgba(255, 255, 255, 0.18);
    }
    .top-badge-pulse {
        position: absolute;
        top: 2px;
        right: 2px;
        min-width: 13px;
        height: 13px;
        border-radius: 2px;
        background: #ef4444;
        color: #ffffff;
        font-size: 8px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 2px;
        border: 1px solid #001428;
        box-shadow: 0 0 6px rgba(239, 68, 68, 0.7);
    }

    /* 2. Main Body Content */
    .mobile-app-main {
        flex: 1;
        width: 100%;
        padding: 6px 8px 16px;
    }

    /* 3. Compact Sharp Floating Bottom Dock */
    .mobile-bottom-dock {
        position: fixed;
        bottom: calc(6px + var(--safe-bottom));
        left: 8px;
        right: 8px;
        height: var(--bottom-nav-height);
        background: rgba(0, 26, 51, 0.94);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 0 3px;
        z-index: 1000;
        box-shadow: 0 6px 20px -2px rgba(0, 20, 40, 0.45);
    }
    .dock-nav-item {
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        color: #94a3b8;
        font-size: 9.5px;
        font-weight: 600;
        gap: 2px;
        transition: all .15s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        padding: 2px 0;
    }
    .dock-nav-icon {
        width: 28px;
        height: 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13.5px;
        transition: all .15s ease;
    }
    .dock-nav-item.active {
        color: #38bdf8;
        font-weight: 800;
    }
    .dock-nav-item.active .dock-nav-icon {
        background: rgba(56, 189, 248, 0.18);
        border: 1px solid rgba(56, 189, 248, 0.35);
        color: #38bdf8;
        transform: translateY(-1px);
    }
    .dock-nav-item:active {
        transform: scale(0.92);
    }

    /* 4. Slide-over Mobile App Drawer (Ultra Clean & Sharp) */
    .mobile-drawer-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 15, 30, 0.7);
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: all .25s ease-in-out;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .mobile-drawer-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
    .mobile-drawer {
        position: fixed;
        top: 0;
        bottom: 0;
        right: -340px;
        width: 320px;
        max-width: 88vw;
        background: #ffffff;
        z-index: 2001;
        display: flex;
        flex-direction: column;
        transition: right .28s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: -8px 0 30px rgba(0, 0, 0, 0.25);
    }
    .mobile-drawer.show {
        right: 0;
    }
    .drawer-header {
        background: linear-gradient(135deg, #001833 0%, #002C54 100%);
        color: #ffffff;
        padding: 12px 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        flex-shrink: 0;
    }
    .drawer-avatar {
        width: 38px;
        height: 38px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #ffffff;
        flex-shrink: 0;
        overflow: hidden;
    }
    .drawer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .drawer-user-info {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .drawer-user-name {
        font-size: 13px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .drawer-user-role {
        font-size: 9.5px;
        color: #38bdf8;
        font-weight: 600;
        margin-top: 2px;
    }
    .drawer-close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 26px;
        height: 26px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.12);
        border: none;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        cursor: pointer;
    }

    /* Drawer Live Search Bar */
    .drawer-search-wrap {
        padding: 6px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        position: relative;
        flex-shrink: 0;
    }
    .drawer-search-wrap i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 11px;
    }
    .drawer-search-input {
        width: 100%;
        height: 30px;
        padding-left: 28px;
        padding-right: 8px;
        font-size: 11px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        background: #ffffff;
        outline: none;
        color: #0f172a;
    }
    .drawer-search-input:focus {
        border-color: #002C54;
        box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.08);
    }

    /* Drawer Menu Body (Dynamic Modules List) */
    .drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 6px;
    }
    .drawer-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    /* Single / Parent Module Item */
    .mob-menu-item-wrap {
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid transparent;
        transition: all .12s ease;
    }
    .mob-menu-parent-btn, .mob-menu-direct-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 8px;
        border-radius: 4px;
        color: #1e293b;
        text-decoration: none !important;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        user-select: none;
        transition: all .12s ease;
        background: transparent;
    }
    .mob-menu-parent-btn:active, .mob-menu-direct-link:active {
        background: #f1f5f9;
        color: #002C54;
    }
    .mob-menu-parent-btn.active-parent, .mob-menu-direct-link.active {
        background: #e0f2fe;
        color: #0284c7;
        font-weight: 800;
    }
    .mob-menu-left {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow: hidden;
    }
    .mob-menu-icon {
        width: 26px;
        height: 26px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #f1f5f9;
        color: #002C54;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .mob-menu-parent-btn.active-parent .mob-menu-icon, .mob-menu-direct-link.active .mob-menu-icon {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }
    .mob-menu-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .mob-menu-right {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .mob-menu-badge {
        font-size: 8.5px;
        font-weight: 800;
        padding: 1px 5px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }
    .mob-menu-chevron {
        font-size: 10px;
        color: #94a3b8;
        transition: transform .2s ease;
    }
    .mob-menu-item-wrap.open .mob-menu-chevron {
        transform: rotate(90deg);
        color: #0284c7;
    }

    /* Submenu List */
    .mob-submenu-list {
        list-style: none;
        padding: 2px 0 4px 18px;
        margin: 0;
        display: none;
        border-left: 2px solid #e2e8f0;
        margin-left: 20px;
    }
    .mob-menu-item-wrap.open .mob-submenu-list {
        display: block;
    }
    .mob-sub-item-link {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        border-radius: 3px;
        color: #475569;
        text-decoration: none !important;
        font-size: 11px;
        font-weight: 600;
        transition: all .12s ease;
        margin-bottom: 1px;
    }
    .mob-sub-item-link:active {
        background: #f1f5f9;
        color: #002C54;
    }
    .mob-sub-item-link.active {
        background: #e0f2fe;
        color: #0284c7;
        font-weight: 800;
        border-left: 3px solid #0284c7;
    }
    .mob-sub-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #94a3b8;
        flex-shrink: 0;
    }
    .mob-sub-item-link.active .mob-sub-dot {
        background: #0284c7;
        transform: scale(1.3);
    }

    /* Drawer Footer */
    .drawer-footer {
        padding: 8px 10px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 5px;
        flex-shrink: 0;
    }
    .btn-switch-desktop {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px;
        font-size: 10.5px;
        font-weight: 700;
        color: #475569;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        text-decoration: none !important;
        transition: background .15s ease;
    }
    .btn-drawer-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px;
        font-size: 10.5px;
        font-weight: 800;
        color: #dc2626;
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 4px;
        text-decoration: none !important;
    }
    </style>

    @yield('styles')
</head>
<body>

<div class="mobile-app-shell">

    {{-- Top App Bar --}}
    <header class="mobile-top-bar">
        <a href="{{ url('dashboard') }}" class="top-bar-brand">
            @if(!empty($setting->left_logo))
                <img src="{{ asset($setting->left_logo) }}" alt="Logo" class="top-bar-logo">
            @else
                <div class="top-bar-logo" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 16px;">A</div>
            @endif
            <div class="top-bar-info">
                <span class="top-bar-name">{{ $setting->name ?? 'ARISE ERP' }}</span>
                <span class="top-bar-tag">
                    <i class="fa fa-map-marker"></i> {{ Session::get('branch_name') ?? 'Main Campus' }}
                </span>
            </div>
        </a>

        <div class="top-bar-actions">
            {{-- Notification Bell --}}
            <a href="{{ url('notice-management') }}" class="top-icon-pill" title="Notices">
                <i class="fa fa-bell-o"></i>
                @if($unreadNoticesCount > 0)
                    <span class="top-badge-pulse">{{ $unreadNoticesCount > 9 ? '9+' : $unreadNoticesCount }}</span>
                @endif
            </a>

            {{-- Quick Menu Trigger --}}
            <button type="button" class="top-icon-pill" id="btnOpenMobileDrawer" title="All Modules">
                <i class="fa fa-th-large"></i>
            </button>
        </div>
    </header>

    {{-- Main Page Content --}}
    <main class="mobile-app-main">
        @yield('content')
    </main>

    {{-- Floating Glassmorphic Bottom Dock Navigation --}}
    <nav class="mobile-bottom-dock">
        <a href="{{ url('dashboard') }}" class="dock-nav-item {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
            <div class="dock-nav-icon"><i class="fa fa-home"></i></div>
            <span>Home</span>
        </a>
        <a href="{{ url('admissionView') }}" class="dock-nav-item {{ request()->is('*admission*') || request()->is('*student*') ? 'active' : '' }}">
            <div class="dock-nav-icon"><i class="fa fa-graduation-cap"></i></div>
            <span>Students</span>
        </a>
        <a href="{{ url('feesCollectAdd') }}" class="dock-nav-item {{ request()->is('*fee*') ? 'active' : '' }}">
            <div class="dock-nav-icon"><i class="fa fa-inr"></i></div>
            <span>Collect</span>
        </a>
        <a href="{{ url('attendance/mark') }}" class="dock-nav-item {{ request()->is('*attendance*') ? 'active' : '' }}">
            <div class="dock-nav-icon"><i class="fa fa-calendar-check-o"></i></div>
            <span>Attendance</span>
        </a>
        <a href="javascript:void(0);" class="dock-nav-item" id="btnBottomMore">
            <div class="dock-nav-icon"><i class="fa fa-bars"></i></div>
            <span>Menu</span>
        </a>
    </nav>

</div>

{{-- Slide-over Mobile App Drawer (Populated with Desktop Sidebar Menus) --}}
<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop"></div>
<div class="mobile-drawer" id="mobileDrawer">
    
    {{-- Drawer Header / User Profile --}}
    <div class="drawer-header">
        <div class="drawer-avatar">
            @if(!empty($userImage) && file_exists(public_path($userImage)))
                <img src="{{ asset($userImage) }}" alt="Avatar">
            @else
                <i class="fa fa-user"></i>
            @endif
        </div>
        <div class="drawer-user-info">
            <span class="drawer-user-name">{{ $userFullName }}</span>
            <span class="drawer-user-role">
                <i class="fa fa-shield mr-1"></i>Administrator &bull; Session {{ Session::get('session_name') ?? date('Y') }}
            </span>
        </div>
        <button type="button" class="drawer-close-btn" id="btnCloseMobileDrawer">&times;</button>
    </div>

    {{-- Live Search Filter Box --}}
    <div class="drawer-search-wrap">
        <i class="fa fa-search"></i>
        <input type="text" id="mobDrawerSearchInput" class="drawer-search-input" placeholder="Search any module or menu...">
    </div>

    {{-- Drawer Navigation Body (Dynamic modules matching desktop sidebar) --}}
    <div class="drawer-body">
        <ul class="drawer-menu-list" id="mobDrawerMenuList">
            @foreach($sidebar as $data)
                @php
                    $submenus = Helper::getSubPermisn($data->id);
                    if ((int) $data->id === 17 || trim((string)($data->url ?? ''), '/') === 'settings_dashboard' || trim((string)($data->url ?? ''), '/') === 'viewSetting') {
                        $submenus = [];
                        $data->url = 'editSetting/1';
                    }
                    if ((int) Session::get('role_id') === 1 && (int) $data->id === 4) {
                        $attendanceSettingsId = $subSidebar
                            ->firstWhere('url', 'attendance/settings')
                            ->id ?? null;
                        $attendanceWindowId = $subSidebar
                            ->firstWhere('url', 'attendance/marking-window')
                            ->id ?? null;
                        if ($attendanceSettingsId && !in_array((string) $attendanceSettingsId, array_map('strval', $submenus), true)) {
                            $submenus[] = (string) $attendanceSettingsId;
                        }
                        if ($attendanceWindowId && !in_array((string) $attendanceWindowId, array_map('strval', $submenus), true)) {
                            $submenus[] = (string) $attendanceWindowId;
                        }
                    }
                    if ((int) Session::get('role_id') === 1 && (int) $data->id === 3) {
                        $studentLogsId = $subSidebar
                            ->firstWhere('url', 'student_logs')
                            ->id ?? null;
                        if ($studentLogsId && !in_array((string) $studentLogsId, array_map('strval', $submenus), true)) {
                            $submenus[] = (string) $studentLogsId;
                        }
                    }
                    if ((int) Session::get('role_id') === 1 && (int) $data->id === 9) {
                        $academicCalendarId = $subSidebar
                            ->firstWhere('url', 'add_weekend')
                            ->id ?? null;
                        if ($academicCalendarId && !in_array((string) $academicCalendarId, array_map('strval', $submenus), true)) {
                            $submenus[] = (string) $academicCalendarId;
                        }
                    }

                    $activeSub = false;
                    $validSubmenusCount = 0;
                    foreach($subSidebar as $sub){
                        if(in_array($sub->id, $submenus)){
                            $validSubmenusCount++;
                            if (url($sub->url) == URL::current() || (!empty($sub->url) && request()->is(trim($sub->url, '/').'*'))) {
                                $activeSub = true;
                            }
                        }
                    }
                    $isParentActive = (url($data->url) == URL::current()) || 
                                      (!empty($data->url) && request()->is(trim($data->url, '/').'*')) ||
                                      ((int) $data->id === 17 && (request()->is('editSetting*') || request()->is('settings*') || request()->is('viewSetting*')));

                    $displayName = (Session::get('locale') == 'hi' && !empty($data->hindi_name)) ? $data->hindi_name : ($data->name ?? '');
                    $hasSub = !empty($submenus) && $validSubmenusCount > 0;
                @endphp

                <li class="mob-menu-item-wrap {{ ($activeSub || $isParentActive) ? 'open' : '' }}" data-name="{{ strtolower($displayName) }}">
                    @if($hasSub)
                        {{-- Parent with Submenus (Accordion Trigger) --}}
                        <div class="mob-menu-parent-btn {{ $activeSub ? 'active-parent' : '' }}">
                            <div class="mob-menu-left">
                                <span class="mob-menu-icon"><i class="fa {{ $data->ican ?? 'fa-folder-o' }}"></i></span>
                                <span class="mob-menu-text">{{ $displayName }}</span>
                            </div>
                            <div class="mob-menu-right">
                                <span class="mob-menu-badge">{{ $validSubmenusCount }}</span>
                                <i class="fa fa-chevron-right mob-menu-chevron"></i>
                            </div>
                        </div>

                        {{-- Submenus List --}}
                        <ul class="mob-submenu-list">
                            @foreach($subSidebar as $sub)
                                @if(in_array($sub->id, $submenus))
                                    @php
                                        $isChildActive = (url($sub->url) == URL::current() || (!empty($sub->url) && request()->is(trim($sub->url, '/').'*')));
                                        $subDisplayName = (Session::get('locale') == 'hi' && !empty($sub->hindi_name)) ? $sub->hindi_name : ($sub->name ?? '');
                                    @endphp
                                    <li class="mob-sub-item-wrap" data-subname="{{ strtolower($subDisplayName) }}">
                                        <a href="{{ url($sub->url) }}" class="mob-sub-item-link {{ $isChildActive ? 'active' : '' }}">
                                            <span class="mob-sub-dot"></span>
                                            <span>{{ $subDisplayName }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        {{-- Direct Link Module (e.g. Dashboard) --}}
                        <a href="{{ url($data->url) }}" class="mob-menu-direct-link {{ $isParentActive ? 'active' : '' }}">
                            <div class="mob-menu-left">
                                <span class="mob-menu-icon"><i class="fa {{ $data->ican ?? 'fa-th-large' }}"></i></span>
                                <span class="mob-menu-text">{{ $displayName }}</span>
                            </div>
                        </a>
                    @endif
                </li>
            @endforeach

            {{-- Notice Management --}}
            @if((int) Session::get('role_id') !== 3)
            <li class="mob-menu-item-wrap" data-name="notice management">
                <a href="{{ url('notice-management') }}" class="mob-menu-direct-link {{ request()->is('notice-management*') ? 'active' : '' }}">
                    <div class="mob-menu-left">
                        <span class="mob-menu-icon" style="background: #fef9c3; color: #ca8a04;"><i class="fa fa-bullhorn"></i></span>
                        <span class="mob-menu-text">Notice Management</span>
                    </div>
                </a>
            </li>
            @endif

            {{-- Complaints Management --}}
            @if((int) Session::get('role_id') === 1)
            <li class="mob-menu-item-wrap" data-name="complaints management">
                <a href="{{ url('complaints-management') }}" class="mob-menu-direct-link {{ request()->is('complaints-management*') ? 'active' : '' }}">
                    <div class="mob-menu-left">
                        <span class="mob-menu-icon" style="background: #ffe4e6; color: #e11d48;"><i class="fa fa-comments-o"></i></span>
                        <span class="mob-menu-text">Complaints Management</span>
                    </div>
                </a>
            </li>
            @endif

        </ul>
    </div>

    {{-- Drawer Footer --}}
    <div class="drawer-footer">
        <a href="{{ url()->current() }}?layout=desktop" class="btn-switch-desktop">
            <i class="fa fa-desktop mr-1"></i> Switch to Desktop View
        </a>
        <a href="{{ url('logout') }}" class="btn-drawer-logout" onclick="return confirm('Are you sure you want to log out?')">
            <i class="fa fa-sign-out mr-1"></i> Log Out
        </a>
    </div>

</div>

<script>
$(document).ready(function() {
    function openDrawer() {
        $('#mobileDrawerBackdrop').addClass('show');
        $('#mobileDrawer').addClass('show');
        $('body').css('overflow', 'hidden');
    }
    function closeDrawer() {
        $('#mobileDrawerBackdrop').removeClass('show');
        $('#mobileDrawer').removeClass('show');
        $('body').css('overflow', '');
    }

    $('#btnOpenMobileDrawer, #btnBottomMore').on('click', openDrawer);
    $('#btnCloseMobileDrawer, #mobileDrawerBackdrop').on('click', closeDrawer);

    // Accordion Toggle for Parent Modules
    $('.mob-menu-parent-btn').on('click', function(e) {
        e.preventDefault();
        const $wrap = $(this).closest('.mob-menu-item-wrap');
        const isOpen = $wrap.hasClass('open');
        
        // Toggle current
        if (isOpen) {
            $wrap.removeClass('open');
        } else {
            $wrap.addClass('open');
        }
    });

    // Real-Time Search in Mobile Drawer
    $('#mobDrawerSearchInput').on('input', function() {
        const query = ($(this).val() || '').trim().toLowerCase();

        $('.mob-menu-item-wrap').each(function() {
            const parentName = $(this).data('name') || '';
            let hasMatch = (!query || parentName.indexOf(query) !== -1);

            // Also search inside submenus
            if (!hasMatch) {
                $(this).find('.mob-sub-item-wrap').each(function() {
                    const subName = $(this).data('subname') || '';
                    if (subName.indexOf(query) !== -1) {
                        hasMatch = true;
                    }
                });
            }

            if (hasMatch) {
                $(this).show();
                if (query) {
                    $(this).addClass('open'); // Auto-expand matching accordions
                }
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

@yield('scripts')
</body>
</html>