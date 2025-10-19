@component('mail::message')
@if($deadlineType === '5_days_before')
# Påminnelse: 5 dagar kvar till anmälningsslut

Hej,

Det är 5 dagar kvar till sista anmälningsdag för följande tävling:

@elseif($deadlineType === 'last_day')
# Sista dagen för anmälan

Hej,

Idag är sista dagen för anmälan till följande tävling:

@else
# Anmälningsperiod avslutad - dags att skicka in anmälan

Hej,

Anmälningsperioden för följande tävling avslutades igår. Det är nu dags att skicka in anmälan till arrangören:

@endif

**Tävlingsdetaljer**<br>
📋 Namn: {{ $competition->name }}<br>
📅 Datum: {{ $competition->date->format('Y-m-d') }}<br>
⏰ Sista anmälningsdag: {{ $competition->last_registration_at->format('Y-m-d') }}<br>
@if($competition->description)
📝 Beskrivning: {{ $competition->description }}<br>
@endif

@if($deadlineType === 'day_after')
**Nästa steg:**
- Logga in på Insidan för att se alla anmälda deltagare
- Sammanställ och skicka in anmälan till arrangören

@endif
@component('mail::button', ['url' => config('app.url') . '/admin/competitions/' . $competition->id])
Visa tävling i Insidan
@endcomponent

Med vänliga hälsningar,<br>
{{ config('app.name') }}
@endcomponent
