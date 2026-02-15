<?php

use App\Models\Competition;

test('an admin can create a competition with a pdf_url', function () {
    loginAdmin();

    $data = [
        'name' => 'Test Competition',
        'date' => '2030-01-15',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'show_status' => 'default',
        'pdf_url' => 'https://example.com/invite.pdf',
    ];

    $this->post('/admin/competitions', $data)->assertStatus(201);

    $this->assertDatabaseHas('competitions', [
        'name' => 'Test Competition',
        'pdf_url' => 'https://example.com/invite.pdf',
    ]);
});

test('an admin can create a competition without a pdf_url', function () {
    loginAdmin();

    $data = [
        'name' => 'No PDF Competition',
        'date' => '2030-01-15',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'show_status' => 'default',
    ];

    $this->post('/admin/competitions', $data)->assertStatus(201);

    $this->assertDatabaseHas('competitions', [
        'name' => 'No PDF Competition',
        'pdf_url' => null,
    ]);
});

test('an admin can update a competition to add a pdf_url', function () {
    loginAdmin();

    $competition = Competition::factory()->create([
        'show_status' => 'default',
    ]);

    $this->patch("/admin/competitions/{$competition->id}", [
        'name' => $competition->name,
        'events' => $competition->events,
        'show_status' => 'default',
        'pdf_url' => 'https://example.com/updated.pdf',
    ]);

    expect($competition->fresh()->pdf_url)->toBe('https://example.com/updated.pdf');
});

test('an admin can remove a pdf_url from a competition', function () {
    loginAdmin();

    $competition = Competition::factory()->create([
        'show_status' => 'default',
        'pdf_url' => 'https://example.com/old.pdf',
    ]);

    $this->patch("/admin/competitions/{$competition->id}", [
        'name' => $competition->name,
        'events' => $competition->events,
        'show_status' => 'default',
        'pdf_url' => null,
    ]);

    expect($competition->fresh()->pdf_url)->toBeNull();
});

test('a non-admin cannot create a competition with a pdf_url', function () {
    login();

    $data = [
        'name' => 'Unauthorized Competition',
        'date' => '2030-01-15',
        'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
        'show_status' => 'default',
        'pdf_url' => 'https://example.com/invite.pdf',
    ];

    $this->post('/admin/competitions', $data)->assertStatus(401);

    $this->assertDatabaseMissing('competitions', [
        'name' => 'Unauthorized Competition',
    ]);
});

test('the admin competitions page includes a firebase jwt', function () {
    loginAdmin();

    $this->get('/admin/competitions')->assertViewHas('jwt');
});

test('pdf_url is available when viewing a competition', function () {
    $user = login();

    $competition = Competition::factory()->create([
        'date' => now()->addDays(7),
        'pdf_url' => 'https://example.com/info.pdf',
    ]);

    $response = $this->get("/competitions/{$competition->id}");
    $response->assertOk();

    $response->assertViewHas('competition', function ($viewCompetition) {
        return $viewCompetition->pdf_url === 'https://example.com/info.pdf';
    });
});

test('pdf_url is included in competition list', function () {
    login();

    Competition::factory()->create([
        'date' => now()->addDays(7),
        'pdf_url' => 'https://example.com/info.pdf',
        'show_status' => 'default',
    ]);

    $response = $this->get('/competitions');
    $response->assertOk();

    $response->assertViewHas('competitions', function ($competitions) {
        return $competitions->first()->pdf_url === 'https://example.com/info.pdf';
    });
});
