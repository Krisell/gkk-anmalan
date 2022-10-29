<?php

use App\User;

test('an administrator can create accounts', function () {
    loginAdmin();

    $this->assertCount(1, User::all());

    $this->post('/admin/accounts', [
        'accounts' => [
            ['firstName' => 'Martin', 'lastName' => 'Krisell', 'email' => 'martin@example.com'],
            ['firstName' => 'Nils', 'lastName' => 'Krisell', 'email' => 'nils@example.com'],
        ],
    ]);

    $this->assertCount(3, User::all());
});
