<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data) {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return $user;
    }

    public function updateUser(User $user, array $data) {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return $user;
    }

    public function getAllUsers() {
        if (Auth::user()->role === 'admin') {
            return User::all();
        }
        throw new \Exception('You are not authorized to access this resource');
    }

    public function getUser(User $user) {
        if (Auth::user()->role === 'admin' || Auth::user()->id === $user->id) {
            return $user;
        }
        throw new \Exception('You are not authorized to access this resource');
    }
}