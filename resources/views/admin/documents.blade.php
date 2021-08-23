@extends('layouts.app')

@section('content')
<gkk-admin-documents 
    :folders='@json($folders)'
    :jwt='@json($jwt)'
></gkk-admin-documents>
@endsection
