<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUnitProgress extends Model
{
    use HasFactory;
    protected $table = 'student_unit_progresses';

    protected $fillable = ['student_course_id','course_unit_id','status','assigned_at','completed_at','assigned_by','completed_by','notes'];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function studentCourse()
    {
        return $this->belongsTo(StudentCourse::class);
    }

    public function unit()
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }
}
