@extends('layouts.inside')

@section('inside')
<gkk-admin-board
    :members='@json($members)'
    :jwt='@json($jwt)'
></gkk-admin-board>
@endsection
