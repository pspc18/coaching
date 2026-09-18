<script>
(function () {
    'use strict';

    var endpoint = @json(url('csrf-token/refresh'));
    var refreshInFlight = null;
    var lastRefreshAt = 0;

    function applyToken(token) {
        if (!token) return;

        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', token);

        document.querySelectorAll('input[name="_token"]').forEach(function (input) {
            input.value = token;
        });

        window.__csrfToken = token;
        if (window.jQuery) {
            window.jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
        }
    }

    function refreshCsrfToken(force) {
        var now = Date.now();
        if (!force && now - lastRefreshAt < 60000) {
            return Promise.resolve(window.__csrfToken || '');
        }
        if (refreshInFlight) return refreshInFlight;

        refreshInFlight = fetch(endpoint, {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (response) {
            if (!response.ok) throw new Error('Token refresh failed');
            return response.json();
        }).then(function (data) {
            applyToken(data.token);
            lastRefreshAt = Date.now();
            return data.token;
        }).catch(function () {
            return '';
        }).finally(function () {
            refreshInFlight = null;
        });

        return refreshInFlight;
    }

    window.refreshCsrfToken = refreshCsrfToken;

    document.addEventListener('DOMContentLoaded', function () {
        applyToken((document.querySelector('meta[name="csrf-token"]') || {}).content);
        refreshCsrfToken(true);
    });
    window.addEventListener('pageshow', function () { refreshCsrfToken(false); });
    window.addEventListener('focus', function () { refreshCsrfToken(false); });
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') refreshCsrfToken(false);
    });

    // Keeps long-running browser and WebView pages connected to a valid
    // session and updates every Blade @csrf field without reloading the page.
    window.setInterval(function () { refreshCsrfToken(true); }, 5 * 60 * 1000);

    if (window.jQuery) {
        window.jQuery(document).ajaxError(function (_event, xhr) {
            if (xhr && xhr.status === 419) refreshCsrfToken(true);
        });
    }
})();
</script>
