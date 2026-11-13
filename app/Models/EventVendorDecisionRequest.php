<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVendorDecisionRequest extends Model
{
    protected $fillable = [
        'event_vendor_id',
        'event_vendor_offer_id',
        'requested_by_user_id',
        'requested_decision',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function eventVendor(): BelongsTo
    {
        return $this->belongsTo(EventVendor::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(EventVendorOffer::class, 'event_vendor_offer_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}