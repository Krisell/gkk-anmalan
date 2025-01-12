@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bjorn_och_klas-min.jpeg); 
height: 500px;
background-size: cover;
background-position-y: center; max-height: 50vh;" class="flex items-center">
</div>
<div class="container mx-auto">
    <div>
        <div class="mx-auto py-12 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
              <div class="flex items-center">
              <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Välkommen på PROVA PÅ-TÄVLING hos Göteborg Kraftsportklubb!</h2>
              </div>

              <p class="text-xl leading-normal text-gray-500">

Brukar du köra knäböj, bänkpress och marklyft på gymmet och undrar hur det är att tävla i styrkelyft?<br>Nu kan du få chansen att testa!


              </p>

              <p class="text-xl leading-normal text-gray-500">
                Söndagen 26 januari 2025 anordnar GKK en prova på-tävling för de som vill testa på 
                hur det är att tävla i styrkelyft. Du behöver inga speciella kläder eller utrustning utan kan ha på 
                dig det du brukar ha när du tränar, men du bör ha tränat lyften knäböj, bänkpress och marklyft samt 
                veta ungefär vad som gäller för att göra godkända lyft. 
              </p>

              <p class="text-xl leading-normal text-gray-500">
                Vi kommer ha domare som dömer som att det är en riktig tävling, en speaker som ropar upp varje lyftare, 
                och klovare som sköter passning, men allt sker utan press och under avslappnade former. Vi kommer 
                inte heller ha någon invägning eller räkna placeringar.
              </p>

              <p class="text-xl">
                <b>Tid:</b> <span class="text-lg">Söndagen 26 januari 2025. Samling 10.00 och tävlingsstart ca 11.00.</span>
                <br>
                <b>Gren:</b> <span class="text-lg"><i>Styrkelyft</i> (knäböj, bänkpress och marklyft), eller <i>enbart Bänkpress</i>.</span>
                <br>
                <b>Plats:</b> <span class="text-lg">Göteborg Kraftsportklubb, hos Friskis och Svettis i Majorna, Karl Johansgatan 152.</span>
                <br>
                <b>Avgift:</b> <span class="text-lg">250 kr (gratis om du redan är medlem i GKK, eller blir medlem före tävlingen).</span>
                <br>
                <b>Anmälan:</b> <span class="text-lg">Senast 20 januari till info@gkk-styrkelyft.se. Det finns ett begränsat antal platser, så först till kvarn gäller! Skicka med namn, födelsedatum och ungefärliga startvikter i grenarna.</span>
                <br>
                <b>Betalning:</b> <span class="text-lg">Sker i samband med anmälan till GKKs Bankgiro eller Swish. Ange "PROVA PÅ" och ditt namn. Swishnummer: 123-581 34 56. <a class="underline" href="https://gkk.test/gkk">Se här för mer info om betalning.</a></span>
              </p>

              <p class="text-xl leading-normal text-gray-500">
                Vi samlas kl 10:00 i GKKs träningslokal på Friskis & Svettis i Majorna där vi i korthet går igenom 
                tävlingsupplägget. Sedan får alla chans att värma upp, och få hjälp av licensierade styrkelyftare för att ex. säkerställa att lyften blir godkända. 
                Tävlingen börjar ca kl 11:00 och pågår till ca 14:00. Det kan vara bra att ha med sig tävlingsproviant.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Tävlingen hålls i stora gympasalen på Friskis & Svettis i Majorna, Karl Johansgatan 152. Publik är varmt välkommen!
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Dagen före prova på-tävlingen, lördagen den 25 januari, hålls Serieomgång
                1 i samma lokal för licensierade lyftare, välkomna att komma och titta, heja på och inspireras! 
              </p<

              <div class="h-6"></div>
              <div class="flex justify-center max-w-full mt-20 overflow-x-hidden space-x-4 xl:justify-between">
                <div class="mr-2 flex-1 text-center">
                  <h3 class="text-md leading-normal text-black mb-2 font-bold">Knäböj</h3>
                  <img class="shadow-md rounded-lg" src="https://goteborg-kraftsportklubb.web.app/grenar/squat-resized.png">
                </div>
                <div class="mr-2 flex-1 text-center">
                  <h3 class="text-md leading-normal text-black mb-2 font-bold">Bänkpress</h3>
                  <img class="shadow-md rounded-lg" src="https://goteborg-kraftsportklubb.web.app/grenar/benchpress.jpg">
                </div>
                <div class="mr-2 flex-1 text-center">
                  <h3 class="text-md leading-normal text-black mb-2 font-bold">Marklyft</h3>
                  <img class="shadow-md rounded-lg" src="https://goteborg-kraftsportklubb.web.app/grenar/deadlift.3.png">
                </div>
              </div>
            </div>


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