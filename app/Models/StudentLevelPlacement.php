<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentLevelPlacement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_program_enrollment_id',
        'program_level_id',
        'academic_year_id',
        'start_date',
        'end_date',
        'is_current',
        'progression_status',
        'placement_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function enrollment()
    {
        return $this->belongsTo(StudentProgramEnrollment::class, 'student_program_enrollment_id');
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->hasOneThrough(
            Student::class,
            StudentProgramEnrollment::class,
            'id',
            'id',
            'student_program_enrollment_id',
            'student_id'
        );
    }

    public function examResults()
{
    return $this->hasMany(ExamResult::class);
}
}