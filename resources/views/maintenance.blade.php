@extends('layouts.app')

@section('head')
    <meta name="robots" content="noindex">
    {{-- Prefixed class names, so they don't clash with Tailwind's utilities --}}
    <style>
        .maint-page {
            display: flex;
            align-items: center;
            justify-content: center;
            /* The header and the footer are 4rem each */
            min-height: calc(100svh - 8rem);
            padding: 32px 16px;
            background:
                radial-gradient(circle at 20% 0%, rgba(255, 255, 255, 0.12), transparent 50%),
                linear-gradient(160deg, var(--color-gkk-light) 0%, var(--color-gkk) 55%, #1d2847 100%);
        }

        .maint-card {
            width: 100%;
            max-width: 480px;
            padding: 36px 32px 32px;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.45);
            text-align: center;
        }

        .maint-logo {
            width: 88px;
            height: auto;
            margin: 0 auto 20px;
        }
    </style>
@endsection

@section('main-class', '')

@section('content')
<section class="maint-page">
    <div class="maint-card">
        <img class="maint-logo" src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png" alt="Göteborg Kraftsportklubb">
        <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-gkk-light">Tillfälligt underhåll</p>
        <h1 class="mb-4 text-3xl font-extrabold leading-tight text-gkk">Vi flyttar till en ny server</h1>
        <p class="mb-3 leading-relaxed text-gray-600">Medlemssidorna, anmälningar och inloggning är pausade medan vi flyttar. Detta är förhoppningsvis klart inom en timme.</p>
        <p class="mb-3 leading-relaxed text-gray-600">Är du inloggad är du det fortfarande när vi är klara. Ladda om sidan om en stund.</p>
        <div class="mt-7 flex flex-wrap justify-center gap-3">
            <a href="javascript:location.reload()" class="rounded-full bg-gkk px-6 py-3 font-semibold text-white shadow-lg shadow-gkk/30 transition hover:-translate-y-0.5">Försök igen</a>
            <a href="/" class="rounded-full px-6 py-3 font-semibold text-gkk ring-2 ring-gkk/20 transition hover:-translate-y-0.5">Till startsidan</a>
        </div>
    </div>
</section>
<footer class="flex h-16 items-center justify-center text-sm text-gray-500">Göteborg Kraftsportklubb - 2026</footer>
@endsection
