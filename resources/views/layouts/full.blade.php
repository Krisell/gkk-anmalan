<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <meta property="og:locale" content="sv" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Göteborg Kraftsportklubb" />
    <meta property="og:description" content="Styrkelyft i Göteborg" />
    <meta property="og:site_name" content="Göteborg Kraftsportklubb" />
    <meta property="og:image" content="https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg" />
    <meta property="og:image:secure_url" content="https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg" />
    <meta property="og:image:width" content="1500" />
    <meta property="og:image:height" content="827" />
    <meta property="og:image:alt" content="Lyftare går in på flaket" />
    <meta property="og:image:type" content="image/jpeg" />

    <link rel="apple-touch-startup-image" href="https://goteborg-kraftsportklubb.web.app/img/favicon.png">
    <link rel="apple-touch-icon" href="https://goteborg-kraftsportklubb.web.app/img/appIconGKK.png">
    <link rel="icon" type="image/png" href="https://goteborg-kraftsportklubb.web.app/img/favicon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Göteborg Kraftsportklubb</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    @vite(['resources/css/main.css', 'resources/js/app.js'])

    @yield('head')
</head>
<body class="bg-gray-50 h-full min-h-full">
@yield('css')

<div id="app">
@yield('content')
</div>

<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-analytics.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-storage.js"></script>
</body>
</html>
