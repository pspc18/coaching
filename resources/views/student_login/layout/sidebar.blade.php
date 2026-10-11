@php
    $sidebarStudent = $getUser ?? Helper::getUser();
    $sidebarName = trim(($sidebarStudent->first_name ?? '') . ' ' . ($sidebarStudent->last_name ?? ''));
    if (empty($sidebarName)) $sidebarName = 'Student';
    $sidebarClass = optional($sidebarStudent)->ClassTypes->name ?? '-';
    $sidebarAdmissionNo = $sidebarStudent->admissionNo ?? '-';
    $sidebarAvatar = !empty($sidebarStudent->image)
        ? env('IMAGE_SHOW_PATH') . '/profile/' . $sidebarStudent->image
        : null;

    $studentMenuItems = [
        ['name' => 'Dashboard', 'url' => 'dashboard', 'icon' => 'fa-home', 'color' => '#0284c7'],
        ['name' => 'Attendance', 'url' => 'AttendanceView_student', 'icon' => 'fa-calendar-check-o', 'color' => '#0284c7'],
        ['name' => 'Fees History', 'url' => 'fees_history', 'icon' => 'fa-inr', 'color' => '#10b981'],
        ['name' => 'Homework', 'url' => 'student_homework', 'icon' => 'fa-book', 'color' => '#f43f5e'],
        ['name' => 'Exam Results', 'url' => 'student/result-card', 'icon' => 'fa-file-text-o', 'color' => '#8b5cf6'],
        ['name' => 'Notices & Alerts', 'url' => 'student-notices', 'icon' => 'fa-bullhorn', 'color' => '#f59e0b'],
        ['name' => 'Download Center', 'url' => 'student_download_center', 'icon' => 'fa-download', 'color' => '#eab308'],
        ['name' => 'Complaints / Support', 'url' => 'student-complaints', 'icon' => 'fa-comments-o', 'color' => '#64748b'],
        ['name' => 'My Teachers', 'url' => 'my_teachers', 'icon' => 'fa-users', 'color' => '#0284c7'],
        ['name' => 'Subjects', 'url' => 'student_subject_view', 'icon' => 'fa-list-alt', 'color' => '#8b5cf6'],
        ['name' => 'Rules & Guidelines', 'url' => 'rule_view', 'icon' => 'fa-shield', 'color' => '#10b981'],
        ['name' => 'Gate Pass', 'url' => 'student_gate_pass_view', 'icon' => 'fa-sign-out', 'color' => '#f59e0b'],
        ['name' => 'Photo Gallery', 'url' => 'gallery_view_student', 'icon' => 'fa-picture-o', 'color' => '#6366f1'],
        ['name' => 'My ID Card', 'url' => 'my_id_card', 'icon' => 'fa-id-card-o', 'color' => '#0284c7'],
        ['name' => 'Profile & Password', 'url' => 'profileStudent', 'icon' => 'fa-user-circle', 'color' => '#64748b'],
    ];
@endphp

{{-- Backdrop for Drawer --}}
<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop"></div>

{{-- Slide-over Mobile App Drawer (Matching Admin Panel Mobile Drawer) --}}
<div class="mobile-drawer" id="mobileDrawer">
    
    {{-- Drawer Header / Student Profile --}}
    <div class="drawer-header">
        <div class="drawer-avatar">
            @if(!empty($sidebarAvatar))
                <img src="{{ $sidebarAvatar }}" alt="Student Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">
            @else
                <i class="fa fa-user"></i>
            @endif
        </div>
        <div class="drawer-user-info">
            <span class="drawer-user-name">{{ $sidebarName }}</span>
            <span class="drawer-user-role">
                <i class="fa fa-graduation-cap mr-1"></i>{{ $sidebarClass }} &bull; Adm #{{ $sidebarAdmissionNo }}
            </span>
        </div>
        <button type="button" class="drawer-close-btn" id="btnCloseMobileDrawer">&times;</button>
    </div>

    {{-- Live Search Filter Box --}}
    <div class="drawer-search-wrap">
        <i class="fa fa-search"></i>
        <input type="text" id="mobDrawerSearchInput" class="drawer-search-input" placeholder="Search student modules...">
    </div>

    {{-- Drawer Navigation Body --}}
    <div class="drawer-body">
        <ul class="drawer-menu-list" id="mobDrawerMenuList">
            @foreach($studentMenuItems as $item)
                @php
                    $isItemActive = (url($item['url']) == URL::current()) || 
                                    (!empty($item['url']) && request()->is(trim($item['url'], '/').'*'));
                @endphp
                <li class="mob-menu-item-wrap" data-name="{{ strtolower($item['name']) }}">
                    <a href="{{ url($item['url']) }}" class="mob-menu-direct-link {{ $isItemActive ? 'active' : '' }}">
                        <div class="mob-menu-left">
                            <span class="mob-menu-icon" style="background: {{ $item['color'] }}15; color: {{ $item['color'] }};">
                                <i class="fa {{ $item['icon'] }}"></i>
                            </span>
                            <span class="mob-menu-text">{{ $item['name'] }}</span>
                        </div>
                        <i class="fa fa-chevron-right mob-menu-chevron"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Drawer Bottom Fast Actions --}}
    <div class="drawer-footer">
        <a href="{{ url('profileStudent') }}" class="btn-drawer-profile">
            <i class="fa fa-user-circle"></i> Profile
        </a>
        @if(count($siblingsList ?? []) > 0)
        <button type="button" class="btn-drawer-profile" id="btnDrawerSwitch">
            <i class="fa fa-users"></i> Switch
        </button>
        @endif
        <a href="{{ url('logout') }}" class="btn-drawer-logout" onclick="event.preventDefault(); document.getElementById('studentLogoutForm').submit();">
            <i class="fa fa-power-off"></i> Logout
        </a>
        <form id="studentLogoutForm" action="{{ url('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<style>
/* ==========================================================================
   SLIDE-OVER MOBILE DRAWER (MATCHING ADMIN MOBILE APP DRAWER)
   ========================================================================== */
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
.drawer-user-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
    font-size: 16px;
    cursor: pointer;
}

/* Search Bar */
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

/* Menu Body */
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
.mob-menu-item-wrap {
    border-radius: 4px;
    overflow: hidden;
    transition: all .12s ease;
}
.mob-menu-direct-link {
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
.mob-menu-direct-link:active {
    background: #f1f5f9;
    color: #002C54;
}
.mob-menu-direct-link.active {
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
    flex-shrink: 0;
}
.mob-menu-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 11.5px;
}
.mob-menu-chevron {
    font-size: 10px;
    color: #94a3b8;
}

/* Drawer Footer */
.drawer-footer {
    padding: 8px 10px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}
.btn-drawer-profile {
    flex: 1;
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
    cursor: pointer;
}
.btn-drawer-logout {
    flex: 1;
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
    cursor: pointer;
}
</style>
