<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentExamNumber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'exam_no',
        'is_current',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'issued_at' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function courseResults()
{
    return $this->hasMany(StudentCourseResult::class);
}

public function examResults()
{
    return $this->hasMany(ExamResult::class);
}
}