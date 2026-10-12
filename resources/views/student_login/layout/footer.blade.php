@php
    $dockAlertsCount = $unreadAlerts ?? ($notificationCount ?? 0);
@endphp

<nav class="mobile-bottom-dock">
    {{-- 1. Home --}}
    <a href="{{ url('dashboard') }}" class="dock-nav-item {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
        <div class="dock-nav-icon"><i class="fa fa-home"></i></div>
        <span>Home</span>
    </a>

    {{-- 2. Attendance --}}
    <a href="{{ url('AttendanceView_student') }}" class="dock-nav-item {{ request()->is('*Attendance*') || request()->is('*attendance*') ? 'active' : '' }}">
        <div class="dock-nav-icon"><i class="fa fa-calendar-check-o"></i></div>
        <span>Attendance</span>
    </a>

    {{-- 3. Fees --}}
    <a href="{{ url('fees_history') }}" class="dock-nav-item {{ request()->is('*fee*') ? 'active' : '' }}">
        <div class="dock-nav-icon"><i class="fa fa-inr"></i></div>
        <span>Fees</span>
    </a>

    {{-- 4. Alerts --}}
    <a href="{{ url('notificationFatchStudent') }}" class="dock-nav-item {{ request()->is('notificationFatchStudent*') ? 'active' : '' }}">
        <div class="dock-nav-icon">
            <i class="fa fa-bell-o"></i>
            <span class="dock-badge-pulse" id="dockAlertsBadge" style="{{ $dockAlertsCount > 0 ? '' : 'display:none;' }}">{{ $dockAlertsCount > 9 ? '9+' : $dockAlertsCount }}</span>
        </div>
        <span>Alerts</span>
    </a>

    {{-- 5. Slide-over Menu Trigger --}}
    <a href="javascript:void(0);" class="dock-nav-item" id="btnBottomMore">
        <div class="dock-nav-icon"><i class="fa fa-bars"></i></div>
        <span>Menu</span>
    </a>
</nav>

<style>
/* ==========================================================================
   FLOATING BOTTOM DOCK NAVIGATION (MATCHING ADMIN MOBILE VIEW)
   ========================================================================== */
.mobile-bottom-dock {
    position: fixed;
    bottom: calc(6px + var(--safe-bottom));
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 16px);
    max-width: 464px;
    height: var(--bottom-nav-height);
    background: rgba(0, 20, 40, 0.95);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(56, 189, 248, 0.25);
    border-radius: 6px;
    box-shadow: 0 4px 22px rgba(0, 10, 25, 0.45);
    display: flex;
    align-items: center;
    justify-content: space-around;
    padding: 0 4px;
    z-index: 1000;
}
.dock-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    text-decoration: none !important;
    color: #94a3b8 !important;
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.01em;
    padding: 3px 0;
    transition: all .15s ease;
    user-select: none;
}
.dock-nav-icon {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: inherit;
    position: relative;
    transition: all .15s ease;
}
.dock-nav-item.active {
    color: #38bdf8 !important;
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
.dock-badge-pulse {
    position: absolute;
    top: 1px;
    right: 1px;
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
