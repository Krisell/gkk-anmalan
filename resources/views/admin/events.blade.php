@extends('layouts.app')

@section('content')
<gkk-admin-events :events='@json($events)' :showing-old='@json($showingOld)'></gkk-admin-events>
@endsection
