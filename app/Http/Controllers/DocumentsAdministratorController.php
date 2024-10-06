<?php

namespace App\Http\Controllers;

use App\Firebase;
use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;

class DocumentsAdministratorController extends Controller
{
    public function index()
    {
        return view('admin.documents', [
            'folders' => DocumentFolder::with('documents')->get(),
            'jwt' => Firebase::makeAdminJwt(),
            'view' => 'member-documents',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'url' => 'required',
            'document_folder_id' => 'required',
        ]);

        return Document::create($data);
    }

    public function destroy(Document $document)
    {
        $document->delete();
    }

    public function update(Document $document, Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'url' => 'required',
        ]);

        $document->update($data);
    }
}
