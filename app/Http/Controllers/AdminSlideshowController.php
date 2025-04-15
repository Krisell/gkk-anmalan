<?php

namespace App\Http\Controllers;

class AdminSlideshowController extends Controller
{
    public function index()
    {
        return view('admin.slideshow', [
            'user' => auth()->user(),
            'view' => 'slideshow',
        ]);
    }
}
