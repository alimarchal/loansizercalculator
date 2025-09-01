<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoanRule;
use App\Models\RehabLevel;
use App\Models\RehabLimit;

class RehabLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map RehabLevel names -> ids
        $levels = RehabLevel::pluck('id', 'name')->all();

        // Safety check
        foreach (['LIGHT REHAB', 'MODERATE REHAB', 'HEAVY REHAB', 'EXTENSIVE REHAB'] as $key) {
            if (!isset($levels[$key])) {
                throw new \RuntimeException("Rehab level missing: {$key}. Seed RehabLevelSeeder first.");
            }
        }

        // Helper: pick limits by experience label
        $limitsForExperience = function (string $expLabel): array {
            // Normalise label (e.g., "1-2", "10+")
            $expLabel = trim($expLabel);

            // Group A: starter (0, 1–2)
            $starter = in_array($expLabel, ['0', '1-2'], true);

            if ($starter) {
                return [
                    'LIGHT REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 75.00],
                ];
            }

            // Group B: experienced (3–4, 5–9, 10+)
            return [
                'LIGHT REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                'MODERATE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                'HEAVY REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                'EXTENSIVE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 75.00],
            ];
        };

        // Iterate all rules and (up)insert 4 limits per rule
        LoanRule::with('experience:id,experiences_range')->chunkById(200, function ($rules) use ($levels, $limitsForExperience) {
            foreach ($rules as $rule) {
                $matrix = $limitsForExperience($rule->experience->experiences_range);

                foreach ($matrix as $levelName => $vals) {
                    RehabLimit::updateOrCreate(
                        [
                            'loan_rule_id' => $rule->id,
                            'rehab_level_id' => $levels[$levelName],
                        ],
                        [
                            'max_ltc' => $vals['ltc'],
                            'max_ltv' => $vals['ltv'],
                            'max_ltfc' => $vals['ltfc'],
                        ]
                    );
                }
            }
        });
    }
}
