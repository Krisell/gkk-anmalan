<?php

use App\Mail\ClubRecordsImported;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Http::preventStrayRequests();
    Mail::fake();
    $this->travelTo('2026-11-01 06:20:00');
});

test('new club records are added, linked to the member, and emailed', function () {
    $user = User::factory()->create(['first_name' => 'Stella', 'last_name' => 'Larsdotter']);
    clubRecord('Knäböj', '140');
    clubRecord('Bänkpress', '110');
    clubRecord('Marklyft', '160');
    clubRecord('Total', '420');
    fakeStyrkelyft([styrkelyftResult()]);

    $this->artisan('gkk:import-club-records')->assertSuccessful();

    $added = Result::whereNotNull('styrkelyft_result_id')->orderBy('id')->get();
    expect($added->pluck('event')->all())->toBe(['Knäböj', 'Marklyft', 'Total']);
    expect($added->first())->toMatchArray([
        'gender' => 'F',
        'weight_class' => '63',
        'result' => '150',
        'user_id' => $user->id,
        'name' => null,
        'styrkelyft_result_id' => 1,
    ]);
    expect($added->last()->result)->toBe('427.5');

    Http::assertSent(fn ($request) => $request['start_date'] === '2026-10-18');
    Mail::assertSent(ClubRecordsImported::class, fn ($mail) => $mail->hasTo('martin.krisell@gmail.com') && \count($mail->records) === 3);
});

test('a dry run saves and emails nothing', function () {
    fakeStyrkelyft([styrkelyftResult()]);

    $this->artisan('gkk:import-club-records --dry-run --since=2015-01-01')
        ->expectsOutputToContain('Inget sparades (--dry-run)')
        ->assertSuccessful();

    expect(Result::count())->toBe(0);
    Mail::assertNothingSent();
    Http::assertSent(fn ($request) => $request['start_date'] === '2015-01-01');
});

test('running again adds nothing, and a record entered by hand is linked instead of added again', function () {
    // Entered by hand, with the competition's last day.
    $manual = clubRecord('Total', '427.5', ['name' => 'Stella Larsdotter', 'competition_date' => '2026-10-25']);
    clubRecord('Knäböj', '200');
    clubRecord('Bänkpress', '200');
    clubRecord('Marklyft', '200');
    fakeStyrkelyft([styrkelyftResult()]);

    $this->artisan('gkk:import-club-records')->assertSuccessful();
    $this->artisan('gkk:import-club-records')->expectsOutput('Inga nya klubbrekord.')->assertSuccessful();

    expect(Result::count())->toBe(4);
    expect($manual->fresh()->styrkelyft_result_id)->toBe(1);
    Mail::assertNothingSent();
});

test('only the best of several new records in a class is added, and an unknown lifter by name', function () {
    clubRecord('Total', '400');
    clubRecord('Knäböj', '999');
    clubRecord('Bänkpress', '999');
    clubRecord('Marklyft', '999');
    fakeStyrkelyft([
        styrkelyftResult(['id' => 2, 'total' => 420.0, 'group' => ['start' => '2026-10-30T10:00:00+01:00']]),
        styrkelyftResult(['id' => 1, 'total' => 410.0]),
    ]);

    $this->artisan('gkk:import-club-records')->assertSuccessful();

    $added = Result::whereNotNull('styrkelyft_result_id')->sole();
    expect($added)->toMatchArray(['result' => '420', 'styrkelyft_result_id' => 2, 'name' => 'Stella Larsdotter', 'user_id' => null]);
    expect(\substr((string) $added->competition_date, 0, 10))->toBe('2026-10-30');
});

test('a bad date is refused', function () {
    $this->artisan('gkk:import-club-records --since=förra-veckan')->assertFailed();
});
