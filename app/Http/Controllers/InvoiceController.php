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
            return;
        }

        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}/preview");

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
}
