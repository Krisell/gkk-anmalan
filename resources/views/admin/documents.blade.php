@extends('layouts.inside')

@section('inside')
<gkk-admin-documents 
    :folders='@json($folders)'
    :jwt='@json($jwt)'
></gkk-admin-documents>
@endsection
