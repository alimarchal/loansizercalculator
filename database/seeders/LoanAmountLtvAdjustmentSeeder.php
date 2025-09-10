<?php

namespace Database\Seeders;

use App\Models\LoanAmount;
use App\Models\LtvRange;
use App\Models\LtvRatio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanAmountLtvAdjustmentSeeder extends Seeder
{
    /**
     * Seed the Loan Amount × LTV adjustment grid.
     * Assumes:
     *  - loan_amounts table with a 'label' column (e.g., "50,000 - 99,999")
     *  - ltv_ranges  table with 'label' (e.g., "50% LTV or less", "55% LTV", ... "80% LTV")
     *  - pivot table: loan_amount_ltv_adjustments (loan_amount_id, ltv_range_id, adjustment_pct)
     */
    public function run(): void
    {
        // Fetch IDs keyed by labels (both must be seeded first)
        $ltvId = LtvRatio::pluck('id', 'ratio_range');    // ['50% LTV or less' => 1, '55% LTV' => 2, ...]
        $amountId = LoanAmount::pluck('id', 'amount_range');  // ['50,000 - 99,999' => 1, '100,000 - 249,999' => 2, ...]

        // === FILL YOUR GRID HERE ===
        // Put your real adjustments (decimal percent; NULL means N/A).
        // The left keys must match your LoanAmount labels; the inner keys must match your LtvRange labels.
        $grid = [
            '50,000 - 99,999' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
            '100,000 - 249,999' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
            '250,000 - 499,999' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
            '500,000 - 999,999' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
            '1,000,000 - 1,499,999' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
            '1,500,000 - 3,000,000' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
        ];

        $rows = [];
        foreach ($grid as $amountLabel => $cols) {
            $laId = $amountId[$amountLabel] ?? null;
            if (!$laId) {
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
                    'loan_amount_id' => $laId,
                    'ltv_ratio_id' => $lrId,
                    'adjustment_pct' => $pct,  // e.g. 0.125 for 0.125%
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('loan_amount_ltv_adjustments')->upsert(
            $rows,
            ['loan_amount_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
