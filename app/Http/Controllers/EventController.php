<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('events', [
            'events' => Event::all(),
            'userRegistrations' => auth()->user()->eventRegistrations,
        ]);
    }

    public function show(Event $event)
    {
        return view('event', [
            'event' => $event,
            'user' => auth()->user(),
            'registration' => auth()->user()->eventRegistrations()->whereEventId($event->id)->first(),
        ]);
    }

    public function store(Request $request)
    {
        return Event::create($this->validated($request));
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validated($request));
    }

    public function adminIndex()
    {
        return view('admin.events', [
            'events' => Event::with('registrations')->get()
        ]);
    }

    public function admin(Event $event)
    {
        return view('admin.event', [
            'event' => $event->load('registrations.user'),
        ]);
    }

    public function destroy(Event $event)
    {
        $event->delete();
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'time' => '',
            'date' => '',
            'location' => '',
            'description' => '',
        ]);
    }
}
