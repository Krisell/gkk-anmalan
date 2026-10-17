<?php

use App\Models\User;
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

// test('signatures_are_invalidated_when_the_document_is_updated', function () {
//     $user = User::factory()->create([
//         'membership_agreement_signed_at' => Carbon::parse('2022-09-30'),
//         'anti_doping_agreement_signed_at' => Carbon::parse('2022-09-30'),
//     ]);

//     login($user);

//     $this->get('/insidan')->assertOk();

//     // The membership agreement was updated 2022-09-29.
//     $user->update([
//         'membership_agreement_signed_at' => Carbon::parse('2022-09-28'),
//         'anti_doping_agreement_signed_at' => Carbon::parse('2022-09-28'),
//     ]);

//     $this->get('/insidan')->assertRedirect('/sign-agreements');
//     $this->get('/member-documents')->assertRedirect('/sign-agreements');
//     $this->get('/events')->assertRedirect('/sign-agreements');
//     $this->get('/competitions')->assertRedirect('/sign-agreements');

//     $user->update(['membership_agreement_signed_at' => now()]);

//     $this->get('/insidan')->assertOk();
//     $this->get('/member-documents')->assertOk();
//     $this->get('/events')->assertOk();
//     $this->get('/competitions')->assertOk();
// });

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

    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'unsigned' && $agreements['anti_doping']['status'] === 'unsigned');

    $user->update(['membership_agreement_signed_at' => now()]);
    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'signed' && $agreements['anti_doping']['status'] === 'unsigned');

    $user->update(['anti_doping_agreement_signed_at' => now()]);
    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'signed' && $agreements['anti_doping']['status'] === 'signed');

    $user->update(['membership_agreement_signed_at' => '2022-09-28']); // Updated at 2022-09-29
    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'expired' && $agreements['anti_doping']['status'] === 'signed');

    $user->update(['anti_doping_agreement_signed_at' => now()->subMonths(12)->subDays(1)]);
    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'expired' && $agreements['anti_doping']['status'] === 'old');

    $user->update(['membership_agreement_signed_at' => now()->subMonths(12)->subDays(1)]);
    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'old' && $agreements['anti_doping']['status'] === 'old');
});

test('agreements_expiring_within_a_month_show_a_reminder_once_per_session', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => now()->subYear()->addWeeks(2),
        'anti_doping_agreement_signed_at' => now(),
    ]);

    login($user);

    $this->get('/sign-agreements')->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'expiring');

    $this->get('/events')->assertRedirect('/sign-agreements');
    $this->getJson('/api/ranking/points')->assertOk();

    $this->postJson('/sign-agreements/postpone')->assertOk()->assertJson(['redirect' => url('/events')]);

    $this->get('/events')->assertOk();
    $this->get('/insidan')->assertOk();
    $this->get('/profile')->assertOk()->assertViewHas('agreements', fn ($agreements) => $agreements['membership']['status'] === 'expiring');
});

test('agreements_signed_more_than_a_month_before_expiry_show_no_reminder', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => now()->subYear()->addMonth()->addDay(),
        'anti_doping_agreement_signed_at' => now()->subYear()->addMonth()->addDay(),
    ]);

    login($user);

    $this->get('/events')->assertOk();
});

test('postponing_is_not_allowed_when_agreements_have_expired', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => now()->subYear()->subDay(),
        'anti_doping_agreement_signed_at' => now(),
    ]);

    login($user);

    $this->postJson('/sign-agreements/postpone')->assertForbidden();
    $this->get('/events')->assertRedirect('/sign-agreements');
});

test('a_postponed_reminder_still_blocks_once_the_agreement_expires', function () {
    $user = User::factory()->create([
        'membership_agreement_signed_at' => now()->subYear()->addWeek(),
        'anti_doping_agreement_signed_at' => now(),
    ]);

    login($user);

    $this->postJson('/sign-agreements/postpone')->assertOk();
    $this->get('/events')->assertOk();

    $this->travel(8)->days();

    $this->get('/events')->assertRedirect('/sign-agreements');
});
