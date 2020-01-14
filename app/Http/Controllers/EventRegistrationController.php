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
            'status' => '',
            'comment' => '',
        ]);

        return EventRegistration::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => auth()->id()],
            $data,
        );
    }
}
