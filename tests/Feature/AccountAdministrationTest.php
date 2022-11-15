<?php

use App\Payment;
use App\User;

test('an admin may se a list of all users', function () {
    $user = User::factory()->create();
    $adminUser = loginAdmin();

    $response = $this->get('/admin/accounts');

    $response->assertOk();
    $response->assertSee($user->email);
    $response->assertSee($adminUser->email);
});

test('payments are sent to the view', function () {
    $user = User::factory()->create();
    $adminUser = loginAdmin();

    Payment::factory(2)->for($user)->create();
    Payment::factory()->create(); // Another user's payment

    $response = $this->get('/admin/accounts');

    $response->assertOk();
    $response->assertSee($user->email);
    $response->assertSee($adminUser->email);
    $this->assertCount(2, $response->original->getData()['accounts'][0]->toArray()['payments']);
});

test('a non admin may not se accounts', function () {
    $adminUser = User::factory()->create(['role' => 'admin']);

    $this->get('/admin/accounts')->assertRedirect();

    $user = login();

    $response = $this->get('/admin/accounts');

    $response->assertUnauthorized();
    $response->assertDontSee($user->email);
    $response->assertDontSee($adminUser->email);
});
