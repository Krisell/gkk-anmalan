@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-organizer-event :event='@json($event)'></gkk-organizer-event>
        </div>
    </div>
</div>
@endsection
