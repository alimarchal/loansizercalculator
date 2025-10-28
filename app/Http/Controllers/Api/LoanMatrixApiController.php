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
        // Get available loan programs from database dynamically
        $availableLoanPrograms = \App\Models\LoanType::select('loan_program')
            ->distinct()
            ->whereNotNull('loan_program')
            ->pluck('loan_program')
            ->toArray();

        // Validate incoming request parameters (accept both IDs and names)
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'credit_score' => 'required|integer|min:300|max:850',
            'experience' => 'nullable|string', // Can be ID or range like "1-2"
            'loan_type' => 'nullable|string', // Can be ID or name like "Fix and Flip"
            'loan_program' => 'nullable|string|in:' . implode(',', $availableLoanPrograms), // Dynamic loan program filter
            'transaction_type' => 'nullable|string', // Can be ID or name like "Purchase"
            'loan_term' => 'nullable|integer|min:6|max:36',
            'purchase_price' => 'nullable|numeric|min:10000|max:10000000',
            'arv' => 'nullable|numeric|min:10000|max:10000000',
            'rehab_budget' => 'nullable|numeric|min:0|max:5000000',
            'broker_points' => 'required|numeric|min:0|max:100',
            'payoff_amount' => 'nullable|numeric|min:0|max:10000000',
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
        $loanProgram = $request->loan_program;
        $transactionType = $request->transaction_type;
        $brokerPoints = $request->broker_points;
        $payOff = $request->payoff_amount;
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
                // Filter by loan program if provided
                ->when($loanProgram, function ($query) use ($loanProgram) {
                    $query->where('loan_types.loan_program', $loanProgram);
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

            // Transform the data to match the matrix format and validate each rule individually
            $validLoanRules = collect();
            $allNotifications = collect();

            // If no specific loan program is requested, use more lenient validation to show available options
            $useGeneralValidation = !$loanProgram;

            foreach ($loanRules as $rule) {
                // For general validation (when no program specified), use the general loan type instead of specific program
                $programForValidation = $useGeneralValidation ? null : ($rule->experience->loanType->loan_program ?? null);

                // Validate business rules for each specific loan program
                $businessValidation = $this->validateBusinessRules(
                    $creditScore,
                    is_numeric($originalExperience) ? (int) $originalExperience : 0,
                    $request->rehab_budget ?: 0,
                    $request->purchase_price ?: 0,
                    ($request->purchase_price ?: 0) + ($request->rehab_budget ?: 0), // Total loan amount estimation
                    $loanType,
                    $programForValidation, // Use null for general validation, specific program otherwise
                    null, // dscr - not provided in this endpoint
                    null, // property type - not provided in this endpoint
                    $maxLoanAmountFromDb // Pass the dynamic max loan amount from database
                );

                if ($businessValidation['valid']) {
                    $validLoanRules->push($rule);
                } else {
                    // Only collect specific program notifications if a program was specified
                    if (!$useGeneralValidation) {
                        $allNotifications = $allNotifications->merge($businessValidation['notifications']);
                    }
                }
            }

            // If no valid loan rules found, return error with filtered notifications
            if ($validLoanRules->isEmpty()) {
                // For general validation, perform a simplified check to see if any program could potentially work
                if ($useGeneralValidation) {
                    $generalValidation = $this->validateBusinessRules(
                        $creditScore,
                        is_numeric($originalExperience) ? (int) $originalExperience : 0,
                        $request->rehab_budget ?: 0,
                        $request->purchase_price ?: 0,
                        ($request->purchase_price ?: 0) + ($request->rehab_budget ?: 0),
                        $loanType,
                        null, // No specific program
                        null,
                        null,
                        $maxLoanAmountFromDb
                    );

                    if (!$generalValidation['valid']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Loan application does not meet qualification criteria. ' . implode(' ', $generalValidation['notifications']),
                            'disqualifier_notifications' => $generalValidation['notifications'],
                            'data' => [],
                            'total_records' => 0
                        ], 400);
                    }
                }

                // Filter notifications to prioritize specific loan program errors over general ones
                $filteredNotifications = $allNotifications->unique();

                // If user specified a loan program, prioritize those notifications
                if ($loanProgram) {
                    $programSpecificNotifications = $filteredNotifications->filter(function ($notification) use ($loanProgram) {
                        if ($loanProgram === 'DESKTOP APPRAISAL') {
                            return strpos($notification, 'Desktop Appraisal') !== false ||
                                strpos($notification, '$100,000') !== false ||
                                strpos($notification, '$250k') !== false ||
                                strpos($notification, 'cannot exceed Purchase Price') !== false;
                        } elseif ($loanProgram === 'FULL APPRAISAL') {
                            return strpos($notification, 'Full Appraisal') !== false ||
                                strpos($notification, '$50,000') !== false ||
                                strpos($notification, 'Extensive Rehab') !== false ||
                                strpos($notification, 'Heavy Rehab') !== false ||
                                strpos($notification, '$100k with 0 experience') !== false ||
                                strpos($notification, '$500k') !== false;
                        }
                        return true;
                    });

                    if ($programSpecificNotifications->isNotEmpty()) {
                        $filteredNotifications = $programSpecificNotifications;
                    }
                }

                // Remove contradictory messages (e.g., if we have Desktop Appraisal specific errors, 
                // don't show Full Appraisal errors and vice versa)
                $finalNotifications = $filteredNotifications;
                if ($filteredNotifications->count() > 1) {
                    // Prioritize more specific errors
                    $hasExtensiveRehabError = $filteredNotifications->contains(function ($notification) {
                        return strpos($notification, 'Extensive Rehab') !== false;
                    });
                    $hasDesktopError = $filteredNotifications->contains(function ($notification) {
                        return strpos($notification, 'cannot exceed Purchase Price') !== false;
                    });

                    if ($hasExtensiveRehabError) {
                        // If it's an extensive rehab error, prioritize that over desktop appraisal errors
                        $finalNotifications = $filteredNotifications->filter(function ($notification) {
                            return strpos($notification, 'Extensive Rehab') !== false ||
                                strpos($notification, 'Heavy Rehab') !== false ||
                                strpos($notification, '$100k with 0 experience') !== false ||
                                strpos($notification, '$500k') !== false ||
                                strpos($notification, '$50,000') !== false;
                        });
                    } elseif ($hasDesktopError) {
                        // If it's a desktop appraisal error, prioritize that
                        $finalNotifications = $filteredNotifications->filter(function ($notification) {
                            return strpos($notification, 'cannot exceed Purchase Price') !== false ||
                                strpos($notification, '$250k') !== false ||
                                strpos($notification, '$100,000') !== false;
                        });
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Loan application does not meet qualification criteria. ' . implode(' ', $finalNotifications->values()->toArray()),
                    'disqualifier_notifications' => $finalNotifications->values()->toArray(),
                    'data' => [],
                    'total_records' => 0
                ], 400);
            }

            // Transform the valid loan rules to matrix format
            $matrixData = $validLoanRules->map(function ($rule) use ($request, $creditScore, $originalExperience, $loanType, $transactionType, $brokerPoints, $state, $titleCharges, $propertyInsurance) {
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
                    loanType: $rule->experience->loanType->name ?? null,
                    permitStatus: $request->permit_status ?? null
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
                        'guc_experience' => $request->guc_experience ? (int) $request->guc_experience : 0,
                        'permit_status' => $request->permit_status ?: null,
                        'payoff_amount' => $request->payoff_amount ? (float) number_format((float) $request->payoff_amount, 2, '.', '') : 0.00,
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
                                ? ($request->payoff_amount ? (float) number_format((float) $request->payoff_amount, 2, '.', '') : 0.00)
                                : ($request->purchase_price ? (float) number_format((float) $request->purchase_price, 2, '.', '') : 0.00),
                            'rehab_budget' => $request->rehab_budget ? (float) number_format((float) $request->rehab_budget, 2, '.', '') : 0.00,
                            'sub_total_buyer_charges' => $transactionType === 'Refinance'
                                ? (($request->payoff_amount ? (float) $request->payoff_amount : 0.00) + ($request->rehab_budget ? (float) $request->rehab_budget : 0.00))
                                : (($request->purchase_price ? (float) $request->purchase_price : 0.00) + ($request->rehab_budget ? (float) $request->rehab_budget : 0.00)),
                        ],
                        'lender_related_charges' => [
                            'lender_origination_fee' => (float) ($request->purchase_price + $request->rehab_budget) * ($pricingInfo['lender_points'] / 100),
                            'broker_fee' => (float) ($request->purchase_price + $request->rehab_budget) * ($request->broker_points / 100),
                            'underwriting_processing_fee' => $rule->experience->loanType->underwritting_fee ? (float) number_format((float) $rule->experience->loanType->underwritting_fee, 2, '.', '') : 0.00,
                            'interest_reserves' =>
                                ($rule->experience->loanType->loan_program === 'EXPERIENCED BUILDER' || $rule->experience->loanType->loan_program === 'FULL APPRAISAL')
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
                                (($rule->experience->loanType->loan_program === 'EXPERIENCED BUILDER' || $rule->experience->loanType->loan_program === 'FULL APPRAISAL')
                                    ? (($request->purchase_price + $request->rehab_budget) * ($pricingInfo['interest_rate'] / 100) / 12)
                                    : 0.00),
                                2,
                                '.',
                                ''
                            ),
                        ],

                        'cash_due_to_buyer' => $this->calculateCashDueToBuyer(
                            $request->purchase_price ?: 0,
                            $request->rehab_budget ?: 0,
                            $titleCharges ?: 0,
                            $propertyInsurance ?: 0,
                            $rule->experience->loanType->legal_doc_prep_fee ?: 0,
                            $pricingInfo['lender_points'],
                            $request->broker_points,
                            $rule->experience->loanType->underwritting_fee ?: 0,
                            $rule->experience->loanType->loan_program ?: '',
                            $pricingInfo['interest_rate'],
                            $transactionType,
                            $request->payoff_amount ?: 0,
                            $loanCalculations['purchase_loan_up_to']
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

                // Minimum rehab budget validation for both programs
                if ($rehabBudget > 0 && $rehabBudget < 10000) {
                    $notifications[] = 'Rehab Budget amount must be at least $10,000. Contact loan officer for pricing.';
                    $valid = false;
                }

                // Program-specific validations
                if ($loanProgram === 'DESKTOP APPRAISAL') {
                    // Desktop Appraisal program validations

                    // Minimum loan amount validation: (Purchase price x 90%) + rehab budget
                    $desktopCalculatedLoan = ($purchasePrice * 0.90) + $rehabBudget;
                    if ($desktopCalculatedLoan > 0 && $desktopCalculatedLoan < 100000) {
                        $notifications[] = 'Total Loan amount must exceed $100,000. Contact your loan officer for pricing.';
                        $valid = false;
                    }

                    // Heavy Rehab validations for Desktop Appraisal
                    if ($rehabBudget > $purchasePrice) {
                        $notifications[] = 'Rehab Budget cannot exceed Purchase Price. Contact loan officer for pricing';
                        $valid = false;
                    }
                    if ($rehabBudget > 250000) {
                        $notifications[] = 'Maximum Rehab Budget allowed for Desktop Appraisal is $250k. Contact loan officer for pricing';
                        $valid = false;
                    }

                } elseif ($loanProgram === 'FULL APPRAISAL') {
                    // Full Appraisal program validations

                    // Note: For full appraisal, we'll need the actual LTC % qualified to calculate properly
                    // For now, using a conservative estimate. This should be calculated based on actual LTC qualified
                    $estimatedLtc = 0.85; // Updated to use more realistic LTC value (85%)
                    $fullAppraisalCalculatedLoan = ($purchasePrice * $estimatedLtc) + $rehabBudget;
                    if ($fullAppraisalCalculatedLoan > 0 && $fullAppraisalCalculatedLoan < 50000) {
                        $notifications[] = 'Total Loan amount must exceed $50,000. Contact your loan officer for pricing.';
                        $valid = false;
                    }

                    // 3+ experience requirement when rehab budget > purchase price
                    if ($rehabBudget > $purchasePrice && $experience < 3) {
                        $notifications[] = '3+ Borrower Experience required for Extensive Rehab Project. Contact Loan officer for pricing.';
                        $valid = false;
                    }

                    // Heavy Rehab validation for Full Appraisal (50%+ of purchase price)
                    $rehabPercentage = $purchasePrice > 0 ? ($rehabBudget / $purchasePrice) * 100 : 0;
                    if ($rehabPercentage >= 50 && ($experience < 1 || $creditScore < 680)) {
                        $notifications[] = '1+ Experience and FICO 680+ required for Heavy Rehab projects. Contact Loan officer for pricing.';
                        $valid = false;
                    }

                    // Rehab budget limitations based on experience
                    if ($experience == 0 && $rehabBudget > 100000) {
                        $notifications[] = 'Maximum Rehab Budget allowed $100k with 0 experience. Contact loan officer for pricing';
                        $valid = false;
                    }
                    if ($experience >= 1 && $rehabBudget > 500000) {
                        $notifications[] = 'Rehab Budget over $500k requires 1+ similar experience. Contact loan officer for pricing.';
                        $valid = false;
                    }

                } else {
                    // Fallback for when loan program is not specified - use more lenient rules to not pre-reject
                    // users before they have a chance to select an appropriate program
                    if ($maxLoanAmountFromDb > 0 && $totalLoanAmount > $maxLoanAmountFromDb) {
                        $notifications[] = 'Loan Size: Maximum Loan size allowed $' . number_format($maxLoanAmountFromDb, 0);
                        $valid = false;
                    } elseif ($totalLoanAmount > 0 && $totalLoanAmount < 50000) {
                        $notifications[] = 'Loan Size: Minimum Loan Size allowed is $50,000';
                        $valid = false;
                    }

                    // Calculate rehab percentage for general validation
                    $rehabPercentage = $purchasePrice > 0 ? ($rehabBudget / $purchasePrice) * 100 : 0;

                    // Only apply the most critical validations when no program is specified
                    // This allows users to see available programs before getting program-specific restrictions

                    // Only fail for extremely high rehab budgets that wouldn't qualify for any program
                    if ($experience == 0 && $rehabBudget > 500000) {
                        $notifications[] = 'High rehab budgets may require additional experience. Please select a loan program to see specific requirements.';
                        $valid = false;
                    }

                    // For moderate heavy rehab, just note that program selection may affect eligibility
                    // Don't fail validation at this stage to allow program comparison
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


                // Calculate total loan amount for New Construction: 85% × (Purchase price + Rehab budget)
                $calculatedTotalLoanAmount = 0.85 * ($purchasePrice + $rehabBudget);

                // Check maximum loan size ($1,500,000) based on calculated loan amount
                if ($calculatedTotalLoanAmount > 1500000) {
                    $notifications[] = 'Loan Size: Maximum Loan size allowed $1,500,000 for New Construction. Your calculated total loan amount is $' . number_format($calculatedTotalLoanAmount, 0) . ' [85% × ($' . number_format($purchasePrice, 0) . ' + $' . number_format($rehabBudget, 0) . ')]. Contact Loan officer for Pricing';
                    $valid = false;
                }

                // Check minimum loan size ($200,000) based on calculated loan amount
                if ($calculatedTotalLoanAmount > 0 && $calculatedTotalLoanAmount < 200000) {
                    $notifications[] = 'Loan Size: Minimum Loan size allowed $200,000 for New Construction. Your calculated total loan amount is $' . number_format($calculatedTotalLoanAmount, 0) . ' [85% × ($' . number_format($purchasePrice, 0) . ' + $' . number_format($rehabBudget, 0) . ')]. Contact Loan officer for Pricing';
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
     * Get starting interest rate for a loan program
     * 
     * @param string $loanProgram
     * @return float
     */
    private function getStartingRate($loanProgram)
    {
        $loanType = \App\Models\LoanType::where('loan_program', $loanProgram)
            ->where('name', 'DSCR Rental Loans')
            ->first();

        return $loanType ? (float) $loanType->loan_starting_rate : 0;
    }

    /**
     * Calculate FICO interest rate adjustment based on credit score and LTV
     * 
     * @param float $creditScore
     * @param int $ltv
     * @param \Illuminate\Support\Collection $ficoData
     * @return float
     */
    private function calculateFicoInterestAdjustment($creditScore, $ltv, $ficoData)
    {
        foreach ($ficoData as $row) {
            // Extract FICO range from row_label (e.g., "660-679", "680-699")
            $range = explode('-', $row->row_label);
            if (count($range) == 2) {
                $min = (int) $range[0];
                $max = (int) $range[1];

                if ($creditScore >= $min && $creditScore <= $max) {
                    return $this->findAdjustmentValueByLtv($row, $ltv);
                }
            }
        }
        return 0;
    }

    /**
     * Calculate loan amount interest rate adjustment
     * 
     * @param float $purchasePrice
     * @param int $ltv
     * @param \Illuminate\Support\Collection $loanAmountData
     * @return float
     */
    private function calculateLoanAmountInterestAdjustment($purchasePrice, $ltv, $loanAmountData)
    {
        foreach ($loanAmountData as $row) {
            // Parse amount range (e.g., "1,000,000 - 1,499,999", "50,000 - 99,999")
            $range = $row->row_label;

            // Handle different range formats
            if (strpos($range, ' - ') !== false) {
                $parts = explode(' - ', $range);
                $min = (float) str_replace(',', '', $parts[0]);
                $max = (float) str_replace(',', '', $parts[1]);

                if ($purchasePrice >= $min && $purchasePrice <= $max) {
                    return $this->findAdjustmentValueByLtv($row, $ltv);
                }
            }
        }
        return 0;
    }

    /**
     * Calculate property type interest rate adjustment
     * 
     * @param string $propertyType
     * @param int $ltv
     * @param \Illuminate\Support\Collection $propertyTypeData
     * @return float
     */
    private function calculatePropertyTypeInterestAdjustment($propertyType, $ltv, $propertyTypeData)
    {
        foreach ($propertyTypeData as $row) {
            if ($row->row_label === $propertyType) {
                return $this->findAdjustmentValueByLtv($row, $ltv);
            }
        }
        return 0;
    }

    /**
     * Calculate occupancy interest rate adjustment
     * 
     * @param string $occupancyType
     * @param int $ltv
     * @param \Illuminate\Support\Collection $occupancyData
     * @return float
     */
    private function calculateOccupancyInterestAdjustment($occupancyType, $ltv, $occupancyData)
    {
        foreach ($occupancyData as $row) {
            if ($row->row_label === $occupancyType) {
                return $this->findAdjustmentValueByLtv($row, $ltv);
            }
        }
        return 0;
    }

    /**
     * Calculate transaction type interest rate adjustment
     * 
     * @param string $transactionType
     * @param int $ltv
     * @param \Illuminate\Support\Collection $transactionData
     * @param float $creditScore
     * @return float
     */
    private function calculateTransactionTypeInterestAdjustment($transactionType, $ltv, $transactionData, $creditScore = null)
    {
        $baseAdjustment = 0;
        $ficoBasedAdjustment = 0;

        // Calculate base transaction type adjustment
        foreach ($transactionData as $row) {
            if ($row->row_label === $transactionType) {
                $baseAdjustment = $this->findAdjustmentValueByLtv($row, $ltv);
                break;
            }
        }

        // Special case for "Refinance Cash Out" - also apply FICO-based Cash Out Refi adjustment
        if ($transactionType === 'Refinance Cash Out' && $creditScore !== null) {
            $ficoBasedTransactionType = $this->getFicoBasedCashOutRefiType($creditScore);

            foreach ($transactionData as $row) {
                if ($row->row_label === $ficoBasedTransactionType) {
                    $ficoBasedAdjustment = $this->findAdjustmentValueByLtv($row, $ltv);
                    break;
                }
            }
        }

        return $baseAdjustment + $ficoBasedAdjustment;
    }

    /**
     * Get FICO-based Cash Out Refi transaction type based on credit score
     * 
     * @param float $creditScore
     * @return string
     */
    private function getFicoBasedCashOutRefiType($creditScore)
    {
        if ($creditScore >= 720) {
            return 'Cash Out Refi 720+ FICO';
        } elseif ($creditScore >= 700) {
            return 'Cash Out Refi 700-720 FICO';
        } elseif ($creditScore >= 680) {
            return 'Cash Out Refi 680-699 FICO';
        } elseif ($creditScore >= 660) {
            return 'Cash Out Refi 660-679 FICO';
        }

        // Default fallback (though this shouldn't happen given DSCR validation)
        return 'Cash Out Refi 660-679 FICO';
    }

    /**
     * Calculate DSCR interest rate adjustment
     * 
     * @param float $dscr
     * @param int $ltv
     * @param \Illuminate\Support\Collection $dscrData
     * @return float
     */
    private function calculateDscrInterestAdjustment($dscr, $ltv, $dscrData)
    {
        foreach ($dscrData as $row) {
            // Parse DSCR range (e.g., "0.80-0.99", "1.00-1.10", "1.10-1.20", "1.20+")
            $range = $row->row_label;

            if (strpos($range, '+') !== false) {
                // Handle ranges like "1.20+"
                $min = (float) str_replace('+', '', $range);
                if ($dscr >= $min) {
                    return $this->findAdjustmentValueByLtv($row, $ltv);
                }
            } elseif (strpos($range, '-') !== false) {
                // Handle ranges like "0.80-0.99"
                $parts = explode('-', $range);
                $min = (float) $parts[0];
                $max = (float) $parts[1];

                if ($dscr >= $min && $dscr <= $max) {
                    return $this->findAdjustmentValueByLtv($row, $ltv);
                }
            }
        }
        return 0;
    }

    /**
     * Calculate prepay interest rate adjustment
     * 
     * @param string $prepayType
     * @param int $ltv
     * @param \Illuminate\Support\Collection $prepayData
     * @return float
     */
    private function calculatePrePayInterestAdjustment($prepayType, $ltv, $prepayData)
    {
        foreach ($prepayData as $row) {
            if ($row->row_label === $prepayType) {
                return $this->findAdjustmentValueByLtv($row, $ltv);
            }
        }
        return 0;
    }

    /**
     * Calculate loan type interest rate adjustment
     * 
     * @param string $loanTypeStr
     * @param int $ltv
     * @param \Illuminate\Support\Collection $loanTypeData
     * @return float
     */
    private function calculateLoanTypeInterestAdjustment($loanTypeStr, $ltv, $loanTypeData)
    {
        foreach ($loanTypeData as $row) {
            if ($row->row_label === $loanTypeStr) {
                return $this->findAdjustmentValueByLtv($row, $ltv);
            }
        }
        return 0;
    }

    /**
     * Find the adjustment value for a specific LTV percentage from a matrix row
     * Uses tier-based logic where borrowers are placed into specific LTV bands
     * 
     * @param object $row
     * @param int $ltv
     * @return float
     */
    private function findAdjustmentValueByLtv($row, $ltv)
    {
        $ltvColumns = [
            50 => '50% LTV or less',
            55 => '55% LTV',
            60 => '60% LTV',
            65 => '65% LTV',
            70 => '70% LTV',
            75 => '75% LTV',
            80 => '80% LTV'
        ];

        // Apply tier-based logic for LTV column selection
        $targetColumn = null;

        if ($ltv <= 50) {
            $targetColumn = '50% LTV or less';
        } elseif ($ltv > 50 && $ltv <= 55) {
            $targetColumn = '55% LTV';
        } elseif ($ltv > 55 && $ltv <= 60) {
            $targetColumn = '60% LTV';
        } elseif ($ltv > 60 && $ltv <= 65) {
            $targetColumn = '65% LTV';
        } elseif ($ltv > 65 && $ltv <= 70) {
            // If borrower LTV is 65.01% - 70% LTV, use 70% LTV column
            $targetColumn = '70% LTV';
        } elseif ($ltv > 70 && $ltv <= 75) {
            // If borrower LTV is 70.01% - 75% LTV, use 75% LTV column
            $targetColumn = '75% LTV';
        } elseif ($ltv > 75 && $ltv <= 80) {
            $targetColumn = '80% LTV';
        } else {
            // For LTV > 80%, use the highest available column (80% LTV)
            $targetColumn = '80% LTV';
        }

        if ($targetColumn && isset($row->{$targetColumn}) && $row->{$targetColumn} !== null) {
            return (float) $row->{$targetColumn};
        }

        return 0;
    }

    /**
     * Calculate max LTV for FICO category based on credit score
     * 
     * @param float $creditScore
     * @param \Illuminate\Support\Collection $ficoData
     * @return int
     */
    private function calculateFicoMaxLtv($creditScore, $ficoData)
    {
        foreach ($ficoData as $row) {
            // Extract FICO range from row_label (e.g., "660-679", "680-699")
            $range = explode('-', $row->row_label);
            if (count($range) == 2) {
                $min = (int) $range[0];
                $max = (int) $range[1];

                if ($creditScore >= $min && $creditScore <= $max) {
                    return $this->findMaxLtvFromRow($row);
                }
            }
        }
        return 0;
    }

    /**
     * Calculate max LTV for Transaction Type category
     * 
     * @param string $transactionType
     * @param \Illuminate\Support\Collection $transactionData
     * @return int
     */
    private function calculateTransactionTypeMaxLtv($transactionType, $transactionData, $creditScore = null)
    {
        // Special handling for "Refinance Cash Out" transaction types
        if ($transactionType === 'Refinance Cash Out' && $creditScore !== null) {
            // Get base transaction type max LTV
            $baseMaxLtv = 0;
            foreach ($transactionData as $row) {
                if ($row->row_label === $transactionType) {
                    $baseMaxLtv = $this->findMaxLtvFromRow($row);
                    break;
                }
            }

            // Get FICO-based transaction type max LTV
            $ficoBasedType = $this->getFicoBasedCashOutRefiType($creditScore);
            $ficoMaxLtv = 0;
            if ($ficoBasedType) {
                foreach ($transactionData as $row) {
                    if ($row->row_label === $ficoBasedType) {
                        $ficoMaxLtv = $this->findMaxLtvFromRow($row);
                        break;
                    }
                }
            }

            // Return maximum (most lenient) of the two max LTV values
            return max($baseMaxLtv, $ficoMaxLtv);
        }

        // Regular logic for other transaction types
        foreach ($transactionData as $row) {
            if ($row->row_label === $transactionType) {
                return $this->findMaxLtvFromRow($row);
            }
        }
        return 0;
    }

    /**
     * Calculate max LTV for Loan Amount category based on purchase price
     * 
     * @param float $purchasePrice
     * @param \Illuminate\Support\Collection $loanAmountData
     * @return int
     */
    private function calculateLoanAmountMaxLtv($purchasePrice, $loanAmountData)
    {
        foreach ($loanAmountData as $row) {
            // Parse amount range (e.g., "1,000,000 - 1,499,999", "50,000 - 99,999")
            $range = $row->row_label;

            // Handle different range formats
            if (strpos($range, ' - ') !== false) {
                $parts = explode(' - ', $range);
                $min = (float) str_replace(',', '', $parts[0]);
                $max = (float) str_replace(',', '', $parts[1]);

                if ($purchasePrice >= $min && $purchasePrice <= $max) {
                    return $this->findMaxLtvFromRow($row);
                }
            }
        }
        return 0;
    }

    /**
     * Calculate max LTV for DSCR category based on DSCR value
     * 
     * @param float $dscr
     * @param \Illuminate\Support\Collection $dscrData
     * @return int
     */
    private function calculateDscrMaxLtv($dscr, $dscrData)
    {
        foreach ($dscrData as $row) {
            // Parse DSCR range (e.g., "0.80-0.99", "1.00-1.10", "1.10-1.20", "1.20+")
            $range = $row->row_label;

            if (strpos($range, '+') !== false) {
                // Handle ranges like "1.20+"
                $min = (float) str_replace('+', '', $range);
                if ($dscr >= $min) {
                    return $this->findMaxLtvFromRow($row);
                }
            } elseif (strpos($range, '-') !== false) {
                // Handle ranges like "0.80-0.99"
                $parts = explode('-', $range);
                $min = (float) $parts[0];
                $max = (float) $parts[1];

                if ($dscr >= $min && $dscr <= $max) {
                    return $this->findMaxLtvFromRow($row);
                }
            }
        }
        return 0;
    }

    /**
     * Calculate max LTV for Occupancy category
     * 
     * @param string $occupancyType
     * @param \Illuminate\Support\Collection $occupancyData
     * @return int
     */
    private function calculateOccupancyMaxLtv($occupancyType, $occupancyData)
    {
        foreach ($occupancyData as $row) {
            if ($row->row_label === $occupancyType) {
                return $this->findMaxLtvFromRow($row);
            }
        }
        return 0;
    }

    /**
     * Find the maximum LTV percentage from a row by examining all LTV columns
     * 
     * @param object $row
     * @return int
     */
    private function findMaxLtvFromRow($row)
    {
        $ltvColumns = [
            '50% LTV or less' => 50,
            '55% LTV' => 55,
            '60% LTV' => 60,
            '65% LTV' => 65,
            '70% LTV' => 70,
            '75% LTV' => 75,
            '80% LTV' => 80
        ];

        $maxValue = -1;
        $maxLtvPercentage = 0;

        foreach ($ltvColumns as $column => $ltvPercentage) {
            $value = $row->{$column};

            // Skip null values (N/A)
            if ($value !== null) {
                $numericValue = (float) $value;

                // Find the maximum non-null value (including zero values)
                if ($numericValue > $maxValue) {
                    $maxValue = $numericValue;
                    $maxLtvPercentage = $ltvPercentage;
                }
            }
        }

        // If all values are zero or no valid values found, check which column has the highest zero value
        if ($maxValue == 0) {
            // Return the highest LTV column that has a zero value (not N/A)
            for ($ltv = 80; $ltv >= 50; $ltv -= 5) {
                if ($ltv == 50) {
                    $column = '50% LTV or less';
                } else {
                    $column = $ltv . '% LTV';
                }

                if (isset($ltvColumns[$column]) && $row->{$column} !== null) {
                    return $ltvColumns[$column];
                }
            }
        }

        return $maxLtvPercentage;
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
        // First, validate basic parameters to get transaction_type
        $basicValidator = Validator::make($request->all(), [
            'transaction_type' => 'required|string',
        ]);

        if ($basicValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $basicValidator->errors()
            ], 400);
        }

        // Determine payoff_amount validation based on transaction_type
        $transactionType = $request->transaction_type;
        $isRefinance = in_array($transactionType, ['Refinance No Cash Out', 'Refinance Cash Out']);
        $payoffValidation = $isRefinance ? 'required|numeric|min:0' : 'nullable|numeric|min:0';

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
            'annual_hoa' => 'nullable|numeric|min:0',
            'dscr' => 'nullable|numeric|min:0.01',
            'user_input_loan_amount' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'payoff_amount' => $payoffValidation,
            'loan_term' => 'nullable|string|in:' . implode(',', \App\Models\LoanTypesDscr::pluck('loan_type_dscr_name')->toArray()),
            'lender_points' => 'nullable|numeric|in:1.00,1.000,1.5000,2.000',
            'pre_pay_penalty' => 'nullable|string|in:' . implode(',', \App\Models\PrepayPeriods::pluck('prepay_name')->toArray()),
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

        // Apply default values for optional parameters
        $dscr = $request->get('dscr', 1.5); // Default to 1.5 if not provided
        $loanTerm = $request->get('loan_term', '30 Year Fixed'); // Default to '30 Year Fixed' if not provided
        $lenderPoints = $request->get('lender_points', 2.000); // Default to 2.000 if not provided
        $prePay = $request->get('pre_pay_penalty', '5 Year Prepay'); // Default to '3 Year Prepay' if not provided
        $annualHoa = $request->get('annual_hoa', 0); // Default to 0 if not provided
        $payoffAmount = $request->get('payoff_amount', 0); // Default to 0 if not provided for non-refinance transactions
        $userInputLoanAmount = $request->get('user_input_loan_amount', 0);
        $titleCharges = $request->get('title_charges', 0);
        $propertyInsurance = $request->get('property_insurance', 0);

        // Ensure annual_hoa is numeric

        try {
            // Extract parameters
            $creditScore = $request->credit_score;
            $experience = $request->experience;
            $transactionType = $request->transaction_type;
            $propertyType = $request->property_type;
            $purchasePrice = $request->purchase_price;
            $occupancyType = $request->occupancy_type;

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
                        'annual_hoa' => $annualHoa, // Use default value
                        'dscr' => $dscr, // Use default value
                        'purchase_date' => $request->purchase_date,
                        'payoff_amount' => $payoffAmount, // Use default value
                        'loan_term' => $loanTerm, // Use default value
                        'lender_points' => $lenderPoints, // Use default value
                        'pre_pay_penalty' => $prePay, // Use default value
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


                // Calculate max LTV for each category based on user inputs
                $ficoMaxLtv = $this->calculateFicoMaxLtv($creditScore, $programData->get('FICO', collect()));
                $transactionTypeMaxLtv = $this->calculateTransactionTypeMaxLtv($transactionType, $programData->get('Transaction Type', collect()), $creditScore);
                $initialLoanAmount = ($transactionTypeMaxLtv * $purchasePrice) / 100; // Initial Loan Amount = Purchase Price × TransactionTypeMaxLtv / 100 (used in loan amount calculation) the amount 
                $loan_amount_max_transaction_limit = $initialLoanAmount;
                $loanAmountMaxLtv = $this->calculateLoanAmountMaxLtv($loan_amount_max_transaction_limit, $programData->get('Loan Amount', collect()));
                $dscrMaxLtv = $this->calculateDscrMaxLtv($dscr, $programData->get('DSCR', collect()));
                $occupancyMaxLtv = $this->calculateOccupancyMaxLtv($occupancyType, $programData->get('Occupancy', collect()));



                // $creditScore (FICO INPUT) ,  $transactionType (INPUT),  purchasePrice (INPUT),  dscr (INPUT),  occupancyType (INPUT)


                // Calculate approved max LTV as minimum of all
                $approvedMaxLtv = min(
                    $ficoMaxLtv,
                    $transactionTypeMaxLtv,
                    $loanAmountMaxLtv,
                    $dscrMaxLtv,
                    $occupancyMaxLtv
                );



                // Calculate interest rate formula components
                $startingRate = $this->getStartingRate($program);
                $ltvFicoAdjustment = $this->calculateFicoInterestAdjustment($creditScore, $approvedMaxLtv, $programData->get('FICO', collect()));
                $loanAmountAdjustment = $this->calculateLoanAmountInterestAdjustment($initialLoanAmount, $approvedMaxLtv, $programData->get('Loan Amount', collect()));
                $propertyTypeAdjustment = $this->calculatePropertyTypeInterestAdjustment($propertyType, $approvedMaxLtv, $programData->get('Property Type', collect()));
                $occupancyAdjustment = $this->calculateOccupancyInterestAdjustment($occupancyType, $approvedMaxLtv, $programData->get('Occupancy', collect()));
                $transactionTypeAdjustment = $this->calculateTransactionTypeInterestAdjustment($transactionType, $approvedMaxLtv, $programData->get('Transaction Type', collect()), $creditScore);
                $dscrAdjustment = $this->calculateDscrInterestAdjustment($dscr, $approvedMaxLtv, $programData->get('DSCR', collect()));
                $prePayAdjustment = $this->calculatePrePayInterestAdjustment($prePay, $approvedMaxLtv, $programData->get('Pre Pay', collect()));
                $loanTypeAdjustment = $this->calculateLoanTypeInterestAdjustment($loanTerm, $approvedMaxLtv, $programData->get('Loan Type', collect()));

                // Calculate final interest rate as sum of all components
                $calculatedInterestRate = $startingRate + $ltvFicoAdjustment + $loanAmountAdjustment + $propertyTypeAdjustment +
                    $occupancyAdjustment + $transactionTypeAdjustment + $dscrAdjustment + $prePayAdjustment + $loanTypeAdjustment;

                // Adjust interest rate based on lender points
                $adjustedInterestRate = $calculatedInterestRate;
                $lenderPointsFloat = (float) $lenderPoints; // Use default value

                if ($lenderPointsFloat == 1.5 || $lenderPointsFloat == 1.5000) {
                    // 1.5 points: add 0.5% to the rate
                    $adjustedInterestRate = $calculatedInterestRate + 0.5;
                } elseif ($lenderPointsFloat == 1.0 || $lenderPointsFloat == 1.000) {
                    // 1.0 points: add 1.0% to the rate  
                    $adjustedInterestRate = $calculatedInterestRate + 1.0;
                }
                // 2.0 points: no adjustment needed (rate stays the same)




                // Calculate monthly payment based on loan term
                $monthlyPayment = 0;
                // TODO: Review this calculation - Changed from hardcoded $380,000 to calculated loan amount
                // Initial Loan Amount = Purchase Price × Approved Max LTV / 100
                // Example: $300,000 × 80% = $240,000 (instead of hardcoded $380,000)



                if ($loanTerm === '10 Year IO') {

                    /**
                     * Calculates the monthly payment for a 10 Year Interest Only (IO) loan term.
                     *
                     * The calculation uses the following formula:
                     *   (Loan Amount × Interest Rate / 12) + (Annual Tax / 12) + (Annual Insurance / 12)
                     *
                     * - $monthlyInterest: The monthly interest-only payment based on the initial loan amount and adjusted interest rate.
                     * - $monthlyTax: The monthly portion of the annual property tax.
                     * - $monthlyInsurance: The monthly portion of the annual insurance.
                     * - $monthlyPayment: The total monthly payment including interest, tax, and insurance.
                     *
                     * @param float $initialLoanAmount The principal loan amount.
                     * @param float $adjustedInterestRate The annual interest rate (percentage).
                     * @param float $request->annual_tax The annual property tax.
                     * @param float $request->annual_insurance The annual insurance cost.
                     * @return float $monthlyPayment The calculated monthly payment for the 10 Year IO loan.
                     */

                    $monthlyInterest = ($initialLoanAmount * ($adjustedInterestRate / 100)) / 12;
                    $monthlyTax = $request->annual_tax / 12;
                    $monthlyInsurance = $request->annual_insurance / 12;
                    $monthlyPayment = $monthlyInterest + $monthlyTax + $monthlyInsurance;

                } else {

                    /**
                     * Calculates the total monthly payment for a loan, including principal & interest,
                     * property tax, insurance, and optionally HOA fees.
                     *
                     * Steps:
                     * - Computes the monthly principal & interest payment using the PMT formula.
                     * - Calculates monthly property tax and insurance from their annual values.
                     * - Sums up principal & interest, tax, and insurance to get the base monthly payment.
                     * - If annual HOA fees are provided, calculates monthly HOA and adds it to the payment.
                     *
                     * @param float $adjustedInterestRate The annual interest rate (percentage).
                     * @param float $initialLoanAmount The initial amount of the loan.
                     * @param float $annualHoa The annual HOA fee (optional).
                     * @param \Illuminate\Http\Request $request The HTTP request containing 'annual_tax' and 'annual_insurance'.
                     * @return float The total monthly payment including all components.
                     */
                    $pmt = $this->pmt($adjustedInterestRate / 100 / 12, 360, $initialLoanAmount) * -1;
                    $monthlyTax = $request->annual_tax / 12;
                    $monthlyInsurance = $request->annual_insurance / 12;
                    $monthlyPayment = $pmt + $monthlyTax + $monthlyInsurance;

                    if ($annualHoa > 0) {
                        $monthlyHoa = $annualHoa / 12;
                        $monthlyPayment += $monthlyHoa;
                    }

                }

                // Calculate correct DSCR: monthly_market_rent / monthlyPayment
                $calculatedDSCR = $monthlyPayment > 0 ? $request->monthly_market_rent / $monthlyPayment : 0;

                // Recalculate DSCR max LTV using the calculated DSCR
                $calculatedDscrMaxLtv = $this->calculateDscrMaxLtv($calculatedDSCR, $programData->get('DSCR', collect()));

                // Recalculate approved max LTV with the new calculated DSCR max LTV
                $approvedMaxLtvWithCalculatedDscr = min(
                    $ficoMaxLtv,
                    $transactionTypeMaxLtv,
                    $loanAmountMaxLtv,
                    $calculatedDscrMaxLtv, // Use calculated DSCR max LTV
                    $occupancyMaxLtv
                );


                $totalLoanAmount = $userInputLoanAmount > 0 ? $userInputLoanAmount : $initialLoanAmount;
                $brokerPoint = $request->broker_points;
                $loan_type = \App\Models\LoanType::where('loan_program', $program)->first();


                // Format each category for this program
                $formattedProgramData = [
                    'loan_program' => $program,
                    'categories' => [],
                    'interest_rate_formula' => [
                        'starting_rate' => $startingRate,
                        'ltv_fico' => $ltvFicoAdjustment,
                        'loan_amount' => $loanAmountAdjustment,
                        'property_type' => $propertyTypeAdjustment,
                        'occupancy' => $occupancyAdjustment,
                        'transaction_type' => $transactionTypeAdjustment,
                        'dscr' => $dscrAdjustment,
                        'pre_pay' => $prePayAdjustment,
                        'loan_type' => $loanTypeAdjustment,
                        'calculated_interest_rate' => $calculatedInterestRate,
                    ],
                    'ltv_formula' => [
                        'fico' => [
                            'input' => $creditScore,
                            'max_ltv' => $ficoMaxLtv,
                        ],
                        'transaction_type' => [
                            'input' => $transactionType,
                            'max_ltv' => $transactionTypeMaxLtv,
                        ],
                        'loan_amount' => [
                            'input' => $initialLoanAmount,
                            'max_ltv' => $loanAmountMaxLtv,
                        ],
                        'dscr' => [
                            'input' => $calculatedDSCR, // Use calculated DSCR instead of request input
                            'max_ltv' => $calculatedDscrMaxLtv, // Use calculated DSCR max LTV
                        ],
                        'occupancy' => [
                            'input' => $request->occupancy_type ?: '',
                            'max_ltv' => $occupancyMaxLtv,
                        ],
                        'approved_max_ltv' => [
                            'max_ltv' => $approvedMaxLtvWithCalculatedDscr, // Use recalculated approved max LTV
                        ],
                    ],

                    'loan_program_values' => [
                        'loan_term' => $loanTerm, // Use default value
                        'max_ltv' => $approvedMaxLtvWithCalculatedDscr, // Use recalculated approved max LTV
                        'monthly_payment' => round($monthlyPayment, 2),
                        'interest_rate' => $adjustedInterestRate,
                        'lender_points' => $lenderPoints, // Use default value
                        'pre_pay_penalty' => $prePay, // Use default value
                        'calculated_dscr' => round($calculatedDSCR, 4), // Add calculated DSCR
                    ],


                    'estimated_closing_statement' => [
                        'loan_amount_section' => [
                            'initial_loan_amount' => $userInputLoanAmount > 0 ? $userInputLoanAmount : $initialLoanAmount,
                        ],
                        'buyer_related_charges' => [
                            ($transactionType === 'Refinance' ? 'loan_payoff' : 'purchase_price') => $transactionType === 'Refinance'
                                ? ($payoffAmount ? (float) number_format((float) $payoffAmount, 2, '.', '') : 0.00)
                                : ($purchasePrice ? (float) number_format((float) $purchasePrice, 2, '.', '') : 0.00),
                        ],
                        'lender_related_charges' => [
                            'lender_origination_fee' => $totalLoanAmount * ($lenderPoints / 100),
                            'broker_fee' => $totalLoanAmount * ($brokerPoint / 100),
                            'underwriting_processing_fee' => $loan_type?->underwritting_fee ? (float) number_format((float) $loan_type->underwritting_fee, 2, '.', '') : 0.00,
                            'interest_reserves' => 0,
                        ],
                        'title_other_charges' => [
                            'title_charges' => (float) $titleCharges,
                            'property_insurance' => (float) $propertyInsurance,
                            'legal_doc_prep_fee' => $loan_type->legal_doc_prep_fee ? (float) number_format((float) $loan_type->legal_doc_prep_fee, 2, '.', '') : 0.00,
                            'subtotal_closing_costs' => ($totalLoanAmount * ($lenderPoints / 100)) + ($totalLoanAmount * ($brokerPoint / 100)) + ($loan_type?->underwritting_fee) + (0) + ($titleCharges) + ($propertyInsurance) + ($loan_type->legal_doc_prep_fee),
                        ],

                        'cash_due_to_buyer' => (float) $purchasePrice - ($userInputLoanAmount > 0 ? $userInputLoanAmount : $initialLoanAmount) + (($totalLoanAmount * ($lenderPoints / 100)) + ($totalLoanAmount * ($brokerPoint / 100)) + ($loan_type?->underwritting_fee) + (0) + ($titleCharges) + ($propertyInsurance) + ($loan_type->legal_doc_prep_fee)),
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

        // Return raw database value with 4 decimal places
        return number_format((float) $value, 3) . '%';
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


    public function pmt($rate, $nper, $pv, $fv = 0, $type = 0)
    {
        if ($rate == 0) {
            return -($pv + $fv) / $nper;
        }

        $pvif = pow(1 + $rate, $nper);
        return -($rate * ($pv * $pvif + $fv)) / (($pvif - 1) * (1 + $rate * $type));
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
    private function calculateLoanAmountsFromInputs($maxLtv, $maxLtc, $maxLtfc, $purchasePrice, $rehabBudget, $arv, $loanType = null, $permitStatus = null)
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
            // For Fix and Flip Full Appraisal: Ensure minimum $10,000 down payment when rehab budget > purchase price
            if ($loanType === 'Fix and Flip' && $rehabBudget > $purchasePrice) {
                // Purchase Loan = Total Loan - Rehab Budget (but not less than 0)
                $purchaseLoanUpTo = max(0, $totalLoanUpTo - $rehabBudget);

                // Ensure minimum $10,000 down payment
                $maxAllowedPurchaseLoan = $purchasePrice - 10000; // Must leave at least $10k for down payment
                if ($maxAllowedPurchaseLoan > 0 && $purchaseLoanUpTo > $maxAllowedPurchaseLoan) {
                    $purchaseLoanUpTo = $maxAllowedPurchaseLoan;
                }
            } else {
                // Standard calculation for other scenarios
                // Purchase Loan = Total Loan - Rehab Budget (but not less than 0)
                $purchaseLoanUpTo = max(0, $totalLoanUpTo - $rehabBudget);
            }

            // Rehab Loan = Minimum of (Rehab Budget, Total Loan Capacity)
            $rehabLoanUpTo = min($rehabBudget, $totalLoanUpTo);

            // Apply permit status restrictions for New Construction
            if ($loanType === 'New Construction' && $permitStatus) {
                $maxPurchaseLoanByPermit = 0;

                if ($permitStatus === 'Permit Approved') {
                    // Max Purchase Loan is 75% of purchase price
                    $maxPurchaseLoanByPermit = ($purchasePrice * 0.75);
                } elseif ($permitStatus === 'Unpermitted') {
                    // Max Purchase Loan is 60% of purchase price
                    $maxPurchaseLoanByPermit = ($purchasePrice * 0.60);
                }

                // Apply the permit status limit if it's lower than calculated purchase loan
                if ($maxPurchaseLoanByPermit > 0 && $maxPurchaseLoanByPermit < $purchaseLoanUpTo) {
                    $purchaseLoanUpTo = $maxPurchaseLoanByPermit;

                    // Recalculate total loan based on adjusted purchase loan + rehab loan
                    $totalLoanUpTo = $purchaseLoanUpTo + $rehabLoanUpTo;
                }
            }
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
            'Townhome',
            'Condo',
            '2-4 Unit'
        ])->pluck('name')->toArray();

        if (!in_array($propertyType, $eligiblePropertyTypes)) {
            $notifications[] = 'Property Type: Property type "' . $propertyType . '" is not eligible for DSCR loans. Eligible property types are: ' . implode(', ', $eligiblePropertyTypes);
            $valid = false;
        }

        return [
            'valid' => $valid,
            'notifications' => $notifications,
            'eligible_property_types' => $eligiblePropertyTypes
        ];
    }

    /**
     * Get occupancy types for DSCR loans
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOccupancyTypes()
    {
        try {
            $occupancyTypes = \App\Models\OccupancyTypes::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($occupancyType) {
                    return [
                        'id' => $occupancyType->id,
                        'name' => $occupancyType->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Occupancy types retrieved successfully',
                'data' => $occupancyTypes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving occupancy types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prepay periods for DSCR loans
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrepayPeriods()
    {
        try {
            $prepayPeriods = \App\Models\PrepayPeriods::select('id', 'prepay_name')
                ->orderBy('prepay_name')
                ->get()
                ->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'name' => $period->prepay_name,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Prepay periods retrieved successfully',
                'data' => $prepayPeriods
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving prepay periods',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get property types for DSCR loans
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDscrPropertyTypes()
    {
        try {
            // Get property types that are eligible for DSCR loans
            $propertyTypes = \App\Models\PropertyType::
                select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($propertyType) {
                    return [
                        'id' => $propertyType->id,
                        'name' => $propertyType->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'DSCR property types retrieved successfully',
                'data' => $propertyTypes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving DSCR property types',
                'error' => $e->getMessage()
            ], 500);
        }
    }    /**
         * Get states available for DSCR loans
         * 
         * @return \Illuminate\Http\JsonResponse
         */
    public function getDscrStates()
    {
        try {
            // Get states that are allowed for DSCR loans
            $states = \App\Models\State::where('is_allowed', true)
                ->select('id', 'code')
                ->orderBy('code')
                ->get()
                ->map(function ($state) {
                    return [
                        'id' => $state->id,
                        'code' => $state->code,
                        'name' => $state->code, // Use code as name since no separate name field exists
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'DSCR states retrieved successfully',
                'data' => $states
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving DSCR states',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get DSCR loan terms from loan_types_dscrs table
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDscrLoanTerms()
    {
        try {
            $loanTerms = \App\Models\LoanTypesDscr::select('id', 'loan_type_dscr_name')
                ->orderBy('display_order')
                ->get()
                ->map(function ($loanTerm) {
                    return [
                        'id' => $loanTerm->id,
                        'name' => $loanTerm->loan_type_dscr_name,
                        'value' => $loanTerm->loan_type_dscr_name,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'DSCR loan terms retrieved successfully',
                'data' => $loanTerms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving DSCR loan terms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate cash due to buyer for loan application
     * 
     * @param float $purchasePrice The purchase price of the property
     * @param float $rehabBudget The rehabilitation budget
     * @param float $titleCharges Title charges
     * @param float $propertyInsurance Property insurance costs
     * @param float $legalDocPrepFee Legal document preparation fee
     * @param float $lenderPoints Lender points percentage
     * @param float $brokerPoints Broker points percentage
     * @param float $underwritingFee Underwriting fee
     * @param string $loanProgram Loan program type
     * @param float $interestRate Interest rate percentage
     * @param string $transactionType Transaction type
     * @param float $payOff Pay off amount for refinance
     * @param float $purchaseLoanAmount Purchase loan amount from calculations
     * @return float
     */
    private function calculateCashDueToBuyer(
        float $purchasePrice,
        float $rehabBudget,
        ?float $titleCharges,
        ?float $propertyInsurance,
        ?float $legalDocPrepFee,
        float $lenderPoints,
        float $brokerPoints,
        ?float $underwritingFee,
        ?string $loanProgram,
        float $interestRate,
        string $transactionType,
        ?float $payOff,
        float $purchaseLoanAmount
    ): float {
        // Calculate total project cost
        $totalProjectCost = $purchasePrice + $rehabBudget;

        // Calculate subtotal closing costs
        $subtotalClosingCosts = (float) ($titleCharges ?: 0) +
            (float) ($propertyInsurance ?: 0) +
            (float) ($legalDocPrepFee ?: 0) +
            ($totalProjectCost * ($lenderPoints / 100)) +
            ($totalProjectCost * ($brokerPoints / 100)) +
            (float) ($underwritingFee ?: 0);

        // Add interest reserves if loan program is EXPERIENCED BUILDER or FULL APPRAISAL
        if ($loanProgram === 'EXPERIENCED BUILDER' || $loanProgram === 'FULL APPRAISAL') {
            $subtotalClosingCosts += ($totalProjectCost * ($interestRate / 100) / 12);
        }

        // Calculate cash due to buyer based on transaction type
        $cashDueToBuyer = 0;

        // Check if this is a Refinance transaction (Refinance Cash Out or Refinance No Cash Out)
        if (in_array($transactionType, ['Refinance No Cash Out', 'Refinance Cash Out', 'Refinance'])) {
            // For Refinance: purchase_loan_amount - payoff_amount + subtotal_closing_costs
            $cashDueToBuyer = $purchaseLoanAmount - ($payOff ?: 0) + $subtotalClosingCosts;
        } else {
            // For Purchase: purchase_price - purchase_loan_amount + subtotal_closing_costs
            $cashDueToBuyer = $purchasePrice - $purchaseLoanAmount + $subtotalClosingCosts;
        }

        return (float) number_format($cashDueToBuyer, 2, '.', '');
    }
}
