<?php

namespace App\Http\Controllers;

class LagSMController extends Controller
{
    public function index()
    {
        return view('lag-sm', ['site' => 'lag-sm']);
    }
}
