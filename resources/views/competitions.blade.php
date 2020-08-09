@extends('layouts.app')

@section('content')
<gkk-competitions :competitions='@json($competitions)' :user-registrations='@json($userRegistrations)'></gkk-competitions>
@endsection
