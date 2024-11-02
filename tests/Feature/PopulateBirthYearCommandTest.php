<?php

use App\Models\User;

test('Birth year is correctly populated from licence_number', function ($licenseNumber, $expectedBirthYear) {
    $user = User::factory()->create(['licence_number' => $licenseNumber]);

    $this->assertNull($user->birth_year);

    $this->artisan('populate-birth-year')
        ->expectsOutput('Populating birth year from licence number ...')
        ->expectsOutput('Birth year successfully populated for 1 users.')
        ->assertExitCode(0);

    $user->refresh();

    $this->assertEquals($expectedBirthYear, $user->birth_year);
})->with([
    ['730819aa', 1973],
    ['820107bb', 1982],
    ['870819aa', 1987],
    ['920107bb', 1992],
    ['000101cc', 2000],
    ['010101dd', 2001],
    ['020101ee', 2002],
    ['050101hh', 2005],
    ['130101hh', 2013],
]);
