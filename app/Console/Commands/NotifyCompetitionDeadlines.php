<?php

namespace App\Console\Commands;

use App\Mail\CompetitionDeadlineNotification;
use App\Models\Competition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyCompetitionDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:notify-competition-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to admins about upcoming competition registration deadlines';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = now()->startOfDay();

        // Get all competitions with registration deadlines
        $competitions = Competition::whereNotNull('last_registration_at')
            ->where('last_registration_at', '>=', $today->copy()->subDays(1))
            ->get();

        if ($competitions->isEmpty()) {
            $this->info('No competitions with upcoming deadlines found.');

            return 0;
        }

        $notificationsSent = 0;

        foreach ($competitions as $competition) {
            $deadline = $competition->last_registration_at->startOfDay();
            $daysUntilDeadline = (int) $today->diffInDays($deadline, false);

            $deadlineType = null;

            // Check if it's 5 days before deadline
            if ($daysUntilDeadline === 5) {
                $deadlineType = '5_days_before';
            }
            // Check if it's the last day (deadline is today)
            elseif ($daysUntilDeadline === 0) {
                $deadlineType = 'last_day';
            }
            // Check if it's the day after deadline
            elseif ($daysUntilDeadline === -1) {
                $deadlineType = 'day_after';
            }

            // Send notification if it's one of the trigger days
            if ($deadlineType) {
                Mail::to('martin.krisell@gmail.com')->send(
                    new CompetitionDeadlineNotification($competition, $daysUntilDeadline, $deadlineType),
                );
                $notificationsSent++;

                $this->info("Sent {$deadlineType} notification for competition: {$competition->name} (ID: {$competition->id})");
            }
        }

        if ($notificationsSent > 0) {
            $this->info("Total notifications sent: {$notificationsSent}");
        } else {
            $this->info('No competitions matched notification criteria today.');
        }

        return 0;
    }
}
