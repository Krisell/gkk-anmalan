@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/glad-simon.webp); 
height: 500px;
background-size: cover;
background-position-y: 40%; background-position-x: 100%; max-height: 50vh;" class="flex items-center">
</div>
<div class="container mx-auto">
    <div>
        <div class="mx-auto py-12 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
                <div class="flex items-center">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Musikhjälpen</h2>
              </div>
              <div>
                <h3 class="text-2xl font-extrabold tracking-tight sm:text-2xl">Göteborg Kraftsportklubb lyfter 1 kg per donerad krona.</h3>
              </div>
              <div
                target="_blank"
                class="mt-2 inline-flex items-center px-4 py-2 border text-white leading-5 font-medium  focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                href="https://bossan.musikhjalpen.se/goeteborg-kraftsportklubb-lyfter-1-kg-per-donerad-krona">
                <img width="60px" src="https://goteborg-kraftsportklubb.web.app/mh-logo.png?v=2">
                <h3 class="ml-4 text-black text-xl font-bold">Donerat: {{ $donatedAmount }}</h3>
              </div>
              <div>
                <h2 class="text-xl font-extrabold tracking-tight sm:text-xl"><i class="fa fa-map-marker mr-2 text-4xl"></i><i class="fa fa-clock-o mr-2 text-4xl"></i>Onsdagen den 14 december finner du oss på Musikhjälpens eventyta, mellan klockan 17 och 21. 16:30 gästar vi även i buren. Varmt välkomna att heja, eller testa på grenarna och hjälpa oss samla kilon!</h2>
                
              </div>
                <div>
                  <a 
                  target="_blank"
                  class="shadow-lg inline-flex items-center px-4 py-2 border hover:scale-105 bg-[#01b08e] text-white leading-5 font-medium  focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                  href="https://bossan.musikhjalpen.se/goeteborg-kraftsportklubb-lyfter-1-kg-per-donerad-krona">
                  <img width="100px" src="https://goteborg-kraftsportklubb.web.app/mh-logo.png">
                  <h3 class="ml-4 text-white text-2xl font-bold">Öppna bössan</h3>
                </a>
                </div>

                <div>
                <div
                  class="mt-2 inline-flex items-center px-4 py-2 border text-white leading-5 font-medium  focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                >
                  <img width="60px" src="https://goteborg-kraftsportklubb.web.app/mh-logo.png?v=2">
                  <h3 class="ml-4 text-black text-xl font-bold">Antal lyfta kilon: 0 kg</h3>
                </div>

                <div
                  class="mt-2 inline-flex items-center px-4 py-2 border text-white leading-5 font-medium  focus:outline-none focus:shadow-outline-indigo transition duration-150 ease-in-out" 
                >
                  <img width="60px" src="https://goteborg-kraftsportklubb.web.app/mh-logo.png?v=2">
                  <h3 class="ml-4 text-black text-xl font-bold">Kvar att lyfta: {{ str_replace('kr', 'kg', $donatedAmount) }}</h3>
                </div>

                <div>
                  <h3 class="text-black text-xl font-bold mt-6">Vi lyfter 1 kg för varje donerad krona!</h3>
                </div>
                <div>
                  <h3 class="text-black text-xl font-bold mt-6">Kom och prova på styrkelyft!</h3>
                </div>
                </div>
                <div>
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
