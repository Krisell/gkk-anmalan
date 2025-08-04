<?php

use App\Models\Payment;
use App\Models\User;
use App\Services\Fortnox;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->fortnoxMock = Mockery::mock(Fortnox::class);
    $this->app->instance(Fortnox::class, $this->fortnoxMock);
});

test('command fails when fortnox integration is not activated', function () {
    $this->fortnoxMock->shouldReceive('token')->once()->andReturn(null);

    $this->artisan('fortnox:create-invoices')
        ->expectsOutput('Fortnox integration not activated.')
        ->assertExitCode(0);
});

test('command creates invoices for membership payments', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create([
        'fortnox_customer_id' => 'CUST123',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 500,
        'sek_discount' => 0,
        'modifier' => null,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025']], 200),
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025-DISCOUNT' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025-DISCOUNT']], 200),
        'https://api.fortnox.se/3/invoices' => Http::response(['Invoice' => ['DocumentNumber' => 'INV001']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'MEMBERSHIP', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->expectsOutput('Artikeln GKK-MEMBERSHIP-2025 finns i Fortnox.')
        ->expectsOutput('Artikeln GKK-MEMBERSHIP-2025-DISCOUNT finns i Fortnox.')
        ->expectsOutput('Faktura INV001 skapad för John Doe.')
        ->assertExitCode(0);

    $payment->refresh();
    expect($payment->fortnox_invoice_document_number)->toBe('INV001');
    expect($payment->fortnox_invoice_created_at)->not->toBeNull();
});

test('command creates invoices for ssflicense payments', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create([
        'fortnox_customer_id' => 'CUST456',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'SSFLICENSE',
        'year' => 2025,
        'sek_amount' => 300,
        'sek_discount' => 50,
        'modifier' => 'AGE_DISCOUNT',
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-SSFLICENSE-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-SSFLICENSE-2025']], 200),
        'https://api.fortnox.se/3/articles/GKK-SSFLICENSE-2025-DISCOUNT' => Http::response(['Article' => ['ArticleNumber' => 'GKK-SSFLICENSE-2025-DISCOUNT']], 200),
        'https://api.fortnox.se/3/invoices' => Http::response(['Invoice' => ['DocumentNumber' => 'INV002']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'SSFLICENSE', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.fortnox.se/3/invoices' &&
               $request->hasHeader('Authorization', 'Bearer test-token') &&
               $request['Invoice']['InvoiceRows'][0]['ArticleNumber'] === 'GKK-SSFLICENSE-2025-DISCOUNT' &&
               $request['Invoice']['InvoiceRows'][0]['Price'] === 300 &&
               $request['Invoice']['InvoiceRows'][0]['Discount'] === 50 &&
               ! isset($request['Invoice']['InvoiceRows'][0]['AccountNumber']);
    });
});

test('command creates invoices for competition payments', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create([
        'fortnox_customer_id' => 'CUST789',
        'first_name' => 'Bob',
        'last_name' => 'Johnson',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'COMPETITION',
        'year' => 2025,
        'sek_amount' => 200,
        'sek_discount' => 0,
        'modifier' => null,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-COMPETITION-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-COMPETITION-2025']], 200),
        'https://api.fortnox.se/3/invoices' => Http::response(['Invoice' => ['DocumentNumber' => 'INV003']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'COMPETITION', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);

    $payment->refresh();
    expect($payment->fortnox_invoice_document_number)->toBe('INV003');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.fortnox.se/3/invoices' &&
               $request->hasHeader('Authorization', 'Bearer test-token') &&
               $request['Invoice']['InvoiceRows'][0]['ArticleNumber'] === 'GKK-COMPETITION-2025' &&
               $request['Invoice']['InvoiceRows'][0]['AccountNumber'] === 3410 &&
               $request['Invoice']['InvoiceRows'][0]['Price'] === 200;
    });
});

test('command creates invoices for other payments', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create([
        'fortnox_customer_id' => 'CUST999',
        'first_name' => 'Alice',
        'last_name' => 'Brown',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'OTHER',
        'year' => 2025,
        'sek_amount' => 150,
        'sek_discount' => 0,
        'modifier' => null,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-OTHER-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-OTHER-2025']], 200),
        'https://api.fortnox.se/3/invoices' => Http::response(['Invoice' => ['DocumentNumber' => 'INV004']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'OTHER', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);

    $payment->refresh();
    expect($payment->fortnox_invoice_document_number)->toBe('INV004');
});

test('command skips payments that already have invoice numbers', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create(['fortnox_customer_id' => 'CUST123']);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'fortnox_invoice_document_number' => 'EXISTING123',
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025']], 200),
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025-DISCOUNT' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025-DISCOUNT']], 200),
    ]);

    Http::preventingStrayRequests();

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'MEMBERSHIP', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('0 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);
});

