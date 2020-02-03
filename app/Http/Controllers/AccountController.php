<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.accounts', [
            'accounts' => User::all(),
            'user' => auth()->user(),
        ]);
    }

    public function promote(User $user)
    {
        $user->update(['role' => 'admin']);
    }
}
