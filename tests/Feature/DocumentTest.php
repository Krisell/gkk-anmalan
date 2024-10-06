<?php

use App\Models\Document;
use App\Models\DocumentFolder;

test('test a not signed in user cant access documents', function () {
    $this->get('/member-documents')->assertRedirect();
});

test('a signed in user can see documents', function () {
    login();

    $this->get('/member-documents')->assertViewHas([
        'folders' => DocumentFolder::all(),
    ]);
});

test('a non admin cant upload a document', function () {
    login();

    $this->post('/admin/documents', [
        'name' => 'MY DOCUMENT',
        'url' => 'https://my-url.com',
    ])->assertUnauthorized();

    $this->assertCount(0, Document::all());
});

test('an admin can upload a document', function () {
    loginAdmin();

    $folder = DocumentFolder::factory()->create();

    $this->post('/admin/documents', [
        'name' => 'MY DOCUMENT',
        'url' => 'https://my-url.com',
        'document_folder_id' => $folder->id,
    ])->assertCreated();

    $this->assertCount(1, Document::all());
});

test('an admin can update a document', function () {
    loginAdmin();

    $document = Document::factory()->create();

    $this->post("/admin/documents/{$document->id}", [
        'name' => 'NEW NAME',
        'url' => 'https://new-url.com',
    ])->assertOk();

    $this->assertDatabaseHas('documents', [
        'name' => 'NEW NAME',
        'url' => 'https://new-url.com',
    ]);
});

test('an admin can delete a document', function () {
    loginAdmin();

    $document = Document::factory()->create();

    $this->delete("/admin/documents/{$document->id}")->assertOk();

    $this->assertCount(0, Document::all());
});
