<?php

namespace App\Http\Controllers;

use App\Competition;
use Illuminate\Http\Request;
use App\CompetitionRegistration;

class CompetitionRegistrationController extends Controller
{
    public function store(Competition $competition, Request $request)
    {
        $data = $request->validate([
            'status' => '',
            'comment' => '',
        ]);

        return CompetitionRegistration::updateOrCreate(
            ['competition_id' => $competition->id, 'user_id' => auth()->id()],
            $data,
        );
    }
}
