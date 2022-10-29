<?php

use App\User;
use Illuminate\Support\Carbon;

test('a_user_without_one_unsigned_agreement_are_redirected_to_the_signing_page', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => null,
        'anti_doping_agreement_signed_at' => null,
    ]);

    login($user);

    $this->get('/insidan')->assertRedirect('/sign-agreements');
    $this->get('/member-documents')->assertRedirect('/sign-agreements');
    $this->get('/events')->assertRedirect('/sign-agreements');
    $this->get('/competitions')->assertRedirect('/sign-agreements');

    $user->update(['membership_agreement_signed_at' => now()]);

    $this->get('/insidan')->assertRedirect('/sign-agreements');
    $this->get('/member-documents')->assertRedirect('/sign-agreements');
    $this->get('/events')->assertRedirect('/sign-agreements');
    $this->get('/competitions')->assertRedirect('/sign-agreements');

    $user->update(['anti_doping_agreement_signed_at' => now()]);

    $this->get('/insidan')->assertOk();
    $this->get('/member-documents')->assertOk();
    $this->get('/events')->assertOk();
    $this->get('/competitions')->assertOk();
});

test('signatures_are_invalidated_after_one_year', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => now()->subMonths(12)->subDays(1),
        'anti_doping_agreement_signed_at' => now()->subMonths(12)->subDays(1),
    ]);

    login($user);

    $this->get('/insidan')->assertRedirect('/sign-agreements');
    $this->get('/member-documents')->assertRedirect('/sign-agreements');
    $this->get('/events')->assertRedirect('/sign-agreements');
    $this->get('/competitions')->assertRedirect('/sign-agreements');

    $user->update(['membership_agreement_signed_at' => now()]);

    $this->get('/insidan')->assertRedirect('/sign-agreements');
    $this->get('/member-documents')->assertRedirect('/sign-agreements');
    $this->get('/events')->assertRedirect('/sign-agreements');
    $this->get('/competitions')->assertRedirect('/sign-agreements');

    $user->update(['anti_doping_agreement_signed_at' => now()]);

    $this->get('/insidan')->assertOk();
    $this->get('/member-documents')->assertOk();
    $this->get('/events')->assertOk();
    $this->get('/competitions')->assertOk();
});

test('signatures_are_invalidated_when_the_document_is_updated', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => Carbon::parse('2022-09-30'),
        'anti_doping_agreement_signed_at' => Carbon::parse('2022-09-30'),
    ]);

    login($user);

    $this->get('/insidan')->assertOk();

    // The membership agreement was updated 2022-09-29.
    $user->update([
        'membership_agreement_signed_at' => Carbon::parse('2022-09-28'),
        'anti_doping_agreement_signed_at' => Carbon::parse('2022-09-28'),
    ]);

    $this->get('/insidan')->assertRedirect('/sign-agreements');
    $this->get('/member-documents')->assertRedirect('/sign-agreements');
    $this->get('/events')->assertRedirect('/sign-agreements');
    $this->get('/competitions')->assertRedirect('/sign-agreements');

    $user->update(['membership_agreement_signed_at' => now()]);

    $this->get('/insidan')->assertOk();
    $this->get('/member-documents')->assertOk();
    $this->get('/events')->assertOk();
    $this->get('/competitions')->assertOk();
});

test('a_user_can_sign_the_agreements', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => null,
        'anti_doping_agreement_signed_at' => null,
    ]);

    login($user);

    $this->post('/sign-agreements/membership')->assertOk();
    $this->assertNotNull($user->membership_agreement_signed_at);
    $this->assertNull($user->anti_doping_agreement_signed_at);

    $user->update([
        'membership_agreement_signed_at' => null,
        'anti_doping_agreement_signed_at' => null,
    ]);

    $this->post('/sign-agreements/anti-doping')->assertOk();
    $this->assertNull($user->membership_agreement_signed_at);
    $this->assertNotNull($user->anti_doping_agreement_signed_at);

    $this->post('/sign-agreements/membership')->assertOk();
    $this->assertNotNull($user->membership_agreement_signed_at);
    $this->assertNotNull($user->anti_doping_agreement_signed_at);
});

test('the_agreements_status_is_passed_to_the_view', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => null,
        'anti_doping_agreement_signed_at' => null,
    ]);

    login($user);

    $this->get('/sign-agreements')->assertViewHas([
        'membershipAgreementStatus' => 'unsigned',
        'antiDopingAgreementStatus' => 'unsigned',
    ]);

    $user->update(['membership_agreement_signed_at' => now()]);
    $this->get('/sign-agreements')->assertViewHas([
        'membershipAgreementStatus' => 'signed',
        'antiDopingAgreementStatus' => 'unsigned',
    ]);

    $user->update(['anti_doping_agreement_signed_at' => now()]);
    $this->get('/sign-agreements')->assertViewHas([
        'membershipAgreementStatus' => 'signed',
        'antiDopingAgreementStatus' => 'signed',
    ]);

    $user->update(['membership_agreement_signed_at' => '2022-09-28']); // Update at 2022-09-29
    $this->get('/sign-agreements')->assertViewHas([
        'membershipAgreementStatus' => 'expired',
        'antiDopingAgreementStatus' => 'signed',
    ]);

    $user->update(['anti_doping_agreement_signed_at' => now()->subMonths(12)->subDays(1)]);
    $this->get('/sign-agreements')->assertViewHas([
        'membershipAgreementStatus' => 'expired',
        'antiDopingAgreementStatus' => 'old',
    ]);

    $user->update(['membership_agreement_signed_at' => now()->subMonths(12)->subDays(1)]);
    $this->get('/sign-agreements')->assertViewHas([
        'membershipAgreementStatus' => 'expired', // Expired takes precedence over old.
        'antiDopingAgreementStatus' => 'old',
    ]);
});
