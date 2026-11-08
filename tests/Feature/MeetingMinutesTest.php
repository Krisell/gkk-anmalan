<?php

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\MeetingMinutes;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

function minutesPayload(array $overrides = []): array
{
    return [
        'meeting_type' => 'Styrelsemöte',
        'year' => 2026,
        'number' => 7,
        'date' => '2026-09-20',
        'start_time' => '17:00',
        'end_time' => '18:15',
        'location' => 'Discord (distans)',
        'chair' => 'Carl Öberg',
        'secretary' => 'Martin Sandström Krisell',
        'adjuster' => 'Philip Waagiström',
        'attendees' => [
            ['name' => 'Carl Öberg', 'present' => true],
            ['name' => 'Martin Sandström Krisell', 'present' => true],
            ['name' => 'Philip Waagiström', 'present' => true],
            ['name' => 'Ida Anemyr', 'present' => false],
        ],
        'items' => [
            ['title' => 'Serie 3 - utvärdering', 'body' => '<div>Det gick <strong>bra</strong>.</div>'],
            ['title' => 'Julfest', 'body' => '<div>En budget godkändes.</div>'],
        ],
        'other_business' => '',
        'next_meeting_at' => '2026-10-17T10:00',
        'next_meeting_location' => 'Majorna',
        'next_meeting_note' => 'Styrelsedag',
        ...$overrides,
    ];
}

test('only admins can reach the minutes', function () {
    login();

    $this->get('/admin/minutes')->assertStatus(401);
    $this->get('/admin/minutes/new')->assertStatus(401);
    $this->post('/admin/minutes', minutesPayload())->assertStatus(401);

    expect(MeetingMinutes::count())->toBe(0);
});

test('an admin can create minutes', function () {
    $admin = loginAdmin();

    $this->post('/admin/minutes', minutesPayload())->assertStatus(201);

    $minutes = MeetingMinutes::sole();
    expect($minutes->number)->toBe(7)
        ->and($minutes->created_by)->toBe($admin->id)
        ->and($minutes->items)->toHaveCount(2)
        ->and($minutes->absentNames())->toBe(['Ida Anemyr']);
});

test('an admin can update and delete minutes', function () {
    loginAdmin();
    $this->post('/admin/minutes', minutesPayload());
    $minutes = MeetingMinutes::sole();

    $this->put("/admin/minutes/{$minutes->id}", minutesPayload(['items' => []]))->assertOk();
    expect($minutes->fresh()->items)->toBe([]);

    $this->delete("/admin/minutes/{$minutes->id}")->assertOk();
    expect(MeetingMinutes::count())->toBe(0);
});

test('items need a title', function () {
    loginAdmin();

    $this->postJson('/admin/minutes', minutesPayload(['items' => [['title' => '', 'body' => 'x']]]))
        ->assertStatus(422)
        ->assertJsonValidationErrors('items.0.title');
});

test('the printed minutes number the standard items before and after the meetings own items', function () {
    loginAdmin();
    $this->post('/admin/minutes', minutesPayload());
    $minutes = MeetingMinutes::sole();

    $this->get("/admin/minutes/{$minutes->id}/print")
        ->assertOk()
        ->assertSeeInOrder([
            'Styrelsemöte 7, verksamhetsåret 2026',
            'Datum: 2026-09-20',
            'Tid: 17.00–18.15',
            'Plats: Discord (distans)',
            'Närvarande: Carl Öberg, Martin Sandström Krisell, Philip Waagiström',
            'Frånvarande: Ida Anemyr',
            '1. Mötets öppnande',
            'Mötesordförande Carl Öberg öppnar mötet.',
            '2. Sekreterare och protokolljusterare',
            'Martin Sandström Krisell väljs till sekreterare och Philip Waagiström till protokolljusterare.',
            '3. Godkännande av föregående mötesprotokoll',
            '4. Fastställande av dagordning',
            '5. Serie 3 - utvärdering',
            'Det gick <strong>bra</strong>.',
            '6. Julfest',
            '7. Övrigt',
            'Inga övriga frågor.',
            '8. Nästa möte',
            'Styrelsedag, Lördag 17 oktober 2026 kl. 10.00, Majorna.',
        ], false);
});

