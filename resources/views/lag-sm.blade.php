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
              <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Lag-SM 2023</h2>
              </div>
              
              <p class="text-xl leading-normal text-gray-500">
              Den 16-17 december 2023 kommer Lag-SM i Styrkelyft avgöras hos Göteborg Kraftsportklubb i Göteborg.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Mer information som tider, tävlande föreningar mm kommer läggas upp längre fram.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Varmt välkoma att komma och titta!
              </p>

              {{-- <p class="text-xl leading-normal text-gray-500">
                Vi kommer ha domare som dömer som att det är en riktig tävling, en speaker som ropar upp varje l
                yftare, och klovare som sköter passning, men allt sker utan press och under mer avslappnade former.
              </p>

              <p class="text-xl">
                <b>Tid:</b> <span class="text-lg">Söndagen den 12 mars 2023, samling 10.00 och tävlingsstart 11.00.</span>
                <br>
                <b>Gren:</b> <span class="text-lg">Styrkelyft (knäböj, bänkpress och marklyft), eller enbart Bänkpress.</span>
                <br>
                <b>Plats:</b> <span class="text-lg">Göteborg Kraftsportklubb, hos Friskis och Svettis i Majorna, Karl Johansgatan 152.</span>
                <br>
                <b>Avgift:</b> <span class="text-lg">150 kr (gratis om du redan är medlem i GKK)</span>
                <br>
                <b>Anmälan:</b> <span class="text-lg">Senast 5 mars till info@gkk-styrkelyft.se. Det finns ett begränsat antal platser, så först till kvarn gäller!</span>
                <br>
              </p>

              <p class="text-xl leading-normal text-gray-500">
                Vi samlas kl 10:00 i GKKs träningslokal på Friskis&Svettis i Majorna där vi i korthet går igenom 
                tävlingsupplägget och alla får en chans att värma upp Sedan börjar tävlingen ca kl 11:00 och 
                pågår till ca 14:00. Det kan vara bra att ha med sig tävlingsproviant.
              </p>
              <p class="text-xl leading-normal text-gray-500">
                Tävlingen hålls i stora jympasalen på Friskis&Svettis i Majorna, Karl Johansgatan 152. Publik välkomnas!
                Dagen före prova på tävlingen, lördagen den 11 e mars, hålls Serie
                1 i samma lokal för licensierade lyftare, välkomna att heja på och inspireras även då! 
              </p> --}}

              <div class="h-6"></div>
              <div class="flex justify-center max-w-full mt-20 overflow-x-hidden space-x-4 xl:justify-between">
                <div class="mr-2 flex-1 text-center">
                  <img class="shadow-md rounded-lg" src="https://goteborg-kraftsportklubb.web.app/grenar/squat-resized.png">
                </div>
                <div class="mr-2 flex-1 text-center">
                  <img class="shadow-md rounded-lg" src="https://goteborg-kraftsportklubb.web.app/grenar/benchpress.jpg">
                </div>
                <div class="mr-2 flex-1 text-center">
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
