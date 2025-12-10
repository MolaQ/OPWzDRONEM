<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\EquipmentSet;

class EquipmentReservation extends Model
{
    use HasFactory;

    protected $table = 'equipment_reservations';

    protected $fillable = [
        'equipment_id',
        'equipment_set_id',
        'user_id',
        'group_id',
        'status',
        'reserved_from',
        'reserved_until',
        'reason',
        'notes',
        'actual_checkout_at',
        'actual_checkin_at',
        'confirmed_by_user_id',
        'confirmed_at',
    ];

    protected $casts = [
        'reserved_from' => 'datetime',
        'reserved_until' => 'datetime',
        'actual_checkout_at' => 'datetime',
        'actual_checkin_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Equipment being reserved
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Equipment set being reserved
     */
    public function equipmentSet(): BelongsTo
    {
        return $this->belongsTo(EquipmentSet::class);
    }

    /**
     * User making the reservation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Group the reservation is for
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * User who confirmed the reservation
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    /**
     * Check if reservation is active (current)
     */
    public function isActive(): bool
    {
        return $this->status === 'confirmed' && now()->between($this->reserved_from, $this->reserved_until);
    }

    /**
     * Check if reservation is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'confirmed' && $this->reserved_from->isFuture();
    }

    /**
     * Check if reservation is past
     */
    public function isPast(): bool
    {
        return $this->reserved_until->isPast();
    }

    /**
     * Get status label in Polish
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Oczekuje na potwierdzenie',
            'confirmed' => 'Potwierdzona',
            'used' => 'Użyta',
            'cancelled' => 'Anulowana',
            'no_show' => 'Nie pojawił się',
            default => $this->status,
        };
    }

    /**
     * Scope for active reservations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'confirmed')
            ->where('reserved_from', '<=', now())
            ->where('reserved_until', '>=', now());
    }

    /**
     * Scope for upcoming reservations
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'confirmed')
            ->where('reserved_from', '>', now());
    }

    /**
     * Scope for pending confirmations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
