<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'student_exam_number_id',
        'student_level_placement_id',
        'exam_session_id',
        'gpa_classification_id',
        'total_courses',
        'total_grade_points',
        'gpa',
        'final_comment',
        'progression_decision',
        'is_published',
        'published_at',
        'published_by',
        'generated_at',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'total_courses' => 'integer',
            'total_grade_points' => 'decimal:2',
            'gpa' => 'decimal:2',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'generated_at' => 'datetime',
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

    public function studentLevelPlacement()
    {
        return $this->belongsTo(StudentLevelPlacement::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function gpaClassification()
    {
        return $this->belongsTo(GpaClassification::class);
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}