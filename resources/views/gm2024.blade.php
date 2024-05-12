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
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Götalandsmästerskapen 2024 i KSL och KBP</h2>
              </div>
              <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">10-12 maj 2024 - Göteborg Kraftsportklubb - Friskis och Svettis i Majorna</h2>
                <p class="text-xl leading-normal text-gray-500">
                  Götalandsmästerskapen i Klassisk Styrkelyft och Klassisk Bänkpress går av stapeln 10-12 maj hos GKK, på Friskis och Svettis i Majorna.
                </p>
                {{-- <img class="w-64 shadow-xl rounded-xl" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Flogo%20centered.png?alt=media&token=f0ea09ac-77b6-4e8a-b532-68cd7b3953e1"> --}}

                <a 
                class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FGM2024Resultat.pdf?alt=media&token=36d75118-d229-4e9a-a0df-07aca2460f48"
                target="_blank">
                <i class="fa fa-file-pdf-o mr-2"></i>Resultat Götalandsmästerskapen 2024
              </a>
              <div></div>

                <a 
                class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                href="https://drive.google.com/drive/u/1/folders/1j93alpHQXl39rY0pC6iVb92QOLNNarlg"
                target="_blank">
                <i class="fa fa-youtube-play mr-2"></i>Stream fredag & lördag
              </a>
              <a 
                class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                href="https://www.youtube.com/watch?v=_EQ4qmLdgVM"
                target="_blank">
                <i class="fa fa-youtube-play mr-2"></i>Stream Söndag - Start 08.30
              </a>
              <div></div>

                <div class="rounded p-2 border-gkk border-2">
                  <p class="text-3xl leading-bold text-gkk">
                    Information om Bastu
                  </p>
                  <p class="text-xl leading-bold text-gkk">
                    Tyvärr kommer bastu inte finnas tillgänglig <b>efter klockan 14 på lördag</b>. Övriga invägningar under helgen, inkl. söndag, har tillgång till bastu. Den går att starta klockan 06 både lördag och söndag inför de tidigaste invägningarna.
                  </p>
                </div>

                <div class="rounded p-2 border-gkk border-2">
                  <p class="text-3xl leading-bold text-gkk">
                    Foto / media
                  </p>
                  <p class="text-xl leading-bold text-gkk">
                    För foto/media som bevistar Götalandsmästerskapen i kommersiellt syfte gäller en avgift på 1000:- per tävlingsdag som ska vara betald till arrangören i förskott via faktura. Ansök genom att kontakta oss på info@gkk-styrkelyft.se med kopia till kassor@gkk-styrkelyft.se. Erlagd mediaavgift tillfaller aktuell tävlingsarrangör.
                  </p>
                </div>


              <div class="mt-8"></div>
                <a 
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://online.styrkelyft.se/web/Docs/3781/2972.pdf"
                  target="_blank">
                  <i class="fa fa-file-pdf-o mr-2"></i>Inbjudan Götalandsmästerskapen 2024
                </a>
                <div></div>


                <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FAnma%CC%88lda%20GM%202024%202024-04-22.pdf?alt=media&token=6d8bcf6c-dcec-417b-92af-627042c2eb44">
                  <p 
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out hover:bg-gkk hover:text-white" 
                    ><i class="fa fa-file-pdf-o mr-2"></i>Startlistor och tävlingstider (uppd. 2024-04-22)</p>
                </a>
                <div></div>
                <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FLottnummer_och_invagningsordning%20(4).pdf?alt=media&token=c40fde28-4d96-4c0d-9af8-b5a9f886f744">
                  <p 
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out hover:bg-gkk hover:text-white" 
                    ><i class="fa fa-file-pdf-o mr-2"></i>Lottnummer och invägningsordning (uppd. 2024-04-22)</p>
                </a>
                <div></div>

                <a target="_blank" href="https://www.facebook.com/events/6992337070876164">
                  <p 
                  class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    ><svg class="w-8 mr-2 h-8" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 30 30">
                      <path d="M15,3C8.373,3,3,8.373,3,15c0,6.016,4.432,10.984,10.206,11.852V18.18h-2.969v-3.154h2.969v-2.099c0-3.475,1.693-5,4.581-5 c1.383,0,2.115,0.103,2.461,0.149v2.753h-1.97c-1.226,0-1.654,1.163-1.654,2.473v1.724h3.593L19.73,18.18h-3.106v8.697 C22.481,26.083,27,21.075,27,15C27,8.373,21.627,3,15,3z"></path>
                  </svg>Klicka för att se Facebookevent för tävlingen</p>
                </a>
                {{-- <p class="mt-4 text-xl leading-normal text-gray-500"><b>Tävlingen sker med Eleiko-ställning av nyare modell.</b></p> --}}
                <p class="mt-4 text-xl leading-normal text-gray-500"><b>Plats:</b> Göteborg Kraftsportklubb, Karl Johansgatan 152, 414 51 Göteborg</p>
                <p class="text-xl leading-normal text-gray-500"><b>Antal deltagare:</b> 182 (!)</p>
                <p class="text-xl leading-normal text-gray-500"><b>Antal deltagande föreningar: </b> 34</p>
                <p class="text-xl leading-normal text-gray-500"><b>Entreavgift:</b> Valfri</p>
                <p class="text-xl leading-normal text-gray-500"><b>Livestream:</b> Sänds via YouTube. Scrolla längst upp för länkar.</p>
                <p class="text-xl leading-normal text-gray-500"><b>Kiosk:</b> På friskis finns att köpa enklare förtäring såsom mackor, korv och dryck. 10 minuter promenad bort finns Coop och en mängd restauranger.</p>
                <p class="text-xl leading-normal text-gray-500"><b>Tävlingsställning:</b> Planerad tävlingsställning är Eleikos tidigare mjuka modell, se bild.</p>
                <div>
                  <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fbank.png?alt=media&token=390adbd2-b620-4bfc-aae3-e502f6056b40">
                  <img 
                    class="rounded-2xl shadow-xl cursor-pointer inline-block ml-2 w-[500px]" 
                    src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fbank.png?alt=media&token=390adbd2-b620-4bfc-aae3-e502f6056b40"
                  >
                  </a>
                </div>

                <p class="text-xl leading-normal text-gray-500"><b>Parkering:</b> Ett begränsat antal parkeringar finns i direkt anslutning till tävlingshallen. Fler parkeringar finns i närområdet - se karta.</p>
                <div>
                  <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fkarta%20(1).png?alt=media&token=5668792a-f351-4175-b1ab-87e0b59215f0">
                  <img 
                  class="rounded-2xl shadow-xl cursor-pointer inline-block ml-2 w-[500px]"
                    src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fkarta%20(1).png?alt=media&token=5668792a-f351-4175-b1ab-87e0b59215f0"
                  ></a>
                </div>
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
