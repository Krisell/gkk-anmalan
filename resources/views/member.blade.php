@extends('layouts.app')

@section('content')
<div style="background-image: url(https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2F258842533_1052725082195945_2490428104765383155_n.jpg?alt=media&token=0c5be85f-7bb1-4af8-9765-66534822320a); 
height: 500px;
background-size: cover;
background-position-y: 90%;" class="flex items-center">
</div>
<div class="container mx-auto">
    <div>
        <div class="mx-auto py-12 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
                <div class="flex items-center">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Medlemsskap</h2>
                </div>
                <p class="text-xl text-gray-500">Berätta om avgifter, rättigheter och skyldigheter, och hur man rent praktiskt gör.</p>
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
