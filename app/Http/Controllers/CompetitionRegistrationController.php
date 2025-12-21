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

        // Check if user wants to compete and validate helper status
        if ($data['status'] && ! auth()->user()->explicit_registration_approval) {
            $helperCount = auth()->user()->eventRegistrations()
                ->whereHas('event', function ($query) {
                    $query->where('date', '>=', now()->subYear());
                })
                ->where('presence_confirmed', true)
                ->count();

            if ($helperCount === 0) {
                return response()->json(['error' => 'Du kan inte anmäla dig till tävlingar eftersom du inte har hjälpt till som funktionär det senaste året.'], 403);
            }
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

        return $registration->update(request()->only(['status', 'comment', 'events']));
    }
}
