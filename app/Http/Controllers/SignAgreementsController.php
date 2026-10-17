<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureAgreementsAreSignedMiddleware;
use Illuminate\Http\Request;

class SignAgreementsController extends Controller
{
    public function index(Request $request)
    {
        return view('sign-agreements', [
            'user' => $request->user(),
            'agreements' => $request->user()->agreementStatuses(),
        ]);
    }

    public function store($agreement)
    {
        if ($agreement === 'membership') {
            return auth()->user()->update([
                'membership_agreement_signed_at' => now(),
            ]);
        }

        if ($agreement === 'anti-doping') {
            return auth()->user()->update([
                'anti_doping_agreement_signed_at' => now(),
            ]);
        }
    }

    /**
     * Let the user continue without signing agreements that are about to expire.
     */
    public function postpone(Request $request)
    {
        abort_if($request->user()->mustSignAgreements(), 403);

        $request->session()->put(EnsureAgreementsAreSignedMiddleware::POSTPONED_SESSION_KEY, true);

        return ['redirect' => $request->session()->pull('url.intended', '/insidan')];
    }
}
