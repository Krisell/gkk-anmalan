<?php

use App\Mail\UnpaidFeeReminderMail;
use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// --- Auth ---

test('unauthenticated users cannot access payment tools', function () {
    $this->get('/admin/payment-tools')->assertRedirect();
});

test('non-admin users cannot access payment tools', function () {
    login();

    $this->get('/admin/payment-tools')->assertStatus(401);
});

test('admin users can access payment tools index', function () {
    loginAdmin();

    $this->get('/admin/payment-tools')->assertSuccessful();
});

// --- Generate Membership Payments: Preview ---

test('preview memberships returns count of eligible members', function () {
    loginAdmin(); // admin user is also eligible

    User::factory(3)->create(['birth_year' => 1990]);
    User::factory()->inactivated()->create();
    User::factory()->honorary()->create();

    $response = $this->postJson('/admin/payment-tools/generate-memberships/preview', [
        'year' => now()->year,
    ]);

    $response->assertOk();
    // 3 created + 1 admin user = 4 eligible
    expect($response->json('count'))->toBe(4);
});

test('preview memberships excludes users with existing payment', function () {
    loginAdmin(); // admin user is also eligible

    $users = User::factory(3)->create(['birth_year' => 1990]);

    Payment::factory()->create([
        'user_id' => $users[0]->id,
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
    ]);

    $response = $this->postJson('/admin/payment-tools/generate-memberships/preview', [
        'year' => now()->year,
    ]);

    $response->assertOk();
    // 2 without payment + 1 admin user = 3 eligible
    expect($response->json('count'))->toBe(3);
});

// --- Generate Membership Payments: Execute ---

test('execute memberships creates payment records for eligible members', function () {
    loginAdmin(); // admin user is also eligible
    $this->travelTo('2025-01-03');

    $normalUser = User::factory()->create(['birth_year' => 1990]);
    $youngUser = User::factory()->create(['birth_year' => now()->subYears(20)->year]);
    $studentUser = User::factory()->student()->create(['birth_year' => 1990]);

    $response = $this->postJson('/admin/payment-tools/generate-memberships/execute', [
        'year' => 2025,
    ]);

    $response->assertOk();
    // 3 created + 1 admin user = 4
    expect($response->json('count'))->toBe(4);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $normalUser->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 1500,
        'sek_discount' => 0,
        'modifier' => null,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $youngUser->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 700,
        'modifier' => 'AGE_DISCOUNT',
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $studentUser->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 700,
        'modifier' => 'STUDENT_DISCOUNT',
    ]);
});

test('execute memberships skips honorary and inactivated members', function () {
    loginAdmin(); // admin user is also eligible

    User::factory()->create();
    $honorary = User::factory()->honorary()->create();
    $inactive = User::factory()->inactivated()->create();

    $response = $this->postJson('/admin/payment-tools/generate-memberships/execute', [
        'year' => now()->year,
    ]);

    $response->assertOk();
    // 1 created + 1 admin user = 2
    expect($response->json('count'))->toBe(2);

    $this->assertDatabaseMissing(Payment::class, ['user_id' => $honorary->id]);
    $this->assertDatabaseMissing(Payment::class, ['user_id' => $inactive->id]);
});

test('execute memberships applies half year discount after june 15', function () {
    loginAdmin();
    $this->travelTo('2025-06-18');

    $user = User::factory()->create(['birth_year' => 1990]);

    $this->postJson('/admin/payment-tools/generate-memberships/execute', ['year' => 2025]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $user->id,
        'sek_amount' => 1500,
        'sek_discount' => 750,
    ]);
});

test('execute memberships applies quarter year discount after september 15', function () {
    loginAdmin();
    $this->travelTo('2025-09-18');

    $user = User::factory()->create(['birth_year' => 1990]);

    $this->postJson('/admin/payment-tools/generate-memberships/execute', ['year' => 2025]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $user->id,
        'sek_amount' => 1500,
        'sek_discount' => 1125,
    ]);
});

test('execute memberships does not create duplicates', function () {
    loginAdmin();

    $user = User::factory()->create(['birth_year' => 1990]);

    $this->postJson('/admin/payment-tools/generate-memberships/execute', ['year' => now()->year]);
    $this->postJson('/admin/payment-tools/generate-memberships/execute', ['year' => now()->year]);

    expect(Payment::where('user_id', $user->id)->where('type', 'MEMBERSHIP')->count())->toBe(1);
});

// --- Generate License Payments: Preview ---

test('preview licenses returns count of users needing licenses', function () {
    loginAdmin();

    $competition = Competition::factory()->create(['date' => now()->year.'-06-01']);
    $user = User::factory()->create();

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'status' => true,
    ]);

    $response = $this->postJson('/admin/payment-tools/generate-licenses/preview', [
        'year' => now()->year,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(1);
});

