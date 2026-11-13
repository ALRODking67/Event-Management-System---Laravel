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
        'agreement_status',
        'agreed_start_datetime',
        'agreed_end_datetime',
        'agreed_scope',
        'agreed_payment_terms',
        'agreement_accepted_at',
        'agreement_accepted_by',
    ];

    protected function casts(): array
    {
        return [
            'agreed_cost' => 'decimal:2',
            'agreed_start_datetime' => 'datetime',
            'agreed_end_datetime' => 'datetime',
            'agreement_accepted_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(VendorConfirmation::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(EventVendorOffer::class)
            ->orderBy('created_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EventVendorPayment::class)
            ->orderBy('payment_date');
    }

    public function agreementAcceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agreement_accepted_by');
    }

    public function decisionRequests(): HasMany
    {
        return $this->hasMany(EventVendorDecisionRequest::class)
            ->latest();
    }
}