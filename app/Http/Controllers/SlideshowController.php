<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideshowController extends Controller
{
    public function index()
    {
        $slides = Slide::select(['id', 'type', 'data', 'order', 'is_visible'])->orderBy('order')->get();

        return [
            'slides' => $slides,
            'hash' => \hash('sha256', \json_encode($slides)),
        ];
    }

    public function updateOrder()
    {
        $data = request()->validate([
            'slides' => 'required|array',
        ]);

        $slides = $data['slides'];
        $ids = [];

        foreach ($slides as $slide) {
            $ids[] = $slide['id'];
        }

        $slides = Slide::whereIn('id', $ids)->get();

        foreach ($slides as $slide) {
            $slide->order = \array_search($slide->id, \array_column($data['slides'], 'id'));
            $slide->save();
        }
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

    public function update(Slide $slide, Request $request)
    {
        $validated = $request->validate([
            'is_visible' => 'required|boolean',
        ]);

        $slide->update($validated);

        return $slide;
    }
}
