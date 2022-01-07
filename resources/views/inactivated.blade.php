@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div>
      <img class="mx-auto" style="height: 150px;" src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png">
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div style="text-align: center">
                <div>
                  <h3 class="text-center mt-6 font-thin text-xl">
            På grund av inaktivitet har ditt konto inaktiverats. <br>Vänligen kontakta styrelsen via Discord eller info@gkk-styrkelyft.se för att återaktivera ditt konto.
                  </h3>
                </div>
                <gkk-link to="/" text="Försök igen"></gkk-link>
            </div>
        </div>
    </div>
</div>
@endsection
