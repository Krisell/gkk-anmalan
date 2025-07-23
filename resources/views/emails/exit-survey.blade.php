@component('mail::message')
# Enkät till tidigare medlem i GKK

Hej {{ $user->first_name }}!

Du får detta mail för att du tidigare varit medlem i GKK men inte längre är aktiv.

Vi har satt ihop en kort enkät för att samla in feedback från tidigare medlemmar. Det tar bara några minuter att fylla i, och din feedback är värdefull för oss.

@component('mail::button', ['url' => 'https://forms.gle/aMAv7T1QgVXXZc3Z6'])
Svara på enkäten
@endcomponent

Hälsningar GKKs styrelse<br>
@endcomponent
