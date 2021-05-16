<?php

namespace Tests\Feature;

use App\Mail\NewsMail;
use App\NewsItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_news_email_page_can_be_accessed_by_administrators_only()
    {
        $item = NewsItem::create([
            'title' => 'ABC',
            'body' => 'DEF',
        ]);

        $this->get("/admin/news/email/{$item->id}")->assertRedirect();

        $user = User::factory()->create();
        auth()->login($user);

        $this->get("/admin/news/email/{$item->id}")->assertUnauthorized();

        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->get("/admin/news/email/{$item->id}")->assertViewHas([
            'item' => $item,
        ]);
    }

    /** @test */
    public function a_news_email_can_be_previewed_in_the_browser()
    {
        $this->post('/admin/news/email/preview', [
            'body' => 'ABC',
            'title' => 'CDE',
        ])->assertRedirect();

        $user = User::factory()->create();
        auth()->login($user);

        $this->post('/admin/news/email/preview', [
            'body' => 'ABC',
            'title' => 'CDE',
        ])->assertUnauthorized();

        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->post('/admin/news/email/preview', [
            'body' => 'ABC',
            'title' => 'CDE',
        ])->assertOk();
    }

    /** @test */
    public function a_news_email_can_be_sent_as_a_test_to_the_signed_in_user()
    {
        Mail::fake();

        $item = NewsItem::create([
            'title' => 'ABC',
            'body' => 'DEF',
        ]);

        $data = \json_encode($item);

        $this->post('/admin/news/email/test', [
            'item' => $item,
        ])->assertRedirect();

        Mail::assertNotSent(NewsMail::class);

        $user = User::factory()->create();
        auth()->login($user);

        $this->post('/admin/news/email/test', [
            'item' => $item,
        ])->assertUnauthorized();

        Mail::assertNotSent(NewsMail::class);

        $user = User::factory()->create(['role' => 'admin']);
        auth()->login($user);

        $this->post('/admin/news/email/test', [
            'item' => $item,
        ])->assertOk();

        Mail::assertSent(NewsMail::class, fn ($mail) => $mail->hasTo($user->email));
    }
}
