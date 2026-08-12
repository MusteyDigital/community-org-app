<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organizationsCreated()
    {
        return $this->hasMany(Organization::class, 'created_by');
    }

    public function membership()
    {
        return $this->hasOne(Member::class);
    }

    public function approvedMembership()
    {
        return $this->hasOne(Member::class)->where('status', 'approved');
    }

    public function hasOrganization(): bool
    {
        return $this->approvedMembership()->exists();
    }
}

