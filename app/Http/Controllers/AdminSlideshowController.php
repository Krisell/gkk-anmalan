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

    /**
     * Live control of the open screens: step, pause, hold a slide, show a message. The page and the screens talk
     * through Firebase's Realtime Database (slideshow/), where the admin's sign-in may write the controls.
     */
    public function live()
    {
        return view('admin.slideshow-live', [
            'user' => auth()->user(),
            'view' => 'slideshow',
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }
}
