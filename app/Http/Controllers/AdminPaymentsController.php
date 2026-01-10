<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentsController extends Controller
{
    public function index()
    {
        $query = Payment::with([
            'user:id,first_name,last_name,email',
            'competition:id,name,date',
        ]);

        // Only exclude paid payments if include_paid is not requested
        if (! request()->has('include_paid')) {
            $query->where(function ($q) {
                $q->whereNull('state')->orWhere('state', '!=', 'PAID');
            });
        }

        $payments = $query->get();

        // If this is an AJAX request for including paid payments, return JSON
        if (request()->has('include_paid') && request()->ajax()) {
            $payments = $payments->filter(function ($payment) {
                return $payment->user !== null;
            })->values();

            return response()->json(['payments' => $payments]);
        }

        return view('admin.payments', [
            'payments' => $payments,
            'user' => auth()->user(),
            'view' => 'payments',
        ]);
    }
}
