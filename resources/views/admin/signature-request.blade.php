@extends('layouts.inside')

@section('inside')
<gkk-admin-signature-request
    :initial-request='@json($request)'
    :sign-urls='@json($signUrls)'
    :users='@json($users)'
    :folders='@json($folders)'
    :jwt='@json($jwt)'
></gkk-admin-signature-request>
@endsection
