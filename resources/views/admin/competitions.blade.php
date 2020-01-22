@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-admin-competitions :competitions='@json($competitions)'></gkk-admin-competitions>
        </div>
    </div>
</div>
@endsection
