<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run()
    {
        $actions = [
            ['action' => 'account-promotion', 'performed_by' => 3, 'user_id' => 1, 'data' => 'admin', 'created_at' => now()->subDays(45)],
            ['action' => 'account-demotion', 'performed_by' => 3, 'user_id' => 1, 'data' => null, 'created_at' => now()->subDays(40)],
            ['action' => 'account-batch-creation', 'performed_by' => 2, 'user_id' => null, 'data' => '5 accounts created', 'created_at' => now()->subDays(38)],
            ['action' => 'explicit-registration-approval-update', 'performed_by' => 2, 'user_id' => 1, 'data' => 'granted', 'created_at' => now()->subDays(35)],
            ['action' => 'explicit-registration-approval-update', 'performed_by' => 2, 'user_id' => 5, 'data' => 'granted', 'created_at' => now()->subDays(34)],
            ['action' => 'explicit-registration-approval-update', 'performed_by' => 3, 'user_id' => 5, 'data' => 'revoked', 'created_at' => now()->subDays(30)],
            ['action' => 'ren-vinnare-education-update', 'performed_by' => 2, 'user_id' => 1, 'data' => 'completed', 'created_at' => now()->subDays(28)],
            ['action' => 'ren-vinnare-education-update', 'performed_by' => 2, 'user_id' => 5, 'data' => 'completed', 'created_at' => now()->subDays(27)],
            ['action' => 'background-check-update', 'performed_by' => 3, 'user_id' => 2, 'data' => 'approved', 'created_at' => now()->subDays(25)],
            ['action' => 'event-registration', 'performed_by' => 2, 'user_id' => 1, 'data' => null, 'created_at' => now()->subDays(20)],
            ['action' => 'event-registration', 'performed_by' => 2, 'user_id' => 5, 'data' => null, 'created_at' => now()->subDays(19)],
            ['action' => 'competition-registration', 'performed_by' => 2, 'user_id' => 1, 'data' => null, 'created_at' => now()->subDays(15)],
            ['action' => 'competition-registration', 'performed_by' => 4, 'user_id' => 5, 'data' => null, 'created_at' => now()->subDays(14)],
            ['action' => 'payment-update', 'performed_by' => 2, 'user_id' => 1, 'data' => 'PAID', 'created_at' => now()->subDays(12)],
            ['action' => 'payment-update', 'performed_by' => 2, 'user_id' => 5, 'data' => 'PAID', 'created_at' => now()->subDays(11)],
            ['action' => 'result-creation', 'performed_by' => 4, 'user_id' => 1, 'data' => null, 'created_at' => now()->subDays(8)],
            ['action' => 'result-deletion', 'performed_by' => 4, 'user_id' => 1, 'data' => null, 'created_at' => now()->subDays(7)],
            ['action' => 'result-creation', 'performed_by' => 4, 'user_id' => 1, 'data' => null, 'created_at' => now()->subDays(6)],
            ['action' => 'account-promotion', 'performed_by' => 3, 'user_id' => 4, 'data' => 'admin', 'created_at' => now()->subDays(3)],
            ['action' => 'explicit-registration-approval-update', 'performed_by' => 4, 'user_id' => 1, 'data' => 'revoked', 'created_at' => now()->subDays(1)],
        ];

        foreach ($actions as $action) {
            ActivityLog::create($action);
        }
    }
}
