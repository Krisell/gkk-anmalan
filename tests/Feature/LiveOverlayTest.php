<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function fakePowerliftingLive(array $overrides = []): void
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

    $topics = \array_merge([
        'next_lifters' => [
            'lifters' => [['weight' => '127.5', 'notice' => '', 'records' => [], 'lifter' => $lifter]],
            'info' => ['current_group' => 'Grupp 01', 'current_round' => 2],
        ],
        'scoresheet/current' => [
            'lifters' => [$lifter],
            'info' => ['current_discipline' => 'SQ'],
            'last_lifts' => [['lifter_id' => '110845', 'lift' => 'SQ1', 'status' => 'good', 'lifter' => $lifter]],
        ],
        'referee/decision' => ['updated' => 1791711790, 'left' => 'w', 'center' => 'w', 'right' => 'r'],
    ], $overrides);

    Http::fake(function ($request) use ($topics) {
        \parse_str(\parse_url($request->url(), PHP_URL_QUERY), $query);
        $topic = \preg_replace('#^competition/\d+/#', '', $query['key']);

        return \array_key_exists($topic, $topics)
            ? Http::response(\json_encode($topics[$topic]), 200, ['Content-Type' => 'text/html'])
            : Http::response('{}');
    });
}

test('the overlay page is a bare page without the regular site layout', function () {
    $this->get('/live/overlay?competition=3001')
        ->assertOk()
        ->assertSee('id="live-overlay"', false)
        ->assertDontSee('firebase');
});

test('the feed proxies and trims the Powerlifting Live topics', function () {
    fakePowerliftingLive();

    $response = $this->getJson('/live/feed/3001')->assertOk();

    $response->assertJsonPath('next_lifters.info.current_round', 2)
        ->assertJsonPath('next_lifters.lifters.0.weight', '127.5')
        ->assertJsonPath('next_lifters.lifters.0.lifter.name', 'Anna Andersson')
        ->assertJsonPath('next_lifters.lifters.0.lifter.lifts', ['SQ1' => '120.0', 'SQ1_status' => 'good'])
        ->assertJsonMissingPath('next_lifters.lifters.0.lifter.license')
        ->assertJsonMissingPath('next_lifters.lifters.0.lifter.records_sq')
        ->assertJsonMissingPath('scoresheet.lifters')
        ->assertJsonPath('scoresheet.info.current_discipline', 'SQ')
        ->assertJsonPath('scoresheet.last_lifts.0.lift', 'SQ1')
        ->assertJsonPath('decision.right', 'r');

    Http::assertSent(fn ($request) => \str_starts_with($request->url(), 'https://se.powerliftinglive.com/feed.php')
        && \str_contains(\urldecode($request->url()), 'key=competition/3001/next_lifters'));
});

test('the feed is cached briefly so that several overlays share one upstream request', function () {
    fakePowerliftingLive();

    $this->getJson('/live/feed/3001')->assertOk();
    $this->getJson('/live/feed/3001')->assertOk();

    Http::assertSentCount(3);
});

test('the feed reports upstream failures without caching them', function () {
    Http::fake(fn () => Http::response('Server error', 500));

    $this->getJson('/live/feed/3001')->assertStatus(502);
    expect(Cache::has('powerliftinglive.feed.3001'))->toBeFalse();
});

test('the feed only accepts numeric competition ids', function () {
    $this->getJson('/live/feed/abc')->assertNotFound();
});
