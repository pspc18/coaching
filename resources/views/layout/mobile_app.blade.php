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
    $userNotificationUnreadCount = 0;
    if ((int) Session::get('role_id') !== 3 && !empty(Session::get('id'))) {
        $userNotificationUnreadCount = DB::table('notifications')
            ->where('user_id', (int) Session::get('id'))
            ->where('branch_id', (int) Session::get('branch_id'))
            ->where('session_id', (int) Session::get('session_id'))
            ->where('show_status', 1)
            ->where('message_seen', 0)
            ->whereNull('deleted_at')
            ->count();
    }

    // Fetch dynamic sidebar permissions exactly like desktop layout/sidebar.blade.php
    $branch = \App\Models\Master\Branch::find(Session::get('branch_id'));
    $branchSidebarIds = !empty($branch->branch_sidebar_id) ? explode(',', $branch->branch_sidebar_id) : [];
    $Permisn = Helper::getPermisn();

    if ((int) Session::get('role_id') === 1) {
        // Admin: Show all modules and submenus for comprehensive review
        $sidebar = DB::table('sidebars')->orderBy('order_by','ASC')->get();
        $subSidebar = DB::table('sidebar_sub')->whereNull('deleted_at')->orderBy('orderBy','ASC')->get();
    } else {
        $sidebar = DB::table('sidebars')->whereNull('deleted_at')->whereIn('id', $Permisn)->orderBy('order_by','ASC')->get();
        $subSidebar = DB::table('sidebar_sub')->where('sub_sidebar','yes')->whereIn('sidebar_id', $branchSidebarIds)->whereNull('deleted_at')->orderBy('orderBy','ASC')->get();
    }

    if ((int) Session::get('role_id') === 1) {
        $sidebar = $sidebar->reject(function ($item) {
            return trim((string) ($item->url ?? ''), '/') === 'attendance/self';
        })->values();
        $subSidebar = $subSidebar->reject(function ($item) {
            return trim((string) ($item->url ?? ''), '/') === 'attendance/self';
        })->values();
    }

    $mobileLogoFile = $setting->left_logo ?? '';
    $mobileLogoUrl = !empty($mobileLogoFile)
        ? rtrim((string) env('IMAGE_SHOW_PATH'), '/') . '/setting/left_logo/' . rawurlencode($mobileLogoFile)
        : '';
    $firmInitial = strtoupper(mb_substr(trim($setting->name ?? 'A'), 0, 1, 'UTF-8')) ?: 'A';
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
    <link rel="icon" type="image/x-icon" href="{{ !empty($mobileLogoUrl) ? $mobileLogoUrl : asset('public/assets/school/img/logo.png') }}">
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
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background: #ffffff;
        padding: 2px;
        object-fit: contain;
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
        flex-shrink: 0;
        display: block;
    }
    .top-bar-avatar {
        background: linear-gradient(135deg, #0284c7 0%, #002C54 100%) !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 800 !important;
        font-size: 14px !important;
        text-transform: uppercase !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        padding: 0 !important;
        line-height: 1 !important;
        user-select: none !important;
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
    .top-icon-pill.active {
        background: rgba(56, 189, 248, 0.25);
        border-color: #38bdf8;
        color: #38bdf8 !important;
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

    /* Native Mobile Pull-To-Refresh Indicator */
    .mob-ptr-container {
        position: fixed;
        top: calc(var(--top-bar-height) + 6px);
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        pointer-events: none;
        z-index: 999;
        transform: translate3d(0, -60px, 0);
        opacity: 0;
        transition: transform .12s cubic-bezier(0.2, 0.8, 0.4, 1), opacity .15s ease;
        will-change: transform, opacity;
    }
    .mob-ptr-container.is-animating {
        transition: transform .28s cubic-bezier(0.2, 0.9, 0.3, 1), opacity .25s ease;
    }
    .mob-ptr-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #ffffff;
        color: #002C54;
        padding: 6px 13px;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 24, 51, 0.2), 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #cbd5e1;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.01em;
        user-select: none;
    }
    .mob-ptr-icon-wrap {
        width: 15px;
        height: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0284c7;
        font-size: 11.5px;
    }
    .mob-ptr-arrow {
        transition: transform .18s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
    }
    .mob-ptr-arrow.rotate-up {
        transform: rotate(180deg);
        color: #16a34a;
    }
    .mob-ptr-spinner {
        color: #0284c7;
        font-size: 12px;
    }
    .mob-ptr-text {
        line-height: 1.2;
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

    /* Global Native Mobile Confirmation Bottom Sheet (Slide-Up Modal) */
    .mob-confirm-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 15, 30, 0.72);
        z-index: 2200;
        opacity: 0;
        visibility: hidden;
        transition: all .2s ease-in-out;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .mob-confirm-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
    .mob-confirm-sheet {
        position: fixed;
        bottom: -380px;
        left: 8px;
        right: 8px;
        background: #ffffff;
        border-radius: 8px;
        padding: 14px 14px 16px;
        z-index: 2201;
        transition: bottom .24s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 -4px 25px rgba(0, 20, 40, 0.35);
        border: 1px solid #cbd5e1;
        max-width: 440px;
        margin: 0 auto;
    }
    .mob-confirm-sheet.show {
        bottom: calc(14px + env(safe-area-inset-bottom, 0px));
    }
    .mob-confirm-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #002C54;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .mob-confirm-desc {
        font-size: 11.5px;
        color: #475569;
        margin-bottom: 14px;
        line-height: 1.45;
    }
    .mob-confirm-buttons {
        display: flex;
        gap: 8px;
    }
    .mob-btn-cancel-dialog {
        flex: 1;
        height: 34px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: background .12s ease;
    }
    .mob-btn-cancel-dialog:active {
        background: #e2e8f0;
    }
    .mob-btn-execute-dialog {
        flex: 1.4;
        height: 34px;
        background: #0284c7;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        font-size: 11.5px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: opacity .12s ease;
    }
    .mob-btn-execute-dialog:active {
        opacity: 0.9;
    }
    .mob-btn-execute-dialog.btn-danger-confirm {
        background: #dc2626 !important;
    }
    </style>

    @yield('styles')
