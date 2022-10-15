@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/erik-boj.jpeg); 
height: 500px;
background-size: cover;
background-position-y: center; max-height: 50vh;" class="flex items-center">
</div>
<div class="container mx-auto">
    <div>
        <div class="mx-auto py-12 px-4 max-w-7xl sm:px-6 lg:px-8">
          <div class="space-y-12">
            <div class="space-y-5 sm:space-y-4 md:max-w-xl lg:max-w-3xl xl:max-w-none">
                <div>
                  <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Dokument</h2>
                  <div class="mt-4">
                    <i class="fa fa-file-pdf-o"></i>
                    <a class="underline" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6">Antidopingavtal mellan medlem och förening (PDF)</a>
                  </div>
                  <div class="mt-4">
                    <i class="fa fa-file-pdf-o"></i>
                    <a class="underline" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188">Medlemsavtal mellan medlem och förening (PDF)</a>
                  </div>
                  <div class="mt-4">
                    <i class="fa fa-file-pdf-o"></i>
                    <a class="underline" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/uploaded%2FN0TGPEf5Nto75XNm2Nux7Lpojez7pz.pdf?alt=media&token=3204252d-8c0c-43f0-baa2-de7b11ca277c">Föreningens stadgar, antagna 2022-02-19 (PDF)</a>
                  </div>
                  <div class="mt-4">
                    <i class="fa fa-file-pdf-o"></i>
                    <a class="underline" target="_blank" href="https://www.gkk-styrkelyft.se/wp-content/uploads/2020/03/gkk-integritetspolicy.pdf">Föreningens integritetspolicy (PDF)</a>
                  </div>
                  <div class="mt-4">
                    <i class="fa fa-link"></i>
                    <a class="underline" target="_blank" href="https://www.styrkelyft.se/dokument/dokument-blanketter">Dokument för tävling mm på förbundets hemsida</a>
                  </div>
                </div>
                
                <div>
                  <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl mt-12">Träningsprogram</h2>
                  <div class="flex mt-4">
                    <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FBeard-Press%202021-02-14.xlsx?alt=media&token=30924370-12aa-4ddf-8bf1-2fd45cc70717">
                      <img width="200px" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2021/02/beardpress-logo-excel.png">
                    </a>
                    <div class="p-4">
                      <b>Beard Press</b><br>
                      Ett bänkpressprogram av Karl Malmberg (@kraftkarlos)<br>
                      <span class="text-gray-500 mt-2">Excelfil</span>
                    </div>
                  </div>
                  <div class="flex mt-4">
                    <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FBa%CC%88nkprogram%20CalleO%CC%88berg.xlsx?alt=media&token=731f217f-fe83-4950-88b6-402515862445">
                      <img width="220px" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2021/07/hemside-bild.png">
                    </a>
                    <div class="p-4">
                      <b>Carl Öbergs bänkpressprogram</b><br>
                      <span class="text-gray-500 mt-2">Excelfil</span>
                    </div>
                  </div>
                </div>
                <div>
                  <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl mt-12">Grafik</h2>
                  <div class="flex items-center mt-2">
                    <img width="100px" src="http://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%C3%A5f%C3%A4rg-p%C3%A5-ljus-bakgrund.png">
                    <div class="ml-2">Tvåfärg mot ljus bakgrund</div>
                  </div>
                  <div class="flex items-center mt-2">
                    <img width="100px" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%C3%A5f%C3%A4rg-p%C3%A5-m%C3%B6rk-bakgrund-300x300.png">
                    <div class="ml-2">Tvåfärg mot mörk bakgrund (för tryck och utskrift)</div>
                  </div>
                  <div class="flex items-center mt-2">
                    <img width="100px" src="http://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%C3%A5f%C3%A4rg-p%C3%A5-m%C3%B6rk-bakgrund-transparent.png">
                    <div class="ml-2">Tvåfärg mot mörk bakgrund (transparent)</div>
                  </div>
                  <div class="flex items-center mt-2">
                    <img width="100px" src="http://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Monokrom-p%C3%A5-m%C3%B6rk-bakgrund.png">
                    <div class="ml-2">Monokrom mot mörk bakgrund</div>
                  </div>
                  <div class="mt-4"> 
                    <i class="fa fa-file-pdf-o"></i>
                    <a class="underline" target="_blank" href="http://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Monokrom-p%C3%A5-m%C3%B6rk-bakgrund.pdf">Monokrom mot mörk bakgrund för tryck/utskrift (PDF)</a>
                  </div>
                  <div class="mt-4">
                    <i class="fa fa-file-pdf-o"></i>
                    <a class="underline" target="_blank" href="http://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%C3%A5f%C3%A4rg-p%C3%A5-m%C3%B6rk-bakgrund.pdf">Tvåfärg på mörk bakgrund för tryck/utskrift (PDF)</a>
                  </div>
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
