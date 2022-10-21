@extends('layouts.inside')

@section('inside')
<div class="container mx-auto">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-navigation :user='@json($user)' :unanswered='@json($unanswered)'></gkk-navigation>

            @auth
                @if ($user->granted_by != 0)
                <gkk-news :user='@json($user)' :news='@json($news)'></gkk-news>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection
