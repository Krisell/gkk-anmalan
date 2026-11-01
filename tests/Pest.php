<?php

use App\Models\Result;
use App\Models\User;
use App\Services\StyrkelyftResults;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function login(?User $user = null)
{
    $user = $user ?: User::factory()->withExplicitApproval()->create();

    Auth::login($user);

    return $user;
}

function loginAdmin()
{
    $user = User::factory()->admin()->create();

    Auth::login($user);

    return $user;
}

function loginSuperadmin()
{
    $user = User::factory()->superadmin()->create();

    Auth::login($user);

    return $user;
}

/**
 * A result as data-api.styrkelyft.se returns it.
 */
function styrkelyftResult(array $overrides = []): array
{
    return \array_replace_recursive([
        'id' => 1,
        'group' => ['competition' => ['id' => 1881, 'name' => '2026 SM Klassisk styrkelyft'], 'start' => '2026-10-24T13:00:00+02:00'],
        'person' => ['first_name' => 'Stella', 'last_name' => 'Larsdotter', 'is_male' => false],
        'club' => ['id' => StyrkelyftResults::CLUB_ID, 'name' => 'Göteborg KK'],
        'competition_type' => ['id' => 1, 'name' => 'CPL', 'description' => 'Klassisk Styrkelyft'],
        'age_category' => ['label' => 'Senior'],
        'weight_class' => ['name' => '-63', 'is_male' => false],
        'body_weight' => 60.75,
        'squat' => 150.0,
        'benchpress' => 107.5,
        'deadlift' => 170.0,
        'total' => 427.5,
        'points' => ['ipfgl' => 95.81, 'ipfglbp' => 91.64],
        'disqualified' => false,
        'rank' => 1,
    ], $overrides);
}

function fakeStyrkelyft(array $results): void
{
    Http::fake(['data-api.styrkelyft.se/*' => Http::response(['count' => \count($results), 'next' => null, 'results' => $results])]);
}

function clubRecord(string $event, string $result, array $attributes = []): Result
{
    return Result::factory()->create([
        'gender' => 'F', 'weight_class' => '63', 'event' => $event, 'result' => $result,
        'competition_date' => '2022-03-28', 'user_id' => null, 'name' => 'Anna Björk', ...$attributes,
    ]);
}
