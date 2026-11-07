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
    clubRecord('Knäböj', '140');
    clubRecord('Bänkpress', '110');
    clubRecord('Marklyft', '160');
    // No total registered.
    fakeStyrkelyft([styrkelyftResult()]);

    expect(StyrkelyftResults::recent()[0]['records'])->toBe(['squat', 'deadlift', 'total']);
});

test('the registered record is still marked, but not an equal lift by someone else long after', function () {
    clubRecord('Knäböj', '150', ['name' => 'Stella Larsdotter', 'competition_date' => '2026-10-25']);
    clubRecord('Bänkpress', '107,5', ['name' => 'Anna Björk']);
    clubRecord('Marklyft', '200');
    clubRecord('Total', '500');
    fakeStyrkelyft([styrkelyftResult()]);

    expect(StyrkelyftResults::recent()[0]['records'])->toBe(['squat']);
});

test('a bench press competition only counts for the bench record, and equipped results never', function () {
    clubRecord('Bänkpress', '100');
    fakeStyrkelyft([
        styrkelyftResult(['id' => 1, 'competition_type' => ['name' => 'CBP', 'description' => 'Klassisk Bänkpress'], 'squat' => 0, 'deadlift' => 0, 'total' => 107.5]),
        styrkelyftResult(['id' => 2, 'competition_type' => ['name' => 'EPL', 'description' => 'Utrustad Styrkelyft'], 'group' => ['start' => '2026-10-20T10:00:00+02:00']]),
    ]);

    [$bench, $equipped] = StyrkelyftResults::recent();

    expect($bench)->toMatchArray(['bench_only' => true, 'total' => null, 'points' => 91.64, 'records' => ['bench']]);
    expect($equipped['records'])->toBe([]);
});

test('a record beaten twice in the period is marked on both, but a lift below an earlier one is not', function () {
    clubRecord('Total', '400');
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

test('equalling the record is not a record, unless it is the registered lift itself', function () {
    // Someone else, the same week: equals the record.
    clubRecord('Knäböj', '150', ['name' => 'Anna Björk', 'competition_date' => '2026-10-24']);
    // The same lifter, at an earlier competition: equals her own record.
    clubRecord('Bänkpress', '107.5', ['name' => 'Stella Larsdotter', 'competition_date' => '2026-06-01']);
    // Imported from this very result.
    clubRecord('Marklyft', '170', ['styrkelyft_result_id' => 1, 'competition_date' => '2026-10-25']);
    // Imported from another result.
    clubRecord('Total', '427.5', ['styrkelyft_result_id' => 999, 'competition_date' => '2026-10-24']);
    fakeStyrkelyft([styrkelyftResult()]);

    expect(StyrkelyftResults::recent()[0]['records'])->toBe(['deadlift']);
});

test('the results are fetched a month at a time, and a month too large for a page is split', function () {
    Http::fake(function (Request $request) {
        \parse_str(\parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);
        // end_date is exclusive.
        $days = (\strtotime($query['end_date']) - \strtotime($query['start_date'])) / 86400;

        // More than fits on a page, unless at most a week.
        return $days > 7
            ? Http::response(['results' => [], 'next' => StyrkelyftResults::API.'?page=2'])
            : Http::response(['next' => null, 'results' => [styrkelyftResult([
                'id' => \crc32($query['start_date']),
                'group' => ['start' => $query['start_date'].'T10:00:00+01:00'],
            ])]]);
    });

    $results = StyrkelyftResults::fetch('2026-09-01');

    $windows = Http::recorded()->map(function ($pair) {
        \parse_str(\parse_url($pair[0]->url(), PHP_URL_QUERY) ?? '', $query);

        return [$query['start_date'], $query['end_date']];
    });
    // Every day from the start to today, including today, in exactly one window of at most a week (end_date is
    // exclusive).
    $leaves = $windows->filter(fn ($w) => (\strtotime($w[1]) - \strtotime($w[0])) / 86400 <= 7)->sortBy(0)->values();
    expect($leaves->first()[0])->toBe('2026-09-01');
    expect($leaves->last()[1])->toBe('2026-11-02');
    $leaves->sliding(2)->each(fn ($pair) => expect($pair->first()[1])->toBe($pair->last()[0]));
    expect($results)->toHaveCount($leaves->count());
});

test('a page after the first keeps its filters, and a result on two pages is kept once', function () {
    $next = StyrkelyftResults::API.'?end_date=2026-10-24&page=2&page_size=1000&start_date=2026-10-24';
    Http::fake(function (Request $request) use ($next) {
        return \str_contains($request->url(), 'page=2')
            ? Http::response(['next' => null, 'results' => [styrkelyftResult(['id' => 1]), styrkelyftResult(['id' => 2])]])
            : Http::response(['next' => $next, 'results' => [styrkelyftResult(['id' => 1])]]);
    });

    expect(collect(StyrkelyftResults::fetch('2026-10-24'))->pluck('id')->sort()->values()->all())->toBe([1, 2]);
    Http::assertSent(fn (Request $request) => $request->url() === $next);
});
