<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'name',
        'code',
        'level_number',
        'sort_order',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function students()
{
    return $this->hasMany(Student::class);
}

public function placements()
{
    return $this->hasMany(StudentLevelPlacement::class);
}
}