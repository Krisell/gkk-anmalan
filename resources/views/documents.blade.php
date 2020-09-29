@extends('layouts.app')

@section('content')
<gkk-documents :documents='@json($documents)'></gkk-documents>
@endsection
