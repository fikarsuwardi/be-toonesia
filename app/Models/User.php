<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Define which fields are mass assignable
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Hide these fields from JSON output
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Add type casting for certain fields
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
