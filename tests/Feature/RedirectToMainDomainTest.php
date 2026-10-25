<?php

test('the other domains redirect permanently to the main domain, keeping the path', function (string $host) {
    $this->get("https://{$host}/medlem?a=1")
        ->assertStatus(301)
        ->assertRedirect('https://goteborgkk.se/medlem?a=1');
})->with([
    'www.goteborgkk.se',
    'gkk-styrkelyft.se',
    'www.gkk-styrkelyft.se',
    'goteborgkraftsportklubb.se',
    'WWW.GoteborgKraftsportklubb.se',
]);

test('the main domain and other hosts are not redirected', function (string $host) {
    $this->get("https://{$host}/gkk")->assertOk();
})->with([
    'goteborgkk.se',
    'gkk-anmalan-production.laravel.cloud',
]);

test('the other domains redirect during maintenance too', function () {
    config(['gkk.database-maintenance' => true]);

    $this->post('https://gkk-styrkelyft.se/login')->assertRedirect('https://goteborgkk.se/login');
});
