<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GpaClassification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'min_gpa',
        'max_gpa',
        'classification_code',
        'final_comment',
        'progression_action',
        'priority_order',
    ];

    protected function casts(): array
    {
        return [
            'min_gpa' => 'decimal:2',
            'max_gpa' => 'decimal:2',
            'priority_order' => 'integer',
        ];
    }

    public function examResults()
{
    return $this->hasMany(ExamResult::class);
}
}