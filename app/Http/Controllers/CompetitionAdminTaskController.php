<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CompetitionAdminTask;
use Illuminate\Http\Request;

class CompetitionAdminTaskController extends Controller
{
    public function update(Request $request, CompetitionAdminTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:done,not_applicable',
        ]);

        abort_unless($task->status === 'pending', 409, 'Uppgiften är redan hanterad.');

        $task->update([
            'status' => $validated['status'],
            'completed_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'competition-admin-task-updated',
            'data' => \json_encode([
                'competition_id' => $task->competition_id,
                'task_id' => $task->id,
                'type' => $task->type,
                'status' => $task->status,
                'competition_name' => $task->competition?->name,
            ]),
        ]);

        return response()->json($task->fresh('competition'));
    }
}
