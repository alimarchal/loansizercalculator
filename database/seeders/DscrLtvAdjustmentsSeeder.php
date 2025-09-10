<?php

namespace Database\Seeders;

use App\Models\Dscr;
use App\Models\LtvRatio;
use App\Models\DscrRanges;
use Illuminate\Database\Seeder;
use App\Models\DscrLtvAdjustments;
use Illuminate\Support\Facades\DB;

class DscrLtvAdjustmentsSeeder extends Seeder
{
    /**
     * Seeds the DSCR × LTV adjustment grid.
     * Prereqs:
     *  - dscrs table seeded (labels like '1.20+', '1.10–1.20', '1.00–1.10', '0.80–0.99')
     *  - ltv_ratios table seeded (ratio_range like '50% LTV or less', '55% LTV', ... '80% LTV')
     *  - pivot table: dscr_ltv_adjustments (dscr_id, ltv_ratio_id, adjustment_pct)
     */
    public function run(): void
    {
        // IDs keyed by labels (must be seeded first)
        $ltvId = LtvRatio::pluck('id', 'ratio_range'); // ['50% LTV or less' => 1, ...]
        $dscrId = DscrRanges::pluck('id', 'dscr_range'); // ['1.20+' => 1, ...]           // ['1.20+' => 1, '1.10–1.20' => 2, ...]

        // TODO: Replace these with your real numbers.
        // Use decimal percents (0.125 means 0.125%). Use null for N/A cells.
        $grid = [
            '1.20+' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => null,
            ],
            '1.10–1.20' => [
                '50% LTV or less' => 0.125,
                '55% LTV' => 0.125,
                '60% LTV' => 0.125,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => null,
            ],
            '1.00–1.10' => [
                '50% LTV or less' => 0.250,
                '55% LTV' => 0.250,
                '60% LTV' => 0.250,
                '65% LTV' => 0.375,
                '70% LTV' => 0.500,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            '0.80–0.99' => [
                '50% LTV or less' => 0.500,
                '55% LTV' => 0.500,
                '60% LTV' => null,
                '65% LTV' => null,
                '70% LTV' => null,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
        ];

        $rows = [];
        foreach ($grid as $dscrLabel => $cols) {
            $dId = $dscrId[$dscrLabel] ?? null;
            if (!$dId) {
                continue;
            }

            foreach ($cols as $ltvLabel => $pct) {
                $lrId = $ltvId[$ltvLabel] ?? null;
                if (!$lrId) {
                    continue;
                }

                // Skip if adjustment percentage is null
                if ($pct === null) {
                    continue;
                }

                $rows[] = [
                    'dscr_range_id' => $dId,
                    'ltv_ratio_id' => $lrId,       // rename to 'ltv_range_id' if your pivot uses that
                    'adjustment_pct' => $pct,        // decimal percent; null => N/A
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('dscr_ltv_adjustments')->upsert(
            $rows,
            ['dscr_range_id', 'ltv_ratio_id'],              // change if your FK name differs
            ['adjustment_pct', 'updated_at']
        );
    }
}
