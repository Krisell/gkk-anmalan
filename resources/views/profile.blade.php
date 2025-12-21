@extends('layouts.inside')

@section('inside')
<gkk-profile
    :user='@json($user)'
    :payments='@json($payments)'
    :last-helper-date='@json($lastHelperDate)'
></gkk-profile>
@endsection
