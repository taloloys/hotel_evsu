<!-- PWA Manifest & Mobile Meta Tags -->
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<meta name="theme-color" content="#7B1113">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="EVSU Hotel">

<!-- Icons -->
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('{{ asset('sw.js') }}')
                .catch(function (error) {
                    console.warn('PWA ServiceWorker registration failed:', error);
                });
        });
    }

    window.addEventListener('beforeinstallprompt', function (event) {
        window.deferredPWAInstallPrompt = event;
        window.dispatchEvent(new CustomEvent('pwa-installable'));
    });
</script>
