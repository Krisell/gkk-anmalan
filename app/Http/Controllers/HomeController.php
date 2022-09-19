<?php

namespace App\Http\Controllers;

use App\Event;
use App\NewsItem;
use App\User;

class HomeController extends Controller
{
    public function index()
    {
        $news = auth()->id() ? NewsItem::orderBy('published_at_date', 'desc')->orderBy('created_at', 'desc')->get() : [];

        $unanswered = [
            'events' => auth()->user() ? Event::visible()->count() - auth()->user()->eventRegistrations()->count() : 0,
            'competitions' => 0, // Intentionally skip, as not all competitions are relevant for all users
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

    public function exclamation()
    {
        return redirect('');
    }
}
