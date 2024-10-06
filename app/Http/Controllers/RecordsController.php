<?php

namespace App\Http\Controllers;

use App\Models\Result;

class RecordsController extends Controller
{
    public function index()
    {
        return view('records', [
            'results' => Result::with('user:id,first_name,last_name')->get(),
            'site' => 'records',
        ]);
    }
}
