<?php

use App\Models\SignatureRequest;
use App\Models\SignatureRequestField;
use App\Models\SignatureRequestSigner;
use App\Models\User;
use Illuminate\Support\Facades\URL;

const VALID_SIGNATURE_PNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

test('a non admin cannot access signature request administration', function () {
    login();

    $this->get('/admin/signature-requests')->assertUnauthorized();
    $this->post('/admin/signature-requests', [])->assertUnauthorized();
});

test('an admin can create a signature request', function () {
    loginAdmin();

    $this->post('/admin/signature-requests', [
        'name' => 'Årsmötesprotokoll 2026',
        'pdf_url' => 'https://example.com/protokoll.pdf',
    ])->assertCreated();

    $this->assertDatabaseHas('signature_requests', [
        'name' => 'Årsmötesprotokoll 2026',
        'created_by' => auth()->id(),
    ]);
});

test('an admin can save signers and fields', function () {
    loginAdmin();

    $request = SignatureRequest::factory()->create();
    $user = User::factory()->create();

    $this->post("/admin/signature-requests/{$request->id}", [
        'name' => $request->name,
        'signers' => [$user->id],
        'fields' => [
            ['user_id' => $user->id, 'page_index' => 1, 'x' => 72.5, 'y' => 141.25, 'width' => 150, 'height' => 50],
        ],
    ])->assertOk();

    $signer = SignatureRequestSigner::firstWhere('user_id', $user->id);
    expect($signer->token)->not->toBeNull();

    $this->assertDatabaseHas('signature_request_fields', [
        'signature_request_signer_id' => $signer->id,
        'page_index' => 1,
        'x' => 72.5,
        'y' => 141.25,
    ]);
});

test('a signature request cannot be changed after it has been sent', function () {
    loginAdmin();

    $request = SignatureRequest::factory()->create(['sent_at' => now()]);

    $this->post("/admin/signature-requests/{$request->id}", [
        'name' => 'Nytt namn',
        'signers' => [User::factory()->create()->id],
    ])->assertConflict();
});

test('activating locks the request', function () {
    loginAdmin();

    $request = SignatureRequest::factory()->create();
    $signer = SignatureRequestSigner::factory()->create(['signature_request_id' => $request->id]);
    SignatureRequestField::factory()->create([
        'signature_request_id' => $request->id,
        'signature_request_signer_id' => $signer->id,
    ]);

    $this->post("/admin/signature-requests/{$request->id}/activate")->assertOk();

    expect($request->fresh()->sent_at)->not->toBeNull();
});

test('activating requires signers and fields', function () {
    loginAdmin();

    $request = SignatureRequest::factory()->create();

    $this->post("/admin/signature-requests/{$request->id}/activate")->assertUnprocessable();

    expect($request->fresh()->sent_at)->toBeNull();
});

test('the admin detail page provides a signing link per unsigned signer', function () {
    loginAdmin();

    $signer = SignatureRequestSigner::factory()->create();
    $signedSigner = SignatureRequestSigner::factory()->create([
        'signature_request_id' => $signer->signature_request_id,
        'signed_at' => now(),
    ]);

    $response = $this->get("/admin/signature-requests/{$signer->signature_request_id}")->assertOk();

    $signUrls = $response->viewData('signUrls');
    expect($signUrls)->toHaveKey($signer->id);
    expect($signUrls)->not->toHaveKey($signedSigner->id);

    $this->get($signUrls[$signer->id])->assertOk();
});

test('the signing page requires a valid signed url', function () {
    $signer = SignatureRequestSigner::factory()->create();

    $this->get("/sign/{$signer->token}")->assertForbidden();

    $url = URL::temporarySignedRoute('sign.show', now()->addDays(14), ['signer' => $signer->token]);

    $this->get($url)->assertOk()->assertViewHas('pdfUrl');
});

test('an expired signing link is rejected', function () {
    $signer = SignatureRequestSigner::factory()->create();

    $url = URL::temporarySignedRoute('sign.show', now()->addDays(14), ['signer' => $signer->token]);

    $this->travel(15)->days();

    $this->get($url)->assertForbidden();
});

test('a signer can sign via a valid link', function () {
    $signer = SignatureRequestSigner::factory()->create();

    $url = URL::temporarySignedRoute('sign.store', now()->addHours(2), ['signer' => $signer->token]);

    $this->post($url, [
        'signature_png' => VALID_SIGNATURE_PNG,
        'signature_type' => 'drawn',
    ])->assertOk();

    $signer->refresh();
    expect($signer->signed_at)->not->toBeNull();
    expect($signer->signature_png)->toBe(VALID_SIGNATURE_PNG);
});

test('a signer cannot sign twice', function () {
    $signer = SignatureRequestSigner::factory()->create(['signed_at' => now()]);

    $url = URL::temporarySignedRoute('sign.store', now()->addHours(2), ['signer' => $signer->token]);

    $this->post($url, [
        'signature_png' => VALID_SIGNATURE_PNG,
        'signature_type' => 'drawn',
    ])->assertConflict();
});

test('an invalid signature image is rejected', function () {
    $signer = SignatureRequestSigner::factory()->create();

    $url = URL::temporarySignedRoute('sign.store', now()->addHours(2), ['signer' => $signer->token]);

    $this->post($url, [
        'signature_png' => 'data:image/png;base64,'.\base64_encode('not a png'),
        'signature_type' => 'drawn',
    ])->assertUnprocessable();

    expect($signer->fresh()->signed_at)->toBeNull();
});

test('an admin can store the completed pdf url', function () {
    loginAdmin();

    $request = SignatureRequest::factory()->create();

    $this->post("/admin/signature-requests/{$request->id}/complete", [
        'completed_pdf_url' => 'https://example.com/signed.pdf',
    ])->assertOk();

    expect($request->fresh()->completed_pdf_url)->toBe('https://example.com/signed.pdf');
});

test('deleting a signature request cascades to signers and fields', function () {
    loginAdmin();

    $request = SignatureRequest::factory()->create();
    $signer = SignatureRequestSigner::factory()->create(['signature_request_id' => $request->id]);
    SignatureRequestField::factory()->create([
        'signature_request_id' => $request->id,
        'signature_request_signer_id' => $signer->id,
    ]);

    $this->delete("/admin/signature-requests/{$request->id}")->assertOk();

    expect(SignatureRequest::count())->toBe(0);
    expect(SignatureRequestSigner::count())->toBe(0);
    expect(SignatureRequestField::count())->toBe(0);
});
