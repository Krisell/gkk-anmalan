<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>GKK Live Overlay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Barlow:wght@500;600&display=swap" rel="stylesheet">
    @vite(['resources/js/live/live-overlay.js'])
</head>
<body data-powerlifting-live="{{ config('services.powerliftinglive.url') }}">
<div id="live-overlay"></div>
</body>
</html>
