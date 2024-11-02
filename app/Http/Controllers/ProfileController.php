<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $payments = $request->user()->payments()->where('year', '>=', date('Y') - 1)->get();

        return view('profile', [
            'user' => $request->user(),
            'view' => 'profile',
            'payments' => $payments,
        ]);
    }

    public function updateName()
    {
        auth()->user()->update([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
        ]);
    }

    public function updateEmail()
    {
        auth()->user()->update([
            'email' => request('email'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        auth()->user()->update([
            'password' => bcrypt(request('password')),
        ]);
    }
}
