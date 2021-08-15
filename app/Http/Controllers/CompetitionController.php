<?php

namespace App\Http\Controllers;

use App\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::visible()->get();

        foreach ($competitions as $competition) {
            if ($competition->publish_count) {
                $competition->publish_count_value = $competition->registrations()->whereStatus(1)->count();
            }
        }

        return view('competitions', [
            'competitions' => $competitions,
            'userRegistrations' => auth()->user()->competitionRegistrations,
        ]);
    }

    public function show(Competition $competition)
    {
        if ($competition->publish_list) {
            $competition->publish_list_value = $competition->registrations()->whereStatus(1)->with(['user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])->get();
        }

        return view('competition', [
            'competition' => $competition,
            'user' => auth()->user(),
            'registration' => auth()->user()->competitionRegistrations()->whereCompetitionId($competition->id)->first(),
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
            'time' => '',
            'date' => '',
            'end_date' => '',
            'location' => '',
            'description' => '',
            'events' => 'json',
            'publish_count' => '',
            'publish_list' => '',
            'last_registration_at' => '',
            'show_status' => 'required',
        ]);
    }
}
