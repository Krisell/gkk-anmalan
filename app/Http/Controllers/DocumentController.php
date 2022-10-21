<?php

namespace App\Http\Controllers;

use App\DocumentFolder;

class DocumentController extends Controller
{
    public function index()
    {
        $query = DocumentFolder::query();

        if (! collect(['admin', 'superadmin'])->contains(auth()->user()->role)) {
            $query->whereOnlyAdministrators(0);
        }

        return view('member-documents', [
            'folders' => $query->with('documents')->get(),
            'view' => 'member-documents',
            'user' => auth()->user(),
        ]);
    }
}
