<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data) {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return $user;
    }

    public function updateUser(User $user, array $data) {
        $user->update($data);
        return $user;
    }

    public function getAllUsers(string $role) {
        return User::query()->where('role' , $role)->get();
    }

    public function getUserById(int $id) {
        return User::find($id);
    }
}