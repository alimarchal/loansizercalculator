<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LtvRatioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ltv_ratios')->insert([
            ['ltv_ratio_name' => '50% LTV (or less)', 'ltv_min' => 0, 'ltv_max' => 50, 'display_order' => 1],
            ['ltv_ratio_name' => '55% LTV', 'ltv_min' => 51, 'ltv_max' => 55, 'display_order' => 2],
            ['ltv_ratio_name' => '60% LTV', 'ltv_min' => 56, 'ltv_max' => 60, 'display_order' => 3],
            ['ltv_ratio_name' => '65% LTV', 'ltv_min' => 61, 'ltv_max' => 65, 'display_order' => 4],
            ['ltv_ratio_name' => '70% LTV', 'ltv_min' => 66, 'ltv_max' => 70, 'display_order' => 5],
            ['ltv_ratio_name' => '75% LTV', 'ltv_min' => 71, 'ltv_max' => 75, 'display_order' => 6],
            ['ltv_ratio_name' => '80% LTV', 'ltv_min' => 76, 'ltv_max' => 80, 'display_order' => 7],
        ]);
    }
}
