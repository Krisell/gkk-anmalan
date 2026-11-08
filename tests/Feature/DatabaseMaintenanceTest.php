<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

function enableDatabaseMaintenance()
{
    config(['gkk.database-maintenance' => true]);
}

test('pages work as usual when maintenance is off', function () {
    $this->getJson('/slideshow/slides')->assertOk();
});

test('public pages that do not need the database still work', function () {
    enableDatabaseMaintenance();

    $this->get('/')->assertOk()->assertDontSee('Vi flyttar till en ny server');
    $this->get('/gkk')->assertOk()->assertSee('Om GKK')->assertDontSee('Förtroendevalda');
});

test('anything that queries the database shows the maintenance page', function () {
    enableDatabaseMaintenance();

    $this->getJson('/slideshow/slides')
        ->assertStatus(503)
        ->assertHeader('Retry-After', 600)
        ->assertJson(['message' => 'Vi flyttar till en ny server. Försök igen om en stund.']);
});

test('writes, login and member pages are blocked', function () {
    enableDatabaseMaintenance();

    $this->post('/login', ['email' => 'a@example.com', 'password' => 'secret'])
        ->assertStatus(503)
        ->assertSee('Vi flyttar till en ny server');
    $this->get('/login')->assertStatus(503);
    $this->get('/insidan')->assertStatus(503);
    $this->get('/admin/accounts')->assertStatus(503);
});

test('logged-in members see the public pages as guests', function () {
    $user = User::factory()->create();
    $sessionKey = Auth::guard('web')->getName();

    enableDatabaseMaintenance();

    $this->withSession([$sessionKey => $user->id])
        ->get('/')
        ->assertOk()
        ->assertSessionHas($sessionKey, $user->id);
});
