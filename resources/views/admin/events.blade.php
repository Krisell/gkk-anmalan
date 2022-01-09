@extends('layouts.inside')

@section('inside')
<gkk-admin-events :events='@json($events)' :showing-old='@json($showingOld)'></gkk-admin-events>
@endsection
