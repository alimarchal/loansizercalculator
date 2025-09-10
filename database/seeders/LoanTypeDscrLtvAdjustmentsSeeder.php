<?php

namespace Database\Seeders;

use App\Models\LoanType;   // id, name, loan_program  (program rows: DSCR Rental Loans / Loan Program #1,#2,#3)
use App\Models\LtvRatio;   // id, ratio_range         ('50% LTV or less', '55% LTV', ... '80% LTV')
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanTypeDscrLtvAdjustmentsSeeder extends Seeder
{
    public function run(): void
    {
        // Lookups
        $ltvId = LtvRatio::pluck('id', 'ratio_range');
        $dscrProdId = DB::table('loan_types_dscrs')->pluck('id', 'loan_type_dscr_name'); // '30 Year Fixed', '10 Year IO', '5/1 ARM'

        // Debug: Show what data we have
        echo "Available LTV Ratios: " . implode(', ', $ltvId->keys()->toArray()) . "\n";
        echo "Available DSCR Products: " . implode(', ', $dscrProdId->keys()->toArray()) . "\n";

        // DSCR programs
        $lp1 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #1')->value('id');
        $lp2 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #2')->value('id');
        $lp3 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #3')->value('id');

        echo "Loan Program IDs: LP1=$lp1, LP2=$lp2, LP3=$lp3\n";

        // Program #1 values (edit to your sheet)
        $GRID_LP1 = [
            '30 Year Fixed' => [
                '50% LTV or less' => 0.0000,
                '55% LTV' => 0.0000,
                '60% LTV' => 0.0000,
                '65% LTV' => 0.0000,
                '70% LTV' => 0.0000,
                '75% LTV' => 0.1250,
                '80% LTV' => null,
            ],
            '10 Year IO' => [
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

        // Program #2 and #3 placeholders so it runs
        $GRID_LP2 = $GRID_LP1;
        $GRID_LP3 = $GRID_LP1;

        $rows = [];
        $insert = function (array $grid, ?int $programId) use (&$rows, $dscrProdId, $ltvId) {
            if (!$programId) {
                echo "Skipping program with null ID\n";
                return;
            }

            foreach ($grid as $prodLabel => $cols) {
                $prodId = $dscrProdId[$prodLabel] ?? null;
                if (!$prodId) {
                    echo "Could not find product ID for: $prodLabel\n";
                    continue;
                }

                foreach ($cols as $ltvLabel => $pct) {
                    $lrId = $ltvId[$ltvLabel] ?? null;
                    if (!$lrId) {
                        echo "Could not find LTV ID for: $ltvLabel\n";
                        continue;
                    }

                    // If your column is NOT NULL, skip N/A cells:
                    // if ($pct === null) continue;

                    $rows[] = [
                        'loan_type_id' => $programId,  // program row
                        'dscr_loan_type_id' => $prodId,     // '30 Year Fixed' etc
                        'ltv_ratio_id' => $lrId,
                        'adjustment_pct' => $pct,        // decimal; null => N/A if nullable
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        };

        $insert($GRID_LP1, $lp1);
        $insert($GRID_LP2, $lp2);
        $insert($GRID_LP3, $lp3);

        echo "Total rows to insert: " . count($rows) . "\n";

        if (count($rows) > 0) {
            DB::table('loan_type_dscr_ltv_adjustments')->upsert(
                $rows,
                ['loan_type_id', 'dscr_loan_type_id', 'ltv_ratio_id'],
                ['adjustment_pct', 'updated_at']
            );
            echo "Records inserted successfully!\n";
        } else {
            echo "No records to insert!\n";
        }
    }
}
