<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function powerliftingLiveTopics(): array
{
    $lifter = [
        'id' => '110845',
        'name' => 'Anna Andersson',
        'team' => 'Göteborg KK',
        'weightclass' => '57',
        'category' => 'Senior',
        'license' => '010101ab1',
        'lifts' => ['SQ1' => '120.0', 'SQ1_status' => 'good', 'SQ1_records' => [['weight' => '130.0']]],
        'records_sq' => [['weight' => '130.0']],
    ];

    return [
        'next_lifters' => [
            'lifters' => [['weight' => '127.5', 'attempt' => '2', 'notice' => '', 'records' => [], 'lifter' => $lifter]],
            'info' => ['current_group' => 'Grupp 01', 'current_round' => 2],
        ],
        'scoresheet/current' => [
            'lifters' => [$lifter],
            'info' => ['current_discipline' => 'SQ'],
            'last_lifts' => [['lifter_id' => '110845', 'lift' => 'SQ1', 'status' => 'good', 'lifter' => $lifter]],
        ],
        'referee/decision' => ['updated' => 1791711790, 'left' => 'w', 'center' => 'w', 'right' => 'r'],
        'clock' => ['clock' => 47],
    ];
}

// Fakes Powerlifting Live's feed.php; changes to $topics after the call are picked up by later requests.
function fakePowerliftingLive(array &$topics): void
{
    Http::fake(function (Request $request) use (&$topics) {
        \parse_str(\parse_url($request->url(), PHP_URL_QUERY), $query);
        $topic = \preg_replace('#^competition/\d+/#', '', $query['key']);

        return \array_key_exists($topic, $topics)
            ? Http::response(\json_encode($topics[$topic]), 200, ['Content-Type' => 'text/html'])
            : Http::response('{}');
    });
}

function scoresheetRequests(): int
{
    return Http::recorded(fn (Request $request) => \str_contains(\urldecode($request->url()), '/scoresheet/'))->count();
}

test('the overlay page is a bare page without the regular site layout', function () {
    $this->get('/live/overlay?competition=3001')
        ->assertOk()
        ->assertSee('id="live-overlay"', false)
        ->assertDontSee('firebase');
});

test('the feed proxies and trims the Powerlifting Live topics', function () {
    $topics = powerliftingLiveTopics();
    fakePowerliftingLive($topics);

    $response = $this->getJson('/live/feed/3001')->assertOk();

    $response->assertJsonPath('next_lifters.info.current_round', 2)
        ->assertJsonPath('next_lifters.lifters.0.weight', '127.5')
        ->assertJsonPath('next_lifters.lifters.0.attempt', '2')
        ->assertJsonPath('next_lifters.lifters.0.lifter.name', 'Anna Andersson')
        ->assertJsonPath('next_lifters.lifters.0.lifter.lifts', ['SQ1' => '120.0', 'SQ1_status' => 'good'])
        ->assertJsonMissingPath('next_lifters.lifters.0.lifter.license')
        ->assertJsonMissingPath('next_lifters.lifters.0.lifter.records_sq')
        ->assertJsonPath('scoresheet.lifters.0.name', 'Anna Andersson')
        ->assertJsonPath('scoresheet.lifters.0.lifts', ['SQ1' => '120.0', 'SQ1_status' => 'good'])
        ->assertJsonMissingPath('scoresheet.lifters.0.license')
        ->assertJsonMissingPath('scoresheet.lifters.0.records_sq')
        ->assertJsonPath('scoresheet.info.current_discipline', 'SQ')
        ->assertJsonPath('scoresheet.last_lifts.0.lift', 'SQ1')
        ->assertJsonPath('decision.right', 'r')
        ->assertJsonPath('clock.clock', 47);

    Http::assertSent(fn (Request $request) => \str_starts_with($request->url(), 'https://se.powerliftinglive.com/feed.php')
        && \str_contains(\urldecode($request->url()), 'key=competition/3001/next_lifters')
        && \str_starts_with($request->header('User-Agent')[0] ?? '', 'GKK live overlay'));
});

test('lifters from the next group are left out', function () {
    $topics = powerliftingLiveTopics();
    $current = $topics['next_lifters']['lifters'][0];
    $topics['next_lifters']['lifters'] = [
        $current,
        ['weight' => '200.0', 'next_group' => 'Grupp 02', 'lifter' => ['id' => '2', 'name' => 'Next Group']],
        ['weight' => '135.0', 'lifter' => ['id' => '3', 'name' => 'Same Group']],
    ];
    fakePowerliftingLive($topics);

    $this->getJson('/live/feed/3001')
        ->assertOk()
        ->assertJsonCount(2, 'next_lifters.lifters')
        ->assertJsonPath('next_lifters.lifters.1.lifter.name', 'Same Group');
});

