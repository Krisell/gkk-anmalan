@extends('layouts.app')

@section('content')
{{-- Hero section with gradient overlay --}}
<div class="relative">
  <div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg);
  height: 500px;
  background-size: cover;
  background-position-y: center; max-height: 50vh;" class="flex items-center">
  </div>
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center px-4">
    <div class="text-center">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white drop-shadow-lg tracking-tight">Styrkelyft</h1>
      <div class="mt-4 flex items-center justify-center gap-3">
        <span class="h-px w-12 bg-white/50"></span>
        <p class="text-white/80 uppercase tracking-[0.3em] text-sm drop-shadow">Knäböj, bänkpress och marklyft</p>
        <span class="h-px w-12 bg-white/50"></span>
      </div>
    </div>
  </div>
</div>

<div class="container mx-auto max-w-4xl px-4 py-10">
  {{-- Intro section --}}
  <div class="mb-10">
    <p class="text-xl sm:text-2xl font-medium text-gkk leading-relaxed">
      I styrkelyft tävlar man i grenarna knäböj, bänkpress och marklyft. Man kan också tävla i enbart bänkpress.
    </p>
  </div>

  {{-- Description --}}
  <div class="space-y-5 mb-10">
    <p class="text-lg leading-relaxed text-gray-600">
      Det finns tävlingar där särskild utrustning, exempelvis knälindor och dräkt, är tillåtet, och det finns tävlingar i så kallad "klassisk styrkelyft" där ingen hjälpande utrustning utöver knävärmare och handledslindor är tillåtet. Den klassiska disciplinen har idag flest utövare och är också en lämplig utgångspunkt för en nybörjare.
    </p>
  </div>

  {{-- The three lifts --}}
  <div class="mb-12">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl bg-white">
          <div class="overflow-hidden">
            <img class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/grenar/squat-resized.png" alt="Knäböj">
          </div>
          <div class="p-4 text-center">
            <h3 class="font-bold text-gkk">Knäböj</h3>
          </div>
        </div>
      </div>
      <div class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl bg-white">
          <div class="overflow-hidden">
            <img class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/grenar/benchpress.jpg" alt="Bänkpress">
          </div>
          <div class="p-4 text-center">
            <h3 class="font-bold text-gkk">Bänkpress</h3>
          </div>
        </div>
      </div>
      <div class="group">
        <div class="overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl bg-white">
          <div class="overflow-hidden">
            <img class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105" src="https://goteborg-kraftsportklubb.web.app/grenar/deadlift.3.png" alt="Marklyft">
          </div>
          <div class="p-4 text-center">
            <h3 class="font-bold text-gkk">Marklyft</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Competition info card --}}
  <div class="bg-gradient-to-br from-gkk to-gkk-light rounded-2xl p-6 sm:p-8 shadow-lg mb-10 text-white">
    <h2 class="text-xl font-bold mb-6 flex items-center">
      <i class="fa fa-trophy mr-2"></i> Att tävla i Styrkelyft
    </h2>
    <div class="space-y-4 text-white/90">
      <p class="leading-relaxed">
        Det finns fyra olika tävlingsdiscipliner inom idrotten som lyftaren kan välja mellan:
      </p>
      <ul class="space-y-2 ml-4">
        <li class="flex items-start">
          <span class="mr-2">•</span>
          <span>Klassisk Styrkelyft (total i Knäböj + Bänkpress + Marklyft)</span>
        </li>
        <li class="flex items-start">
          <span class="mr-2">•</span>
          <span>Klassisk Bänkpress</span>
        </li>
        <li class="flex items-start">
          <span class="mr-2">•</span>
          <span>Utrustad Styrkelyft (total i Utrustad Knäböj + Utrustad Bänkpress + Utrustad Marklyft)</span>
        </li>
        <li class="flex items-start">
          <span class="mr-2">•</span>
          <span>Utrustad Bänkpress</span>
        </li>
      </ul>
      <p class="leading-relaxed">
        Samtliga dessa har mästerskap på distriktsnivå, nationell nivå och internationellt. Det finns även i regel mästerskap för ungdomar, juniorer och veteraner i respektive disciplin.
      </p>
    </div>
  </div>

  {{-- Competition types --}}
  <div class="space-y-5 mb-10">
    <h3 class="text-xl font-bold text-gkk flex items-center">
      <i class="fa fa-calendar mr-2 text-gkk/70"></i> Serietävlingar och Mästerskap
    </h3>
    <p class="text-lg leading-relaxed text-gray-600">
      Man kan tävla antingen i en så kallad <strong class="text-gray-700">serietävling</strong> eller i ett <strong class="text-gray-700">mästerskap</strong>. I mästerskap tävlar man om placeringar och medaljer. I serietävlingar samlar man poäng till föreningens lag, eller tävlar för att se hur mycket man klarar. Till vissa mästerskap (ex. SM) måste man kvala in, och det kan man göra genom att klara uppsatta kvalgränser vid valfri tävling.
    </p>
    <div class="bg-gkk/5 border-l-4 border-gkk rounded-r-lg p-4">
      <p class="text-gray-700">
      <h3 class="text-xl font-bold text-gkk mb-4 flex items-center">
        <i class="fa fa-info-circle mr-2"></i> Tävlingar på hemmaplan
      </h3>
        Göteborg Kraftsportklubb arrangerar ofta serietävlingar på hemmaplan på Friskis och Svettis i Majorna, då hela föreningen är engagerad och medlemmarna antingen är med och tävlar eller ställer upp som funktionär. Om föreningens lag presterar bra under året kan de gå till final i lag-SM där SM-medaljer står på spel.
      </p>
    </div>
    <div class="bg-gkk/5 border-l-4 border-gkk rounded-r-lg p-4">
      <p class="text-gray-700">
      <h3 class="text-xl font-bold text-gkk mb-4 flex items-center">
        <i class="fa fa-balance-scale mr-2"></i> Viktklasser och invägning
      </h3>
        Vid mästerskap anmäler man sig till en viktklass och man måste klara invägningen 2 timmar före tävlingsstart för att få delta. Vid serietävlingar finns inga viktklasser utan där beräknas lagpoängen baserat på vikt på stången och dagens kroppsvikt vid invägningen.
      </p>
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
