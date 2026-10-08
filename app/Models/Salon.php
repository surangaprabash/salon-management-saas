<?php

namespace App\Models;

use App\Enums\SalonStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Salon extends Model
{
    use HasFactory, SoftDeletes;

    /** Slugs that would collide with real URLs on theweb.com. */
    public const RESERVED_SLUGS = [
        'admin', 'api', 'login', 'logout', 'register', 'staff', 'customer',
        'customers', 'salon', 'salons', 'billing', 'storage', 'up', 'www',
        'assets', 'static', 'password', 'dashboard',
    ];

    /** status/slug/blocked_* are set by services, never mass assigned. */
    protected $fillable = [
        'name', 'owner_name', 'email', 'phone', 'address', 'city', 'description',
        'monthly_fee', 'currency', 'billing_start_date', 'next_billing_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => SalonStatus::class,
            'monthly_fee' => 'decimal:2',
            'billing_start_date' => 'date',
            'next_billing_date' => 'date',
            'blocked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Salon $salon) {
            if (blank($salon->slug)) {
                $salon->slug = static::generateUniqueSlug($salon->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'salon';

        if (in_array($base, self::RESERVED_SLUGS, true)) {
            $base .= '-salon';
        }

        $slug = $base;
        $i = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function billingRecords(): HasMany
    {
        return $this->hasMany(BillingRecord::class);
    }

    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    public function isActive(): bool
    {
        return $this->status === SalonStatus::Active;
    }

    public function isBlocked(): bool
    {
        return $this->status === SalonStatus::Blocked;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.addcslashes(trim($term), '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('owner_name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like);
        });
    }

    public function scopeWithStatus(Builder $query, SalonStatus|string|null $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status instanceof SalonStatus ? $status->value : $status);
    }
}