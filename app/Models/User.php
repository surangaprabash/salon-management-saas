<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * role, status and salon_id are intentionally NOT mass assignable.
     * They are set explicitly by services after validation.
     */
    protected $fillable = ['name', 'email', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function salon(): BelongsTo
    {
        // withTrashed so the admin can still see a soft-deleted salon.
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isSalonAdmin(): bool
    {
        return $this->role === UserRole::SalonAdmin;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.addcslashes(trim($term), '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)->orWhere('email', 'like', $like);
        });
    }

    public function scopeWithRole(Builder $query, UserRole|string|null $role): Builder
    {
        if (blank($role)) {
            return $query;
        }

        return $query->where('role', $role instanceof UserRole ? $role->value : $role);
    }

    public function scopeWithStatus(Builder $query, UserStatus|string|null $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status instanceof UserStatus ? $status->value : $status);
    }
}