<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentCourseResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'student_exam_number_id',
        'course_id',
        'exam_session_id',
        'grade_id',
        'raw_score',
        'grade_point_snapshot',
        'comment_snapshot',
        'entered_by',
        'approved_by',
        'approved_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'raw_score' => 'decimal:2',
            'grade_point_snapshot' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentExamNumber()
    {
        return $this->belongsTo(StudentExamNumber::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}