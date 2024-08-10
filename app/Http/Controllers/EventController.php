<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\Competition;
use App\Event;
use App\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::visible()->get();

        foreach ($events as $event) {
            if ($event->publish_count) {
                $event->append('publish_count_value');
            }
        }

        return view('events', [
            'events' => $events,
            'userRegistrations' => auth()->user()->eventRegistrations,
            'view' => 'event',
            'user' => auth()->user(),
        ]);
    }

    public function show(Event $event)
    {
        if ($event->publish_list) {
            $event->append('publish_list_value');
        }

        return view('event', [
            'event' => $event,
            'user' => auth()->user(),
            'registration' => auth()->user()->eventRegistrations()->whereEventId($event->id)->first(),
            'view' => 'event',
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
            'view' => 'event',
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
            ->map(fn ($competition) => $competition->registrations()->whereStatus(1)->pluck('user_id'))
            ->flatten()->unique();

        $remainingUsers = User::query()
            ->whereNotIn('id', $event->registrations->pluck('user_id'))
            ->orderBy('first_name', 'asc')
            ->get();

        return view('admin.event', [
            'event' => $event->load('registrations.user'),
            'competingUsers' => $competingUsers,
            'remainingUsers' => $remainingUsers,
        ]);
    }

    public function destroy(Event $event)
    {
        $event->delete();
    }

    public function add(Event $event, Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $event->registrations()->updateOrCreate([
            'user_id' => request('user_id'),
        ], [
            'status' => true,
            'comment' => 'Tillagd av admin',
            'presence_confirmed' => 1,
        ]);

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'event-registration',
            'user_id' => request('user_id'),
            'data' => json_encode([
                'event_id' => $event->id,
            ]),
        ]);
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
