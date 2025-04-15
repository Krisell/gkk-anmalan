@extends('layouts.inside')

@section('inside')
<gkk-admin-slideshow 
    :user='@json($user)' 
></gkk-admin-slideshow>
@endsection


