<?php

namespace Database\Factories;

use App\Enums\PaymentRequestStatus;
use App\Enums\UserRole;
use App\Models\BillingRecord;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentRequest>
 */
class PaymentRequestFactory extends Factory
{
    protected $model = PaymentRequest::class;

    public function definition(): array
    {
        // Order matters: later closures read earlier resolved attributes.
        return [
            'billing_record_id' => BillingRecord::factory(),
            'salon_id' => fn (array $a) => BillingRecord::findOrFail($a['billing_record_id'])->salon_id,
            'submitted_by' => fn (array $a) => User::factory()
                ->state(['role' => UserRole::SalonAdmin, 'salon_id' => $a['salon_id']])
                ->create()->id,
            'amount' => fn (array $a) => BillingRecord::findOrFail($a['billing_record_id'])->amount,
            'payment_date' => now()->toDateString(),
            'payment_method' => fake()->randomElement(['Bank Transfer', 'Cash Deposit', 'Mobile Wallet']),
            'reference_number' => strtoupper(Str::random(10)),
            'note' => null,
            'receipt_path' => 'receipts/'.Str::uuid().'.png',
            'receipt_original_name' => 'receipt.png',
            'receipt_mime' => 'image/png',
            'receipt_size' => 1024,
            'status' => PaymentRequestStatus::Pending,
        ];
    }

    public function approved(?User $reviewer = null): static
    {
        return $this->state(fn () => [
            'status' => PaymentRequestStatus::Approved,
            'reviewed_by' => $reviewer?->id,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(string $reason = 'Receipt is unclear.', ?User $reviewer = null): static
    {
        return $this->state(fn () => [
            'status' => PaymentRequestStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer?->id,
            'reviewed_at' => now(),
        ]);
    }
}