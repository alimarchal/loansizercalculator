<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanRule;
use App\Models\LoanType;
use App\Models\FicoBand;
use App\Models\TransactionType;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller for Loan Matrix calculations
 * Handles loan program filtering and calculations based on user inputs
 */
class LoanMatrixApiController extends Controller
{
    /**
     * Get loan matrix based on provided criteria
     * This endpoint returns raw loan matrix data similar to the loan-programs endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoanMatrix(Request $request)
    {
        // Validate incoming request parameters (accept both IDs and names)
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'credit_score' => 'required|integer|min:300|max:850',
            'experience' => 'nullable|string', // Can be ID or range like "1-2"
            'loan_type' => 'nullable|string', // Can be ID or name like "Fix and Flip"
            'transaction_type' => 'nullable|string', // Can be ID or name like "Purchase"
            'loan_term' => 'nullable|integer|min:6|max:36',
            'purchase_price' => 'nullable|numeric|min:10000|max:10000000',
            'arv' => 'nullable|numeric|min:10000|max:10000000',
            'rehab_budget' => 'nullable|numeric|min:0|max:5000000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Extract validated parameters
        $creditScore = $request->credit_score;
        $experience = $request->experience;
        $loanType = $request->loan_type;
        $transactionType = $request->transaction_type;

        try {
            // Convert loan_type name to IDs if needed
            $loanTypeIds = [];
            if ($loanType) {
                if (is_numeric($loanType)) {
                    // If numeric, treat as ID
                    $loanTypeIds = [$loanType];
                } else {
                    // If string, find all loan types with matching name
                    $loanTypeIds = \App\Models\LoanType::where('name', $loanType)->pluck('id')->toArray();
                }
            } else {
                // Default to Fix and Flip if no loan_type specified
                $loanTypeIds = \App\Models\LoanType::where('name', 'Fix and Flip')->pluck('id')->toArray();
            }

            // Convert transaction_type name to ID if needed
            $transactionTypeId = null;
            if ($transactionType) {
                if (is_numeric($transactionType)) {
                    // If numeric, treat as ID
                    $transactionTypeId = $transactionType;
                } else {
                    // If string, find transaction type with matching name
                    $transactionTypeModel = \App\Models\TransactionType::where('name', $transactionType)->first();
                    $transactionTypeId = $transactionTypeModel ? $transactionTypeModel->id : null;
                }
            }

            // Handle experience parameter
            $experienceRange = null;
            if ($experience) {
                if (is_numeric($experience)) {
                    // If numeric, treat as experience ID and get its range
                    $experienceModel = \App\Models\Experience::find($experience);
                    $experienceRange = $experienceModel ? $experienceModel->experiences_range : null;
                } else {
                    // If string, treat as experience range directly
                    $experienceRange = $experience;
                }
            }
            // Build query to get loan rules matching the criteria
            $matrixQuery = \App\Models\LoanRule::with([
                'experience.loanType',
                'ficoBand',
                'transactionType',
                'rehabLimits.rehabLevel',
                'pricings.pricingTier'
            ])
                ->join('experiences', 'loan_rules.experience_id', '=', 'experiences.id')
                ->join('fico_bands', 'loan_rules.fico_band_id', '=', 'fico_bands.id')
                ->join('loan_types', 'experiences.loan_type_id', '=', 'loan_types.id')
                // Filter by credit score
                ->where('fico_bands.fico_min', '<=', $creditScore)
                ->where('fico_bands.fico_max', '>=', $creditScore)
                // Filter by experience range if provided
                ->when($experienceRange, function ($query) use ($experienceRange) {
                    $query->where('experiences.experiences_range', $experienceRange);
                })
                // Filter by loan type IDs
                ->when(!empty($loanTypeIds), function ($query) use ($loanTypeIds) {
                    $query->whereIn('loan_types.id', $loanTypeIds);
                })
                // Filter by transaction type if provided
                ->when($transactionTypeId, function ($query) use ($transactionTypeId) {
                    $query->where('loan_rules.transaction_type_id', $transactionTypeId);
                })
                ->orderByRaw("
                CASE experiences.experiences_range
                    WHEN '0' THEN 0 
                    WHEN '1-2' THEN 1 
                    WHEN '3-4' THEN 2 
                    WHEN '5-9' THEN 3 
                    WHEN '10+' THEN 4 
                    ELSE 99
                END
            ")
                ->orderBy('fico_bands.fico_min')
                ->orderBy('fico_bands.fico_max')
                ->select('loan_rules.*');

            $loanRules = $matrixQuery->get();

            // Transform the data to match the matrix format (same as LoanProgramController)
            $matrixData = $loanRules->map(function ($rule) {
                // Get rehab limits grouped by rehab level
                $rehabLimits = $rule->rehabLimits->keyBy('rehabLevel.name');

                // Get pricing data grouped by pricing tier
                $pricings = $rule->pricings->keyBy('pricingTier.price_range');

                return [
                    'loan_rule_id' => $rule->id,
                    'loan_type' => $rule->experience->loanType->name ?? 'N/A',
                    'loan_program' => $rule->experience->loanType->loan_program ?? null,
                    'display_name' => $rule->experience->loanType->loan_program
                        ? ($rule->experience->loanType->name . ' - ' . $rule->experience->loanType->loan_program)
                        : ($rule->experience->loanType->name ?? 'N/A'),
                    'experience' => $rule->experience->experiences_range ?? 'N/A',
                    'fico' => $rule->ficoBand->fico_range ?? 'N/A',
                    'transaction_type' => $rule->transactionType->name ?? 'N/A',
                    'max_total_loan' => $rule->max_total_loan,
                    'max_budget' => $rule->max_budget,

                    // Light Rehab
                    'light_ltc' => $rehabLimits->get('LIGHT REHAB')?->max_ltc,
                    'light_ltv' => $rehabLimits->get('LIGHT REHAB')?->max_ltv,

                    // Moderate Rehab
                    'moderate_ltc' => $rehabLimits->get('MODERATE REHAB')?->max_ltc,
                    'moderate_ltv' => $rehabLimits->get('MODERATE REHAB')?->max_ltv,

                    // Heavy Rehab
                    'heavy_ltc' => $rehabLimits->get('HEAVY REHAB')?->max_ltc,
                    'heavy_ltv' => $rehabLimits->get('HEAVY REHAB')?->max_ltv,

                    // Extensive Rehab
                    'extensive_ltc' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltc,
                    'extensive_ltv' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltv,
                    'extensive_ltfc' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltfc,

                    // Pricing < $250k
                    'ir_lt_250k' => $pricings->get('<250k')?->interest_rate,
                    'lp_lt_250k' => $pricings->get('<250k')?->lender_points,

                    // Pricing $250k-$500k
                    'ir_250_500k' => $pricings->get('250-500k')?->interest_rate,
                    'lp_250_500k' => $pricings->get('250-500k')?->lender_points,

                    // Pricing ≥ $500k
                    'ir_gte_500k' => $pricings->get('>=500k')?->interest_rate,
                    'lp_gte_500k' => $pricings->get('>=500k')?->lender_points,

                    // Additional loan type and loan program table data
                    'loan_type_and_loan_program_table' => [
                        'loan_term' => 0,
                        'intrest_rate' => 0,
                        'lender_points' => 0,
                        'max_ltv' => 0,
                        'max_ltc' => 0,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Loan matrix data retrieved successfully',
                'data' => $matrixData,
                'total_records' => $matrixData->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving loan matrix data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate business rules
     * 
     * @param float $creditScore
     * @param int $experience
     * @param float $rehabBudget
     * @param float $purchasePrice
     * @return array
     */
    private function validateBusinessRules($creditScore, $experience, $rehabBudget, $purchasePrice)
    {
        $notifications = [];
        $valid = true;

        // Rule 1: Minimum credit score allowed 660+
        if ($creditScore < 660) {
            $notifications[] = 'Minimum credit score allowed is 660+';
            $valid = false;
        }

        // Rule 3: 3+ Borrower Experience required for Heavy Rehab projects
        $rehabPercentage = ($rehabBudget / $purchasePrice) * 100;
        if ($rehabPercentage > 50 && $rehabPercentage <= 100 && $experience < 3) {
            $notifications[] = '3+ Borrower Experience required for Heavy Rehab projects';
            $valid = false;
        }

        return [
            'valid' => $valid,
            'notifications' => $notifications
        ];
    }

