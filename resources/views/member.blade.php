@extends('layouts.app')

@section('content')
@php
  $link = 'font-medium text-gkk underline decoration-gkk/30 underline-offset-4 transition-colors hover:decoration-gkk';
  $friskis = 'https://www.friskissvettis.se/goteborg/tranahar/majorna';
@endphp

<x-page-hero image="https://goteborg-kraftsportklubb.web.app/img/kamratskap.jpeg" position="30%" eyebrow="Styrka bygger man tillsammans" title="Medlemskap">
  Som medlem i GKK får du träna i vår fina lokal, delta i våra gemensamma aktiviteter och representera föreningen vid tävling. Man behöver också ställa upp vid några tillfällen varje år då vi själva arrangerar tävlingar, och som ny får man naturligtvis mycket hjälp.
</x-page-hero>

{{-- Så blir du medlem --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="Kom igång" title="Vill du bli medlem?" />

  <div class="relative mt-12">
    {{-- Connecting step track on desktop --}}
    <div aria-hidden="true" class="pointer-events-none absolute top-14 left-16 right-16 hidden h-px bg-gradient-to-r from-gkk/25 via-gkk/25 to-gkk/10 lg:block"></div>

    <ol class="grid gap-6 lg:grid-cols-3">
      <li class="relative flex flex-col rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center gap-4">
          <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gkk text-lg font-bold text-white shadow-md shadow-gkk/25 ring-4 ring-white">1</div>
          <h3 class="text-xl font-bold text-gray-900">Provträna</h3>
        </div>
        <p class="mt-5 leading-relaxed text-gray-600">
          Innan du behöver bestämma dig om medlemskap är du välkommen att provträna hos oss några gånger för att titta på lokalen och träffa medlemmar. Om du känner någon i GKK kan du följa med den personen på ett pass. Annars kan du kontakta oss på <a href="mailto:info@gkk-styrkelyft.se" class="{{ $link }}">info@gkk-styrkelyft.se</a> och skriva att du vill provträna och när.
        </p>
        <div class="mt-auto pt-6">
          <div class="flex items-start gap-3 rounded-xl bg-gkk/5 p-4 text-sm leading-relaxed text-gray-700 ring-1 ring-gkk/10">
            <span class="flex h-[1.625em] shrink-0 items-center">
              <i class="fa fa-lightbulb-o text-lg text-gkk"></i>
            </span>
            <span>Vill du chansa kan du dyka upp en vardag från kl 16 i vår lokal, då det nästan alltid finns medlemmar på plats.</span>
          </div>
        </div>
      </li>

      <li class="relative flex flex-col rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center gap-4">
          <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gkk text-lg font-bold text-white shadow-md shadow-gkk/25 ring-4 ring-white">2</div>
          <h3 class="text-xl font-bold text-gray-900">Registrera dig</h3>
        </div>
        <p class="mt-5 leading-relaxed text-gray-600">
          När du har provtränat och bestämt dig för medlemskap återkommer du till <a href="mailto:info@gkk-styrkelyft.se" class="{{ $link }}">info@gkk-styrkelyft.se</a> och berättar att du vill bli medlem samt skickar med adress, namn, personnummer, telefon och mailadress. Om du är student så skickar du även med bevis på det (ex. bild från Mecenat-appen eller registreringsbevis).
        </p>
      </li>

      <li class="relative flex flex-col rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center gap-4">
          <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gkk text-lg font-bold text-white shadow-md shadow-gkk/25 ring-4 ring-white">3</div>
          <h3 class="text-xl font-bold text-gray-900">Betala och kom igång</h3>
        </div>
        <p class="mt-5 leading-relaxed text-gray-600">
          Efter detta kommer faktura för medlemskap skickas ut och när den är betald är du upptagen i föreningen.
        </p>
        <p class="mt-3.5 leading-relaxed text-gray-600">
          Du kommer också bjudas in till vår medlemswebb där du bland annat behöver godkänna vårt <a class="{{ $link }}" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6">antidopingavtal</a> och <a class="{{ $link }}" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188">medlemsavtal</a>.
        </p>
      </li>
    </ol>
  </div>
</section>

{{-- Avgifter --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="Avgifter" title="Vad kostar det?" />

  {{-- Primary fee cards --}}
  <div class="mt-10 grid gap-6 lg:grid-cols-2">
    <div class="relative isolate flex flex-col overflow-hidden rounded-3xl bg-gradient-to-br from-gkk to-slate-900 p-8 text-white shadow-xl shadow-gkk/20 sm:p-10">
      <div aria-hidden="true" class="pointer-events-none absolute -right-16 -top-16 -z-10 h-56 w-56 rounded-full bg-white/5 blur-2xl"></div>
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-gkk-lightest ring-1 ring-white/15">
          <i class="fa fa-id-card text-lg"></i>
        </div>
        <h3 class="text-lg font-bold">Medlemsavgift (inkl träningsavgift)</h3>
      </div>

      <div class="mt-8 grid gap-6 sm:grid-cols-2 sm:gap-8">
        <div>
          <p class="flex items-baseline gap-1.5">
            <span class="text-5xl font-extrabold tracking-tight">1500</span>
            <span class="text-sm font-medium text-white/70">kr/år</span>
          </p>
          <p class="mt-1.5 text-sm text-white/75">för seniorer</p>
        </div>

        <div class="border-t border-white/15 pt-5 sm:border-t-0 sm:border-l sm:pt-0 sm:pl-8">
          <p class="flex items-baseline gap-1.5">
            <span class="text-4xl font-extrabold tracking-tight sm:text-5xl">700</span>
            <span class="text-sm font-medium text-white/70">kr/år</span>
          </p>
          <p class="mt-1.5 text-sm leading-relaxed text-white/75">för studerande, ungdom/junior (upp till året man fyller 23) och pensionärer</p>
        </div>
      </div>

      <div class="mt-6 flex items-start gap-3 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium leading-relaxed text-white ring-1 ring-white/15">
        <span class="flex h-[1.625em] shrink-0 items-center">
          <i class="fa fa-calendar text-gkk-lightest"></i>
        </span>
        <span>Från och med juli reduceras avgiften med 50 % för resten av året.</span>
      </div>

      <div class="mt-auto pt-6">
        <p class="border-t border-white/15 pt-5 text-sm leading-relaxed text-white/75">
          Inkluderar träningsavgift och ger tillgång till föreningens lokal och utrustning. Entré till Friskis krävs också.
        </p>
      </div>
    </div>

    <div class="flex flex-col rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5 sm:p-10">
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gkk/10 text-gkk">
          <i class="fa fa-trophy text-lg"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Tävlingslicens 2027</h3>
      </div>

      <div class="mt-8 grid gap-6 sm:grid-cols-2 sm:gap-8">
        <div>
          <p class="flex items-baseline gap-1.5">
            <span class="text-5xl font-extrabold tracking-tight text-gkk">1050</span>
            <span class="text-sm font-medium text-gray-500">kr/år</span>
          </p>
          <p class="mt-1.5 text-sm text-gray-500">för övriga</p>
        </div>

        <div class="border-t border-gray-100 pt-5 sm:border-t-0 sm:border-l sm:pt-0 sm:pl-8">
          <p class="flex items-baseline gap-1.5">
            <span class="text-4xl font-extrabold tracking-tight text-gkk sm:text-5xl">300</span>
            <span class="text-sm font-medium text-gray-500">kr</span>
          </p>
          <p class="mt-1.5 text-sm leading-relaxed text-gray-500">för ungdomar (året man fyller 18)</p>
        </div>
      </div>

      <div class="mt-6 flex items-center gap-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 ring-1 ring-emerald-200/80">
        <i class="fa fa-check-circle shrink-0 text-emerald-600"></i>
        <span>GKK subventionerar licensen med 300 kr för alla upp till 25 år</span>
      </div>

      <div class="mt-auto pt-6">
        <p class="border-t border-gray-100 pt-5 text-sm leading-relaxed text-gray-500">
          Betalas endast om man vill tävla, och man behöver inte beställa detta i förväg. Faktura skickas ut i samband med årets första tävling.
        </p>
      </div>
    </div>
  </div>

  {{-- Secondary practical cards: Friskis discount + Opening hours --}}
  <div class="mt-6 grid gap-6 md:grid-cols-2">
    <div class="flex items-start gap-5 rounded-3xl bg-white p-7 shadow-md shadow-gkk/5 ring-1 ring-gray-900/5 sm:items-center sm:p-8">
      <div class="flex shrink-0 items-center justify-center rounded-2xl bg-gkk/10 px-4 py-3 text-3xl font-extrabold tracking-tight text-gkk sm:px-5 sm:py-3.5 sm:text-4xl">
        15%
      </div>
      <div>
        <h3 class="text-base font-bold text-gray-900">Träningskort hos Friskis</h3>
        <p class="mt-1 text-sm font-medium text-gray-700">rabatt för GKK-medlemmar</p>
        <p class="mt-1.5 text-sm text-gray-500">
          Se <a target="_blank" class="{{ $link }}" href="{{ $friskis }}">Friskis och Svettis i Majorna</a>.
        </p>
      </div>
    </div>

    <div class="flex items-start gap-4 rounded-3xl bg-white p-7 shadow-md shadow-gkk/5 ring-1 ring-gray-900/5 sm:p-8">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gkk/10 text-gkk">
        <i class="fa fa-clock-o text-xl"></i>
      </div>
      <div>
        <h3 class="text-base font-bold text-gray-900">Öppettider</h3>
        <p class="mt-1.5 leading-relaxed text-gray-600">
          Se <a target="_blank" class="{{ $link }}" href="{{ $friskis }}">Friskis och Svettis i Majornas</a> hemsida för bemannade öppetider. Som medlem kan man även komma in på obemannade tider.
        </p>
      </div>
    </div>
  </div>
</section>

{{-- Vill du tävla? --}}
<section class="relative isolate mt-24 overflow-hidden bg-gradient-to-br from-slate-900 via-gkk to-gkk py-24">
  <div aria-hidden="true" class="pointer-events-none absolute -left-24 top-0 -z-10 h-96 w-96 rounded-full bg-white/5 blur-3xl"></div>
  <div aria-hidden="true" class="pointer-events-none absolute -right-24 bottom-0 -z-10 h-96 w-96 rounded-full bg-gkk-light/50 blur-3xl"></div>
  <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:px-8">
    <div class="lg:col-span-7">
      <x-section-heading eyebrow="Tävling" title="Vill du tävla?" dark />
      <div class="mt-7 space-y-5 text-lg leading-relaxed text-white">
        <p>
          Många av våra medlemmar tävlar i styrkelyft och bänkpress på olika nivåer, allt ifrån serietävlingar på hemmaplan till internationella mästerskap. Att tävla och utmana sig själv är ett fantastiskt sätt att utvecklas inom sporten och som individ, oavsett ambitionsnivå.
        </p>
        <p>
          Före din första tävling behöver du vara med och hjälpa till när GKK arrangerar, både för att lära dig mer om hur en tävling går till och för att lära känna föreningen bättre. På våra interna medlemssidor sker anmälan till sådana funktionärsuppdrag. Som tävlingsaktiv förväntas du hjälpa till på våra arrangemang vid minst ett tillfälle per år.
        </p>
        <p>
          Till förbundet betalas en tävlingslicens in varje år och denna betalar du till GKK i samband med första tävlingsanmälan (se ovan).
        </p>
        <p>
          Anmälningsavgifter till större tävlingar står ibland föreningen för, om ekonomi finns. Ibland ges också visst bidrag för ex. resa och boende om tävlingen sker på annan ort.
        </p>
      </div>
    </div>

    <div class="lg:col-span-5">
      <div class="rounded-3xl bg-white p-8 shadow-2xl shadow-black/25 ring-1 ring-white/15 sm:p-9">
        <div class="flex items-center gap-4">
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gkk text-white shadow-md shadow-gkk/25">
            <i class="fa fa-graduation-cap text-xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900">Ren Vinnare-utbildning</h3>
        </div>
        <p class="mt-5 leading-relaxed text-gray-600">
          Innan du gör din första tävling för GKK måste du genomföra utbildningen <strong class="text-gray-800">Ren Vinnare</strong> som du finner på <a class="{{ $link }}" target="_blank" href="https://www.renvinnare.se">www.renvinnare.se</a>. När utbildningen är genomförd får du ett diplom som du ska maila in till <a href="mailto:info@gkk-styrkelyft.se" class="{{ $link }}">info@gkk-styrkelyft.se</a>.
        </p>
        <p class="mt-4 leading-relaxed text-gray-600">
          Även om du inte har för avsikt att tävla så rekommenderar vi denna utbildning då den är mycket informativ och bra för alla idrottare. I vår anmälningsportal kommer du också få läsa och godkänna vårt antidopingavtal.
        </p>
      </div>
    </div>
  </div>
</section>

{{-- Antidopingarbete --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <div class="rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5 sm:p-10 lg:p-12">
    <div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-12">
      <div class="lg:col-span-4">
        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-gkk/10 text-gkk">
          <i class="fa fa-shield text-xl"></i>
        </div>
        <x-section-heading eyebrow="Ren idrott" title="Antidopingarbete" />
      </div>
      <p class="text-lg leading-relaxed text-gray-600 lg:col-span-8 lg:pt-2">
        Vi arbetar förebyggande för en ren idrott och har ett löpande antidopingarbete genom vår <a class="{{ $link }}" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FKdSJ2FmrTSijNpxOXVGx0aNtERwoe6.pdf?alt=media&token=9fe3519c-7f6a-473e-851f-897561086e31">antidopingplan</a> och med en utsett antidopingansvarig. Alla medlemmar får skriva på ett antidopingavtal, och före första tävlingen behöver alla genomföra onlineutbildningen <a class="{{ $link }}" target="_blank" href="https://www.renvinnare.se">Ren Vinnare</a> genom Antidoping Sverige. Det genomförs också kontinuerligt dopingkontroller både vid tävling och träning.
      </p>
    </div>
  </div>
</section>

<x-photo-gallery />
@endsection
