@extends('layouts.inside')

@section('inside')
<gkk-admin-event :event='@json($event)'></gkk-admin-event>
@endsection
