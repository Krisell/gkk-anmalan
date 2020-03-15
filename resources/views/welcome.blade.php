@extends('layouts.app')

@section('content')
<div class="container">
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
