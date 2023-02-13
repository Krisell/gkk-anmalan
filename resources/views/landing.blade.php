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

                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-gkk text-white leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2F4UcDqNytWB8qo8RqLj7wK93SieyY9X.pdf?alt=media&token=363cd549-67a6-4880-a92b-ae65c1c5acaf">
                    <i class="fa fa-file-pdf-o mr-2"></i>Kallelse och föredragningslista årsmöte 2023-02-11 kl 10.00
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2Fj3GrXdTlcOB97aGhZqUjLUobjPvTxZ.pdf?alt=media&token=ac09827e-17b0-490c-aafe-97fce3a8b029">
                    <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsberättelse 2022, uppdaterad 2023-02-13.
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FZ1g0KkbTb7ePYq7h0QSr02zReZGeKN.pdf?alt=media&token=7fea9ac6-652d-4c62-9076-39a423d1bc0d">
                    <i class="fa fa-file-pdf-o mr-2"></i>Verksamhetsplan 2023
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FoEhjrzG0047RbdGemlL3KQPQFetwPK.pdf?alt=media&token=e430728a-3198-49a9-a419-87930503e888">
                    <i class="fa fa-file-pdf-o mr-2"></i>Budget 2023 samt Resultaträkning 2022
                  </a>
                </div>
                <div>
                  <a 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gkk bg-white text-gkk leading-5 font-medium rounded-md focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                    href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FZ63bWLNajemKOpMlkgJg4YzupkG4se.pdf?alt=media&token=2b14db44-bdb9-4957-ae52-f99b73b5e671">
                    <i class="fa fa-file-pdf-o mr-2"></i>Revisionsberättelse 2022
                  </a>
                </div>

                <p class="text-xl leading-normal text-gray-500">
                  Göteborg Kraftsportklubb (GKK) är en idrottsförening som bildades 1933. Vi ägnar oss åt idrotten Styrkelyft och tränar och tävlar därmed i knäböj, bänkpress och marklyft. Sedan 2022 har vi också ett antal medlemmar som tränar tyngdlyftning med ambitionen att tävla. I föreningen finns allt från motionärer till elitaktiva på högsta nivå. Vi tränar tillsammans, hjälper varandra och tror på att styrka kommer från gemenskap.
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