    /**
     * Calculate rehab category based on rehab budget percentage
     * 
     * @param float $rehabBudget
     * @param float $purchasePrice
     * @return string
     */
    private function calculateRehabCategory($rehabBudget, $purchasePrice)
    {
        // Calculate rehab percentage: rehab_budget / purchase_price * 100
        $rehabPercentage = ($rehabBudget / $purchasePrice) * 100;

        // Determine rehab category based on percentage
        if ($rehabPercentage <= 25) {
            return 'LIGHT REHAB'; // 0-25%
        } elseif ($rehabPercentage <= 50) {
            return 'MODERATE REHAB'; // 25%-50%
        } elseif ($rehabPercentage <= 100) {
            return 'HEAVY REHAB'; // 50%-100%
        } else {
            return 'EXTENSIVE REHAB'; // >100%
        }
    }

    /**
     * Get loan programs based on loan type
     * 
     * @param string $loanType
     * @return array
     */
    private function getLoanProgramsByType($loanType)
    {
        // Return appropriate loan programs based on loan type
        switch ($loanType) {
            case 'Fix and Flip':
                return ['FULL APPRAISAL', 'DESKTOP APPRAISAL'];
            case 'New Construction':
                return ['EXPERIENCED BUILDER', 'NEW BUILDER'];
            case 'DSCR Rental':
                return ['DSCR RENTAL']; // Assuming this exists
            default:
                return [];
        }
    }

