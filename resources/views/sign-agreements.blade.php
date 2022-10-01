@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div>
      <img class="mx-auto" style="height: 150px;" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FTva%CC%8Afa%CC%88rg-pa%CC%8A-mo%CC%88rk-bakgrund-transparent%20(1).png?alt=media&token=4973abb5-6670-4aec-b036-3d14c30a2584">
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <gkk-agreements 
                :user='@json($user)' 
                :membership-agreement-status='@json($membershipAgreementStatus)'
                :anti-doping-agreement-status='@json($antiDopingAgreementStatus)'
            ></gkk-agreements>
        </div>
    </div>
</div>
@endsection
