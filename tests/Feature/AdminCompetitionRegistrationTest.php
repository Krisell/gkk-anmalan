<?php

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\User;

test('an admin can update an Competition registration', function () {
    $competition = Competition::factory()->create();
    $user = User::factory()->create(['role' => 'admin']);

    $registration = CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'status' => false,
        'comment' => 'No comment',
    ]);

    $data = [
        'competition_id' => $competition->id,
        'user_id' => $user->id,
        'status' => true,
        'status' => 'Another comment',
    ];

    login($user);
    $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", $data)->assertStatus(200);

    $this->assertDatabaseHas('competition_registrations', $data);
});

test('a non admin cant update a registration', function () {
    $competition = Competition::factory()->create();
    $user = User::factory()->create();

    $registration = CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'status' => false,
        'comment' => 'No comment',
    ]);

    $data = [
        'competition_id' => $competition->id,
        'user_id' => $user->id,
        'status' => true,
        'status' => 'Another comment',
    ];

    $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", $data)->assertStatus(302);

    login($user);

    $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", $data)->assertStatus(401);

    $this->assertDatabaseMissing('competition_registrations', $data);
});

test('an admin can change the weight class of a registration', function () {
    $competition = Competition::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $registration = CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'weight_class' => '105',
    ]);

    login($admin);
    $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", ['weight_class' => '120'])->assertStatus(200);

    expect($registration->fresh()->weight_class)->toBe('120');
});
