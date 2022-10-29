<?php

use App\NewsItem;

test('an admin can create a news item', function () {
    loginAdmin();

    $this->post('/admin/news', [
        'title' => 'News Item Title',
        'body' => '<h1>News Item Body</h1>',
        'published_at_date' => '2021-03-03',
    ])->assertStatus(201);

    $this->assertDatabaseHas('news_items', [
        'title' => 'News Item Title',
        'body' => '<h1>News Item Body</h1>',
        'published_at_date' => '2021-03-03',
    ]);
});

test('the published at date defaults to todays date', function () {
    loginAdmin();

    $this->post('/admin/news', [
        'title' => 'News Item Title',
        'body' => '<h1>News Item Body</h1>',
    ])->assertStatus(201);

    $this->assertDatabaseHas('news_items', [
        'title' => 'News Item Title',
        'body' => '<h1>News Item Body</h1>',
        'published_at_date' => now()->format('Y-m-d'),
    ]);
});

test('a non admin cant create a news item', function () {
    login();

    $this->post('/admin/news', [
        'title' => 'News Item Title',
        'body' => '<h1>News Item Body</h1>',
        'published_at_date' => '2021-03-03',
    ])->assertStatus(401);

    $this->assertCount(0, NewsItem::all());
});

test('a title and body must be provided but not published at date', function () {
    loginAdmin();

    $this->json('post', '/admin/news', [
        'title' => 'News Item Title',
        'body' => '',
    ])->assertStatus(422);

    $this->json('post', '/admin/news', [
        'title' => '',
        'body' => 'News Item Body',
    ])->assertStatus(422);

    $this->assertCount(0, NewsItem::all());
});

test('a news item can be updated', function () {
    $news = NewsItem::create([
        'title' => 'Before',
        'body' => 'Before the edit',
    ]);

    loginAdmin();

    $this->json('post', "/admin/news/{$news->id}", [
        'title' => 'After',
        'body' => 'After the edit',
        'published_at_date' => '',
    ])->assertStatus(200);

    $this->assertEquals('After', $news->fresh()->title);
    $this->assertEquals('After the edit', $news->fresh()->body);
});

test('both title and body are required when updating a news item', function () {
    $news = NewsItem::create([
        'title' => 'Before',
        'body' => 'Before the edit',
    ]);

    loginAdmin();

    $this->json('post', "/admin/news/{$news->id}", [
        'title' => 'After',
    ])->assertStatus(422);

    $this->json('post', "/admin/news/{$news->id}", [
        'body' => 'After the edit',
    ])->assertStatus(422);

    $this->json('post', "/admin/news/{$news->id}", [
        'body' => 'After the edit',
        'title' => '',
    ])->assertStatus(422);

    $this->assertEquals('Before', $news->fresh()->title);
    $this->assertEquals('Before the edit', $news->fresh()->body);
});

test('a news item can be deleted by the admin', function () {
    $news = NewsItem::create([
        'title' => 'Before',
        'body' => 'Before the edit',
    ]);

    loginAdmin();

    $this->assertDatabaseHas('news_items', [
        'title' => 'Before',
        'deleted_at' => null,
    ]);

    $this->json('delete', "/admin/news/{$news->id}")->assertStatus(200);

    $this->assertDatabaseMissing('news_items', [
        'title' => 'Before',
        'deleted_at' => null,
    ]);
});
