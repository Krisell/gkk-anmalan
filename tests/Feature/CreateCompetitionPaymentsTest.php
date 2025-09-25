<?php

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\Payment;
use App\Models\User;

test('command fails when no competitions exist', function () {
    $this->artisan('payments:create-competition')
        ->expectsOutput('No competitions found.')
        ->assertExitCode(0);
});

test('command fails when no registrations exist for selected competition', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsOutput('Inga registrerade deltagare hittades för Test Competition.')
        ->assertExitCode(0);
});

test('command creates payments for competition registrations', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $seniorUser = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Senior',
        'birth_year' => 1990,
    ]);

    $juniorUser = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Junior',
        'birth_year' => 2010,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $seniorUser->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $juniorUser->id,
        'status' => 1,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $seniorUser->id => 'John Senior (1990)',
        $juniorUser->id => 'Jane Junior (2010)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', 'all', $userOptions)
        ->expectsOutput('Tävling: Test Competition')
        ->expectsOutput('Senior avgift: 500 SEK')
        ->expectsOutput('Junior avgift: 300 SEK')
        ->expectsOutput('Antal nya betalningar: 2')
        ->expectsConfirmation('Skapa 2 nya betalningsuppgifter?', 'yes')
        ->expectsOutput('Skapade betalning för John Senior (senior, 500 SEK)')
        ->expectsOutput('Skapade betalning för Jane Junior (junior, 300 SEK)')
        ->expectsOutput('Skapade: 2 betalningar')
        ->expectsOutput('Hoppade över: 0 betalningar')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(2);

    $seniorPayment = Payment::where('user_id', $seniorUser->id)->first();
    expect($seniorPayment->type)->toBe('COMPETITION');
    expect($seniorPayment->year)->toBe(now()->year);
    expect($seniorPayment->sek_amount)->toBe(500);
    expect($seniorPayment->sek_discount)->toBe(0);

    $juniorPayment = Payment::where('user_id', $juniorUser->id)->first();
    expect($juniorPayment->type)->toBe('COMPETITION');
    expect($juniorPayment->year)->toBe(now()->year);
    expect($juniorPayment->sek_amount)->toBe(300);
    expect($juniorPayment->sek_discount)->toBe(0);
});

test('command skips users with existing payments', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_year' => 1990,
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'birth_year' => 1995,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user1->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user2->id,
        'status' => 1,
    ]);

    Payment::factory()->create([
        'user_id' => $user1->id,
        'competition_id' => $competition->id,
        'type' => 'COMPETITION',
        'year' => now()->year,
        'sek_amount' => 500,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $user1->id => 'John Doe (1990)',
        $user2->id => 'Jane Smith (1995)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', 'all', $userOptions)
        ->expectsOutput('Antal nya betalningar: 1')
        ->expectsConfirmation('Skapa 1 nya betalningsuppgifter?', 'yes')
        ->expectsOutput('Skapade betalning för Jane Smith (senior, 500 SEK)')
        ->expectsOutput('Skapade: 1 betalningar')
        ->expectsOutput('Hoppade över: 1 betalningar')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(2);
});

test('command handles all users already having payments', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_year' => 1990,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user->id,
        'status' => 1,
    ]);

    Payment::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'type' => 'COMPETITION',
        'year' => now()->year,
        'sek_amount' => 500,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $user->id => 'John Doe (1990)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', 'all', $userOptions)
        ->expectsOutput('Alla deltagare har redan betalningsuppgifter för Test Competition.')
        ->assertExitCode(0);
});

test('command exits when user cancels confirmation', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_year' => 1990,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user->id,
        'status' => 1,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $user->id => 'John Doe (1990)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', 'all', $userOptions)
        ->expectsConfirmation('Skapa 1 nya betalningsuppgifter?', 'no')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(0);
});

test('command correctly determines junior vs senior based on age', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $currentYear = now()->year;
    $juniorCutoffYear = $currentYear - 23;

    $borderlineJunior = User::factory()->create([
        'first_name' => 'Borderline',
        'last_name' => 'Junior',
        'birth_year' => $juniorCutoffYear + 1,
    ]);

    $borderlineSenior = User::factory()->create([
        'first_name' => 'Borderline',
        'last_name' => 'Senior',
        'birth_year' => $juniorCutoffYear,
    ]);

    $userWithoutBirthYear = User::factory()->create([
        'first_name' => 'No',
        'last_name' => 'BirthYear',
        'birth_year' => null,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $borderlineJunior->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $borderlineSenior->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $userWithoutBirthYear->id,
        'status' => 1,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $borderlineJunior->id => 'Borderline Junior ('.($juniorCutoffYear + 1).')',
        $borderlineSenior->id => 'Borderline Senior ('.$juniorCutoffYear.')',
        $userWithoutBirthYear->id => 'No BirthYear',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', 'all', $userOptions)
        ->expectsConfirmation('Skapa 3 nya betalningsuppgifter?', 'yes')
        ->expectsOutput('Skapade betalning för Borderline Junior (junior, 300 SEK)')
        ->expectsOutput('Skapade betalning för Borderline Senior (senior, 500 SEK)')
        ->expectsOutput('Skapade betalning för No BirthYear (senior, 500 SEK)')
        ->assertExitCode(0);

    $juniorPayment = Payment::where('user_id', $borderlineJunior->id)->first();
    expect($juniorPayment->sek_amount)->toBe(300);

    $seniorPayment = Payment::where('user_id', $borderlineSenior->id)->first();
    expect($seniorPayment->sek_amount)->toBe(500);

    $noBirthYearPayment = Payment::where('user_id', $userWithoutBirthYear->id)->first();
    expect($noBirthYearPayment->sek_amount)->toBe(500);
});

test('command can process only a single selected user', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_year' => 1990,
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'birth_year' => 2005,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user1->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user2->id,
        'status' => 1,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $user1->id => 'John Doe (1990)',
        $user2->id => 'Jane Smith (2005)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', $user1->id, $userOptions)
        ->expectsOutput('Tävling: Test Competition')
        ->expectsOutput('Användare: John Doe')
        ->expectsOutput('Senior avgift: 500 SEK')
        ->expectsOutput('Junior avgift: 300 SEK')
        ->expectsOutput('Antal nya betalningar: 1')
        ->expectsConfirmation('Skapa 1 nya betalningsuppgifter?', 'yes')
        ->expectsOutput('Skapade betalning för John Doe (senior, 500 SEK)')
        ->expectsOutput('Skapade: 1 betalningar')
        ->expectsOutput('Hoppade över: 0 betalningar')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(1);

    $payment = Payment::where('user_id', $user1->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->sek_amount)->toBe(500);

    $user2Payment = Payment::where('user_id', $user2->id)->first();
    expect($user2Payment)->toBeNull();
});

