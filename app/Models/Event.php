<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'event_name',
        'start_datetime',
        'end_datetime',
        'status',
        'venue_id',
        'created_by',
        'organization_id',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
        ];
    }

    /**
     * Lokasi penyelenggaraan acara.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    /**
     * Pengguna yang membuat acara.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Daftar anggota yang terlibat dalam acara.
     */
    public function members(): HasMany
    {
        return $this->hasMany(EventMember::class, 'event_id');
    }

    /**
     * Konsep acara.
     */
    public function concept(): HasOne
    {
        return $this->hasOne(EventConcept::class, 'event_id');
    }

    /**
     * Daftar peralatan yang ditugaskan untuk acara.
     */
    public function equipmentAssignments(): HasMany
    {
        return $this->hasMany(EventEquipment::class, 'event_id');
    }

    /**
     * Daftar vendor yang ditugaskan untuk acara.
     */
    public function vendorAssignments(): HasMany
    {
        return $this->hasMany(EventVendor::class, 'event_id');
    }

    /**
     * Rundown atau susunan kegiatan acara.
     */
    public function rundownItems(): HasMany
    {
        return $this->hasMany(RundownItem::class, 'event_id');
    }

    /**
     * Transaksi keuangan acara.
     */
    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'event_id');
    }

    /**
     * Evaluasi acara.
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class, 'event_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}