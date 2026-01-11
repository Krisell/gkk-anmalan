@extends('layouts.app')

@section('content')
{{-- Hero section with gradient overlay --}}
<div class="relative">
  <div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/erik-boj.jpeg);
  height: 500px;
  background-size: cover;
  background-position-y: center; max-height: 50vh;" class="flex items-center">
  </div>
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center px-4">
    <div class="text-center">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white drop-shadow-lg tracking-tight">Dokument & Länkar</h1>
    </div>
  </div>
</div>

<div class="container mx-auto max-w-4xl px-4 py-10">
  {{-- Club clothes card --}}
  <div class="bg-gradient-to-br from-gkk to-gkk-light rounded-2xl p-6 sm:p-8 shadow-lg mb-10 text-white">
    <h2 class="text-xl font-bold mb-4 flex items-center">
      <i class="fa fa-shopping-bag mr-2"></i> Klubbkläder via Intersport
    </h2>
    <p class="text-white/90 mb-4">
      Beställ klubbkläder via vår webbshop hos Intersport. Se till att logga in för att få rätt pris.
    </p>
    <a href="https://team.intersport.se/goteborg-kraftsportklubb" target="_blank" class="inline-flex items-center px-4 py-2 bg-white text-gkk font-semibold rounded-lg shadow hover:bg-gray-100 transition-all duration-200">
      <i class="fa fa-external-link mr-2"></i>
      Öppna webbshopen
    </a>
  </div>

  {{-- Links section --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-link mr-2"></i> Länkar
    </h2>
    <div class="space-y-4">
      <a href="https://styrkelyft.se" target="_blank" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-gkk/10 rounded-lg flex items-center justify-center mr-4">
          <i class="fa fa-globe text-gkk"></i>
        </div>
        <div class="flex-1">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors">Svenska Styrkelyftsförbundet (SSF)</h3>
        </div>
        <i class="fa fa-arrow-right text-gray-400 group-hover:text-gkk transition-colors"></i>
      </a>
      <a href="https://www.styrkelyft.se/dokument/dokument-blanketter" target="_blank" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-gkk/10 rounded-lg flex items-center justify-center mr-4">
          <i class="fa fa-file-text-o text-gkk"></i>
        </div>
        <div class="flex-1">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors">Dokument för tävling mm på förbundets hemsida</h3>
        </div>
        <i class="fa fa-arrow-right text-gray-400 group-hover:text-gkk transition-colors"></i>
      </a>
      <a href="https://data.styrkelyft.se/" target="_blank" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-gkk/10 rounded-lg flex items-center justify-center mr-4">
          <i class="fa fa-database text-gkk"></i>
        </div>
        <div class="flex-1">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors">Databasen för ranking och resultatregistrering</h3>
        </div>
        <i class="fa fa-arrow-right text-gray-400 group-hover:text-gkk transition-colors"></i>
      </a>
    </div>
  </div>

  {{-- Documents section --}}
  <div class="mb-10">
    <h2 class="text-xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-file-pdf-o mr-2"></i> Dokument
    </h2>
    <div class="grid gap-4 sm:grid-cols-2">
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6" target="_blank" class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gkk/30 transition-all group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
          <i class="fa fa-file-pdf-o text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors text-sm">Antidopingavtal</h3>
          <p class="text-gray-500 text-xs">Mellan medlem och förening</p>
        </div>
      </a>
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FKdSJ2FmrTSijNpxOXVGx0aNtERwoe6.pdf?alt=media&token=9fe3519c-7f6a-473e-851f-897561086e31" target="_blank" class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gkk/30 transition-all group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
          <i class="fa fa-file-pdf-o text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors text-sm">Antidopingplan</h3>
          <p class="text-gray-500 text-xs">2026-2028</p>
        </div>
      </a>
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188" target="_blank" class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gkk/30 transition-all group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
          <i class="fa fa-file-pdf-o text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors text-sm">Medlemsavtal</h3>
          <p class="text-gray-500 text-xs">Mellan medlem och förening</p>
        </div>
      </a>
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FN0TGPEf5Nto75XNm2Nux7Lpojez7pz.pdf?alt=media&token=3204252d-8c0c-43f0-baa2-de7b11ca277c" target="_blank" class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gkk/30 transition-all group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
          <i class="fa fa-file-pdf-o text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors text-sm">Föreningens stadgar</h3>
          <p class="text-gray-500 text-xs">Antagna 2022-02-19</p>
        </div>
      </a>
      <a href="https://goteborg-kraftsportklubb.web.app/img/gkk-integritetspolicy.pdf" target="_blank" class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gkk/30 transition-all group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
          <i class="fa fa-file-pdf-o text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors text-sm">Integritetspolicy</h3>
          <p class="text-gray-500 text-xs">Föreningens policy</p>
        </div>
      </a>
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FpUSeLCEIjkrZnfyjNXw7DEIrrlsBTH.pdf?alt=media&token=97fb01f5-6a40-4c7d-9d2e-8605b1893163" target="_blank" class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gkk/30 transition-all group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
          <i class="fa fa-file-pdf-o text-red-600"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 group-hover:text-gkk transition-colors text-sm">Samarbete med SportRehab</h3>
          <p class="text-gray-500 text-xs">Läs mer om samarbetet (PDF)</p>
        </div>
      </a>
    </div>
  </div>


  {{-- Training programs --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-list-ol mr-2"></i> Träningsprogram
    </h2>
    <div class="space-y-6">
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FBeard-Press%202021-02-14.xlsx?alt=media&token=30924370-12aa-4ddf-8bf1-2fd45cc70717" target="_blank" class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group">
        <img width="80" class="rounded-lg shadow-sm mr-4" src="https://goteborg-kraftsportklubb.web.app/img/beardpress-logo-excel.png" alt="Beard Press">
        <div class="flex-1">
          <h3 class="font-bold text-gray-900 group-hover:text-gkk transition-colors">Beard Press</h3>
          <p class="text-gray-600 text-sm">Ett bänkpressprogram av Karl Malmberg (@kraftkarlos)</p>
          <span class="inline-block mt-1 text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded">Excelfil</span>
        </div>
        <i class="fa fa-download text-gray-400 group-hover:text-gkk transition-colors ml-2"></i>
      </a>
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FBa%CC%88nkprogram%20CalleO%CC%88berg.xlsx?alt=media&token=731f217f-fe83-4950-88b6-402515862445" target="_blank" class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group">
        <img width="80" class="rounded-lg shadow-sm mr-4" src="https://goteborg-kraftsportklubb.web.app/img/hemside-bild.png" alt="Carl Öbergs bänkpressprogram">
        <div class="flex-1">
          <h3 class="font-bold text-gray-900 group-hover:text-gkk transition-colors">Carl Öbergs bänkpressprogram</h3>
          <p class="text-gray-600 text-sm">Hög volym och hög intensitet. Riktar sig till dig med år av dedikerad bänkpressträning.</p>
          <span class="inline-block mt-1 text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded">Excelfil</span>
        </div>
        <i class="fa fa-download text-gray-400 group-hover:text-gkk transition-colors ml-2"></i>
      </a>
    </div>
  </div>

  {{-- Graphics section --}}
  <div class="mb-10">
    <h2 class="text-xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-paint-brush mr-2"></i> Grafik
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
        <img class="w-16 h-16 mx-auto mb-2" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-ljus-bakgrund.png?alt=media&token=be907bc2-2491-45e6-ae62-a5a78b080d41" alt="Tvåfärg ljus">
        <p class="text-xs text-gray-600">Tvåfärg mot ljus bakgrund</p>
      </div>
      <div class="bg-gray-800 rounded-xl p-4 shadow-sm text-center">
        <img class="w-16 h-16 mx-auto mb-2" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-300x300.png?alt=media&token=f1942878-84fa-4519-a051-08bbec625fa7" alt="Tvåfärg mörk">
        <p class="text-xs text-gray-300">Tvåfärg mot mörk bakgrund</p>
      </div>
      <div class="bg-gray-800 rounded-xl p-4 shadow-sm text-center">
        <img class="w-16 h-16 mx-auto mb-2" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-transparent.png?alt=media&token=def700f9-01c7-4786-a246-28475a5e32f1" alt="Tvåfärg mörk transparent">
        <p class="text-xs text-gray-300">Tvåfärg transparent</p>
      </div>
      <div class="bg-gray-800 rounded-xl p-4 shadow-sm text-center">
        <img class="w-16 h-16 mx-auto mb-2" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FMonokrom-pa%CC%8A-mo%CC%88rk-bakgrund.png?alt=media&token=aa0b2c8a-4059-42ed-a36e-7436f7b24a39" alt="Monokrom mörk">
        <p class="text-xs text-gray-300">Monokrom mot mörk bakgrund</p>
      </div>
    </div>
    <div class="flex flex-wrap gap-3">
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FMonokrom-pa%CC%8A-mo%CC%88rk-bakgrund.pdf?alt=media&token=f49672e6-dc0b-4dcc-a973-bc7a2e47ffd4" target="_blank" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-gkk hover:text-gkk transition-colors">
        <i class="fa fa-file-pdf-o mr-2 text-red-600"></i>
        Monokrom för tryck (PDF)
      </a>
      <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund.pdf?alt=media&token=e2779fa9-486f-491f-81ed-14d1b1d0d15b" target="_blank" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-gkk hover:text-gkk transition-colors">
        <i class="fa fa-file-pdf-o mr-2 text-red-600"></i>
        Tvåfärg för tryck (PDF)
      </a>
    </div>
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
