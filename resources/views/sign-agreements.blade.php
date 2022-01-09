@extends('layouts.inside')

@section('inside')
<div class="container mx-auto">
    <div>
      <img class="mx-auto" style="height: 150px;" src="https://goteborg-kraftsportklubb.web.app/img/logo-min.png">
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-agreements :user='@json($user)'></gkk-agreements>
        </div>
    </div>
</div>
@endsection
