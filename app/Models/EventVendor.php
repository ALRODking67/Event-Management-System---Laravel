<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventVendor extends Model
{
    protected $fillable = [
        'event_id',
        'vendor_id',
        'service_description',
        'agreed_cost',
        'assignment_status',
    ];

    protected function casts(): array
    {
        return [
            'agreed_cost' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(VendorConfirmation::class, 'event_vendor_id');
    }
}