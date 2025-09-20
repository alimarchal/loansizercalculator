<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Borrower;
use App\Models\LoanProgramResult;
use App\Mail\LoanApplicationWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoanApplicationController extends Controller
{
    /**
     * Submit loan application and create borrower record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitApplication(Request $request)
    {
        // Determine if this is a DSCR loan to apply conditional validation
        $isDscrLoan = $request->input('loan_type') === 'DSCR Rental Loans';

        // Base validation rules
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',

            // Calculator inputs - common fields
            'credit_score' => 'required|integer|min:300|max:850',
            'experience' => 'required|integer|min:0',
            'loan_type' => 'required|string',
            'transaction_type' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'broker_points' => 'required|numeric|min:0',
            'state' => 'required|string|max:2',

            // Always optional fields
            'payoff_amount' => 'nullable|numeric|min:0',
            'title_charges' => 'nullable|numeric|min:0',
            'property_insurance' => 'nullable|numeric|min:0',
            'property_type' => 'nullable|string',
            'property_address' => 'nullable|string|max:500',
            'property_zip_code' => 'nullable|string|max:10',

            // Selected loan program
            'selected_loan_program' => 'required|string',

            // Loan program results
            'loan_programs' => 'required|array|min:1',
            'loan_programs.*.loan_type' => 'required|string',
            'loan_programs.*.loan_program' => 'required|string',
            'loan_programs.*.is_selected' => 'required|boolean',

            // Calculated values
            'calculated_values' => 'required|array',

            // API data
            'api_url' => 'required|string',
            'api_response' => 'required|array',
        ];

        if ($isDscrLoan) {
            // DSCR loan specific validation rules
            $validationRules = array_merge($validationRules, [
                // DSCR required fields
                'occupancy_type' => 'required|string',
                'monthly_market_rent' => 'required|numeric|min:0',
                'annual_tax' => 'required|numeric|min:0',
                'annual_insurance' => 'required|numeric|min:0',
                'annual_hoa' => 'required|numeric|min:0',

                // DSCR optional fields
                'dscr' => 'nullable|numeric|min:0',
                'purchase_date' => 'nullable|date',
                'lender_points' => 'nullable|numeric|min:0',
                'pre_pay_penalty' => 'nullable|string',
                'loan_term' => 'nullable|string', // DSCR can have loan term as string (e.g., "30 Year Fixed")
                'arv' => 'nullable|numeric',
                'rehab_budget' => 'nullable|numeric',

                // DSCR calculated values structure
                'calculated_values.total_loan_amount' => 'required|numeric|min:0',
                'calculated_values.lender_origination_fee' => 'nullable|numeric|min:0',
                'calculated_values.broker_fee' => 'nullable|numeric|min:0',
                'calculated_values.cash_due_to_buyer' => 'nullable|numeric',
            ]);
        } else {
            // Fix & Flip / New Construction validation rules
            $validationRules = array_merge($validationRules, [
                // Required for non-DSCR loans
                'loan_term' => 'required|integer',
                'arv' => 'required|numeric|min:0',
                'rehab_budget' => 'required|numeric|min:0',
                'lender_points' => 'nullable|numeric|min:0',
                'pre_pay_penalty' => 'nullable|string',

                // Optional DSCR fields (not used for non-DSCR)
                'occupancy_type' => 'nullable|string',
                'monthly_market_rent' => 'nullable|numeric|min:0',
                'annual_tax' => 'nullable|numeric|min:0',
                'annual_insurance' => 'nullable|numeric|min:0',
                'annual_hoa' => 'nullable|numeric|min:0',
                'dscr' => 'nullable|numeric|min:0',
                'purchase_date' => 'nullable|date',

                // Standard calculated values structure
                'calculated_values.purchase_loan_amount' => 'required|numeric|min:0',
                'calculated_values.rehab_loan_amount' => 'required|numeric|min:0',
                'calculated_values.total_loan_amount' => 'required|numeric|min:0',
            ]);
        }

        // Validate the incoming request
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Debug: Log the received data
            \Log::info('Loan application submission data:', [
                'loan_type' => $request->loan_type,
                'property_address' => $request->property_address,
                'property_zip_code' => $request->property_zip_code,
                'property_type' => $request->property_type,
                'isDscrLoan' => $isDscrLoan
            ]);

            // Check if user exists with this email
            $user = User::where('email', $request->email)->first();
            $isNewUser = false;
            $temporaryPassword = null;

            if (!$user) {
                // Create new user account
                $temporaryPassword = Str::random(12);

                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($temporaryPassword),
                    'is_auto_generated' => true,
                    'account_source' => 'loan_calculator',
                    'temp_password' => encrypt($temporaryPassword), // Store temporarily for email
                    'password_sent_at' => now(),
                    'is_active' => 'Yes',
                    'email_verified_at' => now(), // Auto-verify for loan calculator users
                ]);

                $isNewUser = true;
            } else {
                // Existing user - send password reset link
                $isNewUser = false;
            }

            // Check if borrower already exists with this email - NO LONGER NEEDED
            // We will always create a new borrower record for each application
            // The same email can have multiple borrower applications

            // Create new borrower record for this application
            $borrowerData = [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email, // Same email allowed for multiple borrowers
                'phone' => $request->phone,
                'credit_score' => $request->credit_score,
                'years_of_experience' => $request->experience,
                'property_state' => $request->state,
                'property_type' => $request->property_type ?? null,
                'property_address' => $request->property_address ?? null,
                'property_zip_code' => $request->property_zip_code ?? null,

                // Calculator inputs - common fields
                'transaction_type' => $request->transaction_type,
                'purchase_price' => $request->purchase_price,
                'broker_points' => $request->broker_points,
                'payoff_amount' => $request->payoff_amount,
                'title_charges' => $request->title_charges,
                'property_insurance' => $request->property_insurance,

                // Selected program
                'selected_loan_type' => $request->loan_type,
                'selected_loan_program' => $request->selected_loan_program,

                // Application data
                'application_status' => 'IN_PROCESS',
                'api_url_called' => $request->api_url,
                'api_response_json' => $request->api_response,
                'application_submitted_at' => now(),
                'application_source' => 'loan_calculator',
                'status' => 'active',
            ];

            // Add loan type specific fields
            if ($isDscrLoan) {
                // For DSCR loans, extract values from API response when available
                $apiResponse = $request->api_response;
                $loanProgramValues = null;

                // Extract loan program values from API response
                if (isset($apiResponse['loan_program_values'])) {
                    $loanProgramValues = $apiResponse['loan_program_values'];
                }

                // DSCR loan specific fields - prioritize API response over request data
                $borrowerData = array_merge($borrowerData, [
                    'loan_term' => $loanProgramValues['loan_term'] ?? $request->loan_term,
                    'arv' => null,
                    'rehab_budget' => null,

                    // DSCR specific fields - use API values when available
                    'lender_points' => $loanProgramValues['lender_points'] ?? $request->lender_points,
                    'pre_pay_penalty' => $loanProgramValues['pre_pay_penalty'] ?? $request->pre_pay_penalty,
                    'dscr' => $loanProgramValues['calculated_dscr'] ?? $request->dscr,

                    // User input fields (not from API)
                    'occupancy_type' => $request->occupancy_type,
                    'monthly_market_rent' => $request->monthly_market_rent,
                    'annual_tax' => $request->annual_tax,
                    'annual_insurance' => $request->annual_insurance,
                    'annual_hoa' => $request->annual_hoa,
                    'purchase_date' => $request->purchase_date,

                    // Extract DSCR loan amounts from API response or calculated values
                    'loan_amount_requested' => $request->loan_amount_requested ?? null,
                    'purchase_loan_amount' => $request->calculated_values['purchase_loan_amount'] ?? 0,
                    'rehab_loan_amount' => 0, // DSCR loans don't have rehab
                    'total_loan_amount' => $request->calculated_values['total_loan_amount'] ?? 0,

                    // DSCR closing costs
                    'lender_origination_fee' => $request->calculated_values['lender_origination_fee'] ?? 0,
                    'broker_fee' => $request->calculated_values['broker_fee'] ?? 0,
                    'underwriting_processing_fee' => $request->calculated_values['underwriting_processing_fee'] ?? 0,
                    'interest_reserves' => $request->calculated_values['interest_reserves'] ?? 0,
                    'title_costs' => $request->calculated_values['title_charges'] ?? 0,
                    'legal_doc_prep_fee' => $request->calculated_values['legal_doc_prep_fee'] ?? 0,
                    'subtotal_closing_costs' => $request->calculated_values['subtotal_closing_costs'] ?? 0,
                    'cash_due_to_buyer' => $request->calculated_values['cash_due_to_buyer'] ?? 0,
                ]);
            } else {
                // Fix & Flip / New Construction loan specific fields
                $borrowerData = array_merge($borrowerData, [
                    'loan_term' => $request->loan_term,
                    'arv' => $request->arv,
                    'rehab_budget' => $request->rehab_budget,
                    'lender_points' => $request->lender_points,
                    'pre_pay_penalty' => $request->pre_pay_penalty,

                    // DSCR fields set to null for non-DSCR loans
                    'occupancy_type' => null,
                    'monthly_market_rent' => null,
                    'annual_tax' => null,
                    'annual_insurance' => null,
                    'annual_hoa' => null,
                    'dscr' => null,
                    'purchase_date' => null,

                    // Standard loan amounts
                    'purchase_loan_amount' => $request->calculated_values['purchase_loan_amount'],
                    'rehab_loan_amount' => $request->calculated_values['rehab_loan_amount'],
                    'total_loan_amount' => $request->calculated_values['total_loan_amount'],

                    // Standard closing costs
                    'lender_origination_fee' => $request->calculated_values['lender_origination_fee'] ?? 0,
                    'broker_fee' => $request->calculated_values['broker_fee'] ?? 0,
                    'underwriting_processing_fee' => $request->calculated_values['underwriting_processing_fee'] ?? 0,
                    'interest_reserves' => $request->calculated_values['interest_reserves'] ?? 0,
                    'title_costs' => $request->calculated_values['title_charges'] ?? 0,
                    'legal_doc_prep_fee' => $request->calculated_values['legal_doc_prep_fee'] ?? 0,
                    'subtotal_closing_costs' => $request->calculated_values['subtotal_closing_costs'] ?? 0,
                    'cash_due_to_buyer' => $request->calculated_values['cash_due_to_buyer'] ?? 0,
                ]);
            }

            $borrower = Borrower::create($borrowerData);

            // Save all loan program results
            foreach ($request->loan_programs as $index => $programData) {
                $loanProgramData = [
                    'borrower_id' => $borrower->id,
                    'loan_type' => $programData['loan_type'] ?? $request->loan_type,
                    'loan_program' => $programData['loan_program'],
                    'is_selected' => $programData['is_selected'],
                    'raw_loan_data' => $programData,
                    'display_order' => $index + 1,
                    'program_status' => $programData['is_selected'] ? 'selected' : 'available',
                ];

                if ($isDscrLoan) {
                    // For DSCR loans, extract data from the API response structure
                    $loanValues = $programData['loan_program_values'] ?? [];

                    $loanProgramData = array_merge($loanProgramData, [
                        'loan_term' => $loanValues['loan_term'] ?? null,
                        'interest_rate' => $loanValues['interest_rate'] ?? null,
                        'lender_points' => $loanValues['lender_points'] ?? null,
                        'max_ltv' => $loanValues['max_ltv'] ?? null,
                        'max_ltc' => null, // DSCR loans don't typically use LTC
                        'max_ltfc' => null, // DSCR loans don't typically use LTFC
                        'purchase_loan_up_to' => $loanValues['loan_amount'] ?? 0,
                        'rehab_loan_up_to' => 0, // DSCR loans don't have rehab
                        'total_loan_up_to' => $loanValues['loan_amount'] ?? 0,
                        'rehab_category' => null,
                        'rehab_percentage' => null,
                        'pricing_tier' => $loanValues['pricing_tier'] ?? null,
                    ]);
                } else {
                    // For Fix & Flip / New Construction loans, use existing logic
                    $loanProgramData = array_merge($loanProgramData, [
                        'loan_term' => $programData['loan_term'] ?? $request->loan_term . ' Months',
                        'interest_rate' => $programData['interest_rate'] ?? null,
                        'lender_points' => $programData['lender_points'] ?? null,
                        'max_ltv' => $programData['max_ltv'] ?? null,
                        'max_ltc' => $programData['max_ltc'] ?? null,
                        'max_ltfc' => $programData['max_ltfc'] ?? null,
                        'purchase_loan_up_to' => $programData['purchase_loan_up_to'] ?? 0,
                        'rehab_loan_up_to' => $programData['rehab_loan_up_to'] ?? 0,
                        'total_loan_up_to' => $programData['total_loan_up_to'] ?? 0,
                        'rehab_category' => $programData['rehab_category'] ?? null,
                        'rehab_percentage' => $programData['rehab_percentage'] ?? null,
                        'pricing_tier' => $programData['pricing_tier'] ?? null,
                    ]);
                }

                LoanProgramResult::create($loanProgramData);
            }

            // Send email notification
            $message = 'Application submitted successfully!';
            if ($isNewUser && $temporaryPassword) {
                $this->sendNewUserEmail($user, $temporaryPassword, $borrower);
                $message = 'Application submitted successfully! A new account has been created and login credentials have been sent to your email.';
            } else {
                $this->sendExistingUserEmail($user, $borrower);
                $message = 'New loan application submitted successfully! A confirmation email has been sent to your existing account.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'borrower_id' => $borrower->id,
                    'application_status' => $borrower->application_status,
                    'is_new_user' => $isNewUser,
                    'email_sent' => true,
                    'is_new_application' => true, // Always true since we always create new borrower records
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email to new user with credentials
     */
    private function sendNewUserEmail($user, $temporaryPassword, $borrower)
    {
        try {
            // Send welcome email with temporary password
            Mail::to($user->email)->send(
                new LoanApplicationWelcome($user, $borrower, true, $temporaryPassword)
            );

            // For now, just update the user that email was sent
            $user->update(['password_sent_at' => now()]);

        } catch (\Exception $e) {
            \Log::error('Failed to send new user email: ' . $e->getMessage());
        }
    }

    /**
     * Send email to existing user with password reset link
     */
    private function sendExistingUserEmail($user, $borrower)
    {
        try {
            // Send loan application confirmation email
            Mail::to($user->email)->send(
                new LoanApplicationWelcome($user, $borrower, false)
            );

        } catch (\Exception $e) {
            \Log::error('Failed to send existing user email: ' . $e->getMessage());
        }
    }

    /**
     * Get borrower applications (for admin)
     */
    public function getBorrowerApplications(Request $request)
    {
        $applications = Borrower::with(['user', 'loanProgramResults', 'selectedLoanProgram'])
            ->where('application_source', 'loan_calculator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Get specific borrower application details
     */
    public function getBorrowerApplication($id)
    {
        $borrower = Borrower::with(['user', 'loanProgramResults', 'selectedLoanProgram'])
            ->where('id', $id)
            ->where('application_source', 'loan_calculator')
            ->first();

        if (!$borrower) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $borrower
        ]);
    }
}