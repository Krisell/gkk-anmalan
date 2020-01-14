<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin', [
            'events' => Event::with('registrations')->get()
        ]);
    }
}
