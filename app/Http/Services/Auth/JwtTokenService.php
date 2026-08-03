<?php

namespace App\Http\Services\Auth;

use App\Auth\LdapJwtUser;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtTokenService
{
    public function createForLdapUser(
        string $identifier,
        string $username
    ): string {
        $jwtUser = new LdapJwtUser(
            identifier: $identifier,
            username: $username,
        );

        return JWTAuth::fromUser($jwtUser);
    }

    public function expiresInSeconds(): int
    {
        return JWTAuth::factory()->getTTL() * 60;
    }
}