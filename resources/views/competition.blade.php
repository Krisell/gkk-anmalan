@extends('layouts.inside')

@section('inside')
<gkk-competition
    :competition='@json($competition)'
    :user='@json($user)'
    :_registration='@json($registration)'>
</gkk-competition>
@endsection
