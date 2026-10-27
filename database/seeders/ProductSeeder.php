<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        foreach ([
            ['Laravel in Practice', 'Build maintainable APIs with Laravel, Eloquent, and automated tests.', 29],
            ['Modern React Patterns', 'A practical guide to components, state, and reusable React interfaces.', 25],
            ['TypeScript Essentials', 'Write safer JavaScript with types, generics, and practical examples.', 19],
            ['Database Design Handbook', 'Model relational data and improve queries with indexes and constraints.', 24],
            ['Practical API Security', 'Protect APIs with authentication, authorization, and input validation.', 32],
            ['Testing Web Applications', 'Build confidence with unit, integration, and end-to-end testing.', 22],
            ['The Developer Career Guide', 'Improve technical communication, code reviews, and engineering habits.', 15],
            ['Git for Everyday Work', 'Learn branching, collaboration, and recovery through common workflows.', 12],
            ['Designing Digital Products', 'Turn product ideas into accessible and useful customer experiences.', 27],
            ['Getting Started with E-books', 'A free introduction to choosing and organizing your digital library.', 0],
        ] as [$title, $description, $price]) {
            Product::firstOrCreate(['title' => $title], [
                'description' => $description,
                'price' => $price,
                'file_path' => null,
            ]);
        }
    }
}
