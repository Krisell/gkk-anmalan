<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Mail\AccountGrantedMail;
use Illuminate\Support\Facades\Mail;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.accounts', [
            'ungranted' => User::whereGrantedBy(0)->get(),
            'accounts' => User::with(['eventRegistrations.event'])->get(),
            'user' => auth()->user(),
        ]);
    }

    public function promote(User $user)
    {
        $user->update(['role' => 'admin']);
    }

    public function demote(User $user)
    {
        $user->update(['role' => null]);
    }

    public function grant(User $user)
    {
        $user->update(['granted_by' => auth()->id()]);

        Mail::to($user->email)->send(new AccountGrantedMail);
    }

    public function destroyUngranted(User $user)
    {
        if ($user->granted_by == 0) {
            $user->delete();
        }
    }
}
