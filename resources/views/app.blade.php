<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased {{ request()->is('admin*') ? 'notranslate' : '' }}">
        <x-inertia::app />

        @if(!request()->is('admin*'))
        <!-- Google Translate -->
        <div id="google_translate_element" style="display:none;"></div>
        <script>
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'id',
                    includedLanguages: 'id,en,ru,de,fr',
                    autoDisplay: false
                }, 'google_translate_element');
            }
        </script>
        <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
        
        <!-- Set language from localStorage -->
        <script>
            try {
                var storedLang = localStorage.getItem('vins_language');
                if (storedLang) {
                    var langCode = storedLang.toLowerCase();
                    console.log('[GTranslate] Will translate to:', storedLang);
                    
                    // Set cookies
                    document.cookie = 'googtrans=/auto/' + langCode + '; path=/;';
                    document.cookie = 'googtrans=/auto/' + langCode + '; path=/; domain=' + window.location.hostname + ';';
                    document.cookie = 'googtrans=/auto/' + langCode + '; path=/; domain=.' + window.location.hostname + ';';
                } else {
                    console.log('[GTranslate] No language stored, default ID');
                }
            } catch(e) {
                console.error('[GTranslate] Error:', e);
            }
        </script>
        @endif
    </body>
</html>
