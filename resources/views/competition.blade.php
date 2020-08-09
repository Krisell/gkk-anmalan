@extends('layouts.app')

@section('content')
<gkk-competition
    :competition='@json($competition)'
    :user='@json($user)'
    :_registration='@json($registration)'>
</gkk-competition>
@endsection
