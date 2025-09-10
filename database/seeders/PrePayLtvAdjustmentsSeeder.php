<?php

namespace Database\Seeders;

use App\Models\PrePay;
use App\Models\LtvRatio;
use App\Models\PrepayPeriods;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LoanType;

class PrePayLtvAdjustmentsSeeder extends Seeder
{
    public function run(): void
    {
        // Must be seeded already
        $ltvId = LtvRatio::pluck('id', 'ratio_range'); // ['50% LTV or less' => 1, ...]
        $prepayId = PrepayPeriods::pluck('id', 'prepay_name');         // ['3 Year Prepay' => 1, '5 Year Prepay' => 2, ...]

        // DSCR programs
        $lp1 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #1')->value('id');
        $lp2 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #2')->value('id');
        $lp3 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #3')->value('id');



        // TODO: Replace with your real numbers (decimal %). null = N/A.
        $grid = [
            '3 Year Prepay' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.125,
                '70% LTV' => 0.250,
                '75% LTV' => 0.375,
                '80% LTV' => null,
            ],
            '5 Year Prepay' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.125,
                '75% LTV' => 0.250,
                '80% LTV' => null,
            ],
        ];

        $rows = [];
        foreach ($grid as $prepayLabel => $cols) {
            $pId = $prepayId[$prepayLabel] ?? null;
            if (!$pId) {
                continue;
            }

            foreach ($cols as $ltvLabel => $pct) {
                $lrId = $ltvId[$ltvLabel] ?? null;
                if (!$lrId) {
                    continue;
                }

                $rows[] = [
                    'loan_type_id' => $lp1,
                    'pre_pay_id' => $pId,
                    'ltv_ratio_id' => $lrId,
                    'adjustment_pct' => $pct,      // e.g. 0.125 for 0.125%
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('pre_pay_ltv_adjustments')->upsert(
            $rows,
            ['pre_pay_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
