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
use Illuminate\Support\Facades\DB;

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
            'title_charges' => 'nullable|numeric|min:0|max:10000000',
            'property_insurance' => 'nullable|numeric|min:0|max:10000000',
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
        $titleCharges = $request->title_charges;
        $propertyInsurance = $request->property_insurance;

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

            // Get maximum loan amount from the database for validation
            $maxLoanAmountFromDb = $loanRules->max('max_total_loan') ?: 0;

            // Validate business rules before processing loan rules
            $businessValidation = $this->validateBusinessRules(
                $creditScore,
                is_numeric($originalExperience) ? (int) $originalExperience : 0,
                $request->rehab_budget ?: 0,
                $request->purchase_price ?: 0,
                ($request->purchase_price ?: 0) + ($request->rehab_budget ?: 0), // Total loan amount estimation
                $loanType,
                null, // loan program - we'll check this per rule
                null, // dscr - not provided in this endpoint
                null, // property type - not provided in this endpoint
                $maxLoanAmountFromDb // Pass the dynamic max loan amount from database
            );

            // If business rules fail, return notifications
            if (!$businessValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan application does not meet qualification criteria. ' . implode(' ', $businessValidation['notifications']),
                    'disqualifier_notifications' => $businessValidation['notifications'],
                    'data' => [],
                    'total_records' => 0
                ], 400);
            }

            // Transform the data to match the matrix format (same as LoanProgramController)
            $matrixData = $loanRules->map(function ($rule) use ($request, $creditScore, $originalExperience, $loanType, $transactionType, $brokerPoints, $state, $titleCharges, $propertyInsurance) {
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
                    loanType: $rule->experience->loanType->name ?? null
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
                        'broker_points' => $brokerPoints ? (float) number_format((float) $brokerPoints, 2, '.', '') : 0.00,
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
                            'purchase_loan_amount' => $loanCalculations['purchase_loan_up_to'],
                            'rehab_loan_amount' => $loanCalculations['rehab_loan_up_to'],
                            'total_loan_amount' => (float) ($loanCalculations['purchase_loan_up_to'] + $loanCalculations['rehab_loan_up_to']),
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
                            'underwriting_processing_fee' => $rule->experience->loanType->underwritting_fee ? (float) number_format((float) $rule->experience->loanType->underwritting_fee, 2, '.', '') : 0.00,
                            'interest_reserves' =>
                                $rule->experience->loanType->loan_program === 'FULL APPRAISAL'
                                ? (float) number_format((float) (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['interest_rate'] / 100) / 12), 2, '.', '')
                                : 0.00,
                        ],
                        'title_other_charges' => [
                            'title_charges' => (float) $titleCharges,
                            'property_insurance' => (float) $propertyInsurance,
                            'legal_doc_prep_fee' => $rule->experience->loanType->legal_doc_prep_fee ? (float) number_format((float) $rule->experience->loanType->legal_doc_prep_fee, 2, '.', '') : 0.00,
                            'subtotal_closing_costs' => (float) number_format(
                                (float) $titleCharges +
                                (float) $propertyInsurance +
                                ($rule->experience->loanType->legal_doc_prep_fee ? (float) $rule->experience->loanType->legal_doc_prep_fee : 0.00) +
                                (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['lender_points'] / 100)) +
                                (($request->purchase_price + $request->rehab_budget) * ($request->broker_points / 100)) +
                                ($rule->experience->loanType->underwritting_fee ? (float) $rule->experience->loanType->underwritting_fee : 0.00) +
                                ($rule->experience->loanType->loan_program === 'FULL APPRAISAL'
                                    ? (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['interest_rate'] / 100) / 12)
                                    : 0.00),
                                2,
                                '.',
                                ''
                            ),
                        ],

                        'cash_due_to_buyer' => (float) number_format(
                            (($request->purchase_price + $request->rehab_budget) +
                                ((float) $titleCharges +
                                    (float) $propertyInsurance +
                                    ($rule->experience->loanType->legal_doc_prep_fee ? (float) $rule->experience->loanType->legal_doc_prep_fee : 0.00) +
                                    (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['lender_points'] / 100)) +
                                    (($request->purchase_price + $request->rehab_budget) * ($request->broker_points / 100)) +
                                    ($rule->experience->loanType->underwritting_fee ? (float) $rule->experience->loanType->underwritting_fee : 0.00) +
                                    ($rule->experience->loanType->loan_program === 'FULL APPRAISAL'
                                        ? (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['interest_rate'] / 100) / 12)
                                        : 0.00))) -
                            ($transactionType === 'Refinance'
                                ? (($request->pay_off ? (float) $request->pay_off : 0.00) + ($request->rehab_budget ? (float) $request->rehab_budget : 0.00))
                                : (($request->purchase_price ? (float) $request->purchase_price : 0.00) + ($request->rehab_budget ? (float) $request->rehab_budget : 0.00))),
                            2,
                            '.',
                            ''
                        ),
                    ],




                ];
            });

            // Check if any loan data was found and return appropriate response
            if ($matrixData->count() > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loan matrix data retrieved successfully',
                    'data' => $matrixData,
                    'total_records' => $matrixData->count()
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No loan programs found matching your criteria. Please adjust your search parameters and try again.',
                    'data' => [],
                    'total_records' => 0
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving loan matrix data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate business rules and return disqualifier notifications
     * 
     * @param float $creditScore
     * @param int $experience
     * @param float $rehabBudget
     * @param float $purchasePrice
     * @param float $totalLoanAmount
     * @param string $loanType
     * @param string $loanProgram
     * @param float $dscr
     * @param string $propertyType
     * @param float $maxLoanAmountFromDb
     * @return array
     */
    private function validateBusinessRules($creditScore, $experience, $rehabBudget, $purchasePrice, $totalLoanAmount = 0, $loanType = null, $loanProgram = null, $dscr = null, $propertyType = null, $maxLoanAmountFromDb = 0)
    {
        $notifications = [];
        $valid = true;

        // Loan type specific validations
        switch ($loanType) {
            case 'Fix and Flip':
                // Fix and Flip specific rules
                if ($creditScore < 660) {
                    $notifications[] = 'Credit: Minimum credit score allowed 660+';
                    $valid = false;
                }

                if ($maxLoanAmountFromDb > 0 && $totalLoanAmount > $maxLoanAmountFromDb) {
                    $notifications[] = 'Loan Size: Maximum Loan size allowed $' . number_format($maxLoanAmountFromDb, 0);
                    $valid = false;
                } elseif ($totalLoanAmount > 0 && $totalLoanAmount < 50000) {
                    $notifications[] = 'Loan Size: Minimum Loan Size allowed is $50,000';
                    $valid = false;
                }

                // Calculate rehab percentage for experience validation
                $rehabPercentage = $purchasePrice > 0 ? ($rehabBudget / $purchasePrice) * 100 : 0;

                // 3+ Borrower Experience required for Heavy Rehab projects (50-100%)
                if ($rehabPercentage > 50 && $rehabPercentage <= 100 && $experience < 3) {
                    $notifications[] = 'Experience: 3+ Borrower Experience required for Heavy Rehab projects';
                    $valid = false;
                }

                // 3+ Borrower Experience required for Extensive Rehab projects (>100%)
                if ($rehabPercentage > 100 && $experience < 3) {
                    $notifications[] = 'Experience: 3+ Borrower Experience required for Extensive Rehab Project';
                    $valid = false;
                }

                // Property Type validation for Fix and Flip
                if ($propertyType && !in_array($propertyType, ['Single Family', 'Townhomes', 'Condos', 'Multi-Family', 'Commercial'])) {
                    $notifications[] = 'Property Type: Property Type not eligible for Fix and Flip';
                    $valid = false;
                }
                break;

            case 'New Construction':
                // New Construction specific rules
                if ($creditScore < 680) {
                    $notifications[] = 'Credit: Minimum credit score allowed 680+ for New Construction';
                    $valid = false;
                }

                if ($maxLoanAmountFromDb > 0 && $totalLoanAmount > $maxLoanAmountFromDb) {
                    $notifications[] = 'Loan Size: Maximum Loan size allowed $' . number_format($maxLoanAmountFromDb, 0) . '. Contact Loan officer for Pricing';
                    $valid = false;
                } elseif ($totalLoanAmount > 0 && $totalLoanAmount < 200000) {
                    $notifications[] = 'Loan Size: Minimum Loan size allowed $200,000 for New Construction. Contact Loan officer for Pricing';
                    $valid = false;
                }

                // Experience and FICO validation for Experienced Builder Program
                if ($loanProgram === 'EXPERIENCED BUILDER' && ($experience < 3 || $creditScore < 680)) {
                    $notifications[] = 'Experience: 3+ Borrower Experience & 680+ FICO required for Experienced Builder Program';
                    $valid = false;
                }

                // Property Type validation for New Construction
                if ($propertyType && !in_array($propertyType, ['Single Family', 'Townhomes', 'Condos'])) {
                    $notifications[] = 'Property Type: Eligible property type for New Construction is Single Family, Townhomes, Condos';
                    $valid = false;
                }

                // Additional FICO check for New Construction
                if ($creditScore < 680) {
                    $notifications[] = 'FICO: Minimum FICO eligible for New Construction 680+';
                    $valid = false;
                }
                break;

            case 'DSCR Rental':
                // DSCR Rental specific rules
                if ($creditScore < 660) {
                    $notifications[] = 'Credit: Minimum credit score allowed 660+ for DSCR Loan';
                    $valid = false;
                }

                if ($maxLoanAmountFromDb > 0 && $totalLoanAmount > $maxLoanAmountFromDb) {
                    $notifications[] = 'Loan Size: Maximum Loan size allowed $' . number_format($maxLoanAmountFromDb, 0) . '. Contact Loan officer for Pricing';
                    $valid = false;
                } elseif ($totalLoanAmount > 0 && $totalLoanAmount < 200000) {
                    $notifications[] = 'Loan Size: Minimum Loan size allowed $200,000 for DSCR Loan. Contact Loan officer for Pricing';
                    $valid = false;
                }

                // DSCR validation
                if ($dscr !== null && $dscr < 0.80) {
                    $notifications[] = 'DSCR: Minimum DSCR allowed for DSCR loan is 0.80x';
                    $valid = false;
                }

                // Property Type validation for DSCR
                if ($propertyType && !in_array($propertyType, ['Single Family', 'Townhomes', 'Condos'])) {
                    $notifications[] = 'Property Type: Eligible property type for DSCR is Single Family, Townhomes, Condos';
                    $valid = false;
                }
                break;

            default:
                // Default validation (fallback to Fix and Flip rules)
                if ($creditScore < 660) {
                    $notifications[] = 'Credit: Minimum credit score allowed 660+';
                    $valid = false;
                }

                if ($maxLoanAmountFromDb > 0 && $totalLoanAmount > $maxLoanAmountFromDb) {
                    $notifications[] = 'Loan Size: Maximum Loan size allowed $' . number_format($maxLoanAmountFromDb, 0);
                    $valid = false;
                } elseif ($totalLoanAmount > 0 && $totalLoanAmount < 50000) {
                    $notifications[] = 'Loan Size: Minimum Loan Size allowed is $50,000';
                    $valid = false;
                }
                break;
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
     * Get DSCR loan matrix data for all three loan programs
     * Returns the complete DSCR matrix with all adjustment tables
     * Supports filtering by loan_program parameter and validates DSCR loan inputs
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoanMatrixDscr(Request $request)
    {
        // Enhanced validation for DSCR loan inputs
        $validator = Validator::make($request->all(), [
            // Optional filter parameter
            'loan_program' => 'nullable|string|in:Loan Program #1,Loan Program #2,Loan Program #3',

            // Required DSCR loan validation inputs
            'credit_score' => 'required|integer|min:300|max:850',
            'experience' => 'required|integer|min:0',
            'broker_points' => 'required|numeric|min:0|max:100',
            'loan_type' => 'required|string|in:DSCR Rental Loans',
            'transaction_type' => 'required|string',
            'property_type' => 'required|string',
            'purchase_price' => 'required|numeric|min:1',
            'occupancy_type' => 'required|string',
            'monthly_market_rent' => 'required|numeric|min:0',
            'annual_tax' => 'required|numeric|min:0',
            'annual_insurance' => 'required|numeric|min:0',
            'annual_hoa' => 'required|numeric|min:0',
            'dscr' => 'required|numeric|min:0.01',
            'purchase_date' => 'nullable|date',
            'payoff_amount' => 'required|numeric|min:0',
            'loan_term' => 'required|string|in:10 Year Interest Only,30 Year Fixed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Extract parameters
            $creditScore = $request->credit_score;
            $experience = $request->experience;
            $transactionType = $request->transaction_type;
            $propertyType = $request->property_type;
            $purchasePrice = $request->purchase_price;
            $occupancyType = $request->occupancy_type;
            $dscr = $request->dscr;

            // Validate transaction type exists in database
            $transactionTypeModel = \App\Models\TransactionType::where('name', $transactionType)->first();
            if (!$transactionTypeModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction type. Please select a valid transaction type.',
                    'available_transaction_types' => \App\Models\TransactionType::pluck('name')->toArray()
                ], 400);
            }

            // Validate property type exists in database
            $propertyTypeModel = \App\Models\PropertyType::where('name', $propertyType)->first();
            if (!$propertyTypeModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid property type. Please select a valid property type.',
                    'available_property_types' => \App\Models\PropertyType::pluck('name')->toArray()
                ], 400);
            }

            // Validate occupancy type exists in database
            $occupancyTypeModel = \App\Models\OccupancyTypes::where('name', $occupancyType)->first();
            if (!$occupancyTypeModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid occupancy type. Please select a valid occupancy type.',
                    'available_occupancy_types' => \App\Models\OccupancyTypes::pluck('name')->toArray()
                ], 400);
            }

            // Run DSCR-specific disqualifier validation
            $dscrValidation = $this->validateDscrBusinessRules(
                $creditScore,
                $experience,
                $purchasePrice,
                $dscr,
                $propertyType
            );

            // If validation fails, return disqualifier notifications
            if (!$dscrValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'DSCR loan application does not meet qualification criteria',
                    'disqualifier_notifications' => $dscrValidation['notifications'],
                    'user_inputs' => [
                        'credit_score' => $creditScore,
                        'experience' => $experience,
                        'broker_points' => $request->broker_points,
                        'loan_type' => $request->loan_type,
                        'transaction_type' => $transactionType,
                        'property_type' => $propertyType,
                        'purchase_price' => $purchasePrice,
                        'occupancy_type' => $occupancyType,
                        'monthly_market_rent' => $request->monthly_market_rent,
                        'annual_tax' => $request->annual_tax,
                        'annual_insurance' => $request->annual_insurance,
                        'annual_hoa' => $request->annual_hoa,
                        'dscr' => $dscr,
                        'purchase_date' => $request->purchase_date,
                        'payoff_amount' => $request->payoff_amount,
                        'loan_term' => $request->loan_term,
                    ]
                ], 400);
            }
            // Get requested loan program or all three by default
            $requestedProgram = $request->get('loan_program');
            $loanPrograms = $requestedProgram ? [$requestedProgram] : [
                'Loan Program #1',
                'Loan Program #2',
                'Loan Program #3'
            ];

            // Build the raw SQL query for DSCR matrix (same as LoanProgramController)
            $sql = "
                SELECT * FROM (
                  /* ---------- FICO × LTV (program-aware) ---------- */
                  SELECT
                    'FICO'                AS row_group,
                    lt.loan_program       AS program,
                    fb.fico_range         AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN fla.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN fla.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN fla.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN fla.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN fla.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN fla.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN fla.adjustment_pct END) AS `80% LTV`
                  FROM fico_bands fb
                  JOIN fico_ltv_adjustments fla ON fla.fico_band_id = fb.id
                  JOIN ltv_ratios lr ON lr.id = fla.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = fla.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, fb.fico_min, fb.fico_range

                  UNION ALL

                  /* ---------- Loan Amount × LTV (program-aware) ---------- */
                  SELECT
                    'Loan Amount'         AS row_group,
                    lt.loan_program       AS program,
                    la.amount_range       AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN l.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN l.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN l.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN l.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN l.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN l.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN l.adjustment_pct END) AS `80% LTV`
                  FROM loan_amounts la
                  JOIN loan_amount_ltv_adjustments l ON l.loan_amount_id = la.id
                  JOIN ltv_ratios lr ON lr.id = l.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = l.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, la.display_order, la.amount_range

                  UNION ALL

                  /* ---------- Property Type × LTV (program-aware) ---------- */
                  SELECT
                    'Property Type'       AS row_group,
                    lt.loan_program       AS program,
                    pt.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN p.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN p.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN p.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN p.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN p.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN p.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN p.adjustment_pct END) AS `80% LTV`
                  FROM property_type_ltv_adjustments p
                  JOIN property_types pt ON pt.id = p.property_type_id
                  JOIN ltv_ratios lr ON lr.id = p.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = p.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, pt.name

                  UNION ALL

                  /* ---------- Occupancy × LTV (program-aware) ---------- */
                  SELECT
                    'Occupancy'           AS row_group,
                    lt.loan_program       AS program,
                    oc.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN o.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN o.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN o.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN o.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN o.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN o.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN o.adjustment_pct END) AS `80% LTV`
                  FROM occupancy_ltv_adjustments o
                  JOIN occupancy_types oc ON oc.id = o.occupancy_type_id
                  JOIN ltv_ratios lr ON lr.id = o.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = o.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, oc.name

                  UNION ALL

                  /* ---------- Transaction Type × LTV (program-aware) ---------- */
                  SELECT
                    'Transaction Type'    AS row_group,
                    lt.loan_program       AS program,
                    tt.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN t.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN t.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN t.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN t.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN t.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN t.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN t.adjustment_pct END) AS `80% LTV`
                  FROM transaction_type_ltv_adjustments t
                  JOIN transaction_types tt ON tt.id = t.transaction_type_id
                  JOIN ltv_ratios lr ON lr.id = t.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = t.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, tt.name

                  UNION ALL

                  /* ---------- DSCR Range × LTV (program-aware) ---------- */
                  SELECT
                    'DSCR'                AS row_group,
                    lt.loan_program       AS program,
                    dr.dscr_range         AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN d.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN d.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN d.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN d.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN d.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN d.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN d.adjustment_pct END) AS `80% LTV`
                  FROM dscr_ltv_adjustments d
                  JOIN dscr_ranges dr ON dr.id = d.dscr_range_id
                  JOIN ltv_ratios lr ON lr.id = d.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = d.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, dr.dscr_range

                  UNION ALL

                  /* ---------- Prepay × LTV (program-aware) ---------- */
                  SELECT
                    'Pre Pay'             AS row_group,
                    lt.loan_program       AS program,
                    pp.prepay_name        AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN p.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN p.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN p.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN p.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN p.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN p.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN p.adjustment_pct END) AS `80% LTV`
                  FROM pre_pay_ltv_adjustments p
                  JOIN prepay_periods pp ON pp.id = p.pre_pay_id
                  JOIN ltv_ratios lr ON lr.id = p.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = p.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, pp.prepay_name

                  UNION ALL

                  /* ---------- Loan Type × LTV (program-aware) ---------- */
                  SELECT
                    'Loan Type'           AS row_group,
                    lt.loan_program       AS program,
                    ltd.loan_type_dscr_name AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN l.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN l.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN l.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN l.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN l.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN l.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN l.adjustment_pct END) AS `80% LTV`
                  FROM loan_type_dscr_ltv_adjustments l
                  JOIN loan_types_dscrs ltd ON ltd.id = l.dscr_loan_type_id
                  JOIN ltv_ratios lr ON lr.id = l.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = l.loan_type_id
                  WHERE lt.loan_program " . ($requestedProgram ? "= ?" : "IN (?, ?, ?)") . "
                  GROUP BY lt.loan_program, ltd.loan_type_dscr_name
                ) AS big_matrix
                ORDER BY
                  FIELD(row_group,
                        'FICO','Loan Amount','Property Type','Occupancy',
                        'Transaction Type','DSCR','Pre Pay','Loan Type'),
                  program IS NULL, program, row_label
            ";

            // Prepare parameters based on filtering
            $parameters = [];
            $parametersPerQuery = $requestedProgram ? 1 : 3;

            for ($i = 0; $i < 8; $i++) { // 8 UNION queries
                if ($requestedProgram) {
                    $parameters[] = $requestedProgram;
                } else {
                    $parameters = array_merge($parameters, $loanPrograms);
                }
            }

            // Execute the query
            $matrixData = DB::select($sql, $parameters);

            // Group data by loan program and then by row_group
            $groupedByProgram = collect($matrixData)->groupBy('program')->map(function ($programData) {
                return $programData->groupBy('row_group');
            });

            // Transform the data into the response format
            $responseData = [];
            foreach ($loanPrograms as $program) {
                $programData = $groupedByProgram->get($program, collect());

                // Format each category for this program
                $formattedProgramData = [
                    'loan_program' => $program,
                    'categories' => [],
                    'loan_program_values' => [
                        'loan_term' => 0,
                        'max_ltv' => 0,
                        'monthly_payment' => 0,
                        'interest_rate' => 0,
                        'lender_points' => 0,
                        'pre_pay_penalty' => 0,
                    ],
                    'interest_rate_formula' => [
                        'starting_rate' => 0,
                        'ltv_fico' => 0,
                        'loan_amount' => 0,
                        'property_type' => 0,
                        'occupancy' => 0,
                        'transaction_type' => 0,
                        'dscr' => 0,
                        'pre_pay' => 0,
                        'loan_type' => 0,
                        'calculated_interest_rate' => 0,
                    ],
                    'ltv_formula' => [
                        'fico' => [
                            'input' => 0,
                            'max_ltv' => 0,
                        ],
                        'transaction_type' => [
                            'input' => 0,
                            'max_ltv' => 0,
                        ],
                        'loan_amount' => [
                            'input' => 0,
                            'max_ltv' => 0,
                        ],
                        'dscr' => [
                            'input' => 0,
                            'max_ltv' => 0,
                        ],
                        'occupancy' => [
                            'input' => 0,
                            'max_ltv' => 0,
                        ],
                        'approved_max_ltv' => [
                            'input' => 0,
                            'max_ltv' => 0,
                        ],
                    ],
                ];

                foreach ($programData as $category => $rows) {
                    $formattedProgramData['categories'][$category] = $rows->map(function ($row) {
                        return [
                            'row_label' => $row->row_label,
                            'adjustments' => [
                                '50% LTV or less' => $this->formatAdjustment($row->{'50% LTV or less'}),
                                '55% LTV' => $this->formatAdjustment($row->{'55% LTV'}),
                                '60% LTV' => $this->formatAdjustment($row->{'60% LTV'}),
                                '65% LTV' => $this->formatAdjustment($row->{'65% LTV'}),
                                '70% LTV' => $this->formatAdjustment($row->{'70% LTV'}),
                                '75% LTV' => $this->formatAdjustment($row->{'75% LTV'}),
                                '80% LTV' => $this->formatAdjustment($row->{'80% LTV'}),
                            ]
                        ];
                    })->values()->toArray();
                }

                $responseData[] = $formattedProgramData;
            }

            return response()->json([
                'success' => true,
                'message' => 'DSCR Loan Matrix Retrieved Successfully',
                'data' => $responseData,
                'total_programs' => count($loanPrograms),
                'filtered_by' => $requestedProgram ? "Single program: $requestedProgram" : "All programs"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving DSCR loan matrix data',
                'error' => $e->getMessage()
            ], 500);
        }
    }    /**
         * Format adjustment percentage for display
         * 
         * @param mixed $value
         * @return string
         */
    private function formatAdjustment($value)
    {
        if ($value === null) {
            return 'N/A';
        }

        // Convert to percentage format
        return number_format((float) $value * 100, 4) . '%';
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

    /**
     * Validate DSCR-specific business rules and return disqualifier notifications
     * 
     * @param float $creditScore
     * @param int $experience
     * @param float $purchasePrice
     * @param float $dscr
     * @param string $propertyType
     * @return array
     */
    private function validateDscrBusinessRules($creditScore, $experience, $purchasePrice, $dscr, $propertyType)
    {
        $notifications = [];
        $valid = true;

        // Credit Score Validation
        if ($creditScore < 660) {
            $notifications[] = 'Credit: Minimum credit score allowed 660+ for DSCR Loan';
            $valid = false;
        }

        // Loan Size Validation
        if ($purchasePrice > 1500000) {
            $notifications[] = 'Loan Size: Maximum Loan size allowed $1,500,000. Contact Loan officer for Pricing';
            $valid = false;
        }

        if ($purchasePrice < 200000) {
            $notifications[] = 'Loan Size: Minimum Loan size allowed $200,000 for DSCR Loan. Contact Loan officer for Pricing';
            $valid = false;
        }

        // DSCR Validation
        if ($dscr < 0.80) {
            $notifications[] = 'DSCR: Minimum DSCR allowed for DSCR loan is 0.80x';
            $valid = false;
        }

        // Property Type Validation - Get eligible property types from database
        $eligiblePropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Townhomes',
            'Condos'
        ])->pluck('name')->toArray();

        if (!in_array($propertyType, $eligiblePropertyTypes)) {
            $notifications[] = 'Property Type: Eligible property type for DSCR is Single Family, Townhomes, Condos';
            $valid = false;
        }

        return [
            'valid' => $valid,
            'notifications' => $notifications,
            'eligible_property_types' => $eligiblePropertyTypes
        ];
    }
}
