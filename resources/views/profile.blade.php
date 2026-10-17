@extends('layouts.inside')

@section('inside')
<gkk-profile
    :user='@json($user)'
    :payments='@json($payments)'
    :last-helper-date='@json($lastHelperDate)'
    :agreement-statuses='@json($agreements)'
></gkk-profile>
@endsection
