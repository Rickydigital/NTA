<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grade_code',
        'grade_point',
        'min_score',
        'max_score',
        'comment_label',
        'result_status',
        'affects_gpa',
        'is_pass_grade',
    ];

    protected function casts(): array
    {
        return [
            'grade_point' => 'decimal:2',
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'affects_gpa' => 'boolean',
            'is_pass_grade' => 'boolean',
        ];
    }
}