<?php

namespace App\Http\Controllers\User\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\V1\AuthLoginRequest;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        public AuthService $authService,
    )
    {
    }

    public function login(AuthLoginRequest $request)
    {
        return response()->json(
            ['token' => $this->authService->login($request->validated()),
            'role' => UserRole::USER->value]
        );
    }
}
