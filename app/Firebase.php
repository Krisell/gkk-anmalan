<?php

namespace App;

use Firebase\JWT\JWT;

class Firebase {
    public static function makeAdminJwt()
    {
        return JWT::encode([
            'iss' => config('firebase.service_account_email'),
            'sub' => config('firebase.service_account_email'),
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => time(),
            'exp' => time() + (60*60),
            'uid' => auth()->id(),
            'claims' => [
                'type' => 'admin',
            ]
        ], config('firebase.private_key'), 'RS256');
    }
}