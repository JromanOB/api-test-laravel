<?php 

namespace App\Http\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Services\Auth\LdapAuthenticationService;

class AuthService
{
    public function __construct(
        private readonly LdapAuthenticationService $ldapAuthenticationService,
        private readonly JwtTokenService $jwtTokenService,
    ) {}

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 'error'
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function loginWithLdap(
        string $username,
        string $password
    ): array {
        $ldapUser = $this
            ->ldapAuthenticationService
            ->authenticate(
                username: $username,
                password: $password,
            );

        $ldapUsername = $ldapUser
            ->getFirstAttribute('samaccountname');

        if (! is_string($ldapUsername) || $ldapUsername === '') {
            throw new \RuntimeException(
                'El usuario LDAP no posee un sAMAccountName válido.'
            );
        }

        $identifier = $ldapUser->getConvertedGuid()
            ?? $ldapUser->getDn();

        if (! is_string($identifier) || $identifier === '') {
            throw new \RuntimeException(
                'No se pudo obtener el identificador del usuario LDAP.'
            );
        }

        $token = $this
            ->jwtTokenService
            ->createForLdapUser(
                identifier: $identifier,
                username: $ldapUsername,
            );

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this
                ->jwtTokenService
                ->expiresInSeconds(),
            'user' => [
                'username' => $ldapUsername,
            ],
        ];
    }

    public function validateToken() {
        try {
            JWTAuth::parseToken()->authenticate();

            return response()->json([
                'valid' => true
            ], 200);

        } catch (JWTException $exception) {
            return response()->json([
                'valid' => false
            ], 401);
        }
    }

    public function me() {
        return response()->json(Auth::user());
    }

    public function logout() {
        Auth::logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh() {
        return response()->json([
            'access_token' => Auth::refresh(),
            'token_type' => 'Bearer'
        ]);
    }
}