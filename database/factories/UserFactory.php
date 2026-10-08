<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'salon_id' => Salon::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('07########'),
            'role' => UserRole::Customer,
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
            // Factory/test default only. Seeders override this.
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => UserRole::SuperAdmin, 'salon_id' => null]);
    }

    public function salonAdmin(): static
    {
        return $this->state(['role' => UserRole::SalonAdmin]);
    }

    public function staff(): static
    {
        return $this->state(['role' => UserRole::Staff]);
    }

    public function customer(): static
    {
        return $this->state(['role' => UserRole::Customer]);
    }

    public function disabled(): static
    {
        return $this->state(['status' => UserStatus::Disabled]);
    }

    public function inSalon(Salon $salon): static
    {
        return $this->state(['salon_id' => $salon->id]);
    }
}