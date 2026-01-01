@extends('layouts.app')

@section('content')
<div class="relative">
  <div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bella-min.jpg);
  height: 500px;
  background-size: cover;
  background-position-y: center; max-height: 50vh;" class="flex items-center">
  </div>
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center px-4">
    <div class="text-center">
      {{-- <p class="text-white/80 uppercase tracking-[0.3em] text-sm mb-3 drop-shadow">Sedan 1933</p> --}}
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white drop-shadow-lg tracking-tight">Göteborg Kraftsportklubb</h1>
      <div class="mt-4 flex items-center justify-center gap-3">
        <span class="h-px w-12 bg-white/50"></span>
        {{-- <p class="text-white/90 text-lg sm:text-xl drop-shadow">Styrka genom gemenskap</p> --}}
        <p class="text-white/80 uppercase tracking-[0.3em] text-sm drop-shadow">Sedan 1933</p>
        <span class="h-px w-12 bg-white/50"></span>
      </div>
    </div>
  </div>
</div>
<div class="container mx-auto">
    <div>
        <div class="mx-auto py-8 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-10">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
                <a href="/prova-pa" class="block group">
                  <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-gkk to-gkk-light p-6 sm:p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white/5"></div>
                    <div class="relative">
                      <div class="inline-block bg-white/20 text-white text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full mb-4">
                        <i class="fa fa-calendar mr-1"></i> 15 februari 2026
                      </div>
                      <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                        Prova på-tävling i styrkelyft
                      </h3>
                      <p class="text-white/90 text-base sm:text-lg leading-relaxed">
                        Är du nyfiken på att tävla i styrkelyft eller bänkpress? Nu får du chansen att testa hur det är under avslappnade former!
                      </p>
                      <div class="mt-4 inline-flex items-center text-white font-medium group-hover:translate-x-1 transition-transform duration-200">
                        Läs mer och anmäl dig
                        <i class="fa fa-arrow-right ml-2"></i>
                      </div>
                    </div>
                  </div>
                </a>

                <a href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FDszgogxmrxsz3hOJauVM8p7gwj6Hnw.pdf?alt=media&token=523767fd-aeee-4167-92d2-f502aa1d4662" target="_blank" class="block group">
                  <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-700 to-gray-800 p-6 sm:p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.01]">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/5"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white/5"></div>
                    <div class="relative">
                      <div class="inline-block bg-white/20 text-white text-xs font-semibold uppercase tracking-wider px-3 py-1 rounded-full mb-4">
                        <i class="fa fa-calendar mr-1"></i> 7 februari 2026 kl 10:00
                      </div>
                      <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                        Årsmöte 2026
                      </h3>
                      <p class="text-white/90 text-base sm:text-lg leading-relaxed">
                        Kallelse och föredragningslista för GKKs årsmöte. Alla medlemmar är välkomna!
                      </p>
                      <div class="mt-4 inline-flex items-center text-white font-medium group-hover:translate-x-1 transition-transform duration-200">
                        <i class="fa fa-file-pdf-o mr-2"></i>
                        Öppna kallelse
                        <i class="fa fa-arrow-right ml-2"></i>
                      </div>
                    </div>
                  </div>
                </a>
                {{-- <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2Fn3Gc8wTbI8OygNYumrXnwoDd8p7VUp.pdf?alt=media&token=dd7cb0d9-9d0e-463a-b8d1-31f029219478">
                    <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsberättelse 2024
                  </a>
                </div> --}}
                {{-- <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FVerksamhetsplan%20och%20budget%202024.pdf?alt=media&token=d15bbc76-f0dd-4d2a-a678-11b60fe2c79d">
                    <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsplan och budget 2024
                  </a>
                </div> --}}
                {{-- <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FEkonomiskt%20resultat%202023.pdf?alt=media&token=990c709a-2ad8-41cb-997f-5898c306db36">
                    <i class="fa fa-file-pdf-o mr-2"></i>Ekonomiskt resultat 2023
                  </a>
                </div> --}}
                {{-- <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FEuEFEZcycAXmSp1d1JJjc5VEbv1lgh.pdf?alt=media&token=3687af19-bfaa-4d65-9826-c4a646d70b79">
                    <i class="fa fa-file-pdf-o mr-2"></i>Valberedningens förslag för 2025
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FocGMIpBbUbFUteTxgxr5Bu5bJKbrvZ.pdf?alt=media&token=4f9b5ff7-f004-4fc2-bb2f-e821414aa589">
                    <i class="fa fa-file-pdf-o mr-2"></i>Revisionsberättelse 2024
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FXSlmDdMsbMNAUo2SkErTwfIzf93sDQ.pdf?alt=media&token=6346e687-ffac-4e86-8111-7641adf5aae1">
                    <i class="fa fa-file-pdf-o mr-2"></i>Motion (Veteranutskott) och styrelsens yttrande
                  </a>
                </div>
                <div>
                  <p class="text-xl leading-normal text-black">
                    Ytterligare möteshandlingar hittar medlemmar på "Insidan" under "Dokument".
                  </p>
                </div> --}}

                

                {{-- <a href="/gm" class="mt-6 block">
                  <div class="rounded p-2 border-gkk border-2">
                    <p class="text-3xl leading-bold text-gkk">
                      Götalandsmästerskapen 10-12 maj 2024
                    </p>
                    <p class="text-xl leading-bold text-gkk">
                      GKK arrangerar Götalandsmästerskapen i Klassisk Styrkelyft och Klassisk Bänkpress!<br>Klicka här för att läsa med på vår GM-sida.
                    </p>
                  </div>
                </a> --}}


                <p class="text-lg leading-relaxed text-gray-600">
                  Göteborg Kraftsportklubb (GKK) är en idrottsförening som bildades 1933. Vi ägnar oss åt idrotten Styrkelyft och tränar och tävlar därmed i knäböj, bänkpress och marklyft. I föreningen finns allt från motionärer till elitaktiva på högsta nivå. Vi tränar tillsammans, hjälper varandra och tror på att styrka kommer från gemenskap.
                </p>
                <p class="text-lg leading-relaxed text-gray-600">
                  Sedan december 2018 har vi vår egna klubb- och träningslokal hos <a class="underline hover:text-gkk transition-colors" href="https://www.friskissvettis.se/goteborg/harfinnsvi/majorna" target="_blank">Friskis & Svettis Majorna</a> i Göteborg. För att träna hos oss behöver du därmed också vara medlem och ha träningskort hos Friskis & Svettis. Träning kan ske även under obemannade tider genom att registrera en kod i kassan. Mer information om du är intresserad av att bli medlem i GKK hittar du under <a class="underline hover:text-gkk transition-colors" href="/medlem">Medlemskap</a>.
                </p>
                <p class="text-lg leading-relaxed text-gray-600">
                  GKK finns även på <a class="underline hover:text-gkk transition-colors" href="https://www.instagram.com/goteborgkk/" target="_blank">Instagram</a> där du kan hitta foton och löpande information från föreningen.
                </p>
            </div>
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
      </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- <gkk-navigation :user='@json($user)' :unanswered='@json($unanswered)'></gkk-navigation> --}}
        </div>
    </div>
</div>
@endsection
