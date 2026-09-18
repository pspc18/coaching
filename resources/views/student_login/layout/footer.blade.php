@php
    $notificationCount = DB::table('notifications')
        ->where('admission_id', Session::get('id'))
        ->where('show_status', 1)
        ->count();
@endphp

<footer class="app-footer" aria-label="Student navigation">
    <nav class="footer-nav">
        <a href="{{ url('dashboard') }}" class="footer-link {{ request()->is('dashboard') ? 'active' : '' }}" @if(request()->is('dashboard')) aria-current="page" @endif>
            <span class="footer-icon"><i class="bi bi-house-door{{ request()->is('dashboard') ? '-fill' : '' }}"></i></span>
            <span class="footer-label">Home</span>
        </a>

        <a href="{{ url('profileStudent') }}" class="footer-link {{ request()->is('profileStudent*') ? 'active' : '' }}" @if(request()->is('profileStudent*')) aria-current="page" @endif>
            <span class="footer-icon"><i class="bi bi-person{{ request()->is('profileStudent*') ? '-fill' : '' }}"></i></span>
            <span class="footer-label">Profile</span>
        </a>

        <a href="{{ url('AttendanceView_student') }}" class="footer-link {{ request()->is('AttendanceView_student*') ? 'active' : '' }}" @if(request()->is('AttendanceView_student*')) aria-current="page" @endif>
            <span class="footer-icon"><i class="bi bi-calendar2-check{{ request()->is('AttendanceView_student*') ? '-fill' : '' }}"></i></span>
            <span class="footer-label">Attendance</span>
        </a>

        @php $alertsActive = request()->is('notificationFatchStudent*') || request()->is('notification_detail_stu*'); @endphp
        <a href="{{ url('notificationFatchStudent') }}" class="footer-link {{ $alertsActive ? 'active' : '' }}" @if($alertsActive) aria-current="page" @endif>
            <span class="footer-icon">
                <i class="bi bi-bell{{ $alertsActive ? '-fill' : '' }}"></i>
                @if($notificationCount > 0)
                    <span class="footer-badge" aria-label="{{ $notificationCount }} unread alerts">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                @endif
            </span>
            <span class="footer-label">Alerts</span>
        </a>
    </nav>
</footer>
