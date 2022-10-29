<?php

use App\Mail\NewsMail;
use App\NewsItem;
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
