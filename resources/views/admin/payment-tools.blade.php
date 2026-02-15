@extends('layouts.inside')

@section('inside')
<gkk-admin-payment-tools :competitions='@json($competitions)'></gkk-admin-payment-tools>
@endsection
