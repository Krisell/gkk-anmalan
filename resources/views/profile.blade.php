@extends('layouts.inside')

@section('inside')
<gkk-profile :user='@json($user)'></gkk-profile>
@endsection
