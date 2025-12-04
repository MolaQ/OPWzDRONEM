<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id',
        'flight_hours_required', 'sim_hours_required', 'require_lab', 'status'
    ];

    protected $casts = [
        'require_lab' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function unitProgress()
    {
        return $this->hasMany(StudentUnitProgress::class);
    }
}
