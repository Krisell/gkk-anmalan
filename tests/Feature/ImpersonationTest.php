<?php

use App\Models\User;

test('A normal user cant impersonate', function () {
    $user = User::factory()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($user)->post('/admin/impersonate/'.$userToImpersonate->id)->assertStatus(401);

    $this->assertNull(session('original_impersonating_user'));
    $this->assertNotEquals($userToImpersonate->id, auth()->id());
});

test('A normal admin cant impersonate', function () {
    $admin = User::factory()->admin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($admin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertStatus(401);

    $this->assertNull(session('original_impersonating_user'));
    $this->assertNotEquals($userToImpersonate->id, auth()->id());
});

test('A superadmin can impersonate', function () {
    $superadmin = User::factory()->superadmin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($superadmin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();

    $this->assertEquals($userToImpersonate->id, auth()->id());
    $this->assertEquals($superadmin->email, session('original_impersonating_user'));
});

test('Impersonation does not affect last_visited_at', function () {
    $superadmin = User::factory()->superadmin()->create();
    $userToImpersonate = User::factory()->create();

    $this->actingAs($superadmin)->post('/admin/impersonate/'.$userToImpersonate->id)->assertOk();
    $this->get('/insidan')->assertOk();

    $this->travel(1)->days();

    $this->get('/insidan')->assertOk();

    $this->assertNull($userToImpersonate->fresh()->last_visited_at);
});
