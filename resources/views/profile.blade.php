@extends('layouts.inside')

@section('inside')
<gkk-profile 
    :user='@json($user)' 
    :payments='@json($payments)'
></gkk-profile>
@endsection
