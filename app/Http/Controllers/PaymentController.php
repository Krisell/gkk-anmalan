<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function update(Request $request, Payment $payment)
    {
        $payment->update(['state' => request('state')]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => "updated payment {$payment->id}",
        ]);

        ActivityLog::create([
            'performed_by' => $request->user()->id,
            'action' => 'payment-update',
            'data' => json_encode([
                'payment_id' => $payment->id,
                'state' => request('state'),
            ]),
        ]);

        return $payment;
    }
}
