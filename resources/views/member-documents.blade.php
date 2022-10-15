@extends('layouts.inside')

@section('inside')
<gkk-documents 
    :folders='@json($folders)'
    :user='@json($user)'>
</gkk-documents>
@endsection