test('new minutes pick up where the latest meeting left off', function () {
    loginAdmin();
    $this->post('/admin/minutes', minutesPayload());

    $draft = MeetingMinutes::draftFromLatest();

    expect($draft->number)->toBe(8)
        ->and($draft->year)->toBe(2026)
        ->and($draft->date->format('Y-m-d'))->toBe('2026-10-17')
        ->and($draft->start_time)->toBe('10:00')
        ->and($draft->location)->toBe('Majorna')
        ->and($draft->chair)->toBe('Carl Öberg')
        ->and($draft->adjuster)->toBeNull()
        ->and(collect($draft->attendees)->every(fn ($attendee) => $attendee['present']))->toBeTrue()
        ->and(collect($draft->attendees)->pluck('name')->all())->toContain('Ida Anemyr');

    $this->get('/admin/minutes/new')->assertOk()->assertSee('gkk-admin-minutes-editor', false);
});

test('numbering starts over in a new year', function () {
    loginAdmin();
    $this->post('/admin/minutes', minutesPayload(['next_meeting_at' => '2027-01-10T17:00']));

    $draft = MeetingMinutes::draftFromLatest();

    expect($draft->year)->toBe(2027)->and($draft->number)->toBe(1)
        ->and(MeetingMinutes::nextNumbers())->toBe([2026 => 8]);
});

test('new minutes can be a copy of earlier minutes', function () {
    loginAdmin();
    $this->post('/admin/minutes', minutesPayload(['number' => 6, 'date' => '2026-08-20', 'other_business' => '<div>Fika</div>']));
    $this->post('/admin/minutes', minutesPayload(['items' => [], 'attendees' => []]));
    $original = MeetingMinutes::where('number', 6)->sole();

    $copy = MeetingMinutes::draftFromLatest($original);

    expect($copy->exists)->toBeFalse()
        ->and($copy->number)->toBe(8)
        ->and($copy->date->format('Y-m-d'))->toBe('2026-10-17')
        ->and($copy->items)->toBe($original->items)
        ->and($copy->other_business)->toBe('<div>Fika</div>')
        ->and($copy->adjuster)->toBe('Philip Waagiström')
        ->and(collect($copy->attendees)->pluck('name')->all())->toContain('Ida Anemyr')
        ->and(collect($copy->attendees)->every(fn ($attendee) => $attendee['present']))->toBeTrue();

    $this->get("/admin/minutes/new?copy={$original->id}")
        ->assertOk()
        ->assertViewHas('copiedFrom', 'Styrelsemöte 6, verksamhetsåret 2026');
    $this->get('/admin/minutes/new?copy=999')->assertNotFound();
});

test('attendees can be picked from active members', function () {
    loginAdmin();
    User::factory()->create(['first_name' => 'Aktiv', 'last_name' => 'Medlem']);
    User::factory()->inactivated()->create(['first_name' => 'Gammal', 'last_name' => 'Medlem']);

    $this->get('/admin/minutes/new')
        ->assertOk()
        ->assertSee('Aktiv Medlem')
        ->assertDontSee('Gammal Medlem');
});

function savedMinutes(array $overrides = []): MeetingMinutes
{
    return MeetingMinutes::create([...minutesPayload(), ...$overrides]);
}

function reviewUrl(MeetingMinutes $minutes, string $route = 'minutes.review'): string
{
    return URL::temporarySignedRoute($route, now()->addHour(), ['minutes' => $minutes->id]);
}

test('minutes can be sent to the adjuster, who approves them through a signed link', function () {
    loginAdmin();
    $minutes = savedMinutes();

    $this->postJson("/admin/minutes/{$minutes->id}/review")
        ->assertOk()
        ->assertJsonPath('status', 'review')
        ->assertJson(fn ($json) => $json->where('review_url', fn ($url) => \str_contains($url, 'signature='))->etc());

    Auth::logout();

    $this->get("/minutes/{$minutes->id}/review")->assertForbidden();
    $this->get(reviewUrl($minutes))->assertOk()->assertSee('Justera protokollet');

    $this->post(reviewUrl($minutes, 'minutes.review.approve'))->assertRedirect();

    $minutes->refresh();
    expect($minutes->status())->toBe('approved')
        ->and($minutes->approved_by_name)->toBe('Philip Waagiström');

    $this->get(reviewUrl($minutes))->assertSee('Protokollet är justerat')
        ->assertSee('Justerat '.now()->format('Y-m-d').' av Philip Waagiström');
});

