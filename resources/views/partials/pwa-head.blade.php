{{-- PWA meta tags + service worker registration + auto install detection --}}
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#1d4ed8">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Pointage">
<meta name="application-name" content="Pointage">
<link rel="apple-touch-icon" href="/logo3.png">
<link rel="icon" type="image/png" sizes="192x192" href="/logo3.png">
<link rel="icon" type="image/png" sizes="512x512" href="/logo3.png">

<script>
(function () {
    'use strict';

    // ---- Service Worker ----
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (e) {
                console.warn('SW registration failed:', e);
            });
        });
    }

    // ---- Detection helpers ----
    var ua = navigator.userAgent || '';
    var isIOS = /iPad|iPhone|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var isAndroid = /Android/.test(ua);
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true
        || document.referrer.startsWith('android-app://');

    // Ne rien faire si déjà installé
    if (isStandalone) return;

    var deferredPrompt = null;
    var banner = null;
    var DISMISS_KEY = 'pwa-install-dismissed';
    var DISMISS_HOURS = 24; // re-afficher après 24h si refusé

    function wasDismissedRecently() {
        try {
            var ts = localStorage.getItem(DISMISS_KEY);
            if (!ts) return false;
            return (Date.now() - parseInt(ts, 10)) < (DISMISS_HOURS * 3600 * 1000);
        } catch (e) { return false; }
    }

    function dismissBanner() {
        try { localStorage.setItem(DISMISS_KEY, String(Date.now())); } catch (e) {}
        if (banner) { banner.remove(); banner = null; }
    }

    // ---- Banner styles ----
    function injectStyles() {
        if (document.getElementById('pwa-install-styles')) return;
        var css = ''
            + '#pwa-install-banner{position:fixed;bottom:0;left:0;right:0;z-index:99999;'
            + 'background:linear-gradient(135deg,#1e3a5f 0%,#1d4ed8 100%);'
            + 'color:#fff;padding:12px 16px;display:flex;align-items:center;gap:12px;'
            + 'box-shadow:0 -4px 20px rgba(0,0,0,.3);'
            + 'transform:translateY(100%);transition:transform .35s ease;'
            + 'padding-bottom:calc(12px + env(safe-area-inset-bottom,0px));'
            + 'font-family:system-ui,-apple-system,sans-serif}'
            + '#pwa-install-banner.show{transform:translateY(0)}'
            + '#pwa-install-banner .pwa-icon{flex-shrink:0;width:44px;height:44px;'
            + 'border-radius:12px;background:rgba(255,255,255,.15);'
            + 'display:flex;align-items:center;justify-content:center}'
            + '#pwa-install-banner .pwa-icon img{width:32px;height:32px;border-radius:8px}'
            + '#pwa-install-banner .pwa-text{flex:1;min-width:0}'
            + '#pwa-install-banner .pwa-title{font-size:.9rem;font-weight:700;margin:0 0 2px}'
            + '#pwa-install-banner .pwa-sub{font-size:.78rem;color:rgba(255,255,255,.8);margin:0;line-height:1.3}'
            + '#pwa-install-banner .pwa-btn{flex-shrink:0;background:#fff;color:#1d4ed8;'
            + 'border:none;border-radius:10px;padding:10px 18px;font-size:.85rem;font-weight:700;'
            + 'cursor:pointer;touch-action:manipulation;white-space:nowrap}'
            + '#pwa-install-banner .pwa-btn:active{transform:scale(.95)}'
            + '#pwa-install-banner .pwa-close{flex-shrink:0;background:transparent;border:none;'
            + 'color:rgba(255,255,255,.6);font-size:1.4rem;line-height:1;cursor:pointer;'
            + 'padding:4px 8px;touch-action:manipulation}'
            + '@media(min-width:640px){#pwa-install-banner{max-width:480px;left:50%;'
            + 'transform:translate(-50%,100%);border-radius:16px 16px 0 0}'
            + '#pwa-install-banner.show{transform:translate(-50%,0)}}';
        var style = document.createElement('style');
        style.id = 'pwa-install-styles';
        style.textContent = css;
        document.head.appendChild(style);
    }

    // ---- Create banner ----
    function createBanner(opts) {
        if (banner) return;
        if (wasDismissedRecently()) return;
        injectStyles();

        banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.innerHTML = ''
            + '<div class="pwa-icon"><img src="' + (window.ASSET_LOGO || '/logo3.png') + '" alt=""></div>'
            + '<div class="pwa-text">'
            + '  <p class="pwa-title">' + opts.title + '</p>'
            + '  <p class="pwa-sub">' + opts.subtitle + '</p>'
            + '</div>'
            + '<button class="pwa-btn" id="pwa-install-action">' + opts.button + '</button>'
            + '<button class="pwa-close" id="pwa-install-close" aria-label="Fermer">&times;</button>';

        document.body.appendChild(banner);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () { banner.classList.add('show'); });
        });

        document.getElementById('pwa-install-action').addEventListener('click', opts.onAction);
        document.getElementById('pwa-install-close').addEventListener('click', dismissBanner);
    }

    // ---- iOS instructions modal ----
    function showIOSInstructions() {
        dismissBanner();
        injectStyles();

        var modal = document.createElement('div');
        modal.id = 'pwa-ios-modal';
        modal.style.cssText = 'position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,.6);'
            + 'display:flex;align-items:center;justify-content:center;padding:20px;';
        modal.innerHTML = ''
            + '<div style="background:#fff;border-radius:20px;max-width:380px;width:100%;padding:28px 24px;'
            + 'text-align:center;font-family:system-ui,-apple-system,sans-serif;'
            + 'box-shadow:0 25px 50px rgba(0,0,0,.3)">'
            + '  <div style="width:64px;height:64px;margin:0 auto 16px;border-radius:16px;'
            + 'background:linear-gradient(135deg,#1e3a5f,#1d4ed8);display:flex;align-items:center;justify-content:center">'
            + '    <img src="/logo3.png" style="width:44px;height:44px;border-radius:10px" alt="">'
            + '  </div>'
            + '  <h3 style="font-size:1.15rem;font-weight:700;color:#0f172a;margin:0 0 12px">Installer l\'application</h3>'
            + '  <p style="font-size:.9rem;color:#475569;line-height:1.5;margin:0 0 20px">'
            + '    Pour installer <strong>Pointage</strong> sur votre iPhone ou iPad :</p>'
            + '  <ol style="text-align:left;font-size:.88rem;color:#334155;line-height:1.6;'
            + '    padding-left:20px;margin:0 0 24px">'
            + '    <li style="margin-bottom:10px">Appuyez sur l\'icône <strong>Partager</strong> '
            + '      <svg style="display:inline;vertical-align:middle" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2">'
            + '        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8M16 6l-4-4-4 4M12 2v13"/>'
            + '      </svg> dans la barre du bas</li>'
            + '    <li style="margin-bottom:10px">Sélectionnez <strong>« Sur l\'écran d\'accueil »</strong></li>'
            + '    <li>Appuyez sur <strong>« Ajouter »</strong></li>'
            + '  </ol>'
            + '  <button id="pwa-ios-close" style="width:100%;background:linear-gradient(135deg,#2563eb,#1d4ed8);'
            + '    color:#fff;border:none;border-radius:12px;padding:14px;font-size:.95rem;font-weight:700;'
            + '    cursor:pointer;touch-action:manipulation">J\'ai compris</button>'
            + '</div>';

        document.body.appendChild(modal);

        function closeModal() { modal.remove(); }
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        document.getElementById('pwa-ios-close').addEventListener('click', closeModal);
    }

    // ---- Android / Chrome: beforeinstallprompt ----
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;

        if (wasDismissedRecently()) return;

        createBanner({
            title: 'Installer Pointage',
            subtitle: 'Accédez rapidement depuis votre écran d\'accueil',
            button: 'Installer',
            onAction: function () {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function (choice) {
                    if (choice.outcome === 'dismissed') {
                        dismissBanner();
                    }
                    deferredPrompt = null;
                    if (banner) { banner.remove(); banner = null; }
                });
            }
        });
    });

    // ---- iOS: detect and show instructions after 3s ----
    if (isIOS && !isStandalone) {
        window.addEventListener('load', function () {
            setTimeout(function () {
                if (wasDismissedRecently()) return;
                createBanner({
                    title: 'Installer Pointage',
                    subtitle: 'Ajoutez l\'app sur votre écran d\'accueil',
                    button: 'Comment ?',
                    onAction: showIOSInstructions
                });
            }, 3000);
        });
    }

    // ---- Detect successful installation ----
    window.addEventListener('appinstalled', function () {
        if (banner) { banner.remove(); banner = null; }
        try { localStorage.removeItem(DISMISS_KEY); } catch (e) {}
    });
})();
</script>
