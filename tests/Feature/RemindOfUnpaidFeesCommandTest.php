<?php

use App\Models\Payment;
use App\Models\User;

test('command shows info when no unpaid fees found', function () {
    Payment::all(); // Unsure why this is needed, but probably triggering migrations

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->assertSuccessful()
        ->expectsOutput('No unpaid fees found for all payment types.');
});

test('command shows info about unpaid fees', function () {
    $user = User::factory()->create();

    // Create an unpaid fee older han 30 days
    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 500,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->assertSuccessful()
        ->expectsOutput('Found 1 unpaid fees.')
        ->expectsOutputToContain($user->first_name.' '.$user->last_name);
});

test('command only shows fees with invoice emailed at least 30 days ago', function () {
    $user = User::factory()->create();

    // Create an unpaid fee that was invoiced just 10 days ago
    Payment::factory()->create([
        'user_id' => $user->id,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(10),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->assertSuccessful()
        ->expectsOutput('No unpaid fees found for all payment types.');
});

test('command only shows unpaid fees', function () {
    $user = User::factory()->create();

    Payment::factory()->create([
        'user_id' => $user->id,
        'state' => 'PAID',
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->assertSuccessful()
        ->expectsOutput('No unpaid fees found for all payment types.');
});

test('command filters by MEMBERSHIP payment type', function () {
    $user1 = User::factory()->create(['first_name' => 'Member', 'last_name' => 'User']);
    $user2 = User::factory()->create(['first_name' => 'License', 'last_name' => 'User']);

    // Create both MEMBERSHIP and SSFLICENSE unpaid fees
    Payment::factory()->create([
        'user_id' => $user1->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    Payment::factory()->create([
        'user_id' => $user2->id,
        'type' => 'SSFLICENSE',
        'sek_amount' => 300,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'MEMBERSHIP')
        ->assertSuccessful()
        ->expectsOutput('Found 1 unpaid membership fees.')
        ->expectsOutputToContain('Member User')
        ->doesntExpectOutputToContain('License User');
});

test('command filters by SSFLICENSE payment type', function () {
    $user1 = User::factory()->create(['first_name' => 'Member', 'last_name' => 'User']);
    $user2 = User::factory()->create(['first_name' => 'License', 'last_name' => 'User']);

    // Create both MEMBERSHIP and SSFLICENSE unpaid fees
    Payment::factory()->create([
        'user_id' => $user1->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    Payment::factory()->create([
        'user_id' => $user2->id,
        'type' => 'SSFLICENSE',
        'sek_amount' => 300,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'SSFLICENSE')
        ->assertSuccessful()
        ->expectsOutput('Found 1 unpaid ssflicense fees.')
        ->expectsOutputToContain('License User')
        ->doesntExpectOutputToContain('Member User');
});

test('command shows all payment types when ALL is selected', function () {
    $user1 = User::factory()->create(['first_name' => 'Member', 'last_name' => 'User']);
    $user2 = User::factory()->create(['first_name' => 'License', 'last_name' => 'User']);

    // Create both MEMBERSHIP and SSFLICENSE unpaid fees
    Payment::factory()->create([
        'user_id' => $user1->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    Payment::factory()->create([
        'user_id' => $user2->id,
        'type' => 'SSFLICENSE',
        'sek_amount' => 300,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->assertSuccessful()
        ->expectsOutput('Found 2 unpaid fees.')
        ->expectsOutputToContain('Member User');
});
