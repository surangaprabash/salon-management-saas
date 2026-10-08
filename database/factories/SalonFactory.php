<?php

namespace Database\Factories;

use App\Enums\SalonStatus;
use App\Models\Salon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Salon>
 */
class SalonFactory extends Factory
{
    protected $model = Salon::class;

    public function definition(): array
    {
        $start = now()->subMonths(3)->startOfMonth()->addDays(9);

        return [
            'name' => fake()->unique()->company().' Salon',
            'owner_name' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('07########'),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Colombo', 'Kandy', 'Galle', 'Kurunegala', 'Negombo']),
            'description' => fake()->sentence(12),
            'status' => SalonStatus::Active,
            'monthly_fee' => 5000,
            'currency' => 'LKR',
            'billing_start_date' => $start,
            'next_billing_date' => now()->addMonth()->startOfMonth()->addDays(9),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => SalonStatus::Pending]);
    }

    public function blocked(string $reason = 'Unpaid billing'): static
    {
        return $this->state([
            'status' => SalonStatus::Blocked,
            'blocked_reason' => $reason,
            'blocked_at' => now(),
        ]);
    }
}