<?php

namespace App\Http\Controllers;

class SlideshowController extends Controller
{
    public function log()
    {
        logger('SlideshowController log method called');
        logger('User Agent: '.request()->header('User-Agent'));
    }
}
