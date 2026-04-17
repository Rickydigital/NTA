<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'program_level_id',
        'reg_no',
        'first_name',
        'second_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone_no',
        'email',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class);
    }

    public function examNumbers()
    {
        return $this->hasMany(StudentExamNumber::class);
    }

    public function programEnrollments()
{
    return $this->hasMany(StudentProgramEnrollment::class);
}

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->second_name,
            $this->last_name,
        ])));
    }
}