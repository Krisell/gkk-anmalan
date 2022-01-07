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
        return view('landing', ['site' => 'landing']);
    }

    public function powerlifting()
    {
        return view('powerlifting', ['site' => 'powerlifting']);
    }

    public function about()
    {
        return view('about', ['site' => 'about']);
    }

    public function member()
    {
        return view('member', ['site' => 'member']);
    }

    public function documents()
    {
        return view('documents', ['site' => 'documents']);
    }

    public function inside()
    {
        $news = auth()->id() ? NewsItem::orderBy('published_at_date', 'desc')->orderBy('created_at', 'desc')->get() : [];

        $unanswered = [
            'events' => auth()->user() ? Event::visible()->count() - auth()->user()->eventRegistrations()->count() : 0,
            'competitions' => auth()->user() ? Competition::visible()->count() - auth()->user()->competitionRegistrations()->count() : 0,
        ];

        if (auth()->user() && \in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            $unanswered['ungranted'] = User::whereGrantedBy(0)->count();
        }

        return view('inside', [
            'user' => auth()->user(),
            'unanswered' => $unanswered,
            'news' => $news,
            'site' => 'inside'
        ]);
    }

    public function exclamation()
    {
        return redirect('');
    }
}
