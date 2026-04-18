<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_level_id',
        'code',
        'name',
        'credit_hours',
        'description',
    ];

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class);
    }

    public function courseResults()
{
    return $this->hasMany(StudentCourseResult::class);
}
}