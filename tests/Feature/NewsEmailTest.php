<?php

use App\Mail\NewsMail;
use App\Models\NewsItem;
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

test('a news email can be sent to all members', function () {
    Mail::fake();

    $item = NewsItem::create([
        'title' => 'Important News',
        'body' => 'This is important content',
    ]);

    // Create some approved members
    $approvedUser1 = \App\Models\User::factory()->create(['granted_by' => 1]);
    $approvedUser2 = \App\Models\User::factory()->create(['granted_by' => 2]);
    $approvedUser3 = \App\Models\User::factory()->create(['granted_by' => 1]);

    // Create an unapproved user (should not receive email)
    $unapprovedUser = \App\Models\User::factory()->create(['granted_by' => 0]);

    loginAdmin();

    $response = $this->post('/admin/news/email/send-all', [
        'body' => $item->body,
        'title' => $item->title,
    ])->assertOk();

    $response->assertJson([
        'message' => 'Email sent successfully',
        'count' => 4, // 3 approved users + 1 admin
    ]);

    Mail::assertSent(NewsMail::class, function ($mail) use ($approvedUser1) {
        return $mail->hasTo($approvedUser1->email);
    });

    Mail::assertSent(NewsMail::class, function ($mail) use ($approvedUser2) {
        return $mail->hasTo($approvedUser2->email);
    });

    Mail::assertSent(NewsMail::class, function ($mail) use ($approvedUser3) {
        return $mail->hasTo($approvedUser3->email);
    });

    Mail::assertNotSent(NewsMail::class, function ($mail) use ($unapprovedUser) {
        return $mail->hasTo($unapprovedUser->email);
    });
});

test('only administrators can send news emails to all members', function () {
    Mail::fake();

    $item = NewsItem::create([
        'title' => 'Important News',
        'body' => 'This is important content',
    ]);

    $this->post('/admin/news/email/send-all', [
        'body' => $item->body,
        'title' => $item->title,
    ])->assertRedirect();

    Mail::assertNotSent(NewsMail::class);

    login();

    $this->post('/admin/news/email/send-all', [
        'body' => $item->body,
        'title' => $item->title,
    ])->assertUnauthorized();

    Mail::assertNotSent(NewsMail::class);
});
