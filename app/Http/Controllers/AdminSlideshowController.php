<?php

namespace App\Http\Controllers;

use App\Firebase;

class AdminSlideshowController extends Controller
{
    public function index()
    {
        return view('admin.slideshow', [
            'user' => auth()->user(),
            'view' => 'slideshow',
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }
}
