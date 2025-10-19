<?php

use App\Mail\EventCompetitionNotificationMail;
use App\Models\Competition;
use App\Models\Event;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('admin can send notification email for an event', function () {
    Mail::fake();

    $admin = loginAdmin();
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'date' => now()->addDays(7),
        'location' => 'Test Location',
    ]);

    // Create some granted users (granted_by > 0 means granted)
    User::factory()->count(3)->create();

    $response = $this->post("/admin/events/{$event->id}/send-notification", [
        'confirmed' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'recipients_count' => 3,
    ]);

    // Verify emails were sent
    Mail::assertSent(EventCompetitionNotificationMail::class, 3);

    // Verify notification log was created
    expect(NotificationLog::count())->toBe(1);
    $log = NotificationLog::first();
    expect($log->notifiable_id)->toBe($event->id);
    expect($log->notifiable_type)->toBe(Event::class);
    expect($log->sent_by)->toBe($admin->id);
    expect($log->recipients_count)->toBe(3);
});

test('admin can send notification email for a competition', function () {
    Mail::fake();

    $admin = loginAdmin();
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => now()->addDays(7),
        'location' => 'Test Location',
        'events' => json_encode(['ksl' => true, 'kbp' => true]),
    ]);

    // Create some granted users (granted_by > 0 means granted)
    User::factory()->count(2)->create();

    $response = $this->post("/admin/competitions/{$competition->id}/send-notification", [
        'confirmed' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'recipients_count' => 2,
    ]);

    // Verify emails were sent
    Mail::assertSent(EventCompetitionNotificationMail::class, 2);

    // Verify notification log was created
    expect(NotificationLog::count())->toBe(1);
    $log = NotificationLog::first();
    expect($log->notifiable_id)->toBe($competition->id);
    expect($log->notifiable_type)->toBe(Competition::class);
    expect($log->sent_by)->toBe($admin->id);
    expect($log->recipients_count)->toBe(2);
});

test('notification requires confirmation', function () {
    $admin = loginAdmin();
    $event = Event::factory()->create();

    $response = $this->post("/admin/events/{$event->id}/send-notification", [
        'confirmed' => false,
    ]);

    $response->assertStatus(422);
});

test('admin can view notification status for an event', function () {
    $admin = loginAdmin();
    $event = Event::factory()->create();

    // Create a notification log
    NotificationLog::create([
        'notifiable_type' => Event::class,
        'notifiable_id' => $event->id,
        'sent_by' => $admin->id,
        'recipients_count' => 5,
    ]);

    $response = $this->get("/admin/events/{$event->id}/notification-status");

    $response->assertStatus(200);
    $response->assertJsonCount(1, 'notifications');
    $response->assertJsonPath('notifications.0.recipients_count', 5);
});

test('notification email renders correctly for event', function () {
    $event = Event::factory()->create([
        'name' => 'Test Event',
        'date' => '2025-12-25',
        'time' => '10:00 - 15:00',
        'location' => 'Test Location',
        'last_registration_at' => '2025-12-20',
        'description' => 'Test Description',
    ]);

    $mail = new EventCompetitionNotificationMail($event, 'event');

    $mail->assertSeeInHTML('Test Event');
    $mail->assertSeeInHTML('2025-12-25');
    $mail->assertSeeInHTML('10:00 - 15:00');
    $mail->assertSeeInHTML('Test Location');
    $mail->assertSeeInHTML('2025-12-20');
    $mail->assertSeeInHTML('Test Description');
    $mail->assertSeeInHTML('Ny funktionärsanmälan är öppen!');
});

test('notification email renders correctly for competition', function () {
    $competition = Competition::factory()->create([
        'name' => 'Test Competition',
        'date' => '2025-12-25',
        'time' => '08:00 - 16:00',
        'location' => 'Competition Location',
        'last_registration_at' => '2025-12-18',
        'description' => 'Competition Description',
        'events' => json_encode(['ksl' => true, 'kbp' => true, 'sl' => false, 'bp' => false]),
    ]);

    $mail = new EventCompetitionNotificationMail($competition, 'competition');

    $mail->assertSeeInHTML('Test Competition');
    $mail->assertSeeInHTML('2025-12-25');
    $mail->assertSeeInHTML('08:00 - 16:00');
    $mail->assertSeeInHTML('Competition Location');
    $mail->assertSeeInHTML('2025-12-18');
    $mail->assertSeeInHTML('Competition Description');
    $mail->assertSeeInHTML('Ny tävlingsanmälan är öppen!');
    $mail->assertSeeInHTML('KSL');
    $mail->assertSeeInHTML('KBP');
});

test('only granted users receive notification emails', function () {
    Mail::fake();

    $admin = loginAdmin();
    $event = Event::factory()->create();

    // Create granted and non-granted users
    User::factory()->count(2)->create();
    User::factory()->count(3)->ungranted()->create();

    $response = $this->post("/admin/events/{$event->id}/send-notification", [
        'confirmed' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'recipients_count' => 2,
    ]);

    // Verify only 2 emails were sent (to granted users)
    Mail::assertSent(EventCompetitionNotificationMail::class, 2);
});

test('non-admin cannot send notifications', function () {
    $user = login();
    $event = Event::factory()->create();

    $response = $this->post("/admin/events/{$event->id}/send-notification", [
        'confirmed' => true,
    ]);

    $response->assertStatus(403);
});