    /**
     * Find matching loan rule from the matrix
     * 
     * @param float $creditScore
     * @param int $experience
     * @param string $loanProgram
     * @param string $transactionType
     * @param string $rehabCategory
     * @return \App\Models\LoanRule|null
     */
    private function findMatchingLoanRule($creditScore, $experience, $loanProgram, $transactionType, $rehabCategory)
    {
        // Find matching FICO band based on credit score
        $ficoBand = FicoBand::where('fico_min', '<=', $creditScore)
            ->where('fico_max', '>=', $creditScore)
            ->first();

        if (!$ficoBand) {
            return null;
        }

        // Find matching transaction type
        $transactionTypeModel = TransactionType::where('name', $transactionType)->first();

        if (!$transactionTypeModel) {
            return null;
        }

        // Find matching loan rule with all criteria
        $loanRule = LoanRule::whereHas('experience.loanType', function ($query) use ($loanProgram) {
            $query->where('loan_program', $loanProgram);
        })
            ->whereHas('experience', function ($query) use ($experience) {
                $query->where('min_experience', '<=', $experience)
                    ->where('max_experience', '>=', $experience);
            })
            ->where('fico_band_id', $ficoBand->id)
            ->where('transaction_type_id', $transactionTypeModel->id)
            ->with([
                'experience.loanType',
                'ficoBand',
                'transactionType',
                'rehabLimits.rehabLevel',
                'pricings.pricingTier'
            ])
            ->first();

        return $loanRule;
    }

