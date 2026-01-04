<?php

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\Payment;
use App\Models\User;

test('Membership payments can be created up front', function () {
    $users = User::factory(3)->create([
        'birth_year' => 1970,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('3 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $users[0]->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'sek_amount' => 1500,
    ]);
});

test('Users with existing payments are skipped', function () {
    $users = User::factory(3)->create([
        'birth_year' => 1970,
    ]);

    Payment::factory()->create([
        'user_id' => $users[0]->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => 'PAID',
    ]);

    Payment::factory()->create([
        'user_id' => $users[1]->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => null,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('1 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseCount(Payment::class, 3);
});

test('Inactivated members are skipped', function () {
    $activeUser = User::factory()->create();
    $inactiveUser = User::factory()->inactivated()->create();

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('1 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, ['user_id' => $activeUser->id]);
    $this->assertDatabaseMissing(Payment::class, ['user_id' => $inactiveUser->id]);
});

test('Honoraray members are skipped for membership fees', function () {
    $activeUser = User::factory()->create();
    $honoraryUser = User::factory()->honorary()->create();

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput("$honoraryUser->email is an honorary member so no payment was created.")
        ->expectsOutput('1 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, ['user_id' => $activeUser->id]);
    $this->assertDatabaseMissing(Payment::class, ['user_id' => $honoraryUser->id]);
});

test('Honorary members are not skipped for SSF license fees', function () {
    $activeUser = User::factory()->create();
    $honoraryUser = User::factory()->honorary()->create();

    $competition = Competition::factory()->create([
        'date' => '2024-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $honoraryUser->id,
        'competition_id' => $competition->id,
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $activeUser->id,
        'competition_id' => $competition->id,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsQuestion('Välj målgrupp', 'ALL')
        ->expectsQuestion('2 license payments will be created. Do you want to continue?', true)
        ->expectsOutput('2 license payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, ['user_id' => $activeUser->id]);
    $this->assertDatabaseHas(Payment::class, ['user_id' => $honoraryUser->id]);
});

test('A normal user does not receive a discount', function () {
    $user = User::factory()->create([
        'birth_year' => now()->subYears(30)->year,
    ]);

    $this->travelTo('2024-01-03');

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('1 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $user->id,
        'sek_amount' => 1500,
        'sek_discount' => 0,
    ]);
});

test('Specific ages receive discount', function () {
    $youngUser = User::factory()->create([
        'birth_year' => now()->subYears(20)->year,
    ]);

    $oldUser = User::factory()->create([
        'birth_year' => now()->subYears(70)->year,
    ]);

    $this->travelTo('2024-01-03');

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('2 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $youngUser->id,
        'sek_amount' => 700,
        'sek_discount' => 0, // Currently the age discount is handled as a separate article. This discount field will be used for half year discounts.
        'modifier' => 'AGE_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $oldUser->id,
        'sek_amount' => 700,
        'sek_discount' => 0,
        'modifier' => 'AGE_DISCOUNT',
    ]);
});

test('A user with student state receives discount', function () {
    $studentUser = User::factory()->student()->create();
    $normalUser = User::factory()->create();

    $this->travelTo('2025-01-03');

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2025')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('2 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $studentUser->id,
        'sek_amount' => 700,
        'sek_discount' => 0, // Currently the age discount is handled as a separate article. This discount field will be used for half year discounts.
        'modifier' => 'STUDENT_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $normalUser->id,
        'sek_amount' => 1500,
        'sek_discount' => 0,
        'modifier' => null,
    ]);
});

test('No license entries are created without competition registrations', function () {
    $user = User::factory()->create();

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsOutput('No users found that need license payments.')
        ->assertExitCode(0);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $user->id]);
});

test('No license entries are created for competition registration the previous year', function () {
    $user = User::factory()->create();

    $competition = Competition::factory()->create([
        'date' => '2023-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsOutput('No users found that need license payments.')
        ->assertExitCode(0);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $user->id]);
});

test('No license entries are created for competition registration the nextc year', function () {
    $user = User::factory()->create();

    $competition = Competition::factory()->create([
        'date' => '2025-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsOutput('No users found that need license payments.')
        ->assertExitCode(0);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $user->id]);
});

test('License entries are created with competition registration the relevant year', function () {
    $user = User::factory()->create();

    $competition = Competition::factory()->create([
        'date' => '2024-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsQuestion('Välj målgrupp', 'ALL')
        ->expectsQuestion('1 license payments will be created. Do you want to continue?', true)
        ->expectsOutput('1 license payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, ['user_id' => $user->id, 'type' => 'SSFLICENSE', 'year' => 2024]);
});

test('License entries are only created if registration status is true', function () {
    $user = User::factory()->create();

    $competition = Competition::factory()->create([
        'date' => '2024-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'status' => false,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsOutput('No users found that need license payments.')
        ->assertExitCode(0);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $user->id]);
});

test('License entries are not created if not confirmed', function () {
    $user = User::factory()->create();

    $competition = Competition::factory()->create([
        'date' => '2024-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsQuestion('Välj målgrupp', 'ALL')
        ->expectsQuestion('1 license payments will be created. Do you want to continue?', false)
        ->assertExitCode(0);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $user->id]);
});

test('The correct fee and discount is set for payment registrations', function () {
    $currentYear = 2026;

    $userA = User::factory()->create(['birth_year' => $currentYear - 17]); // Youth license fee + discount
    $userB = User::factory()->create(['birth_year' => $currentYear - 18]); // Youth license fee + discount
    $userC = User::factory()->create(['birth_year' => $currentYear - 19]); // Discounted license fee
    $userD = User::factory()->create(['birth_year' => $currentYear - 25]); // Discounted license fee
    $userE = User::factory()->create(['birth_year' => $currentYear - 26]); // Full license fee
    $userF = User::factory()->create(['birth_year' => $currentYear - 80]); // Full license fee

    $competition = Competition::factory()->create([
        'date' => '2026-08-04',
    ]);

    foreach ([$userA, $userB, $userC, $userD, $userE, $userF] as $user) {
        CompetitionRegistration::factory()->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
        ]);
    }

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2026')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsQuestion('Välj målgrupp', 'ALL')
        ->expectsQuestion('6 license payments will be created. Do you want to continue?', true)
        ->expectsOutput('6 license payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userA->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 300,
        'sek_discount' => 300,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userB->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 300,
        'sek_discount' => 300,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userC->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 1050,
        'sek_discount' => 300,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userD->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 1050,
        'sek_discount' => 300,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userE->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 1050,
        'sek_discount' => 0,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userF->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 1050,
        'sek_discount' => 0,
    ]);
});

test('Half year discounts are given for membership fee for new members', function () {
    $this->travelTo('2025-06-18');

    $normalUser = User::factory()->create(['birth_year' => now()->subYears(30)->year]);
    $youngUser = User::factory()->create(['birth_year' => now()->subYears(20)->year]);
    $oldUser = User::factory()->create(['birth_year' => now()->subYears(70)->year]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2025')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('3 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $youngUser->id,
        'sek_amount' => 700,
        'sek_discount' => 350, // Half year discount
        'modifier' => 'AGE_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $oldUser->id,
        'sek_amount' => 700,
        'sek_discount' => 350, // Half year discount
        'modifier' => 'AGE_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $normalUser->id,
        'sek_amount' => 1500,
        'sek_discount' => 750, // Half year discount
        'modifier' => null,
    ]);
});

test('Quarter year discounts are given for membership fee for new members', function () {
    $this->travelTo('2025-09-18');

    $normalUser = User::factory()->create(['birth_year' => now()->subYears(30)->year]);
    $youngUser = User::factory()->create(['birth_year' => now()->subYears(20)->year]);
    $oldUser = User::factory()->create(['birth_year' => now()->subYears(70)->year]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2025')
        ->expectsQuestion('Välj typ av avgift', 'MEMBERSHIP')
        ->expectsQuestion('Välj målgrupp', 'MULTIPLE')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('3 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $youngUser->id,
        'sek_amount' => 700,
        'sek_discount' => 525, // Quarter year discount
        'modifier' => 'AGE_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $oldUser->id,
        'sek_amount' => 700,
        'sek_discount' => 525, // Quarter year discount
        'modifier' => 'AGE_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $normalUser->id,
        'sek_amount' => 1500,
        'sek_discount' => 1125, // Quarter year discount
        'modifier' => null,
    ]);
});

test('Single license payment can be created from competition-registered users', function () {
    $userA = User::factory()->create(['email' => 'user.a@example.com']);
    $userB = User::factory()->create(['email' => 'user.b@example.com']);

    $competition = Competition::factory()->create([
        'date' => '2024-01-01',
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $userA->id,
        'competition_id' => $competition->id,
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $userB->id,
        'competition_id' => $competition->id,
    ]);

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsQuestion('Välj målgrupp', 'SINGLE')
        ->expectsQuestion('Välj användare', 'user.a@example.com')
        ->expectsQuestion('1 license payments will be created. Do you want to continue?', true)
        ->expectsOutput('1 license payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, ['user_id' => $userA->id, 'type' => 'SSFLICENSE']);
    $this->assertDatabaseMissing(Payment::class, ['user_id' => $userB->id, 'type' => 'SSFLICENSE']);
});

test('Bug reproduces when selecting second user from relationship-loaded collection', function () {
    // This test reproduces the bug by having multiple users and selecting the second one
    $userFirst = User::factory()->create(['email' => 'first.user@example.com']);
    $userSecond = User::factory()->create(['email' => 'second.user@example.com']);

    $competition = Competition::factory()->create(['date' => '2024-01-01']);

    // Register both users
    CompetitionRegistration::factory()->create([
        'user_id' => $userFirst->id,
        'competition_id' => $competition->id,
    ]);

    CompetitionRegistration::factory()->create([
        'user_id' => $userSecond->id,
        'competition_id' => $competition->id,
    ]);

    // Test the old implementation (relationship loading)
    $yearCompetitions = Competition::whereBetween('date', ['2024-01-01', '2024-12-31'])->get();
    $relationshipLoadedUsers = $yearCompetitions->flatMap(function ($competition) {
        return $competition->registrations->where('status', true)->pluck('user');
    })->unique();

    expect($relationshipLoadedUsers->count())->toBe(2);

    // Try to select the second user (this is where the bug might occur)
    $selectedUsers = $relationshipLoadedUsers->where('email', 'second.user@example.com');

    // Debug: Let's see what's in the collection
    $availableEmails = $relationshipLoadedUsers->pluck('email')->toArray();

    if ($selectedUsers->count() === 0) {
        $this->fail('Bug reproduced! User "second.user@example.com" not found in relationship-loaded collection. Available emails: '.\implode(', ', $availableEmails));
    }

    expect($selectedUsers->count())->toBe(1, 'Should find the second user');

    // Compare with the fixed approach
    $registeredUserIds = $yearCompetitions->flatMap(function ($competition) {
        return $competition->registrations->where('status', true)->pluck('user_id');
    })->unique();
    $freshLoadedUsers = User::whereIn('id', $registeredUserIds)->get();

    $freshSelectedUsers = $freshLoadedUsers->where('email', 'second.user@example.com');
    expect($freshSelectedUsers->count())->toBe(1, 'Fresh-loaded approach should always work');
});
