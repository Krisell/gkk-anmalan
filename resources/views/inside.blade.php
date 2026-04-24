@extends('layouts.inside')

@section('inside')
<gkk-navigation
    :user='@json($user)'
    :unanswered='@json($unanswered)'
    :has-pending-payments='@json(session('has_pending_payment'))'
></gkk-navigation>

@auth
    @if ($user->granted_by != 0)
    <gkk-news :user='@json($user)' :news='@json($news)'></gkk-news>
    @endif
@endauth

<gkk-floating-slideshow :user='@json($user)'></gkk-floating-slideshow>
@endsection
