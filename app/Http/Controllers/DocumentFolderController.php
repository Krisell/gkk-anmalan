<?php

namespace App\Http\Controllers;

use App\DocumentFolder;
use Illuminate\Http\Request;

class DocumentFolderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'order' => 'required',
        ]);

        return DocumentFolder::create($data);
    }

    public function update(DocumentFolder $folder, Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'order' => 'required',
        ]);

        $folder->update($data);
    }

    public function destroy(DocumentFolder $folder)
    {
        $folder->delete();
    }
}
