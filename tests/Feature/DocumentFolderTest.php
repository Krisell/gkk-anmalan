<?php

namespace Tests\Feature;

use App\DocumentFolder;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DocumentFolderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_non_admin_cant_create_a_folder()
    {
        Auth::login(User::factory()->create());

        $this->post('/admin/document-folders', [
            'name' => 'MY DOCUMENT',
            'order' => 1,
        ])->assertUnauthorized();

        $this->assertCount(0, DocumentFolder::all());
    }

    /** @test */
    public function an_admin_can_create_a_folder()
    {
        Auth::login(User::factory()->create(['role' => 'admin']));
        $this->withoutExceptionHandling();

        $this->post('/admin/document-folders', [
            'name' => 'MY DOCUMENT',
            'order' => 1,
        ])->assertCreated();

        $this->assertCount(1, DocumentFolder::all());
    }

    /** @test */
    public function an_admin_can_update_a_folder()
    {
        Auth::login(User::factory()->create(['role' => 'admin']));

        $folder = DocumentFolder::factory()->create();

        $this->post("/admin/document-folders/{$folder->id}", [
            'name' => 'NEW NAME',
            'order' => 27,
        ])->assertOk();

        $this->assertDatabaseHas('document_folders', [
            'name' => 'NEW NAME',
            'order' => 27,
        ]);
    }

    /** @test */
    public function an_admin_can_delete_a_document()
    {
        Auth::login(User::factory()->create(['role' => 'admin']));

        $folder = DocumentFolder::factory()->create();

        $this->withoutExceptionHandling();
        $this->delete("/admin/document-folders/{$folder->id}")->assertOk();

        $this->assertCount(0, DocumentFolder::all());
    }
}
