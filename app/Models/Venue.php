<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    protected $fillable = [
        'venue_name',
        'address',
        'capacity',
        'electricity_info',
        'access_info',
        'security_info',
        'parking_info',
        'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    /**
     * Daftar acara yang diselenggarakan di venue ini.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'venue_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}