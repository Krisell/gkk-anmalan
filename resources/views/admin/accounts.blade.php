@extends('layouts.inside')

@section('inside')
<gkk-admin-accounts 
    :user='@json($user)' 
    :ungranted='@json($ungranted)' 
    :initial-accounts='@json($accounts)'
></gkk-admin-accounts>
@endsection


