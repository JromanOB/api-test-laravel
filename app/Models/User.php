<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'username',
        'email',
        'phonenumber',
        'fullname',
        'is_active',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function getJWTIdentifier() { 
        return $this->getKey(); 
    }

    public function getJWTCustomClaims() { 
        return []; 
    }

    public const PAGINATE = 10;
}
