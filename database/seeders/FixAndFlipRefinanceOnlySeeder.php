<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FixAndFlipRefinanceOnlySeeder extends Seeder
{
    /**
     * Run only the Fix and Flip Refinance seeder for testing
     */
    public function run(): void
    {
        $this->call([
            FixAndFlipRefinanceSeeder::class,
        ]);
    }
}