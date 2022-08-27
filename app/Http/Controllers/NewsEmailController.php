<?php

namespace App\Http\Controllers;

use App\Mail\NewsMail;
use App\NewsItem;
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
}
