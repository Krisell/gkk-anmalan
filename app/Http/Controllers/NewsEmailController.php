<?php

namespace App\Http\Controllers;

use App\Mail\NewsMail;
use App\Models\NewsItem;
use Illuminate\Support\Facades\Mail;

class NewsEmailController extends Controller
{
    public function show(NewsItem $item)
    {
        return view('news-email', [
            'item' => $item,
        ]);
    }

    public function preview()
    {
        $item = new NewsItem([
            'body' => request('body'),
            'title' => request('title'),
        ]);

        return new NewsMail($item);
    }

    public function test()
    {
        $item = new NewsItem([
            'body' => request('body'),
            'title' => request('title'),
        ]);

        Mail::to(auth()->user())->send(new NewsMail($item));
    }

    public function sendToAll()
    {
        $item = new NewsItem([
            'body' => request('body'),
            'title' => request('title'),
        ]);

        $users = \App\Models\User::where('granted_by', '>', 0)->get();

        foreach ($users as $user) {
            Mail::to($user)->send(new NewsMail($item));
        }

        return response()->json([
            'message' => 'Email sent successfully',
            'count' => $users->count(),
        ]);
    }
}
