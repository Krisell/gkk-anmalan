<?php

use App\Models\Result;
use App\Models\Slide;
use App\Services\SlideshowSnapshot;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
});

function enableSlideshowPublishing(bool $succeed = true)
{
    $key = \openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    \openssl_pkey_export($key, $privateKey);

    config([
        'firebase.database_url' => 'https://example-db.firebaseio.com',
        'firebase.service_account_email' => 'publisher@example.iam.gserviceaccount.com',
        'firebase.private_key' => $privateKey,
    ]);

    Http::fake($succeed ? [
        'oauth2.googleapis.com/token' => Http::response(['access_token' => 'test-token']),
        'example-db.firebaseio.com/*' => Http::response(),
    ] : [
        '*' => Http::response('Permission denied', 401),
    ]);
}

function publishedVersions(): array
{
    return Http::recorded(fn (Request $request) => $request->method() === 'PUT')
        ->map(fn ($pair) => $pair[0]['version'])
        ->values()
        ->all();
}

test('the app serves the slides and club records', function () {
    $slide = Slide::factory()->create();
    Result::factory()->create();

    $this->getJson('/slideshow/snapshot')
        ->assertOk()
        ->assertJsonPath('slides.0.id', $slide->id)
        ->assertJsonCount(1, 'records')
        ->assertJsonStructure(['slides', 'records', 'version']);
});

test('the version changes when the slides or the records change', function () {
    $slide = Slide::factory()->create();
    $version = SlideshowSnapshot::build()['version'];

    $slide->update(['data' => ['title' => 'Ny rubrik']]);
    expect(SlideshowSnapshot::build()['version'])->not->toBe($version);

    $version = SlideshowSnapshot::build()['version'];
    Result::factory()->create();
    expect(SlideshowSnapshot::build()['version'])->not->toBe($version);
});

test('changing slides publishes the version once, with the service account', function () {
    enableSlideshowPublishing();
    loginAdmin();

    $slides = Slide::factory()->count(3)->create();

    $this->post('/slideshow/order', [
        'slides' => $slides->reverse()->values()->map(fn ($slide) => ['id' => $slide->id])->all(),
    ])->assertOk();

    expect(publishedVersions())->toBe([SlideshowSnapshot::build()['version']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->url() === 'https://example-db.firebaseio.com/slideshow.json'
        && \array_key_exists('build', $request->data())
        && $request->hasHeader('Authorization', 'Bearer test-token'));
});

test('deleting a result publishes the version', function () {
    enableSlideshowPublishing();
    loginAdmin();

    $result = Result::factory()->create();

    $this->delete("/admin/results/{$result->id}")->assertOk();

    expect(last(publishedVersions()))->toBe(SlideshowSnapshot::build()['version']);
});

test('nothing is published without a database URL', function () {
    Http::fake();
    loginAdmin();

    Slide::factory()->create();
    $this->artisan('gkk:publish-slideshow')->assertSuccessful();

    Http::assertNothingSent();
});

test('a failed publish does not fail the save, but fails the command', function () {
    enableSlideshowPublishing(succeed: false);
    loginAdmin();

    $this->post('/slideshow/slides', ['type' => 'Slide', 'data' => ['title' => 'Hej']])->assertCreated();

    $this->artisan('gkk:publish-slideshow')->assertFailed();
});

test('the publish command publishes the version', function () {
    enableSlideshowPublishing();
    Slide::factory()->create();

    $this->artisan('gkk:publish-slideshow')->assertSuccessful();

    expect(publishedVersions())->toBe([SlideshowSnapshot::build()['version']]);
});
