@extends('layouts.inside')

@section('inside')
<gkk-admin-accounts 
    :user='@json($user)' 
    :ungranted='@json($ungranted)' 
    :accounts='@json($accounts)'
></gkk-admin-accounts>
@endsection


