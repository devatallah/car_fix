<?php

namespace Database\Seeders;

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
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@email.com',
            'mobile' => '1234567890',
            'password' => bcrypt('123456'),
        ]);
        $this->call([AdminSeeder::class]);
    }
}
