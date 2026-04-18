<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'name',
        'session_type',
        'start_date',
        'end_date',
        'is_result_entry_open',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_result_entry_open' => 'boolean',
            'is_published' => 'boolean',
        ];
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