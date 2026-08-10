<?php 

namespace App\Http\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Services\Auth\LdapAuthenticationService;
use App\Http\Services\User\UserService;
use Illuminate\Http\JsonResponse;

class AuthService
{
    public function __construct(
        private readonly LdapAuthenticationService $ldapAuthenticationService,
        private readonly JwtTokenService $jwtTokenService,
        private readonly UserService $userService,
    ) {}

    // public function login(Request $request) {
    //     $credentials = $request->only('email', 'password');

    //     if (! $token = Auth::attempt($credentials)) {
    //         return response()->json([
    //             'message' => 'Unauthorized',
    //             'status' => 'error'
    //         ], 401);
    //     }

    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //         'expires_in' => Auth::factory()->getTTL() * 60
    //     ]);
    // }

    public function login(
        string $username,
        string $password
    ):JsonResponse  {
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

        $user = $this->userService->findByUsername($ldapUsername);
        
        $token = Auth::guard('api')->login($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
            ],
        ]);

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