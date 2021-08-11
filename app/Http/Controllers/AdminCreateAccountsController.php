<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreatedByAdminWelcome;

class AdminCreateAccountsController extends Controller
{
    public function store()
    {
        $newAccounts = collect(request('accounts'))->filter(function ($account) {
            return !User::whereEmail($account['email'])->exists();
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

        return $newAccounts;
    }
}
