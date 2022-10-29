<?php

use App\Competition;

test('a past competition is by default hidden', function () {
    Competition::factory()->create(['date' => now()->subDays(10)]);
    Competition::factory()->create(['date' => now()->subDays(2)]);
    Competition::factory()->create(['date' => now()->addDays(3)]);
    Competition::factory()->create(['date' => now()->addDays(10)]);

    login();

    $this->assertCount(2, $this->get('competitions')->getOriginalContent()->getData()['competitions']);
});
