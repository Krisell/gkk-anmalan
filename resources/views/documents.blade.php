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
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Dokument & Träningsprogram</h2>
                </div>
                <p class="text-xl text-gray-500">TODO: Motsvarande det som idag ligger på "Dokument" på hemsidan.</p>
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
