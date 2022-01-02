<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignAgreementsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_without_signed_agreements_are_redirected_to_the_signing_page()
    {
        $user = User::factory()->create([
            'membership_agreement_signed_at' => null,
            'anti_doping_agreement_signed_at' => null,
        ]);

        auth()->login($user);

        $this->get('/insidan')->assertRedirect('/sign-agreements');
        $this->get('/documents')->assertRedirect('/sign-agreements');
        $this->get('/events')->assertRedirect('/sign-agreements');
        $this->get('/competitions')->assertRedirect('/sign-agreements');

        $user->update(['membership_agreement_signed_at' => now()]);

        $this->get('/insidan')->assertRedirect('/sign-agreements');
        $this->get('/documents')->assertRedirect('/sign-agreements');
        $this->get('/events')->assertRedirect('/sign-agreements');
        $this->get('/competitions')->assertRedirect('/sign-agreements');

        $user->update(['anti_doping_agreement_signed_at' => now()]);

        $this->get('/')->assertOk();
        $this->get('/documents')->assertOk();
        $this->get('/events')->assertOk();
        $this->get('/competitions')->assertOk();
    }

    /** @test */
    public function a_user_can_sign_the_agreements()
    {
        $user = User::factory()->create([
            'membership_agreement_signed_at' => null,
            'anti_doping_agreement_signed_at' => null,
        ]);

        auth()->login($user);

        $this->post('/sign-agreements')->assertOk();

        $this->assertNotNull($user->membership_agreement_signed_at);
        $this->assertNotNull($user->anti_doping_agreement_signed_at);
    }
}
