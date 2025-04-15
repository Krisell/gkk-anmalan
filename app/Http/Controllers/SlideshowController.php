<?php

namespace App\Http\Controllers;

use App\Models\Slide;

class SlideshowController extends Controller
{
    public function index()
    {
        return Slide::all(['type', 'data', 'order']);
    }

    public function log()
    {
        if (request('id') == null) {
            logger('SlideshowController log method called from agent with missing id.');
        } else {
            logger('SlideshowController log method called from agent with id '.request('id'));
        }

        logger('User Agent: '.request()->header('User-Agent'));
    }
}
