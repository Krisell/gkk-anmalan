@extends('layouts.app')

@section('content')
<gkk-events :events='@json($events)' :user-registrations='@json($userRegistrations)'></gkk-events>
@endsection
