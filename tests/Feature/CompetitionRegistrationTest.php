<?php

namespace Tests\Feature;

use App\Competition;
use App\CompetitionRegistration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_competition_registration_can_be_created_given_the_required_data()
    {
        $competition = Competition::factory()->create();
        $user = User::factory()->create();
        auth()->login($user);

        $data = [
            'licence_number' => 'ab',
            'weight_class' => '74',
            'gender' => 'Män',
            'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ];

        $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(201);

        $this->assertDatabaseHas('competition_registrations', $data);
    }

    /** @test */
    public function weight_class_doesnt_have_to_be_specified_for_all_competitions()
    {
        $competition = Competition::factory()->create();
        $user = User::factory()->create();
        auth()->login($user);

        $data = [
            'licence_number' => 'ab',
            'weight_class' => '¯\_(ツ)_/¯',
            'gender' => 'Män',
            'events' => \json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
            'status' => 1,
        ];

        $this->post("/competitions/{$competition->id}/registrations", $data)->assertStatus(201);

        $this->assertDatabaseHas('competition_registrations', $data);
    }

    /** @test */
    public function a_competition_registration_can_be_updated_given_the_required_data()
    {
        $competition = Competition::factory()->create();
        $user = User::factory()->create();
        auth()->login($user);

        $registration = CompetitionRegistration::factory()->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
        ]);
        $this->post("/competitions/{$competition->id}/registrations", \array_merge($registration->toArray(), [
            'licence_number' => 'abc',
        ]))->assertStatus(200);

        $this->assertEquals('abc', $registration->fresh()->licence_number);
    }
}
