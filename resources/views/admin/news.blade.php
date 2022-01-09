@extends('layouts.inside')

@section('head')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/trix@1.3.1/dist/trix.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/trix@1.3.1/dist/trix.js"></script>
@endsection

@section('inside')
<gkk-admin-news 
    :news='@json($news ?? null)' 
    :jwt='@json($jwt)'
></gkk-admin-news>
@endsection


