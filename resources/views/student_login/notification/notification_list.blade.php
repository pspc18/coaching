@php
    $getUser = Helper::getUser();
    $category = strtolower(trim((string) ($category ?? 'all')));
    $allowedCategories = ['all', 'attendance', 'fees', 'result', 'notice', 'complaint'];
    if (!in_array($category, $allowedCategories, true)) {
        $category = 'all';
    }

    $pageLabel = 'Notifications';
    $selectedCategoryLabel = [
        'all' => 'All',
        'attendance' => 'Attendance',
        'fees' => 'Fees',
        'result' => 'Results',
        'notice' => 'Notices',
        'complaint' => 'Complaints',
    ][$category] ?? 'All';

    $categoryMeta = [
        'all' => ['label' => 'All', 'icon' => 'bi-grid-3x3-gap-fill'],
        'attendance' => ['label' => 'Attendance', 'icon' => 'bi-calendar2-check'],
        'fees' => ['label' => 'Fees', 'icon' => 'bi-wallet2'],
        'result' => ['label' => 'Results', 'icon' => 'bi-file-earmark-bar-graph'],
        'notice' => ['label' => 'Notices', 'icon' => 'bi-megaphone'],
        'complaint' => ['label' => 'Complaints', 'icon' => 'bi-chat-square-text'],
    ];

    $buildUrl = function (array $params) {
        $params = array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });

        return url('notificationFatchStudent'.(count($params) ? '?'.http_build_query($params) : ''));
    };
@endphp
@extends('student_login.layout.app')
@section('title', $pageLabel)
@section('page_title', strtoupper($pageLabel))
@section('page_sub', Session::get('first_name').' Â· '.(optional($getUser->ClassTypes)->name ?? 'Student'))

