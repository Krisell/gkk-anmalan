<?php

use App\Models\User;

test('Only admin can hit the endpoint', function () {
    $user = User::factory()->create();

    $this
        ->actingAs(User::factory()->create())
        ->post("/admin/license/{$user->id}/2025")
        ->assertStatus(401);

    $this
        ->actingAs(User::factory()->admin()->create())
        ->post("/admin/license/{$user->id}/2025")
        ->assertStatus(200);
});

test('A license can be activated for a user', function () {
    $user = User::factory()->create([
        'birth_year' => 2000,
    ]);

    $this
        ->actingAs(User::factory()->admin()->create())
        ->post("/admin/license/{$user->id}/2025")
        ->assertStatus(200);

    $this->assertDatabaseHas('payments', [
        'type' => 'SSFLICENSE',
        'year' => 2025,
        'sek_amount' => 900,
        'sek_discount' => 300,
        'modifier' => null,
        'state' => null,
    ]);
});
