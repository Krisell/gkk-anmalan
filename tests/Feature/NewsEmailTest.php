<?php

use App\Mail\NewsMail;
use App\Models\EmailSend;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('the news email page can be accessed by administrators only', function () {
    $item = NewsItem::create([
        'title' => 'ABC',
        'body' => 'DEF',
    ]);

    $this->get("/admin/news/email/{$item->id}")->assertRedirect();

    login();

    $this->get("/admin/news/email/{$item->id}")->assertUnauthorized();

    loginAdmin();

    $this->get("/admin/news/email/{$item->id}")->assertViewHas([
        'item' => $item,
    ]);
});

test('a news email can be previewed in the browser', function () {
    $this->post('/admin/news/email/preview', [
        'body' => 'ABC',
        'title' => 'CDE',
    ])->assertRedirect();

    login();

    $this->post('/admin/news/email/preview', [
        'body' => 'ABC',
        'title' => 'CDE',
    ])->assertUnauthorized();

    loginAdmin();

    $this->post('/admin/news/email/preview', [
        'body' => 'ABC',
        'title' => 'CDE',
    ])->assertOk();
});

test('a news email can be sent as a test to the signed in user', function () {
    Mail::fake();

    $item = NewsItem::create([
        'title' => 'ABC',
        'body' => 'DEF',
    ]);

    $this->post('/admin/news/email/test', [
        'body' => $item->body,
        'title' => $item->title,
    ])->assertRedirect();

    Mail::assertNotSent(NewsMail::class);

    login();

    $this->post('/admin/news/email/test', [
        'body' => $item->body,
        'title' => $item->title,
    ])->assertUnauthorized();

    Mail::assertNotSent(NewsMail::class);

    $user = loginAdmin();

    $this->post('/admin/news/email/test', [
        'body' => $item->body,
        'title' => $item->title,
    ])->assertOk();

    Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($user->email));
});

test('the recipients endpoint lists active members and shows who has already received the email', function () {
    $item = NewsItem::create(['title' => 'T', 'body' => 'B']);

    $active = User::factory()->create(['first_name' => 'Alice', 'last_name' => 'A']);
    $alreadySent = User::factory()->create(['first_name' => 'Bob', 'last_name' => 'B']);
    User::factory()->inactivated()->create(['first_name' => 'Carol', 'last_name' => 'C']);

    EmailSend::create([
        'subject_type' => NewsItem::class,
        'subject_id' => $item->id,
        'user_id' => $alreadySent->id,
        'email' => $alreadySent->email,
        'mailable' => NewsMail::class,
        'sent_at' => now(),
    ]);

    $this->get("/admin/news/email/{$item->id}/recipients")->assertRedirect();

    login();
    $this->get("/admin/news/email/{$item->id}/recipients")->assertUnauthorized();

    loginAdmin();
    $response = $this->get("/admin/news/email/{$item->id}/recipients")->assertOk();

    $emails = collect($response->json('recipients'))->pluck('email');
    expect($emails)->toContain($active->email)
        ->and($emails)->toContain($alreadySent->email)
        ->and($emails)->not->toContain('carol');

    $sentEntry = collect($response->json('recipients'))->firstWhere('id', $alreadySent->id);
    $unsentEntry = collect($response->json('recipients'))->firstWhere('id', $active->id);
    expect($sentEntry['sent_at'])->not->toBeNull()
        ->and($unsentEntry['sent_at'])->toBeNull();
});

test('sending a news email to all active members logs each send and skips inactive members', function () {
    Mail::fake();

    $item = NewsItem::create(['title' => 'T', 'body' => 'B']);

    $active1 = User::factory()->create();
    $active2 = User::factory()->create();
    $inactive = User::factory()->inactivated()->create();

    $this->post("/admin/news/email/{$item->id}/send", [
        'title' => $item->title,
        'body' => $item->body,
    ])->assertRedirect();

    Mail::assertNothingSent();

    login();
    $this->post("/admin/news/email/{$item->id}/send", [
        'title' => $item->title,
        'body' => $item->body,
    ])->assertUnauthorized();

    Mail::assertNothingSent();

    $admin = loginAdmin();
    $response = $this->post("/admin/news/email/{$item->id}/send", [
        'title' => $item->title,
        'body' => $item->body,
    ])->assertOk();

    Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($active1->email));
    Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($active2->email));
    Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($admin->email));
    Mail::assertNotSent(NewsMail::class, fn ($mail) => $mail->hasTo($inactive->email));

    $sentCount = EmailSend::where('subject_type', NewsItem::class)->where('subject_id', $item->id)->count();
    expect($response->json('count'))->toBe($sentCount)
        ->and($sentCount)->toBeGreaterThanOrEqual(3);
});

test('a news email can be sent to a specific subset of users', function () {
    Mail::fake();

    $item = NewsItem::create(['title' => 'T', 'body' => 'B']);
    $a = User::factory()->create();
    $b = User::factory()->create();
    User::factory()->create();

    loginAdmin();

    $response = $this->post("/admin/news/email/{$item->id}/send", [
        'title' => $item->title,
        'body' => $item->body,
        'user_ids' => [$a->id, $b->id],
    ])->assertOk();

    expect($response->json('count'))->toBe(2);

    Mail::assertSent(NewsMail::class, 2);
    Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($a->email));
    Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($b->email));
});
