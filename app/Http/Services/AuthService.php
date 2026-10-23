<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService {
    public function Login(array $data) {
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                return $user->createApiToken('User')->plainTextToken;
            }
        }

        throw new \Exception('Invalid Credentials');
    }
}