<?php

use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

test('command shows info when no unpaid fees found', function () {
    Payment::all(); // Unsure why this is needed, but probably triggering migrations

    $result = Artisan::call('gkk:remind-of-unpaid-fees');

    expect($result)->toBe(Command::SUCCESS);

    expect(Artisan::output())->toContain('No unpaid fees found.');
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

    $result = Artisan::call('gkk:remind-of-unpaid-fees');
    $output = Artisan::output();

    expect($output)->toContain('Found 1 unpaid fees');
    expect($output)->toContain($user->first_name.' '.$user->last_name);
    expect($output)->toContain('MEMBERSHIP');
    expect($output)->toContain('2025');
    expect($output)->toContain('500');
});

test('command only shows fees with invoice emailed at least 30 days ago', function () {
    $user = User::factory()->create();

    // Create an unpaid fee that was invoiced just 10 days ago
    Payment::factory()->create([
        'user_id' => $user->id,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(10),
    ]);

    $result = Artisan::call('gkk:remind-of-unpaid-fees');
    expect($result)->toBe(Command::SUCCESS);
    expect(Artisan::output())->toContain('No unpaid fees found.');
});

test('command only shows unpaid fees', function () {
    $user = User::factory()->create();

    Payment::factory()->create([
        'user_id' => $user->id,
        'state' => 'PAID',
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $result = Artisan::call('gkk:remind-of-unpaid-fees');
    expect($result)->toBe(Command::SUCCESS);
    expect(Artisan::output())->toContain('No unpaid fees found.');
});
