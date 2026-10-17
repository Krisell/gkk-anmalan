<?php

use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\User;

test('A normal user cant impersonate', function () {
    $user = User::factory()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($user)->post('/admin/impersonate/'.$userToImpersonate->id)->assertStatus(401);

    $this->assertNull(session('original_impersonating_user'));
    $this->assertNotEquals($userToImpersonate->id, auth()->id());
});

test('An admin can impersonate', function () {
    $admin = User::factory()->admin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();

    $this->assertEquals($userToImpersonate->id, auth()->id());
    $this->assertEquals($admin->id, session('original_impersonating_user'));
});

test('A superadmin can impersonate', function () {
    $superadmin = User::factory()->superadmin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($superadmin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();

    $this->assertEquals($userToImpersonate->id, auth()->id());
    $this->assertEquals($superadmin->id, session('original_impersonating_user'));
});

test('A user can be impersonated by email', function () {
    $admin = User::factory()->admin()->create();
    $userToImpersonate = User::factory()->create(['email' => 'member@example.com']);

    $this->actingAs($admin)->post('/admin/impersonate/Member@Example.com')->assertOk();

    $this->assertEquals($userToImpersonate->id, auth()->id());
});

test('An admin cant impersonate a superadmin', function () {
    $admin = User::factory()->admin()->create();
    $superadmin = User::factory()->superadmin()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$superadmin->id)->assertForbidden();

    $this->assertEquals($admin->id, auth()->id());
    $this->assertNull(session('original_impersonating_user'));
});

test('An admin cant impersonate themselves', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$admin->id)->assertStatus(422);

    $this->assertNull(session('original_impersonating_user'));
});

test('Impersonating while already impersonating is not allowed', function () {
    $admin = User::factory()->admin()->create();
    $otherAdmin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$otherAdmin->id)->assertOk();
    $this->post('/admin/impersonate/'.$member->id)->assertForbidden();

    $this->assertEquals($otherAdmin->id, auth()->id());
    $this->assertEquals($admin->id, session('original_impersonating_user'));
});

test('Impersonation is logged in the activity log', function () {
    $admin = User::factory()->admin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();

    $this->assertDatabaseHas(ActivityLog::class, [
        'performed_by' => $admin->id,
        'action' => 'impersonation',
        'user_id' => $userToImpersonate->id,
    ]);
});

test('Stopping impersonation logs the admin back in', function () {
    $admin = User::factory()->admin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();
    $this->delete('/impersonate')->assertOk();

    $this->assertEquals($admin->id, auth()->id());
    $this->assertNull(session('original_impersonating_user'));
});

test('Stopping impersonation without impersonating is forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->delete('/impersonate')->assertForbidden();

    $this->assertEquals($user->id, auth()->id());
});

test('Session data does not carry over when switching users', function () {
    $admin = User::factory()->admin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($admin)->withSession(['some_cached_flag' => 'admin-value']);

    $this->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();
    $this->assertNull(session('some_cached_flag'));

    session(['some_cached_flag' => 'member-value']);

    $this->delete('/impersonate')->assertOk();
    $this->assertNull(session('some_cached_flag'));
});

test('Pending payments are shown for the impersonated user, not the admin', function () {
    $admin = User::factory()->admin()->withExplicitApproval()->create();
    $member = User::factory()->withExplicitApproval()->create();

    Payment::factory()->create([
        'user_id' => $member->id,
        'state' => null,
        'year' => now()->year,
        'created_at' => now()->subWeek(),
    ]);

    $this->actingAs($admin)->get('/insidan')->assertViewHas('hasPendingPayment', false);

    $this->post('/admin/impersonate/'.$member->id)->assertOk();
    $this->get('/insidan')->assertViewHas('hasPendingPayment', true);

    $this->delete('/impersonate')->assertOk();
    $this->get('/insidan')->assertViewHas('hasPendingPayment', false);
});

test('Impersonation does not affect last_visited_at', function () {
    $superadmin = User::factory()->superadmin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($superadmin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();

    // Simulate fresh requests, where the user is re-read from the session
    auth()->forgetGuards();
    $this->get('/insidan')->assertOk();

    $this->travel(1)->days();

    auth()->forgetGuards();
    $this->get('/insidan')->assertOk();

    $this->assertNull($userToImpersonate->fresh()->last_visited_at);
});