test('preview licenses excludes users with existing license', function () {
    loginAdmin();

    $competition = Competition::factory()->create(['date' => now()->year.'-06-01']);
    $user = User::factory()->create();

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'status' => true,
    ]);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'SSFLICENSE',
        'year' => now()->year,
    ]);

    $response = $this->postJson('/admin/payment-tools/generate-licenses/preview', [
        'year' => now()->year,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(0);
});

test('preview licenses only includes users with active registrations', function () {
    loginAdmin();

    $competition = Competition::factory()->create(['date' => now()->year.'-06-01']);
    $user = User::factory()->create();

    CompetitionRegistration::factory()->create([
        'user_id' => $user->id,
        'competition_id' => $competition->id,
        'status' => false,
    ]);

    $response = $this->postJson('/admin/payment-tools/generate-licenses/preview', [
        'year' => now()->year,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(0);
});

// --- Generate License Payments: Execute ---

test('execute licenses creates license payments using SSFLicense class', function () {
    loginAdmin();

    $competition = Competition::factory()->create(['date' => '2026-06-01']);

    $adultUser = User::factory()->create(['birth_year' => 2026 - 30]);
    $youthUser = User::factory()->create(['birth_year' => 2026 - 17]);

    foreach ([$adultUser, $youthUser] as $user) {
        CompetitionRegistration::factory()->create([
            'user_id' => $user->id,
            'competition_id' => $competition->id,
            'status' => true,
        ]);
    }

    $response = $this->postJson('/admin/payment-tools/generate-licenses/execute', [
        'year' => 2026,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(2);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $adultUser->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 1050,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $youthUser->id,
        'type' => 'SSFLICENSE',
        'year' => 2026,
        'sek_amount' => 300,
        'modifier' => 'YOUTH',
    ]);
});

// --- Create Competition Payments: Preview ---

test('preview competition payments returns counts and breakdown', function () {
    loginAdmin();

    $competition = Competition::factory()->create([
        'name' => 'Test Tävling',
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $senior = User::factory()->create(['birth_year' => 1990]);
    $junior = User::factory()->create(['birth_year' => 2010]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $senior->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $junior->id,
        'status' => 1,
    ]);

    $response = $this->postJson('/admin/payment-tools/competition-payments/preview', [
        'competition_id' => $competition->id,
        'senior_amount' => 500,
        'junior_amount' => 300,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(2);
    expect($response->json('seniors'))->toBe(1);
    expect($response->json('juniors'))->toBe(1);
    expect($response->json('competition_name'))->toBe('Test Tävling');
});

// --- Create Competition Payments: Execute ---

test('execute competition payments creates records with correct amounts', function () {
    loginAdmin();

    $competition = Competition::factory()->create([
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $senior = User::factory()->create(['birth_year' => 1990]);
    $junior = User::factory()->create(['birth_year' => 2010]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $senior->id,
        'status' => 1,
    ]);

    CompetitionRegistration::factory()->create([
        'competition_id' => $competition->id,
        'user_id' => $junior->id,
        'status' => 1,
    ]);

    $response = $this->postJson('/admin/payment-tools/competition-payments/execute', [
        'competition_id' => $competition->id,
        'senior_amount' => 500,
        'junior_amount' => 300,
    ]);

    $response->assertOk();
    expect($response->json('created'))->toBe(2);
    expect($response->json('skipped'))->toBe(0);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $senior->id,
        'competition_id' => $competition->id,
        'type' => 'COMPETITION',
        'sek_amount' => 500,
    ]);

    $this->assertDatabaseHas(Payment::class, [
        'user_id' => $junior->id,
        'competition_id' => $competition->id,
        'type' => 'COMPETITION',
        'sek_amount' => 300,
    ]);
});

test('execute competition payments skips existing payments', function () {
    loginAdmin();

    $competition = Competition::factory()->create([
        'date' => now()->addDays(30),
        'show_status' => 'show',
    ]);

    $user = User::factory()->create(['birth_year' => 1990]);

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
    ]);

    $response = $this->postJson('/admin/payment-tools/competition-payments/execute', [
        'competition_id' => $competition->id,
        'senior_amount' => 500,
        'junior_amount' => 300,
    ]);

    $response->assertOk();
    expect($response->json('created'))->toBe(0);
    expect($response->json('skipped'))->toBe(1);
});

// --- Sync Fortnox Customers: Preview ---

test('preview sync customers returns count of users without fortnox id', function () {
    loginAdmin();

    User::factory(2)->create(['fortnox_customer_id' => null]);
    User::factory()->create(['fortnox_customer_id' => 'GKK-123']);

    $response = $this->postJson('/admin/payment-tools/sync-customers/preview');

    $response->assertOk();
    // Count includes the admin user created by loginAdmin() who also lacks fortnox_customer_id
    expect($response->json('count'))->toBeGreaterThanOrEqual(2);
});

// --- Create Fortnox Invoices: Preview ---

test('preview create invoices returns count of payments needing invoices', function () {
    loginAdmin();

    Payment::factory(3)->create([
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Payment::factory()->create([
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
        'fortnox_invoice_document_number' => '12345',
        'state' => null,
    ]);

    $response = $this->postJson('/admin/payment-tools/create-invoices/preview', [
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(3);
});

test('preview create invoices filters by type', function () {
    loginAdmin();

    Payment::factory(2)->create([
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Payment::factory()->create([
        'type' => 'SSFLICENSE',
        'year' => now()->year,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    $response = $this->postJson('/admin/payment-tools/create-invoices/preview', [
        'type' => 'SSFLICENSE',
        'year' => now()->year,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(1);
});

// --- Email Invoices: Preview ---

test('preview email invoices returns count of unsent invoices', function () {
    loginAdmin();

    Payment::factory(2)->create([
        'fortnox_invoice_document_number' => '12345',
        'fortnox_invoice_emailed_at' => null,
    ]);

    Payment::factory()->create([
        'fortnox_invoice_document_number' => '12346',
        'fortnox_invoice_emailed_at' => now(),
    ]);

    $response = $this->postJson('/admin/payment-tools/email-invoices/preview');

    $response->assertOk();
    expect($response->json('count'))->toBe(2);
});

// --- Verify Payments: Preview ---

test('preview verify payments returns count of unverified invoices', function () {
    loginAdmin();

    Payment::factory(3)->create([
        'fortnox_invoice_document_number' => '12345',
        'state' => null,
    ]);

    Payment::factory()->create([
        'fortnox_invoice_document_number' => '12346',
        'state' => 'PAID',
    ]);

    $response = $this->postJson('/admin/payment-tools/verify-payments/preview');

    $response->assertOk();
    expect($response->json('count'))->toBe(3);
});

// --- Remind Unpaid Fees: Preview ---

test('preview remind unpaid returns overdue payments', function () {
    loginAdmin();

    $user = User::factory()->create(['first_name' => 'Test', 'last_name' => 'User']);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
        'state' => null,
        'sek_amount' => 1500,
        'sek_discount' => 0,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    // This one is too recent, should not be included
    Payment::factory()->create([
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(10),
    ]);

    $response = $this->postJson('/admin/payment-tools/remind-unpaid/preview', [
        'type' => 'ALL',
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(1);
    expect($response->json('items.0.user'))->toBe('Test User');
    expect($response->json('items.0.amount'))->toBe(1500);
});

test('preview remind unpaid filters by type', function () {
    loginAdmin();

    Payment::factory()->create([
        'type' => 'MEMBERSHIP',
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    Payment::factory()->create([
        'type' => 'SSFLICENSE',
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $response = $this->postJson('/admin/payment-tools/remind-unpaid/preview', [
        'type' => 'MEMBERSHIP',
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(1);
});

// --- Remind Unpaid Fees: Execute ---

test('execute remind unpaid sends emails', function () {
    loginAdmin();
    Mail::fake();

    $user = User::factory()->create(['email' => 'test@example.com']);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $response = $this->postJson('/admin/payment-tools/remind-unpaid/execute', [
        'type' => 'ALL',
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(1);

    Mail::assertSent(UnpaidFeeReminderMail::class, function ($mail) {
        return $mail->hasTo('test@example.com');
    });
});

test('execute remind unpaid does not send to paid or recent invoices', function () {
    loginAdmin();
    Mail::fake();

    // Paid - should not receive reminder
    Payment::factory()->create([
        'type' => 'MEMBERSHIP',
        'state' => 'PAID',
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    // Recent invoice - should not receive reminder
    Payment::factory()->create([
        'type' => 'MEMBERSHIP',
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(10),
    ]);

    $response = $this->postJson('/admin/payment-tools/remind-unpaid/execute', [
        'type' => 'ALL',
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(0);

    Mail::assertNothingSent();
});

// --- Streaming Fortnox operations: Auth check ---

test('execute sync customers returns error when fortnox not activated', function () {
    loginAdmin();

    $response = $this->postJson('/admin/payment-tools/sync-customers/execute');

    $response->assertStatus(422);
    expect($response->json('error'))->toContain('Fortnox');
});

test('execute create invoices returns error when fortnox not activated', function () {
    loginAdmin();

    $response = $this->postJson('/admin/payment-tools/create-invoices/execute', [
        'type' => 'MEMBERSHIP',
        'year' => now()->year,
    ]);

    $response->assertStatus(422);
    expect($response->json('error'))->toContain('Fortnox');
});

test('execute email invoices returns error when fortnox not activated', function () {
    loginAdmin();

    $response = $this->postJson('/admin/payment-tools/email-invoices/execute');

    $response->assertStatus(422);
    expect($response->json('error'))->toContain('Fortnox');
});

test('execute verify payments returns error when fortnox not activated', function () {
    loginAdmin();

    $response = $this->postJson('/admin/payment-tools/verify-payments/execute');

    $response->assertStatus(422);
    expect($response->json('error'))->toContain('Fortnox');
});
