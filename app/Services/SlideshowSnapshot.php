<?php

namespace App\Services;

use App\Models\Result;
use App\Models\Slide;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * The data the screen slideshow (/slides-for-screen) shows: the slides and the latest club records.
 *
 * The open screen must update when the data or the slideshow's code changes, without asking the app (on Laravel Cloud
 * every request keeps the app from scaling to zero). So the app publishes a version of the data and the frontend build
 * to Firebase's Realtime Database (at slideshow/published) whenever slides or results change and on every deploy, and
 * the screen subscribes to it and reloads when it changes. The rest of slideshow/ is the live control (see
 * resources/js/components/Slideshow/remote.js).
 */
class SlideshowSnapshot
{
    /**
     * @return array{slides: mixed, records: mixed, version: string}
     */
    public static function build(): array
    {
        $slides = Slide::select(['id', 'type', 'data', 'order', 'is_visible'])->orderBy('order')->get();
        $records = self::records();

        return [
            'slides' => $slides,
            'records' => $records,
            'version' => \hash('sha256', \json_encode([$slides, $records, self::frontendBuild()])),
        ];
    }

    /**
     * A club record is the best (highest) result for a given gender, weight class and event. Returns the 10 most
     * recently set records.
     */
    public static function records()
    {
        return Result::with('user:id,first_name,last_name')
            ->get()
            ->groupBy(fn (Result $result) => $result->gender.'|'.$result->weight_class.'|'.$result->event)
            ->map(fn ($group) => $group->sortByDesc(fn (Result $result) => (float) \str_replace(',', '.', $result->result))->first())
            ->sortByDesc('competition_date')
            ->take(10)
            ->values();
    }

    /**
     * Changes with every frontend build, so that a deploy reloads the open screens.
     */
    public static function frontendBuild(): ?string
    {
        $manifest = public_path('build/manifest.json');

        return \is_file($manifest) ? \md5_file($manifest) : null;
    }

    /**
     * Publishing is on only where FIREBASE_DATABASE_URL is set (production), since local .env files may hold the
     * production service account.
     */
    public static function enabled(): bool
    {
        return (bool) config('firebase.database_url');
    }

    /**
     * @return bool Whether the version was published.
     */
    public static function publish(): bool
    {
        if (! self::enabled()) {
            return false;
        }

        $published = [
            'version' => self::build()['version'],
            'build' => self::frontendBuild(),
            'publishedAt' => now()->toIso8601String(),
        ];

        try {
            // PATCH, not PUT, so that the live control's data next to it stays. The top-level version and build are
            // for screens still running the code from before slideshow/published; remove them once those have reloaded.
            Http::withToken(self::accessToken())
                ->patch(\rtrim(config('firebase.database_url'), '/').'/slideshow.json', [
                    'published' => $published,
                    'version' => $published['version'],
                    'build' => $published['build'],
                ])
                ->throw();

            return true;
        } catch (Throwable $e) {
            // A failed publish must not fail the admin's save. The open screens update at the next publish.
            report($e);

            return false;
        }
    }

    /**
     * Publishes once at the end of the request (or command), however many slides or results it changed.
     */
    public static function publishSoon(): void
    {
        // Kept on the container, not in a static property, so that it doesn't leak between Octane requests.
        if (! self::enabled() || app()->bound('slideshow.publish-scheduled')) {
            return;
        }

        app()->instance('slideshow.publish-scheduled', true);

        app()->terminating(function () {
            app()->forgetInstance('slideshow.publish-scheduled');
            self::publish();
        });
    }

    /**
     * An OAuth access token for the service account, which bypasses the database rules.
     * https://firebase.google.com/docs/database/rest/auth#google_oauth2_access_tokens
     */
    private static function accessToken(): string
    {
        $assertion = JWT::encode([
            'iss' => config('firebase.service_account_email'),
            'scope' => 'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => \time(),
            'exp' => \time() + 60 * 60,
        ], config('firebase.private_key'), 'RS256');

        return Http::asForm()
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ])
            ->throw()
            ->json('access_token');
    }
}
