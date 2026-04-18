<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressionRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'from_program_level_id',
        'to_program_level_id',
        'min_gpa_required',
        'max_failed_courses_allowed',
        'blocked_by_disco',
        'blocked_by_fail_oral',
        'requires_manual_approval',
        'decision',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'min_gpa_required' => 'decimal:2',
            'max_failed_courses_allowed' => 'integer',
            'blocked_by_disco' => 'boolean',
            'blocked_by_fail_oral' => 'boolean',
            'requires_manual_approval' => 'boolean',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function fromLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'from_program_level_id');
    }

    public function toLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'to_program_level_id');
    }
}