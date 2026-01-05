@extends('layouts.inside')

@section('inside')
<gkk-admin-payments
    :user='@json($user)'
    :initial-payments='@json($payments)'
></gkk-admin-payments>
@endsection
