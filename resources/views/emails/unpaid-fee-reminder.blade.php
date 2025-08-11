@component('mail::message')
# Påminnelse om obetald avgift

Hej {{ $user->first_name }},

Du har en obetald avgift som behöver betalas.

**Detaljer**<br>
ℹ️ Avgiften gäller: {{ $typeText }}<br>
ℹ️ År: {{ $payment->year }}<br>
ℹ️ Belopp: {{ $payment->sek_amount }} SEK<br>

Om du loggar in på Insidan och klickar på "Profil" kan du se dina obetalda fakturor.

Betalning sker genom Swish-QR-kod direkt på fakturan, eller genom manuell betalning enligt info på https://goteborgkk.se/gkk

Kontakta styrelsen via info@gkk-styrkelyft.se om du har några frågor om avgiften.

Med vänliga hälsningar,<br>
{{ config('app.name') }}
@endcomponent