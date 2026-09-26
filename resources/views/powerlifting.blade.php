@extends('layouts.app')

@section('content')
<x-page-hero image="https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg" eyebrow="Knäböj, bänkpress och marklyft" title="Styrkelyft">
  I styrkelyft tävlar man i grenarna knäböj, bänkpress och marklyft. Man kan också tävla i enbart bänkpress.
</x-page-hero>

{{-- De tre grenarna --}}
<section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
  <div class="grid gap-10 lg:grid-cols-3 lg:gap-16">
    <div>
      <x-section-heading eyebrow="Grenarna" title="Tre lyft. En idrott." />
      <p class="mt-6 text-lg leading-relaxed text-gray-600">
        Det finns tävlingar där särskild utrustning, exempelvis knälindor och dräkt, är tillåtet, och det finns tävlingar i så kallad "klassisk styrkelyft" där ingen hjälpande utrustning utöver knävärmare och handledslindor är tillåtet. Den klassiska disciplinen har idag flest utövare och är också en lämplig utgångspunkt för en nybörjare.
      </p>
    </div>
    <div class="grid gap-6 sm:grid-cols-3 lg:col-span-2">
      @foreach ([
        ['01', 'Knäböj', 'squat-resized.png'],
        ['02', 'Bänkpress', 'benchpress.jpg'],
        ['03', 'Marklyft', 'deadlift.3.png'],
      ] as [$number, $lift, $file])
        <div class="group relative h-72 overflow-hidden rounded-2xl bg-gkk shadow-lg sm:h-auto sm:min-h-80">
          <img src="https://goteborg-kraftsportklubb.web.app/grenar/{{ $file }}" alt="{{ $lift }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
          <div class="absolute inset-0 bg-gradient-to-t from-gkk via-gkk/30 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 p-6">
            <div class="text-sm font-bold text-white/60">{{ $number }}</div>
            <h3 class="text-2xl font-bold text-white">{{ $lift }}</h3>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Att tävla --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <div class="relative isolate overflow-hidden rounded-3xl bg-gkk px-6 py-14 shadow-xl shadow-gkk/20 sm:px-12">
    <div class="absolute -right-24 -top-24 -z-10 h-80 w-80 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-32 -left-16 -z-10 h-80 w-80 rounded-full bg-gkk-light/60 blur-3xl"></div>
    <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
      <div>
        <x-section-heading eyebrow="Tävling" title="Att tävla i Styrkelyft" dark />
        <p class="mt-6 text-lg leading-relaxed text-white/80">
          Det finns fyra olika tävlingsdiscipliner inom idrotten som lyftaren kan välja mellan. Samtliga dessa har mästerskap på distriktsnivå, nationell nivå och internationellt. Det finns även i regel mästerskap för ungdomar, juniorer och veteraner i respektive disciplin.
        </p>
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        @foreach ([
          ['Klassisk Styrkelyft', 'Total i Knäböj + Bänkpress + Marklyft'],
          ['Klassisk Bänkpress', null],
          ['Utrustad Styrkelyft', 'Total i Utrustad Knäböj + Utrustad Bänkpress + Utrustad Marklyft'],
          ['Utrustad Bänkpress', null],
        ] as [$discipline, $description])
          <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
            <i class="fa fa-trophy text-gkk-lightest"></i>
            <h3 class="mt-3 font-bold text-white">{{ $discipline }}</h3>
            @if ($description)
              <p class="mt-1 text-sm leading-relaxed text-white/70">{{ $description }}</p>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- Serietävlingar och mästerskap --}}
<section class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
  <div class="max-w-3xl">
    <x-section-heading eyebrow="Tävlingsformer" title="Serietävlingar och Mästerskap" />
    <p class="mt-6 text-lg leading-relaxed text-gray-600">
      Man kan tävla antingen i en så kallad <strong class="text-gray-800">serietävling</strong> eller i ett <strong class="text-gray-800">mästerskap</strong>. I mästerskap tävlar man om placeringar och medaljer. I serietävlingar samlar man poäng till föreningens lag, eller tävlar för att se hur mycket man klarar. Till vissa mästerskap (ex. SM) måste man kvala in, och det kan man göra genom att klara uppsatta kvalgränser vid valfri tävling.
    </p>
  </div>
  <div class="mt-10 grid gap-6 md:grid-cols-2">
    <div class="rounded-2xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gkk text-white">
        <i class="fa fa-home text-xl"></i>
      </div>
      <h3 class="mt-5 text-xl font-bold text-gray-900">Tävlingar på hemmaplan</h3>
      <p class="mt-3 leading-relaxed text-gray-600">
        Göteborg Kraftsportklubb arrangerar ofta serietävlingar på hemmaplan på Friskis och Svettis i Majorna, då hela föreningen är engagerad och medlemmarna antingen är med och tävlar eller ställer upp som funktionär. Om föreningens lag presterar bra under året kan de gå till final i lag-SM där SM-medaljer står på spel.
      </p>
    </div>
    <div class="rounded-2xl bg-white p-8 shadow-lg shadow-gkk/5 ring-1 ring-gray-900/5">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gkk text-white">
        <i class="fa fa-balance-scale text-xl"></i>
      </div>
      <h3 class="mt-5 text-xl font-bold text-gray-900">Viktklasser och invägning</h3>
      <p class="mt-3 leading-relaxed text-gray-600">
        Vid mästerskap anmäler man sig till en viktklass och man måste klara invägningen 2 timmar före tävlingsstart för att få delta. Vid serietävlingar finns inga viktklasser utan där beräknas lagpoängen baserat på vikt på stången och dagens kroppsvikt vid invägningen.
      </p>
    </div>
  </div>
</section>

<x-photo-gallery />
@endsection
