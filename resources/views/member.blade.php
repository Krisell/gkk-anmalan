@extends('layouts.app')

@section('content')
{{-- Hero section with gradient overlay --}}
<div class="relative">
  <div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/kamratskap.jpeg);
  height: 500px;
  background-size: cover;
  background-position-y: 30%; max-height: 50vh;" class="flex items-center">
  </div>
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center px-4">
    <div class="text-center">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white drop-shadow-lg tracking-tight">Medlemskap</h1>
      <div class="mt-4 flex items-center justify-center gap-3">
        <span class="h-px w-12 bg-white/50"></span>
        <p class="text-white/80 uppercase tracking-[0.3em] text-sm drop-shadow">Styrka bygger man tillsammans</p>
        <span class="h-px w-12 bg-white/50"></span>
      </div>
    </div>
  </div>
</div>

<div class="container mx-auto max-w-4xl px-4 py-10">
  {{-- Intro section --}}
  <div class="mb-10">
    <p class="text-xl sm:text-2xl font-medium text-gkk leading-relaxed">
      Som medlem i GKK får du möjlighet att träna i vår fina lokal, delta i våra gemensamma aktiviteter och representera föreningen vid tävling.
    </p>
    <p class="text-lg leading-relaxed text-gray-600 mt-4">
      Man behöver också ställa upp vid några tillfällen varje år då vi själva arrangerar tävlingar, och som ny får man naturligtvis mycket hjälp.
    </p>
  </div>

  {{-- Become a member card --}}
  <div class="bg-gradient-to-br from-gkk to-gkk-light rounded-2xl p-6 sm:p-8 shadow-lg mb-10 text-white">
    <h2 class="text-xl font-bold mb-4 flex items-center">
      <i class="fa fa-user-plus mr-2"></i> Vill du bli medlem?
    </h2>
    <div class="space-y-4 text-white/90">
      <p class="leading-relaxed">
        Börja med att läsa den information som finns här på vår hemsida, och kom till oss och provträna (se info nedan). När du har bestämt dig återkommer du till <a href="mailto:info@gkk-styrkelyft.se" class="underline text-white hover:text-white/80 transition-colors">info@gkk-styrkelyft.se</a> och berättar att du vill bli medlem samt skickar med adress, namn, personnummer, telefon och mailadress. Om du är student så skickar du även med bevis på det (ex. bild från Mecenat-appen eller registreringsbevis).
      </p>
      <p class="leading-relaxed">
        Efter detta kommer faktura för medlemskap skickas ut och när den är betald är du upptagen i föreningen.
      </p>
      <p class="leading-relaxed">
        Du kommer också bjudas in till vår medlemswebb där du bland annat behöver godkänna vårt <a class="underline text-white hover:text-white/80 transition-colors" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6">antidopingavtal</a> och <a class="underline text-white hover:text-white/80 transition-colors" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188">medlemsavtal</a>.
      </p>
    </div>
  </div>

  {{-- Fees card --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-money mr-2"></i> Avgifter
    </h2>
    <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-lg p-3 mb-6">
      <p class="text-amber-800 text-sm">
        <i class="fa fa-info-circle mr-1"></i>
        Notera att SSF har höjt licensavgiften för 2026
      </p>
    </div>
    <div class="space-y-6">
      <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-gkk/10 rounded-lg flex items-center justify-center mr-4">
          <i class="fa fa-id-card text-gkk"></i>
        </div>
        <div>
          <h3 class="font-bold text-gray-900 mb-1">Medlemsavgift (inkl träningsavgift)</h3>
          <p class="text-gray-600">
            <span class="font-semibold text-gkk">1500 kr/år</span> för seniorer<br>
            <span class="font-semibold text-gkk">700 kr/år</span> för studerande, ungdom/junior (upp till året man fyller 23) och pensionärer
          </p>
          <p class="text-gray-500 text-sm mt-1">
            Inkluderar träningsavgift och ger tillgång till föreningens lokal och utrustning. Entré till Friskis krävs också.
          </p>
        </div>
      </div>
      <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-gkk/10 rounded-lg flex items-center justify-center mr-4">
          <i class="fa fa-trophy text-gkk"></i>
        </div>
        <div>
          <h3 class="font-bold text-gray-900 mb-1">Tävlingslicens 2026</h3>
          <p class="text-gray-600">
            <span class="font-semibold text-gkk">300 kr</span> för ungdomar (året man fyller 18)<br>
            <span class="font-semibold text-gkk">1050 kr/år</span> för övriga
          </p>
          <p class="text-gray-500 text-sm mt-1">
            Betalas endast om man vill tävla. Faktura skickas ut i samband med årets första tävling.
          </p>
          <div class="mt-2 inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">
            GKK subventionerar licensen med 300 kr för alla upp till 25 år
          </div>
        </div>
      </div>
      <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-gkk/10 rounded-lg flex items-center justify-center mr-4">
          <i class="fa fa-percent text-gkk"></i>
        </div>
        <div>
          <h3 class="font-bold text-gray-900 mb-1">Träningskort hos Friskis</h3>
          <p class="text-gray-600">
            <span class="font-semibold text-gkk">15% rabatt</span> för GKK-medlemmar
          </p>
          <p class="text-gray-500 text-sm mt-1">
            Se <a target="_blank" class="underline hover:text-gkk transition-colors" href="https://www.friskissvettis.se/goteborg/tranahar/majorna">Friskis och Svettis i Majorna</a>.
          </p>
        </div>
      </div>
    </div>
  </div>

  {{-- Trial training --}}
  <div class="mb-10">
    <h2 class="text-xl font-bold text-gkk mb-4 flex items-center">
      <i class="fa fa-handshake-o mr-2"></i> Provträning
    </h2>
    <p class="text-lg leading-relaxed text-gray-600">
      Innan du behöver bestämma dig om medlemskap är du välkommen att provträna hos oss några gånger för att titta på lokalen och träffa medlemmar. Om du känner någon i GKK kan du följa med den personen på ett pass. Annars kan du kontakta oss på <a href="mailto:info@gkk-styrkelyft.se" class="underline hover:text-gkk transition-colors">info@gkk-styrkelyft.se</a> och skriva att du vill provträna och när.
    </p>
    <div class="bg-gkk/5 border-l-4 border-gkk rounded-r-lg p-4 mt-4">
      <p class="text-gray-700">
        <i class="fa fa-lightbulb-o text-gkk mr-2"></i>
        Vill du chansa kan du dyka upp en vardag från kl 16 i vår lokal, då det nästan alltid finns medlemmar på plats.
      </p>
    </div>
  </div>

  {{-- Opening hours --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-4 flex items-center">
      <i class="fa fa-clock-o mr-2"></i> Öppettider
    </h2>
    <p class="text-gray-600 leading-relaxed">
      Se <a target="_blank" class="underline hover:text-gkk transition-colors" href="https://www.friskissvettis.se/goteborg/tranahar/majorna">Friskis och Svettis i Majornas</a> hemsida för bemannade öppetider. Som medlem kan man även komma in på obemannade tider.
    </p>
  </div>

  {{-- Competition section --}}
  <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl p-6 sm:p-8 shadow-lg mb-10 text-white">
    <h2 class="text-xl font-bold mb-6 flex items-center">
      <i class="fa fa-trophy mr-2"></i> Vill du tävla?
    </h2>
    <div class="space-y-4 text-white/90">
      <p class="leading-relaxed">
        Många av våra medlemmar tävlar i styrkelyft och bänkpress på olika nivåer, allt ifrån serietävlingar på hemmaplan till internationella mästerskap. Att tävla och utmana sig själv är ett fantastiskt sätt att utvecklas inom sporten, oavsett ambitionsnivå.
      </p>
      <p class="leading-relaxed">
        Före din första tävling behöver du vara med och hjälpa till när GKK arrangerar, både för att lära dig mer om hur en tävling går till och för att lära känna föreningen bättre. På våra interna medlemssidor sker anmälan till sådana funktionärsuppdrag. Som tävlingsaktiv förväntas du hjälpa till på våra arrangemang vid minst ett tillfälle per år.
      </p>
      <p class="leading-relaxed">
        Till förbundet betalas en tävlingslicens in varje år och denna betalar du till GKK i samband med första tävlingsanmälan (se ovan).
      </p>
      <p class="leading-relaxed">
        Anmälningsavgifter till större tävlingar står ibland föreningen för, om ekonomi finns. Ibland ges också visst bidrag för ex. resa och boende om tävlingen sker på annan ort.
      </p>
    </div>
  </div>

  {{-- Ren Vinnare --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-4 flex items-center">
      <i class="fa fa-graduation-cap mr-2"></i> Ren Vinnare-utbildning
    </h2>
    <p class="text-gray-600 leading-relaxed mb-4">
      Innan du gör din första tävling för GKK måste du genomföra utbildningen <strong class="text-gray-700">Ren Vinnare</strong> som du finner på <a class="underline hover:text-gkk transition-colors" target="_blank" href="https://www.renvinnare.se">www.renvinnare.se</a>. När utbildningen är genomförd får du ett diplom som du ska maila in till <a href="mailto:info@gkk-styrkelyft.se" class="underline hover:text-gkk transition-colors">info@gkk-styrkelyft.se</a>.
    </p>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg p-3">
      <p class="text-yellow-600 text-sm">
        <i class="fa fa-exclamation-triangle mr-1"></i>
        Diplomet ska vara klubben tillhanda innan du gör din första tävling (obligatoriskt för att få tävla).
      </p>
    </div>
    <p class="text-gray-600 leading-relaxed mt-4">
      Även om du inte har för avsikt att tävla så rekommenderar vi denna utbildning då den är mycket informativ och bra för alla idrottare. I vår anmälningsportal kommer du också få läsa och godkänna vårt antidopingavtal.
    </p>
  </div>

  {{-- Anti-doping --}}
  <div class="mb-10">
    <h2 class="text-xl font-bold text-gkk mb-4 flex items-center">
      <i class="fa fa-shield mr-2"></i> Antidopingarbete
    </h2>
    <p class="text-lg leading-relaxed text-gray-600">
      Vi arbetar förebyggande för en ren idrott och har ett löpande antidopingarbete genom vår <a class="underline hover:text-gkk transition-colors" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FKdSJ2FmrTSijNpxOXVGx0aNtERwoe6.pdf?alt=media&token=9fe3519c-7f6a-473e-851f-897561086e31">antidopingplan</a> och med en utsett antidopingansvarig. Alla medlemmar får skriva på ett antidopingavtal, och före första tävlingen behöver alla genomföra onlineutbildningen <a class="underline hover:text-gkk transition-colors" target="_blank" href="https://www.renvinnare.se">Ren Vinnare</a> genom Antidoping Sverige. Det genomförs också kontinuerligt dopingkontroller både vid tävling och träning.
    </p>
  </div>

  {{-- Image gallery --}}
  <div class="mb-8">
    <ul role="list" class="space-y-6 sm:grid sm:grid-cols-2 sm:gap-6 sm:space-y-0 lg:grid-cols-3 lg:gap-8">
      <li class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl">
          <img class="h-[280px] w-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg" alt="">
        </div>
      </li>
      <li class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl">
          <img class="h-[280px] w-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/img/bjornlyftare-min.jpg" alt="">
        </div>
      </li>
      <li class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl">
          <img class="h-[280px] w-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/img/bankpress-min.jpg" alt="">
        </div>
      </li>
      <li class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl">
          <img class="h-[280px] w-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/img/tavling-min.jpg" alt="">
        </div>
      </li>
      <li class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl">
          <img class="h-[280px] w-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/img/mark-min.jpg" alt="">
        </div>
      </li>
      <li class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl">
          <img class="h-[280px] w-full object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/img/clara-min.jpg" alt="">
        </div>
      </li>
    </ul>
  </div>
</div>
@endsection
