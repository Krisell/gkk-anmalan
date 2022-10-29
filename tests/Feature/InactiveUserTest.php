<?php

use App\User;

test('an_inactive_user_is_redirected_to_an_information_page', function () {
    login(User::factory()->inactivated()->create());

    $this->get('/')->assertRedirect('/inactivated');
    $this->get('/events')->assertRedirect('/inactivated');
    $this->get('/competitions')->assertRedirect('/inactivated');
    $this->get('/dokument')->assertRedirect('/inactivated');
});

test('an_administrator_can_inactivate_a_user_account', function () {
    $user = User::factory()->create();

    $this->assertNull($user->inactivated_at);

    login(User::factory()->create(['role' => 'user']));

    $this->post("/admin/accounts/inactivate/{$user->id}")->assertUnauthorized();

    loginAdmin();

    $this->post("/admin/accounts/inactivate/{$user->id}")->assertOk();
    $this->assertNotNull($user->fresh()->inactivated_at);
});

test('an_administrator_can_reactivate_a_user_account', function () {
    $user = User::factory()->inactivated()->create();
    $this->assertNotNull($user->inactivated_at);

    login(User::factory()->create(['role' => 'user']));

    $this->post("/admin/accounts/reactivate/{$user->id}")->assertUnauthorized();

    loginAdmin();

    $this->post("/admin/accounts/reactivate/{$user->id}")->assertOk();
    $this->assertNull($user->fresh()->inactivated_at);
});
