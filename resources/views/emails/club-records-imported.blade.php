@component('mail::message')
# {{ count($records) === 1 ? 'Nytt klubbrekord' : count($records).' nya klubbrekord' }}

Hej,

Följande klubbrekord har lagts till automatiskt från resultaten på data.styrkelyft.se:

@foreach ($records as $record)
**{{ $record['name'] }}**, {{ $record['gender'] === 'F' ? 'Damer' : 'Herrar' }} {{ $record['weight_class'] }} kg<br>
🏋️ {{ $record['event'] }}: {{ $record['result'] }} kg (tidigare: {{ $record['previous'] }})<br>
📅 {{ $record['date'] }}, {{ $record['competition'] }}

@endforeach
Stämmer något inte, till exempel ett resultat som rättats i efterhand, går rekordet att ta bort under Klubbrekord i admin.

@component('mail::button', ['url' => url('/admin/results')])
Visa klubbrekorden
@endcomponent
@endcomponent
