<?php

namespace App\Http\Controllers;

use App\Firebase;
use App\Models\DocumentFolder;
use App\Models\SignatureRequest;
use App\Models\SignatureRequestSigner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class SignatureRequestAdminController extends Controller
{
    public function index()
    {
        return view('admin.signature-requests', [
            'requests' => SignatureRequest::with(['signers' => function ($query) {
                $query->select(['id', 'signature_request_id', 'user_id', 'signed_at'])->with('user:id,first_name,last_name');
            }])->latest()->get(),
            'users' => User::whereNull('inactivated_at')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email']),
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function show(SignatureRequest $signatureRequest)
    {
        $signatureRequest->load(['signers.user', 'fields']);

        // Signing links are minted fresh on every page load so an admin can
        // always copy a valid one and share it any way they like.
        $signUrls = $signatureRequest->signers
            ->whereNull('signed_at')
            ->mapWithKeys(fn ($signer) => [$signer->id => $this->makeSignUrl($signer)]);

        return view('admin.signature-request', [
            'request' => $signatureRequest,
            'signUrls' => $signUrls,
            'users' => User::whereNull('inactivated_at')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email']),
            'folders' => DocumentFolder::orderBy('order')->get(['id', 'name']),
            'jwt' => Firebase::makeAdminJwt(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'pdf_url' => 'required|url',
        ]);

        $signatureRequest = SignatureRequest::create([
            ...$data,
            'created_by' => auth()->id(),
        ]);

        return $signatureRequest;
    }

    public function update(SignatureRequest $signatureRequest, Request $request)
    {
        abort_if($signatureRequest->sent_at !== null, 409, 'Förfrågan är redan utskickad och kan inte ändras.');

        $data = $request->validate([
            'name' => 'required|string',
            'signers' => 'required|array|min:1',
            'signers.*' => 'integer|exists:users,id|distinct',
            'fields' => 'array',
            'fields.*.user_id' => 'required|integer',
            'fields.*.page_index' => 'required|integer|min:0',
            'fields.*.x' => 'required|numeric',
            'fields.*.y' => 'required|numeric',
            'fields.*.width' => 'required|numeric|min:1',
            'fields.*.height' => 'required|numeric|min:1',
        ]);

        collect($data['fields'] ?? [])->each(function ($field) use ($data) {
            abort_unless(\in_array($field['user_id'], $data['signers']), 422, 'Fält tilldelat användare som inte är signerare.');
        });

        DB::transaction(function () use ($signatureRequest, $data) {
            $signatureRequest->update(['name' => $data['name']]);

            $signatureRequest->signers()->whereNotIn('user_id', $data['signers'])->get()->each->delete();

            $signersByUserId = collect($data['signers'])->mapWithKeys(function ($userId) use ($signatureRequest) {
                return [$userId => $signatureRequest->signers()->firstOrCreate(['user_id' => $userId])];
            });

            $signatureRequest->fields()->delete();

            foreach ($data['fields'] ?? [] as $field) {
                $signatureRequest->fields()->create([
                    'signature_request_signer_id' => $signersByUserId[$field['user_id']]->id,
                    'page_index' => $field['page_index'],
                    'x' => $field['x'],
                    'y' => $field['y'],
                    'width' => $field['width'],
                    'height' => $field['height'],
                ]);
            }
        });

        return $signatureRequest->load(['signers.user', 'fields']);
    }

    public function destroy(SignatureRequest $signatureRequest)
    {
        $signatureRequest->delete();
    }

    public function activate(SignatureRequest $signatureRequest)
    {
        abort_if($signatureRequest->signers()->count() === 0, 422, 'Det finns inga signerare.');
        abort_if($signatureRequest->fields()->count() === 0, 422, 'Det finns inga signaturfält.');

        $signatureRequest->update(['sent_at' => now()]);
    }

    public function complete(SignatureRequest $signatureRequest, Request $request)
    {
        $data = $request->validate([
            'completed_pdf_url' => 'required|url',
        ]);

        $signatureRequest->update($data);
    }

    private function makeSignUrl(SignatureRequestSigner $signer)
    {
        return URL::temporarySignedRoute('sign.show', now()->addDays(14), ['signer' => $signer->token]);
    }
}
