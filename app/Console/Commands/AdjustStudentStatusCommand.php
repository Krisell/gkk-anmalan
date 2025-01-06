<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AdjustStudentStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gkk:adjust-student-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ask for user

        // Check current student status

        // If not set, confirm if user wants to set it, otherwise inverse

        // If user has an unpaid MEMBERSHIP invoice, confirm to do the following:
        // 1. Credit the invoice
        // 2. Delete the payment entry
        // 3. Create a new payment entry with the correct status
        // 4. Create a new invoice
        // 5. Send the invoice to the user
    }
}
