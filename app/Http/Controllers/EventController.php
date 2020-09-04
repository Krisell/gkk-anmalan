<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::visible()->get();

        foreach ($events as $event) {
            if ($event->publish_count) {
                $event->publish_count_value = $event->registrations()->whereStatus(1)->count();
            }
        }

        return view('events', [
            'events' => $events,
            'userRegistrations' => auth()->user()->eventRegistrations,
        ]);
    }

    public function show(Event $event)
    {
        if ($event->publish_list) {
            $event->publish_list_value = $event->registrations()->whereStatus(1)->with(['user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])->get();
        }

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
            'end_date' => '',
            'location' => '',
            'description' => '',
            'publish_count' => '',
            'publish_list' => '',
            'last_registration_at' => '',
            'show_status' => 'required',
        ]);
    }
}
