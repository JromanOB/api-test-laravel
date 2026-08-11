<?php 

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Services\Auth\LdapAuthenticationService;
use App\Http\Services\User\UserService;
use App\Http\Utils\RateLimiter\Limitator;

class AuthService
{
    public function __construct(
        private readonly LdapAuthenticationService $ldapAuthenticationService,
        private readonly UserService $userService,
        private readonly Limitator $limitator,
    ) {}

    // public function login(array $data): JsonResponse {
    //      $ldapUser = $this
    //          ->ldapAuthenticationService
    //          ->authenticate(
    //              username: $data['username'],
    //              password: $data['password'],
    //          );

    //      $ldapUsername = $ldapUser
    //          ->getFirstAttribute('samaccountname');

    //      if (! is_string($ldapUsername) || $ldapUsername === '') {
    //          throw new \RuntimeException(
    //              'El usuario LDAP no posee un sAMAccountName válido.'
    //          );
    //      }

    //      $user = $this->userService->findByUsername($ldapUsername);

    //      $token = Auth::login($user);

    //      return response()->json([
    //          'access_token' => $token,
    //          'token_type' => 'Bearer',
    //          'expires_in' => Auth::factory()->getTTL() * 60,
    //          'user' => [
    //              'id' => $user->id,
    //          ],
    //      ]);
    // }

    public function login(array $data): JsonResponse {
        $ip = request()->ip();

        $key = 'login:' . $ip;

        if ($this->limitator->tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Demasiados intentos. Intente nuevamente en '
                    . $this->limitator->availableIn($key)
                    . ' segundos.'
            ], 429);
        }

        try {

            $ldapUser = $this
                ->ldapAuthenticationService
                ->authenticate(
                    username: $data['username'],
                    password: $data['password'],
                );

            $ldapUsername = $ldapUser->getFirstAttribute('samaccountname');

            if (! is_string($ldapUsername) || $ldapUsername === '') {
                throw new \RuntimeException(
                    'El usuario LDAP no posee un sAMAccountName válido.'
                );
            }

            $user = $this->userService->findByUsername($ldapUsername);

            $token = Auth::login($user);

            $this->limitator->clear($key);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
                'user' => ['id' => $user->id],
            ]);

        } catch (\Exception $e) {
            $this->limitator->hit($key);

            throw $e;
        }
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