<?php

namespace Database\Seeders;

use App\Models\LtvRatio;
use App\Models\PropertyType;
use App\Models\LtvRange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LoanType;

class PropertyTypeLtvAdjustmentSeeder extends Seeder
{
    /**
     * Seeds the Property Type × LTV adjustment grid.
     * Prereqs:
     *  - property_types table seeded (names match your PropertyTypeSeeder)
     *  - ltv_ranges table seeded (labels: "50% LTV or less", "55% LTV", ... "80% LTV")
     *  - pivot table: property_type_ltv_adjustments (property_type_id, ltv_range_id, adjustment_pct)
     */
    public function run(): void
    {
        // IDs keyed by labels (must be seeded first)
        $ltvId = LtvRatio::pluck('id', 'ratio_range');        // ['50% LTV or less' => 1, ...]
        $ptypeId = PropertyType::pluck('id', 'name');   // ['Single Family' => 3, ...]
        // DSCR programs
        $lp1 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #1')->value('id');
        $lp2 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #2')->value('id');
        $lp3 = LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #3')->value('id');



        // === FILL THIS GRID WITH YOUR REAL ADJUSTMENTS ===
        // Use decimal percentages (e.g., 0.125 for 0.125%). Use null for N/A.
        // Left keys MUST match your PropertyTypeSeeder names exactly.
        // Inner keys MUST match your LtvRange labels exactly.
        $grid = [
            'Vacant Land' => [
                '50% LTV or less' => null,
                '55% LTV' => null,
                '60% LTV' => null,
                '65% LTV' => null,
                '70% LTV' => null,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Entitled Land (Permit Approved)' => [
                '50% LTV or less' => null,
                '55% LTV' => null,
                '60% LTV' => null,
                '65% LTV' => null,
                '70% LTV' => null,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Single Family' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => 0.000,
            ],
            'Condo' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.250,
                '80% LTV' => null,
            ],
            'Townhome' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.000,
                '80% LTV' => null,
            ],
            '2-4 Unit' => [
                '50% LTV or less' => 0.000,
                '55% LTV' => 0.000,
                '60% LTV' => 0.000,
                '65% LTV' => 0.000,
                '70% LTV' => 0.000,
                '75% LTV' => 0.125,
                '80% LTV' => null,
            ],
            'Multi Family Apartment 5+ Units' => [
                '50% LTV or less' => 0.250,
                '55% LTV' => 0.250,
                '60% LTV' => 0.250,
                '65% LTV' => 0.250,
                '70% LTV' => 0.250,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Mixed Use' => [
                '50% LTV or less' => 0.250,
                '55% LTV' => 0.250,
                '60% LTV' => 0.250,
                '65% LTV' => 0.250,
                '70% LTV' => 0.250,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Warehouse Industrial' => [
                '50% LTV or less' => 0.250,
                '55% LTV' => 0.250,
                '60% LTV' => 0.250,
                '65% LTV' => 0.250,
                '70% LTV' => 0.250,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Hospitality' => [
                '50% LTV or less' => 0.500,
                '55% LTV' => 0.500,
                '60% LTV' => 0.500,
                '65% LTV' => null,
                '70% LTV' => null,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Office' => [
                '50% LTV or less' => 0.250,
                '55% LTV' => 0.250,
                '60% LTV' => 0.250,
                '65% LTV' => 0.250,
                '70% LTV' => 0.250,
                '75% LTV' => null,
                '80% LTV' => null,
            ],
            'Portfolio Residential' => [
                '50% LTV or less' => 0.500,
                '55% LTV' => 0.500,
                '60% LTV' => 0.500,
                '65% LTV' => 0.500,
                '70% LTV' => 0.500,
                '75% LTV' => 0.500,
                '80% LTV' => null,
            ],
        ];

        $rows = [];

        // Create data for all three loan programs
        $loanPrograms = [$lp1, $lp2, $lp3];

        foreach ($loanPrograms as $loanProgramId) {
            if (!$loanProgramId) {
                continue; // Skip if loan program not found
            }

            foreach ($grid as $ptypeLabel => $cols) {
                $ptId = $ptypeId[$ptypeLabel] ?? null;
                if (!$ptId) {
                    continue;
                }

                foreach ($cols as $ltvLabel => $pct) {
                    $lrId = $ltvId[$ltvLabel] ?? null;
                    if (!$lrId) {
                        continue;
                    }

                    $rows[] = [
                        'loan_type_id' => $loanProgramId,
                        'property_type_id' => $ptId,
                        'ltv_ratio_id' => $lrId,
                        'adjustment_pct' => $pct,   // decimal percent; null => N/A
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('property_type_ltv_adjustments')->upsert(
            $rows,
            ['loan_type_id', 'property_type_id', 'ltv_ratio_id'],
            ['adjustment_pct', 'updated_at']
        );
    }
}
