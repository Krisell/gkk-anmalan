<?php

use Illuminate\Support\Facades\DB;

test('the health check answers without touching the database', function () {
    DB::enableQueryLog();

    $this->get('/up')->assertOk()->assertSee('OK');

    expect(DB::getQueryLog())->toBeEmpty();
});

test('the health check passes during database maintenance', function () {
    config(['gkk.database-maintenance' => true]);

    $this->get('/up')->assertOk();
});
