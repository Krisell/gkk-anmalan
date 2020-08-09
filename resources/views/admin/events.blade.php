@extends('layouts.app')

@section('content')
<gkk-admin-events :events='@json($events)'></gkk-admin-events>
@endsection
