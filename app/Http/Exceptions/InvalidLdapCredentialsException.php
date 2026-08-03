<?php

namespace App\Http\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

class InvalidLdapCredentialsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Credenciales incorrectas');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 401);
    }
}