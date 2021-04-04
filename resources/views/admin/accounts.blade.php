@extends('layouts.app')

@section('content')
<gkk-admin-accounts 
    :user='@json($user)' 
    :ungranted='@json($ungranted)' 
    :accounts='@json($accounts)'
></gkk-admin-accounts>
@endsection


