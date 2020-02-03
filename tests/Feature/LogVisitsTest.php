<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogVisitsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_visit_is_counted()
    {
        $user = factory(User::class)->create()->fresh();

        $this->assertEquals(0, $user->visits);
        $this->assertNull($user->last_visited_at);

        auth()->login($user);
        $this->get('/');

        $this->assertEquals(1, $user->visits);
        $this->assertNotNull($user->last_visited_at);
    }

    /** @test */
    public function a_non_logged_in_user_can_still_visit_a_page()
    {
        $this->get('/')->assertOk();
    }

    /** @test */
    public function a_visit_is_counted_at_most_once_every_30_minutes()
    {
        $this->withoutExceptionHandling();
        auth()->login($user = factory(User::class)->create());

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
    }
}