test('the feed is cached briefly so that several overlays share one upstream request', function () {
    $topics = powerliftingLiveTopics();
    fakePowerliftingLive($topics);

    $this->getJson('/live/feed/3001')->assertOk();
    $this->getJson('/live/feed/3001')->assertOk();

    // Lifter order, referee decision, clock and scoresheet, once.
    Http::assertSentCount(4);
});

test('the scoresheet is only fetched again when a result may have changed', function () {
    $topics = powerliftingLiveTopics();
    fakePowerliftingLive($topics);

    $this->getJson('/live/feed/3001')->assertOk();
    expect(scoresheetRequests())->toBe(1);

    // Nothing changed: the small topics are fetched again, the scoresheet is not.
    $this->travel(4)->seconds();
    $this->getJson('/live/feed/3001')->assertOk();
    expect(scoresheetRequests())->toBe(1);
    Http::assertSentCount(4 + 3);

    // A new referee decision means a result may be recorded: fetch it, and keep doing so for a few seconds.
    $topics['referee/decision'] = ['updated' => 1791711900, 'left' => 'r', 'center' => 'r', 'right' => 'r'];
    $topics['scoresheet/current']['last_lifts'][0]['status'] = 'failed';
    $this->travel(2)->seconds();
    $this->getJson('/live/feed/3001')->assertOk()->assertJsonPath('scoresheet.last_lifts.0.status', 'failed');
    expect(scoresheetRequests())->toBe(2);

    $this->travel(2)->seconds();
    $this->getJson('/live/feed/3001')->assertOk();
    expect(scoresheetRequests())->toBe(3);

    $this->travel(2)->seconds();
    $this->getJson('/live/feed/3001')->assertOk();
    expect(scoresheetRequests())->toBe(3);

    // Without changes it is still refreshed every 10 seconds.
    $this->travel(10)->seconds();
    $this->getJson('/live/feed/3001')->assertOk();
    expect(scoresheetRequests())->toBe(4);
});

test('the feed reports upstream failures without caching them', function () {
    Http::fake(fn () => Http::response('Server error', 500));

    $this->getJson('/live/feed/3001')->assertStatus(502);
    expect(Cache::has('powerliftinglive.feed.3001'))->toBeFalse();
});

test('the feed only accepts numeric competition ids', function () {
    $this->getJson('/live/feed/abc')->assertNotFound();
});

it('serves the whole competition per lifter, trimmed, and only fetches it once in a while', function () {
    Http::fake(['*' => Http::response(['lifters' => [[
        'id' => '1', 'name' => 'Anna Andersson', 'group' => 'Grupp 03', 'license' => '010101ab1', 'place' => '1',
        'lifts' => ['SQ1' => '120.0', 'SQ1_status' => 'good', 'SQ1_done' => '1792827427', 'SQ1_records' => [['weight' => '130.0']]],
    ]]])]);

    $this->getJson('/live/results/2939')
        ->assertOk()
        ->assertExactJson(['lifters' => [[
            'id' => '1', 'name' => 'Anna Andersson', 'group' => 'Grupp 03', 'place' => '1',
            'lifts' => ['SQ1' => '120.0', 'SQ1_status' => 'good'],
        ]]]);
    $this->getJson('/live/results/2939')->assertOk();

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request) => \str_contains(\urldecode($request->url()), 'key=competition/2939/scoresheet'));
});

it('serves the start order of the next group, trimmed, and caches it briefly', function () {
    Http::fake(['*' => Http::response(['lifters' => [
        [
            'lifter_id' => '2', 'attempt' => '1', 'discipline' => 'DL', 'lift' => 'DL1', 'weight' => '120.0', 'records' => [],
            'lifter' => ['id' => '2', 'name' => 'Bea Berg', 'group' => 'Grupp 01', 'license' => '020202ab2', 'records_dl' => [['weight' => '200.0']]],
        ],
        'junk',
    ]])]);

    $this->getJson('/live/next-group/3137')
        ->assertOk()
        ->assertExactJson(['lifters' => [[
            'lifter_id' => '2', 'attempt' => '1', 'discipline' => 'DL', 'lift' => 'DL1', 'weight' => '120.0',
            'lifter' => ['id' => '2', 'name' => 'Bea Berg', 'group' => 'Grupp 01'],
        ]]]);
    $this->getJson('/live/next-group/3137')->assertOk();

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request) => \str_contains(\urldecode($request->url()), 'key=competition/3137/next_lifters_next_group'));
});

it('reports when the next group cannot be fetched, without caching the failure', function () {
    Http::fake(['*' => Http::response('', 500)]);

    $this->getJson('/live/next-group/3137')->assertStatus(502);
    expect(Cache::has('powerliftinglive.next-group.3137'))->toBeFalse();
});
