<?php

namespace App\Http\Controllers;

use App\Competition;
use Illuminate\Http\Request;
use App\CompetitionRegistration;

class CompetitionRegistrationController extends Controller
{
    public function store(Competition $competition, Request $request)
    {
        $data = $request->validate([
            'status' => '',
            'comment' => '',
            'licence_number' => 'required',
            'events' => 'json|required',
            'gender' => 'required',
            'weight_class' => 'required',
        ]);

        auth()->user()->update([
            'licence_number' => $data['licence_number'],
            'gender' => $data['gender'],
            'weight_class' => $data['weight_class'],
        ]);

        if ($competition->last_registration_at) {
            abort_if(now()->toDateString() > $competition->last_registration_at->toDateString(), 401);
        }

        return CompetitionRegistration::updateOrCreate(
            ['competition_id' => $competition->id, 'user_id' => auth()->id()],
            $data,
        );
    }

    public function update(Competition $competition, CompetitionRegistration $registration)
    {
        abort_unless(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin', 401);
        
        return $registration->update(request()->only(['status', 'comment']));
    }
}
