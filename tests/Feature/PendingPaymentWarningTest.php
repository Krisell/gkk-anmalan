<?php

use App\Models\Payment;
use App\Models\User;

test('no warning is shown for an unpaid fee that has not been invoiced yet', function () {
    $user = User::factory()->create();

    Payment::factory()->for($user)->create([
        'state' => null,
        'created_at' => now()->subDays(60),
        'fortnox_invoice_created_at' => null,
        'fortnox_invoice_emailed_at' => null,
    ]);

    expect($user->hasPendingPayment())->toBeFalse();
});

test('no warning is shown before the invoice is overdue', function () {
    $user = User::factory()->create();

    Payment::factory()->for($user)->create([
        'state' => null,
        'fortnox_invoice_created_at' => now()->subDays(User::OVERDUE_INVOICE_DAYS - 1),
    ]);

    expect($user->hasPendingPayment())->toBeFalse();
});

test('a warning is shown once the invoice is overdue', function () {
    $user = User::factory()->create();

    Payment::factory()->for($user)->create([
        'state' => null,
        'fortnox_invoice_created_at' => now()->subDays(User::OVERDUE_INVOICE_DAYS + 1),
    ]);

    expect($user->hasPendingPayment())->toBeTrue();
});

test('a warning is shown for an overdue invoice from a previous year', function () {
    $user = User::factory()->create();

    Payment::factory()->for($user)->create([
        'state' => null,
        'year' => now()->year - 1,
        'fortnox_invoice_created_at' => now()->subMonths(4),
    ]);

    expect($user->hasPendingPayment())->toBeTrue();
});

test('no warning is shown for a paid invoice', function () {
    $user = User::factory()->create();

    Payment::factory()->for($user)->create([
        'state' => 'PAID',
        'fortnox_invoice_created_at' => now()->subDays(60),
    ]);

    expect($user->hasPendingPayment())->toBeFalse();
});

test('the inside view passes the overdue flag to the navigation', function () {
    $user = User::factory()->create();

    Payment::factory()->for($user)->create([
        'state' => null,
        'fortnox_invoice_created_at' => now()->subDays(60),
    ]);

    $this->actingAs($user)->get('/insidan')->assertViewHas('hasPendingPayment', true);
});
