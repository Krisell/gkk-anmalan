<?php

namespace Tests\Feature;

use App\User;
use App\Competition;
use Tests\TestCase;
use App\CompetitionRegistration;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCompetitionRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_update_an_Competition_registration()
    {
        $competition = factory(Competition::class)->create();
        $user = factory(User::class)->create(['role' => 'admin']);

        $registration = factory(CompetitionRegistration::class)->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
            'status' => 0,
            'comment' => 'No comment'
        ]);

        $data = [
            'competition_id' => $competition->id,
            'user_id' => $user->id,
            'status' => 1,
            'status' => 'Another comment',
        ];

        auth()->login($user);
        $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", $data)->assertStatus(200);

        $this->assertDatabaseHas('competition_registrations', $data);
    }

    /** @test */
    public function a_non_admin_cant_update_a_registration()
    {
        $competition = factory(Competition::class)->create();
        $user = factory(User::class)->create();

        $registration = factory(CompetitionRegistration::class)->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
            'status' => 0,
            'comment' => 'No comment'
        ]);

        $data = [
            'competition_id' => $competition->id,
            'user_id' => $user->id,
            'status' => 1,
            'status' => 'Another comment',
        ];

        $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", $data)->assertStatus(302);

        auth()->login($user);

        $this->post("/competitions/{$competition->id}/registrations/{$registration->id}", $data)->assertStatus(401);

        $this->assertDatabaseMissing('competition_registrations', $data);
    }
}
