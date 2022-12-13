<?php

namespace App\Http\Controllers;

use App\MusicHelpSet;

class MusicHelpSetController extends Controller
{
    public function store()
    {
        MusicHelpSet::create([
            'weight_lifted' => request('weight_lifted'),
            'repetitions' => request('repetitions'),
        ]);
    }

    public function destroy(MusicHelpSet $musicHelpSet)
    {
        $musicHelpSet->delete();
    }
}
