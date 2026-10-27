<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Demo data is only seeded in local and testing environments.');

            return;
        }

        DB::transaction(function (): void {
            $this->call([
                UserSeeder::class,
                ProductSeeder::class,
                OrderSeeder::class,
            ]);
        });
    }
}
