<?php

namespace App\Http\Controllers;

use App\Competition;
use App\Event;
use App\NewsItem;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $news = auth()->id() ? NewsItem::orderBy('published_at_date', 'desc')->orderBy('created_at', 'desc')->get() : [];

        $unanswered = [
            'events' => auth()->user() ? Event::visible()->count() - auth()->user()->eventRegistrations()->count() : 0,
            'competitions' => auth()->user() ? Competition::visible()->count() - auth()->user()->competitionRegistrations()->count() : 0,
        ];

        if (auth()->user() && \in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            $unanswered['ungranted'] = User::whereGrantedBy(0)->count();
        }

        return view('welcome', [
            'user' => auth()->user(),
            'unanswered' => $unanswered,
            'news' => $news,
        ]);
    }
}
