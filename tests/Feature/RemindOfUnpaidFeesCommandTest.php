<?php

use App\Mail\UnpaidFeeReminderMail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'no')
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
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'no')
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
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'no')
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
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 2 användare?', 'no')
        ->assertSuccessful()
        ->expectsOutput('Found 2 unpaid fees.')
        ->expectsOutputToContain('Member User');
});

test('command sends emails when user confirms', function () {
    Mail::fake();

    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
    ]);

    $payment1 = Payment::factory()->create([
        'user_id' => $user1->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $payment2 = Payment::factory()->create([
        'user_id' => $user2->id,
        'type' => 'SSFLICENSE',
        'sek_amount' => 300,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 2 användare?', 'yes')
        ->assertSuccessful()
        ->expectsOutput('Skickar påminnelser...')
        ->expectsOutput('✓ Påminnelse skickad till John Doe (john@example.com)')
        ->expectsOutput('✓ Påminnelse skickad till Jane Smith (jane@example.com)')
        ->expectsOutput('Totalt 2 påminnelser skickade.');

    Mail::assertSent(UnpaidFeeReminderMail::class, 2);

    Mail::assertSent(UnpaidFeeReminderMail::class, function ($mail) use ($user1, $payment1) {
        return $mail->hasTo($user1->email) &&
               $mail->user->id === $user1->id &&
               $mail->payment->id === $payment1->id;
    });

    Mail::assertSent(UnpaidFeeReminderMail::class, function ($mail) use ($user2, $payment2) {
        return $mail->hasTo($user2->email) &&
               $mail->user->id === $user2->id &&
               $mail->payment->id === $payment2->id;
    });
});

test('command does not send emails when user declines', function () {
    Mail::fake();

    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'no')
        ->assertSuccessful()
        ->expectsOutput('Inga emails skickade.');

    Mail::assertNotSent(UnpaidFeeReminderMail::class);
});

test('command sends correct email content', function () {
    Mail::fake();

    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'ALL')
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'yes')
        ->assertSuccessful();

    Mail::assertSent(UnpaidFeeReminderMail::class, function ($mail) use ($user, $payment) {
        $renderedMail = $mail->render();

        return $mail->hasTo($user->email) &&
               $mail->hasSubject('Påminnelse om obetald avgift') &&
               \str_contains($renderedMail, $user->first_name) &&
               \str_contains($renderedMail, $payment->type) &&
               \str_contains($renderedMail, $payment->year) &&
               \str_contains($renderedMail, $payment->sek_amount);
    });
});

test('command allows selecting a single user to remind', function () {
    Mail::fake();

    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
    ]);

    $payment1 = Payment::factory()->create([
        'user_id' => $user1->id,
        'type' => 'MEMBERSHIP',
        'sek_amount' => 500,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $payment2 = Payment::factory()->create([
        'user_id' => $user2->id,
        'type' => 'SSFLICENSE',
        'sek_amount' => 300,
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'SINGLE')
        ->expectsQuestion('Select a user to remind:', $user1->id)
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'yes')
        ->assertSuccessful()
        ->expectsOutput('✓ Påminnelse skickad till John Doe (john@example.com)')
        ->doesntExpectOutputToContain('jane@example.com');

    Mail::assertSent(UnpaidFeeReminderMail::class, 1);

    Mail::assertSent(UnpaidFeeReminderMail::class, function ($mail) use ($user1, $payment1) {
        return $mail->hasTo($user1->email) &&
               $mail->user->id === $user1->id &&
               $mail->payment->id === $payment1->id;
    });

    Mail::assertNotSent(UnpaidFeeReminderMail::class, function ($mail) use ($user2) {
        return $mail->hasTo($user2->email);
    });
});

test('command shows correct user options when selecting single user', function () {
    $user1 = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $user2 = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);

    Payment::factory()->create([
        'user_id' => $user1->id,
        'type' => 'MEMBERSHIP',
        'year' => 2024,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    Payment::factory()->create([
        'user_id' => $user2->id,
        'type' => 'SSFLICENSE',
        'year' => 2025,
        'state' => null,
        'fortnox_invoice_emailed_at' => now()->subDays(45),
    ]);

    $this->artisan('gkk:remind-of-unpaid-fees')
        ->expectsQuestion('Which payment type do you want to check?', 'ALL')
        ->expectsQuestion('Do you want to remind all users or select a specific user?', 'SINGLE')
        ->expectsQuestion('Select a user to remind:', $user2->id)
        ->expectsConfirmation('Vill du skicka påminnelser via email till 1 användare?', 'no')
        ->assertSuccessful()
        ->expectsOutput('Found 1 unpaid fees.')
        ->expectsOutputToContain('Jane Smith');
});
