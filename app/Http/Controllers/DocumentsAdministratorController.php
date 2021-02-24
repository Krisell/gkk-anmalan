<?php

namespace App\Http\Controllers;

use App\Document;
use App\Firebase;
use Illuminate\Http\Request;

class DocumentsAdministratorController extends Controller
{
    public function index()
    {
        return view('admin.documents', [
            'documents' => Document::all(),
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'url' => 'required',
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
