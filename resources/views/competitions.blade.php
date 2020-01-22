@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-competitions :competitions='@json($competitions)' :user-registrations='@json($userRegistrations)'></gkk-competitions>
        </div>
    </div>
</div>
@endsection
