@extends('layouts.app')

@section('content')
<gkk-admin-competitions :competitions='@json($competitions)' :showing-old='@json($showingOld)'></gkk-admin-competitions>
@endsection
