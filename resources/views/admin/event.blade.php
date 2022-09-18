@extends('layouts.app')

@section('content')
<gkk-admin-event :event='@json($event)' :competing-users='@json($competingUsers)'></gkk-admin-event>
@endsection
