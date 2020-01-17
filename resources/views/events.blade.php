@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-events :events='@json($events)' :user-registrations='@json($userRegistrations)'></gkk-event>
        </div>
    </div>
</div>
@endsection
