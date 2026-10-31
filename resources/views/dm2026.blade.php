@extends('layouts.app')

@section('content')
@php
  $img = 'https://goteborg-kraftsportklubb.web.app/img/';
  $startlistsUrl = 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FStartlistor%20DM%20SL%202026.pdf?alt=media&token=c9306e6a-fb85-4df6-8c85-7854b716fb0b';

  $facts = [
    ['icon' => 'calendar', 'label' => 'Datum', 'value' => '14 november', 'note' => 'Lördag, första start 09:30'],
    ['icon' => 'map-marker', 'label' => 'Plats', 'value' => 'Friskis & Svettis Majorna', 'note' => 'Karl Johansgatan 152, Göteborg'],
    ['icon' => 'users', 'label' => 'Klasser', 'value' => 'Senior, ungdom, junior', 'note' => 'Samtliga viktklasser, damer och herrar'],
  ];

@endphp

{{-- Hero --}}
<section class="relative isolate overflow-hidden bg-gkk">
  <img src="{{ $img }}mark-min.jpg" alt="" class="landing-hero-image absolute inset-0 -z-20 h-full w-full object-cover object-center">
  <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gkk via-gkk/70 to-black/40"></div>
  <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/50 to-transparent"></div>

  <div class="mx-auto flex max-w-7xl flex-col justify-end px-4 pt-14 pb-14 sm:h-[60svh] sm:min-h-[440px] sm:max-h-[620px] sm:px-6 sm:pt-20 sm:pb-20 lg:px-8">
    <div class="landing-rise max-w-3xl">
      <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.15em] text-white shadow-sm ring-1 ring-white/40 backdrop-blur-md sm:text-xs sm:tracking-[0.25em]">
        <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
        Lördag 14 november 2026 · Göteborg
      </div>
      <h1 class="text-[2.5rem] font-extrabold leading-[0.95] tracking-tight text-white drop-shadow-lg sm:text-6xl lg:text-7xl">
        DM i klassisk<br>
        styrkelyft 2026
      </h1>
      <p class="mt-6 max-w-xl text-lg leading-relaxed text-white/85 sm:text-xl">
        Distriktsmästerskap för senior, ungdom och junior – arrangerat av Göteborg Kraftsportklubb och Västra Götalands Styrkelyftförbund.
      </p>
    </div>
  </div>
</section>

{{-- Snabbfakta --}}
<section class="relative mx-auto -mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
  <div class="grid gap-4 md:grid-cols-3">
    @foreach ($facts as $fact)
      <{{ isset($fact['href']) ? 'a' : 'div' }} @isset($fact['href']) href="{{ $fact['href'] }}" target="_blank" @endisset class="block rounded-2xl bg-white p-6 shadow-lg shadow-gray-900/5 ring-1 ring-gray-900/5 {{ isset($fact['href']) ? 'transition hover:-translate-y-0.5 hover:shadow-xl' : '' }}">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gkk/10 text-gkk">
          <i class="fa fa-{{ $fact['icon'] }}"></i>
        </div>
        <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $fact['label'] }}</p>
        <p class="mt-1 text-lg font-bold text-gray-900">{{ $fact['value'] }}</p>
        <p class="mt-0.5 text-sm text-gray-500">{{ $fact['note'] }}</p>
      </{{ isset($fact['href']) ? 'a' : 'div' }}>
    @endforeach
  </div>
</section>

