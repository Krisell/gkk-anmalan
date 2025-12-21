<?php

use App\Models\Payment;
use App\Models\User;

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

test('an admin can grant explicit registration approval to a user', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['explicit_registration_approval' => false]);

    $response = $this->patch("/admin/accounts/{$user->id}/competition-permission", [
        'explicit_registration_approval' => true,
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Registration approval updated successfully']);

    $this->assertTrue($user->fresh()->explicit_registration_approval);
});

test('an admin can revoke explicit registration approval from a user', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['explicit_registration_approval' => true]);

    $response = $this->patch("/admin/accounts/{$user->id}/competition-permission", [
        'explicit_registration_approval' => false,
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Registration approval updated successfully']);

    $this->assertFalse($user->fresh()->explicit_registration_approval);
});

test('a non-admin cannot update competition permissions', function () {
    $user = login(); // Regular user
    $targetUser = User::factory()->create(['explicit_registration_approval' => false]);

    $response = $this->patch("/admin/accounts/{$targetUser->id}/competition-permission", [
        'explicit_registration_approval' => true,
    ]);

    $response->assertUnauthorized();
    $this->assertFalse($targetUser->fresh()->explicit_registration_approval);
});

test('updating competition permission requires valid boolean value', function () {
    $admin = loginAdmin();
    $user = User::factory()->create();

    $response = $this->patchJson("/admin/accounts/{$user->id}/competition-permission", [
        'explicit_registration_approval' => 'invalid',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['explicit_registration_approval']);
});
