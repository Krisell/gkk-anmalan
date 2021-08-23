<?php

namespace Tests\Feature;

use App\Document;
use App\DocumentFolder;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_a_not_signed_in_user_cant_access_documents()
    {
        $this->get('/documents')->assertRedirect();
    }

    /** @test */
    public function a_signed_in_user_can_see_documents()
    {
        Auth::login(User::factory()->create());

        $this->get('/documents')->assertViewHas([
            'folders' => DocumentFolder::all(),
        ]);
    }

    /** @test */
    public function a_non_admin_cant_upload_a_document()
    {
        Auth::login(User::factory()->create());

        $this->post('/admin/documents', [
            'name' => 'MY DOCUMENT',
            'url' => 'https://my-url.com',
        ])->assertUnauthorized();

        $this->assertCount(0, Document::all());
    }

    /** @test */
    public function an_admin_can_upload_a_document()
    {
        Auth::login(User::factory()->create(['role' => 'admin']));

        $folder = DocumentFolder::factory()->create();

        $this->post('/admin/documents', [
            'name' => 'MY DOCUMENT',
            'url' => 'https://my-url.com',
            'document_folder_id' => $folder->id,
        ])->assertCreated();

        $this->assertCount(1, Document::all());
    }

    /** @test */
    public function an_admin_can_update_a_document()
    {
        Auth::login(User::factory()->create(['role' => 'admin']));

        $document = Document::factory()->create();

        $this->post("/admin/documents/{$document->id}", [
            'name' => 'NEW NAME',
            'url' => 'https://new-url.com',
        ])->assertOk();

        $this->assertDatabaseHas('documents', [
            'name' => 'NEW NAME',
            'url' => 'https://new-url.com',
        ]);
    }

    /** @test */
    public function an_admin_can_delete_a_document()
    {
        Auth::login(User::factory()->create(['role' => 'admin']));

        $document = Document::factory()->create();

        $this->delete("/admin/documents/{$document->id}")->assertOk();

        $this->assertCount(0, Document::all());
    }
}
