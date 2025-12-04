<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseMaterial extends Model
{
    protected $table = 'course_unit_materials';

    protected $fillable = [
        'course_unit_id',
        'title',
        'description',
        'type',
        'url_or_file_path',
        'uploaded_by_id',
        'is_approved',
        'rejection_reason',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Relacja: materiał należy do zagadnienia kursu
     */
    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    /**
     * Relacja: materiał przesłany przez użytkownika
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    /**
     * Relacja: komentarze do materiału
     */
    public function comments(): HasMany
    {
        return $this->hasMany(MaterialComment::class, 'course_material_id');
    }

    /**
     * Zwróć pełną ścieżkę do pliku jeśli to PDF
     */
    public function getFileUrl(): ?string
    {
        if ($this->type === 'pdf') {
            return asset('storage/' . $this->url_or_file_path);
        }
        return $this->url_or_file_path;
    }

    /**
     * Sprawdź czy materiał to plik czy link
     */
    public function isFile(): bool
    {
        return $this->type === 'pdf';
    }

    public function isLink(): bool
    {
        return in_array($this->type, ['video_link', 'external_link']);
    }
}
