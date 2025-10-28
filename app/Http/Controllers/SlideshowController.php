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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'data' => 'required|array',
            'is_visible' => 'boolean',
        ]);

        // Increment order of all existing slides to make room at position 0
        Slide::query()->increment('order');

        $slide = Slide::create([
            'type' => $validated['type'],
            'data' => $validated['data'],
            'order' => 0,
            'is_visible' => $validated['is_visible'] ?? true,
        ]);

        return $slide;
    }

    public function updateSlide(Slide $slide, Request $request)
    {
        $validated = $request->validate([
            'type' => 'string',
            'data' => 'array',
            'is_visible' => 'boolean',
        ]);

        $slide->update($validated);

        return $slide;
    }

    public function destroy(Slide $slide)
    {
        $slide->delete();

        return response()->json(['message' => 'Slide deleted successfully']);
    }
}
