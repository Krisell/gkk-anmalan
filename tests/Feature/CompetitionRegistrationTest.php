<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Carbon\Carbon;
use Tests\TestCase;
use App\Competition;
use App\CompetitionRegistration;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompetitionRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_competition_registration_can_be_created_given_the_required_data()
    {
        $competition = factory(Competition::class)->create();
        $user = factory(User::class)->create();
        auth()->login($user);

        $data = [
            'licence_number' => 'ab',
            'weight_class' => '74',
            'gender' => 'Män',
            'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ];

        $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(201);

        $this->assertDatabaseHas('competition_registrations', $data);
    }

    /** @test */
    public function a_competition_registration_can_be_updated_given_the_required_data()
    {
        $competition = factory(Competition::class)->create();
        $user = factory(User::class)->create();
        auth()->login($user);

        $registration = factory(CompetitionRegistration::class)->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
        ]);
        $this->post("/competitions/{$competition->id}/registrations", array_merge($registration->toArray(), [
            'licence_number' => 'abc',
        ]))->assertStatus(200);

        $this->assertEquals('abc', $registration->fresh()->licence_number);
    }
}
