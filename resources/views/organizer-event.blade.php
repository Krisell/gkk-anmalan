@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-organizer-event
                :event='@json($event)'
                :user='@json($user)'
                :_registration='@json($registration)'>
            </gkk-organizer-event>
        </div>
    </div>
</div>
@endsection
