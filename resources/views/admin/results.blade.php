@extends('layouts.inside')

@section('inside')
<gkk-admin-results :results='@json($results)' :users='@json($users)'></gkk-admin-results>
@endsection
