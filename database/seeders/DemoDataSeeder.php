<?php

namespace Database\Seeders;

use App\Enums\BillingStatus;
use App\Enums\PaymentRequestStatus;
use App\Enums\SalonStatus;
use App\Enums\UserRole;
use App\Models\BillingRecord;
use App\Models\PaymentRequest;
use App\Models\Salon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    // 1x1 PNG used as a placeholder receipt.
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    public function run(): void
    {
        $admin = User::where('role', UserRole::SuperAdmin->value)->firstOrFail();
        $password = Hash::make(config('admin.seed.password')); // hashed once, reused
        $disk = Storage::disk(config('admin.receipts.disk'));

        $P = BillingStatus::Pending;
        $D = BillingStatus::Paid;
        $A = PaymentRequestStatus::Approved;
        $R = PaymentRequestStatus::Rejected;
        $W = PaymentRequestStatus::Pending;

        // [name, owner, city, fee, status, [[billing status, request status|null] x 3 months]]
        $salons = [
            ['ABC Beauty Salon', 'Nimali Perera', 'Colombo', 5000, SalonStatus::Active, [[$D, $A], [$D, $A], [$P, null]]],
            ['Glow Salon', 'Kasun Fernando', 'Kandy', 7500, SalonStatus::Active, [[$D, $A], [$D, $A], [$P, $W]]],
            ['Style Studio', 'Dilani Silva', 'Galle', 4000, SalonStatus::Blocked, [[$D, $A], [$P, $R], [$P, null]]],
        ];

        foreach ($salons as [$name, $owner, $city, $fee, $status, $months]) {
            $salon = Salon::factory()->create([
                'name' => $name,
                'owner_name' => $owner,
                'city' => $city,
                'email' => Str::slug($name).'@example.test',
                'monthly_fee' => $fee,
                'status' => $status,
                'blocked_reason' => $status === SalonStatus::Blocked ? 'Unpaid September bill' : null,
                'blocked_at' => $status === SalonStatus::Blocked ? now() : null,
            ]);

            $owner = User::factory()->salonAdmin()->inSalon($salon)->create([
                'name' => $owner,
                'email' => Str::slug($owner, '.').'@example.test',
                'password' => $password,
            ]);

            User::factory()->count(2)->staff()->inSalon($salon)->create(['password' => $password]);
            User::factory()->count(5)->customer()->inSalon($salon)->create(['password' => $password]);

            foreach ($months as $i => [$billingStatus, $requestStatus]) {
                $start = now()->startOfMonth()->subMonths(2 - $i);

                $bill = BillingRecord::factory()->create([
                    'salon_id' => $salon->id,
                    'period_start' => $start,
                    'period_end' => $start->copy()->endOfMonth()->startOfDay(),
                    'due_date' => $start->copy()->day(10),
                    'amount' => $fee,
                    'currency' => 'LKR',
                    'status' => $billingStatus,
                    'paid_at' => $billingStatus === BillingStatus::Paid ? $start->copy()->day(8) : null,
                ]);

                if ($requestStatus) {
                    $this->makeRequest($disk, $bill, $owner, $admin, $requestStatus, $start);
                }
            }
        }
    }

    private function makeRequest($disk, BillingRecord $bill, User $submitter, User $admin, PaymentRequestStatus $status, Carbon $month): void
    {
        $path = "receipts/demo/{$bill->salon_id}-{$bill->id}.png";
        $disk->put($path, base64_decode(self::PNG));

        $reviewed = $status !== PaymentRequestStatus::Pending;

        $request = new PaymentRequest([
            'salon_id' => $bill->salon_id,
            'billing_record_id' => $bill->id,
            'submitted_by' => $submitter->id,
            'amount' => $bill->amount,
            'payment_date' => $month->copy()->day(8),
            'payment_method' => 'Bank Transfer',
            'reference_number' => strtoupper(Str::random(10)),
            'receipt_path' => $path,
            'receipt_original_name' => 'receipt.png',
            'receipt_mime' => 'image/png',
            'receipt_size' => (int) $disk->size($path),
        ]);

        $request->status = $status;
        $request->reviewed_by = $reviewed ? $admin->id : null;
        $request->reviewed_at = $reviewed ? $month->copy()->day(9) : null;
        $request->rejection_reason = $status === PaymentRequestStatus::Rejected ? 'Receipt is unclear.' : null;
        $request->save();
    }
}