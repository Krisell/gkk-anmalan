@extends('layouts.inside')

@section('inside')
<gkk-admin-activity-logs
    :user='@json($user)'
    :initial-logs='@json($logs)'
></gkk-admin-activity-logs>
@endsection
