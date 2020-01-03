<?php

namespace App\Http\Controllers;

use App\OrganizerEvent;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin', [
            'organizerEvents' => OrganizerEvent::all()
        ]);
    }
}
