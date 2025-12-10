<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMaintenanceLog extends Model
{
    use HasFactory;

    protected $table = 'equipment_maintenance_logs';

    protected $fillable = [
        'equipment_id',
        'type',
        'description',
        'findings',
        'actions_taken',
        'cost',
        'performed_by_user_id',
        'performed_at',
        'next_maintenance_recommended',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'next_maintenance_recommended' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Equipment this maintenance belongs to
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * User who performed the maintenance
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    /**
     * Get maintenance type label in Polish
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'preventive_maintenance' => 'Konserwacja preventywna',
            'repair' => 'Naprawa',
            'inspection' => 'Inspekcja',
            'calibration' => 'Kalibracja',
            'battery_replacement' => 'Wymiana baterii',
            'cleaning' => 'Czyszczenie',
            'software_update' => 'Aktualizacja oprogramowania',
            'other' => 'Inne',
            default => $this->type,
        };
    }
}
