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
                'events' => auth()->user() ? Event::upcoming()->count() - auth()->user()->eventRegistrations()->count() : 0,
                'competitions' => auth()->user() ? Competition::upcoming()->count() - auth()->user()->competitionRegistrations()->count() : 0,
            ],
        ]);
    }
}