</head>
<body>

<div class="mobile-app-shell">

    {{-- Top App Bar --}}
    <header class="mobile-top-bar">
        <a href="{{ url('dashboard') }}" class="top-bar-brand">
            @if(!empty($mobileLogoUrl))
                <img 
                    src="{{ $mobileLogoUrl }}" 
                    alt="Logo" 
                    class="top-bar-logo"
                    onerror="this.style.display='none'; var av=document.getElementById('mobileTopBarAvatar'); if(av){av.style.display='flex';}"
                >
                <div id="mobileTopBarAvatar" class="top-bar-logo top-bar-avatar" style="display: none;">
                    {{ $firmInitial }}
                </div>
            @else
                <div id="mobileTopBarAvatar" class="top-bar-logo top-bar-avatar">
                    {{ $firmInitial }}
                </div>
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
            <a href="{{ url('user-notifications') }}" class="top-icon-pill {{ request()->is('user-notifications*') ? 'active' : '' }}" title="Notifications">
                <i class="fa fa-bell-o"></i>
                @if($userNotificationUnreadCount > 0)
                    <span class="top-badge-pulse">{{ $userNotificationUnreadCount > 9 ? '9+' : $userNotificationUnreadCount }}</span>
                @endif
            </a>

            {{-- Quick Menu Trigger --}}
            <button type="button" class="top-icon-pill" id="btnOpenMobileDrawer" title="All Modules">
                <i class="fa fa-th-large"></i>
            </button>
        </div>
    </header>

    {{-- Global Mobile Native Pull-To-Refresh Indicator --}}
    <div id="mobPullToRefresh" class="mob-ptr-container" aria-hidden="true">
        <div class="mob-ptr-pill">
            <div class="mob-ptr-icon-wrap">
                <i class="fa fa-arrow-down mob-ptr-arrow" id="mobPtrArrow"></i>
                <i class="fa fa-circle-o-notch fa-spin mob-ptr-spinner" id="mobPtrSpinner" style="display: none;"></i>
            </div>
            <span class="mob-ptr-text" id="mobPtrText">Pull down to refresh</span>
        </div>
    </div>

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
                    if ((int) Session::get('role_id') === 1) {
                        $submenus = $subSidebar->where('sidebar_id', $data->id)->pluck('id')->map(fn($id) => (string) $id)->toArray();
                    } else {
                        $submenus = array_map('strval', Helper::getSubPermisn($data->id));
                    }

                    if ((int) $data->id === 17 || trim((string)($data->url ?? ''), '/') === 'settings_dashboard' || trim((string)($data->url ?? ''), '/') === 'viewSetting') {
                        $submenus = [];
                        $data->url = 'editSetting/1';
                    }

                    $activeSub = false;
                    $validSubmenusCount = 0;
                    foreach($subSidebar as $sub){
                        if(in_array((string) $sub->id, array_map('strval', $submenus), true)){
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
                                @if(in_array((string) $sub->id, array_map('strval', $submenus), true))
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

            {{-- User Notifications --}}
            @if((int) Session::get('role_id') !== 3)
            <li class="mob-menu-item-wrap" data-name="notifications user notifications">
                <a href="{{ url('user-notifications') }}" class="mob-menu-direct-link {{ request()->is('user-notifications*') ? 'active' : '' }}">
                    <div class="mob-menu-left">
                        <span class="mob-menu-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa fa-bell-o"></i></span>
                        <span class="mob-menu-text">Notifications</span>
                    </div>
                    @if($userNotificationUnreadCount > 0)
                        <span class="badge badge-danger" style="font-size: 10px; border-radius: 10px; padding: 2px 6px;">{{ $userNotificationUnreadCount > 99 ? '99+' : $userNotificationUnreadCount }}</span>
                    @endif
                </a>
            </li>
            @endif

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

    // Firm Logo Corrupted / Broken Image Fallback
    var $topLogo = $('img.top-bar-logo');
    if ($topLogo.length) {
        $topLogo.on('error', function(){
            $(this).hide();
            $('#mobileTopBarAvatar').css('display', 'flex');
        });
        if ($topLogo[0].complete && ($topLogo[0].naturalWidth === 0 || $topLogo[0].naturalHeight === 0)) {
            $topLogo.hide();
            $('#mobileTopBarAvatar').css('display', 'flex');
        }
    }

    // =========================================================================
    // GLOBAL NATIVE MOBILE PULL-TO-REFRESH (PTR) ENGINE
    // =========================================================================
    (function initMobilePullToRefresh() {
        const ptrContainer = document.getElementById('mobPullToRefresh');
        const ptrArrow = document.getElementById('mobPtrArrow');
        const ptrSpinner = document.getElementById('mobPtrSpinner');
        const ptrText = document.getElementById('mobPtrText');
        if (!ptrContainer || !ptrArrow || !ptrSpinner || !ptrText) return;

        let startY = 0;
        let startX = 0;
        let diffY = 0;
        let isPulling = false;
        let isRefreshing = false;
        let canPull = false;
        const PULL_THRESHOLD = 68;
        const MAX_PULL = 90;

        function isAtPageTop() {
            return (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0) <= 2;
        }

        function isInsideExcludedElement(target) {
            if (!target) return false;
            if (target.closest && (
                target.closest('#mobileDrawer') ||
                target.closest('.modal') ||
                target.closest('.dropdown-menu') ||
                target.closest('input, textarea, select, button, [contenteditable="true"]')
            )) {
                return true;
            }
            if ($('#mobileDrawer').hasClass('show') || $('.modal.show, .modal.in').length > 0) {
                return true;
            }
            return false;
        }

        window.addEventListener('touchstart', function(e) {
            if (isRefreshing || e.touches.length !== 1) return;
            if (!isAtPageTop() || isInsideExcludedElement(e.target)) {
                canPull = false;
                return;
            }

            startY = e.touches[0].clientY;
            startX = e.touches[0].clientX;
            diffY = 0;
            isPulling = false;
            canPull = true;
            ptrContainer.classList.remove('is-animating');
        }, { passive: true });

        window.addEventListener('touchmove', function(e) {
            if (!canPull || isRefreshing || e.touches.length !== 1) return;

            const currentY = e.touches[0].clientY;
            const currentX = e.touches[0].clientX;
            const deltaY = currentY - startY;
            const deltaX = currentX - startX;

            if (deltaY <= 0 || !isAtPageTop()) {
                canPull = false;
                if (isPulling) resetPTR(true);
                return;
            }

            if (Math.abs(deltaX) > deltaY) {
                canPull = false;
                if (isPulling) resetPTR(true);
                return;
            }

            if (deltaY > 6) {
                isPulling = true;
                if (e.cancelable) {
                    e.preventDefault();
                }

                const pullDistance = Math.min(deltaY * 0.42, MAX_PULL);
                diffY = pullDistance;

                const translateY = Math.max(0, pullDistance);
                ptrContainer.style.transform = 'translate3d(0, ' + translateY + 'px, 0)';
                ptrContainer.style.opacity = Math.min(pullDistance / 24, 1);

                if (pullDistance >= PULL_THRESHOLD) {
                    ptrArrow.classList.add('rotate-up');
                    ptrText.textContent = 'Release to refresh';
                } else {
                    ptrArrow.classList.remove('rotate-up');
                    ptrText.textContent = 'Pull down to refresh';
                }
            }
        }, { passive: false });

        function resetPTR(animate) {
            canPull = false;
            isPulling = false;
            isRefreshing = false;
            if (animate) ptrContainer.classList.add('is-animating');
            ptrContainer.style.transform = 'translate3d(0, -60px, 0)';
            ptrContainer.style.opacity = '0';
            setTimeout(function() {
                ptrArrow.style.display = 'inline-block';
                ptrArrow.classList.remove('rotate-up');
                ptrSpinner.style.display = 'none';
                ptrText.textContent = 'Pull down to refresh';
                ptrContainer.classList.remove('is-animating');
            }, 300);
        }

        window.addEventListener('touchend', function(e) {
            if (!canPull || !isPulling || isRefreshing) {
                canPull = false;
                isPulling = false;
                return;
            }

            if (diffY >= PULL_THRESHOLD) {
                isRefreshing = true;
                ptrContainer.classList.add('is-animating');
                ptrContainer.style.transform = 'translate3d(0, 48px, 0)';
                ptrContainer.style.opacity = '1';

                ptrArrow.style.display = 'none';
                ptrSpinner.style.display = 'inline-block';
                ptrText.textContent = 'Refreshing...';

                if (navigator.vibrate) {
                    try { navigator.vibrate(25); } catch(err) {}
                }

                if (typeof window.mobPullToRefreshHandler === 'function') {
                    try {
                        window.mobPullToRefreshHandler(function done() {
                            resetPTR(true);
                        });
                    } catch(err) {
                        console.error('Custom PTR error:', err);
                        window.location.reload();
                    }
                } else {
                    setTimeout(function() {
                        window.location.reload();
                    }, 350);
                }
            } else {
                resetPTR(true);
            }
        }, { passive: true });

        window.addEventListener('touchcancel', function() {
            if (isPulling && !isRefreshing) {
                resetPTR(true);
            }
        }, { passive: true });
    })();

    // Global Native Mobile Slide-Up Confirmation Modal
    window._mobOnConfirmCallback = null;
    window.showMobileConfirm = function(title, message, callback, okText, isDanger, iconClass) {
        okText = okText || 'Confirm';
        isDanger = (typeof isDanger === 'undefined') ? false : !!isDanger;
        iconClass = iconClass || 'fa-question-circle text-primary';
        window._mobOnConfirmCallback = callback;

        $('#mobGlobalConfirmTitle span').text(title);
        $('#mobGlobalConfirmTitle i').attr('class', 'fa ' + iconClass);
        $('#mobGlobalConfirmDesc').html(message);
        $('#mobGlobalBtnExecuteConfirm').html(okText);

        if (isDanger) {
            $('#mobGlobalBtnExecuteConfirm').addClass('btn-danger-confirm');
        } else {
            $('#mobGlobalBtnExecuteConfirm').removeClass('btn-danger-confirm');
        }

        $('#mobGlobalConfirmBackdrop').addClass('show');
        $('#mobGlobalConfirmSheet').addClass('show');
    };

    window.closeMobileConfirm = function() {
        $('#mobGlobalConfirmBackdrop').removeClass('show');
        $('#mobGlobalConfirmSheet').removeClass('show');
        window._mobOnConfirmCallback = null;
    };

    $(document).on('click', '#mobGlobalBtnCancelConfirm, #mobGlobalConfirmBackdrop', function() {
        window.closeMobileConfirm();
    });

    $(document).on('click', '#mobGlobalBtnExecuteConfirm', function() {
        var cb = window._mobOnConfirmCallback;
        window.closeMobileConfirm();
        if (typeof cb === 'function') {
            cb();
        }
    });
});
</script>

{{-- Global Native Mobile Confirmation Bottom Sheet Modal --}}
<div class="mob-confirm-backdrop" id="mobGlobalConfirmBackdrop"></div>
<div class="mob-confirm-sheet" id="mobGlobalConfirmSheet">
    <div class="mob-confirm-title" id="mobGlobalConfirmTitle">
        <i class="fa fa-question-circle text-primary"></i> <span>Confirm Action</span>
    </div>
    <div class="mob-confirm-desc" id="mobGlobalConfirmDesc">
        Are you sure you want to proceed?
    </div>
    <div class="mob-confirm-buttons">
        <button type="button" class="mob-btn-cancel-dialog" id="mobGlobalBtnCancelConfirm">Cancel</button>
        <button type="button" class="mob-btn-execute-dialog" id="mobGlobalBtnExecuteConfirm">Confirm</button>
    </div>
</div>

@yield('scripts')
</body>
</html>