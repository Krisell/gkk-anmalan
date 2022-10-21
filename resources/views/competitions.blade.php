@extends('layouts.inside')

@section('inside')
<gkk-competitions 
    :competitions='@json($competitions)' 
    :user-registrations='@json($userRegistrations)'
    :user='@json($user)'
></gkk-competitions>
@endsection
