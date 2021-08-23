@extends('layouts.app')

@section('content')
<gkk-documents :folders='@json($folders)'></gkk-documents>
@endsection
