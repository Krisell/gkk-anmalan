<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminCreateAccountsController extends Controller
{
    public function store()
    {
        $newAccounts = collect(request('accounts'))->filter(function ($account) {
            return !User::whereEmail($account['email'])->exists();
        });

        // foreach ($newAccounts as $account) {
        //     User::create([
        //         'first_name' => $account['firstName'],
        //         'last_name' => $account['lastName'],
        //         'email' => $account['email'],
        //         'password' => Hash::make(Str::random(30)),
        //         'granted_by' => auth()->id(),
        //     ]);
        // }

        return $newAccounts;
    }
}
