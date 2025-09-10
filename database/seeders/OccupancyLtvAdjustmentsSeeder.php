<?php

namespace Database\Seeders;

use App\Models\LtvRatio;
use App\Models\OccupancyTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LoanType;



class OccupancyLtvAdjustmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IDs keyed by labels (must be seeded first)
        $ltvId = LtvRatio::pluck('id', 'ratio_range');      // ['50% LTV or less' => 1, ...]
        $occId = OccupancyTypes::pluck('id', 'name');     // ['Vacant' => 1, 'Occupied' => 2] (ensure your column is 'label')
        // DSCR programs
        $lp1 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #1')->value('id');
        $lp2 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #2')->value('id');
        $lp3 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #3')->value('id');


        // === ADJUST THESE VALUES TO MATCH YOUR SHEET ===
        // Use decimal percentages (0.125 means 0.125%). Use null for N/A.
        $grid = [
            'Occupied' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => null,
            ],
            'Vacant' => [
                '50% LTV or less' => 0.250,
                '55% LTV' => 0.250,
                '60% LTV' => 0.250,
                '65% LTV' => 0.250,
                '70% LTV' => 0.250,
                '75% LTV' => 0.250,
                '80% LTV' => null,
            ],
        ];

        $rows = [];
        foreach ($grid as $occLabel => $cols) {
            $oId = $occId[$occLabel] ?? null;
            if (!$oId) {
                continue;
            }

            foreach ($cols as $ltvLabel => $pct) {
                $lrId = $ltvId[$ltvLabel] ?? null;
                if (!$lrId) {
                    continue;
                }

                $rows[] = [
                    'loan_type_id' => $lp1,
                    'occupancy_type_id' => $oId,
                    'ltv_ratio_id' => $lrId,
                    'adjustment_pct' => $pct,   // decimal percent; null => N/A
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('occupancy_ltv_adjustments')->upsert(
            $rows,
            ['occupancy_type_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
