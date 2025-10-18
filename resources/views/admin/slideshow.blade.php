@extends('layouts.inside')

@section('inside')
<gkk-admin-slideshow
    :user='@json($user)'
    :jwt='@json($jwt)'
></gkk-admin-slideshow>
@endsection


