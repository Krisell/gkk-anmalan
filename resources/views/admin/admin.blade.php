@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-admin-events :events='@json($events)'></gkk-admin-events>
        </div>
    </div>
</div>
@endsection
