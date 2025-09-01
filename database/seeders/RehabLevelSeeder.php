<?php

namespace Database\Seeders;

use App\Models\RehabLevel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RehabLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['name' => 'LIGHT REHAB'],
            ['name' => 'MODERATE REHAB'],
            ['name' => 'HEAVY REHAB'],
            ['name' => 'EXTENSIVE REHAB'],
        ];

        RehabLevel::upsert($levels, ['name']);
    }
}
