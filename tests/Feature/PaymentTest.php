<?php

use App\Payment;
use App\User;

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

    $this->post("/admin/accounts/payment/{$user->id}", [
        'type' => 'MEMBERSHIP',
        'year' => 2022,
    ])->assertUnauthorized();

    loginAdmin();

    $this->post("/admin/accounts/payment/{$user->id}", [
        'type' => 'MEMBERSHIP',
        'year' => 2022,
    ])->assertCreated();

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2022,
    ]);
});

test('an admin can remove a payment', function () {
    login();

    $user = User::factory()->create();

    $paymentA = Payment::factory()->for($user)->create(['year' => 2022]);
    $paymentB = Payment::factory()->for($user)->create(['year' => 2021]);

    loginAdmin();

    $this->delete("/admin/accounts/payment/{$paymentA->id}")->assertOk();

    $this->assertDatabaseMissing('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2022,
    ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2021,
    ]);

    $this->delete("/admin/accounts/payment/{$paymentB->id}")->assertOk();

    $this->assertEmpty(Payment::all());
});
