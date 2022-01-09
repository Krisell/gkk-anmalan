@extends('layouts.inside')

@section('inside')
<gkk-events :events='@json($events)' :user-registrations='@json($userRegistrations)'></gkk-events>
@endsection
