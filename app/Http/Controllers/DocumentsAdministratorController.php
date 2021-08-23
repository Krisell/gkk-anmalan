<?php

namespace App\Http\Controllers;

use App\Document;
use App\DocumentFolder;
use App\Firebase;
use Illuminate\Http\Request;

class DocumentsAdministratorController extends Controller
{
    public function index()
    {
        return view('admin.documents', [
            'folders' => DocumentFolder::with('documents')->get(),
            'jwt' => Firebase::makeAdminJwt(),
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
