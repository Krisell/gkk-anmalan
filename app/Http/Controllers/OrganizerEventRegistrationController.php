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
            'name' => 'required',
            'status' => '',
            'comment' => '',
        ]);

        $event->registrations()->create($data);
    }
}
