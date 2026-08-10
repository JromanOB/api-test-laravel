<?php

namespace App\Http\Controllers;

use App\Http\Services\Auth\AuthService;
use App\Http\Requests\Auth\LdapLoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LdapLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $result = $this->authService->login(
            username: $credentials['username'],
            password: $credentials['password'],
        );

        return $result;
    }

    public function validateToken()
    {
        return $this->authService->validateToken();
    }

    public function me()
    {
        return $this->authService->me();
    }

    public function logout()
    {
        return $this->authService->logout();
    }

    public function refresh()
    {
        return $this->authService->refresh();
    }
}