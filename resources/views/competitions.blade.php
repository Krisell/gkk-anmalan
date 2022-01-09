@extends('layouts.inside')

@section('inside')
<gkk-competitions :competitions='@json($competitions)' :user-registrations='@json($userRegistrations)'></gkk-competitions>
@endsection
