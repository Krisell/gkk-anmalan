<?php

use App\Mail\CompetitionDeadlineNotification;
use App\Models\Competition;
use Illuminate\Support\Facades\Mail;

test('command shows info when no competitions with deadlines found', function () {
    Competition::factory()->create([
        'name' => 'Competition Without Deadline',
        'date' => now()->addDays(10),
        'last_registration_at' => null,
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('No competitions with upcoming deadlines found.');
});

test('command sends notification 5 days before deadline', function () {
    Mail::fake();

    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(15),
        'last_registration_at' => now()->addDays(5),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('Sent 5_days_before notification for competition: Test Competition (ID: '.$competition->id.')')
        ->expectsOutput('Total notifications sent: 1');

    Mail::assertSent(CompetitionDeadlineNotification::class, 1);

    Mail::assertSent(CompetitionDeadlineNotification::class, function ($mail) use ($competition) {
        return $mail->hasTo('martin.krisell@gmail.com') &&
               $mail->competition->id === $competition->id &&
               $mail->deadlineType === '5_days_before' &&
               $mail->daysUntilDeadline === 5;
    });
});

test('command sends notification on last day of registration', function () {
    Mail::fake();

    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(10),
        'last_registration_at' => now(),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('Sent last_day notification for competition: Test Competition (ID: '.$competition->id.')')
        ->expectsOutput('Total notifications sent: 1');

    Mail::assertSent(CompetitionDeadlineNotification::class, 1);

    Mail::assertSent(CompetitionDeadlineNotification::class, function ($mail) use ($competition) {
        return $mail->hasTo('martin.krisell@gmail.com') &&
               $mail->competition->id === $competition->id &&
               $mail->deadlineType === 'last_day' &&
               $mail->daysUntilDeadline === 0;
    });
});

test('command sends notification day after deadline', function () {
    Mail::fake();

    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(5),
        'last_registration_at' => now()->subDay(),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('Sent day_after notification for competition: Test Competition (ID: '.$competition->id.')')
        ->expectsOutput('Total notifications sent: 1');

    Mail::assertSent(CompetitionDeadlineNotification::class, 1);

    Mail::assertSent(CompetitionDeadlineNotification::class, function ($mail) use ($competition) {
        return $mail->hasTo('martin.krisell@gmail.com') &&
               $mail->competition->id === $competition->id &&
               $mail->deadlineType === 'day_after' &&
               $mail->daysUntilDeadline === -1;
    });
});

test('command does not send notification for competitions not matching criteria', function () {
    Mail::fake();

    // Competition with deadline in 3 days (not a trigger day)
    Competition::factory()->create([
        'name' => 'Competition 3 Days Away',
        'date' => now()->addDays(10),
        'last_registration_at' => now()->addDays(3),
    ]);

    // Competition with deadline in 10 days
    Competition::factory()->create([
        'name' => 'Competition 10 Days Away',
        'date' => now()->addDays(20),
        'last_registration_at' => now()->addDays(10),
    ]);

    // Competition with deadline 2 days ago (too old)
    Competition::factory()->create([
        'name' => 'Competition 2 Days Ago',
        'date' => now()->addDays(5),
        'last_registration_at' => now()->subDays(2),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('No competitions matched notification criteria today.');

    Mail::assertNotSent(CompetitionDeadlineNotification::class);
});

test('command sends multiple notifications for multiple competitions', function () {
    Mail::fake();

    $competition1 = Competition::factory()->create([
        'name' => 'Competition 5 Days Before',
        'date' => now()->addDays(15),
        'last_registration_at' => now()->addDays(5),
    ]);

    $competition2 = Competition::factory()->create([
        'name' => 'Competition Last Day',
        'date' => now()->addDays(10),
        'last_registration_at' => now(),
    ]);

    $competition3 = Competition::factory()->create([
        'name' => 'Competition Day After',
        'date' => now()->addDays(5),
        'last_registration_at' => now()->subDay(),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('Sent 5_days_before notification for competition: Competition 5 Days Before (ID: '.$competition1->id.')')
        ->expectsOutput('Sent last_day notification for competition: Competition Last Day (ID: '.$competition2->id.')')
        ->expectsOutput('Sent day_after notification for competition: Competition Day After (ID: '.$competition3->id.')')
        ->expectsOutput('Total notifications sent: 3');

    Mail::assertSent(CompetitionDeadlineNotification::class, 3);
});

test('command sends correct email content for 5 days before', function () {
    Mail::fake();

    $competition = Competition::factory()->create([
        'name' => 'Swedish Championship',
        'date' => now()->addDays(15),
        'last_registration_at' => now()->addDays(5),
        'description' => 'National powerlifting championship',
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful();

    Mail::assertSent(CompetitionDeadlineNotification::class, function ($mail) use ($competition) {
        $renderedMail = $mail->render();

        return $mail->hasTo('martin.krisell@gmail.com') &&
               $mail->hasSubject('Påminnelse: 5 dagar kvar till anmälningsslut - Swedish Championship') &&
               \str_contains($renderedMail, 'Swedish Championship') &&
               \str_contains($renderedMail, '5 dagar kvar till anmälningsslut') &&
               \str_contains($renderedMail, $competition->date->format('Y-m-d')) &&
               \str_contains($renderedMail, $competition->last_registration_at->format('Y-m-d'));
    });
});

test('command sends correct email content for last day', function () {
    Mail::fake();

    $competition = Competition::factory()->create([
        'name' => 'Swedish Championship',
        'date' => now()->addDays(10),
        'last_registration_at' => now(),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful();

    Mail::assertSent(CompetitionDeadlineNotification::class, function ($mail) {
        $renderedMail = $mail->render();

        return $mail->hasTo('martin.krisell@gmail.com') &&
               $mail->hasSubject('Sista dagen för anmälan - Swedish Championship') &&
               \str_contains($renderedMail, 'Sista dagen för anmälan') &&
               \str_contains($renderedMail, 'Swedish Championship');
    });
});

test('command sends correct email content for day after', function () {
    Mail::fake();

    $competition = Competition::factory()->create([
        'name' => 'Swedish Championship',
        'date' => now()->addDays(5),
        'last_registration_at' => now()->subDay(),
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful();

    Mail::assertSent(CompetitionDeadlineNotification::class, function ($mail) {
        $renderedMail = $mail->render();

        return $mail->hasTo('martin.krisell@gmail.com') &&
               $mail->hasSubject('Anmälningsperiod avslutad - dags att skicka in anmälan - Swedish Championship') &&
               \str_contains($renderedMail, 'Anmälningsperiod avslutad') &&
               \str_contains($renderedMail, 'dags att skicka in anmälan till arrangören') &&
               \str_contains($renderedMail, 'Swedish Championship');
    });
});

test('command ignores competitions without registration deadline', function () {
    Mail::fake();

    Competition::factory()->create([
        'name' => 'Competition Without Deadline',
        'date' => now()->addDays(10),
        'last_registration_at' => null,
    ]);

    Competition::factory()->create([
        'name' => 'Another Competition Without Deadline',
        'date' => now()->addDays(5),
        'last_registration_at' => null,
    ]);

    $this->artisan('gkk:notify-competition-deadlines')
        ->assertSuccessful()
        ->expectsOutput('No competitions with upcoming deadlines found.');

    Mail::assertNotSent(CompetitionDeadlineNotification::class);
});
