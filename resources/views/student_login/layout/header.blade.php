@php
    $headerSetting = $getSetting ?? Helper::getSetting();
    $headerStudent = $getUser ?? Helper::getUser();
    $headerName = trim(($headerStudent->first_name ?? '').' '.($headerStudent->last_name ?? ''));
    if (empty($headerName)) $headerName = 'Student';
    $headerClassName = optional($headerStudent)->ClassTypes->name ?? '-';
    $headerSession = Session::get('session_name') ?? date('Y');
    
    $headerLogoFile = $headerSetting->left_logo ?? '';
    $headerLogoUrl = !empty($headerLogoFile)
        ? rtrim((string) env('IMAGE_SHOW_PATH'), '/') . '/setting/left_logo/' . rawurlencode($headerLogoFile)
        : '';
    $headerFirmInitial = strtoupper(mb_substr(trim($headerSetting->name ?? 'A'), 0, 1, 'UTF-8')) ?: 'A';

    $alertsCount = $unreadAlerts ?? ($notificationCount ?? 0);
    $hasSiblings = count($siblingsList ?? []) > 0;
@endphp

<header class="mobile-top-bar">
    {{-- Brand: Logo + School Name + Student Class --}}
    <a href="{{ url('dashboard') }}" class="top-bar-brand">
        @if(!empty($headerLogoUrl))
            <img 
                src="{{ $headerLogoUrl }}" 
                alt="Logo" 
                class="top-bar-logo"
                onerror="this.style.display='none'; var av=document.getElementById('studentTopBarAvatar'); if(av){av.style.display='flex';}"
            >
            <div id="studentTopBarAvatar" class="top-bar-logo top-bar-avatar" style="display: none;">
                {{ $headerFirmInitial }}
            </div>
        @else
            <div id="studentTopBarAvatar" class="top-bar-logo top-bar-avatar">
                {{ $headerFirmInitial }}
            </div>
        @endif
        <div class="top-bar-info">
            <span class="top-bar-name">{{ $headerSetting->name ?? 'ARISE ERP' }}</span>
            <span class="top-bar-tag">
                <i class="fa fa-graduation-cap"></i> {{ $headerClassName }} &bull; {{ $headerSession }}
            </span>
        </div>
    </a>

    {{-- Top Action Buttons --}}
    <div class="top-bar-actions">
        {{-- Hard Refresh --}}
        <button type="button" class="top-icon-pill" id="hardRefreshBtn" title="Refresh App">
            <i class="fa fa-refresh"></i>
        </button>

        {{-- Alerts / Notifications --}}
        <a href="{{ url('notificationFatchStudent') }}" class="top-icon-pill {{ request()->is('notificationFatchStudent*') || request()->is('notification_detail_stu*') ? 'active' : '' }}" title="Notifications">
            <i class="fa fa-bell-o"></i>
            @if($alertsCount > 0)
                <span class="top-badge-pulse">{{ $alertsCount > 9 ? '9+' : $alertsCount }}</span>
            @endif
        </a>

        {{-- Sibling Switch (if available) --}}
        @if($hasSiblings)
        <button type="button" class="top-icon-pill" id="openSwitchSheet" title="Switch Student Profile">
            <i class="fa fa-users"></i>
        </button>
        @endif

        {{-- Mobile Drawer Trigger --}}
        <button type="button" class="top-icon-pill" id="btnOpenMobileDrawer" title="All Modules">
            <i class="fa fa-th-large"></i>
        </button>
    </div>
</header>

<style>
/* ==========================================================================
   TOP BAR COMPONENT (GLASSMORPHIC DARK NAVY - MATCHING ADMIN MOBILE)
   ========================================================================== */
.mobile-top-bar {
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
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
    min-width: 0;
    flex: 1;
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
    min-width: 0;
    overflow: hidden;
}
.top-bar-name {
    font-size: 12.5px;
    font-weight: 800;
    line-height: 1.15;
    color: #ffffff;
    letter-spacing: -0.01em;
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.top-bar-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
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
    padding: 0;
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
</style>
