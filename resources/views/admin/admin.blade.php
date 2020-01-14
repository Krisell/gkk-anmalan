@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-admin-admin :events='@json($events)'></gkk-admin-admin>
        </div>
    </div>
</div>
@endsection
