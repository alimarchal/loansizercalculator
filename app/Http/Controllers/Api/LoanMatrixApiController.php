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
            'broker_points' => 'required|numeric|min:0|max:100',
            'pay_off' => 'nullable|numeric|min:0|max:10000000',
            'rehab_completed' => 'nullable|numeric|min:0|max:10000000',
            'state' => 'required|string|max:2', // State code (e.g., 'CA', 'TX', 'NY')
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
        $brokerPoints = $request->broker_points;
        $payOff = $request->pay_off;
        $rehabCompleted = $request->rehab_completed;
        $state = $request->state;

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

            // Validate state for the selected loan type(s)
            if (!empty($loanTypeIds) && $state) {
                $isStateAllowed = false;

                foreach ($loanTypeIds as $loanTypeId) {
                    // Check if the state is allowed for this loan type
                    $allowedStates = \App\Models\LoanType::find($loanTypeId)
                        ->states()
                        ->where('code', strtoupper($state))
                        ->where('is_allowed', true)
                        ->exists();

                    if ($allowedStates) {
                        $isStateAllowed = true;
                        break;
                    }
                }

                if (!$isStateAllowed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'We do not lend in this selected state',
                        'error' => 'State not allowed for the selected loan type'
                    ], 400);
                }
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

            // Handle experience parameter - convert numeric input to range
            $experienceRange = null;
            $originalExperience = $experience;
            if ($experience) {
                if (is_numeric($experience)) {
                    // If numeric, find the experience range that contains this number
                    $experienceValue = (int) $experience;
                    $experienceModel = \App\Models\Experience::where('min_experience', '<=', $experienceValue)
                        ->where('max_experience', '>=', $experienceValue)
                        ->first();
                    $experienceRange = $experienceModel ? $experienceModel->experiences_range : null;
                } else {
                    // If string (like "1-2"), use it as the range directly
                    $experienceRange = $experience;
                    // Try to find a representative numeric value for this range
                    $experienceModel = \App\Models\Experience::where('experiences_range', $experience)->first();
                    $originalExperience = $experienceModel ? $experienceModel->min_experience : $experience;
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
            $matrixData = $loanRules->map(function ($rule) use ($request, $creditScore, $originalExperience, $loanType, $transactionType, $state) {
                // Get rehab limits grouped by rehab level
                $rehabLimits = $rule->rehabLimits->keyBy('rehabLevel.name');

                // Get pricing data grouped by pricing tier
                $pricings = $rule->pricings->keyBy('pricingTier.price_range');

                // Calculate rehab percentage and determine category
                $rehabPercentage = 0;
                $applicableRehabCategory = 'LIGHT REHAB'; // Default
                $applicableMaxLtv = 0;
                $applicableMaxLtc = 0;
                $applicableMaxLtfc = 0;

                if ($request->purchase_price && $request->rehab_budget) {
                    $rehabPercentage = ($request->rehab_budget / $request->purchase_price) * 100;

                    // Determine rehab category based on percentage
                    if ($rehabPercentage <= 25) {
                        $applicableRehabCategory = 'LIGHT REHAB'; // 0-25%
                    } elseif ($rehabPercentage <= 50) {
                        $applicableRehabCategory = 'MODERATE REHAB'; // 25%-50%
                    } elseif ($rehabPercentage <= 100) {
                        $applicableRehabCategory = 'HEAVY REHAB'; // 50%-100%
                    } else {
                        $applicableRehabCategory = 'EXTENSIVE REHAB'; // >100%
                    }

                    // Get the max_ltv, max_ltc, and max_ltfc for the applicable rehab category
                    $applicableRehabLimit = $rehabLimits->get($applicableRehabCategory);
                    if ($applicableRehabLimit) {
                        $applicableMaxLtv = $applicableRehabLimit->max_ltv ? (float) number_format((float) $applicableRehabLimit->max_ltv, 2, '.', '') : 0.00;
                        $applicableMaxLtc = $applicableRehabLimit->max_ltc ? (float) number_format((float) $applicableRehabLimit->max_ltc, 2, '.', '') : 0.00;
                        $applicableMaxLtfc = $applicableRehabLimit->max_ltfc ? (float) number_format((float) $applicableRehabLimit->max_ltfc, 2, '.', '') : 0.00;
                    }
                }

                // Calculate loan amounts using the loan calculation function
                $loanCalculations = $this->calculateLoanAmountsFromInputs(
                    $applicableMaxLtv,
                    $applicableMaxLtc,
                    $applicableMaxLtfc,
                    $request->purchase_price ?: 0,
                    $request->rehab_budget ?: 0,
                    $request->arv ?: 0,
                    $rule->experience->loanType->name ?? null
                );

                // Determine pricing tier based on total loan amount and get rates
                $pricingInfo = $this->getPricingByLoanAmount($pricings, $loanCalculations['total_loan_up_to'], $request->loan_term ?: 12);
                return [
                    'loan_rule_id' => $rule->id,
                    'loan_type' => $rule->experience->loanType->name ?? 'N/A',
                    'loan_program' => $rule->experience->loanType->loan_program ?? null,
                    'display_name' => $rule->experience->loanType->loan_program
                        ? ($rule->experience->loanType->name . ' - ' . $rule->experience->loanType->loan_program)
                        : ($rule->experience->loanType->name ?? 'N/A'),
                    'experience' => $originalExperience, // Show original input
                    'experience_range' => $rule->experience->experiences_range ?? 'N/A', // Show database range
                    'fico' => $rule->ficoBand->fico_range ?? 'N/A',
                    'transaction_type' => $rule->transactionType->name ?? 'N/A',
                    'max_total_loan' => $rule->max_total_loan ? (float) number_format((float) $rule->max_total_loan, 2, '.', '') : 0.00,
                    'max_budget' => $rule->max_budget ? (float) number_format((float) $rule->max_budget, 2, '.', '') : 0.00,

                    // Light Rehab (0-25%)
                    'light_rehab_0_25_percent_max_ltc' => $rehabLimits->get('LIGHT REHAB')?->max_ltc ? (float) number_format((float) $rehabLimits->get('LIGHT REHAB')->max_ltc, 2, '.', '') : 0.00,
                    'light_rehab_0_25_percent_max_ltv' => $rehabLimits->get('LIGHT REHAB')?->max_ltv ? (float) number_format((float) $rehabLimits->get('LIGHT REHAB')->max_ltv, 2, '.', '') : 0.00,

                    // Moderate Rehab (25-50%)
                    'moderate_rehab_25_50_percent_max_ltc' => $rehabLimits->get('MODERATE REHAB')?->max_ltc ? (float) number_format((float) $rehabLimits->get('MODERATE REHAB')->max_ltc, 2, '.', '') : 0.00,
                    'moderate_rehab_25_50_percent_max_ltv' => $rehabLimits->get('MODERATE REHAB')?->max_ltv ? (float) number_format((float) $rehabLimits->get('MODERATE REHAB')->max_ltv, 2, '.', '') : 0.00,

                    // Heavy Rehab (50-100%)
                    'heavy_rehab_50_100_percent_max_ltc' => $rehabLimits->get('HEAVY REHAB')?->max_ltc ? (float) number_format((float) $rehabLimits->get('HEAVY REHAB')->max_ltc, 2, '.', '') : 0.00,
                    'heavy_rehab_50_100_percent_max_ltv' => $rehabLimits->get('HEAVY REHAB')?->max_ltv ? (float) number_format((float) $rehabLimits->get('HEAVY REHAB')->max_ltv, 2, '.', '') : 0.00,

                    // Extensive Rehab (100%+)
                    'extensive_rehab_100_plus_percent_max_ltc' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltc ? (float) number_format((float) $rehabLimits->get('EXTENSIVE REHAB')->max_ltc, 2, '.', '') : 0.00,
                    'extensive_rehab_100_plus_percent_max_ltv' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltv ? (float) number_format((float) $rehabLimits->get('EXTENSIVE REHAB')->max_ltv, 2, '.', '') : 0.00,
                    'extensive_rehab_100_plus_percent_max_ltfc' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltfc ? (float) number_format((float) $rehabLimits->get('EXTENSIVE REHAB')->max_ltfc, 2, '.', '') : 0.00,

                    // Pricing < $250k
                    'loan_size_less_than_250k_interest_rate' => $pricings->get('<250k')?->interest_rate ? (float) number_format((float) $pricings->get('<250k')->interest_rate, 2, '.', '') : 0.00,
                    'loan_size_less_than_250k_lender_points' => $pricings->get('<250k')?->lender_points ? (float) number_format((float) $pricings->get('<250k')->lender_points, 2, '.', '') : 0.00,

                    // Pricing $250k-$500k
                    'loan_size_250k_to_500k_interest_rate' => $pricings->get('250-500k')?->interest_rate ? (float) number_format((float) $pricings->get('250-500k')->interest_rate, 2, '.', '') : 0.00,
                    'loan_size_250k_to_500k_lender_points' => $pricings->get('250-500k')?->lender_points ? (float) number_format((float) $pricings->get('250-500k')->lender_points, 2, '.', '') : 0.00,

                    // Pricing ≥ $500k
                    'loan_size_500k_and_above_interest_rate' => $pricings->get('>=500k')?->interest_rate ? (float) number_format((float) $pricings->get('>=500k')->interest_rate, 2, '.', '') : 0.00,
                    'loan_size_500k_and_above_lender_points' => $pricings->get('>=500k')?->lender_points ? (float) number_format((float) $pricings->get('>=500k')->lender_points, 2, '.', '') : 0.00,

                    // User inputs data
                    'user_inputs' => [
                        'credit_score' => $creditScore ? (float) number_format((float) $creditScore, 2, '.', '') : 0.00,
                        'experience' => $originalExperience,
                        'loan_type' => $loanType,
                        'transaction_type' => $transactionType,
                        'loan_term' => $request->loan_term ? (float) number_format((float) $request->loan_term, 2, '.', '') : 0.00,
                        'purchase_price' => $request->purchase_price ? (float) number_format((float) $request->purchase_price, 2, '.', '') : 0.00,
                        'arv' => $request->arv ? (float) number_format((float) $request->arv, 2, '.', '') : 0.00,
                        'rehab_budget' => $request->rehab_budget ? (float) number_format((float) $request->rehab_budget, 2, '.', '') : 0.00,
                        'state' => $state,
                    ],

                    // Additional loan type and loan program table data
                    'loan_type_and_loan_program_table' => [
                        'loan_term' => $request->loan_term ? $request->loan_term . ' Months' : '0 Months',
                        'intrest_rate' => $pricingInfo['interest_rate'],
                        'lender_points' => $pricingInfo['lender_points'],
                        'max_ltv' => $applicableMaxLtv,
                        'max_ltc' => $applicableMaxLtc,
                        'max_ltfc' => $applicableMaxLtfc,
                        'percentage_max_ltv_max_ltc' => (float) number_format($rehabPercentage, 2, '.', ''),
                        'rehab_category' => $applicableRehabCategory,
                        'purchase_loan_up_to' => $loanCalculations['purchase_loan_up_to'],
                        'rehab_loan_up_to' => $loanCalculations['rehab_loan_up_to'],
                        'total_loan_up_to' => $loanCalculations['total_loan_up_to'],
                    ],


                    'estimated_closing_statement' => [
                        'loan_amount_section' => [
                            'purchase_loan_amount' => $request->purchase_price ? (float) number_format((float) $request->purchase_price, 2, '.', '') : 0.00,
                            'rehab_loan_amount' => $request->rehab_budget ? (float) number_format((float) $request->rehab_budget, 2, '.', '') : 0.00,
                            'total_loan_amount' => (float) ($request->purchase_price + $request->rehab_budget),
                        ],
                        'buyer_related_charges' => [
                            ($transactionType === 'Refinance' ? 'payoff' : 'purchase_price') => $transactionType === 'Refinance'
                                ? ($request->pay_off ? (float) number_format((float) $request->pay_off, 2, '.', '') : 0.00)
                                : ($request->purchase_price ? (float) number_format((float) $request->purchase_price, 2, '.', '') : 0.00),
                            'rehab_budget' => $request->rehab_budget ? (float) number_format((float) $request->rehab_budget, 2, '.', '') : 0.00,
                            'sub_total_buyer_charges' => $transactionType === 'Refinance'
                                ? (($request->pay_off ? (float) $request->pay_off : 0.00) + ($request->rehab_budget ? (float) $request->rehab_budget : 0.00))
                                : (($request->purchase_price ? (float) $request->purchase_price : 0.00) + ($request->rehab_budget ? (float) $request->rehab_budget : 0.00)),
                        ],
                        'lender_related_charges' => [
                            'lender_origination_fee' => (float) ($request->purchase_price + $request->rehab_budget) * ($pricingInfo['lender_points'] / 100),
                            'broker_fee' => (float) ($request->purchase_price + $request->rehab_budget) * ($request->broker_points / 100),
                            'underwriting_processing_fee' => 1495.00,
                            'interest_reserves' =>
                                $rule->experience->loanType->loan_program === 'FULL APPRAISAL'
                                ? (float) number_format((float) (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['interest_rate'] / 100) / 12), 2, '.', '')
                                : 0.00,
                        ],
                        'title_other_charges' => [
                            'title_charges' => 0.00,
                            'property_insurance' => 0.00,
                            'legal_doc_prep_fee' =>
                                $rule->experience->loanType->loan_program === 'FULL APPRAISAL'
                                ? 995.00
                                : 0.00,
                            'subtotal_closing_costs' => 0.00,
                        ],

                        'cash_due_to_buyer' => 0.00,
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
     * Calculate loan amounts based on the rule and inputs using business formulas
     * 
     * @param float $maxLtv Maximum Loan to Value percentage
     * @param float $maxLtc Maximum Loan to Cost percentage  
     * @param float $maxLtfc Maximum Loan to Future Cost percentage
     * @param float $purchasePrice Property purchase price
     * @param float $rehabBudget Rehabilitation budget
     * @param float $arv After Repair Value
     * @param string $loanType Loan type (Fix and Flip, New Construction, etc.)
     * @return array
     */
    private function calculateLoanAmountsFromInputs($maxLtv, $maxLtc, $maxLtfc, $purchasePrice, $rehabBudget, $arv, $loanType = null)
    {
        // Initialize default values
        $totalLoanUpTo = 0;
        $purchaseLoanUpTo = 0;
        $rehabLoanUpTo = 0;

        // Only calculate if we have the required inputs
        if ($purchasePrice > 0 && $arv > 0) {

            // Calculate Max LTV Amount: Max LTV % × ARV
            $maxLtvAmount = ($maxLtv / 100) * $arv;

            // Calculate Max LTC Amount: (Max LTC % × Purchase Price) + Rehab Budget  
            $maxLtcAmount = (($maxLtc / 100) * $purchasePrice) + $rehabBudget;

            // Special calculation for New Construction when rehab budget > purchase price
            if ($loanType === 'New Construction' && $rehabBudget > $purchasePrice) {
                // For New Construction: Total Cost = Purchase Price + Rehab Budget
                $totalCost = $purchasePrice + $rehabBudget;

                // Calculate Max LTFC Amount: Max LTFC % × Total Cost
                $maxLtfcAmount = ($maxLtfc / 100) * $totalCost;

                // Get the minimum of Max LTV Amount and Max LTFC Amount
                $totalLoanUpTo = min($maxLtvAmount, $maxLtfcAmount);

            } elseif ($rehabBudget >= $purchasePrice) {
                // Original logic for Fix and Flip when rehab budget >= purchase price
                // MAX TOTAL LOAN = The Minimum of (Max LTV × ARV) & ((Max LTC × Purchase Price) + Rehab Budget) & (Max LTFC × (Purchase Price + Rehab Budget))

                // Calculate Max LTFC Amount: Max LTFC % × (Purchase Price + Rehab Budget)
                $maxLtfcAmount = ($maxLtfc / 100) * ($purchasePrice + $rehabBudget);

                // Get the minimum of all three amounts
                $totalLoanUpTo = min($maxLtvAmount, $maxLtcAmount, $maxLtfcAmount);

            } else {
                // IF REHAB BUDGET < PURCHASE PRICE  
                // MAX TOTAL LOAN = The Minimum of (Max LTV % × ARV) & ((Max LTC × Purchase Price) + Rehab Budget)

                // Get the minimum of LTV and LTC amounts (no LTFC consideration)
                $totalLoanUpTo = min($maxLtvAmount, $maxLtcAmount);
            }

            // Calculate individual loan components based on total loan capacity
            // Purchase Loan = Total Loan - Rehab Budget (but not less than 0)
            $purchaseLoanUpTo = max(0, $totalLoanUpTo - $rehabBudget);

            // Rehab Loan = Minimum of (Rehab Budget, Total Loan Capacity)
            $rehabLoanUpTo = min($rehabBudget, $totalLoanUpTo);
        }

        return [
            'total_loan_up_to' => (float) number_format($totalLoanUpTo, 2, '.', ''),
            'purchase_loan_up_to' => (float) number_format($purchaseLoanUpTo, 2, '.', ''),
            'rehab_loan_up_to' => (float) number_format($rehabLoanUpTo, 2, '.', ''),
        ];
    }

    /**
     * Get pricing information based on loan amount with loan term adjustments
     * 
     * @param \Illuminate\Support\Collection $pricings Collection of pricing data
     * @param float $loanAmount Total loan amount to determine pricing tier
     * @param int $loanTerm Loan term in months
     * @return array
     */
    private function getPricingByLoanAmount($pricings, $loanAmount, $loanTerm = 12)
    {
        $interestRate = 0;
        $lenderPoints = 0;

        // Determine pricing tier based on loan amount
        if ($loanAmount < 250000) {
            // < $250K pricing tier
            $pricing = $pricings->get('<250k');
            if ($pricing) {
                $interestRate = (float) $pricing->interest_rate;
                $lenderPoints = (float) $pricing->lender_points;
            }
        } elseif ($loanAmount < 500000) {
            // $250K-$500K pricing tier  
            $pricing = $pricings->get('250-500k');
            if ($pricing) {
                $interestRate = (float) $pricing->interest_rate;
                $lenderPoints = (float) $pricing->lender_points;
            }
        } else {
            // ≥ $500K pricing tier
            $pricing = $pricings->get('>=500k');
            if ($pricing) {
                $interestRate = (float) $pricing->interest_rate;
                $lenderPoints = (float) $pricing->lender_points;
            }
        }

        // Apply loan term adjustment: +0.50% if loan term is 18 months
        if ($loanTerm == 18) {
            $interestRate += 0.00;
            $lenderPoints += 0.50;
        }

        return [
            'interest_rate' => (float) number_format($interestRate, 2, '.', ''),
            'lender_points' => (float) number_format($lenderPoints, 2, '.', ''),
        ];
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

    /**
     * Get available property types and states for a specific loan type
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoanTypeOptions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $loanTypeName = $request->input('loan_type');

        try {
            // Get all loan programs for the selected loan type
            $loanTypes = LoanType::where('name', $loanTypeName)->get();

            if ($loanTypes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan type not found'
                ], 404);
            }

            // Collect all unique property types and states across all loan programs
            $propertyTypes = collect();
            $states = collect();

            foreach ($loanTypes as $loanType) {
                // Get property types for this loan type
                $loanTypePropertyTypes = $loanType->propertyTypes;
                $propertyTypes = $propertyTypes->merge($loanTypePropertyTypes);

                // Get states for this loan type
                $loanTypeStates = $loanType->states;
                $states = $states->merge($loanTypeStates);
            }

            // Remove duplicates and format the response
            $uniquePropertyTypes = $propertyTypes->unique('id')->values()->map(function ($propertyType) {
                return [
                    'id' => $propertyType->id,
                    'name' => $propertyType->name,
                ];
            });

            $uniqueStates = $states->unique('id')->values()->map(function ($state) {
                return [
                    'id' => $state->id,
                    'code' => $state->code,
                    'name' => $state->name,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Loan type options retrieved successfully',
                'data' => [
                    'property_types' => $uniquePropertyTypes,
                    'states' => $uniqueStates,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving loan type options',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
