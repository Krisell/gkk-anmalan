<?php

namespace App\Http\Controllers;

use App\OrganizerEvent;
use Illuminate\Http\Request;
use App\OrganizerEventRegistration;

class OrganizerEventRegistrationController extends Controller
{
    public function store(OrganizerEvent $event, Request $request)
    {
        $data = $request->validate([
            'status' => '',
            'comment' => '',
        ]);

        return OrganizerEventRegistration::updateOrCreate(
            ['organizer_event_id' => $event->id, 'user_id' => auth()->id()],
            $data,
        );
    }
}
