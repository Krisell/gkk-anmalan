<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

test('emails are stored lowercase', function () {
    $user = User::factory()->create(['email' => ' Martin.Krisell@Example.com ']);

    expect($user->fresh()->email)->toBe('martin.krisell@example.com');
});

test('sign in works regardless of email case', function () {
    User::factory()->create(['email' => 'martin@example.com']);

    $this->post('/login', [
        'email' => 'Martin@Example.COM',
        'password' => 'password', // default in factory
    ])->assertRedirect('/insidan');

    $this->assertNotNull(auth()->user());
});

test('registering with a different-case duplicate email is rejected', function () {
    Mail::fake();
    User::factory()->create(['email' => 'martin@example.com']);

    $this->post('register', [
        'first_name' => 'Martin',
        'last_name' => 'Krisell',
        'birth_year' => 1987,
        'email' => 'MARTIN@example.com',
        'password' => 'asdasdasd',
        'password_confirmation' => 'asdasdasd',
    ])->assertSessionHasErrors('email');

    $this->assertCount(1, User::all());
});

test('microsoft sign in matches a mixed-case address', function () {
    $user = User::factory()->create(['email' => 'martin@example.com']);

    Http::fake([
        'graph.microsoft.com/*' => Http::response(['mail' => 'Martin@Example.com']),
    ]);

    $this->post('/auth/microsoft', ['accessToken' => 'token'])->assertRedirect('/insidan');

    expect(auth()->id())->toBe($user->id);
});

test('admin-created accounts skip existing emails regardless of case', function () {
    loginAdmin();
    Mail::fake();
    User::factory()->create(['email' => 'martin@example.com']);

    $this->post('/admin/accounts', [
        'accounts' => [
            ['firstName' => 'Martin', 'lastName' => 'Krisell', 'email' => 'Martin@Example.com'],
        ],
    ]);

    expect(User::count())->toBe(2); // the admin and the existing user
});
