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
            'licence_number' => '',
            'events' => 'json',
            'gender' => '',
            'weight_class' => '',
        ]);

        auth()->user()->update([
            'licence_number' => $data['licence_number'],
            'gender' => $data['gender'],
            'weight_class' => $data['weight_class'],
        ]);

        return CompetitionRegistration::updateOrCreate(
            ['competition_id' => $competition->id, 'user_id' => auth()->id()],
            $data,
        );
    }
}
