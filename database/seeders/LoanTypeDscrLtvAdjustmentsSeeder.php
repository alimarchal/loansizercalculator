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
     */
    public function run(): void
    {
        // Clear existing data first (optional)
        DB::table('loan_type_dscr_ltv_adjustments')->truncate();

        // Get the loan program
        $lp1 = LoanType::where('name', 'DSCR Rental Loans')
            ->where('loan_program', 'Loan Program #1')
            ->value('id');

        if (!$lp1) {
            $this->command->error('Loan Program #1 not found. Make sure loan_types are seeded first.');
            return;
        }

        // Get DSCR product IDs - let's be more explicit
        $dscrProducts = DB::table('loan_types_dscrs')->get();
        $this->command->info('Found DSCR products:');
        foreach ($dscrProducts as $product) {
            $this->command->info("  ID: {$product->id} - Name: '{$product->loan_type_dscr_name}'");
        }

        // Manual mapping to avoid any lookup issues
        $dscrIds = [
            '30 Year Fixed' => 1,
            '10 Year IO' => 2,
            '5/1 ARM' => 3,
        ];

        // Get LTV ratios
        $ltvRatios = LtvRatio::all();
        $this->command->info('Found LTV ratios:');
        foreach ($ltvRatios as $ratio) {
            $this->command->info("  ID: {$ratio->id} - Range: '{$ratio->ratio_range}'");
        }

        // Manual mapping for LTV ratios (adjust IDs based on your actual data)
        $ltvIds = [
            '50% LTV or less' => 1,
            '55% LTV' => 2,
            '60% LTV' => 3,
            '65% LTV' => 4,
            '70% LTV' => 5,
            '75% LTV' => 6,
            '80% LTV' => 7,
        ];

        // Adjustment grid
        $grid = [
            '30 Year Fixed' => [
                '50% LTV or less' => 0.0000,
                '55% LTV' => 0.0000,
                '60% LTV' => 0.0000,
                '65% LTV' => 0.0000,
                '70% LTV' => 0.0000,
                '75% LTV' => 0.1250,
                '80% LTV' => 0.0000,
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

        $rows = [];
        $processedCount = 0;

        foreach ($grid as $dscrProductName => $ltvAdjustments) {
            $dscrId = $dscrIds[$dscrProductName];

            $this->command->info("Processing {$dscrProductName} (ID: {$dscrId}):");

            foreach ($ltvAdjustments as $ltvRange => $adjustmentPct) {
                $ltvId = $ltvIds[$ltvRange];

                $rows[] = [
                    'loan_type_id' => $lp1,
                    'dscr_loan_type_id' => $dscrId,
                    'ltv_ratio_id' => $ltvId,
                    'adjustment_pct' => $adjustmentPct,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $processedCount++;
                $this->command->info("  {$ltvRange} -> {$adjustmentPct}");
            }
        }

        if (empty($rows)) {
            $this->command->error('No rows to insert.');
            return;
        }

        DB::table('loan_type_dscr_ltv_adjustments')->upsert(
            $rows,
            ['loan_type_id', 'dscr_loan_type_id', 'ltv_ratio_id'], // unique constraint columns
            ['adjustment_pct', 'updated_at'] // columns to update on conflict
        );

        $this->command->info("Successfully inserted {$processedCount} loan type DSCR LTV adjustments.");
        $this->command->info("Expected 21 rows (3 products × 7 LTV ratios)");
    }
}