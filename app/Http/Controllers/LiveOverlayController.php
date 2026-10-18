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
 * The proxy also trims the (fairly large) scoresheet to what the overlay
 * needs and caches briefly so that several open overlays share one request.
 */
class LiveOverlayController extends Controller
{
    private const TOPICS = [
        'next_lifters' => 'next_lifters',
        'scoresheet' => 'scoresheet/current',
        'decision' => 'referee/decision',
    ];

    private const LIFTER_FIELDS = [
        'id', 'name', 'team', 'weightclass', 'weightclass_short_alt', 'category', 'category_short', 'sex', 'raw', 'lot', 'lifts',
        'total', 'place', 'prog_place',
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
        $baseUrl = \rtrim(config('services.powerliftinglive.url'), '/');

        try {
            $responses = Http::pool(fn (Pool $pool) => \array_map(
                fn (string $topic, string $name) => $pool->as($name)
                    ->timeout(4)
                    ->get("{$baseUrl}/feed.php", ['key' => "competition/{$competition}/{$topic}"]),
                self::TOPICS,
                \array_keys(self::TOPICS),
            ));
        } catch (\Throwable $e) {
            return null;
        }

        $data = [];

        foreach (\array_keys(self::TOPICS) as $name) {
            $response = $responses[$name] ?? null;

            if (! $response instanceof Response || ! $response->successful() || ! \is_array($response->json())) {
                return null;
            }

            $data[$name] = $response->json();
        }

        $nextLifters = $data['next_lifters'];
        $scoresheet = $data['scoresheet'];

        return [
            'next_lifters' => [
                'info' => $nextLifters['info'] ?? null,
                'lifters' => \array_map(
                    fn ($entry) => $this->trimEntry($entry),
                    \array_slice($nextLifters['lifters'] ?? [], 0, 3),
                ),
            ],
            'scoresheet' => [
                'info' => $scoresheet['info'] ?? null,
                'last_lifts' => \array_map(
                    fn ($entry) => $this->trimEntry($entry),
                    \array_slice($scoresheet['last_lifts'] ?? [], 0, 3),
                ),
            ],
            'decision' => $data['decision'],
            'fetched_at' => now()->getTimestampMs(),
        ];
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
            $lifter = Arr::only($entry['lifter'], self::LIFTER_FIELDS);

            if (\is_array($lifter['lifts'] ?? null)) {
                $lifter['lifts'] = \array_filter($lifter['lifts'], fn ($value) => ! \is_array($value));
            }

            $trimmed['lifter'] = $lifter;
        }

        return $trimmed;
    }
}
