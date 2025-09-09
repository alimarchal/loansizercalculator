<?php

namespace Database\Seeders;

use App\Models\LtvRatio;
use Illuminate\Database\Seeder;

class LtvRatioSeeder extends Seeder
{
    public function run(): void
    {
        $ltvRatios = [
            ['ratio_range' => '50% LTV (or less)', 'ltv_min' => 0, 'ltv_max' => 50, 'display_order' => 1],
            ['ratio_range' => '55% LTV', 'ltv_min' => 51, 'ltv_max' => 55, 'display_order' => 2],
            ['ratio_range' => '60% LTV', 'ltv_min' => 56, 'ltv_max' => 60, 'display_order' => 3],
            ['ratio_range' => '65% LTV', 'ltv_min' => 61, 'ltv_max' => 65, 'display_order' => 4],
            ['ratio_range' => '70% LTV', 'ltv_min' => 66, 'ltv_max' => 70, 'display_order' => 5],
            ['ratio_range' => '75% LTV', 'ltv_min' => 71, 'ltv_max' => 75, 'display_order' => 6],
            ['ratio_range' => '80% LTV', 'ltv_min' => 76, 'ltv_max' => 80, 'display_order' => 7],
        ];

        foreach ($ltvRatios as $ratio) {
            LtvRatio::firstOrCreate(
                ['ratio_range' => $ratio['ratio_range']],
                $ratio
            );
        }
    }
}
