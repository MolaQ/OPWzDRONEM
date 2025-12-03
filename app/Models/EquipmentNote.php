<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'rental_id',
        'note',
        'type',
        'created_by_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Equipment this note belongs to
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Rental this note is related to (if any)
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * User who created this note
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get badge color based on type
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'info' => 'blue',
            'warning' => 'yellow',
            'damage' => 'red',
            'maintenance' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get type label in Polish
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'info' => 'Informacja',
            'warning' => 'Ostrzeżenie',
            'damage' => 'Uszkodzenie',
            'maintenance' => 'Konserwacja',
            default => 'Inne',
        };
    }
}
