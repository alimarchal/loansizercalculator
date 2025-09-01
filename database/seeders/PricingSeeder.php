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

        // Centralised defaults (as percentages, because your columns are DECIMAL(12,2))
        $starter = [ // experience: "0", "1-2"
            'interest' => 12.75,
            'points' => 2.99,
        ];

        $experienced = [ // experience: "3-4", "5-9", "10+"
            'interest' => 12.50,
            'points' => 2.99,
        ];

        // If you need per-tier tweaks, set them here (else all tiers use same numbers):
        $perTierAdjust = [
            '<250k' => ['interest_delta' => 0.00, 'points_delta' => 0.00],
            '250-500k' => ['interest_delta' => 0.00, 'points_delta' => 0.00],
            '> =500k' => ['interest_delta' => 0.00, 'points_delta' => 0.00], // keep label consistent; fix spacing if needed
        ];
        // Normalise key typo to your exact label:
        $perTierAdjust['>=500k'] = $perTierAdjust['> =500k'];
        unset($perTierAdjust['> =500k']);

        // Seed for all rules
        LoanRule::with('experience:id,experiences_range')->chunkById(200, function ($rules) use ($tiers, $starter, $experienced, $perTierAdjust) {
            foreach ($rules as $rule) {
                $isStarter = in_array($rule->experience->label, ['0', '1-2'], true);
                $base = $isStarter ? $starter : $experienced;

                foreach (['<250k', '250-500k', '>=500k'] as $label) {
                    $adj = $perTierAdjust[$label] ?? ['interest_delta' => 0, 'points_delta' => 0];

                    Pricing::updateOrCreate(
                        [
                            'loan_rule_id' => $rule->id,
                            'pricing_tier_id' => $tiers[$label],
                        ],
                        [
                            'interest_rate' => $base['interest'] + $adj['interest_delta'], // e.g., 12.50
                            'lender_points' => $base['points'] + $adj['points_delta'],   // e.g., 2.99
                        ]
                    );
                }
            }
        });
    }
}
