<?php

namespace Database\Seeders;

<<<<<<< HEAD
=======
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
=======
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
        ]);
    }
}
