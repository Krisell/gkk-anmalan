<?php

namespace Tests\Feature;

use App\NewsItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_a_news_item()
    {
        $user = User::factory()->create(['role' => 'admin']);

        auth()->login($user);

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
    }

    /** @test */
    public function the_published_at_date_defaults_to_todays_date()
    {
        $user = User::factory()->create(['role' => 'admin']);

        auth()->login($user);

        $this->post('/admin/news', [
            'title' => 'News Item Title',
            'body' => '<h1>News Item Body</h1>',
        ])->assertStatus(201);

        $this->assertDatabaseHas('news_items', [
            'title' => 'News Item Title',
            'body' => '<h1>News Item Body</h1>',
            'published_at_date' => now()->format('Y-m-d'),
        ]);
    }

    /** @test */
    public function a_non_admin_cant_create_a_news_item()
    {
        $user = User::factory()->create();

        auth()->login($user);

        $this->post('/admin/news', [
            'title' => 'News Item Title',
            'body' => '<h1>News Item Body</h1>',
            'published_at_date' => '2021-03-03',
        ])->assertStatus(401);

        $this->assertCount(0, NewsItem::all());
    }

    /** @test */
    public function a_title_and_body_must_be_provided_but_not_published_at_date()
    {
        $user = User::factory()->create(['role' => 'admin']);

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

    /** @test */
    public function a_news_item_can_be_updated()
    {
        $news = NewsItem::create([
            'title' => 'Before',
            'body' => 'Before the edit',
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->json('post', "/admin/news/{$news->id}", [
            'title' => 'After',
            'body' => 'After the edit',
            'published_at_date' => '',
        ])->assertStatus(200);

        $this->assertEquals('After', $news->fresh()->title);
        $this->assertEquals('After the edit', $news->fresh()->body);
    }

    /** @test */
    public function both_title_and_body_are_required_when_updating_a_news_item()
    {
        $news = NewsItem::create([
            'title' => 'Before',
            'body' => 'Before the edit',
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

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
    }

    /** @test */
    public function a_news_item_can_be_deleted_by_the_admin()
    {
        $news = NewsItem::create([
            'title' => 'Before',
            'body' => 'Before the edit',
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->assertDatabaseHas('news_items', [
            'title' => 'Before',
            'deleted_at' => null,
        ]);

        $this->json('delete', "/admin/news/{$news->id}")->assertStatus(200);

        $this->assertDatabaseMissing('news_items', [
            'title' => 'Before',
            'deleted_at' => null,
        ]);
    }
}
