@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
          <gkk-admin-accounts :user='@json($user)' :accounts='@json($accounts)'></gkk-admin-accounts>
        </div>
    </div>
</div>
@endsection


