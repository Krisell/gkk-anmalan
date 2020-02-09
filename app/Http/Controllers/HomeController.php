<?php

namespace App\Http\Controllers;

use App\Event;
use App\Competition;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'user' => auth()->user(),
            'unanswered' => [
                'events' => auth()->user() ? Event::count() - auth()->user()->eventRegistrations()->count() : 0,
                'competitions' => auth()->user() ? Competition::count() - auth()->user()->competitionRegistrations()->count() : 0,
            ],
        ]);
    }
}
