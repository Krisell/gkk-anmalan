<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Live competition overlay (HUD) for OBS, backed by Powerlifting Live.
 *
 * Powerlifting Live publishes its live state as MQTT topics, and the latest
 * message of each topic can be read over HTTP via feed.php?key=<topic>. The
 * endpoint sends no CORS headers, so the overlay polls this proxy instead.
 * Going through our server also means Powerlifting Live only ever sees our
 * server, never the network at the competition venue. The proxy trims the
 * data to what the overlay needs and caches briefly so that several open
 * overlays share one request.
 */
class LiveOverlayController extends Controller
{
    // The small topics, fetched on every (uncached) request.
    private const TOPICS = [
        'next_lifters' => 'next_lifters',
        'decision' => 'referee/decision',
        'clock' => 'clock',
    ];

    // The scoresheet is by far the largest topic (~36 KB). It's fetched again only when the lifter order or the
    // referee decision changed (that is when a result can have been recorded), for a few seconds after such a
    // change in case Powerlifting Live updates its topics one after another, and otherwise every 10 seconds.
    private const SCORESHEET_FOLLOW_UP_SECONDS = 3;

    private const SCORESHEET_MAX_AGE_SECONDS = 10;

    private const LIFTER_FIELDS = [
        'id', 'name', 'team', 'weightclass', 'weightclass_short_alt', 'category', 'category_short', 'sex', 'raw', 'lot', 'lifts',
        // 'wilks' holds the IPF GL points (the field name is a leftover from the Wilks formula).
        'total', 'place', 'prog_place', 'wilks',
    ];

    public function show()
    {
        return view('live-overlay');
    }

    public function feed(int $competition)
    {
        $key = "powerliftinglive.feed.{$competition}";

        $feed = Cache::get($key);

        if ($feed === null) {
            $feed = $this->fetch($competition);

            if ($feed === null) {
                return response()->json(['error' => 'Powerlifting Live is unavailable'], 502)
                    ->header('Cache-Control', 'no-store');
            }

            Cache::put($key, $feed, 1);
        }

        return response()->json($feed)->header('Cache-Control', 'no-store');
    }

    private function fetch(int $competition): ?array
    {
        $data = $this->fetchTopics($competition, self::TOPICS);

        if ($data === null) {
            return null;
        }

        // Entries marked next_group belong to the following group; Powerlifting Live's own screens skip them.
        $upcoming = \array_values(\array_filter(
            $data['next_lifters']['lifters'] ?? [],
            fn ($entry) => \is_array($entry) && ($entry['next_group'] ?? null) === null,
        ));

        $scoresheet = $this->scoresheet($competition, \md5(\json_encode([$upcoming, $data['decision']])));

        if ($scoresheet === null) {
            return null;
        }

        return [
            'next_lifters' => [
                'info' => $data['next_lifters']['info'] ?? null,
                'lifters' => \array_map(fn ($entry) => $this->trimEntry($entry), \array_slice($upcoming, 0, 3)),
            ],
            'scoresheet' => $scoresheet,
            'decision' => $data['decision'],
            'clock' => $data['clock'],
            'fetched_at' => now()->getTimestampMs(),
        ];
    }

    /**
     * The trimmed scoresheet, from cache unless it may have changed. $state identifies the current lifter order
     * and referee decision.
     */
    private function scoresheet(int $competition, string $state): ?array
    {
        $key = "powerliftinglive.scoresheet.{$competition}";
        $now = now()->getTimestamp();
        $cached = Cache::get($key);

        $changedAt = $cached && $cached['state'] === $state ? $cached['changed_at'] : $now;
        $fresh = $cached
            && $now - $changedAt >= self::SCORESHEET_FOLLOW_UP_SECONDS
            && $now - $cached['fetched_at'] < self::SCORESHEET_MAX_AGE_SECONDS;

        if ($fresh) {
            return $cached['data'];
        }

        $topics = $this->fetchTopics($competition, ['scoresheet' => 'scoresheet/current']);

        if ($topics === null) {
            // Keep using the last scoresheet during a short glitch; the small topics are still current.
            return $cached['data'] ?? null;
        }

        $data = [
            'info' => $topics['scoresheet']['info'] ?? null,
            'last_lifts' => \array_map(
                fn ($entry) => $this->trimEntry($entry),
                \array_slice($topics['scoresheet']['last_lifts'] ?? [], 0, 3),
            ),
            // The current group's lifters, in Powerlifting Live's order, for the results list (?view=scoreboard).
            'lifters' => \array_map(fn ($lifter) => $this->trimLifter($lifter), $topics['scoresheet']['lifters'] ?? []),
        ];

        Cache::put($key, ['state' => $state, 'changed_at' => $changedAt, 'fetched_at' => $now, 'data' => $data], 60);

        return $data;
    }

    /**
     * Fetches the latest message of each topic in parallel, or null if any of them failed.
     */
    private function fetchTopics(int $competition, array $topics): ?array
    {
        $baseUrl = \rtrim(config('services.powerliftinglive.url'), '/');
        // Identifies us to Powerlifting Live, so that they can get in touch rather than block the requests.
        $userAgent = 'GKK live overlay (+'.config('app.url').')';

        try {
            $responses = Http::pool(fn (Pool $pool) => \array_map(
                fn (string $topic, string $name) => $pool->as($name)
                    ->withUserAgent($userAgent)
                    ->timeout(4)
                    ->get("{$baseUrl}/feed.php", ['key' => "competition/{$competition}/{$topic}"]),
                $topics,
                \array_keys($topics),
            ));
        } catch (\Throwable $e) {
            return null;
        }

        $data = [];

        foreach (\array_keys($topics) as $name) {
            $response = $responses[$name] ?? null;

            if (! $response instanceof Response || ! $response->successful() || ! \is_array($response->json())) {
                return null;
            }

            $data[$name] = $response->json();
        }

        return $data;
    }

    /**
     * Keep the scalar fields of a next_lifters/last_lifts entry and a subset of its nested lifter.
     */
    private function trimEntry(mixed $entry): array
    {
        if (! \is_array($entry)) {
            return [];
        }

        $trimmed = \array_filter($entry, fn ($value) => ! \is_array($value));

        if (\is_array($entry['lifter'] ?? null)) {
            $trimmed['lifter'] = $this->trimLifter($entry['lifter']);
        }

        return $trimmed;
    }

    /**
     * The lifter fields the overlay uses, with the per-attempt record lists removed from the lifts.
     */
    private function trimLifter(mixed $lifter): array
    {
        if (! \is_array($lifter)) {
            return [];
        }

        $trimmed = Arr::only($lifter, self::LIFTER_FIELDS);

        if (\is_array($trimmed['lifts'] ?? null)) {
            $trimmed['lifts'] = \array_filter($trimmed['lifts'], fn ($value) => ! \is_array($value));
        }

        return $trimmed;
    }
}