test('command exits early when user cancels confirmation', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create(['fortnox_customer_id' => 'CUST123']);

    Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025']], 200),
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025-DISCOUNT' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025-DISCOUNT']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'MEMBERSHIP', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'no')
        ->assertExitCode(0);

    Http::assertNotSent(function ($request) {
        return $request->url() === 'https://api.fortnox.se/3/invoices';
    });
});

test('command creates missing articles when confirmed', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create([
        'fortnox_customer_id' => 'CUST123',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 500,
        'sek_discount' => 0,
        'modifier' => null,
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025' => Http::response([], 404),
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025-DISCOUNT' => Http::response([], 404),
        'https://api.fortnox.se/3/articles' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025']], 201),
        'https://api.fortnox.se/3/invoices' => Http::response(['Invoice' => ['DocumentNumber' => 'INV001']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'MEMBERSHIP', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('Artikeln GKK-MEMBERSHIP-2025 finns inte i Fortnox. Vill du skapa den?', 'yes')
        ->expectsConfirmation('Artikeln GKK-MEMBERSHIP-2025-DISCOUNT finns inte i Fortnox. Vill du skapa den?', 'yes')
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.fortnox.se/3/articles' &&
               $request->hasHeader('Authorization', 'Bearer test-token') &&
               $request['Article']['ArticleNumber'] === 'GKK-MEMBERSHIP-2025' &&
               $request['Article']['Description'] === 'Medlemsavgift 2025';
    });
});

test('command handles discount modifiers correctly', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $user = User::factory()->create([
        'fortnox_customer_id' => 'CUST123',
        'first_name' => 'Young',
        'last_name' => 'Member',
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'MEMBERSHIP',
        'year' => 2025,
        'sek_amount' => 300,
        'sek_discount' => 100,
        'modifier' => 'YOUTH',
        'fortnox_invoice_document_number' => null,
        'state' => null,
    ]);

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025']], 200),
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025-DISCOUNT' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025-DISCOUNT']], 200),
        'https://api.fortnox.se/3/invoices' => Http::response(['Invoice' => ['DocumentNumber' => 'INV005']], 200),
    ]);

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'MEMBERSHIP', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('1 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.fortnox.se/3/invoices' &&
               $request->hasHeader('Authorization', 'Bearer test-token') &&
               $request['Invoice']['InvoiceRows'][0]['ArticleNumber'] === 'GKK-MEMBERSHIP-2025-DISCOUNT' &&
               $request['Invoice']['InvoiceRows'][0]['Price'] === 300 &&
               $request['Invoice']['InvoiceRows'][0]['Discount'] === 100 &&
               $request['Invoice']['InvoiceRows'][0]['DiscountType'] === 'AMOUNT' &&
               ! isset($request['Invoice']['InvoiceRows'][0]['AccountNumber']);
    });
});

test('command processes multiple payments with rate limiting', function () {
    $this->fortnoxMock->shouldReceive('token')->andReturn('test-token');

    $users = User::factory()->count(7)->create();

    foreach ($users as $i => $user) {
        $user->update(['fortnox_customer_id' => 'CUST'.($i + 1)]);

        Payment::factory()->create([
            'user_id' => $user->id,
            'type' => 'MEMBERSHIP',
            'year' => 2025,
            'sek_amount' => 500,
            'sek_discount' => 0,
            'modifier' => null,
            'fortnox_invoice_document_number' => null,
            'state' => null,
        ]);
    }

    Http::fake([
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025']], 200),
        'https://api.fortnox.se/3/articles/GKK-MEMBERSHIP-2025-DISCOUNT' => Http::response(['Article' => ['ArticleNumber' => 'GKK-MEMBERSHIP-2025-DISCOUNT']], 200),
        'https://api.fortnox.se/3/invoices' => Http::sequence()
            ->push(['Invoice' => ['DocumentNumber' => 'INV001']], 200)
            ->push(['Invoice' => ['DocumentNumber' => 'INV002']], 200)
            ->push(['Invoice' => ['DocumentNumber' => 'INV003']], 200)
            ->push(['Invoice' => ['DocumentNumber' => 'INV004']], 200)
            ->push(['Invoice' => ['DocumentNumber' => 'INV005']], 200)
            ->push(['Invoice' => ['DocumentNumber' => 'INV006']], 200)
            ->push(['Invoice' => ['DocumentNumber' => 'INV007']], 200),
    ]);

    $startTime = \time();

    $this->artisan('fortnox:create-invoices')
        ->expectsChoice('Välj vilken avgift som ska skapas som fakturor i Fortnox', 'MEMBERSHIP', ['MEMBERSHIP', 'SSFLICENSE', 'COMPETITION', 'OTHER'])
        ->expectsConfirmation('7 betalningar kommer att skapas som fakturor i Fortnox. Vill du fortsätta?', 'yes')
        ->assertExitCode(0);

    $endTime = \time();

    expect($endTime - $startTime)->toBeGreaterThanOrEqual(2);
    expect(Payment::whereNotNull('fortnox_invoice_document_number')->count())->toBe(7);
});
