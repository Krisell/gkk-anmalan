@extends('layouts.inside')

@section('inside')
<gkk-admin-competitions :competitions='@json($competitions)' :showing-old='@json($showingOld)'></gkk-admin-competitions>
@endsection
