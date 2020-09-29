<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return view('documents', [
            'documents' => [
                ['name' => 'Protokoll 1, 2020-03-08', 'url' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/protokoll%2F2020%2FStyrelseprotokoll%201.%2020200308.pdf?alt=media&token=e6b803ee-5c82-42bf-8f0c-726a4c871b2b'],
                ['name' => 'Protokoll 2, 2020-03-15', 'url' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/protokoll%2F2020%2FStyrelseprotokoll%202.%2020200315.pdf?alt=media&token=b13e7d66-775e-40b7-bab6-d910ed840fec'],
                ['name' => 'Protokoll 3, 2020-04-19', 'url' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/protokoll%2F2020%2FStyrelseprotokoll%203.%2020200419.pdf?alt=media&token=223c5a71-55b6-477d-bc4e-b78d7353b7d9'],
                ['name' => 'Protokoll 4, 2020-06-08', 'url' => 'https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/protokoll%2F2020%2FStyrelseprotokoll%204%2020200608.pdf?alt=media&token=bb4bf750-8aa5-417b-8576-9425ba2c0f54'],
            ],
        ]);
    }
}
