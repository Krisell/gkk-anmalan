@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-admin-organizer-event :event='@json($event)'></gkk-admin-organizer-event>
        </div>
    </div>
</div>
@endsection
