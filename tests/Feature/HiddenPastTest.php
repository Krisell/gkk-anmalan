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
        $cA = Competition::factory()->create(['date' => now()->subDays(10)]);
        $cB = Competition::factory()->create(['date' => now()->subDays(2)]);
        $cC = Competition::factory()->create(['date' => now()->addDays(3)]);
        $cD = Competition::factory()->create(['date' => now()->addDays(10)]);

        $user = User::factory()->create();

        auth()->login($user);

        $viewData = $this->get('competitions')->getOriginalContent()->getData()['competitions'];

        $this->assertCount(2, $viewData);
    }
}
