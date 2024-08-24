@component('mail::message')
# En ny medlem har skapat ett konto

En ny medlem har skapat ett konto som har blivit godkänt.


{{ $name }}
{{ $email }}

Hälsningar,<br>
{{ config('app.name') }}
@endcomponent
