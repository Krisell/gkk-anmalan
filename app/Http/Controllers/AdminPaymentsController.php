<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class AdminPaymentsController extends Controller
{
    public function index()
    {
        return view('admin.payments', [
            'payments' => Payment::with([
                'user:id,first_name,last_name,email',
                'competition:id,name,date',
            ])->where('state', '!=', 'PAID')->get(),
            'user' => auth()->user(),
            'view' => 'payments',
        ]);
    }
}