@section('content')
<section class="student-notification-app">
    <div class="notification-hero">
        <div class="notification-hero-pattern"></div>
        <div class="notification-hero-copy">
            <span>{{ $category === 'all' ? 'Notification center' : $selectedCategoryLabel.' updates' }}</span>
            <h1>{{ $pageLabel }}</h1>
            <p>{{ $unreadCount > 0 ? 'You have '.$unreadCount.' unread '.strtolower($selectedCategoryLabel).' '.($unreadCount === 1 ? 'update' : 'updates').'.' : 'You are all caught up.' }}</p>
        </div>
        <div class="notification-hero-icon">
            <i class="bi {{ $category === 'notice' ? 'bi-megaphone-fill' : 'bi-bell-fill' }}"></i>
            @if($unreadCount > 0)
                <span id="notificationHeroCount">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </div>
        <div class="notification-stats">
            <div><span>Total</span><strong>{{ $notifications->total() }}</strong></div>
            <div><span>Unread</span><strong id="notificationUnreadStat">{{ $unreadCount }}</strong></div>
            <div><span>Scope</span><strong>{{ $selectedCategoryLabel }}</strong></div>
        </div>
    </div>

    <div class="notification-content">
        @if(session('message'))
            <div class="notification-flash"><i class="bi bi-check-circle-fill"></i><span>{{ session('message') }}</span></div>
        @endif

        <div class="notification-toolbar">
            <div class="notification-quick-filters" aria-label="Quick filters">
                @foreach($categoryMeta as $key => $meta)
                    @php
                        $query = ['category' => $key];
                        if ($filter !== 'all') {
                            $query['filter'] = $filter;
                        }
                        $count = (int) data_get($categoryCounts ?? [], $key, 0);
                    @endphp
                    <a href="{{ $buildUrl($query) }}" class="{{ $category === $key ? 'active' : '' }}" @if($category === $key) aria-current="page" @endif>
                        <i class="bi {{ $meta['icon'] }}"></i>
                        {{ $meta['label'] }}
                        @if($count > 0)<span>{{ $count > 99 ? '99+' : $count }}</span>@endif
                    </a>
                @endforeach
            </div>

            <div class="notification-filters" aria-label="Filter notifications">
                @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $key => $label)
                    @php
                        $query = ['filter' => $key];
                        if ($category !== 'all') {
                            $query['category'] = $category;
                        }
                        $count = $key === 'unread' ? $unreadCount : null;
                    @endphp
                    <a href="{{ $buildUrl($query) }}" class="{{ $filter === $key ? 'active' : '' }}" @if($filter === $key) aria-current="page" @endif>
                        @if($key === 'all')
                            <i class="bi bi-inboxes"></i>
                        @elseif($key === 'unread')
                            <i class="bi bi-envelope"></i>
                        @else
                            <i class="bi bi-envelope-open"></i>
                        @endif
                        {{ $label }}
                        @if($key === 'unread' && $count > 0)<span id="filterUnreadCount">{{ $count }}</span>@endif
                    </a>
                @endforeach
            </div>

            <div class="notification-bulk-actions">
                <form method="POST" action="{{ route('student.attendance-notifications.mark-all-read') }}">
                    @csrf
                    @if($category !== 'all')<input type="hidden" name="category" value="{{ $category }}">@endif
                    <button type="submit" class="bulk-button" {{ $unreadCount === 0 ? 'disabled' : '' }}><i class="bi bi-check2-all"></i><span>Read all</span></button>
                </form>
                <form method="POST" action="{{ route('student.attendance-notifications.clear-all') }}" id="clearNotificationsForm">
                    @csrf
                    @method('DELETE')
                    @if($category !== 'all')<input type="hidden" name="category" value="{{ $category }}">@endif
                    <button type="submit" class="bulk-button danger" {{ $notifications->total() === 0 ? 'disabled' : '' }}><i class="bi bi-trash3"></i><span>Clear</span></button>
                </form>
            </div>
        </div>

        <div class="notification-list">
            @forelse($notifications as $notification)
                @php
                    $isUnread = (int) $notification->message_seen === 0;
                    $content = trim(strip_tags((string) ($notification->content ?? '')));
                    $isLong = mb_strlen($content) > 125 || substr_count($content, "\n") > 2;
                    $notificationType = strtolower((string) ($notification->type ?? ''));
                    $typeConfig = [
                        'notice' => ['icon' => 'bi-megaphone', 'label' => 'Notice', 'tone' => 'amber'],
                        'exam_result' => ['icon' => 'bi-file-earmark-bar-graph', 'label' => 'Result', 'tone' => 'purple'],
                        'fee_payment' => ['icon' => 'bi-wallet2', 'label' => 'Payment', 'tone' => 'green'],
                        'fee_revert' => ['icon' => 'bi-arrow-counterclockwise', 'label' => 'Fee update', 'tone' => 'red'],
                        'complaint_reply' => ['icon' => 'bi-chat-square-text', 'label' => 'Reply', 'tone' => 'blue'],
                        'complaint_status' => ['icon' => 'bi-chat-square-check', 'label' => 'Complaint', 'tone' => 'blue'],
                    ];
                    $config = $typeConfig[$notificationType] ?? ['icon' => 'bi-calendar2-check', 'label' => 'Attendance', 'tone' => 'blue'];
                    $createdAt = \Carbon\Carbon::parse($notification->created_at);
                @endphp

                <article class="notification-card {{ $isUnread ? 'is-unread' : '' }}" id="notification-{{ $notification->id }}">
                    <div class="notification-card-accent tone-{{ $config['tone'] }}"></div>
                    <div class="notification-card-head">
                        <span class="notification-card-icon tone-{{ $config['tone'] }}"><i class="bi {{ $config['icon'] }}"></i></span>
                        <div class="notification-title-wrap">
                            <div class="notification-title-line">
                                <h2>{{ $notification->title ?: 'Notification' }}</h2>
                                @if($isUnread)<span class="unread-dot" aria-label="Unread"></span>@endif
                            </div>
                            <div class="notification-meta">
                                <span class="notification-type">{{ $config['label'] }}</span>
                                <span><i class="bi bi-clock"></i> {{ $createdAt->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="read-status {{ $isUnread ? 'unread' : '' }}">{{ $isUnread ? 'New' : 'Read' }}</span>
                    </div>

                    <div class="notification-message {{ $isLong ? 'is-collapsed' : '' }}" id="message-{{ $notification->id }}">
                        {{ $content !== '' ? $content : 'No notification message available.' }}
                    </div>

                    @if($notificationType === 'notice' && $notification->managed_notice_id && $notification->attachment_path)
                        <a href="{{ url('notice-management/'.$notification->managed_notice_id.'/attachment') }}" class="notification-attachment">
                            <span><i class="bi bi-file-earmark-pdf-fill"></i></span>
                            <div><strong>{{ $notification->attachment_name ?: 'Notice attachment.pdf' }}</strong><small>Open PDF attachment</small></div>
                            <i class="bi bi-download"></i>
                        </a>
                    @endif

                    <div class="notification-card-footer">
                        <span><i class="bi bi-calendar3"></i> {{ $createdAt->format('d M Y Â· h:i A') }}</span>
                        <div class="notification-card-actions">
                            @if($isLong)
                                <button type="button" class="notification-toggle" data-id="{{ $notification->id }}" data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}" aria-expanded="false">View more <i class="bi bi-chevron-down"></i></button>
                            @elseif($isUnread)
                                <button type="button" class="mark-read-button" data-id="{{ $notification->id }}" data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}"><i class="bi bi-check2"></i> Mark read</button>
                            @else
                                <a href="{{ url('notification_detail_stu/'.$notification->id) }}">View details <i class="bi bi-chevron-right"></i></a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="notification-empty">
                    <span><i class="bi bi-bell-slash"></i></span>
                    <h2>No {{ strtolower($pageLabel) }} found</h2>
                    <p>{{ $filter === 'all' ? 'New updates from your school will appear here.' : 'There are no '.$filter.' items in this section.' }}</p>
                    @if($filter !== 'all' || $category !== 'all')
                        <a href="{{ $buildUrl(['category' => 'all']) }}">View all {{ strtolower($pageLabel) }}</a>
                    @endif
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())<div class="notification-pagination">{{ $notifications->links() }}</div>@endif
    </div>
