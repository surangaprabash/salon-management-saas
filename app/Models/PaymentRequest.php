<?php

namespace App\Models;

use App\Enums\PaymentRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRequest extends Model
{
    use HasFactory;

    /** status, reviewed_*, rejection_reason are set only by the payment service. */
    protected $fillable = [
        'salon_id', 'billing_record_id', 'submitted_by',
        'amount', 'payment_date', 'payment_method', 'reference_number', 'note',
        'receipt_path', 'receipt_original_name', 'receipt_mime', 'receipt_size',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'status' => PaymentRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function billingRecord(): BelongsTo
    {
        return $this->belongsTo(BillingRecord::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withTrashed();
    }

    public function isPending(): bool
    {
        return $this->status === PaymentRequestStatus::Pending;
    }
}