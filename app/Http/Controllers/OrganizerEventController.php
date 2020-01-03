<?php

namespace App\Http\Controllers;

use App\OrganizerEvent;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        return OrganizerEvent::create($data);
    }
}