test('command creates payment for single user with junior pricing', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $juniorUser = User::factory()->create([
        'first_name' => 'Junior',
        'last_name' => 'Player',
        'birth_year' => 2010,
    ]);

    $seniorUser = User::factory()->create([
        'first_name' => 'Senior',
        'last_name' => 'Player',
        'birth_year' => 1990,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $juniorUser->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $seniorUser->id,
        'status' => 1,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $juniorUser->id => 'Junior Player (2010)',
        $seniorUser->id => 'Senior Player (1990)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', $juniorUser->id, $userOptions)
        ->expectsOutput('Tävling: Test Competition')
        ->expectsOutput('Användare: Junior Player')
        ->expectsOutput('Senior avgift: 500 SEK')
        ->expectsOutput('Junior avgift: 300 SEK')
        ->expectsOutput('Antal nya betalningar: 1')
        ->expectsConfirmation('Skapa 1 nya betalningsuppgifter?', 'yes')
        ->expectsOutput('Skapade betalning för Junior Player (junior, 300 SEK)')
        ->expectsOutput('Skapade: 1 betalningar')
        ->expectsOutput('Hoppade över: 0 betalningar')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(1);

    $payment = Payment::where('user_id', $juniorUser->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->sek_amount)->toBe(300);

    $seniorPayment = Payment::where('user_id', $seniorUser->id)->first();
    expect($seniorPayment)->toBeNull();
});

test('command shows no new payments when selected user already has payment', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_year' => 1990,
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'birth_year' => 1995,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user1->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user2->id,
        'status' => 1,
    ]);

    Payment::factory()->create([
        'user_id' => $user1->id,
        'competition_id' => $competition->id,
        'type' => 'COMPETITION',
        'year' => now()->year,
        'sek_amount' => 500,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $user1->id => 'John Doe (1990)',
        $user2->id => 'Jane Smith (1995)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', $user1->id, $userOptions)
        ->expectsOutput('Alla deltagare har redan betalningsuppgifter för Test Competition.')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(1);
});

test('command processes all users when "all users" is selected', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_year' => 1990,
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'birth_year' => 2005,
    ]);

    $user3 = User::factory()->create([
        'first_name' => 'Bob',
        'last_name' => 'Johnson',
        'birth_year' => 1985,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user1->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user2->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $user3->id,
        'status' => 1,
    ]);

    $userOptions = [
        'all' => 'Alla användare',
        $user1->id => 'John Doe (1990)',
        $user2->id => 'Jane Smith (2005)',
        $user3->id => 'Bob Johnson (1985)',
    ];

    $this->artisan('payments:create-competition')
        ->expectsChoice('Välj tävling', $competition->id, [$competition->id => "Test Competition ({$competition->date->format('Y-m-d')})"])
        ->expectsQuestion('Avgift för seniorer (SEK)', '500')
        ->expectsQuestion('Avgift för juniorer (SEK)', '300')
        ->expectsChoice('Välj användare', 'all', $userOptions)
        ->expectsOutput('Tävling: Test Competition')
        ->expectsOutput('Senior avgift: 500 SEK')
        ->expectsOutput('Junior avgift: 300 SEK')
        ->expectsOutput('Antal nya betalningar: 3')
        ->expectsConfirmation('Skapa 3 nya betalningsuppgifter?', 'yes')
        ->expectsOutput('Skapade betalning för John Doe (senior, 500 SEK)')
        ->expectsOutput('Skapade betalning för Jane Smith (junior, 300 SEK)')
        ->expectsOutput('Skapade betalning för Bob Johnson (senior, 500 SEK)')
        ->expectsOutput('Skapade: 3 betalningar')
        ->expectsOutput('Hoppade över: 0 betalningar')
        ->assertExitCode(0);

    expect(Payment::count())->toBe(3);

    $user1Payment = Payment::where('user_id', $user1->id)->first();
    expect($user1Payment->sek_amount)->toBe(500);

    $user2Payment = Payment::where('user_id', $user2->id)->first();
    expect($user2Payment->sek_amount)->toBe(300);

    $user3Payment = Payment::where('user_id', $user3->id)->first();
    expect($user3Payment->sek_amount)->toBe(500);
});
