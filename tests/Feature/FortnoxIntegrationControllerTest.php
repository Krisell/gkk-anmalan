<?php

use App\Models\IntegrationToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

test('Only superadmins can reach the FN api', function () {
    $this->get('/fn')->assertRedirect('login');

    login();
    $this->get('/fn')->assertStatus(401);
    $this->get('/fn/activation')->assertStatus(401);

    loginAdmin();
    $this->get('/fn')->assertStatus(401);
    $this->get('/fn/activation')->assertStatus(401);

    loginSuperadmin();
    $this->get('/fn')->assertRedirect('fn/activation');
    $this->get('/fn/activation')->assertRedirect();
});

test('activation endpoint without code initiates the Fortnox OAuth flow', function () {
    loginSuperadmin();

    Str::createRandomStringsUsing(fn () => 'fake-random-string');

    $redirectUrl = 'https://apps.fortnox.se/oauth-v1/auth?'.\http_build_query([
        'client_id' => config('services.fortnox.client_id'),
        'redirect_uri' => config('app.url').'/fn/activation',
        'scope' => 'invoice customer article print',
        'access_type' => 'offline',
        'response_type' => 'code',
        'state' => 'fake-random-string',
    ]);

    $this->get('/fn/activation')->assertRedirect($redirectUrl);
});

test('activation endpoint with code stores the Fortnox integration token', function () {
    loginSuperadmin();

    $this->freezeTime();

    Http::fake([
        'https://apps.fortnox.se/oauth-v1/token' => Http::response([
            'access_token' => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'expires_in' => 3600,
            'scope' => 'invoice customer article print',
        ]),
        'https://api.fortnox.se/3/invoices' => Http::response([]),
    ]);

    $this->get('/fn/activation?code=test-code');

    $integrationToken = IntegrationToken::where('type', 'fortnox')->first();
    $this->assertEquals('fortnox', $integrationToken->type);
    $this->assertEquals('invoice customer article print', $integrationToken->scope);
    $this->assertEquals('test-access-token', $integrationToken->access_token);
    $this->assertEquals('test-refresh-token', $integrationToken->refresh_token);
    $this->assertEquals(
        now()->addSeconds(3600)->format('Y-m-d H:i:s'),
        $integrationToken->access_token_expires_at->format('Y-m-d H:i:s'),
    );
});

test('index endpoint without existing integration token redirects to activation', function () {
    loginSuperadmin();

    $this->get('/fn')->assertRedirect('fn/activation');
});

test('index endpoint with existing integration token shows the Fortnox index', function () {
    loginSuperadmin();

    IntegrationToken::create([
        'type' => 'fortnox',
        'scope' => 'invoice customer article print',
        'access_token' => 'test-access-token',
        'refresh_token' => 'test-refresh-token',
        'access_token_expires_at' => now()->addSeconds(3600),
    ]);

    Http::fake([
        'https://api.fortnox.se/3/customers?page=1' => Http::response([
            'Customers' => [
                ['some' => 'data'],
                ['more' => 'data'],
            ],
            'MetaInformation' => [
                '@CurrentPage' => 1,
                '@TotalPages' => 2,
            ],
        ]),
        'https://api.fortnox.se/3/customers?page=2' => Http::response([
            'Customers' => [
                ['some' => 'data'],
                ['more' => 'data'],
            ],
            'MetaInformation' => [
                '@CurrentPage' => 2,
                '@TotalPages' => 2,
            ],
        ]),
    ]);

    $this->get('/fn/customers')->assertJson([
        ['some' => 'data'],
        ['more' => 'data'],
    ]);
});

test('disconnect endpoint deletes the Fortnox integration token', function () {
    loginSuperadmin();

    IntegrationToken::create([
        'type' => 'fortnox',
        'scope' => 'invoice customer article print',
        'access_token' => 'test-access-token',
        'refresh_token' => 'test-refresh-token',
        'access_token_expires_at' => now()->addSeconds(3600),
    ]);

    $this->get('/fn/disconnect')->assertRedirect('fn');

    $this->assertNull(IntegrationToken::where('type', 'fortnox')->first());
});
