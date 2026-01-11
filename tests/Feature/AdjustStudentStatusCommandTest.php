<?php

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Mock Fortnox token
    $this->mock(\App\Services\Fortnox::class, function ($mock) {
        $mock->shouldReceive('token')->andReturn('fake-token');
    });

    Http::fake([
        'https://api.fortnox.se/3/invoices/*/credit' => Http::response(['status' => 'OK'], 200),
    ]);
});

test('command requires year parameter', function () {
    $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
    $this->expectExceptionMessage('Not enough arguments (missing: "year")');

    $this->artisan('gkk:adjust-student-status');
});

test('command shows error when fortnox integration not activated', function () {
    $this->mock(\App\Services\Fortnox::class, function ($mock) {
        $mock->shouldReceive('token')->andReturn(null);
    });

    $this->artisan('gkk:adjust-student-status', ['year' => 2024])
        ->expectsOutput('Fortnox integration not activated.')
        ->assertSuccessful();
});

// Test the core payment filtering logic independently
test('payment query finds only MEMBERSHIP payments for specified year', function () {
    $user = User::factory()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'is_student_over_23' => false,
    ]);

    // Create the correct payment
    $correctPayment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => 'PAID',
        'fortnox_invoice_document_number' => 'INV-CORRECT',
    ]);

    // Create payments that should be ignored
    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'SSFLICENSE',
        'year' => 2024,
        'state' => 'PAID',
        'fortnox_invoice_document_number' => 'INV-WRONG-TYPE',
    ]);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2023,
        'state' => 'PAID',
        'fortnox_invoice_document_number' => 'INV-WRONG-YEAR',
    ]);

    // Test the query that the command uses
    $foundPayment = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($foundPayment->id)->toBe($correctPayment->id);
    expect($foundPayment->fortnox_invoice_document_number)->toBe('INV-CORRECT');
});

test('payment query excludes payments without fortnox_invoice_document_number', function () {
    $user = User::factory()->create();

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => 'PAID',
        'fortnox_invoice_document_number' => null,
    ]);

    $foundPayment = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($foundPayment)->toBeNull();
});

test('payment query finds payments regardless of state', function () {
    $user = User::factory()->create();

    $unpaidPayment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => null, // Unpaid
        'fortnox_invoice_document_number' => 'INV-UNPAID',
    ]);

    $foundPayment = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($foundPayment->id)->toBe($unpaidPayment->id);
    expect($foundPayment->state)->toBeNull();
});

test('payment query returns first matching payment when multiple exist', function () {
    $user = User::factory()->create();

    $firstPayment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => 'PAID',
        'fortnox_invoice_document_number' => 'INV-FIRST',
        'created_at' => now()->subDays(2),
    ]);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => null,
        'fortnox_invoice_document_number' => 'INV-SECOND',
        'created_at' => now()->subDays(1),
    ]);

    $foundPayment = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    // Should return the first one found (by database ordering)
    expect($foundPayment->fortnox_invoice_document_number)->toBe('INV-FIRST');
});

test('payment query filters correctly by year boundaries', function () {
    $user = User::factory()->create();

    // Create payments for different years
    $payment2023 = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2023,
        'fortnox_invoice_document_number' => 'INV-2023',
    ]);

    $payment2024 = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'fortnox_invoice_document_number' => 'INV-2024',
    ]);

    $payment2025 = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'fortnox_invoice_document_number' => 'INV-2025',
    ]);

    // Test filtering for 2024
    $found2024 = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($found2024->fortnox_invoice_document_number)->toBe('INV-2024');

    // Test filtering for 2023
    $found2023 = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2023)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($found2023->fortnox_invoice_document_number)->toBe('INV-2023');
});

test('payment query distinguishes between MEMBERSHIP and other payment types', function () {
    $user = User::factory()->create();

    $membershipPayment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'fortnox_invoice_document_number' => 'INV-MEMBERSHIP',
    ]);

    $licensePayment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'SSFLICENSE',
        'year' => 2024,
        'fortnox_invoice_document_number' => 'INV-LICENSE',
    ]);

    // Test MEMBERSHIP filter
    $foundMembership = $user->payments()
        ->where('type', 'MEMBERSHIP')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($foundMembership->fortnox_invoice_document_number)->toBe('INV-MEMBERSHIP');

    // Test SSFLICENSE filter
    $foundLicense = $user->payments()
        ->where('type', 'SSFLICENSE')
        ->where('year', 2024)
        ->whereNotNull('fortnox_invoice_document_number')
        ->first();

    expect($foundLicense->fortnox_invoice_document_number)->toBe('INV-LICENSE');
});
