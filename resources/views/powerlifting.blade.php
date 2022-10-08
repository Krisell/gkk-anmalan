@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg); 
height: 500px;
background-size: cover;
background-position-y: center;" class="flex items-center">
</div>
<div class="container mx-auto">
    <div>
        <div class="mx-auto py-12 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
              <div class="flex items-center">
              <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Om Styrkelyft</h2>
              </div>
              <p class="text-xl leading-normal text-gray-500">I styrkelyft tävlar man i grenarna knäböj, bänkpress och marklyft. Man kan också tävla i enbart bänkpress. Det finns tävlingar där särskild utrustning, exempelvis knälindor och dräkt, är tillåtet, och det finns tävlingar i så kallad "klassisk styrkelyft" där ingen hjälpande utrustning utöver knävärmare och handledslindor är tillåtet. Den klassiska disciplinen har idag flest utövare och är också en lämplig utgångpunkt för en nybörjare.</p>
              <p class="text-xl leading-normal text-gray-400">Här är tanken att visa bilder på de tre lyften.</p>
            </div>
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
              <div class="flex items-center">
              <h3 class="text-2xl font-extrabold tracking-tight">Att tävla i Styrkelyft</h3>
              </div>
              <p class="text-xl leading-normal text-gray-500">
                Det finns fyra olika tävlingsdiscipliner inom idrotten som lyftaren kan välja mellan:
              </p>
                <ul class="px-8">
                  <li class="list-disc text-xl leading-normal text-gray-500">Klassisk Styrkelyft (total i Knäböj + Bänkpress + Marklyft)</li>
                  <li class="list-disc text-xl leading-normal text-gray-500">Klassisk Bänkpress</li>
                  <li class="list-disc text-xl leading-normal text-gray-500">Utrustad Styrkelyft (total i Utrustad Knäböj + Utrustad Bänkpress + Utrustad Marklyft)</li>
                  <li class="list-disc text-xl leading-normal text-gray-500">Utrustad Bänkpress</li>
                </ul>

              <p class="text-xl leading-normal text-gray-500">
                Samtliga dessa har mästerskap på distriktsnivå, nationell nivå och internationellt. Det finns även i regel mästerskap för ungdomar, juniorer och veteraner i respektive disciplin.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Man kan tävla antingen i en så kallad <b>serietävling</b> eller i ett <b>mästerskap</b>. I mästerskap tävlar man om placeringar och medaljer. I serietävlingar samlar man poäng till föreningens lag, eller tävlar för att se hur mycket man klarar. Till vissa mästerskap (ex. SM) måste man kvala in, och det kan man göra genom att klara uppsatta kvalgränser vid en serietävling.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Göteborg Kraftsportklubb arrangerar ofta serietävlingar på hemmaplan på Friskis och Svettis i Majorna, då hela föreningen är engagerad och medlemmarna antingen är med och tävlar eller ställer upp som funktionär. Om föreningens lag presterar bra under året kan de gå till final i lag-SM där SM-medaljer står på spel.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Vid mästerskap anmäler man sig till en viktklass och man måste väga in 2 timmar före tävlingsstart för att få delta. Vid serietävlingar finns inga viktklasser utan där beräknas lagpoängen baserat på vikt på stången och dagens kroppsvikt.
              </p>
            </div>
            <ul role="list" class="space-y-12 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:gap-y-12 sm:space-y-0 lg:grid-cols-3 lg:gap-x-8">
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://goteborg-kraftsportklubb.web.app/img/bjornlyftare-min.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://goteborg-kraftsportklubb.web.app/img/bankpress-min.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://goteborg-kraftsportklubb.web.app/img/tavling-min.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://goteborg-kraftsportklubb.web.app/img/mark-min.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://goteborg-kraftsportklubb.web.app/img/clara-min.jpg" alt="">
                  </div>
                </div>
              </li>
      
              <!-- More people... -->
            </ul>
          </div>
        </div>
      </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- <gkk-navigation :user='@json($user)' :unanswered='@json($unanswered)'></gkk-navigation> --}}
        </div>
    </div>
</div>
@endsection
