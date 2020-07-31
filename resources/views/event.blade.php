@extends('layouts.app')

@section('content')
<gkk-event
    :event='@json($event)'
    :user='@json($user)'
    :_registration='@json($registration)'>
</gkk-event>
@endsection
