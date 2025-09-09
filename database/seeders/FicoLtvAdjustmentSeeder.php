<?php

namespace Database\Seeders;

use App\Models\FicoBand;
use App\Models\LtvRange;
use App\Models\LtvRatio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FicoLtvAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        // Map LTV labels -> ids (must be seeded first)
        $ltvId = LtvRatio::pluck('id', 'ratio_range'); // ['50% LTV or less' => 1, '55% LTV' => 2, ...]

        // Map FICO labels -> ids (must be seeded first)
        $ficoId = FicoBand::pluck('id', 'fico_range'); // ['660-679' => 1, '680-699' => 2, ...]

        // Grid from your sheet (percent values, use NULL for N/A)
        $grid = [
            '660-679' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.250,
                '60% LTV' => 0.375,
                '65% LTV' => 0.500,
                '70% LTV' => null,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            '680-699' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.125,
                '60% LTV' => 0.125,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => null,
            ],
            '700-719' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.125,
                '60% LTV' => 0.125,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => 0.450,
            ],
            '720-739' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.125,
                '60% LTV' => 0.125,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => 1.000,
            ],
            '740+' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.125,
                '60% LTV' => 0.125,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => 0.750,
            ],
            '760+' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.125,
                '60% LTV' => 0.125,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => 0.500,
            ],
        ];

        $rows = [];
        foreach ($grid as $ficoLabel => $cols) {
            $fbId = $ficoId[$ficoLabel] ?? null;
            if (!$fbId) {
                continue;
            }

            foreach ($cols as $ltvLabel => $pct) {
                $lrId = $ltvId[$ltvLabel] ?? null;
                if (!$lrId) {
                    continue;
                }

                $rows[] = [
                    'fico_band_id' => $fbId,
                    'ltv_ratio_id' => $lrId,
                    'adjustment_pct' => $pct,  // decimal percent, e.g. 0.125 = 0.125%
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Upsert on the natural unique pair
        DB::table('fico_ltv_adjustments')->upsert(
            $rows,
            ['fico_band_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
