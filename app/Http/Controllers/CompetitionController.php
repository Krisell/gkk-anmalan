<?php

namespace App\Http\Controllers;

use App\Mail\EventCompetitionNotificationMail;
use App\Models\ActivityLog;
use App\Models\Competition;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::visible()->orderBy('date')->get();

        foreach ($competitions as $competition) {
            if ($competition->publish_count) {
                $competition->append('publish_count_value');
            }
        }

        return view('competitions', [
            'competitions' => $competitions,
            'userRegistrations' => auth()->user()->competitionRegistrations,
            'view' => 'competition',
            'user' => auth()->user(),
        ]);
    }

    public function show(Competition $competition)
    {
        if ($competition->publish_list) {
            $competition->append('publish_list_value');
        }

        return view('competition', [
            'competition' => $competition,
            'user' => auth()->user(),
            'registration' => auth()->user()->competitionRegistrations()->whereCompetitionId($competition->id)->first(),
            'view' => 'competition',
        ]);
    }

    public function store(Request $request)
    {
        return Competition::create($this->validated($request));
    }

    public function update(Request $request, Competition $competition)
    {
        $competition->update($this->validated($request));
    }

    public function adminIndex(Request $request)
    {
        $competitions = Competition::with('registrations');

        if (! $request->has('all')) {
            $competitions->where('date', '>', now()->subDays(7));
        }

        return view('admin.competitions', [
            'competitions' => $competitions->latest()->get(),
            'showingOld' => $request->has('all'),
            'view' => 'competition',
        ]);
    }

    public function admin(Competition $competition)
    {
        $remainingUsers = User::query()
            ->whereNotIn('id', $competition->registrations->pluck('user_id'))
            ->orderBy('first_name', 'asc')
            ->get();

        return view('admin.competition', [
            'competition' => $competition->load('registrations.user'),
            'remainingUsers' => $remainingUsers,
        ]);
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();
    }

    public function sendNotification(Competition $competition, Request $request)
    {
        $request->validate([
            'confirmed' => 'required|boolean|accepted',
        ]);

        // Get all granted users with email addresses
        $users = User::whereNotNull('email')
            ->where('granted_by', '>', 0)
            ->get();

        $recipientCount = 0;

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new EventCompetitionNotificationMail($competition, 'competition')
                );
                $recipientCount++;
            } catch (\Exception $e) {
                // Log error but continue sending to other users
                \Log::error("Failed to send competition notification to user {$user->id}: ".$e->getMessage());
            }
        }

        // Log the notification
        NotificationLog::create([
            'notifiable_type' => Competition::class,
            'notifiable_id' => $competition->id,
            'sent_by' => auth()->id(),
            'recipients_count' => $recipientCount,
        ]);

        return response()->json([
            'success' => true,
            'recipients_count' => $recipientCount,
        ]);
    }

    public function notificationStatus(Competition $competition)
    {
        $logs = $competition->notificationLogs()
            ->with('sentBy:id,first_name,last_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $logs,
        ]);
    }

    public function add(Competition $competition, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'events' => 'required|json',
        ]);

        $user = User::findOrFail($request->user_id);

        $competition->registrations()->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'status' => true,
            'comment' => 'Tillagd av admin',
            'gender' => $user->gender,
            'weight_class' => $user->weight_class,
            'events' => $request->events,
        ]);

        ActivityLog::create([
            'performed_by' => auth()->id(),
            'action' => 'competition-registration',
            'user_id' => $user->id,
            'data' => \json_encode([
                'event_id' => $competition->id,
            ]),
        ]);
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'time' => 'nullable',
            'date' => 'nullable',
            'end_date' => 'nullable',
            'location' => 'nullable',
            'description' => 'nullable',
            'events' => 'json',
            'publish_count' => 'nullable',
            'publish_list' => 'nullable',
            'last_registration_at' => 'nullable',
            'show_status' => 'required',
        ]);
    }
}
