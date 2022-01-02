@extends('layouts.app')

@section('content')
<div style="background-image: url(https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_1991-as-Smart-Object-1psSmall-1.jpg); 
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
                {{-- <img style="height: 80px;" class="mr-4" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png"> --}}
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Om Styrkelyft</h2>
                </div>
                <p class="text-xl text-gray-500">I styrkelyft tävlar man i grenarna knäböj, bänkpress och marklyft. Man kan också tävla i enbart bänkpress. Det finns tävlingar där utrustning (ex. knälindor och böjdräkt) är tillåtet, och det finns tävlingar i klassisk styrkelyft där ingen hjälpande utrustning utöver knävärmare och handledslindor är tillåtet. Som ny i sporten rekommenderar vi att man börjar fokuseras på den klassiska lyftningen, som också är störst idag.</p>
            </div>
            <ul role="list" class="space-y-12 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:gap-y-12 sm:space-y-0 lg:grid-cols-3 lg:gap-x-8">
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_1991-as-Smart-Object-1psSmall-1.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_1879-as-Smart-Object-1psSmall_v2.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_1882-as-Smart-Object-1psSmall-e1584523116204.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_1888-as-Smart-Object-1psSmall.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_1951-as-Smart-Object-1psSmall.jpg" alt="">
                  </div>
                </div>
              </li>
              <li>
                <div class="space-y-4">
                  <div class="aspect-w-3 aspect-h-2">
                    <img style="height: 300px" class="object-cover shadow-lg rounded-lg" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/MG_2636-as-Smart-Object-1psSmall_v2.jpg" alt="">
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
