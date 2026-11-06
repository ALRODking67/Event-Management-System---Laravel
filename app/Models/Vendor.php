<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    protected $fillable = [
        'vendor_name',
        'contact_name',
        'email',
        'phone',
        'service_type',
        'organization_id',
    ];

    /**
     * Akun pengguna yang terhubung dengan vendor ini.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'vendor_id');
    }

    /**
     * Daftar penugasan vendor pada berbagai acara.
     */
    public function eventAssignments(): HasMany
    {
        return $this->hasMany(EventVendor::class, 'vendor_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}