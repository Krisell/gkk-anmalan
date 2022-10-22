<?php

namespace App\Http\Controllers;

use App\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::visible()->orderBy('date')->get();

        foreach ($competitions as $competition) {
            if ($competition->publish_count) {
                $competition->append('publish_count_value');
            }
        }

        return view('competitions', [
            'competitions' => $competitions,
            'userRegistrations' => auth()->user()->competitionRegistrations,
            'view' => 'competition',
            'user' => auth()->user(),
        ]);
    }

    public function show(Competition $competition)
    {
        if ($competition->publish_list) {
            $competition->append('publish_list_value');
        }

        return view('competition', [
            'competition' => $competition,
            'user' => auth()->user(),
            'registration' => auth()->user()->competitionRegistrations()->whereCompetitionId($competition->id)->first(),
            'view' => 'competition',
        ]);
    }

    public function store(Request $request)
    {
        return Competition::create($this->validated($request));
    }

    public function update(Request $request, Competition $competition)
    {
        $competition->update($this->validated($request));
    }

    public function adminIndex(Request $request)
    {
        $competitions = Competition::with('registrations');

        if (! $request->has('all')) {
            $competitions->where('date', '>', now()->subDays(7));
        }

        return view('admin.competitions', [
            'competitions' => $competitions->latest()->get(),
            'showingOld' => $request->has('all'),
            'view' => 'competition',
        ]);
    }

    public function admin(Competition $competition)
    {
        return view('admin.competition', [
            'competition' => $competition->load('registrations.user'),
        ]);
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'time' => 'nullable',
            'date' => 'nullable',
            'end_date' => 'nullable',
            'location' => 'nullable',
            'description' => 'nullable',
            'events' => 'json',
            'publish_count' => 'nullable',
            'publish_list' => 'nullable',
            'last_registration_at' => 'nullable',
            'show_status' => 'required',
        ]);
    }
}
