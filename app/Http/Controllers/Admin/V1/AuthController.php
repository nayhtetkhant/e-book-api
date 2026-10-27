<?php

namespace App\Http\Controllers\Admin\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\V1\AuthLoginRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        public AuthService $authService,
    ) {}

    public function login(AuthLoginRequest $request)
    {
        return response()->json(
            ['token' => $this->authService->login($request->validated()),
                'role' => UserRole::ADMIN->value],
        );
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
