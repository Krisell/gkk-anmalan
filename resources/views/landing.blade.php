@extends('layouts.app')

@section('content')
@php
  $img = 'https://goteborg-kraftsportklubb.web.app/img/';
@endphp

{{-- Hero --}}
<section class="relative isolate overflow-hidden bg-gkk">
  <img src="{{ $img }}bella-min.jpg" alt="" class="landing-hero-image absolute inset-0 -z-20 h-full w-full object-cover object-center">
  <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gkk via-gkk/70 to-black/40"></div>
  <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/50 to-transparent"></div>

  <div class="mx-auto flex max-w-7xl flex-col justify-end px-4 pt-14 pb-14 sm:h-[64svh] sm:min-h-[480px] sm:max-h-[680px] sm:px-6 sm:pt-20 sm:pb-24 lg:px-8">
    <div class="landing-rise max-w-3xl">
      <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.15em] sm:text-xs sm:tracking-[0.25em] text-white shadow-sm ring-1 ring-white/40 backdrop-blur-md">
        <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
        Styrkelyft i Göteborg sedan 1933
      </div>
      <h1 class="text-[2.75rem] font-extrabold leading-[0.95] tracking-tight text-white drop-shadow-lg sm:text-6xl lg:text-7xl">
        Göteborg<br>
        Kraftsportklubb
      </h1>
      <p class="mt-6 max-w-xl text-lg leading-relaxed text-white/85 sm:text-xl">
        Knäböj, bänkpress och marklyft – från motionärer till elitaktiva på högsta nivå. Vi tränar tillsammans och hjälper varandra.
      </p>
      <div class="mt-10 flex flex-wrap gap-4">
        <a href="/medlem" class="group inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-semibold text-gkk shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:shadow-xl">
          Bli medlem
          <i class="fa fa-arrow-right transition-transform group-hover:translate-x-1"></i>
        </a>
        <a href="/styrkelyft" class="inline-flex items-center gap-2 rounded-full px-6 py-3 font-semibold text-white ring-1 ring-white/40 backdrop-blur-sm transition hover:bg-white/10 hover:ring-white/70">
          Vad är styrkelyft?
        </a>
      </div>
    </div>
  </div>
</section>

