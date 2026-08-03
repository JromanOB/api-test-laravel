<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(Request $request)
    {
        return $this->authService->login($request);
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