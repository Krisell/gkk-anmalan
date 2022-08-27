<?php

namespace App\Http\Controllers;

class SignAgreementsController extends Controller
{
    public function index()
    {
        return view('sign-agreements', [
            'user' => auth()->user(),
        ]);
    }

    public function store()
    {
        auth()->user()->update([
            'membership_agreement_signed_at' => now(),
            'anti_doping_agreement_signed_at' => now(),
        ]);
    }
}
