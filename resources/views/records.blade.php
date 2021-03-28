@extends('layouts.app')

@section('content')
<gkk-records :results='@json($results)'></gkk-records>
@endsection
