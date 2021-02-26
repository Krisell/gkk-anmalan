@extends('layouts.app')

@section('content')
<gkk-admin-documents 
    :documents='@json($documents)'
    :folders='@json($folders)'
    :jwt='@json($jwt)'
></gkk-admin-documents>
@endsection
