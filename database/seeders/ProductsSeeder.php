<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'category_id' => 1,
                'name' => 'Nasi Goreng',
                'description' => 'Nasi Goreng',
                'price' => 10000,
                'is_available' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Mie Goreng',
                'description' => 'Mie Goreng',
                'price' => 10000,
                'is_available' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Es Teh',
                'description' => 'Es Teh',
                'price' => 10000,
                'is_available' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Es Jeruk',
                'description' => 'Es Jeruk',
                'price' => 10000,
                'is_available' => true
            ]
        ];

        Products::insert($data);
    }
}
