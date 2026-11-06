<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentCategory extends Model
{
    protected $fillable = [
        'category_name',
        'organization_id',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}