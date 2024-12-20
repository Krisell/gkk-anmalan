<?php

namespace App\Services;

use App\Models\IntegrationToken;
use Illuminate\Support\Facades\Http;

class Fortnox
{
    private function refresh()
    {
        $baseURL = 'https://apps.fortnox.se/oauth-v1';
        $config = config('services.fortnox');

        $integrationToken = IntegrationToken::whereType('fortnox')->first();

        $response = Http::asForm()->withBasicAuth($config['client_id'], $config['client_secret'])->post($baseURL.'/token', [
            'refresh_token' => $integrationToken->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $integrationToken->update([
            'access_token' => $response->json()['access_token'],
            'refresh_token' => $response->json()['refresh_token'],
            'access_token_expires_at' => now()->addSeconds($response->json()['expires_in']),
        ]);

        return $integrationToken;
    }

    public function token()
    {
        $integrationToken = IntegrationToken::whereType('fortnox')->first();

        if (! $integrationToken) {
            return null;
        }

        if ($integrationToken->access_token_expires_at->subMinutes(5)->isFuture()) {
            return $integrationToken->access_token;
        }

        return $this->refresh()->access_token;
    }
}
