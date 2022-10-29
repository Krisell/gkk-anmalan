<?php

use App\User;

test('sign in can be completed with the correct details', function () {
    $user = User::factory()->create();

    $this->assertNull(auth()->user());

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password', // default in factory
    ])->assertRedirect('/insidan');

    $this->assertNotNull(auth()->user());
});

test('sign in cannot be completed with the incorrect details', function () {
    $user = User::factory()->create();

    $this->assertNull(auth()->user());

    $this->get('/login');
    $response = $this->followingRedirects()->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSee($user->email);
    $response->assertSee('Kunde inte logga in med dessa användaruppgifter.');

    $this->assertNull(auth()->user());
});

test('exclamation point still points to landing page', function () {
    $this->get('!')->assertRedirect('');
});
