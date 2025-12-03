<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_group_id',
        'equipment_id',
        'equipment_set_id',
        'rented_at',
        'returned_at',
        'rented_by_user_id',
        'returned_by_user_id',
        'rental_notes',
        'return_notes',
    ];

    protected $casts = [
        'rented_at' => 'datetime',
        'returned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Rental group (borrowers)
     */
    public function rentalGroup(): BelongsTo
    {
        return $this->belongsTo(RentalGroup::class);
    }

    /**
     * Individual equipment rented
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Equipment set rented
     */
    public function equipmentSet(): BelongsTo
    {
        return $this->belongsTo(EquipmentSet::class);
    }

    /**
     * User who processed rental (admin/instructor)
     */
    public function rentedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rented_by_user_id');
    }

    /**
     * User who processed return
     */
    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_user_id');
    }

    /**
     * Check if returned
     */
    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    /**
     * Check if active (not returned)
     */
    public function isActive(): bool
    {
        return $this->returned_at === null;
    }

    /**
     * Get rental duration in days
     */
    public function getDurationInDays(): int
    {
        $end = $this->returned_at ?? now();
        return $this->rented_at->diffInDays($end);
    }

    /**
     * Get display name of rented item
     */
    public function getItemNameAttribute(): string
    {
        if ($this->equipment) {
            return $this->equipment->name;
        }

        if ($this->equipmentSet) {
            return $this->equipmentSet->name . ' (Zestaw)';
        }

        return 'Nieznany';
    }

    /**
     * Get display name of borrowers
     */
    public function getBorrowersNameAttribute(): string
    {
        if ($this->rentalGroup) {
            return $this->rentalGroup->name;
        }

        return 'Nieznany';
    }

    /**
     * Scope for active rentals
     */
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    /**
     * Scope for returned rentals
     */
    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }
}
