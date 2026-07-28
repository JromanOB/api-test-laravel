<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'username',
        'phonenumber',
        'email',
        'password'
    ];

    public function getJWTIdentifier() { 
        return $this->getKey(); 
    }

    public function getJWTCustomClaims() { 
        return []; 
    }

    public const PAGINATE = 10;
}
