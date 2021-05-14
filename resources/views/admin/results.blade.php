@extends('layouts.app')

@section('content')
<gkk-admin-results :results='@json($results)' :users='@json($users)'></gkk-admin-results>
@endsection
