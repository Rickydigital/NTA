<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function studentExamNumbers()
    {
        return $this->hasMany(StudentExamNumber::class);
    }

    public function intakeEnrollments()
{
    return $this->hasMany(StudentProgramEnrollment::class, 'intake_academic_year_id');
}

public function levelPlacements()
{
    return $this->hasMany(StudentLevelPlacement::class);
}
}
