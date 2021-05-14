<?php

namespace App\Http\Controllers;

use App\Result;
use App\User;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function index()
    {
        return view('admin.results', [
            'results' => Result::with('user:id,first_name,last_name')->get(),
            'users' => User::get(['id', 'first_name', 'last_name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'gender' => 'required',
            'competition_date' => 'required',
            'weight_class' => 'required',
            'event' => 'required',
            'user_id' => 'required',
            'result' => 'required',
        ]);

        return Result::create($data);
    }

    public function destroy(Result $result)
    {
        $result->delete();
    }
}
