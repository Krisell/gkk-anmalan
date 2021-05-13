@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div>
      <img class="mx-auto" style="height: 150px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-agreements :user='@json($user)'></gkk-agreements>
        </div>
    </div>
</div>
@endsection
