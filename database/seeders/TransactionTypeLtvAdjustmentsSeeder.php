<?php

namespace Database\Seeders;

use App\Models\LtvRange;
use App\Models\LtvRatio;
use App\Models\TransactionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LoanType;

class TransactionTypeLtvAdjustmentsSeeder extends Seeder
{
    /**
     * Seeds the Transaction Type × LTV adjustment grid.
     * Prereqs:
     *  - transaction_types table seeded (label column)
     *  - ltv_ranges table seeded (labels: '50% LTV or less' ... '80% LTV')
     *  - pivot table: transaction_type_ltv_adjustments (transaction_type_id, ltv_ratio_id, adjustment_pct)
     */
    public function run(): void
    {
        // IDs keyed by labels (must be seeded first)
        $ltvId = LtvRatio::pluck('id', 'ratio_range');             // ['50% LTV or less' => 1, ...]
        $txId = TransactionType::pluck('id', 'name');      // ['Purchase' => 5, 'Refi No Cash Out' => 6, ...]

        // DSCR programs
        $lp1 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #1')->value('id');
        $lp2 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #2')->value('id');
        $lp3 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #3')->value('id');



        // === TODO: Replace with your real adjustments (decimal percents). Use null for N/A. ===
        // The left keys MUST exactly match your TransactionType labels.
        // The inner keys MUST exactly match your LtvRange labels.
        $grid = [
            'Purchase' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => null,
            ],
            'Refinance No Cash Out' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.125,
                '75% LTV' => 0.250,
                '80% LTV' => null,
            ],
            'Refinance Cash Out' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => null,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
        ];

        $rows = [];
        foreach ($grid as $txLabel => $cols) {
            $tId = $txId[$txLabel] ?? null;
            if (!$tId) {
                continue;
            }

            foreach ($cols as $ltvLabel => $pct) {
                $lrId = $ltvId[$ltvLabel] ?? null;
                if (!$lrId) {
                    continue;
                }

                $rows[] = [
                    'loan_type_id' => $lp1,
                    'transaction_type_id' => $tId,
                    'ltv_ratio_id' => $lrId,
                    'adjustment_pct' => $pct,  // decimal percent; null => N/A
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('transaction_type_ltv_adjustments')->upsert(
            $rows,
            ['transaction_type_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
