<?php

namespace App\Http\Services\Auth;

use App\Http\Exceptions\InvalidLdapCredentialsException;
use LdapRecord\Models\ActiveDirectory\User;

class LdapAuthenticationService
{
    public function authenticate(
        string $username,
        string $password
    ): User {
        $ldapUser = User::where(
            'samaccountname',
            '=',
            $username
        )->first();

        if (! $ldapUser) {
            throw new InvalidLdapCredentialsException();
        }

        $isAuthenticated = $ldapUser
            ->getConnection()
            ->auth()
            ->attempt(
                $ldapUser->getDn(),
                $password
            );

        if (! $isAuthenticated) {
            throw new InvalidLdapCredentialsException();
        }

        return $ldapUser;
    }

    public function findByUsername(string $username): ?array
    {
        $ldapUser = User::where(
            'samaccountname',
            '=',
            $username
        )->first();

        if (! $ldapUser) {
            return null;
        }

        return [
            'username' => $ldapUser->getFirstAttribute('samaccountname'),
            'email' => $ldapUser->getFirstAttribute('mail'),
            'fullname' => $ldapUser->getFirstAttribute('displayname'),
        ];
    }
}