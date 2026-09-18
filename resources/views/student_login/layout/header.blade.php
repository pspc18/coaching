@php
    $headerStudent = $getUser ?? Helper::getUser();
    $headerName = trim(($headerStudent->first_name ?? '').' '.($headerStudent->last_name ?? ''));
    $headerImage = !empty($headerStudent->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$headerStudent->image
        : asset('public/assets/student_login/img/user_icon.png');
@endphp

<header class="app-header">
    <div class="app-header-bar">
        <button type="button" id="menuToggle" class="header-menu" aria-label="Open navigation menu" aria-controls="sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-copy">
            <span class="header-eyebrow">Student portal</span>
            <h1>@yield('page_title', 'Dashboard')</h1>
            <div class="header-context"><span class="context-dot"></span><span>@yield('page_sub', 'Welcome back')</span></div>
        </div>

        <button type="button" id="hardRefreshBtn" class="header-refresh" data-refresh-url="{{ url('hard-refresh') }}" aria-label="Refresh student app">
            <i class="bi bi-arrow-clockwise"></i>
        </button>

        <a href="{{ url('profileStudent') }}" class="header-profile {{ request()->is('profileStudent*') ? 'active' : '' }}" aria-label="Open {{ $headerName ?: 'student' }} profile">
            @if(!empty($headerStudent->image))
                <span class="header-avatar-photo" style="background-image:url('{{ $headerImage }}')" role="img" aria-label="{{ $headerName ?: 'Student' }}"></span>
            @else
                <span class="header-avatar-fallback" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
            @endif
            <span class="profile-online" aria-hidden="true"></span>
        </a>
    </div>
</header>

<style>
/* Self-contained shared app bar: remains stable even when the external CSS is stale. */
.app-header{
    position:fixed!important;top:0!important;left:50%!important;right:auto!important;z-index:900!important;
    display:block!important;width:100%!important;max-width:450px!important;min-height:70px!important;height:70px!important;
    margin:0!important;padding:7px 10px!important;transform:translateX(-50%)!important;
    color:#253858!important;background:rgba(255,255,255,.97)!important;border:0!important;
    border-bottom:1px solid #e8ecf3!important;box-shadow:0 8px 24px rgba(30,43,79,.08)!important;
    backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);overflow:visible!important;
}
.app-header .app-header-bar{
    width:100%!important;height:56px!important;min-height:56px!important;margin:0!important;padding:0!important;
    display:grid!important;grid-template-columns:44px minmax(0,1fr) 36px 36px!important;
    align-items:center!important;gap:10px!important;
}
.app-header .header-menu{
    position:relative!important;width:44px!important;height:44px!important;min-width:44px!important;max-width:44px!important;
    margin:0!important;padding:0!important;border:1px solid #e3e9f5!important;border-radius:14px!important;
    display:grid!important;place-items:center!important;color:#3156d3!important;
    background:linear-gradient(145deg,#f4f7ff,#e9efff)!important;box-shadow:0 5px 13px rgba(49,86,211,.1)!important;
    opacity:1!important;visibility:visible!important;cursor:pointer!important;
}
.app-header .header-menu i{display:block!important;margin:0!important;font-size:25px!important;line-height:1!important;color:inherit!important}
.app-header .header-menu:active{transform:scale(.94)!important}
.app-header .header-menu:focus-visible{outline:3px solid rgba(49,86,211,.18)!important;outline-offset:2px!important}
.app-header .header-copy{display:block!important;min-width:0!important;width:auto!important;margin:0!important;padding:0!important;line-height:1.1!important;text-align:left!important;overflow:hidden!important}
.app-header .header-eyebrow{display:block!important;margin:0 0 2px!important;color:#8490a3!important;font-size:8px!important;line-height:1.1!important;font-weight:700!important;letter-spacing:.09em!important;text-transform:uppercase!important}
.app-header .header-copy h1{display:block!important;margin:0!important;padding:0!important;overflow:hidden!important;color:#253858!important;font-size:15px!important;line-height:18px!important;font-weight:750!important;letter-spacing:-.01em!important;text-overflow:ellipsis!important;white-space:nowrap!important}
.app-header .header-context{display:flex!important;min-width:0!important;margin:4px 0 0!important;padding:0!important;align-items:center!important;gap:5px!important;color:#8a94a6!important;font-size:9px!important;line-height:11px!important;overflow:hidden!important}
.app-header .header-context>span:last-child{display:block!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important}
.app-header .context-dot{display:block!important;width:5px!important;height:5px!important;min-width:5px!important;max-width:5px!important;min-height:5px!important;max-height:5px!important;flex:0 0 5px!important;border-radius:50%!important;background:#2bb673!important;box-shadow:0 0 0 3px rgba(43,182,115,.12)!important}
.app-header .header-profile{
    position:relative!important;width:36px!important;height:36px!important;min-width:36px!important;min-height:36px!important;
    max-width:36px!important;max-height:36px!important;margin:0!important;padding:0!important;overflow:hidden!important;
    display:block!important;border:1px solid #dfe6f2!important;border-radius:12px!important;background:#fff!important;
    box-shadow:0 5px 13px rgba(30,43,79,.1)!important;opacity:1!important;visibility:visible!important;
}
.app-header .header-refresh{
    position:relative!important;width:36px!important;height:36px!important;min-width:36px!important;min-height:36px!important;
    max-width:36px!important;max-height:36px!important;margin:0!important;padding:0!important;border:1px solid #dfe6f2!important;
    border-radius:12px!important;display:grid!important;place-items:center!important;color:#3156d3!important;background:#fff!important;
    box-shadow:0 5px 13px rgba(30,43,79,.1)!important;opacity:1!important;visibility:visible!important;cursor:pointer!important;
}
.app-header .header-refresh i{display:block!important;font-size:18px!important;line-height:1!important}
.app-header .header-refresh:active{transform:scale(.94)!important}
.app-header .header-refresh.is-loading{color:#3156d3!important;background:#e8eeff!important}
.app-header .header-avatar-photo,.app-header .header-avatar-fallback{
    position:absolute!important;inset:2px!important;width:30px!important;height:30px!important;
    min-width:30px!important;min-height:30px!important;max-width:30px!important;max-height:30px!important;
    margin:0!important;padding:0!important;display:grid!important;place-items:center!important;border-radius:9px!important;
}
.app-header .header-avatar-photo{background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important}
.app-header .header-avatar-fallback{background:linear-gradient(145deg,#e8eeff,#d7e2ff)!important;color:#3156d3!important;font-size:18px!important}
.app-header .header-avatar-fallback i{display:block!important;color:inherit!important;font-size:18px!important;line-height:1!important}
.app-header .profile-online{position:absolute!important;right:0!important;bottom:0!important;width:9px!important;height:9px!important;min-width:9px!important;min-height:9px!important;border:2px solid #fff!important;border-radius:50%!important;background:#2bb673!important}
:root[data-theme="dark"] .app-header{color:#e7ecf4!important;background:rgba(27,36,51,.98)!important;border-bottom-color:#303b4c!important;box-shadow:0 8px 24px rgba(0,0,0,.22)!important}
:root[data-theme="dark"] .app-header .header-copy h1{color:#e7ecf4!important}
:root[data-theme="dark"] .app-header .header-eyebrow,:root[data-theme="dark"] .app-header .header-context{color:#9da8b8!important}
:root[data-theme="dark"] .app-header .header-menu{color:#8ea7ff!important;background:#222e42!important;border-color:#354258!important;box-shadow:none!important}
:root[data-theme="dark"] .app-header .header-refresh{background:#222e42!important;border-color:#3b485d!important;box-shadow:none!important}
:root[data-theme="dark"] .app-header .header-profile{background:#222e42!important;border-color:#3b485d!important;box-shadow:none!important}
:root[data-theme="dark"] .app-header .profile-online{border-color:#1b2433!important}
@media(max-width:359px){
    .app-header{padding-left:7px!important;padding-right:7px!important}
    .app-header .app-header-bar{grid-template-columns:42px minmax(0,1fr) 34px 34px!important;gap:6px!important}
    .app-header .header-menu{width:42px!important;height:42px!important;min-width:42px!important;max-width:42px!important}
    .app-header .header-refresh{width:34px!important;height:34px!important;min-width:34px!important;min-height:34px!important;max-width:34px!important;max-height:34px!important}
    .app-header .header-profile{width:34px!important;height:34px!important;min-width:34px!important;min-height:34px!important;max-width:34px!important;max-height:34px!important}
    .app-header .header-avatar-photo,.app-header .header-avatar-fallback{width:28px!important;height:28px!important;min-width:28px!important;min-height:28px!important;max-width:28px!important;max-height:28px!important}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var hardRefreshBtn = document.getElementById('hardRefreshBtn');
    if (!hardRefreshBtn) return;

    hardRefreshBtn.addEventListener('click', async function (event) {
        event.preventDefault();
        if (this.disabled) return;

        var icon = this.querySelector('i');
        this.disabled = true;
        this.classList.add('is-loading');
        if (icon) icon.classList.add('refresh-rotate');

        try {
            var response = await fetch(this.dataset.refreshUrl, { cache: 'no-store', headers: { 'Accept': 'application/json' } });
            var result = await response.json();
            if (!response.ok || result.status !== 'success') {
                throw new Error('Refresh failed');
            }
            var url = new URL(window.location.href);
            url.searchParams.set('v', Date.now());
            window.location.replace(url.toString());
        } catch (error) {
            this.disabled = false;
            this.classList.remove('is-loading');
            if (icon) icon.classList.remove('refresh-rotate');
        }
    }, true);
});
</script>
