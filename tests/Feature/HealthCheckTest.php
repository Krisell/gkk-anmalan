<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

test('the health check answers without touching the database', function () {
    DB::enableQueryLog();

    $this->get('/up')->assertOk()->assertSee('OK');

    expect(DB::getQueryLog())->toBeEmpty();
});

test('the health check passes during database maintenance', function () {
    config(['gkk.database-maintenance' => true]);

    $this->get('/up')->assertOk();
});

test('the health check is left out of the maintenance log', function () {
    config(['gkk.database-maintenance' => true]);

    Log::shouldReceive('build')->never();

    $this->get('/up')->assertOk();
});
