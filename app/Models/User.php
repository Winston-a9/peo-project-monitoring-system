<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'division',   
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Returns true if this user is a super admin.
     * Super admin = role is 'admin' AND division is null (no division restriction).
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && $this->division === null;
    }

    /**
     * Returns true if this user is a division-scoped admin.
     * Division admin = role is 'admin' AND division is set.
     */
    public function isDivisionAdmin(): bool
    {
        return $this->role === 'admin' && $this->division !== null;
    }
}