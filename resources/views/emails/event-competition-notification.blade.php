@component('mail::message')
# {{ $item->name }}

@if($type === 'competition')
**Ny tävlingsanmälan är öppen!**
@else
**Ny funktionärsanmälan är öppen!**
@endif

@if($item->date)
**Datum:** {{ $item->date }}@if($item->end_date) – {{ $item->end_date }}@endif
@endif

@if($item->time)
**Tid:** {{ $item->time }}
@endif

@if($item->location)
**Plats:** {{ $item->location }}
@endif

@if($item->last_registration_at)
**Sista anmälningsdag:** {{ $item->last_registration_at }}
@endif

@if($type === 'competition' && $item->events)
@php
    $events = json_decode($item->events, true);
    $eventList = [];
    if(isset($events['ksl']) && $events['ksl']) $eventList[] = 'KSL';
    if(isset($events['kbp']) && $events['kbp']) $eventList[] = 'KBP';
    if(isset($events['sl']) && $events['sl']) $eventList[] = 'SL';
    if(isset($events['bp']) && $events['bp']) $eventList[] = 'BP';
@endphp
@if(count($eventList) > 0)
**Grenar:** {{ implode(', ', $eventList) }}
@endif
@endif

@if($item->description)

---

{!! nl2br(e($item->description)) !!}
@endif

@component('mail::button', ['url' => $type === 'competition' ? url('/competitions/'.$item->id) : url('/events/'.$item->id)])
Gå till anmälan
@endcomponent

<br>
Logga in på GKK:s anmälningssystem för att anmäla dig!

<br>
Göteborg Kraftsportklubb
@endcomponent
