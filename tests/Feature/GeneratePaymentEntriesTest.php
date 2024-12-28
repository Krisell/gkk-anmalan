<?php

use App\Models\Payment;
use App\Models\User;

test('Membership payments can be created up front', function () {
    $users = User::factory(3)->create([
        'birth_year' => 1970,
    ]);

    $this->artisan('generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'Medlemsavgift')
        ->expectsQuestion('Välj målgrupp', 'Alla')
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

    $this->artisan('generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'Medlemsavgift')
        ->expectsQuestion('Välj målgrupp', 'Alla')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('1 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseCount(Payment::class, 3);
});

test('Inactivated members are skipped', function () {
    $activeUser = User::factory()->create();
    $inactiveUser = User::factory()->inactivated()->create();

    $this->artisan('generate-payment-entries')
        ->expectsQuestion('Välj år för avgift', '2024')
        ->expectsQuestion('Välj typ av avgift', 'Medlemsavgift')
        ->expectsQuestion('Välj målgrupp', 'Alla')
        ->expectsQuestion('Hur många användare vill du hämta?', '100')
        ->expectsOutput('1 membership payments created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas(Payment::class, ['user_id' => $activeUser->id]);
    $this->assertDatabaseMissing(Payment::class, ['user_id' => $inactiveUser->id]);
});
