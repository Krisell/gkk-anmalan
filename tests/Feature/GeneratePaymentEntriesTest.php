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
        ->expectsQuestion('0 license payments will be created. Do you want to continue?', true)
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
        ->expectsQuestion('0 license payments will be created. Do you want to continue?', true)
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
        ->expectsQuestion('0 license payments will be created. Do you want to continue?', true)
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
        ->expectsQuestion('0 license payments will be created. Do you want to continue?', true)
        ->expectsOutput('0 license payments created successfully.')
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
        ->expectsQuestion('1 license payments will be created. Do you want to continue?', false)
        ->assertExitCode(0);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $user->id]);
});

test('The correct fee is set for payment registrations', function () {
    $currentYear = 2024;

    $userA = User::factory()->create(['birth_year' => $currentYear - 17]); // Discounted license fee
    $userB = User::factory()->create(['birth_year' => $currentYear - 18]); // Discounted license fee
    $userC = User::factory()->create(['birth_year' => $currentYear - 19]); // Full license fee
    $userD = User::factory()->create(['birth_year' => $currentYear - 80]); // Full license fee

    $competition = Competition::factory()->create([
        'date' => '2024-08-04',
    ]);

    foreach ([$userA, $userB, $userC, $userD] as $user) {
        CompetitionRegistration::factory()->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
        ]);
    }

    $this->artisan('gkk:generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'SSFLICENSE')
        ->expectsQuestion('4 license payments will be created. Do you want to continue?', true)
        ->expectsOutput('4 license payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userA->id,
        'type' => 'SSFLICENSE',
        'year' => 2024,
        'sek_amount' => 200,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userB->id,
        'type' => 'SSFLICENSE',
        'year' => 2024,
        'sek_amount' => 200,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userC->id,
        'type' => 'SSFLICENSE',
        'year' => 2024,
        'sek_amount' => 900,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $userD->id,
        'type' => 'SSFLICENSE',
        'year' => 2024,
        'sek_amount' => 900,
    ]);
});
