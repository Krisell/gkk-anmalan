@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/mark-min.jpg); 
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
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Distriktsmästerskapen i KSL och BP</h2>
              </div>
              <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">12-13 november 2022 - Göteborg Kraftsportklubb - Friskis och Svettis i Majorna</h2>
                <p class="text-xl leading-normal text-gray-500">
                  Distriktsmästerskapen i Klassisk Styrkelyft och Utrustad Bänkpress går av stapeln 12-13 november hos GKK, på Friskis och Svettis i Majorna. 143 lyftare gör upp om medaljerna!
                </p>
                <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk hover:bg-gkk hover:text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FStartlistor%20rev.%202022-10-29.pdf?alt=media&token=31549bb3-7f27-41de-810b-ebeffdfb3f25">
                  <i class="fa fa-file-pdf-o mr-2"></i>Startlistor och tävlingstider (rev. 2022-10-29)
                </a>
                <p class="mt-4 text-xl leading-normal text-gray-500"><b>Observera ändrade starttider från inbjudan på grund av stort deltagarantal.</b></p>
                <p class="mt-4 text-xl leading-normal text-gray-500"><b>Plats:</b> Göteborg Kraftsportklubb, Karl Johansgatan 152, 414 51 Göteborg</p>
                <p class="text-xl leading-normal text-gray-500"><b>Antal deltagare:</b> Totalt 143 st, fördelat på 132 st i KSL och 11 st i BP.</p>
                <p class="text-xl leading-normal text-gray-500"><b>Antal deltagande föreningar: </b> 20</p>
                <p class="text-xl leading-normal text-gray-500"><b>Entreavgift:</b> Valfri</p>
                <p class="text-xl leading-normal text-gray-500"><b>Livestream:</b> Ja, länk läggs upp här på tävlingsdagen.</p>
                <p class="text-xl leading-normal text-gray-500"><b>Kiosk:</b> På friskis finns att köpa enklare förtäring såsom mackor, korv och dryck.</p>
                <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk hover:bg-gkk hover:text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FStartlistor%20rev.%202022-10-29.pdf?alt=media&token=31549bb3-7f27-41de-810b-ebeffdfb3f25">
                  <i class="fa fa-file-pdf-o mr-2"></i>Startlistor och tävlingstider (rev. 2022-10-29)
                </a>
                <p class="text-xl leading-normal text-gray-500">
                  Resultatlistor publiceras här efter avslutad tävling.
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
