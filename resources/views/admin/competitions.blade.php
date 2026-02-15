@extends('layouts.inside')

@section('inside')
<gkk-admin-competitions :competitions='@json($competitions)' :showing-old='@json($showingOld)' :jwt="'{{ $jwt }}'"></gkk-admin-competitions>
@endsection
