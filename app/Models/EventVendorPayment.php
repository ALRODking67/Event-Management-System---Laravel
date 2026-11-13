<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventVendorPayment extends Model
{
    protected $fillable = [
        'event_vendor_id',
        'amount',
        'payment_type',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'recorded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function eventVendor(): BelongsTo
    {
        return $this->belongsTo(EventVendor::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function financialTransaction(): HasOne
    {
        return $this->hasOne(FinancialTransaction::class, 'vendor_payment_id');
    }
}