{{-- PWA meta tags + service worker registration --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#1d4ed8">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Pointage">
<link rel="apple-touch-icon" href="{{ asset('logo3.png') }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="Pointage">

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js').catch(function (e) {
            console.warn('SW registration failed:', e);
        });
    });
}
</script>
