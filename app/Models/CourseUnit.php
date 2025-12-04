<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'type', 'title', 'description', 'is_required', 'duration_minutes',
        'parent_id', 'category', 'position'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'duration_minutes' => 'integer',
        'position' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CourseUnit::class, 'parent_id')->orderBy('position');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class, 'course_unit_id');
    }

    public function approvedMaterials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class, 'course_unit_id')->where('is_approved', true);
    }

    public function scopeCategory($query, ?string $category)
    {
        if ($category) {
            $query->where('category', $category);
        }
        return $query;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
