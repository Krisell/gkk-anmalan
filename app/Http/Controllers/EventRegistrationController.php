<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventRegistration;
use Illuminate\Http\Request;

class EventRegistrationController extends Controller
{
    public function store(Event $event, Request $request)
    {
        $data = $request->validate([
            'status' => 'required',
            'comment' => '',
        ]);

        if ($event->last_registration_at) {
            abort_if(now()->toDateString() > $event->last_registration_at->toDateString(), 401);
        }

        return EventRegistration::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => auth()->id()],
            $data,
        );
    }
}
