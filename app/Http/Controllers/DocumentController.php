<?php

namespace App\Http\Controllers;

use App\Document;
use App\DocumentFolder;
use Illuminate\Http\Request;

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
        ]);
    }
}
