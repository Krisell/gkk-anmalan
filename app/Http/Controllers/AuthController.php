<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function google()
    {
        $keys = Http::get('https://www.googleapis.com/oauth2/v3/certs')->json();
        $payload = (array) JWT::decode(
            request('idToken'),
            JWK::parseKeySet($keys),
        );

        abort_if($payload['aud'] !== '52915573324-bn4g8a0iuv4r9mbgmi23sd8464g0j0mv.apps.googleusercontent.com', 401);

        return $this->login($payload['email']);
    }

    public function microsoft()
    {
        $token = request('accessToken');

        $data = Http::withHeaders(['Authorization' => "Bearer $token"])->get('https://graph.microsoft.com/v1.0/me/')->json();

        if (! $data) {
            return redirect('/insidan');
        }

        $email = $data['mail'] ?? $data['userPrincipalName'] ?? null;

        return $this->login($email);
    }

    private function login($email)
    {
        $user = User::whereEmail($email)->first();

        if ($user) {
            auth()->login($user);

            return redirect(request('to', '/insidan'));
        }

        return redirect("/register?email={$email}");
    }
}
