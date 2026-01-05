<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('eventRegistrations.event');
        $payments = $request->user()->payments()->where('year', '>=', 2025)->with('competition')->get();

        // Find last helper activity
        $lastHelperDate = $user->eventRegistrations()
            ->whereHas('event')
            ->where('presence_confirmed', true)
            ->whereHas('event', function ($query) {
                $query->where('date', '>=', now()->subYear());
            })
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->orderBy('events.date', 'desc')
            ->value('events.date');

        return view('profile', [
            'user' => $user,
            'view' => 'profile',
            'payments' => $payments,
            'lastHelperDate' => $lastHelperDate,
        ]);
    }

    public function updateName()
    {
        auth()->user()->update([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
        ]);
    }

    public function updateEmail()
    {
        auth()->user()->update([
            'email' => request('email'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        auth()->user()->update([
            'password' => bcrypt(request('password')),
        ]);
    }
}
