@extends('layouts.app')

@section('content')
@php
  $storage = 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/';
  $links = [
    ['fa-globe', 'Svenska Styrkelyftsförbundet (SSF)', 'https://styrkelyft.se'],
    ['fa-file-text-o', 'Dokument för tävling mm på förbundets hemsida', 'https://www.styrkelyft.se/dokument-policys/'],
    ['fa-database', 'Databasen för ranking och resultatregistrering', 'https://data.styrkelyft.se/'],
  ];
  $documents = [
    ['Antidopingavtal', 'Mellan medlem och förening', $storage.'documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6'],
    ['Antidopingplan', '2026-2028', $storage.'uploaded%2FKdSJ2FmrTSijNpxOXVGx0aNtERwoe6.pdf?alt=media&token=9fe3519c-7f6a-473e-851f-897561086e31'],
    ['Medlemsavtal', 'Mellan medlem och förening', $storage.'documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188'],
    ['Föreningens stadgar', 'Antagna 2022-02-19', $storage.'uploaded%2FN0TGPEf5Nto75XNm2Nux7Lpojez7pz.pdf?alt=media&token=3204252d-8c0c-43f0-baa2-de7b11ca277c'],
    ['Integritetspolicy', 'Föreningens policy', 'https://goteborg-kraftsportklubb.web.app/img/gkk-integritetspolicy.pdf'],
    ['Samarbete med SportRehab', 'Läs mer om samarbetet (PDF)', $storage.'uploaded%2FpUSeLCEIjkrZnfyjNXw7DEIrrlsBTH.pdf?alt=media&token=97fb01f5-6a40-4c7d-9d2e-8605b1893163'],
  ];
  $programs = [
    ['Beard Press', 'Ett bänkpressprogram av Karl Malmberg (@kraftkarlos)', 'https://goteborg-kraftsportklubb.web.app/img/beardpress-logo-excel.png', $storage.'documents%2FBeard-Press%202021-02-14.xlsx?alt=media&token=30924370-12aa-4ddf-8bf1-2fd45cc70717'],
    ['Carl Öbergs bänkpressprogram', 'Hög volym och hög intensitet. Riktar sig till dig med år av dedikerad bänkpressträning.', 'https://goteborg-kraftsportklubb.web.app/img/hemside-bild.png', $storage.'documents%2FBa%CC%88nkprogram%20CalleO%CC%88berg.xlsx?alt=media&token=731f217f-fe83-4950-88b6-402515862445'],
  ];
  $logos = [
    ['Tvåfärg mot ljus bakgrund', false, $storage.'static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-ljus-bakgrund.png?alt=media&token=be907bc2-2491-45e6-ae62-a5a78b080d41'],
    ['Tvåfärg mot mörk bakgrund', true, $storage.'static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-300x300.png?alt=media&token=f1942878-84fa-4519-a051-08bbec625fa7'],
    ['Tvåfärg transparent', true, $storage.'static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-transparent.png?alt=media&token=def700f9-01c7-4786-a246-28475a5e32f1'],
    ['Monokrom mot mörk bakgrund', true, $storage.'static%2FMonokrom-pa%CC%8A-mo%CC%88rk-bakgrund.png?alt=media&token=aa0b2c8a-4059-42ed-a36e-7436f7b24a39'],
  ];
  $printFiles = [
    ['Monokrom för tryck (PDF)', $storage.'static%2FMonokrom-pa%CC%8A-mo%CC%88rk-bakgrund.pdf?alt=media&token=f49672e6-dc0b-4dcc-a973-bc7a2e47ffd4'],
    ['Tvåfärg för tryck (PDF)', $storage.'static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund.pdf?alt=media&token=e2779fa9-486f-491f-81ed-14d1b1d0d15b'],
  ];
@endphp

<x-page-hero image="https://goteborg-kraftsportklubb.web.app/img/erik-boj.jpeg" eyebrow="Resurser" title="Dokument & Länkar" />

{{-- Länkar --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="Förbundet" title="Länkar" />
  <div class="mt-8 grid gap-4 md:grid-cols-3">
    @foreach ($links as [$icon, $title, $url])
      <a href="{{ $url }}" target="_blank" class="group flex flex-col rounded-2xl bg-white p-6 shadow-md shadow-gkk/5 ring-1 ring-gray-900/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gkk/10 text-gkk transition-colors group-hover:bg-gkk group-hover:text-white">
          <i class="fa {{ $icon }}"></i>
        </div>
        <h3 class="mt-4 flex-1 font-semibold text-gray-900">{{ $title }}</h3>
        <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-gkk">
          Besök <i class="fa fa-arrow-right transition-transform group-hover:translate-x-1"></i>
        </span>
      </a>
    @endforeach
  </div>
</section>

{{-- Dokument --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="Föreningen" title="Dokument" />
  <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($documents as [$title, $description, $url])
      <a href="{{ $url }}" target="_blank" class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-md shadow-gkk/5 ring-1 ring-gray-900/5 transition duration-300 hover:shadow-xl hover:ring-gkk/20">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
          <i class="fa fa-file-pdf-o text-lg"></i>
        </div>
        <div class="min-w-0 flex-1">
          <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-gkk">{{ $title }}</h3>
          <p class="text-sm text-gray-500">{{ $description }}</p>
        </div>
        <i class="fa fa-external-link text-gray-300 transition-colors group-hover:text-gkk"></i>
      </a>
    @endforeach
  </div>
</section>

{{-- Träningsprogram --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="Träning" title="Träningsprogram" />
  <div class="mt-8 grid gap-6 md:grid-cols-2">
    @foreach ($programs as [$title, $description, $image, $url])
      <a href="{{ $url }}" target="_blank" class="group flex items-center gap-5 rounded-2xl bg-white p-6 shadow-md shadow-gkk/5 ring-1 ring-gray-900/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
        <img src="{{ $image }}" alt="{{ $title }}" class="h-20 w-20 shrink-0 rounded-xl object-cover shadow-sm">
        <div class="min-w-0 flex-1">
          <h3 class="font-bold text-gray-900 transition-colors group-hover:text-gkk">{{ $title }}</h3>
          <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ $description }}</p>
          <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-200">
            <i class="fa fa-file-excel-o"></i> Excelfil
          </span>
        </div>
        <i class="fa fa-download text-gray-300 transition-colors group-hover:text-gkk"></i>
      </a>
    @endforeach
  </div>
</section>

{{-- Grafik --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <x-section-heading eyebrow="Logotyper" title="Grafik" />
    <div class="flex flex-wrap gap-3">
      @foreach ($printFiles as [$title, $url])
        <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-900/10 transition hover:text-gkk hover:ring-gkk/40">
          <i class="fa fa-file-pdf-o text-red-600"></i> {{ $title }}
        </a>
      @endforeach
    </div>
  </div>
  <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
    @foreach ($logos as [$title, $dark, $url])
      <div class="flex flex-col items-center rounded-2xl p-6 text-center shadow-md ring-1 {{ $dark ? 'bg-gray-900 ring-white/10' : 'bg-white ring-gray-900/5' }}">
        <img src="{{ $url }}" alt="{{ $title }}" class="h-24 w-24 object-contain">
        <p class="mt-4 text-sm {{ $dark ? 'text-white/70' : 'text-gray-600' }}">{{ $title }}</p>
      </div>
    @endforeach
  </div>
</section>

<x-photo-gallery />
@endsection
