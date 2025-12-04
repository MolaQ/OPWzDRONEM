<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'group_id', 'start_date', 'end_date',
        'flight_hours_required', 'sim_hours_required', 'require_lab', 'status'
    ];

    protected $casts = [
        'require_lab' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function unitProgress()
    {
        return $this->hasMany(StudentUnitProgress::class);
    }
}
