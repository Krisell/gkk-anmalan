<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $key = \openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    \openssl_pkey_export($key, $privateKey);
    $this->publicKey = \openssl_pkey_get_details($key)['key'];

    config([
        'firebase.private_key' => $privateKey,
        'firebase.service_account_email' => 'test@example.iam.gserviceaccount.com',
        'services.live_control.pin' => '4711',
    ]);
});

it('shows the control page', function () {
    $this->get('/live/control')->assertOk()->assertSee('live-control');
});

it('signs in to control the overlay with the PIN', function () {
    $response = $this->postJson('/live/control/token', ['pin' => '4711']);

    $response->assertOk();
    $claims = JWT::decode($response->json('token'), new Key($this->publicKey, 'RS256'));
    expect($claims->uid)->toBe('live-control')
        ->and($claims->claims->liveControl)->toBeTrue()
        ->and($claims->aud)->toContain('identitytoolkit');
});

it('refuses a wrong or missing PIN', function () {
    $this->postJson('/live/control/token', ['pin' => '0000'])->assertForbidden();
    $this->postJson('/live/control/token', ['pin' => ['4711']])->assertForbidden();
    $this->postJson('/live/control/token')->assertForbidden();
});

it('is off when no PIN is configured', function () {
    config(['services.live_control.pin' => null]);

    $this->postJson('/live/control/token', ['pin' => ''])->assertForbidden();
});

it('never puts the PIN in the page', function () {
    $this->get('/live/control')->assertDontSee('4711');
});

it('limits PIN attempts', function () {
    foreach (\range(1, 10) as $attempt) {
        $this->postJson('/live/control/token', ['pin' => '0000'])->assertForbidden();
    }

    $this->postJson('/live/control/token', ['pin' => '4711'])->assertStatus(429);
});

it('counts PIN attempts separately from the overlay polling the feed', function () {
    Http::fake(['*' => Http::response([])]);

    foreach (\range(1, 15) as $poll) {
        $this->getJson('/live/feed/2939');
    }

    $this->postJson('/live/control/token', ['pin' => '4711'])->assertOk();
});
