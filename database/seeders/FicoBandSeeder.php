<?php

namespace Database\Seeders;

use App\Models\FicoBand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FicoBandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bands = [
            ['fico_range' => '660-679', 'fico_min' => 660, 'fico_max' => 679],
            ['fico_range' => '680-699', 'fico_min' => 680, 'fico_max' => 699],
            ['fico_range' => '700-719', 'fico_min' => 700, 'fico_max' => 719],
            ['fico_range' => '720-739', 'fico_min' => 720, 'fico_max' => 739],
            ['fico_range' => '740+', 'fico_min' => 740, 'fico_max' => 759],
            ['fico_range' => '760+', 'fico_min' => 760, 'fico_max' => 850],
        ];

        FicoBand::upsert($bands, ['fico_range', 'fico_min', 'fico_max']);
    }
}
