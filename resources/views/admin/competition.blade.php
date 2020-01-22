@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-admin-competition :competition='@json($competition)'></gkk-admin-competition>
        </div>
    </div>
</div>
@endsection
