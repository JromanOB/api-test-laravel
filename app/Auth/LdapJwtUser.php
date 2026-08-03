<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class LdapJwtUser implements Authenticatable, JWTSubject
{
    public function __construct(
        private readonly string $identifier,
        public readonly string $username,
    ) {
    }

    public function getJWTIdentifier(): string
    {
        return $this->identifier;
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'username' => $this->username,
            'auth_source' => 'ldap',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'identifier';
    }

    public function getAuthIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // No se utiliza.
    }

    public function getRememberTokenName(): string
    {
        return '';
    }
}