<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\SSFLicense;
use Illuminate\Http\Request;

class LicenseAdminController extends Controller
{
    public function store(Request $request, User $user, $year)
    {
        SSFLicense::createFor($user, $year);
    }
}
