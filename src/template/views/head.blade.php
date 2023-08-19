@php($css_file = \KalimahApps\Daleel\BladeUtil::getRootLink('css/output.css'))
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>
    {{ !empty($page_title) ? $page_title . ' | ' : '' }}
    {{ \KalimahApps\Daleel\BladeUtil::getConfig('title') }}
</title>

@if (\KalimahApps\Daleel\BladeUtil::getConfig('favicon'))
    <link rel="icon" type="image/x-icon"
        href="/{{ \KalimahApps\Daleel\BladeUtil::getConfig('favicon') }}" />
@endif

<meta name="description"
    content="{{ \KalimahApps\Daleel\BladeUtil::getConfig('main.subtitle') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet">
<link
    href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap"
    rel="stylesheet">
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css"
    integrity="sha512-Jk4AqjWsdSzSWCSuQTfYRIF84Rq/eV0G2+tu07byYwHcbTGfdmLrHjUSwvzp5HvbiqK4ibmNwdcG49Y5RGYPTg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ $css_file }}">
@if (\KalimahApps\Daleel\BladeUtil::isSearchEnabled())
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@docsearch/css@3" />
@endif

<script>
    // Handle dark theme switch
    function updateTheme(toggle = false) {
        const storedValue = localStorage.getItem('daleel-theme');
        const mediaValue = document.documentElement.classList.contains('dark') ?
            'dark' : 'light';
        const userValue = storedValue ?? mediaValue;

        let nextValue = userValue;
        if (toggle) {
            nextValue = userValue === 'dark' ? 'light' : 'dark';
        }

        document.documentElement.setAttribute('data-theme', nextValue);
        localStorage.setItem('daleel-theme', nextValue);
    }

    // Update theme on load
    updateTheme();
</script>
