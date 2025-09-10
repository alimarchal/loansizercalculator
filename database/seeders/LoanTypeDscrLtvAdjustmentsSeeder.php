<?php

namespace Database\Seeders;

use App\Models\LoanType;  // assumes columns: id, name
use App\Models\LtvRatio;  // assumes columns: id, ratio_range
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanTypeDscrLtvAdjustmentsSeeder extends Seeder
{
    /**
     * Seeds the Loan Type × LTV adjustment grid.
     * Prereqs:
     *  - loan_types seeded (pluck by 'name')
     *  - ltv_ratios seeded (pluck by 'ratio_range' — labels must MATCH exactly)
     *  - pivot table: loan_type_dscr_ltv_adjustments (loan_type_id, ltv_ratio_id, adjustment_pct)
     */
    public function run(): void
    {
        // IDs keyed by labels (must exist already)
        $ltvId = LtvRatio::pluck('id', 'ratio_range'); // e.g. ['50% LTV or less' => 1, ...]
        $loanTypeId = LoanType::pluck('id', 'name');        // e.g. ['30 Year Fixed' => 1, ...]

        // Adjust these numbers to your sheet; null means N/A.
        $grid = [
            '30 Year Fixed' => [
                '50% LTV or less' => 0.0000,
                '55% LTV' => 0.0000,
                '60% LTV' => 0.0000,
                '65% LTV' => 0.0000,
                '70% LTV' => 0.0000,
                '75% LTV' => 0.1250,
                '80% LTV' => null,
            ],
            '30 Year IO' => [
                '50% LTV or less' => 0.1250,
                '55% LTV' => 0.1250,
                '60% LTV' => 0.1250,
                '65% LTV' => 0.1250,
                '70% LTV' => 0.2500,
                '75% LTV' => 0.3750,
                '80% LTV' => null,
            ],
            '5/1 ARM' => [
                '50% LTV or less' => 0.1250,
                '55% LTV' => 0.1250,
                '60% LTV' => 0.1250,
                '65% LTV' => 0.2500,
                '70% LTV' => 0.3750,
                '75% LTV' => 0.5000,
                '80% LTV' => null,
            ],
        ];

        $rows = [];
        foreach ($grid as $loanTypeLabel => $cols) {
            $ltId = $loanTypeId[$loanTypeLabel] ?? null;
            if (!$ltId) {
                continue; // label mismatch -> skip
            }

            foreach ($cols as $ltvLabel => $pct) {
                $lrId = $ltvId[$ltvLabel] ?? null;
                if (!$lrId) {
                    continue; // label mismatch -> skip
                }

                $rows[] = [
                    'loan_type_id' => $ltId,
                    'ltv_ratio_id' => $lrId,
                    'adjustment_pct' => $pct,    // decimal percent; null => N/A
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('loan_type_dscr_ltv_adjustments')->upsert(
            $rows,
            ['loan_type_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
