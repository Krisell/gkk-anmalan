<?php

use App\Models\DocumentFolder;

test('a non admin cant create a folder', function () {
    login();

    $this->post('/admin/document-folders', [
        'name' => 'MY DOCUMENT',
        'order' => 1,
    ])->assertUnauthorized();

    $this->assertCount(0, DocumentFolder::all());
});

test('an admin can create a folder', function () {
    loginAdmin();

    $this->post('/admin/document-folders', [
        'name' => 'MY DOCUMENT',
        'order' => 1,
    ])->assertCreated();

    $this->assertCount(1, DocumentFolder::all());
});

test('an admin can update a folder', function () {
    loginAdmin();

    $folder = DocumentFolder::factory()->create();

    $this->post("/admin/document-folders/{$folder->id}", [
        'name' => 'NEW NAME',
        'order' => 27,
    ])->assertOk();

    $this->assertDatabaseHas('document_folders', [
        'name' => 'NEW NAME',
        'order' => 27,
    ]);
});

test('an admin can delete a document', function () {
    loginAdmin();

    $folder = DocumentFolder::factory()->create();

    $this->withoutExceptionHandling();
    $this->delete("/admin/document-folders/{$folder->id}")->assertOk();

    $this->assertCount(0, DocumentFolder::all());
});