    /**
     * Calculate loan amounts based on the rule and inputs
     * 
     * @param \App\Models\LoanRule $loanRule
     * @param float $purchasePrice
     * @param float $rehabBudget
     * @param float $arv
     * @param string $rehabCategory
     * @return array
     */
    private function calculateLoanAmounts($loanRule, $purchasePrice, $rehabBudget, $arv, $rehabCategory)
    {
        // Get rehab limits for the specific rehab category
        $rehabLimit = $loanRule->rehabLimits()
            ->whereHas('rehabLevel', function ($query) use ($rehabCategory) {
                $query->where('name', $rehabCategory);
            })
            ->first();

        // Default values if no rehab limits found
        $maxLtv = $rehabLimit ? $rehabLimit->max_ltv : 0;
        $maxLtc = $rehabLimit ? $rehabLimit->max_ltc : 0;
        $maxLtfc = $rehabLimit ? $rehabLimit->max_ltfc : 0;

        // Calculate maximum loan amounts based on LTV/LTC
        $maxLtvAmount = ($maxLtv / 100) * $arv;
        $maxLtcAmount = ($maxLtc / 100) * $purchasePrice + $rehabBudget;

        // Calculate maximum total loan based on rehab budget vs purchase price
        if ($rehabBudget >= $purchasePrice) {
            // If rehab budget >= purchase price, use LTFC calculation as well
            $maxLtfcAmount = ($maxLtfc / 100) * ($purchasePrice + $rehabBudget);
            $maxTotalLoan = min($maxLtvAmount, $maxLtcAmount, $maxLtfcAmount);
        } else {
            // If rehab budget < purchase price, use LTV and LTC only
            $maxTotalLoan = min($maxLtvAmount, $maxLtcAmount);
        }

        // Rule 2: Maximum loan size allowed $1,000,000 (from loan rule)
        $maxTotalLoan = min($maxTotalLoan, $loanRule->max_total_loan ?: 1000000);

        // Calculate maximum initial loan (purchase loan)
        $maxInitialLoan = max(0, $maxTotalLoan - $rehabBudget);

        // Calculate maximum rehab loan
        $maxRehabLoan = min($rehabBudget, $maxTotalLoan);

        return [
            'max_ltv' => $maxLtv,
            'max_ltc' => $maxLtc,
            'max_ltfc' => $maxLtfc,
            'max_initial_loan' => $maxInitialLoan,
            'max_rehab_loan' => $maxRehabLoan,
            'max_total_loan' => $maxTotalLoan
        ];
    }

    /**
     * Get pricing information based on loan amount
     * 
     * @param \App\Models\LoanRule $loanRule
     * @param float $loanAmount
     * @return array
     */
    private function getPricingInfo($loanRule, $loanAmount)
    {
        // Initialize default pricing values
        $pricingData = [
            'interest_rate_lt_250k' => 0,
            'lender_points_lt_250k' => 0,
            'interest_rate_250k_500k' => 0,
            'lender_points_250k_500k' => 0,
            'interest_rate_gte_500k' => 0,
            'lender_points_gte_500k' => 0,
        ];

        // Get pricing for different loan size tiers
        $pricings = $loanRule->pricings()->with('pricingTier')->get();

        foreach ($pricings as $pricing) {
            $tierName = $pricing->pricingTier->price_range ?? $pricing->pricingTier->tier_name;

            // Map pricing tiers to output fields
            switch ($tierName) {
                case '<250k':
                case 'Loan Size < $250K':
                    $pricingData['interest_rate_lt_250k'] = $pricing->interest_rate;
                    $pricingData['lender_points_lt_250k'] = $pricing->lender_points;
                    break;
                case '250-500k':
                case 'Loan Size $250K-$500K':
                    $pricingData['interest_rate_250k_500k'] = $pricing->interest_rate;
                    $pricingData['lender_points_250k_500k'] = $pricing->lender_points;
                    break;
                case '>=500k':
                case 'Loan Size ≥ $500K':
                    $pricingData['interest_rate_gte_500k'] = $pricing->interest_rate;
                    $pricingData['lender_points_gte_500k'] = $pricing->lender_points;
                    break;
            }
        }

        return $pricingData;
    }
}
