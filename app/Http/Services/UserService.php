<?php

namespace App\Http\Services;

use App\Models\User;

class UserService
{
    public function createUser(array $data) {
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