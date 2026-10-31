<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

beforeEach(function () {
    $key = \openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    \openssl_pkey_export($key, $privateKey);
    $this->publicKey = \openssl_pkey_get_details($key)['key'];

    config([
        'firebase.private_key' => $privateKey,
        'firebase.service_account_email' => 'test@example.iam.gserviceaccount.com',
    ]);
});

test('an administrator can open the live control with an admin sign-in', function () {
    $admin = loginAdmin();

    $response = $this->get('/admin/slideshow/live')->assertOk()->assertSee('gkk-admin-slideshow-live', false);

    \preg_match("/:jwt='\"([^\"]+)\"'/", $response->getContent(), $match);
    $claims = JWT::decode($match[1], new Key($this->publicKey, 'RS256'));

    // database.rules.json lets this claim write slideshow/control and slideshow/commands.
    expect($claims->claims->type)->toBe('admin')
        ->and($claims->uid)->toBe($admin->id);
});

test('members and guests cannot open the live control', function () {
    $this->get('/admin/slideshow/live')->assertRedirect();

    login();
    $this->get('/admin/slideshow/live')->assertUnauthorized();
});
