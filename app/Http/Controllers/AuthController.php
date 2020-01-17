<?php

namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function google()
    {
        $payload = app(\Google_Client::class)->verifyIdToken(request('idToken'));

        abort_if($payload['aud'] !== '52915573324-bn4g8a0iuv4r9mbgmi23sd8464g0j0mv.apps.googleusercontent.com', 401);

        return $this->login($payload['email']);
    }

    public function microsoft()
    {
        $token = request('accessToken');
        $json = app(Client::class)->request('GET', 'https://graph.microsoft.com/v1.0/me/', [
            'headers' => ['Authorization' => "Bearer $token"],
        ])->getBody()->getContents();

        $data = json_decode($json);

        if (! $data) {
            return redirect('/');
        }

        $email = $data->mail ?? $data->userPrincipalName;

        return $this->login($email);
    }

    private function login($email)
    {
        $user = User::whereEmail($email)->first();

        if ($user) {
            auth()->login($user);
            return redirect(request('to', '/'));
        }

        return redirect("/register?email={$email}");
    }
}
