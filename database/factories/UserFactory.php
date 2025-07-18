<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $male = \random_int(0, 1) === 0;

        return [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'birth_year' => now()->subYears(30)->year, // Not eligible for age discount
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'membership_agreement_signed_at' => now(),
            'anti_doping_agreement_signed_at' => now(),
            'inactivated_at' => null,
            'is_student_over_23' => false,
            'granted_by' => 1,
            'gender' => $male ? 'm' : 'f',
            'weight_class' => $male ? '74' : '63',
        ];
    }

    public function ungranted()
    {
        return $this->state(fn () => [
            'granted_by' => '0',
        ]);
    }

    public function admin()
    {
        return $this->state(fn () => [
            'role' => 'admin',
        ]);
    }

    public function superadmin()
    {
        return $this->state(fn () => [
            'role' => 'superadmin',
        ]);
    }

    public function inactivated()
    {
        return $this->state(fn () => [
            'inactivated_at' => now(),
            'birth_year' => \rand(1950, 2020),
        ]);
    }

    public function honorary()
    {
        return $this->state(fn () => [
            'is_honorary_member' => true,
        ]);
    }

    public function young()
    {
        return $this->state(fn () => [
            'birth_year' => now()->subYears(20)->year,
        ]);
    }

    public function old()
    {
        return $this->state(fn () => [
            'birth_year' => now()->subYears(70)->year,
        ]);
    }

    public function student()
    {
        return $this->state(fn () => [
            'is_student_over_23' => true,
        ]);
    }
}
