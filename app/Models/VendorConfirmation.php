<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorConfirmation extends Model
{
    protected $fillable = [
        'event_vendor_id',
        'availability_status',
        'confirmed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
        ];
    }

    public function eventVendor(): BelongsTo
    {
        return $this->belongsTo(EventVendor::class, 'event_vendor_id');
    }
}