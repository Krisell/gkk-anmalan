<?php

namespace App\Http\Controllers;

use App\Firebase;

class AdminSlideshowController extends Controller
{
    /**
     * The slideshow's page: live control of the open screens (step, pause, hold a slide, messages, a web page), and the
     * slides themselves. The page and the screens talk through Firebase's Realtime Database (slideshow/), where the
     * admin's sign-in may write the controls; the same sign-in uploads the slides' images.
     */
    public function index()
    {
        return view('admin.slideshow', [
            'user' => auth()->user(),
            'view' => 'slideshow',
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }
}
