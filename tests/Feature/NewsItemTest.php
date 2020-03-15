<?php

namespace Tests\Feature;

use App\User;
use App\NewsItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_create_a_news_item()
    {
        $user = factory(User::class)->create(['role' => 'admin']);

        auth()->login($user);

        $this->post('/admin/news', [
            'title' => 'News Item Title',
            'body' => '<h1>News Item Body</h1>',
        ])->assertStatus(201);

        $this->assertDatabaseHas('news_items', [
            'title' => 'News Item Title',
            'body' => '<h1>News Item Body</h1>',
        ]);
    }

    /** @test */
    function a_non_admin_cant_create_a_news_item()
    {
        $user = factory(User::class)->create();

        auth()->login($user);

        $this->post('/admin/news', [
            'title' => 'News Item Title',
            'body' => '<h1>News Item Body</h1>',
        ])->assertStatus(401);

        $this->assertCount(0, NewsItem::all());
    }

    /** @test */
    function a_title_and_body_must_be_provided()
    {
        $user = factory(User::class)->create(['role' => 'admin']);

        auth()->login($user);

        $this->json('post', '/admin/news', [
            'title' => 'News Item Title',
            'body' => '',
        ])->assertStatus(422);

        $this->json('post', '/admin/news', [
            'title' => '',
            'body' => 'News Item Body',
        ])->assertStatus(422);

        $this->assertCount(0, NewsItem::all());
    }
}
