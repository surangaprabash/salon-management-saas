<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.seed.email');
        $password = config('admin.seed.password');

        if (blank($password)) {
            throw new RuntimeException('Set ADMIN_SEED_PASSWORD in .env before running the seeder.');
        }

        $user = User::withTrashed()->firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => $user->name ?: 'Super Admin',
            'role' => UserRole::SuperAdmin,
            'status' => UserStatus::Active,
            'salon_id' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        // Never overwrite the password of an existing account.
        if (! $user->exists) {
            $user->password = $password;
        }

        $user->deleted_at = null;
        $user->save();
    }
}