<?php

namespace App\Http\Controllers;

use App\Competition;
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

    public function adminIndex(Request $request)
    {
        $events = Event::with('registrations');

        if (! $request->has('all')) {
            $events->where('date', '>', now()->subDays(7));
        }

        return view('admin.events', [
            'events' => $events->latest()->get(),
            'showingOld' => $request->has('all'),
        ]);
    }

    public function admin(Event $event)
    {
        // Figure out competing users same day, to mark in admin UI
        // This only works for one or two day competitions currently.
        $competitions = collect([
            ...Competition::where('date', $event->date)->get(),
            ...Competition::where('end_date', $event->date)->get(),
        ]);

        $competingUsers = $competitions
            ->map(fn ($competition) => $competition->registrations()->pluck('user_id'))
            ->flatten()->unique();

        return view('admin.event', [
            'event' => $event->load('registrations.user'),
            'competingUsers' => $competingUsers,
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
            'time' => 'nullable',
            'date' => 'nullable',
            'end_date' => 'nullable',
            'location' => 'nullable',
            'description' => 'nullable',
            'publish_count' => 'nullable',
            'publish_list' => 'nullable',
            'last_registration_at' => 'nullable',
            'show_status' => 'required',
        ]);
    }
}
