<?php

namespace App\Http\Controllers;

use App\MusicHelpSet;
use Illuminate\Support\Facades\Cache;

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
        Cache::forget('musikhjalpen-lifted-weight');

        return MusicHelpSet::create([
            'repetitions' => request('repetitions'),
            'weight_lifted' => request('weight_lifted'),
        ]);
    }

    public function destroy(MusicHelpSet $musicHelpSet)
    {
        Cache::forget('musikhjalpen-lifted-weight');

        $musicHelpSet->delete();
    }
}
