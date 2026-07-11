{{-- Global loading spinner helper for all buttons and forms --}}
<style>
.btn-spinner {
    display: none;
    width: 1em;
    height: 1em;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: btn-spin 0.6s linear infinite;
    flex-shrink: 0;
}
.btn-spinner--show { display: inline-block; }
.is-loading .btn-spinner--show { display: inline-block; }
.is-loading .btn-label { opacity: 0.7; }

@keyframes btn-spin {
    to { transform: rotate(360deg); }
}

/* Full-page overlay spinner for heavy processing */
.page-spinner-overlay {
    position: fixed;
    inset: 0;
    z-index: 99998;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(2px);
    display: none;
    align-items: center;
    justify-content: center;
}
.page-spinner-overlay--show { display: flex; }
.page-spinner-circle {
    width: 48px;
    height: 48px;
    border: 4px solid rgba(96, 165, 250, 0.2);
    border-top-color: #60a5fa;
    border-radius: 50%;
    animation: btn-spin 0.7s linear infinite;
}

/* Link/card loading state */
.is-loading-link {
    pointer-events: none;
    opacity: 0.6;
}
.is-loading-link::after {
    content: '';
    display: inline-block;
    width: 14px;
    height: 14px;
    margin-left: 8px;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: btn-spin 0.6s linear infinite;
    vertical-align: middle;
}
</style>

<script>
(function () {
    'use strict';

    // Auto-attach spinner to all form submissions
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.dataset.spinner === 'false') return;

        var btn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (!btn) return;

        // Store original state
        var originalHTML = btn.innerHTML;
        var originalWidth = btn.offsetWidth;

        btn.classList.add('is-loading');
        btn.disabled = true;
        btn.style.minWidth = originalWidth + 'px';

        // Inject spinner if not already present
        if (!btn.querySelector('.btn-spinner')) {
            var spinner = document.createElement('span');
            spinner.className = 'btn-spinner btn-spinner--show';
            spinner.style.marginRight = '0.5em';
            btn.insertBefore(spinner, btn.firstChild);
        } else {
            btn.querySelector('.btn-spinner').classList.add('btn-spinner--show');
        }

        // Restore after 30s as safety net
        setTimeout(function () {
            if (btn.classList.contains('is-loading')) {
                btn.classList.remove('is-loading');
                btn.disabled = false;
                var sp = btn.querySelector('.btn-spinner');
                if (sp) sp.classList.remove('btn-spinner--show');
            }
        }, 30000);
    }, true);

    // Auto-attach spinner to links with data-spinner="link" or .qa-action
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[data-spinner="link"], a.qa-action, a.btn-link-spinner');
        if (!link) return;
        // Don't intercept if target=_blank
        if (link.target === '_blank') return;
        // Add loading state
        link.classList.add('is-loading-link');
    }, true);

    // Expose helper for manual spinner control
    window.Spinners = {
        show: function (btn) {
            if (!btn) return;
            var originalWidth = btn.offsetWidth;
            btn.classList.add('is-loading');
            btn.disabled = true;
            btn.style.minWidth = originalWidth + 'px';
            if (!btn.querySelector('.btn-spinner')) {
                var spinner = document.createElement('span');
                spinner.className = 'btn-spinner btn-spinner--show';
                spinner.style.marginRight = '0.5em';
                btn.insertBefore(spinner, btn.firstChild);
            } else {
                btn.querySelector('.btn-spinner').classList.add('btn-spinner--show');
            }
        },
        hide: function (btn) {
            if (!btn) return;
            btn.classList.remove('is-loading');
            btn.disabled = false;
            var sp = btn.querySelector('.btn-spinner');
            if (sp) sp.classList.remove('btn-spinner--show');
        },
        showOverlay: function () {
            var ov = document.getElementById('pageSpinnerOverlay');
            if (!ov) {
                ov = document.createElement('div');
                ov.id = 'pageSpinnerOverlay';
                ov.className = 'page-spinner-overlay';
                ov.innerHTML = '<div class="page-spinner-circle"></div>';
                document.body.appendChild(ov);
            }
            ov.classList.add('page-spinner-overlay--show');
        },
        hideOverlay: function () {
            var ov = document.getElementById('pageSpinnerOverlay');
            if (ov) ov.classList.remove('page-spinner-overlay--show');
        }
    };
})();
</script>
