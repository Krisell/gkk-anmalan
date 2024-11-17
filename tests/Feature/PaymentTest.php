<?php

use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\User;

test('a user has many payments', function () {
    $user = User::factory()->create();

    Payment::factory(3)->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2022,
    ]);

    $this->assertCount(3, $user->payments);
});

test('an admin can mark user as paid', function () {
    login();

    $user = User::factory()->create();

    $payment = Payment::factory()->for($user)->create([
        'type' => 'MEMBERSHIP',
        'year' => 2022,
        'sek_amount' => 100,
    ]);

    $this->patch("/payments/{$payment->id}", [
        'state' => 'PAID',
    ])->assertStatus(403);

    loginAdmin();

    $this->patch("/payments/{$payment->id}", [
        'state' => 'PAID',
    ])->assertOk();

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => auth()->id(),
        'action' => "updated payment {$payment['id']}",
    ]);

    $this->assertDatabaseHas(ActivityLog::class, [
        'performed_by' => auth()->id(),
        'action' => 'payment-update',
        'data' => \json_encode([
            'payment_id' => $payment['id'],
            'state' => 'PAID',
        ]),
    ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2022,
        'sek_amount' => 100,
        'state' => 'PAID',
    ]);
});

test('an admin can mark a payment as not paid', function () {
    login();

    $user = User::factory()->create();

    $paymentA = Payment::factory()->for($user)->create(['year' => 2022, 'state' => 'PAID']);
    $paymentB = Payment::factory()->for($user)->create(['year' => 2021, 'state' => 'PAID']);

    loginAdmin();

    $this->patch("/payments/{$paymentA->id}")->assertOk();

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => auth()->id(),
        'action' => "updated payment {$paymentA->id}",
    ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2022,
        'state' => null,
    ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2021,
        'state' => 'PAID',
    ]);

    $this->patch("/payments/{$paymentB->id}")->assertOk();

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => auth()->id(),
        'action' => "updated payment {$paymentB->id}",
    ]);

    $this->assertDatabaseHas(ActivityLog::class, [
        'performed_by' => auth()->id(),
        'action' => 'payment-update',
        'data' => \json_encode([
            'payment_id' => $paymentB->id,
            'state' => null,
        ]),
    ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2021,
        'state' => null,
    ]);
});
