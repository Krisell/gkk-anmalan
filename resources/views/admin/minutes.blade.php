@extends('layouts.inside')

@section('inside')
<gkk-admin-minutes-list :minutes='@json($minutes)'></gkk-admin-minutes-list>
@endsection
