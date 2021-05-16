<?php

namespace App\Http\Controllers;

use App\Mail\NewsMail;
use App\NewsItem;
use Illuminate\Http\Request;
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
        $item = NewsItem::make([
            'body' => request('body'),
            'title' => request('title'),
        ]);

        return new NewsMail($item);
    }

    public function test()
    {
        Mail::to(auth()->user())->send(new NewsMail(request('item')));
    }
}
