<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Fortnox;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    public function show(Fortnox $fortnox, Payment $payment)
    {
        $this->authorize('show', $payment);

        $token = $fortnox->token();

        if (! $token) {
            $dummyPath = storage_path('app/dummy-invoice.pdf');

            if (\file_exists($dummyPath)) {
                return response(\file_get_contents($dummyPath), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="invoice.pdf"',
                ]);
            }

            return;
        }

        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}/preview");

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
}
