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

        // Define limits by experience AND FICO combination
        $limitsMatrix = [
            // Experience 0
            '0' => [
                '660-679' => [
                    'LIGHT REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '680-699' => [
                    'LIGHT REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '700-719' => [
                    'LIGHT REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '720-739' => [
                    'LIGHT REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '740+' => [
                    'LIGHT REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
            ],

            // Experience 1-2
            '1-2' => [
                '660-679' => [
                    'LIGHT REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '680-699' => [
                    'LIGHT REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '700-719' => [
                    'LIGHT REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '720-739' => [
                    'LIGHT REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '740+' => [
                    'LIGHT REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 75.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
            ],

            // Experience 3-4
            '3-4' => [
                '660-679' => [
                    'LIGHT REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 0.00, 'ltv' => 0.00, 'ltfc' => 0.00],
                ],
                '680-699' => [
                    'LIGHT REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 85.00],
                ],
                '700-719' => [
                    'LIGHT REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 85.00],
                ],
                '720-739' => [
                    'LIGHT REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 85.00],
                ],
                '740+' => [
                    'LIGHT REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 80.00, 'ltv' => 75.00, 'ltfc' => 85.00],
                ],
            ],

            // Experience 5-9 - All FICO bands same
            '5-9' => [
                '*' => [
                    'LIGHT REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 90.00],
                ]
            ],

            // Experience 10+ - All FICO bands same
            '10+' => [
                '*' => [
                    'LIGHT REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'MODERATE REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'HEAVY REHAB' => ['ltc' => 90.00, 'ltv' => 75.00, 'ltfc' => 0.00],
                    'EXTENSIVE REHAB' => ['ltc' => 85.00, 'ltv' => 75.00, 'ltfc' => 90.00],
                ]
            ],
        ];

        // Iterate all rules and upsert 4 limits per rule
        LoanRule::with(['experience:id,experiences_range', 'ficoBand:id,fico_range'])->chunkById(200, function ($rules) use ($levels, $limitsMatrix) {
            foreach ($rules as $rule) {
                $expRange = $rule->experience->experiences_range;
                $ficoRange = $rule->ficoBand->fico_range;

                if (!isset($limitsMatrix[$expRange])) {
                    throw new \RuntimeException("No rehab limits defined for experience range: {$expRange}");
                }

                $expMatrix = $limitsMatrix[$expRange];

                // Check if we have specific FICO mapping or use wildcard
                if (isset($expMatrix[$ficoRange])) {
                    $matrix = $expMatrix[$ficoRange];
                } elseif (isset($expMatrix['*'])) {
                    $matrix = $expMatrix['*'];
                } else {
                    throw new \RuntimeException("No rehab limits defined for experience {$expRange} and FICO {$ficoRange}");
                }

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