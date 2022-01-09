@extends('layouts.inside')

@section('head')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/trix@1.3.1/dist/trix.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/trix@1.3.1/dist/trix.js"></script>
@endsection

@section('inside')
<gkk-admin-news-email :item='@json($item)'></gkk-admin-news-email>
@endsection
