<?php

use App\Models\Competition;
use App\Models\CompetitionRegistration;

test('a competition registration can be created given the required data', function () {
    $competition = Competition::factory()->create();
    login();

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(200);

    $this->assertDatabaseHas('competition_registrations', $data);
});

test('weight class doesnt have to be specified for all competitions', function () {
    $competition = Competition::factory()->create();
    login();

    $data = [
        'weight_class' => '¯\_(ツ)_/¯',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => true,
    ];

    $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(200);

    $this->assertDatabaseHas('competition_registrations', $data);
});

test('a competition registration can be updated given the required data', function () {
    $competition = Competition::factory()->create();
    $user = login();

    $registration = CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
    ]);
    $this->post("/competitions/{$competition->id}/registrations", \array_merge($registration->toArray(), [
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => true, 'bp' => false]),
    ]))->assertStatus(200);

    $this->assertEquals(true, json_decode($registration->fresh()->events, true)['sl']);
});

test('licence number is not required to decline a competition', function () {
    $competition = Competition::factory()->create();
    login();

    $data = [
        'weight_class' => '74',
        'gender' => 'Män',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'status' => false,
    ];

    $this->postJson("/competitions/{$competition->id}/registrations", $data)->assertStatus(200);

    $this->assertDatabaseHas(CompetitionRegistration::class, $data);
});
