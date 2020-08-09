@extends('layouts.app')

@section('content')
<gkk-profile :user='@json($user)'></gkk-profile>
@endsection
