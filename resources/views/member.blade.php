@extends('layouts.app')

@section('content')
@php
  $link = 'font-medium text-gkk underline decoration-gkk/30 underline-offset-4 transition-colors hover:decoration-gkk';
  $friskis = 'https://www.friskissvettis.se/goteborg/tranahar/majorna';
@endphp

<x-page-hero image="https://goteborg-kraftsportklubb.web.app/img/kamratskap.jpeg" position="30%" eyebrow="Styrka bygger man tillsammans" title="Medlemskap">
  Som medlem i GKK får du möjlighet att träna i vår fina lokal, delta i våra gemensamma aktiviteter och representera föreningen vid tävling. Man behöver också ställa upp vid några tillfällen varje år då vi själva arrangerar tävlingar, och som ny får man naturligtvis mycket hjälp.
</x-page-hero>

{{-- Så blir du medlem --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <x-section-heading eyebrow="Kom igång" title="Vill du bli medlem?" />

  <ol class="mt-12 grid gap-6 lg:grid-cols-3">
    <li class="relative rounded-2xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gkk text-lg font-bold text-white">1</div>
      <h3 class="mt-5 text-xl font-bold text-gray-900">Provträna</h3>
      <p class="mt-3 leading-relaxed text-gray-600">
        Innan du behöver bestämma dig om medlemskap är du välkommen att provträna hos oss några gånger för att titta på lokalen och träffa medlemmar. Om du känner någon i GKK kan du följa med den personen på ett pass. Annars kan du kontakta oss på <a href="mailto:info@gkk-styrkelyft.se" class="{{ $link }}">info@gkk-styrkelyft.se</a> och skriva att du vill provträna och när.
      </p>
      <div class="mt-5 flex gap-3 rounded-xl bg-gkk/5 p-4 text-sm text-gray-700">
        <i class="fa fa-lightbulb-o mt-0.5 text-lg text-gkk"></i>
        <span>Vill du chansa kan du dyka upp en vardag från kl 16 i vår lokal, då det nästan alltid finns medlemmar på plats.</span>
      </div>
    </li>
    <li class="relative rounded-2xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gkk text-lg font-bold text-white">2</div>
      <h3 class="mt-5 text-xl font-bold text-gray-900">Anmäl dig</h3>
      <p class="mt-3 leading-relaxed text-gray-600">
        Börja med att läsa den information som finns här på vår hemsida, och kom till oss och provträna. När du har bestämt dig återkommer du till <a href="mailto:info@gkk-styrkelyft.se" class="{{ $link }}">info@gkk-styrkelyft.se</a> och berättar att du vill bli medlem samt skickar med adress, namn, personnummer, telefon och mailadress. Om du är student så skickar du även med bevis på det (ex. bild från Mecenat-appen eller registreringsbevis).
      </p>
    </li>
    <li class="relative rounded-2xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gkk text-lg font-bold text-white">3</div>
      <h3 class="mt-5 text-xl font-bold text-gray-900">Betala och kom igång</h3>
      <p class="mt-3 leading-relaxed text-gray-600">
        Efter detta kommer faktura för medlemskap skickas ut och när den är betald är du upptagen i föreningen.
      </p>
      <p class="mt-3 leading-relaxed text-gray-600">
        Du kommer också bjudas in till vår medlemswebb där du bland annat behöver godkänna vårt <a class="{{ $link }}" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6">antidopingavtal</a> och <a class="{{ $link }}" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188">medlemsavtal</a>.
      </p>
    </li>
  </ol>
</section>

{{-- Avgifter --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <x-section-heading eyebrow="Avgifter" title="Vad kostar det?" />
    <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm text-amber-800 ring-1 ring-amber-200">
      <i class="fa fa-info-circle"></i> Notera att SSF har höjt licensavgiften för 2026
    </div>
  </div>

  <div class="mt-10 grid gap-6 lg:grid-cols-3">
    <div class="flex flex-col rounded-3xl bg-gkk p-8 text-white shadow-xl shadow-gkk/20">
      <div class="flex items-center gap-3">
        <i class="fa fa-id-card text-gkk-lightest"></i>
        <h3 class="font-semibold">Medlemsavgift (inkl träningsavgift)</h3>
      </div>
      <p class="mt-6"><span class="text-5xl font-extrabold tracking-tight">1500</span> <span class="text-white/70">kr/år</span></p>
      <p class="text-white/70">för seniorer</p>
      <p class="mt-4"><span class="text-3xl font-extrabold tracking-tight">700</span> <span class="text-white/70">kr/år</span></p>
      <p class="text-white/70">för studerande, ungdom/junior (upp till året man fyller 23) och pensionärer</p>
      <p class="mt-auto border-t border-white/15 pt-5 text-sm leading-relaxed text-white/70">
        Inkluderar träningsavgift och ger tillgång till föreningens lokal och utrustning. Entré till Friskis krävs också.
      </p>
    </div>

    <div class="flex flex-col rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex items-center gap-3">
        <i class="fa fa-trophy text-gkk"></i>
        <h3 class="font-semibold text-gray-900">Tävlingslicens 2026</h3>
      </div>
      <p class="mt-6"><span class="text-5xl font-extrabold tracking-tight text-gkk">1050</span> <span class="text-gray-500">kr/år</span></p>
      <p class="text-gray-500">för övriga</p>
      <p class="mt-4"><span class="text-3xl font-extrabold tracking-tight text-gkk">300</span> <span class="text-gray-500">kr</span></p>
      <p class="text-gray-500">för ungdomar (året man fyller 18)</p>
      <div class="mt-5 rounded-xl bg-green-50 px-3 py-2 text-sm font-medium text-green-800 ring-1 ring-green-200">
        GKK subventionerar licensen med 300 kr för alla upp till 25 år
      </div>
      <p class="mt-auto border-t border-gray-100 pt-5 text-sm leading-relaxed text-gray-500">
        Betalas endast om man vill tävla. Faktura skickas ut i samband med årets första tävling.
      </p>
    </div>

    <div class="flex flex-col rounded-3xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex items-center gap-3">
        <i class="fa fa-percent text-gkk"></i>
        <h3 class="font-semibold text-gray-900">Träningskort hos Friskis</h3>
      </div>
      <p class="mt-6"><span class="text-5xl font-extrabold tracking-tight text-gkk">15%</span></p>
      <p class="text-gray-500">rabatt för GKK-medlemmar</p>
      <p class="mt-auto border-t border-gray-100 pt-5 text-sm leading-relaxed text-gray-500">
        Se <a target="_blank" class="{{ $link }}" href="{{ $friskis }}">Friskis och Svettis i Majorna</a>.
      </p>
    </div>
  </div>

  <div class="mt-6 flex items-start gap-4 rounded-2xl bg-white p-6 shadow-md shadow-gkk/5 ring-1 ring-gray-900/5">
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gkk/10 text-gkk">
      <i class="fa fa-clock-o"></i>
    </div>
    <div>
      <h3 class="font-semibold text-gray-900">Öppettider</h3>
      <p class="mt-1 leading-relaxed text-gray-600">
        Se <a target="_blank" class="{{ $link }}" href="{{ $friskis }}">Friskis och Svettis i Majornas</a> hemsida för bemannade öppetider. Som medlem kan man även komma in på obemannade tider.
      </p>
    </div>
  </div>
</section>

{{-- Vill du tävla? --}}
<section class="relative isolate mt-24 overflow-hidden bg-gkk py-24">
  <div class="absolute -left-24 top-0 -z-10 h-96 w-96 rounded-full bg-white/5 blur-2xl"></div>
  <div class="absolute -right-24 bottom-0 -z-10 h-96 w-96 rounded-full bg-gkk-light/60 blur-3xl"></div>
  <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8">
    <div>
      <x-section-heading eyebrow="Tävling" title="Vill du tävla?" dark />
      <div class="mt-6 space-y-4 text-lg leading-relaxed text-white/80">
        <p>
          Många av våra medlemmar tävlar i styrkelyft och bänkpress på olika nivåer, allt ifrån serietävlingar på hemmaplan till internationella mästerskap. Att tävla och utmana sig själv är ett fantastiskt sätt att utvecklas inom sporten, oavsett ambitionsnivå.
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

    <div class="lg:pt-16">
      <div class="rounded-3xl bg-white p-8 shadow-2xl">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gkk text-white">
          <i class="fa fa-graduation-cap text-xl"></i>
        </div>
        <h3 class="mt-5 text-xl font-bold text-gray-900">Ren Vinnare-utbildning</h3>
        <p class="mt-3 leading-relaxed text-gray-600">
          Innan du gör din första tävling för GKK måste du genomföra utbildningen <strong class="text-gray-800">Ren Vinnare</strong> som du finner på <a class="{{ $link }}" target="_blank" href="https://www.renvinnare.se">www.renvinnare.se</a>. När utbildningen är genomförd får du ett diplom som du ska maila in till <a href="mailto:info@gkk-styrkelyft.se" class="{{ $link }}">info@gkk-styrkelyft.se</a>.
        </p>
        <div class="mt-5 flex gap-3 rounded-xl bg-yellow-50 p-4 text-sm text-yellow-800 ring-1 ring-yellow-200">
          <i class="fa fa-exclamation-triangle mt-0.5"></i>
          <span>Diplomet ska vara klubben tillhanda innan du gör din första tävling (obligatoriskt för att få tävla).</span>
        </div>
        <p class="mt-5 leading-relaxed text-gray-600">
          Även om du inte har för avsikt att tävla så rekommenderar vi denna utbildning då den är mycket informativ och bra för alla idrottare. I vår anmälningsportal kommer du också få läsa och godkänna vårt antidopingavtal.
        </p>
      </div>
    </div>
  </div>
</section>

{{-- Antidopingarbete --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <div class="grid items-start gap-8 lg:grid-cols-3 lg:gap-16">
    <x-section-heading eyebrow="Ren idrott" title="Antidopingarbete" />
    <p class="text-lg leading-relaxed text-gray-600 lg:col-span-2">
      Vi arbetar förebyggande för en ren idrott och har ett löpande antidopingarbete genom vår <a class="{{ $link }}" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FKdSJ2FmrTSijNpxOXVGx0aNtERwoe6.pdf?alt=media&token=9fe3519c-7f6a-473e-851f-897561086e31">antidopingplan</a> och med en utsett antidopingansvarig. Alla medlemmar får skriva på ett antidopingavtal, och före första tävlingen behöver alla genomföra onlineutbildningen <a class="{{ $link }}" target="_blank" href="https://www.renvinnare.se">Ren Vinnare</a> genom Antidoping Sverige. Det genomförs också kontinuerligt dopingkontroller både vid tävling och träning.
    </p>
  </div>
</section>

<x-photo-gallery />
@endsection
