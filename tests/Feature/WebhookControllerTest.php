<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

test('Webhook can be called with a signed URL and existing action', function () {
    Log::shouldReceive('info')->once()->with('Webhook log');

    $url = URL::signedRoute('webhook', ['action' => 'log']);

    $this->get($url)->assertOk();
});

test('Webhook cannot be called with a signed URL and non-existing action', function () {
    $url = URL::signedRoute('webhook', ['action' => 'non-existing']);

    $this->get($url)->assertForbidden();
});

test('Webhook cannot be called with a non-signed URL', function () {
    $this->get(route('webhook', ['action' => 'log']))->assertForbidden();
});
