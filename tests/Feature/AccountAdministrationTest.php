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

test('an admin can mark a user as completed Ren Vinnare education', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['ren_vinnare_education_completed_at' => null]);

    $response = $this->patch("/admin/accounts/{$user->id}/ren-vinnare-education", [
        'ren_vinnare_education_completed' => true,
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Ren Vinnare education status updated successfully']);

    $this->assertNotNull($user->fresh()->ren_vinnare_education_completed_at);
});

test('an admin can clear Ren Vinnare education completion from a user', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['ren_vinnare_education_completed_at' => now()]);

    $response = $this->patch("/admin/accounts/{$user->id}/ren-vinnare-education", [
        'ren_vinnare_education_completed' => false,
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Ren Vinnare education status updated successfully']);

    $this->assertNull($user->fresh()->ren_vinnare_education_completed_at);
});

test('a non-admin cannot update Ren Vinnare education status', function () {
    $user = login(); // Regular user
    $targetUser = User::factory()->create(['ren_vinnare_education_completed_at' => null]);

    $response = $this->patch("/admin/accounts/{$targetUser->id}/ren-vinnare-education", [
        'ren_vinnare_education_completed' => true,
    ]);

    $response->assertUnauthorized();
    $this->assertNull($targetUser->fresh()->ren_vinnare_education_completed_at);
});

test('updating Ren Vinnare education requires valid boolean value', function () {
    $admin = loginAdmin();
    $user = User::factory()->create();

    $response = $this->patchJson("/admin/accounts/{$user->id}/ren-vinnare-education", [
        'ren_vinnare_education_completed' => 'invalid',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['ren_vinnare_education_completed']);
});

test('updating Ren Vinnare education creates activity log entry', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['ren_vinnare_education_completed_at' => null]);

    $initialLogCount = \App\Models\ActivityLog::count();

    $response = $this->patch("/admin/accounts/{$user->id}/ren-vinnare-education", [
        'ren_vinnare_education_completed' => true,
    ]);

    $response->assertOk();

    $this->assertDatabaseCount('activity_logs', $initialLogCount + 1);

    $latestLog = \App\Models\ActivityLog::latest()->first();
    $this->assertEquals($admin->id, $latestLog->performed_by);
    $this->assertEquals('ren-vinnare-education-update', $latestLog->action);
    $this->assertEquals($user->id, $latestLog->user_id);
    $this->assertEquals('completed', $latestLog->data);
});

test('an admin can set background check date for a user', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['background_check_valid_from' => null]);

    $response = $this->patch("/admin/accounts/{$user->id}/background-check", [
        'background_check_valid_from' => '2025-06-15',
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Background check status updated successfully']);

    $this->assertEquals('2025-06-15', $user->fresh()->background_check_valid_from->format('Y-m-d'));
});

test('an admin can clear background check date from a user', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['background_check_valid_from' => '2025-01-01']);

    $response = $this->patch("/admin/accounts/{$user->id}/background-check", [
        'background_check_valid_from' => null,
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Background check status updated successfully']);

    $this->assertNull($user->fresh()->background_check_valid_from);
});

test('a non-admin cannot update background check', function () {
    $user = login();
    $targetUser = User::factory()->create(['background_check_valid_from' => null]);

    $response = $this->patch("/admin/accounts/{$targetUser->id}/background-check", [
        'background_check_valid_from' => '2025-06-15',
    ]);

    $response->assertUnauthorized();
    $this->assertNull($targetUser->fresh()->background_check_valid_from);
});

test('updating background check requires valid date value', function () {
    $admin = loginAdmin();
    $user = User::factory()->create();

    $response = $this->patchJson("/admin/accounts/{$user->id}/background-check", [
        'background_check_valid_from' => 'invalid-date',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['background_check_valid_from']);
});

test('updating background check creates activity log entry', function () {
    $admin = loginAdmin();
    $user = User::factory()->create(['background_check_valid_from' => null]);

    $initialLogCount = \App\Models\ActivityLog::count();

    $response = $this->patch("/admin/accounts/{$user->id}/background-check", [
        'background_check_valid_from' => '2025-06-15',
    ]);

    $response->assertOk();

    $this->assertDatabaseCount('activity_logs', $initialLogCount + 1);

    $latestLog = \App\Models\ActivityLog::latest()->first();
    $this->assertEquals($admin->id, $latestLog->performed_by);
    $this->assertEquals('background-check-update', $latestLog->action);
    $this->assertEquals($user->id, $latestLog->user_id);
    $this->assertEquals('2025-06-15', $latestLog->data);
});
