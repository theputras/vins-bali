<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $settings = \Illuminate\Support\Facades\Cache::rememberForever('global_settings', function () {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                $jsonKeys = ['terms_and_conditions', 'home_usps', 'home_services', 'rental_requirements', 'home_brand_logos', 'seo_settings'];
                foreach ($jsonKeys as $k) {
                    if (isset($settings[$k])) {
                        $decoded = @json_decode($settings[$k], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $settings[$k] = $decoded;
                        }
                    }
                }
                return $settings;
            });
            $seoSettings = $settings['seo_settings'] ?? [];
            $faviconUrl = !empty($seoSettings['favicon_path']) ? asset('storage/' . $seoSettings['favicon_path']) : asset('images/logo.png');
        @endphp

        @if(!empty($seoSettings['google_verification_code']))
            <meta name="google-site-verification" content="{{ $seoSettings['google_verification_code'] }}" />
        @endif

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="{{ $faviconUrl }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>

        @if(!request()->is('vbpanel*') && !empty($seoSettings['google_analytics_script']))
            {!! $seoSettings['google_analytics_script'] !!}
        @endif
    </head>
    <body class="font-sans antialiased {{ request()->is('vbpanel*') ? 'notranslate' : '' }}">
        <x-inertia::app />

        @if(!request()->is('vbpanel*'))
        <!-- Google Translate (widget UI hidden, translation functionality only) -->
        <div id="google_translate_element" style="visibility:hidden!important;height:0!important;width:0!important;overflow:hidden!important;position:fixed!important;top:-9999px!important;left:-9999px!important;"></div>
        <script>
            // 1) Set cookies BEFORE loading the translate script
            (function() {
                try {
                    var hostname = window.location.hostname;
                    var parts = hostname.split('.');
                    var rootDomain = parts.length > 2 ? parts.slice(-2).join('.') : hostname;

                    var storedLang = localStorage.getItem('vins_language');
                    if (storedLang && storedLang !== 'ID') {
                        var langCode = storedLang.toLowerCase();
                        document.cookie = 'googtrans=/auto/' + langCode + '; path=/;';
                        document.cookie = 'googtrans=/auto/' + langCode + '; path=/; domain=' + hostname + ';';
                        document.cookie = 'googtrans=/auto/' + langCode + '; path=/; domain=.' + hostname + ';';
                        document.cookie = 'googtrans=/auto/' + langCode + '; path=/; domain=.' + rootDomain + ';';
                    } else {
                        // Nuke ALL googtrans cookies on every domain variation
                        var expiry = 'expires=Thu, 01 Jan 1970 00:00:00 GMT';
                        document.cookie = 'googtrans=; path=/; ' + expiry + ';';
                        document.cookie = 'googtrans=; path=/; domain=' + hostname + '; ' + expiry + ';';
                        document.cookie = 'googtrans=; path=/; domain=.' + hostname + '; ' + expiry + ';';
                        document.cookie = 'googtrans=; path=/; domain=.' + rootDomain + '; ' + expiry + ';';
                    }
                } catch(e) {}
            })();

            // 2) Google Translate init callback
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'id',
                    includedLanguages: 'id,en',
                    autoDisplay: false
                }, 'google_translate_element');

                var storedLang = localStorage.getItem('vins_language');
                var attempts = 0;
                var triggerInterval = setInterval(function() {
                    attempts++;
                    var combo = document.querySelector('.goog-te-combo');
                    if (combo) {
                        if (storedLang === 'EN') {
                            // Translate to English
                            combo.value = 'en';
                        } else {
                            // Reset to original Indonesian
                            combo.value = 'id';
                        }
                        combo.dispatchEvent(new Event('change'));
                        clearInterval(triggerInterval);
                        setTimeout(hideAllTranslateWidgets, 1000);
                    }
                    if (attempts > 50) clearInterval(triggerInterval);
                }, 100);
            }

            // 3) Function to hide all Google Translate UI elements
            function hideAllTranslateWidgets() {
                var selectors = [
                    'iframe.skiptranslate',
                    'iframe.VIpgJd-ZVi9od-ORHb-OEVmcd',
                    'iframe[id$=".container"]',
                    'iframe.goog-te-banner-frame',
                    'div.skiptranslate',
                    'div[class*="VIpgJd"]',
                    'div[class*="goog-te"]',
                    '#goog-gt-tt',
                    '#google_translate_element'
                ];
                
                document.querySelectorAll(selectors.join(',')).forEach(function(el) {
                    el.style.setProperty('display', 'none', 'important');
                    el.style.setProperty('visibility', 'hidden', 'important');
                    el.style.setProperty('height', '0', 'important');
                    el.style.setProperty('width', '0', 'important');
                    el.style.setProperty('position', 'absolute', 'important');
                    el.style.setProperty('top', '-9999px', 'important');
                });

                // Reset body top (Google Translate pushes body down for its banner)
                if (document.body) {
                    document.body.style.setProperty('top', '0px', 'important');
                }
            }

            // 4) MutationObserver — only to catch late-injected UI and body top changes
            (function() {
                var observerStarted = false;
                
                function startObserver() {
                    if (observerStarted) return;
                    observerStarted = true;
                    
                    var observer = new MutationObserver(function(mutations) {
                        var needsHide = false;
                        mutations.forEach(function(mutation) {
                            // Check newly added nodes
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) {
                                    var tag = node.tagName;
                                    var cls = node.className || '';
                                    if (tag === 'IFRAME' && (cls.indexOf('skiptranslate') !== -1 || cls.indexOf('VIpgJd') !== -1)) {
                                        needsHide = true;
                                    }
                                    if (tag === 'DIV' && (cls.indexOf('skiptranslate') !== -1 || cls.indexOf('goog-te') !== -1)) {
                                        needsHide = true;
                                    }
                                }
                            });
                            
                            // Catch inline style changes (Google sets visibility:visible)
                            if (mutation.type === 'attributes' && mutation.target && mutation.target.tagName === 'IFRAME') {
                                var cls = mutation.target.className || '';
                                if (cls.indexOf('skiptranslate') !== -1 || cls.indexOf('VIpgJd') !== -1) {
                                    needsHide = true;
                                }
                            }
                        });
                        
                        if (needsHide) {
                            hideAllTranslateWidgets();
                        }
                        
                        // Always ensure body top is 0
                        if (document.body && document.body.style.top !== '0px') {
                            document.body.style.setProperty('top', '0px', 'important');
                        }
                    });

                    observer.observe(document.documentElement, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['style', 'class']
                    });
                }
                
                // Delay observer start to let Google Translate finish initialization
                setTimeout(startObserver, 3000);
            })();
        </script>
        <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
        @endif
    </body>
</html>
