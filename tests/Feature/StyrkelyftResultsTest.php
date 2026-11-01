<?php

use App\Models\Result;
use App\Services\SlideshowSnapshot;
use App\Services\StyrkelyftResults;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
    $this->travelTo('2026-11-01 12:00:00');
});

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

function record(string $event, string $result, array $attributes = []): Result
{
    return Result::factory()->create([
        'gender' => 'F', 'weight_class' => '63', 'event' => $event, 'result' => $result,
        'competition_date' => '2022-03-28', 'user_id' => null, 'name' => 'Anna Björk', ...$attributes,
    ]);
}

test('only GKK results from the last 30 days are fetched, newest first', function () {
    fakeStyrkelyft([
        styrkelyftResult(['id' => 1]),
        styrkelyftResult(['id' => 2, 'club' => ['id' => 8, 'name' => 'Göteborgs SK']]),
        styrkelyftResult(['id' => 3, 'disqualified' => true]),
        styrkelyftResult(['id' => 4, 'group' => ['start' => '2026-10-31T10:00:00+01:00']]),
    ]);

    $results = StyrkelyftResults::recent();

    expect(collect($results)->pluck('id')->all())->toBe([4, 1]);
    expect($results[1])->toMatchArray([
        'date' => '2026-10-24',
        'name' => 'Stella Larsdotter',
        'gender' => 'F',
        'weight_class' => '63',
        'total' => 427.5,
        'points' => 95.81,
        'rank' => 1,
    ]);

    Http::assertSent(fn (Request $request) => \str_starts_with($request->url(), StyrkelyftResults::API)
        && $request['start_date'] === '2026-10-02');
});

test('a lift above the club record, or in a class without one, is a club record', function () {
    record('Knäböj', '140');
    record('Bänkpress', '110');
    record('Marklyft', '160');
    // No total registered.
    fakeStyrkelyft([styrkelyftResult()]);

    expect(StyrkelyftResults::recent()[0]['records'])->toBe(['squat', 'deadlift', 'total']);
});

test('the registered record is still marked, but not an equal lift by someone else long after', function () {
    record('Knäböj', '150', ['name' => 'Stella Larsdotter', 'competition_date' => '2026-10-25']);
    record('Bänkpress', '107,5', ['name' => 'Anna Björk']);
    record('Marklyft', '200');
    record('Total', '500');
    fakeStyrkelyft([styrkelyftResult()]);

    expect(StyrkelyftResults::recent()[0]['records'])->toBe(['squat']);
});

test('a bench press competition only counts for the bench record, and equipped results never', function () {
    record('Bänkpress', '100');
    fakeStyrkelyft([
        styrkelyftResult(['id' => 1, 'competition_type' => ['name' => 'CBP', 'description' => 'Klassisk Bänkpress'], 'squat' => 0, 'deadlift' => 0, 'total' => 107.5]),
        styrkelyftResult(['id' => 2, 'competition_type' => ['name' => 'EPL', 'description' => 'Utrustad Styrkelyft'], 'group' => ['start' => '2026-10-20T10:00:00+02:00']]),
    ]);

    [$bench, $equipped] = StyrkelyftResults::recent();

    expect($bench)->toMatchArray(['bench_only' => true, 'total' => null, 'points' => 91.64, 'records' => ['bench']]);
    expect($equipped['records'])->toBe([]);
});

test('a record beaten twice in the period is marked on both, but a lift below an earlier one is not', function () {
    record('Total', '400');
    fakeStyrkelyft([
        styrkelyftResult(['id' => 3, 'total' => 420.0, 'group' => ['start' => '2026-10-30T10:00:00+01:00']]),
        styrkelyftResult(['id' => 2, 'total' => 427.5]),
        styrkelyftResult(['id' => 1, 'total' => 410.0, 'group' => ['start' => '2026-10-10T10:00:00+02:00']]),
    ]);

    $records = collect(StyrkelyftResults::recent())->mapWithKeys(fn ($result) => [$result['id'] => \in_array('total', $result['records'])]);

    expect($records->all())->toBe([3 => false, 2 => true, 1 => true]);
});

test('the last results are kept when the API fails, and the snapshot carries them', function () {
    fakeStyrkelyft([styrkelyftResult()]);
    expect(StyrkelyftResults::recent())->toHaveCount(1);

    Cache::forget('styrkelyft.results');
    Http::fake(['data-api.styrkelyft.se/*' => Http::response('Bad gateway', 502)]);

    expect(StyrkelyftResults::recent())->toHaveCount(1);
    expect(SlideshowSnapshot::build()['results'])->toHaveCount(1);
});

test('nothing is shown when the API has never answered', function () {
    Http::fake(['data-api.styrkelyft.se/*' => Http::response('Bad gateway', 502)]);

    expect(StyrkelyftResults::recent())->toBe([]);
});
