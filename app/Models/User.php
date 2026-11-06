<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role_id',
    'is_active',
    'vendor_id',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke role pengguna.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke data vendor yang terhubung dengan akun pengguna.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relasi ke keanggotaan acara.
     */
    public function eventMemberships(): HasMany
    {
        return $this->hasMany(EventMember::class);
    }

    /**
     * Relasi ke acara yang dibuat pengguna.
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Relasi ke item rundown yang menjadi tanggung jawab pengguna.
     */
    public function responsibleRundownItems(): HasMany
    {
        return $this->hasMany(RundownItem::class, 'responsible_user_id');
    }

    /**
     * Relasi ke peralatan acara yang terakhir diperbarui pengguna.
     */
    public function updatedEventEquipment(): HasMany
    {
        return $this->hasMany(EventEquipment::class, 'updated_by');
    }

    /**
     * Relasi ke transaksi keuangan yang dicatat pengguna.
     */
    public function recordedFinancialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'recorded_by');
    }

    /**
     * Relasi ke evaluasi yang dibuat pengguna.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evaluated_by');
    }

    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(\App\Models\OrganizationMember::class);
    }
}