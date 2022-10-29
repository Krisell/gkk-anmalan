<?php

use App\Result;

test('an administrator can add a result', function () {
    loginAdmin();

    $data = [
        'gender' => 'M',
        'competition_date' => '2021-03-02',
        'weight_class' => '74',
        'event' => 'Knäböj',
        'user_id' => 1,
        'result' => '150',
    ];

    $this->post('/admin/results', $data)->assertCreated();

    $this->assertDatabaseHas('results', $data);
});

test('an administrator can delete a result', function () {
    loginAdmin();

    $result = Result::factory()->create();

    $this->delete("/admin/results/{$result->id}")->assertOk();

    $this->assertEmpty(Result::all());
});
