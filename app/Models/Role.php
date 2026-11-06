<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'role_name',
    ];

    /**
     * Pengguna yang memiliki role ini sebagai role utama.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Keanggotaan acara yang menggunakan role ini.
     */
    public function eventMemberships(): HasMany
    {
        return $this->hasMany(EventMember::class, 'role_id');
    }
}
