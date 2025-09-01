<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Pricing, PricingTier, LoanRule};

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Grab tiers once (ids keyed by label)
        $tiers = PricingTier::pluck('id', 'price_range')->all();

        foreach (['<250k', '250-500k', '>=500k'] as $label) {
            if (!isset($tiers[$label])) {
                throw new \RuntimeException("Pricing tier missing: {$label}. Seed PricingTierSeeder first.");
            }
        }

        // Define pricing by FICO band (same rates across all tiers and experience levels)
        $ficoPricing = [
            '660-679' => ['interest' => 12.750, 'points' => 2.990],
            '680-699' => ['interest' => 12.750, 'points' => 2.990],
            '700-719' => ['interest' => 12.500, 'points' => 2.500],
            '720-739' => ['interest' => 12.000, 'points' => 2.500],
            '740+' => ['interest' => 11.500, 'points' => 2.500],
        ];

        // Special case: Experience 0, FICO 660-679 should have N/A pricing
        $specialCases = [
            '0_660-679' => ['interest' => 0.00, 'points' => 0.00],
        ];

        // Seed for all rules
        LoanRule::with(['experience:id,experiences_range', 'ficoBand:id,fico_range'])->chunkById(200, function ($rules) use ($tiers, $ficoPricing, $specialCases) {
            foreach ($rules as $rule) {
                $expRange = $rule->experience->experiences_range;
                $ficoRange = $rule->ficoBand->fico_range;
                $specialKey = $expRange . '_' . $ficoRange;

                // Check for special case first
                if (isset($specialCases[$specialKey])) {
                    $pricing = $specialCases[$specialKey];
                } elseif (isset($ficoPricing[$ficoRange])) {
                    $pricing = $ficoPricing[$ficoRange];
                } else {
                    throw new \RuntimeException("No pricing defined for FICO range: {$ficoRange}");
                }

                // Same pricing across all three tiers for each FICO band
                foreach (['<250k', '250-500k', '>=500k'] as $label) {
                    Pricing::updateOrCreate(
                        [
                            'loan_rule_id' => $rule->id,
                            'pricing_tier_id' => $tiers[$label],
                        ],
                        [
                            'interest_rate' => $pricing['interest'],
                            'lender_points' => $pricing['points'],
                        ]
                    );
                }
            }
        });
    }
}