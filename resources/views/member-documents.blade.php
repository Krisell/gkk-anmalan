@extends('layouts.inside')

@section('inside')
<gkk-documents :folders='@json($folders)'></gkk-documents>
@endsection
