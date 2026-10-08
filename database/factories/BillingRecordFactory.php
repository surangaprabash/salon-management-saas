<?php

namespace Database\Factories;

use App\Enums\BillingStatus;
use App\Models\BillingRecord;
use App\Models\Salon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillingRecord>
 */
class BillingRecordFactory extends Factory
{
    protected $model = BillingRecord::class;

    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('-3 years', 'now'))->startOfMonth();

        return [
            'salon_id' => Salon::factory(),
            'period_start' => $start,
            'period_end' => $start->copy()->endOfMonth()->startOfDay(),
            'due_date' => $start->copy()->day(10),
            'amount' => 5000,
            'currency' => 'LKR',
            'status' => BillingStatus::Pending,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => BillingStatus::Paid,
            'paid_at' => $attrs['due_date'],
        ]);
    }

    public function forPeriod(Carbon $month): static
    {
        return $this->state([
            'period_start' => $month->copy()->startOfMonth(),
            'period_end' => $month->copy()->endOfMonth()->startOfDay(),
            'due_date' => $month->copy()->startOfMonth()->day(10),
        ]);
    }
}