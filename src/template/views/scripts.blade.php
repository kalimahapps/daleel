@php($js_file = \KalimahApps\Daleel\BladeUtil::getRootLink('js/index.js'))
<script src="{{ $js_file }}"></script>
@if (\KalimahApps\Daleel\BladeUtil::isSearchEnabled())
    <script src="https://cdn.jsdelivr.net/npm/@docsearch/js@3"></script>
    <script type="text/javascript">
        docsearch({
            appId: "{{ \KalimahApps\Daleel\BladeUtil::getConfig('search.options.app_id') }}",
            apiKey: "{{ \KalimahApps\Daleel\BladeUtil::getConfig('search.options.api_key') }}",
            indexName: "{{ \KalimahApps\Daleel\BladeUtil::getConfig('search.options.index_name') }}",
            insights: false,
            container: '#search',
            debug: false
        });
    </script>
@endif

@php($gtag = \KalimahApps\Daleel\BladeUtil::getConfig('gtag'))
@if ($gtag !== false)
    <!-- Google tag (gtag.js) -->
    <script async
        src="https://www.googletagmanager.com/gtag/js?id={{ $gtag }}">
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', {{ $gtag }});
    </script>
@endif
