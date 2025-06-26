<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'unpaid-membership@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Membership',
        ]);

        $user->payments()->create([
            'sek_amount' => 1500,
            'type' => 'MEMBERSHIP',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-discounted-membership@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Discounted Membership',
        ]);

        $user->payments()->create([
            'sek_amount' => 700,
            'sek_discount' => 0, // This is a separate article, discount is used for half year memberships
            'modifier' => 'AGE_DISCOUNT',
            'type' => 'MEMBERSHIP',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-ssf-license@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'SSF License',
        ]);

        $user->payments()->create([
            'sek_amount' => 900,
            'type' => 'SSFLICENSE',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-competition-fee@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Competition Fee',
        ]);

        $user->payments()->create([
            'sek_amount' => 500,
            'type' => 'COMPETITION',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-other-fee@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Other Fee',
        ]);

        $user->payments()->create([
            'sek_amount' => 300,
            'type' => 'OTHER',
            'year' => 2025,
        ]);
    }
}
