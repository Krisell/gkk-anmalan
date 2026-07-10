<?php

namespace App\Http\Controllers;

use App\Models\SignatureRequestSigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SignatureController extends Controller
{
    public function show(SignatureRequestSigner $signer)
    {
        $signer->load(['signature_request', 'fields', 'user']);

        return view('sign-document', [
            'signer' => [
                'name' => \trim("{$signer->user->first_name} {$signer->user->last_name}"),
                'signed_at' => $signer->signed_at,
            ],
            'documentName' => $signer->signature_request->name,
            'pdfUrl' => $signer->signature_request->pdf_url,
            'fields' => $signer->fields->map->only(['page_index', 'x', 'y', 'width', 'height'])->values(),
            // The signed middleware validates the POST too, so the form needs its own signed URL.
            'postUrl' => URL::temporarySignedRoute('sign.store', now()->addHours(2), ['signer' => $signer->token]),
        ]);
    }

    public function store(SignatureRequestSigner $signer, Request $request)
    {
        abort_if($signer->signed_at !== null, 409, 'Dokumentet är redan signerat.');

        $data = $request->validate([
            'signature_png' => 'required|string|starts_with:data:image/png;base64,|max:300000',
            'signature_type' => 'required|in:drawn,typed',
        ]);

        $decoded = \base64_decode(\substr($data['signature_png'], \strlen('data:image/png;base64,')), true);

        abort_unless($decoded !== false && \str_starts_with($decoded, "\x89PNG\r\n\x1a\n"), 422, 'Ogiltig signaturbild.');

        $signer->update([
            'signature_png' => $data['signature_png'],
            'signature_type' => $data['signature_type'],
            'signed_at' => now(),
            'ip_address' => $request->ip(),
        ]);
    }
}
