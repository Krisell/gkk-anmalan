@extends('layouts.inside')

@section('inside')
<gkk-agreements
    :user='@json($user)'
    :agreements='@json($agreements)'
></gkk-agreements>
@endsection
