<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Database\Seeders\PostSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(3)->create();

        User::factory()->create([
            'email' => 'admin@example.com',
            'username' => 'admin',
            'firstname' => 'Admin',
            'lastname' => 'Test',
        ]);

        $this->call([
            PostSeeder::class,
        ]);
    }
}
