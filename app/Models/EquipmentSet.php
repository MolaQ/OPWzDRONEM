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
            ->where('status', '!=', 'dostepny')
            ->doesntExist();
    }

    /**
     * Get missing equipment (borrowed separately)
     */
    public function missingEquipment()
    {
        return $this->equipments()
            ->where('status', '!=', 'dostepny')
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

        // All equipment must be 'dostepny'
        return $this->isComplete();
    }

    /**
     * Scope to search by barcode
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }
}
