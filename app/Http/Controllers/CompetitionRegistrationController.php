<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\Event;
use Illuminate\Http\Request;

class CompetitionRegistrationController extends Controller
{
    public function store(Competition $competition, Request $request)
    {
        $data = $request->validate([
            'status' => 'nullable',
            'comment' => 'nullable',
            'events' => 'json|required',
            'gender' => 'required',
            'weight_class' => 'required',
        ]);

        auth()->user()->update([
            'gender' => $data['gender'],
            'weight_class' => $data['weight_class'],
        ]);

        if ($competition->last_registration_at) {
            abort_if(now()->toDateString() > $competition->last_registration_at->toDateString(), 401);
        }

        $registration = CompetitionRegistration::updateOrCreate(
            ['competition_id' => $competition->id, 'user_id' => auth()->id()],
            $data,
        );

        $simultaneousEvent = Event::where('date', $competition->date)->first();

        return [
            'registration' => $registration,
            'simultaneousEvent' => $simultaneousEvent,
        ];
    }

    public function update(Competition $competition, CompetitionRegistration $registration)
    {
        abort_unless(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin', 401);

        return $registration->update(request()->only(['status', 'comment']));
    }
}
