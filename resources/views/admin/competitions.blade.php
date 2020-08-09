@extends('layouts.app')

@section('content')
<gkk-admin-competitions :competitions='@json($competitions)'></gkk-admin-competitions>
@endsection
