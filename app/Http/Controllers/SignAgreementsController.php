<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SignAgreementsController extends Controller
{
    public function index(Request $request)
    {
        return view('sign-agreements', [
            'user' => $request->user(),
            'membershipAgreementStatus' => $this->membershipAgreementStatus($request->user()),
            'antiDopingAgreementStatus' => $this->antiDopingAgreementStatus($request->user()),
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

    private function membershipAgreementStatus(User $user)
    {
        $membership = $user->membership_agreement_signed_at;

        if (! $membership) {
            return 'unsigned';
        }

        $membership = Carbon::parse($membership);

        $lastUpdatedMembershipAgreement = config('agreements.membership.last_updated_at');

        if ($lastUpdatedMembershipAgreement && $membership->lt($lastUpdatedMembershipAgreement)) {
            return 'expired';
        }

        if ($membership->lt(now()->subYear())) {
            return 'old';
        }

        return 'signed';
    }

    private function antiDopingAgreementStatus(User $user)
    {
        $antiDoping = $user->anti_doping_agreement_signed_at;

        if (! $antiDoping) {
            return 'unsigned';
        }

        $antiDoping = Carbon::parse($antiDoping);

        $lastUpdatedAntiDopingAgreement = config('agreements.anti_doping.last_updated_at');

        if ($lastUpdatedAntiDopingAgreement && $antiDoping->lt($lastUpdatedAntiDopingAgreement)) {
            return 'expired';
        }

        if ($antiDoping->lt(now()->subYear())) {
            return 'old';
        }

        return 'signed';
    }
}
