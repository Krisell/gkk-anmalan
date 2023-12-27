<?php

use App\ActivityLog;
use App\User;

function data()
{
    return [
        'name' => 'Martin',
        'time' => '9–15',
        'date' => '2020-02-03 00:00:00',
        'location' => 'Friskis majorna',
        'description' => 'Alla behövs!',
        'show_status' => 'default',
    ];
}

test('an admin can create an event', function () {
    loginAdmin();

    $this->post('/admin/events', data())->assertStatus(201);
    $this->assertDatabaseHas('events', data());
});

test('a superadmin can create an event', function () {
    loginSuperadmin();

    $this->post('/admin/events', data())->assertStatus(201);
    $this->assertDatabaseHas('events', data());
});

test('a non admin cannot create an event', function () {
    $this->post('/admin/events', data())->assertStatus(302);

    login();

    $this->post('/admin/events', data())->assertStatus(401);
    $this->assertDatabaseMissing('events', data());
});

test('a non admin cant promote users', function () {
    $user = User::factory()->create();
    $nonAdminUser = User::factory()->create(['role' => 'user']);
    login($nonAdminUser);

    $this->assertNull($user->role);

    $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(401);
    $this->assertNull($user->fresh()->role);
});

test('a normal admin cant promote other users', function () {
    $user = User::factory()->create();
    loginAdmin();

    $this->assertNull($user->role);

    $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(401);
    $this->assertNull($user->fresh()->role);
});

test('a superadmin can promote other users', function () {
    $user = User::factory()->create();
    loginSuperadmin();

    $this->assertNull($user->role);

    $this->post("/admin/accounts/promote/{$user->id}")->assertStatus(200);
    $this->assertEquals('admin', $user->fresh()->role);

    $this->assertDatabaseHas(ActivityLog::class, [
        'performed_by' => auth()->id(),
        'action' => 'account-promotion',
        'user_id' => $user->id,
    ]);
});

test('a non admin cant demote users', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $nonAdminUser = User::factory()->create(['role' => 'user']);
    login($nonAdminUser);

    $this->assertEquals('admin', $user->role);

    $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(401);
    $this->assertEquals('admin', $user->fresh()->role);
});

test('a normal admin cant demote other users', function () {
    $user = User::factory()->create(['role' => 'admin']);
    loginAdmin();

    $this->assertEquals('admin', $user->role);

    $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(401);
    $this->assertEquals('admin', $user->fresh()->role);
});

test('a superadmin can demote other users', function () {
    $user = User::factory()->create(['role' => 'admin']);
    loginSuperadmin();

    $this->assertEquals('admin', $user->role);

    $this->post("/admin/accounts/demote/{$user->id}")->assertStatus(200);
    $this->assertNull($user->fresh()->role);

    $this->assertDatabaseHas(ActivityLog::class, [
        'performed_by' => auth()->id(),
        'action' => 'account-demotion',
        'user_id' => $user->id,
    ]);
});

test('only an admin can load the documents admin page with the firebase jwt', function () {
    login();

    $this->get('/admin/documents')->assertUnauthorized();

    loginAdmin();

    $this->get('/admin/documents')->assertViewHas('jwt');
});
