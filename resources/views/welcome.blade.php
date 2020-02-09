@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-navigation :user='@json($user)' :unanswered='@json($unanswered)'></gkk-navigation>
        </div>
    </div>
</div>
@endsection
