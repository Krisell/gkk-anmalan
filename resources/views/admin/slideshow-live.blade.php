@extends('layouts.inside')

@section('inside')
<gkk-admin-slideshow-live :jwt='@json($jwt)'></gkk-admin-slideshow-live>
@endsection
