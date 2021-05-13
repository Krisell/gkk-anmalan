<?php

namespace App\Http\Controllers;

use App\Document;
use App\DocumentFolder;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return view('documents', [
            'documents' => Document::orderBy('name')->get(),
            'folders' => DocumentFolder::get(),
        ]);
    }
}
