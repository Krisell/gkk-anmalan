<?php

namespace App\Http\Controllers;

use App\User;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class AuthController extends Controller
{
    public function google()
    {
        $client = app(\Google_Client::class);
        $client->setCache(new FilesystemCachePool(new Filesystem(new Local(__DIR__.'/'))));
        $payload = $client->verifyIdToken(request('idToken'));

        abort_if($payload['aud'] !== '52915573324-bn4g8a0iuv4r9mbgmi23sd8464g0j0mv.apps.googleusercontent.com', 401);

        return $this->login($payload['email']);
    }

    public function microsoft()
    {
        $token = request('accessToken');

        $data = Http::withHeaders(['Authorization' => "Bearer $token"])->get('https://graph.microsoft.com/v1.0/me/')->json();

        if (! $data) {
            return redirect('/');
        }

        $email = $data['mail'] ?? $data['userPrincipalName'] ?? null;

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
