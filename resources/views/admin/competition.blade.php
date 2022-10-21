@extends('layouts.inside')

@section('inside')
<gkk-admin-competition :competition='@json($competition)'></gkk-admin-competition>
@endsection
