<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee'],
            ['name' => 'Non Coffee'],
            ['name' => 'Snack'],
            ['name' => 'Food'],
        ];

        foreach ($categories as $category) {
            \App\Models\Categories::create($category);
        }
    }
}
