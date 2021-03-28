<?php

namespace App\Http\Controllers;

use App\Event;
use App\NewsItem;
use App\Competition;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $news = Auth()->id() ? NewsItem::orderBy('published_at_date', 'desc')->orderBy('created_at', 'desc')->get() : [];

        return view('welcome', [
            'user' => auth()->user(),
            'unanswered' => [
                'events' => auth()->user() ? Event::visible()->count() - auth()->user()->eventRegistrations()->count() : 0,
                'competitions' => auth()->user() ? Competition::visible()->count() - auth()->user()->competitionRegistrations()->count() : 0,
            ],
            'news' => $news,
        ]);
    }
}
