<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProgramEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'program_id',
        'intake_academic_year_id',
        'enrollment_date',
        'completion_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'completion_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function intakeAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'intake_academic_year_id');
    }

    public function levelPlacements()
    {
        return $this->hasMany(StudentLevelPlacement::class);
    }

    
}