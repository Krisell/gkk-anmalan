<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function google($token)
    {
        $payload = app(\Google_Client::class)->verifyIdToken($token);

        abort_if($payload['aud'] !== '52915573324-bn4g8a0iuv4r9mbgmi23sd8464g0j0mv.apps.googleusercontent.com', 401);

        $user = User::whereEmail($payload['email'])->first();

        if ($user) {
            auth()->login($user);
            return redirect('/');
        }

        dd("NO USER");
    }
}
