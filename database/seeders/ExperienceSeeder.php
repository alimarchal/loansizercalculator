<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['experiences_range' => '0', 'min_experience' => 0, 'max_experience' => 0],
            ['experiences_range' => '1-2', 'min_experience' => 1, 'max_experience' => 2],
            ['experiences_range' => '3-4', 'min_experience' => 3, 'max_experience' => 4],
            ['experiences_range' => '5-9', 'min_experience' => 5, 'max_experience' => 9],
            ['experiences_range' => '10+', 'min_experience' => 10, 'max_experience' => 50],
        ];

        Experience::upsert($data, ['experiences_range', 'min_experience', 'max_experience']);
    }


}
