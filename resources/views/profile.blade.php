@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-profile :user='@json($user)'></gkk-profile>
        </div>
    </div>
</div>
@endsection
