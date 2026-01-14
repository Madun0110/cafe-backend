<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN UTAMA
        |--------------------------------------------------------------------------
        */
        Admin::updateOrCreate(
            ['email' => 'admin@kopiku.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',   // role admin
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | KASIR
        |--------------------------------------------------------------------------
        */
        Admin::updateOrCreate(
            ['email' => 'kasir@kopiku.com'],
            [
                'name'     => 'Kasir KopiKu',
                'password' => Hash::make('kasir123'),
                'role'     => 'kasir',   // role kasir
            ]
        );
    }
}
