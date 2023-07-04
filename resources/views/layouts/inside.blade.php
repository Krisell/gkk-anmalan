@extends('layouts.app')

@section('content')
@auth
<div class="mt-36 mb-8 md:ml-[210px]">
@else
<div class="mt-36 mb-8">
@endif
    @yield('inside')
</div>
@endsection
