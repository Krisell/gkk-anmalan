<?php

namespace App\Http\Controllers;

use App\NewsItem;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return view('admin.news');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'published_at_date' => '',
        ]);

        return NewsItem::create($data);
    }

    public function update(Request $request, NewsItem $news)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $news->update([
            'title' => $data['title'],
            'body' => $data['body'],
        ]);
    }

    public function destroy(NewsItem $news)
    {
        $news->delete();
    }
}
