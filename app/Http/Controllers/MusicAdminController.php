<?php

namespace App\Http\Controllers;

use App\MusicHelpSet;

class MusicAdminController extends Controller
{
    public function index()
    {
        return view('admin.music', [
            'musicHelpSets' => MusicHelpSet::latest()->get(),
        ]);
    }

    public function store()
    {
        return MusicHelpSet::create([
            'repetitions' => request('repetitions'),
            'weight_lifted' => request('weight_lifted'),
        ]);
    }

    public function destroy(MusicHelpSet $musicHelpSet)
    {
        $musicHelpSet->delete();
    }
}
