<?php

namespace App\Http\Controllers;

use App\Models\User;

class ImpersonationController extends Controller
{
    public function store($emailOrId)
    {
        $user = \is_numeric($emailOrId)
            ? User::whereId($emailOrId)->firstOrFail()
            : User::where(['email' => $emailOrId])->firstOrFail();

        session(['original_impersonating_user' => auth()->user()->email]);

        $user->wasLoggedInByAdmin = true;
        auth()->login($user);
    }

    public function delete()
    {
        abort_unless(session()->has('original_impersonating_user'), 403);

        $email = session('original_impersonating_user');
        $user = User::where(['email' => $email])->firstOrFail();

        auth()->login($user);

        session()->forget('original_impersonating_user');
    }
}
