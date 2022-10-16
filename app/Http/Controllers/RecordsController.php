<?php

namespace App\Http\Controllers;

use App\Result;

class RecordsController extends Controller
{
    public function index()
    {
        if (config('gkk.no-database-yet')) {
            return redirect(config('gkk.redirect').'/records');
        }

        return view('records', [
            'results' => Result::with('user:id,first_name,last_name')->get(),
            'site' => 'records',
        ]);
    }
}
