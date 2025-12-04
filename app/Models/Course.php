<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'active', 'default_flight_hours_required', 'default_sim_hours_required', 'require_lab'];

    protected $casts = [
        'active' => 'boolean',
        'require_lab' => 'boolean',
    ];

    public function units()
    {
        return $this->hasMany(CourseUnit::class);
    }

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }
}
