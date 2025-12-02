<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'barcode',
        'name',
        'model',
        'category',
        'status',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to search by barcode
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'green',
            'in_use' => 'blue',
            'maintenance' => 'yellow',
            'damaged' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Polish
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available' => 'Dostępny',
            'in_use' => 'W użyciu',
            'maintenance' => 'Konserwacja',
            'damaged' => 'Uszkodzony',
            default => 'Nieznany',
        };
    }
}
