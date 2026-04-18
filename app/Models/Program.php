<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function levels()
{
    return $this->hasMany(ProgramLevel::class);
}

public function enrollments()
{
    return $this->hasMany(StudentProgramEnrollment::class);
}

public function students()
{
    return $this->hasMany(Student::class);
}

public function progressionRules()
{
    return $this->hasMany(ProgressionRule::class);
}

}