<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Equipment items in this set
     */
    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'equipment_set_items')
            ->withTimestamps();
    }

    /**
     * Rentals of this set
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Check if set is complete (all equipment available)
     */
    public function isComplete(): bool
    {
        return $this->equipments()
            ->where('status', '!=', 'available')
            ->doesntExist();
    }

    /**
     * Get missing equipment (not available)
     */
    public function missingEquipment()
    {
        return $this->equipments()
            ->where('status', '!=', 'available')
            ->get();
    }

    /**
     * Check if set is available for rental
     */
    public function isAvailable(): bool
    {
        if (!$this->active) {
            return false;
        }

        // All equipment must be 'available'
        return $this->isComplete();
    }

    /**
     * Get the current status of the set based on equipment conditions.
     * Returns: 'available', 'rented', 'damaged', 'maintenance', 'incomplete', 'unavailable'
     */
    public function getStatusAttribute(): string
    {
        // Check if set is currently rented
        if ($this->rentals()->whereNull('returned_at')->exists()) {
            return 'rented';
        }

        $equipmentStatuses = $this->equipments->pluck('status');

        // Check for damaged equipment (highest priority)
        if ($equipmentStatuses->contains('damaged')) {
            return 'damaged';
        }

        // Check for maintenance/under service
        if ($equipmentStatuses->contains('maintenance') || $equipmentStatuses->contains('under_service')) {
            return 'maintenance';
        }

        // Check for rented equipment (incomplete set)
        if ($equipmentStatuses->contains('rented')) {
            return 'incomplete';
        }

        // All equipment available
        if ($equipmentStatuses->every(fn($status) => $status === 'available')) {
            return 'available';
        }

        // Retired or other statuses
        return 'unavailable';
    }

    /**
     * Get equipment by specific status.
     */
    public function equipmentByStatus(string $status)
    {
        return $this->equipments()->where('status', $status)->get();
    }

    /**
     * Scope to search by barcode
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }
}
