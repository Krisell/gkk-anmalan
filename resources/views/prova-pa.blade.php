@extends('layouts.app')

@section('content')
{{-- Hero section with gradient overlay --}}
<div class="relative">
  <div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg);
  height: 500px;
  background-size: cover;
  background-position-y: center; max-height: 50vh;" class="flex items-center">
  </div>
  <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-white"></div>
  <div class="absolute bottom-0 left-0 right-0 pb-8 px-4">
    <div class="container mx-auto max-w-4xl">
      <div class="inline-block bg-gkk text-white text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full mb-3">
        <i class="fa fa-calendar mr-1"></i> 15 februari 2026
      </div>
      <h1 class="text-3xl sm:text-4xl font-bold text-gkk">Prova på-tävling</h1>
    </div>
  </div>
</div>

<div class="container mx-auto max-w-4xl px-4 py-10">
  {{-- Intro section --}}
  <div class="mb-10">
    <p class="text-xl sm:text-2xl font-medium text-gkk leading-relaxed">
      Brukar du köra knäböj, bänkpress och marklyft på gymmet och undrar hur det är att tävla i styrkelyft? Nu kan du få chansen att testa!
    </p>
  </div>

  {{-- Description --}}
  <div class="space-y-5 mb-10">
    <p class="text-lg leading-relaxed text-gray-600">
      Söndagen 15 februari 2026 anordnar GKK en prova på-tävling för de som vill testa på
      hur det är att tävla i styrkelyft. Du behöver inga speciella kläder eller utrustning utan kan ha på
      dig det du brukar ha när du tränar, men du bör ha tränat lyften knäböj, bänkpress och marklyft samt
      veta ungefär vad som gäller för att göra godkända lyft.
    </p>
    <p class="text-lg leading-relaxed text-gray-600">
      Vi kommer ha domare som dömer som att det är en riktig tävling, en speaker som ropar upp varje lyftare,
      och klovare som sköter passning, men allt sker utan press och under avslappnade former. Vi kommer
      inte heller ha någon invägning eller räkna placeringar.
    </p>
  </div>

  {{-- Event details card --}}
  <div class="bg-gradient-to-br from-gkk to-gkk-light rounded-2xl p-6 sm:p-8 shadow-lg mb-10 text-white">
    <h2 class="text-xl font-bold mb-6 flex items-center">
      <i class="fa fa-info-circle mr-2"></i> Praktisk information
    </h2>
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-clock-o text-lg -mt-1 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Tid</div>
            <div class="text-white">Söndagen 15 februari 2026</div>
            <div class="text-white/80 text-sm">Samling 10:00, tävlingsstart ca 11:00</div>
          </div>
        </div>
      </div>
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-trophy text-lg -mt-1 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Gren</div>
            <div class="text-white">Styrkelyft eller enbart Bänkpress</div>
            <div class="text-white/80 text-sm">Knäböj, bänkpress och marklyft</div>
          </div>
        </div>
      </div>
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-map-marker text-lg -mt-1 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Plats</div>
            <div class="text-white">Friskis & Svettis Majorna</div>
            <div class="text-white/80 text-sm">Karl Johansgatan 152, Göteborg</div>
          </div>
        </div>
      </div>
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-money text-lg -mt-1 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Avgift</div>
            <div class="text-white">300 kr</div>
            <div class="text-white/80 text-sm">Gratis för GKK-medlemmar</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Registration info --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-4 flex items-center">
      <i class="fa fa-pencil-square-o mr-2"></i> Anmälan
    </h2>
    <div class="space-y-4 text-gray-700">
      <p class="text-lg">
        <span class="font-semibold">Sista anmälningsdag:</span> 6 februari till
        <a href="mailto:info@gkk-styrkelyft.se" class="text-gkk hover:text-gkk-light underline transition-colors">info@gkk-styrkelyft.se</a>
      </p>
      <p class="text-gray-600">
        Det finns ett begränsat antal platser, så först till kvarn gäller! Skicka med namn, födelsedatum och ungefärliga startvikter i grenarna för ev. gruppindelning.
      </p>
      <div class="pt-4 mt-4 border-t border-gray-200">
        <p class="font-semibold text-gkk mb-1">Betalning</p>
        <p class="text-gray-600">
          Sker i samband med anmälan till GKKs Bankgiro eller Swish. Ange "PROVA PÅ" och ditt namn.<br>
          <span class="font-medium">Swishnummer: 123-581 34 56</span> ·
          <a class="text-gkk hover:text-gkk-light underline transition-colors" href="/gkk">Mer info om betalning</a>
        </p>
      </div>
    </div>
  </div>

  {{-- More info --}}
  <div class="space-y-5 mb-12">
    <p class="text-lg leading-relaxed text-gray-600">
      Vi samlas kl 10:00 i GKKs träningslokal på Friskis & Svettis i Majorna där vi i korthet går igenom
      tävlingsupplägget. Sedan får alla chans att värma upp, och få hjälp av licensierade styrkelyftare för att ex. säkerställa att lyften blir godkända.
      Tävlingen börjar ca kl 11:00 och pågår till ca 14:00. Det kan vara bra att ha med sig tävlingsproviant och kiosk med mindre utbud finns på Friskis.
    </p>
    <p class="text-lg leading-relaxed text-gray-600">
      Tävlingen hålls i stora gympasalen på Friskis & Svettis i Majorna. <strong class="text-gray-700">Publik är varmt välkommen!</strong>
    </p>
    <div class="bg-gkk/5 border-l-4 border-gkk rounded-r-lg p-4">
      <p class="text-gray-700">
        <i class="fa fa-lightbulb-o text-gkk mr-2"></i>
        Dagen före prova på-tävlingen, lördagen den 14 februari, hålls Serieomgång 1 i samma lokal för licensierade lyftare. Välkomna att komma och titta, heja på och inspireras!
      </p>
    </div>
  </div>

  {{-- The three lifts --}}
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-gkk mb-6 text-center">De tre grenarna</h2>
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

  {{-- CTA --}}
  <div class="text-center mt-12 mb-8">
    <a href="mailto:info@gkk-styrkelyft.se" class="inline-flex items-center px-8 py-4 bg-gkk text-white font-semibold rounded-xl shadow-lg hover:bg-gkk-light hover:shadow-xl transition-all duration-200 hover:scale-105">
      <i class="fa fa-envelope mr-2"></i>
      Anmäl dig nu
    </a>
    <p class="text-gray-500 text-sm mt-3">Sista anmälningsdag: 6 februari 2026</p>
  </div>
</div>
@endsection