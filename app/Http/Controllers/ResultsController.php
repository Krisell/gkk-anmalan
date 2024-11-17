<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function index()
    {
        return view('admin.results', [
            'results' => Result::with('user:id,first_name,last_name')->get(),
            'users' => User::get(['id', 'first_name', 'last_name']),
            'view' => 'records',
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

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'result-creation',
            'data' => \json_encode($data),
        ]);

        return Result::create($data);
    }

    public function destroy(Result $result)
    {
        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'result-deletion',
            'data' => \json_encode([
                'result_id' => $result->id,
            ]),
        ]);

        $result->delete();
    }
}
