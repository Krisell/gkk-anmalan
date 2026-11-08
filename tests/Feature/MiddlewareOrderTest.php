<?php

use Illuminate\Support\Facades\DB;

// Who may make a request is decided before the route model binding, so that a guest or a non-admin never gets a
// database lookup, or a 404 that tells whether an id exists, on a route they can't use.

test('a guest is sent to the login before an admin route looks up its model', function () {
    DB::enableQueryLog();

    $this->get('/admin/news/email/999999')->assertRedirect(route('login'));
    $this->get('/admin/news/email/${n.item.id}')->assertRedirect(route('login'));

    expect(collect(DB::getQueryLog())->pluck('query')->filter(fn ($sql) => \str_contains($sql, 'news_items')))->toBeEmpty();
});

test('a non admin is turned away before an admin route looks up its model', function () {
    login();

    $this->get('/admin/news/999999')->assertStatus(401);
});

test('a superadmin route turns away an admin before it looks up its model', function () {
    loginAdmin();

    $this->post('/admin/accounts/promote/999999')->assertStatus(401);
});

test('a signed route checks the signature before it looks up its model', function () {
    $this->get('/minutes/999999/review')->assertForbidden();
});
