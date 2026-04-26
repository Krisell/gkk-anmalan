@extends('layouts.app')

@section('main-class', '')

@section('content')
@auth
<div class="mt-24 mb-8 md:ml-[240px] page-transition">
@else
<div class="mt-24 mb-8 page-transition">
@endif
    @yield('inside')
</div>
@endsection
