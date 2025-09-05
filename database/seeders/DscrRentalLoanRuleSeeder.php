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

class DscrRentalLoanRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get DSCR Rental loan type
        $dscrLoanType = \App\Models\LoanType::where('name', 'DSCR Rental')->where('loan_program', '#1')->first();
        if (!$dscrLoanType) {
            echo "DSCR Rental loan type not found!\n";
            return;
        }

        // Get DSCR Rental experiences dynamically
        $dscrExperiences = \App\Models\Experience::where('loan_type_id', $dscrLoanType->id)->get();
        $experiences = [];
        foreach ($dscrExperiences as $exp) {
            $experiences[$exp->id] = $exp->experiences_range;
        }

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

        // DSCR Rental matrix data
        $dscrRules = [];

        // Get experience IDs by range
        $exp0 = $dscrExperiences->where('experiences_range', '0')->first()?->id;
        $exp12 = $dscrExperiences->where('experiences_range', '1-2')->first()?->id;
        $exp34 = $dscrExperiences->where('experiences_range', '3-4')->first()?->id;
        $exp59 = $dscrExperiences->where('experiences_range', '5-9')->first()?->id;
        $exp10plus = $dscrExperiences->where('experiences_range', '10+')->first()?->id;

        if (!$exp0 || !$exp12 || !$exp34 || !$exp59 || !$exp10plus) {
            echo "Missing experience ranges for DSCR Rental!\n";
            return;
        }

        $dscrRules = [
            // Experience "0"
            ['exp_id' => $exp0, 'fico_id' => 1, 'max_loan' => 0, 'max_budget' => 0, 'has_rehab' => false, 'pricing' => []],
            ['exp_id' => $exp0, 'fico_id' => 2, 'max_loan' => 0, 'max_budget' => 0, 'has_rehab' => false, 'pricing' => []],
            [
                'exp_id' => $exp0,
                'fico_id' => 3,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.75, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.75, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.75, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp0,
                'fico_id' => 4,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.25, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.25, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.25, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp0,
                'fico_id' => 5,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.75, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 9.75, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 9.75, 'points' => 2.00]
                ]
            ],

            // Experience "1-2" 
            [
                'exp_id' => $exp12,
                'fico_id' => 1,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.25, 'points' => 2.25],
                    ['tier_id' => 2, 'rate' => 11.25, 'points' => 2.25],
                    ['tier_id' => 3, 'rate' => 11.25, 'points' => 2.25]
                ]
            ],
            [
                'exp_id' => $exp12,
                'fico_id' => 2,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.00, 'points' => 2.25],
                    ['tier_id' => 2, 'rate' => 11.00, 'points' => 2.25],
                    ['tier_id' => 3, 'rate' => 11.00, 'points' => 2.25]
                ]
            ],
            [
                'exp_id' => $exp12,
                'fico_id' => 3,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.75, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.75, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.75, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp12,
                'fico_id' => 4,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.25, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.25, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.25, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp12,
                'fico_id' => 5,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.75, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 9.75, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 9.75, 'points' => 2.00]
                ]
            ],

            // Experience "3-4"
            [
                'exp_id' => $exp34,
                'fico_id' => 1,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 11.00, 'points' => 2.25],
                    ['tier_id' => 2, 'rate' => 11.00, 'points' => 2.25],
                    ['tier_id' => 3, 'rate' => 11.00, 'points' => 2.25]
                ]
            ],
            [
                'exp_id' => $exp34,
                'fico_id' => 2,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.75, 'points' => 2.25],
                    ['tier_id' => 2, 'rate' => 10.75, 'points' => 2.25],
                    ['tier_id' => 3, 'rate' => 10.75, 'points' => 2.25]
                ]
            ],
            [
                'exp_id' => $exp34,
                'fico_id' => 3,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.50, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.50, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.50, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp34,
                'fico_id' => 4,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.00, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.00, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.00, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp34,
                'fico_id' => 5,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.50, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 9.50, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 9.50, 'points' => 2.00]
                ]
            ],

            // Experience "5-9"
            [
                'exp_id' => $exp59,
                'fico_id' => 1,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.75, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.75, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.75, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp59,
                'fico_id' => 2,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.50, 'points' => 2.00],
                    ['tier_id' => 2, 'rate' => 10.50, 'points' => 2.00],
                    ['tier_id' => 3, 'rate' => 10.50, 'points' => 2.00]
                ]
            ],
            [
                'exp_id' => $exp59,
                'fico_id' => 3,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.25, 'points' => 1.75],
                    ['tier_id' => 2, 'rate' => 10.25, 'points' => 1.75],
                    ['tier_id' => 3, 'rate' => 10.25, 'points' => 1.75]
                ]
            ],
            [
                'exp_id' => $exp59,
                'fico_id' => 4,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.75, 'points' => 1.75],
                    ['tier_id' => 2, 'rate' => 9.75, 'points' => 1.75],
                    ['tier_id' => 3, 'rate' => 9.75, 'points' => 1.75]
                ]
            ],
            [
                'exp_id' => $exp59,
                'fico_id' => 5,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.25, 'points' => 1.75],
                    ['tier_id' => 2, 'rate' => 9.25, 'points' => 1.75],
                    ['tier_id' => 3, 'rate' => 9.25, 'points' => 1.75]
                ]
            ],

            // Experience "10+"
            [
                'exp_id' => $exp10plus,
                'fico_id' => 1,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.50, 'points' => 1.75],
                    ['tier_id' => 2, 'rate' => 10.50, 'points' => 1.75],
                    ['tier_id' => 3, 'rate' => 10.50, 'points' => 1.75]
                ]
            ],
            [
                'exp_id' => $exp10plus,
                'fico_id' => 2,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.25, 'points' => 1.75],
                    ['tier_id' => 2, 'rate' => 10.25, 'points' => 1.75],
                    ['tier_id' => 3, 'rate' => 10.25, 'points' => 1.75]
                ]
            ],
            [
                'exp_id' => $exp10plus,
                'fico_id' => 3,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 10.00, 'points' => 1.50],
                    ['tier_id' => 2, 'rate' => 10.00, 'points' => 1.50],
                    ['tier_id' => 3, 'rate' => 10.00, 'points' => 1.50]
                ]
            ],
            [
                'exp_id' => $exp10plus,
                'fico_id' => 4,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.50, 'points' => 1.50],
                    ['tier_id' => 2, 'rate' => 9.50, 'points' => 1.50],
                    ['tier_id' => 3, 'rate' => 9.50, 'points' => 1.50]
                ]
            ],
            [
                'exp_id' => $exp10plus,
                'fico_id' => 5,
                'max_loan' => 2000000,
                'max_budget' => 350000,
                'has_rehab' => true,
                'pricing' => [
                    ['tier_id' => 1, 'rate' => 9.00, 'points' => 1.50],
                    ['tier_id' => 2, 'rate' => 9.00, 'points' => 1.50],
                    ['tier_id' => 3, 'rate' => 9.00, 'points' => 1.50]
                ]
            ],
        ];

        // Create loan rules for DSCR Rental
        foreach ($dscrRules as $ruleData) {
            $loanRule = LoanRule::create([
                'experience_id' => $ruleData['exp_id'],
                'fico_band_id' => $ruleData['fico_id'],
                'transaction_type_id' => $purchaseTransactionId,
                'max_total_loan' => $ruleData['max_loan'],
                'max_budget' => $ruleData['max_budget']
            ]);

            // Add rehab limits (80% LTC, 70% LTV for DSCR - all rehab levels)
            if ($ruleData['has_rehab']) {
                foreach ([1, 2, 3, 4] as $rehabLevelId) { // Light, Moderate, Heavy, Extensive
                    $loanRule->rehabLimits()->create([
                        'rehab_level_id' => $rehabLevelId,
                        'max_ltc' => 80,
                        'max_ltv' => 70,
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

        echo "DSCR Rental loan rules seeded successfully!\n";
    }
}
