@extends('layouts.app')

@section('content')
@auth
<div class="mt-24 mb-8 md:ml-[240px]">
@else
<div class="mt-24 mb-8">
@endif
    @yield('inside')
</div>
@endsection