{{-- Aktuellt --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <div class="mb-8 flex items-end justify-between gap-4">
    <x-section-heading title="Aktuellt" />
    <a href="https://www.instagram.com/goteborgkk/" target="_blank" class="hidden shrink-0 items-center gap-2 font-semibold text-gkk transition-colors hover:text-gkk-light sm:inline-flex">
      <i class="fa fa-instagram text-lg"></i> Följ oss på Instagram
    </a>
  </div>

  <div class="space-y-6">
    <article class="group relative grid overflow-hidden rounded-3xl bg-gkk shadow-xl shadow-gkk/20 md:grid-cols-5">
      <div class="relative h-56 overflow-hidden md:col-span-2 md:h-auto">
        <img src="{{ $img }}tavling-min.jpg" alt="" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-gkk/80 to-transparent md:bg-gradient-to-r md:from-transparent md:to-gkk"></div>
      </div>
      <div class="relative p-6 sm:p-10 md:col-span-3">
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="relative">
          <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white">
            <i class="fa fa-calendar"></i> 15 februari 2026
          </div>
          <h3 class="text-2xl font-bold text-white sm:text-3xl">Prova på-tävling i styrkelyft</h3>
          <p class="mt-3 text-base leading-relaxed text-white/85 sm:text-lg">
            Tack till alla tävlande och GKKare som ställde upp som funktionärer! Fler prova-på-tävlingar kommer, men är du sugen på Styrkelyft så föreslår vi att du kommer och provtränar hos oss.
          </p>
          <a href="/medlem" class="mt-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-5 py-2.5 font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/20">
            Kom och provträna <i class="fa fa-arrow-right transition-transform group-hover:translate-x-1"></i>
          </a>
        </div>
      </div>
    </article>

    {{-- Årsmöte 2026 - kommenterat efter avslutat årsmöte, återanvänd nästa år
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-700 to-gray-800 p-6 sm:p-8 shadow-lg">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white/5"></div>
        <div class="relative">
          <div class="inline-block bg-white/20 text-white text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full mb-4">
            <i class="fa fa-calendar mr-1"></i> 7 februari 2026 kl 10:00
          </div>
          <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
            Årsmöte 2026
          </h3>
          <p class="text-white/90 text-base sm:text-lg leading-relaxed mb-6">
            Kallelse och föredragningslista för GKKs årsmöte. Alla medlemmar är välkomna!
          </p>
          <div class="sm:space-y-3 space-y-5">
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FDszgogxmrxsz3hOJauVM8p7gwj6Hnw.pdf?alt=media&token=523767fd-aeee-4167-92d2-f502aa1d4662" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Kallelse och föredragningslista
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FTdyPoE0TYusCkmhZaqkw5tGyxa3eAA.pdf?alt=media&token=3ee8858a-678a-4e98-984b-2c70e9e4aa7d" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Motion: Arvode till styrelsemedlemmar
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FYYfvl5TC5vL5NvdQNlJoVrHVnkYBDD.pdf?alt=media&token=7f664c81-54ab-4b52-a6de-825d6eb46172" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Verksamhetsberättelse 2025
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FQSdFJd0OmEBcmCioe60ug4X2whj7AO.pdf?alt=media&token=7d841940-f7f0-4be9-a19b-bb2dfb558475" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Verksamhetsplan 2026
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FSoeoOPllsyCQVpfGS9m8aQkSr9jiCq.pdf?alt=media&token=97ca8ceb-5d70-41ef-9eff-b4d9c25d1308" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Revisionsberättelse 2025
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FpB9uYlSoXO7hip1x0BeC2saMChsyTm.pdf?alt=media&token=4e4cbe69-d45f-459c-bd16-617e600c8392" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Valberedningens förslag 2026
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FpjYLYN7NyUgTlxW4Ypd9VgnblvtXUa.pdf?alt=media&token=27da4b32-b0d0-4a8b-bc8d-d2190220e3ec" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Budgetbeskrivning 2026
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FkDHYic7TOoCdqWHnzG4P68UOcWP1xh.pdf?alt=media&token=5690eb30-56b5-4497-bade-606bb88cccb6" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Budget 2026
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2F0ZHr5N4dJ9hMCwrqQbFmyIZ9ZLxoDY.pdf?alt=media&token=67f4c436-8e05-4050-85a8-da7d7f4783bc" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Resultaträkning
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FKU6Ci1znpmcXkYj4Y1LmbhbVAbgE3G.pdf?alt=media&token=d7b4caef-ab40-45e6-abe2-5781f40cd3e7" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Balansräkning
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
            <div>
              <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FAr9V5mNTs8BwsXTEDjITQlyCMpGwdj.pdf?alt=media&token=ad278c2d-ceef-439e-a304-e9e4afc1141d" target="_blank" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:translate-x-1">
                <i class="fa fa-file-pdf-o mr-2"></i>
                Ekonomisk berättelse
                <i class="fa fa-external-link ml-2 text-sm"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    --}}
    {{-- <div>
      <a 
        target="_blank"
        class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-hidden focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2Fn3Gc8wTbI8OygNYumrXnwoDd8p7VUp.pdf?alt=media&token=dd7cb0d9-9d0e-463a-b8d1-31f029219478">
        <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsberättelse 2024
      </a>
    </div> --}}
    {{-- <div>
      <a 
        target="_blank"
        class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-hidden focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FVerksamhetsplan%20och%20budget%202024.pdf?alt=media&token=d15bbc76-f0dd-4d2a-a678-11b60fe2c79d">
        <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsplan och budget 2024
      </a>
    </div> --}}
    {{-- <div>
      <a 
        target="_blank"
        class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-hidden focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FEkonomiskt%20resultat%202023.pdf?alt=media&token=990c709a-2ad8-41cb-997f-5898c306db36">
        <i class="fa fa-file-pdf-o mr-2"></i>Ekonomiskt resultat 2023
      </a>
    </div> --}}
    {{-- <div>
      <a 
        target="_blank"
        class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-hidden focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FEuEFEZcycAXmSp1d1JJjc5VEbv1lgh.pdf?alt=media&token=3687af19-bfaa-4d65-9826-c4a646d70b79">
        <i class="fa fa-file-pdf-o mr-2"></i>Valberedningens förslag för 2025
      </a>
    </div>
    <div>
      <a 
        target="_blank"
        class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-hidden focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FocGMIpBbUbFUteTxgxr5Bu5bJKbrvZ.pdf?alt=media&token=4f9b5ff7-f004-4fc2-bb2f-e821414aa589">
        <i class="fa fa-file-pdf-o mr-2"></i>Revisionsberättelse 2024
      </a>
    </div>
    <div>
      <a 
        target="_blank"
        class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-hidden focus:shadow-outline-indigo transition duration-150 ease-in-out" 
        href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FXSlmDdMsbMNAUo2SkErTwfIzf93sDQ.pdf?alt=media&token=6346e687-ffac-4e86-8111-7641adf5aae1">
        <i class="fa fa-file-pdf-o mr-2"></i>Motion (Veteranutskott) och styrelsens yttrande
      </a>
    </div>
    <div>
      <p class="text-xl leading-normal text-black">
        Ytterligare möteshandlingar hittar medlemmar på "Insidan" under "Dokument".
      </p>
    </div> --}}

    

    {{-- <a href="/gm" class="mt-6 block">
      <div class="rounded-sm p-2 border-gkk border-2">
        <p class="text-3xl leading-bold text-gkk">
          Götalandsmästerskapen 10-12 maj 2024
        </p>
        <p class="text-xl leading-bold text-gkk">
          GKK arrangerar Götalandsmästerskapen i Klassisk Styrkelyft och Klassisk Bänkpress!<br>Klicka här för att läsa med på vår GM-sida.
        </p>
      </div>
    </a> --}}
  </div>
</section>

{{-- Om klubben --}}
<section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
  <div class="grid items-center gap-16 lg:grid-cols-2">
    <div>
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gkk-light">Om GKK</p>
      <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Styrka kommer från gemenskap</h2>
      <div class="mt-6 space-y-5 text-lg leading-relaxed text-gray-600">
        <p>
          Göteborg Kraftsportklubb (GKK) är en idrottsförening som bildades 1933. Vi ägnar oss åt idrotten Styrkelyft och tränar och tävlar därmed i knäböj, bänkpress och marklyft. I föreningen finns allt från motionärer till elitaktiva på högsta nivå. Vi tränar tillsammans, hjälper varandra och tror på att styrka kommer från gemenskap.
        </p>
        <p>
          Sedan december 2018 har vi vår egna klubb- och träningslokal hos <a class="font-medium text-gkk underline decoration-gkk/30 underline-offset-4 transition-colors hover:decoration-gkk" href="https://www.friskissvettis.se/goteborg/harfinnsvi/majorna" target="_blank">Friskis & Svettis Majorna</a> i Göteborg. För att träna hos oss behöver du därmed också vara medlem och ha träningskort hos Friskis & Svettis. Träning kan ske även under obemannade tider genom att registrera en kod i kassan. Mer information om du är intresserad av att bli medlem i GKK hittar du under <a class="font-medium text-gkk underline decoration-gkk/30 underline-offset-4 transition-colors hover:decoration-gkk" href="/medlem">Medlemskap</a>.
        </p>
      </div>
    </div>

    <div class="relative mx-auto w-full max-w-lg lg:max-w-none">
      <div class="absolute -inset-4 -z-10 rotate-2 rounded-[2rem] bg-gradient-to-br from-gkk/10 to-gkk-lightest/30"></div>
      <div class="grid grid-cols-5 gap-4">
        <img src="{{ $img }}bjorn_och_klas-min.jpeg" alt="Lyftare går in på flaket" class="col-span-3 h-80 w-full rounded-2xl object-cover shadow-lg sm:h-96">
        <img src="{{ $img }}clara-min.jpg" alt="" class="col-span-2 mt-12 h-80 w-full rounded-2xl object-cover shadow-lg sm:h-96">
      </div>
      <div class="absolute -bottom-6 left-6 flex items-center gap-4 rounded-2xl bg-white px-5 py-4 shadow-xl ring-1 ring-gray-900/5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gkk text-white">
          <i class="fa fa-map-marker text-xl"></i>
        </div>
        <div>
          <div class="text-sm text-gray-500">Vi tränar på</div>
          <div class="font-semibold text-gray-900">Friskis & Svettis Majorna</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Tre lyft --}}
