@extends('layouts.inside')

@section('inside')
<gkk-admin-signature-requests
    :requests='@json($requests)'
    :users='@json($users)'
    :jwt='@json($jwt)'
></gkk-admin-signature-requests>
@endsection
