<?php

use App\Models\Competition;

it('can show all competitions for signed in users', function () {
    $this->get('/competitions')->assertRedirect('/login');

    login();

    $competition = Competition::factory()->create([
        'date' => now()->addDays(7),
        'publish_count' => true,
    ]);

    $this->get('/competitions')->assertSee($competition->name);
});
