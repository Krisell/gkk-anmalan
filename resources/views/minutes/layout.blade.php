<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $minutes->title() }} – Göteborg Kraftsportklubb</title>
    <link rel="icon" type="image/png" href="https://goteborg-kraftsportklubb.web.app/img/favicon.png">
    <style>
        @page { size: A4; margin: 20mm 18mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f6; color: #111; font: 10.5pt/1.45 Helvetica, Arial, sans-serif; }
        .toolbar { position: sticky; top: 0; display: flex; gap: 8px; justify-content: center; padding: 12px; background: #314270; }
        .toolbar a, .toolbar button { font: inherit; font-size: 14px; padding: 6px 14px; border-radius: 6px; border: 1px solid #fff; background: #fff; color: #314270; text-decoration: none; cursor: pointer; }
        .toolbar a { background: transparent; color: #fff; }
        .page { max-width: 210mm; margin: 24px auto; padding: 20mm 18mm; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
        h1 { font-size: 13pt; margin: 0; }
        h2 { font-size: 12pt; margin: 0 0 14pt; font-weight: bold; }
        .meta p { margin: 0; }
        .attendance { margin: 14pt 0 18pt; }
        .attendance p { margin: 0 0 2pt; }
        .item { margin-bottom: 12pt; break-inside: avoid-page; }
        .item h3 { font-size: 10.5pt; margin: 0 0 2pt; }
        .item .body > :first-child { margin-top: 0; }
        .item .body > :last-child { margin-bottom: 0; }
        .item .body p, .item .body div { margin: 0 0 6pt; }
        .item .body ul, .item .body ol { margin: 0 0 6pt; padding-left: 18pt; }
        .item .body img { max-width: 100%; height: auto; }
        .item .body figure { margin: 6pt 0; }
        .item .body figcaption { display: none; }
        .item .body a { color: inherit; }
        .approval { margin-top: 24pt; padding-top: 8pt; border-top: 1px solid #ccc; font-style: italic; }
        .panel { max-width: 210mm; margin: 24px auto 0; padding: 16px 20px; background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.15); font: 14px/1.5 Helvetica, Arial, sans-serif; }
        .panel h2 { font-size: 16px; margin: 0 0 6px; }
        .panel p { margin: 0 0 10px; }
        .panel .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-start; }
        .panel textarea { width: 100%; min-height: 80px; padding: 8px; font: inherit; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 8px; }
        .panel .primary, .panel .secondary { font: inherit; padding: 8px 16px; border-radius: 6px; cursor: pointer; border: 1px solid #314270; }
        .panel .primary { background: #314270; color: #fff; }
        .panel .secondary { background: #fff; color: #314270; }
        .panel .ok { color: #166534; }
        .panel .warn { color: #92400e; }
        .panel details { flex: 1 1 100%; }
        .panel summary { cursor: pointer; color: #314270; margin: 4px 0 8px; }
        @media print {
            .panel { display: none; }
            body { background: #fff; }
            .toolbar { display: none; }
            .page { margin: 0; padding: 0; max-width: none; box-shadow: none; }
        }
    </style>
</head>
<body>
@yield('toolbar')

<main class="page">
    <h1>Göteborg Kraftsportklubb</h1>
    <h2>{{ $minutes->title() }}</h2>

    <div class="meta">
        <p>Datum: {{ $minutes->date->format('Y-m-d') }}</p>
        @if ($minutes->timeSpan())
            <p>Tid: {{ $minutes->timeSpan() }}</p>
        @endif
        @if ($minutes->location)
            <p>Plats: {{ $minutes->location }}</p>
        @endif
    </div>

    <div class="attendance">
        <p>Närvarande: {{ implode(', ', $minutes->presentNames()) }}</p>
        <p>Frånvarande: {{ implode(', ', $minutes->absentNames()) }}</p>
    </div>

    @foreach ($minutes->numberedItems() as $item)
        <section class="item">
            <h3>{{ $item['number'] }}. {{ $item['title'] }}</h3>
            {{-- Bodies are HTML written by admins in the editor, like news items. --}}
            <div class="body">{!! $item['body'] !!}</div>
        </section>
    @endforeach

    @if ($minutes->approved_at)
        <p class="approval">Justerat {{ $minutes->approved_at->format('Y-m-d') }} av {{ $minutes->approved_by_name }}.</p>
    @endif
</main>
</body>
</html>
