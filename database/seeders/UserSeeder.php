<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        foreach ([
            ['Admin', 'admin@example.com', UserRole::ADMIN],
            ['Demo Reader', 'reader@example.com', UserRole::USER],
            ['Pending Reader', 'pending.reader@example.com', UserRole::USER],
            ['Failed Reader', 'failed.reader@example.com', UserRole::USER],
        ] as [$name, $email, $role]) {
            User::firstOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password'),
                'role' => $role,
                'email_verified_at' => now(),
            ]);
        }
    }
}
