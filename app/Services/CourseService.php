<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\StudentCourse;
use App\Models\StudentUnitProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function enrollStudent(User $user, Course $course, ?int $flightHoursRequired = null, ?int $simHoursRequired = null, ?bool $requireLab = null): StudentCourse
    {
        $flight = $flightHoursRequired ?? $course->default_flight_hours_required;
        $sim = $simHoursRequired ?? $course->default_sim_hours_required;
        $reqLab = $requireLab ?? $course->require_lab;

        return StudentCourse::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'flight_hours_required' => $flight,
                'sim_hours_required' => $sim,
                'require_lab' => $reqLab,
                'status' => 'active',
            ]
        );
    }

    public function assignUnit(StudentCourse $studentCourse, CourseUnit $unit, int $assignedByUserId, ?string $notes = null): StudentUnitProgress
    {
        // Enforce license for practice_flight
        if ($unit->type === 'practice_flight') {
            $user = User::find($studentCourse->user_id);
            if (!$user || !$this->hasActivePilotLicense($user)) {
                throw new \RuntimeException('Uczeń nie posiada aktywnej licencji pilota.');
            }
        }

        return StudentUnitProgress::updateOrCreate(
            ['student_course_id' => $studentCourse->id, 'course_unit_id' => $unit->id],
            [
                'status' => 'assigned',
                'assigned_at' => now(),
                'assigned_by' => $assignedByUserId,
                'notes' => $notes,
            ]
        );
    }

    public function completeUnit(StudentCourse $studentCourse, CourseUnit $unit, int $completedByUserId, ?string $notes = null): StudentUnitProgress
    {
        $progress = StudentUnitProgress::firstOrCreate(
            ['student_course_id' => $studentCourse->id, 'course_unit_id' => $unit->id],
            ['status' => 'assigned']
        );

        // Enforce license for practice_flight
        if ($unit->type === 'practice_flight') {
            $user = User::find($studentCourse->user_id);
            if (!$user || !$this->hasActivePilotLicense($user)) {
                throw new \RuntimeException('Uczeń nie posiada aktywnej licencji pilota.');
            }
        }

        $progress->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => $completedByUserId,
            'notes' => $notes,
        ]);

        return $progress;
    }

    /**
     * Bulk assign by student barcodes.
     * @param CourseUnit $unit
     * @param array<int,string> $studentBarcodes
     * @param int $assignedByUserId
     * @return array{success:int,failed:int,errors:array<int,string>}
     */
    public function bulkAssign(CourseUnit $unit, array $studentBarcodes, int $assignedByUserId): array
    {
        $success = 0; $failed = 0; $errors = [];
        foreach ($studentBarcodes as $barcode) {
            $user = User::where('barcode', $barcode)->first();
            if (!$user) { $failed++; $errors[] = "Nie znaleziono ucznia: {$barcode}"; continue; }
            $studentCourse = StudentCourse::where('user_id', $user->id)->where('course_id', $unit->course_id)->first();
            if (!$studentCourse) {
                $studentCourse = $this->enrollStudent($user, $unit->course);
            }
            try {
                $this->assignUnit($studentCourse, $unit, $assignedByUserId);
                $success++;
            } catch (\Throwable $e) {
                $failed++; $errors[] = $user->name . ': ' . $e->getMessage();
            }
        }
        return compact('success','failed','errors');
    }

    protected function hasActivePilotLicense(User $user): bool
    {
        // Active license: license_expiry_date > now()
        if (method_exists($user, 'getAttribute')) {
            $expiry = $user->license_expiry_date ?? null;
        } else {
            $expiry = null;
        }
        if (!$expiry) { return false; }
        try {
            return \Carbon\Carbon::parse($expiry)->isFuture();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