</section>

<style>
.student-notification-app{min-height:calc(100vh - 154px)!important;padding:0 0 28px!important;color:#253858!important;background:#f3f6fb!important;overflow:hidden!important}.notification-hero{position:relative!important;min-height:168px!important;padding:20px 15px 62px!important;overflow:hidden!important;color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4 55%,#172d73)!important;border-radius:0 0 28px 28px!important;box-shadow:0 15px 32px rgba(31,56,139,.19)!important}.notification-hero-pattern{position:absolute!important;inset:0!important;opacity:.12!important;background-image:linear-gradient(rgba(255,255,255,.3) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.3) 1px,transparent 1px)!important;background-size:28px 28px!important}.notification-hero-copy{position:relative!important;z-index:1!important;max-width:70%!important}.notification-hero-copy>span{display:block!important;color:rgba(255,255,255,.68)!important;font-size:8px!important;font-weight:700!important;letter-spacing:.08em!important;text-transform:uppercase!important}.notification-hero-copy h1{margin:3px 0!important;color:#fff!important;font-size:22px!important;line-height:1.2!important;font-weight:750!important}.notification-hero-copy p{margin:0!important;color:rgba(255,255,255,.75)!important;font-size:9px!important}.notification-hero-icon{position:absolute!important;z-index:1!important;right:17px!important;top:19px!important;width:51px!important;height:51px!important;border:1px solid rgba(255,255,255,.26)!important;border-radius:16px!important;display:grid!important;place-items:center!important;color:#3156d3!important;background:rgba(255,255,255,.94)!important;box-shadow:0 8px 20px rgba(13,31,83,.24)!important;font-size:23px!important}.notification-hero-icon>span{position:absolute!important;right:-5px!important;top:-6px!important;min-width:19px!important;height:19px!important;padding:0 4px!important;border:2px solid #fff!important;border-radius:99px!important;display:grid!important;place-items:center!important;color:#fff!important;background:#e34850!important;font-size:7px!important;font-weight:750!important;box-sizing:content-box!important}.notification-stats{position:absolute!important;right:15px!important;bottom:13px!important;left:15px!important;z-index:1!important;display:grid!important;grid-template-columns:repeat(3,1fr)!important;padding:9px 2px!important;border:1px solid rgba(255,255,255,.17)!important;border-radius:15px!important;background:rgba(15,35,90,.28)!important;backdrop-filter:blur(10px)!important}.notification-stats>div{text-align:center!important}.notification-stats>div+div{border-left:1px solid rgba(255,255,255,.15)!important}.notification-stats span{display:block!important;color:rgba(255,255,255,.62)!important;font-size:7px!important;text-transform:uppercase!important;letter-spacing:.06em!important}.notification-stats strong{display:block!important;margin-top:3px!important;color:#fff!important;font-size:11px!important;font-weight:700!important}
.notification-content{padding:0 13px!important}.notification-flash{margin:13px 0 -2px!important;padding:10px 11px!important;border:1px solid #cfeede!important;border-radius:12px!important;display:flex!important;align-items:center!important;gap:8px!important;color:#147957!important;background:#eaf9f2!important;font-size:9px!important}.notification-toolbar{margin:18px 0 11px!important}.notification-quick-filters{display:flex!important;gap:6px!important;overflow-x:auto!important;padding:1px 0 8px!important;scrollbar-width:none!important}.notification-quick-filters::-webkit-scrollbar,.notification-filters::-webkit-scrollbar{display:none!important}.notification-quick-filters a,.notification-filters a{min-height:34px!important;padding:7px 11px!important;border:1px solid #e1e6ef!important;border-radius:99px!important;display:inline-flex!important;align-items:center!important;gap:5px!important;flex:0 0 auto!important;color:#6d798c!important;text-decoration:none!important;background:#fff!important;font-size:9px!important;font-weight:650!important}.notification-quick-filters a.active,.notification-filters a.active{color:#fff!important;background:linear-gradient(145deg,#4169e1,#294bb4)!important;border-color:transparent!important;box-shadow:0 5px 12px rgba(49,86,211,.2)!important}.notification-quick-filters a>span,.notification-filters a>span{min-width:16px!important;height:16px!important;padding:0 4px!important;border-radius:99px!important;display:grid!important;place-items:center!important;color:#fff!important;background:#e34850!important;font-size:7px!important}.notification-filters{display:flex!important;gap:6px!important;overflow-x:auto!important;padding:1px 0 9px!important;scrollbar-width:none!important}.notification-bulk-actions{display:flex!important;justify-content:flex-end!important;gap:7px!important}.notification-bulk-actions form{margin:0!important}.bulk-button{min-height:34px!important;padding:6px 10px!important;border:1px solid #dfe5ef!important;border-radius:10px!important;display:inline-flex!important;align-items:center!important;gap:5px!important;color:#3156d3!important;background:#fff!important;font-size:9px!important;font-weight:650!important}.bulk-button.danger{color:#d14d4d!important;background:#fff8f8!important;border-color:#f3dddd!important}.bulk-button:disabled{opacity:.45!important;cursor:not-allowed!important}
.notification-list{display:block!important}.notification-card{position:relative!important;margin:0 0 10px!important;padding:13px!important;border:1px solid #e6ebf3!important;border-radius:17px!important;color:#253858!important;background:#fff!important;box-shadow:0 6px 20px rgba(42,55,92,.055)!important;overflow:hidden!important}.notification-card.is-unread{border-color:#d8e2ff!important;background:linear-gradient(120deg,#fff,#f7f9ff)!important;box-shadow:0 8px 24px rgba(49,86,211,.08)!important}.notification-card-accent{position:absolute!important;left:0!important;top:14px!important;bottom:14px!important;width:3px!important;border-radius:0 4px 4px 0!important}.notification-card-accent.tone-blue{background:#4169e1!important}.notification-card-accent.tone-green{background:#139466!important}.notification-card-accent.tone-purple{background:#7445c7!important}.notification-card-accent.tone-amber{background:#d78a00!important}.notification-card-accent.tone-red{background:#d14d4d!important}.notification-card-head{display:flex!important;align-items:flex-start!important;gap:10px!important}.notification-card-icon{width:38px!important;height:38px!important;min-width:38px!important;max-width:38px!important;border-radius:12px!important;display:grid!important;place-items:center!important;font-size:17px!important}.notification-card-icon.tone-blue{color:#3156d3!important;background:#e8eeff!important}.notification-card-icon.tone-green{color:#139466!important;background:#e4f8ef!important}.notification-card-icon.tone-purple{color:#7445c7!important;background:#f0e9ff!important}.notification-card-icon.tone-amber{color:#d78a00!important;background:#fff4dc!important}.notification-card-icon.tone-red{color:#d14d4d!important;background:#ffeceb!important}.notification-title-wrap{min-width:0!important;flex:1!important}.notification-title-line{display:flex!important;align-items:center!important;gap:6px!important}.notification-title-line h2{margin:1px 0 0!important;overflow:hidden!important;color:#253858!important;font-size:12px!important;line-height:1.3!important;font-weight:700!important;text-overflow:ellipsis!important;white-space:nowrap!important}.unread-dot{width:6px!important;height:6px!important;min-width:6px!important;border-radius:50%!important;background:#e34850!important}.notification-meta{display:flex!important;align-items:center!important;gap:7px!important;margin-top:4px!important;color:#98a2b3!important;font-size:8px!important}.notification-type{padding:2px 6px!important;border-radius:99px!important;color:#52617a!important;background:#edf1f6!important;font-size:7px!important;font-weight:650!important}.read-status{flex:0 0 auto!important;margin-top:1px!important;padding:3px 6px!important;border-radius:99px!important;color:#7d899b!important;background:#eef1f5!important;font-size:7px!important;font-weight:700!important}.read-status.unread{color:#3156d3!important;background:#e8eeff!important}.notification-message{margin:11px 1px 0!important;color:#667085!important;font-size:10px!important;line-height:1.55!important;white-space:pre-line!important;overflow-wrap:anywhere!important}.notification-message.is-collapsed{max-height:3.1em!important;overflow:hidden!important;position:relative!important}.notification-message.is-collapsed:after{content:""!important;position:absolute!important;right:0!important;bottom:0!important;left:0!important;height:1.2em!important;background:linear-gradient(transparent,#fff)!important}.notification-card.is-unread .notification-message.is-collapsed:after{background:linear-gradient(transparent,#f8faff)!important}.notification-attachment{margin-top:10px!important;padding:9px!important;border:1px solid #f1d9dc!important;border-radius:12px!important;display:flex!important;align-items:center!important;gap:9px!important;color:#a73c48!important;text-decoration:none!important;background:#fff7f8!important}.notification-attachment>span{width:33px!important;height:33px!important;min-width:33px!important;border-radius:10px!important;display:grid!important;place-items:center!important;color:#d14d4d!important;background:#ffe8ea!important;font-size:16px!important}.notification-attachment>div{min-width:0!important;flex:1!important}.notification-attachment strong{display:block!important;overflow:hidden!important;font-size:9px!important;text-overflow:ellipsis!important;white-space:nowrap!important}.notification-attachment small{display:block!important;margin-top:2px!important;color:#a8878b!important;font-size:7px!important}.notification-card-footer{margin-top:11px!important;padding-top:9px!important;border-top:1px solid #eef1f6!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:8px!important;color:#98a2b3!important;font-size:7px!important}.notification-card-actions button,.notification-card-actions a{min-height:28px!important;padding:4px 7px!important;border:0!important;border-radius:8px!important;display:inline-flex!important;align-items:center!important;gap:4px!important;color:#3156d3!important;text-decoration:none!important;background:#edf2ff!important;font-size:8px!important;font-weight:650!important}.notification-empty{padding:38px 17px!important;border:1px dashed #dce2ec!important;border-radius:18px!important;text-align:center!important;color:#98a2b3!important;background:#fff!important}.notification-empty>span{width:53px!important;height:53px!important;margin:0 auto 10px!important;border-radius:17px!important;display:grid!important;place-items:center!important;color:#7186c9!important;background:#eaf0ff!important;font-size:23px!important}.notification-empty h2{margin:0!important;color:#344054!important;font-size:13px!important;font-weight:700!important}.notification-empty p{margin:5px auto 0!important;max-width:240px!important;font-size:9px!important;line-height:1.45!important}.notification-empty a{display:inline-block!important;margin-top:11px!important;padding:7px 11px!important;border-radius:9px!important;color:#fff!important;text-decoration:none!important;background:#3156d3!important;font-size:9px!important}.notification-pagination{margin-top:14px!important}.notification-pagination .pagination{justify-content:center!important;flex-wrap:wrap!important}.notification-pagination .page-link{font-size:9px!important}
:root[data-theme="dark"] .student-notification-app{color:#e7ecf4!important;background:#111827!important}:root[data-theme="dark"] .notification-quick-filters a,:root[data-theme="dark"] .notification-filters a,:root[data-theme="dark"] .bulk-button,:root[data-theme="dark"] .notification-card,:root[data-theme="dark"] .notification-empty{color:#aab4c3!important;background:#1b2433!important;border-color:#2c3748!important;box-shadow:none!important}:root[data-theme="dark"] .notification-card.is-unread{background:#1d2940!important;border-color:#344666!important}:root[data-theme="dark"] .notification-title-line h2,:root[data-theme="dark"] .notification-empty h2{color:#e7ecf4!important}:root[data-theme="dark"] .notification-message{color:#aab4c3!important}:root[data-theme="dark"] .notification-message.is-collapsed:after{background:linear-gradient(transparent,#1b2433)!important}:root[data-theme="dark"] .notification-card.is-unread .notification-message.is-collapsed:after{background:linear-gradient(transparent,#1d2940)!important}:root[data-theme="dark"] .notification-card-footer{border-color:#303b4c!important}:root[data-theme="dark"] .notification-type,:root[data-theme="dark"] .read-status{color:#aab4c3!important;background:#263245!important}:root[data-theme="dark"] .notification-attachment{background:#362329!important;border-color:#52313a!important}.notification-pagination nav{max-width:100%!important;overflow-x:auto!important}
@media(max-width:359px){.notification-hero{padding-left:12px!important;padding-right:12px!important}.notification-stats{left:12px!important;right:12px!important}.notification-content{padding-left:10px!important;padding-right:10px!important}.notification-card{padding:11px!important}}
</style>
@endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfToken = @json(csrf_token());

    function updateUnreadSummary() {
        var stat = document.getElementById('notificationUnreadStat');
        var current = stat ? Math.max(0, parseInt(stat.textContent, 10) - 1) : 0;
        if (stat) stat.textContent = current;
        var heroBadge = document.getElementById('notificationHeroCount');
        var filterBadge = document.getElementById('filterUnreadCount');
        if (current === 0) {
            if (heroBadge) heroBadge.remove();
            if (filterBadge) filterBadge.remove();
        } else {
            if (heroBadge) heroBadge.textContent = current > 99 ? '99+' : current;
            if (filterBadge) filterBadge.textContent = current;
        }
    }

    function markRead(button) {
        var card = document.getElementById('notification-' + button.dataset.id);
        if (!card || !card.classList.contains('is-unread')) return Promise.resolve();
        button.disabled = true;
        return fetch(button.dataset.readUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                if (!response.ok) throw new Error();
                card.classList.remove('is-unread');
                var dot = card.querySelector('.unread-dot');
                if (dot) dot.remove();
                var status = card.querySelector('.read-status');
                if (status) {
                    status.textContent = 'Read';
                    status.classList.remove('unread');
                }
                updateUnreadSummary();
                if (button.classList.contains('mark-read-button')) {
                    button.remove();
                } else {
                    button.disabled = false;
                }
            })
            .catch(function () {
                button.disabled = false;
                StudentModal.error('Could not update', 'Please check your connection and try again.');
            });
    }

    document.querySelectorAll('.notification-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            var message = document.getElementById('message-' + button.dataset.id);
            var expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            message.classList.toggle('is-collapsed', expanded);
            button.innerHTML = expanded ? 'View more <i class="bi bi-chevron-down"></i>' : 'View less <i class="bi bi-chevron-up"></i>';
            if (!expanded) markRead(button);
        });
    });

    document.querySelectorAll('.mark-read-button').forEach(function (button) {
        button.addEventListener('click', function () {
            markRead(button);
        });
    });

    var clearForm = document.getElementById('clearNotificationsForm');
    if (clearForm) {
        clearForm.addEventListener('submit', function (event) {
            event.preventDefault();
            StudentModal.confirm({
                type: 'warning',
                title: 'Clear all notifications?',
                message: 'This will remove every notification in this section.',
                confirmText: 'Yes, clear all',
                cancelText: 'Cancel',
                danger: true
            }).then(function (result) {
                if (result.isConfirmed) clearForm.submit();
            });
        });
    }
});
</script>
@endsection
