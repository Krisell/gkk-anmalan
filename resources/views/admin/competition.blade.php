@extends('layouts.app')

@section('content')
<gkk-admin-competition :competition='@json($competition)'></gkk-admin-competition>
@endsection
