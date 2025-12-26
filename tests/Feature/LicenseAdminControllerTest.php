<?php

use App\Models\User;

test('Only admin can hit the endpoint', function () {
    $user = User::factory()->create();

    $this
        ->actingAs(User::factory()->create())
        ->post("/admin/licenses/{$user->id}/2026")
        ->assertStatus(401);

    $this
        ->actingAs(User::factory()->admin()->create())
        ->post("/admin/licenses/{$user->id}/2026")
        ->assertStatus(200);
});

test('A license can be activated for a user', function () {
    $user = User::factory()->create([
        'birth_year' => 2003,
    ]);

    $this
        ->actingAs(User::factory()->admin()->create())
        ->post("/admin/licenses/{$user->id}/2026")
        ->assertStatus(200);

    $this->assertDatabaseHas('payments', [
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 1050,
        'sek_discount' => 300,
        'modifier' => null,
        'state' => null,
    ]);
});
