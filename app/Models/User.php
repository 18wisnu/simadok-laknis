<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'is_active',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class);
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }
}
