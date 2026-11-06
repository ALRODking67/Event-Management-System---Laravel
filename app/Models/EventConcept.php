<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventConcept extends Model
{
    protected $fillable = [
        'event_id',
        'purpose',
        'theme',
        'target_audience',
        'event_format',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}