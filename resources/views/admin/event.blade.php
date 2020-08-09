@extends('layouts.app')

@section('content')
<gkk-admin-event :event='@json($event)'></gkk-admin-event>
@endsection
