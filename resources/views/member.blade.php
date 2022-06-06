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
          <div>
            <div class="md:max-w-xl lg:max-w-3xl xl:max-w-none">
                <div class="flex items-center">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Medlemskap</h2>
                </div>
                <p class="text-xl leading-normal text-gray-500 mt-4">
                  Som medlem i GKK får man möjlighet att träna i vår fina lokal, att delta i våra gemensamma aktiviteter och att representera föreningen vid tävling om man vill.
                  Men behöver också ställa upp vid några tillfällen varje år då vi själva arrangerar tävlingar, men det handlar om tydliga uppgifter och som ny får man mycket hjälp i början.
                </p>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Priser</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    <b>Medlemsavgift:</b> 1500 kr per år för seniorer, 700 kr per år för studerande, upp till 23 års ålder och för pensionärer.
                    <br>
                    <b>Avgift för tävlingslicens:</b> 700 kr per år, 200 kr till och med 18 års ålder. Obs att under 2021 togs ingen licensavgift ut pga Corona. Denna avgift betalas endast om man vill tävla och i samband med sin första tävling.
                    <br>
                    <b>Träningskort hos Friskis:</b> 15 % rabatt
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Provträning</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Innan du bestämmer dig för medlemskap är du välkommen att provträna hos oss vid ett par tillfällen för att titta på lokalen och träffa några i föreningen. Information om hur man rent praktiskt gör. 
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Öppettider</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Hänvisa till Friskis och nämna att man med kod kan komma in på obemannade tider. 
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Medlemsregister</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    När du har bestämt dig återkommer du till info@gkk-styrkelyft.se och berättar att du vill bli medlem och skickar med ...
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Efter detta kommer faktura för medlemskap skickas ut och när den är betald är du upptagen i föreningen.
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Vill du tävla?</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Före din första tävling behöver du vara med och hjälpa till när GKK arrangerar, både för att lära dig mer om hur en tävling går till och för att lära känna föreningen bättre. På våra interna medlemssidor sker anmälan till sådana funktionärsuppdrag.
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Till förbundet betalas en tävlingslicens in varje år och denna betalar du till GKK i samband med första tävlingsanmälan.
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Anmälningsavgifter till tävlingar står ibland föreningen för, om ekonomi finns. Ibland ges också bidrag för ex. resa och boende om tävlingen sker på annan ort. För de som tävlar mycket finns alltså möjlighet att få tillbaka stora delar av medlemsavgiften den vägen.
                  </p>
                </div>

            </div>
            <ul role="list" class="mt-12 space-y-12 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:gap-y-12 sm:space-y-0 lg:grid-cols-3 lg:gap-x-8">
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
