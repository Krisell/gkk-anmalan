<?php

namespace App\Http\Controllers;

use App\Firebase;
use App\NewsItem;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return view('admin.news', [
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function edit(NewsItem $news)
    {
        return view('admin.news', [
            'news' => $news,
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'published_at_date' => 'nullable',
        ]);

        if (empty($data['published_at_date'])) {
            $data['published_at_date'] = now()->format('Y-m-d');
        }

        return NewsItem::create($data);
    }

    public function update(Request $request, NewsItem $news)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'published_at_date' => 'nullable',
        ]);

        $news->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at_date' => $data['published_at_date'],
        ]);
    }

    public function destroy(NewsItem $news)
    {
        $news->delete();
    }
}
