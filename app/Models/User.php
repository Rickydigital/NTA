<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'second_name',
        'last_name',
        'gender',
        'phone_no',
        'username',
        'email',
        'password',
        'is_active',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' .
            ($this->second_name ? $this->second_name . ' ' : '') .
            $this->last_name
        );
    }
}