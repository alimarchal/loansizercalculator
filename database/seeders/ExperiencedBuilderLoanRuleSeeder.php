<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoanRule;
use App\Models\Experience;
use App\Models\FicoBand;
use App\Models\TransactionType;
use App\Models\RehabLevel;
use App\Models\PricingTier;
use App\Models\LoanType;

class ExperiencedBuilderLoanRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the EXPERIENCED BUILDER loan type ID
        $experiencedBuilderLoanType = LoanType::where('name', 'New Construction')
            ->where('loan_program', 'EXPERIENCED BUILDER')
            ->first();

        if (!$experiencedBuilderLoanType) {
            $this->command->error('EXPERIENCED BUILDER loan type not found!');
            return;
        }

        // Create experience ranges for EXPERIENCED BUILDER if they don't exist
        $experienceRanges = [
            ['experiences_range' => '0', 'min_experience' => 0, 'max_experience' => 0],
            ['experiences_range' => '1-2', 'min_experience' => 1, 'max_experience' => 2],
            ['experiences_range' => '3-4', 'min_experience' => 3, 'max_experience' => 4],
            ['experiences_range' => '5-9', 'min_experience' => 5, 'max_experience' => 9],
            ['experiences_range' => '10+', 'min_experience' => 10, 'max_experience' => 50],
        ];

        foreach ($experienceRanges as $range) {
            Experience::firstOrCreate([
                'loan_type_id' => $experiencedBuilderLoanType->id,
                'experiences_range' => $range['experiences_range']
            ], [
                'min_experience' => $range['min_experience'],
                'max_experience' => $range['max_experience']
            ]);
        }

        // Get the experience IDs for EXPERIENCED BUILDER
        $experiences = Experience::where('loan_type_id', $experiencedBuilderLoanType->id)
            ->pluck('id', 'experiences_range')
            ->toArray();

        // Get FICO bands
        $ficoBands = [
            1 => '660-679',
            2 => '680-699',
            3 => '700-719',
            4 => '720-739',
            5 => '740+'
        ];

        // Get transaction type (Purchase = ID 1)
        $purchaseTransactionId = 1;

        // Get pricing tiers
        $pricingTiers = [
            1 => '<250k',
            2 => '250-500k',
            3 => '>=500k'
        ];

        // EXPERIENCED BUILDER matrix data based on the provided table
        $experiencedBuilderRules = [
            // Experience "0"
            [
                'exp_range' => '0',
                'fico_id' => 1,
                'max_loan' => 0,
                'max_budget' => 0,
                'has_rehab' => false,
                'pricing' => []
            ],
            [
                'exp_range' => '0',
                'fico_id' => 2,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '0',
                'fico_id' => 3,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '0',
                'fico_id' => 4,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '0',
                'fico_id' => 5,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "1-2"
            [
                'exp_range' => '1-2',
                'fico_id' => 1,
                'max_loan' => 0,
                'max_budget' => 0,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '1-2',
                'fico_id' => 2,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '1-2',
                'fico_id' => 3,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '1-2',
                'fico_id' => 4,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '1-2',
                'fico_id' => 5,
                'max_loan' => 1000000,
                'max_budget' => 100000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "3-4"
            [
                'exp_range' => '3-4',
                'fico_id' => 1,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => false,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '3-4',
                'fico_id' => 2,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '3-4',
                'fico_id' => 3,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '3-4',
                'fico_id' => 4,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '3-4',
                'fico_id' => 5,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "5-9"
            [
                'exp_range' => '5-9',
                'fico_id' => 1,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '5-9',
                'fico_id' => 2,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '5-9',
                'fico_id' => 3,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '5-9',
                'fico_id' => 4,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '5-9',
                'fico_id' => 5,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "10+"
            [
                'exp_range' => '10+',
                'fico_id' => 1,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '10+',
                'fico_id' => 2,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 2, 'rate' => 12.75, 'points' => 2.99],
                    ['tier_id' => 3, 'rate' => 12.75, 'points' => 2.99]
                ]
            ],
            [
                'exp_range' => '10+',
                'fico_id' => 3,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '10+',
                'fico_id' => 4,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_range' => '10+',
                'fico_id' => 5,
                'max_loan' => 1000000,
                'max_budget' => 500000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],
        ];

        // Create loan rules for EXPERIENCED BUILDER
        foreach ($experiencedBuilderRules as $ruleData) {
            $experienceId = $experiences[$ruleData['exp_range']];

            $loanRule = LoanRule::create([
                'experience_id' => $experienceId,
                'fico_band_id' => $ruleData['fico_id'],
                'transaction_type_id' => $purchaseTransactionId,
                'max_total_loan' => $ruleData['max_loan'],
                'max_budget' => $ruleData['max_budget']
            ]);

            // Add rehab limits for EXPERIENCED BUILDER (80% LTC, 75% LTV, 85% LTFC for extensive rehab)
            if ($ruleData['has_rehab']) {
                // For EXPERIENCED BUILDER, we only add extensive rehab limits
                $loanRule->rehabLimits()->create([
                    'rehab_level_id' => 4, // Extensive rehab
                    'max_ltc' => 80,
                    'max_ltv' => 75,
                    'max_ltfc' => 85
                ]);
            }

            // Add pricing
            foreach ($ruleData['pricing'] as $pricingData) {
                $loanRule->pricings()->create([
                    'pricing_tier_id' => $pricingData['tier_id'],
                    'interest_rate' => $pricingData['rate'],
                    'lender_points' => $pricingData['points']
                ]);
            }
        }

        $this->command->info('EXPERIENCED BUILDER loan rules seeded successfully!');
    }
}
