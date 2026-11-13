<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVendorOffer extends Model
{
    protected $fillable = [
        'event_vendor_id',
        'offered_by_user_id',
        'offer_type',
        'amount',
        'start_datetime',
        'end_datetime',
        'scope_description',
        'payment_terms',
        'notes',
        'status',
        'responded_by_user_id',
        'responded_at',
        'response_note',
        'accepted_by_user_id',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'responded_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function eventVendor(): BelongsTo
    {
        return $this->belongsTo(EventVendor::class);
    }

    public function offeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'offered_by_user_id');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by_user_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }
}
