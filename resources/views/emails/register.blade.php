@component('mail::message')
# Välkommen att registrera dig på GKKs anmälningssidor

Om du ännu inte har skapat ett konto på GKKs nya anmälningssidor så kan du klicka på knappen nedan och välja lösenord för att direkt skapa ett konto.

Därefter kan du klicka på *Funktionärsanmälan* och ange om du kan komma på årsmötet eller inte.

@component('mail::button', ['url' => $link])
{{ __('Klicka här för att skapa ett konto') }}
@endcomponent

Om du redan har ett konto kan du bortse från detta mail, och logga in via <a href="https://anmalan.gkk-styrkelyft.se/login">https://anmalan.gkk-styrkelyft.se/login</a>

Hälsningar,<br>
{{ config('app.name') }}
@endcomponent
