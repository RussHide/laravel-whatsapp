<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory(9)->create();

         User::create([
             'name' => 'Fabian Hide',
             'email' => 'fabian@gmail.com',
             'password' => bcrypt('12345678'),
         ]);
    }
}