{{-- Om tävlingen --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
    <div>
      {{-- Startlistor --}}
      <a href="{{ $startlistsUrl }}" target="_blank" class="group mb-10 flex items-center gap-5 rounded-3xl bg-gkk p-6 text-white shadow-xl shadow-gkk/20 transition hover:-translate-y-0.5 hover:shadow-2xl sm:p-7">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-2xl ring-1 ring-white/30">
          <i class="fa fa-list-ol"></i>
        </div>
        <div class="flex-1">
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Lördag 14 november</p>
          <p class="mt-1 text-2xl font-extrabold tracking-tight">Startlistor</p>
          <p class="mt-1 text-sm text-white/80">Grupper, invägning och starttider</p>
        </div>
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-gkk transition group-hover:translate-x-0.5">
          <i class="fa fa-arrow-right"></i>
        </span>
      </a>

      <x-section-heading eyebrow="Om tävlingen" title="Distriktsmästerskapen i Styrkelyft 2026" />
      <div class="mt-6 space-y-4 text-lg leading-relaxed text-gray-600">
        <p>
          Lördagen den 14 november avgörs distriktsmästerskapen i klassisk styrkelyft hos oss på Friskis & Svettis Majorna. Tävlingen arrangeras av Göteborg Kraftsportklubb tillsammans med Västra Götalands Styrkelyftförbund.
        </p>
        <p>
          Det tävlas i senior-, ungdoms- och juniorklasserna i samtliga viktklasser för damer och herrar. Lyftare som är få i sin viktklass tävlar mot varandra i en koefficientklass.
        </p>
        <p>
          Publik är varmt välkommen hela dagen – kom och heja fram distriktets nya mästare!
        </p>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <img src="{{ $img }}bjorn_och_klas-min.jpeg" alt="" class="h-48 w-full rounded-2xl object-cover shadow-lg sm:h-64">
      <img src="{{ $img }}bankpress-min.jpg" alt="" class="mt-8 h-48 w-full rounded-2xl object-cover shadow-lg sm:h-64">
      <img src="{{ $img }}tavling-min.jpg" alt="" class="h-48 w-full rounded-2xl object-cover shadow-lg sm:h-64">
      <img src="{{ $img }}clara-min.jpg" alt="" class="mt-8 h-48 w-full rounded-2xl object-cover shadow-lg sm:h-64">
    </div>
  </div>
</section>

{{-- Inbjudan --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <a href="/dm2026/inbjudan.pdf" target="_blank" class="group flex flex-col gap-6 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:shadow-lg sm:flex-row sm:items-center sm:p-8">
    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gkk/10 text-2xl text-gkk">
      <i class="fa fa-file-pdf-o"></i>
    </div>
    <div class="flex-1">
      <p class="text-xs font-semibold uppercase tracking-wider text-gkk-light">För föreningar och lyftare</p>
      <h2 class="mt-1 text-2xl font-bold text-gray-900">Inbjudan till DM 2026</h2>
      <p class="mt-1 text-gray-600">All information om anmälan, avgifter och tider finns i inbjudan.</p>
    </div>
    <span class="inline-flex shrink-0 items-center gap-2 self-start rounded-full bg-gkk px-5 py-2.5 font-semibold text-white transition group-hover:bg-gkk-light sm:self-center">
      Öppna PDF <i class="fa fa-external-link"></i>
    </span>
  </a>
</section>

{{-- Praktisk info --}}
<section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="På plats" title="Praktisk information" />
  <div class="mt-10 grid gap-6 md:grid-cols-3">
    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-900/5">
      <i class="fa fa-map-marker text-2xl text-gkk"></i>
      <h3 class="mt-4 font-bold text-gray-900">Hitta hit</h3>
      <p class="mt-2 text-gray-600">Friskis & Svettis Majorna, Karl Johansgatan 152, 414 51 Göteborg.</p>
    </div>
    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-900/5">
      <i class="fa fa-coffee text-2xl text-gkk"></i>
      <h3 class="mt-4 font-bold text-gray-900">Kiosk</h3>
      <p class="mt-2 text-gray-600">Mackor, korv och dryck finns att köpa på plats. Restauranger och livsmedelsbutik ligger 5–10 minuters promenad bort.</p>
    </div>
    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-900/5">
      <i class="fa fa-envelope-o text-2xl text-gkk"></i>
      <h3 class="mt-4 font-bold text-gray-900">Frågor?</h3>
      <p class="mt-2 text-gray-600">
        Kontakta Maximus Andersson<br>
        <a href="tel:+46706690195" class="font-semibold text-gkk hover:text-gkk-light">070-669 01 95</a><br>
        <a href="mailto:maximus@goteborgkk.se" class="font-semibold text-gkk hover:text-gkk-light">maximus@goteborgkk.se</a>
      </p>
    </div>
  </div>
</section>

@endsection
