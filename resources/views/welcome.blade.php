@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div>
      <img class="mx-auto" style="height: 150px;" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-transparent%20(1).png?alt=media&token=4973abb5-6670-4aec-b036-3d14c30a2584">
    </div>

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
