<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LdapLoginRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Services\Auth\AuthService;

class LdapAuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function login(
        LdapLoginRequest $request
    ): JsonResponse {
        $credentials = $request->validated();

        $result = $this->authService->loginWithLdap(
            username: $credentials['username'],
            password: $credentials['password'],
        );

        return response()->json($result);
    }
}