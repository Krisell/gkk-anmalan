<?php

namespace App\Http\Controllers;

use App\Mail\AccountCreatedByAdminWelcome;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminCreateAccountsController extends Controller
{
    public function store()
    {
        $newAccounts = collect(request('accounts'))->filter(function ($account) {
            return ! User::whereEmail($account['email'])->exists();
        })->values();

        foreach ($newAccounts as $account) {
            $user = User::create([
                'first_name' => $account['firstName'],
                'last_name' => $account['lastName'],
                'email' => $account['email'],
                'password' => Hash::make(Str::random(30)),
                'granted_by' => auth()->id(),
            ]);

            Mail::to($user)->send(new AccountCreatedByAdminWelcome);
        }

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'account-batch-creation',
            'data' => json_encode([
                'accounts' => $newAccounts,
            ]),
        ]);

        return $newAccounts;
    }
}
