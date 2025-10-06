<?php

namespace Database\Seeders;

use App\Models\FicoBand;
use App\Models\LoanRule;
use App\Models\Experience;
use App\Models\TransactionType;
use App\Models\RehabLevel;
use App\Models\RehabLimit;
use App\Models\Pricing;
use App\Models\PricingTier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FixAndFlipRefinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder duplicates all Fix and Flip Purchase loan rules for Refinance transaction types
     */
    public function run(): void
    {
        // Get transaction types
        $purchase = TransactionType::where('name', 'Purchase')->firstOrFail();
        $refinanceNoCashOut = TransactionType::where('name', 'Refinance No Cash Out')->firstOrFail();
        $refinanceCashOut = TransactionType::where('name', 'Refinance Cash Out')->firstOrFail();

        // Get all existing Purchase loan rules for Fix and Flip
        $purchaseRules = LoanRule::where('transaction_type_id', $purchase->id)
            ->with(['experience.loanType', 'ficoBand', 'rehabLimits.rehabLevel', 'pricings.pricingTier'])
            ->get();

        // Filter only Fix and Flip loan rules
        $fixAndFlipPurchaseRules = $purchaseRules->filter(function ($rule) {
            return $rule->experience->loanType && $rule->experience->loanType->name === 'Fix and Flip';
        });

        if ($fixAndFlipPurchaseRules->isEmpty()) {
            $this->command->warn('No Fix and Flip Purchase loan rules found. Please seed Fix and Flip Purchase rules first.');
            return;
        }

        $this->command->info('Found ' . $fixAndFlipPurchaseRules->count() . ' Fix and Flip Purchase rules to duplicate.');

        $refinanceTypes = [$refinanceNoCashOut, $refinanceCashOut];
        $totalCreated = 0;

        foreach ($refinanceTypes as $refinanceType) {
            $this->command->info("Creating rules for: {$refinanceType->name}");

            foreach ($fixAndFlipPurchaseRules as $purchaseRule) {
                // Create new loan rule with same data but different transaction type
                $newRule = LoanRule::updateOrCreate(
                    [
                        'experience_id' => $purchaseRule->experience_id,
                        'fico_band_id' => $purchaseRule->fico_band_id,
                        'transaction_type_id' => $refinanceType->id,
                    ],
                    [
                        'max_total_loan' => $purchaseRule->max_total_loan,
                        'max_budget' => $purchaseRule->max_budget,
                    ]
                );

                // Duplicate Rehab Limits
                foreach ($purchaseRule->rehabLimits as $rehabLimit) {
                    RehabLimit::updateOrCreate(
                        [
                            'loan_rule_id' => $newRule->id,
                            'rehab_level_id' => $rehabLimit->rehab_level_id,
                        ],
                        [
                            'max_ltc' => $rehabLimit->max_ltc,
                            'max_ltv' => $rehabLimit->max_ltv,
                            'max_ltfc' => $rehabLimit->max_ltfc,
                        ]
                    );
                }

                // Duplicate Pricing
                foreach ($purchaseRule->pricings as $pricing) {
                    Pricing::updateOrCreate(
                        [
                            'loan_rule_id' => $newRule->id,
                            'pricing_tier_id' => $pricing->pricing_tier_id,
                        ],
                        [
                            'interest_rate' => $pricing->interest_rate,
                            'lender_points' => $pricing->lender_points,
                        ]
                    );
                }

                $totalCreated++;
            }
        }

        $this->command->info("Successfully created {$totalCreated} refinance loan rules with all related data.");
        $this->command->info('Fix and Flip refinance loan rules seeding completed!');
    }
}