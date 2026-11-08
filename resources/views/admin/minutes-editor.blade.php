@extends('layouts.inside')

@section('inside')
<gkk-admin-minutes-editor
    :minutes='@json($minutes)'
    :next-numbers='@json((object) $nextNumbers)'
    :members='@json($members)'
    :workflow='@json($workflow)'
    :folders='@json($folders)'
    :copied-from='@json($copiedFrom)'
    :jwt='@json($jwt)'
></gkk-admin-minutes-editor>
@endsection
