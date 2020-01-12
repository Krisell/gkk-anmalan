<?php

namespace App\Http\Controllers;

use App\OrganizerEvent;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{
    public function index()
    {
        return view('organizer-events', [
            'events' => OrganizerEvent::all(),
        ]);
    }

    public function show(OrganizerEvent $event)
    {
        return view('organizer-event', [
            'event' => $event,
            'user' => auth()->user(),
            'registration' => auth()->user()->organizerRegistrations()->whereOrganizerEventId($event->id)->first(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'time' => '',
            'date' => '',
            'location' => '',
            'description' => '',
        ]);

        return OrganizerEvent::create($data);
    }

    public function admin(OrganizerEvent $event)
    {
        return view('admin-organizer-event', [
            'event' => $event->load('registrations.user'),
        ]);
    }
}
