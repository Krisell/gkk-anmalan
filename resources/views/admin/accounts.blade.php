@extends('layouts.app')

@section('content')
<gkk-admin-accounts :user='@json($user)' :accounts='@json($accounts)'></gkk-admin-accounts>
@endsection


