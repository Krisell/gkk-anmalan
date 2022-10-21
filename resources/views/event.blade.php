@extends('layouts.inside')

@section('inside')
<gkk-event
    :event='@json($event)'
    :user='@json($user)'
    :_registration='@json($registration)'>
</gkk-event>
@endsection
