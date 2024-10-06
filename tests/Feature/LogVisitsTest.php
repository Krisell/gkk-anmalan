<?php

use App\Models\User;
use Carbon\Carbon;

test('a visit is counted', function () {
    $user = User::factory()->create()->fresh();

    $this->assertEquals(0, $user->visits);
    $this->assertNull($user->last_visited_at);

    login($user);
    $this->get('/');

    $this->assertEquals(1, $user->visits);
    $this->assertNotNull($user->last_visited_at);
});

test('a non logged in user can still visit a page', function () {
    $this->get('/')->assertOk();
});

test('a visit is counted at most once every 30 minutes', function () {
    $user = login();

    $this->get('/');

    $this->assertEquals(1, $user->visits);
    $this->assertNotNull($user->last_visited_at);
    $lastTime = $user->last_visited_at;

    Carbon::setTestNow(now()->addMinutes(20));

    $this->get('/');

    $this->assertEquals(1, $user->visits);
    $this->assertEquals($lastTime, $user->last_visited_at);

    Carbon::setTestNow(now()->addMinutes(20));

    $this->get('/');

    $this->assertEquals(2, $user->visits);
    $this->assertNotEquals($lastTime, $user->last_visited_at);

    Carbon::setTestNow(now()->addMinutes(40));

    $this->get('/');

    $this->assertEquals(3, $user->visits);

    Carbon::setTestNow(now()->addMinutes(20));

    $this->get('/');

    $this->assertEquals(3, $user->visits);
});
