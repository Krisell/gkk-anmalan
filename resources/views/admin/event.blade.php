@extends('layouts.app')

@section('content')
<gkk-admin-event 
    :event='@json($event)' 
    :competing-users='@json($competingUsers)' 
    :remaining-users='@json($remainingUsers)'
></gkk-admin-event>
@endsection
