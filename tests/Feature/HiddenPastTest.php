<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Competition;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HiddenPastTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_past_competition_is_by_default_hidden()
    {
        $cA = factory(Competition::class)->create(['date' => now()->subDays(10)]);
        $cB = factory(Competition::class)->create(['date' => now()->subDays(2)]);
        $cC = factory(Competition::class)->create(['date' => now()->addDays(3)]);
        $cD = factory(Competition::class)->create(['date' => now()->addDays(10)]);

        $user = factory(User::class)->create();

        auth()->login($user);

        $viewData = $this->get('competitions')->getOriginalContent()->getData()['competitions'];

        $this->assertCount(2, $viewData);
    }
}
