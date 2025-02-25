@extends('layouts.app')

@section('content')
<div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/kamratskap.jpeg); 
height: 500px;
background-size: cover;
background-position-y: 30%; background-position-x: 50%; max-height: 50vh;" class="flex items-center">
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
                  Man behöver också ställa upp vid några tillfällen varje år då vi själva arrangerar tävlingar, och som ny får man naturligtvis mycket hjälp.
                </p>

                <div class="mt-8">
                <h2 class="text-xl font-extrabold tracking-tight -mb-2">Vill du bli medlem?</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Börja med att läsa den information som finns här på vår hemsida, och kom till oss och provträna (se info nedan). När du har bestämt dig återkommer du till info@gkk-styrkelyft.se och berättar att du vill bli medlem samt skickar med adress, namn, personnummer, telefon och mailadress. Om du är student så skickar du även med bevis på det (ex. bild från Mecenat-appen eller registreringsbevis).
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Efter detta kommer faktura för medlemskap skickas ut och när den är betald är du upptagen i föreningen.
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Du kommer också bjudas in till vår medlemswebb där du bland annat behöver godkänna vårt <a class="underline" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FAntidopingavtal.pdf?alt=media&token=f43b48b7-62e8-405e-8b3e-f75ac2c465c6">antidopingavtal</a> och <a class="underline" target="_blank" href="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/documents%2FMedlemsavtal%20GKK%202022-09-20.pages.pdf?alt=media&token=52b3f7dd-e27c-49f2-a63d-0bdf2a0a0188">medlemsavtal</a>.
                  </p>
              </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight-mb-2">Avgifter</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    <div class="text-xl leading-normal text-gray-500"><b>Medlemsavgift:</b> 1500 kr per år för seniorer, 700 kr per år för studerande, ungdom/junior (upp till året man fyller 23) och för pensionärer.</div>
                    <div class="text-xl leading-normal text-gray-500 mt-2"><b>Avgift för tävlingslicens:</b> För ungdomar (året man fyller 18): 200kr, för övriga 900 kr per år. Denna avgift betalas endast om man vill tävla och i samband med sin första tävling. Föreningen skickar ut faktura efter i samband med första tävlingen för året. <b>Under 2025 har GKK beslutat att subventionera tävlingslicensen för alla upp till 25 år med 300 kr.</b></div>
                    <div class="text-xl leading-normal text-gray-500 mt-2"><b>Träningskort hos Friskis:</b> 15 % rabatt. Se <a target="_blank" class="underline" href="https://www.friskissvettis.se/goteborg/tranahar/majorna">Friskis och Svettis i Majorna</a>.</div>
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Provträning</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Innan du behöver bestämma dig om medlemskap är du välkommen att provträna hos oss några gånger för att titta på lokalen och träffa medlemmar. Om du känner någon i GKK kan du följa med den personen på ett pass. Annars kan du kontakta oss på info@gkk-styrkelyft.se och skriva att du vill provträna och när. Vi försöker då se till att någon finns på plats som kan ta emot dig och berätta lite om oss och hjälpa dig med lyften. Vill du chansa kan du dyka upp en vardag från kl 16 i vår lokal, då det nästan alltid finns medlemmar på plats.
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Öppettider</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Se <a target="_blank" class="underline" href="https://www.friskissvettis.se/goteborg/tranahar/majorna">Friskis och Svettis i Majornas</a> hemsida för bemannade öppetider.
                    Som medlem kan man även komma in på obemannade tider.
                  </p>
                </div>

                <div class="mt-8">
                  <h2 class="text-xl font-extrabold tracking-tight -mb-2">Tävlingssugen?</h2>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Före din första tävling behöver du vara med och hjälpa till när GKK arrangerar, både för att lära dig mer om hur en tävling går till och för att lära känna föreningen bättre. På våra interna medlemssidor sker anmälan till sådana funktionärsuppdrag. Som tävlingsaktiv förväntas du hjälpa till på våra arrangemang vid minst ett tillfälle per år.
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Till förbundet betalas en tävlingslicens in varje år och denna betalar du till GKK i samband med första tävlingsanmälan (se ovan).
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Anmälningsavgifter till större tävlingar står ibland föreningen för, om ekonomi finns. Ibland ges också visst bidrag för ex. resa och boende om tävlingen sker på annan ort.
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">

                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    Innan du gör din första tävling för GKK måste du genomföra utbildningen <b>Ren Vinnare</b> som du finner på <a class="underline" target="_blank" href="https://www.renvinnare.se">www.renvinnare.se</a>. När utbildningen är genomförd får du ett diplom som du ska maila in till info@gkk-styrkelyft.se. Diplomet ska vara klubben tillhanda innan du gör din första tävling (obligatoriskt för att få tävla). Även om du inte har för avsikt att tävla så rekommenderar vi denna utbildning då den är mycket informativ och bra för alla idrottare.
                  </p>
                  <p class="text-xl leading-normal text-gray-500 mt-4">
                    I vår anmälningsportal kommer du också få läsa och godkänna vårt antidopingavtal.
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