test('the adjuster can ask for changes, and the minutes can be sent again', function () {
    loginAdmin();
    $minutes = savedMinutes(['review_requested_at' => now()]);

    $this->post(reviewUrl($minutes, 'minutes.review.changes'), ['comment' => 'Stavfel i punkt 5'])->assertRedirect();
    expect($minutes->fresh()->status())->toBe('changes_requested');

    $this->get("/admin/minutes/{$minutes->id}")->assertOk();
    $this->postJson("/admin/minutes/{$minutes->id}/review")->assertJsonPath('status', 'review')->assertJsonPath('review_comment', null);
});

test('minutes cannot be sent for review without an adjuster', function () {
    loginAdmin();
    $minutes = savedMinutes(['adjuster' => null]);

    $this->postJson("/admin/minutes/{$minutes->id}/review")->assertStatus(422);
});

test('a link cannot approve minutes that are not waiting for review', function () {
    $minutes = savedMinutes();

    $this->post(reviewUrl($minutes, 'minutes.review.approve'))->assertStatus(409);
    expect($minutes->fresh()->approved_at)->toBeNull();
});

test('approved minutes are locked until reopened', function () {
    loginAdmin();
    $minutes = savedMinutes(['review_requested_at' => now(), 'approved_at' => now(), 'approved_by_name' => 'Philip Waagiström']);

    $this->putJson("/admin/minutes/{$minutes->id}", minutesPayload(['items' => []]))->assertStatus(409);

    $this->postJson("/admin/minutes/{$minutes->id}/reopen")->assertJsonPath('status', 'draft');
    $this->putJson("/admin/minutes/{$minutes->id}", minutesPayload(['items' => []]))->assertOk();
});

test('approved minutes can be published to a document folder for members and unpublished again', function () {
    loginAdmin();
    $folder = DocumentFolder::factory()->create(['name' => 'Styrelseprotokoll', 'only_administrators' => false]);
    $minutes = savedMinutes(['review_requested_at' => now(), 'approved_at' => now(), 'approved_by_name' => 'Philip Waagiström']);

    $this->postJson("/admin/minutes/{$minutes->id}/publish", ['document_folder_id' => $folder->id, 'name' => 'Styrelsemöte 7, 2026'])
        ->assertOk()
        ->assertJsonPath('status', 'published')
        ->assertJsonPath('document_folder_id', $folder->id);

    $document = Document::sole();
    expect($document->url)->toBe("/member-documents/minutes/{$minutes->id}")
        ->and($document->name)->toBe('Styrelsemöte 7, 2026');

    login();
    $this->get($document->url)->assertOk()->assertSee('Styrelsemöte 7, verksamhetsåret 2026')->assertSee('Julfest');

    loginAdmin();
    $this->postJson("/admin/minutes/{$minutes->id}/reopen")->assertStatus(409);
    $this->deleteJson("/admin/minutes/{$minutes->id}/publish")->assertJsonPath('status', 'approved');
    expect(Document::count())->toBe(0);

    login();
    $this->get("/member-documents/minutes/{$minutes->id}")->assertNotFound();
});

test('only approved minutes can be published', function () {
    loginAdmin();
    $folder = DocumentFolder::factory()->create();
    $minutes = savedMinutes(['review_requested_at' => now()]);

    $this->postJson("/admin/minutes/{$minutes->id}/publish", ['document_folder_id' => $folder->id, 'name' => 'x'])->assertStatus(409);
    expect(Document::count())->toBe(0);
});

test('minutes published to an admin only folder are hidden from members', function () {
    $folder = DocumentFolder::factory()->create(['only_administrators' => true]);
    $minutes = savedMinutes(['approved_at' => now(), 'published_at' => now()]);
    $document = Document::create(['name' => 'x', 'url' => $minutes->publishedUrl(), 'document_folder_id' => $folder->id]);
    $minutes->update(['document_id' => $document->id]);

    login();
    $this->get($minutes->publishedUrl())->assertNotFound();

    loginAdmin();
    $this->get($minutes->publishedUrl())->assertOk();
});
