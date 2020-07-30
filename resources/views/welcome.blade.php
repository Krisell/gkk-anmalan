@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div>
      <img class="mx-auto" style="height: 150px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-navigation :user='@json($user)' :unanswered='@json($unanswered)'></gkk-navigation>

            @auth
            <gkk-news :user='@json($user)' :news='@json($news)'></gkk-news>
            @endauth
        </div>
    </div>
</div>
@endsection
