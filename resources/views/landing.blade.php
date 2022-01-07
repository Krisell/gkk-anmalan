@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/bella-min.jpg); 
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
                {{-- <img style="height: 80px;" class="mr-4" src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png"> --}}
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Välkommen till Göteborg Kraftsportklubb</h2>
                </div>
                <p class="text-xl text-gray-500">
                  Göteborg Kraftsportklubb (GKK) är en idrottsförening som bildades 1933. Vi ägnar oss åt idrotten Styrkelyft och tränar och tävlar därmed i knäböj, bänkpress och marklyft. Vi tränar tillsammans, hjälper varandra och tror på att styrka kommer från gemenskap.
                </p>
                <p class="text-xl text-gray-500">
                  Sedan 2019 har vi vår egna klubblokal hos Friskis & Svettis Majorna i Göteborg. För att träna hos oss behöver du därmed också vara medlem och ha träningskort hos Friskis & Svettis. Träning kan ske även under obemannade tider genom att registrera en kod i kassan. Priser för träning hos friskis <a class="underline" href="https://www.friskissvettis.se/goteborg/harfinnsvi/majorna" target="_blank">hittar du här</a>. Mer information om du är intresserad av att gå med i GKK hittar du under <a class="underline" href="https://gkk.test/member">Medlemsskap</a>.
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
