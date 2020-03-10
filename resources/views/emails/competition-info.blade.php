@component('mail::message')
# Anmälan till Serie 2 – Tävling och funktionär

Hej!

Nu ligger både funktionärs- och tävlingsanmälan uppe på vår nya anmälningssida <a href="https://anmalan.gkk-styrkelyft.se">https://anmalan.gkk-styrkelyft.se</a>.

Vi understryker att det är obligatoriskt för samtliga medlemmar att fylla i funktionärsanmälan.

Har du inte ett konto ännu kan du snabbt och enkelt registrera dig här:

@component('mail::button', ['url' => $link])
{{ __('Klicka här för att skapa ett konto') }}
@endcomponent

Hälsningar,<br>
{{ config('app.name') }}
@endcomponent
