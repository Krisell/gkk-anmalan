@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bella-min.jpg); 
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
                  <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Välkommen till Göteborg Kraftsportklubb</h2>
                </div>

                {{-- <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FHzu1KcuyMUmOkGTnxQWqBOjrmrS0CA.pdf?alt=media&token=e7393521-554f-4591-aab6-b78a735d87e2">
                    <i class="fa fa-file-pdf-o mr-2"></i>Kallelse och föredragningslista årsmöte 2024-02-10 kl 10.00
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FLbWn2RCC8LrT0wVJTWOK2ZbwfyIY7r.pdf?alt=media&token=2f57c4a8-c3d3-42fc-b4bb-76a7e367abad">
                    <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsberättelse 2023, uppdaterad 2024-02-23.
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FVerksamhetsplan%20och%20budget%202024.pdf?alt=media&token=d15bbc76-f0dd-4d2a-a678-11b60fe2c79d">
                    <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsplan och budget 2024
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FEkonomiskt%20resultat%202023.pdf?alt=media&token=990c709a-2ad8-41cb-997f-5898c306db36">
                    <i class="fa fa-file-pdf-o mr-2"></i>Ekonomiskt resultat 2023
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FValberedningens%20fo%CC%88rslag%202024.pdf?alt=media&token=d6ba9bf8-9826-4344-888e-c290cd265bbd">
                    <i class="fa fa-file-pdf-o mr-2"></i>Valberedningens förslag för 2024
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FRevisionsberattelse_2023_Goteborg_Kraftsportklubb%20(1).pdf?alt=media&token=c5d4d798-82e6-4d86-995c-4cef2cf38bfc">
                    <i class="fa fa-file-pdf-o mr-2"></i>Revisionsberättelse 2023
                  </a>
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


                {{-- <a href="/prova-pa" class="mt-6 block">
                  <div class="rounded p-2 border-gkk border-2">
                    <p class="text-3xl leading-bold text-gkk">
                      Är du intresserad av att prova på att tävla i styrkelyft eller bänkpress? 
                    </p>
                    <p class="text-xl leading-bold text-gkk">
                      Den 4 februari 2024 håller GKK en prova-på-tävling där alla som inte ännu tävlar i styrkelyft
                      får möjligheten att testa hur det är! Mer information om tävlingen <span class="underline">hittar du här</span>.
                    </p>
                  </div>
                </a> --}}

                <p class="text-xl leading-normal text-gray-500">
                  Göteborg Kraftsportklubb (GKK) är en idrottsförening som bildades 1933. Vi ägnar oss åt idrotten Styrkelyft och tränar och tävlar därmed i knäböj, bänkpress och marklyft. I föreningen finns allt från motionärer till elitaktiva på högsta nivå. Vi tränar tillsammans, hjälper varandra och tror på att styrka kommer från gemenskap.
                </p>
                <p class="text-xl leading-normal text-gray-500">
                  Sedan december 2018 har vi vår egna klubb- och träningslokal hos <a class="underline" href="https://www.friskissvettis.se/goteborg/harfinnsvi/majorna" target="_blank">Friskis & Svettis Majorna</a> i Göteborg. För att träna hos oss behöver du därmed också vara medlem och ha träningskort hos Friskis & Svettis. Träning kan ske även under obemannade tider genom att registrera en kod i kassan. Mer information om du är intresserad av att bli medlem i GKK hittar du under <a class="underline" href="/medlem">Medlemskap</a>.
                </p>
                <p class="text-xl leading-normal text-gray-500">
                  GKK finns även på <a class="underline" href="https://www.instagram.com/goteborgkk/" target="_blank">Instagram</a> där du kan hitta foton och löpande information från föreningen.
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
