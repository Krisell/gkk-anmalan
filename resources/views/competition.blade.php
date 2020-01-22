@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-competition
                :competition='@json($competition)'
                :user='@json($user)'
                :_registration='@json($registration)'>
            </gkk-competition>
        </div>
    </div>
</div>
@endsection
