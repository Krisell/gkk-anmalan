@extends('layouts.inside')

@section('inside')
<gkk-records :results='@json($results)'></gkk-records>
@endsection
