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
                  Distriktsmästerskapen i Klassisk Styrkelyft och Utrustad Bänkpress gick av stapeln 12-13 november hos GKK, på Friskis och Svettis i Majorna.
                </p>
                <div>
                  <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FResultatDM.pdf?alt=media&token=ee58f9ac-05b9-4452-9c69-33a20a69529f">
                  <i class="fa fa-file-pdf-o mr-2"></i>Resultatlistor lördag och söndag
                </a>
                </div>

                <div>
                  <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out hover:bg-gkk hover:text-white" 
                  href="https://photos.app.goo.gl/JmFz9fVx6PVji7uLA">
                  <i class="fa fa-camera-retro mr-2"></i>Se bilder från tävlingen tagna av våra fotografer
                </a>
                </div>

                <div>
                <a 
                target="_blank"
                class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out hover:bg-gkk hover:text-white" 
                href="https://photos.app.goo.gl/rjtSMYaa369YWB2NA">
                <i class="fa fa-camera-retro mr-2"></i>Se andras bilder från tävlingen och dela med dig av dina egna
              </a>
              </div>

              <p class="text-xl leading-normal text-gray-500">
                140 lyftare gjorde upp om medaljerna! I klasser med 3 eller fler deltagare fick lyftarna på prispallen sponsrade priser genom 
                <img style="display: inline; width: 140px; margin-top: -5px;" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Ftyngrelogga.png?alt=media&token=29616c97-a25c-4555-a404-b548684d763d">.
              </p>

              <p class="text-xl leading-normal text-gray-500">
                <b>Wahlanders</b> sponsrade även med fyra presentkort på 500 kr som gick till bästa herr och dam i ungdoms- och juniorklassnrna. 
              </p>
              <img width="150px" src="https://www.wahlanders.se/bilder/wahlanders-logo.jpg">
              <p class="text-xl leading-normal text-gray-500">
                Följande lyftare fick detta pris:<br>
                <b>Bästa Dam Ungdom:</b> Amanda Löfkvist, Vedums AIS (69.41 IPF)<br>
                <b>Bästa Dam Junior:</b> Cornelia Hinnersson, Göteborg KK (75.96 IPF)<br>
                <b>Bästa Herr Ungdom:</b> Nicolas Almén, Göteborg SK (85.12 IPF)<br>
                <b>Bästa Herr Junior:</b> Oscar Meyer, Halmstad TSK (98.68 IPF)
              </p>

              <div class="mt-8"></div>
                <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk hover:bg-gkk hover:text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FStartlistor%20rev.%202022-11-08.pdf?alt=media&token=4fde4a7d-c70d-4dcf-96ff-ae13c4448842">
                  <i class="fa fa-file-pdf-o mr-2"></i>Startlistor och tävlingstider (rev. 2022-11-08)
                </a>
                <p class="mt-4 text-xl leading-normal text-gray-500"><b>OBS: På grund av rådande energikris kommer tillgång till bastu inte finnas under tävlingen.</b></p>
                <p class="mt-4 text-xl leading-normal text-gray-500"><b>Tävlingen sker med Eleiko-ställning av nyare modell.</b></p>
                <p class="mt-4 text-xl leading-normal text-gray-500"><b>Plats:</b> Göteborg Kraftsportklubb, Karl Johansgatan 152, 414 51 Göteborg</p>
                <p class="text-xl leading-normal text-gray-500"><b>Antal deltagare:</b> Totalt 143 st, fördelat på 132 st i KSL och 11 st i BP.</p>
                <p class="text-xl leading-normal text-gray-500"><b>Antal deltagande föreningar: </b> 20</p>
                <p class="text-xl leading-normal text-gray-500"><b>Entreavgift:</b> Valfri</p>
                <p class="text-xl leading-normal text-gray-500"><b>Livestream:</b> <a class="underline" target="_blank" href="https://www.youtube.com/watch?v=CQeyhXTxDIo">https://www.youtube.com/watch?v=CQeyhXTxDIo</a></p>
                <p class="text-xl leading-normal text-gray-500"><b>Kiosk:</b> På friskis finns att köpa enklare förtäring såsom mackor, korv och dryck.</p>
                <div>
                  <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FResultatDM.pdf?alt=media&token=ee58f9ac-05b9-4452-9c69-33a20a69529f">
                  <i class="fa fa-file-pdf-o mr-2"></i>Resultatlistor lördag och söndag
                </a>
              </div>
                <a 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 border border-gkk hover:bg-gkk hover:text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FStartlistor%20rev.%202022-11-08.pdf?alt=media&token=4fde4a7d-c70d-4dcf-96ff-ae13c4448842">
                  <i class="fa fa-file-pdf-o mr-2"></i>Startlistor och tävlingstider (rev. 2022-11-08)
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
