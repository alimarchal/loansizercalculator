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

class DesktopAppraisalLoanRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Desktop Appraisal experiences (IDs: 6-10)
        $experiences = [
            6 => '0',      // Experience ID 6 = "0"
            7 => '1-2',    // Experience ID 7 = "1-2" 
            8 => '3-4',    // Experience ID 8 = "3-4"
            9 => '5-9',    // Experience ID 9 = "5-9"
            10 => '10+'    // Experience ID 10 = "10+"
        ];

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

        // Get rehab levels
        $rehabLevels = [
            1 => 'LIGHT REHAB',
            2 => 'MODERATE REHAB',
            3 => 'HEAVY REHAB',
            4 => 'EXTENSIVE REHAB'
        ];

        // Get pricing tiers
        $pricingTiers = [
            1 => '<250k',
            2 => '250-500k',
            3 => '>=500k'
        ];

        // Desktop Appraisal matrix data
        $desktopRules = [
            // Experience "0"
            ['exp_id' => 6, 'fico_id' => 1, 'max_loan' => 0, 'max_budget' => 0, 'has_rehab' => false, 'pricing' => []],
            ['exp_id' => 6, 'fico_id' => 2, 'max_loan' => 0, 'max_budget' => 0, 'has_rehab' => false, 'pricing' => []],
            [
                'exp_id' => 6,
                'fico_id' => 3,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 6,
                'fico_id' => 4,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 6,
                'fico_id' => 5,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "1-2" 
            [
                'exp_id' => 7,
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
                'exp_id' => 7,
                'fico_id' => 2,
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
                'exp_id' => 7,
                'fico_id' => 3,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 7,
                'fico_id' => 4,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 7,
                'fico_id' => 5,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "3-4"
            [
                'exp_id' => 8,
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
                'exp_id' => 8,
                'fico_id' => 2,
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
                'exp_id' => 8,
                'fico_id' => 3,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 8,
                'fico_id' => 4,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 8,
                'fico_id' => 5,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "5-9"
            [
                'exp_id' => 9,
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
                'exp_id' => 9,
                'fico_id' => 2,
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
                'exp_id' => 9,
                'fico_id' => 3,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 9,
                'fico_id' => 4,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 9,
                'fico_id' => 5,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],

            // Experience "10+"
            [
                'exp_id' => 10,
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
                'exp_id' => 10,
                'fico_id' => 2,
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
                'exp_id' => 10,
                'fico_id' => 3,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.50, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 10,
                'fico_id' => 4,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 12.00, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 12.00, 'points' => 2.50]
                ]
            ],
            [
                'exp_id' => 10,
                'fico_id' => 5,
                'max_loan' => 1500000,
                'max_budget' => 250000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 2, 'rate' => 11.50, 'points' => 2.50],
                    ['tier_id' => 3, 'rate' => 11.50, 'points' => 2.50]
                ]
            ],
        ];

        // Create loan rules for Desktop Appraisal
        foreach ($desktopRules as $ruleData) {
            $loanRule = LoanRule::create([
                'experience_id' => $ruleData['exp_id'],
                'fico_band_id' => $ruleData['fico_id'],
                'transaction_type_id' => $purchaseTransactionId,
                'max_total_loan' => $ruleData['max_loan'],
                'max_budget' => $ruleData['max_budget']
            ]);

            // Add rehab limits (90% LTC, 75% LTV for Light, Moderate, Heavy - no Extensive)
            if ($ruleData['has_rehab']) {
                foreach ([1, 2, 3] as $rehabLevelId) { // Light, Moderate, Heavy
                    $loanRule->rehabLimits()->create([
                        'rehab_level_id' => $rehabLevelId,
                        'max_ltc' => 90,
                        'max_ltv' => 75,
                        'max_ltfc' => 0
                    ]);
                }
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
    }
}