<section class="relative isolate overflow-hidden bg-gkk py-24">
  <div class="absolute -left-24 top-0 -z-10 h-96 w-96 rounded-full bg-white/5 blur-2xl"></div>
  <div class="absolute -right-24 bottom-0 -z-10 h-96 w-96 rounded-full bg-gkk-light/60 blur-3xl"></div>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gkk-lightest">Styrkelyft</p>
      <h2 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Tre lyft – eller bara bänk</h2>
      <p class="mt-4 text-lg text-white/75">I styrkelyft tävlar man i tre grenar och den som lyfter tyngst sammanlagt vinner. Bänkpress är dessutom en egen tävlingsform, så du kan välja att tävla i enbart bänkpress.</p>
    </div>
    <div class="mt-12 grid gap-6 md:grid-cols-3">
      @foreach ([
        ['01', 'Knäböj', 'Med stången på ryggen går du ner tills höftleden är under knäets överkant och reser dig upp igen.', false],
        ['02', 'Bänkpress', 'Liggandes på bänken sänker du stången till bröstet, håller still och pressar upp den på domarens signal.', true],
        ['03', 'Marklyft', 'Stången lyfts från golvet tills du står helt upprätt med raka knän och bakåtdragna axlar.', false],
      ] as [$number, $lift, $description, $standalone])
        <div class="relative rounded-2xl p-8 backdrop-blur-sm transition duration-300 hover:-translate-y-1 {{ $standalone ? 'bg-white/10 ring-2 ring-white/40 hover:bg-white/15' : 'bg-white/5 ring-1 ring-white/10 hover:bg-white/10' }}">
          @if ($standalone)
            <div class="absolute right-6 top-6 inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-gkk shadow-sm">
              <i class="fa fa-trophy"></i> Egen tävlingsform
            </div>
          @endif
          <div class="text-5xl font-extrabold text-white/20">{{ $number }}</div>
          <h3 class="mt-4 text-xl font-bold text-white">{{ $lift }}</h3>
          <p class="mt-2 leading-relaxed text-white/70">{{ $description }}</p>
          @if ($standalone)
            <p class="mt-4 border-t border-white/15 pt-4 text-sm font-medium text-white/90">Tävla i bänkpress som del av styrkelyft, eller i enbart bänkpress.</p>
          @endif
        </div>
      @endforeach
    </div>
    <a href="/styrkelyft" class="mt-10 inline-flex items-center gap-2 font-semibold text-white transition-colors hover:text-gkk-lightest">
      Läs mer om styrkelyft <i class="fa fa-arrow-right"></i>
    </a>
  </div>
</section>

<x-photo-gallery />

{{-- CTA --}}
<section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
  <div class="relative isolate overflow-hidden rounded-3xl bg-gray-900 px-6 py-16 text-center shadow-2xl sm:px-16">
    <img src="{{ $img }}bjornlyftare-min.jpg" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover opacity-30">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-gkk/90 to-gray-900/90"></div>
    <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl">Sugen på att testa?</h2>
    <p class="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-white/80">
      Varmt välkommen att hälsa på och träna ett pass med oss – oavsett om du är nybörjare eller har tävlat i många år.
    </p>
    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
      <a href="/medlem" class="group inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-semibold text-gkk shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
        Bli medlem <i class="fa fa-arrow-right transition-transform group-hover:translate-x-1"></i>
      </a>
      <a href="/gkk" class="inline-flex items-center gap-2 rounded-full px-6 py-3 font-semibold text-white ring-1 ring-white/40 transition hover:bg-white/10">
        Kontakta oss
      </a>
    </div>
  </div>
</section>
@endsection
