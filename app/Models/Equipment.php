<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'barcode',
        'serial_number',
        'name',
        'model',
        'category',
        'status',
        'description',
        'purchase_date',
        'warranty_expiry_date',
        'cost',
        'location',
        'condition_status',
        'last_maintenance_date',
        'next_maintenance_due',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
        'last_maintenance_date' => 'datetime',
        'next_maintenance_due' => 'datetime',
    ];

    /**
     * Sets this equipment belongs to
     */
    public function equipmentSets(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentSet::class, 'equipment_set_items')
            ->withTimestamps();
    }

    /**
     * Rentals of this equipment
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Notes about this equipment
     */
    public function notes(): HasMany
    {
        return $this->hasMany(EquipmentNote::class)->latest();
    }

    /**
     * Maintenance logs for this equipment
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceLog::class)->latest();
    }

    /**
     * Reservations for this equipment
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(EquipmentReservation::class);
    }

    /**
     * Current active rental
     */
    public function currentRental()
    {
        return $this->rentals()->whereNull('returned_at')->first();
    }

    /**
     * Check if equipment is available for rental
     */
    public function isAvailableForRental(): bool
    {
        return $this->status === 'dostepny';
    }

    /**
     * Scope to search by barcode
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope for available equipment
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'dostepny');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'dostepny' => 'green',
            'wypozyczony' => 'blue',
            'w_uzyciu' => 'cyan',
            'konserwacja' => 'yellow',
            'uszkodzony' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Polish
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'dostepny' => 'Dostępny',
            'wypozyczony' => 'Wypożyczony',
            'w_uzyciu' => 'W użyciu',
            'konserwacja' => 'Konserwacja',
            'uszkodzony' => 'Uszkodzony',
            default => 'Nieznany',
        };
    }
}
