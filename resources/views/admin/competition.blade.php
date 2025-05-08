@extends('layouts.inside')

@section('inside')
<gkk-admin-competition 
    :competition='@json($competition)'
    :remaining-users='@json($remainingUsers)'
></gkk-admin-competition>
@endsection
