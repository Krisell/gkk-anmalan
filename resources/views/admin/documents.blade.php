@extends('layouts.app')

@section('content')
<gkk-admin-documents 
    :documents='@json($documents)'
    :jwt='@json($jwt)'
></gkk-admin-documents>
@endsection
