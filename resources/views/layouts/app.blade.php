<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-startup-image" href="https://anmalan.gkk-styrkelyft.se/favicon.png">
    <link rel="apple-touch-icon" href="https://anmalan.gkk-styrkelyft.se/appIconGKK.png">
    <link rel="icon" type="image/png" href="https://anmalan.gkk-styrkelyft.se/favicon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Göteborg Kraftsportklubb</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ mix('css/main.css') }}" rel="stylesheet">

    @yield('head')
</head>
<body class="bg-gray-50 h-full min-h-full">
@yield('css')

<div id="app" class="mt-16">
<gkk-navbar :user='@json(auth()->user())' :site='@json($site ?? '')'></gkk-navbar>

<main class="mt-16">
@yield('content')
</main>

</div>

<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-analytics.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.0/firebase-storage.js"></script>
<script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
