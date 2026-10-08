<?php

namespace App\Models;

use App\Enums\BillingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingRecord extends Model
{
    use HasFactory;

    /** status/paid_at are changed only by the payment service. */
    protected $fillable = [
        'salon_id', 'period_start', 'period_end', 'due_date',
        'amount', 'currency', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'status' => BillingStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    protected function periodLabel(): Attribute
    {
        return Attribute::get(fn () => $this->period_start?->format('F Y'));
    }

    public function isPaid(): bool
    {
        return $this->status === BillingStatus::Paid;
    }

    public function scopeOutstanding(Builder $query): Builder
    {
        return $query->where('status', BillingStatus::Pending->value);
    }
}