<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed roles and users first
        $this->call([
            RoleSeeder::class,
        ]);

        // Seed lookup tables
        $this->call([
            LoanTypeSeeder::class,
            TransactionTypeSeeder::class,
            PropertyTypeSeeder::class,
            StateSeeder::class,
        ]);
    }
}
