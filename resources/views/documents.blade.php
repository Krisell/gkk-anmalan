@extends('layouts.app')

@section('content')
<gkk-documents :documents='@json($documents)' :folders='@json($folders)'></gkk-documents>
@endsection
