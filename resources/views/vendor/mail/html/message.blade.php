@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
        <img style="width: 100px; margin: auto;" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-transparent%20(1).png?alt=media&token=4973abb5-6670-4aec-b036-3d14c30a2584">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}
        @endcomponent
    @endslot
@endcomponent
