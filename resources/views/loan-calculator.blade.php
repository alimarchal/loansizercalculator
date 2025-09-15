<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculator</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="{{ url('apexcharts/apexcharts.js') }}"></script>
    <link href="{{ url('select2/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ url('jsandcss/daterangepicker.min.css') }}">
    <script src="{{ url('jsandcss/moment.min.js') }}"></script>
    <script src="{{ url('jsandcss/knockout-3.5.1.js') }}" defer></script>
    <script src="{{ url('jsandcss/daterangepicker.min.js') }}" defer></script>

    <!-- jsPDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    @stack('header')

    <style>
        .select2 {
            /*width:100%!important;*/
            width: auto !important;
            display: block;
        }

        .select2-container .select2-selection--single {
            height: auto;
            /* Reset the height if necessary */
            padding: 0.7rem 1rem;
            /* This should match Tailwind's py-2 px-4 */
            line-height: 1.25;
            /* Adjust based on Tailwind's line height for consistency */
            /*font-size: 0.875rem; !* Matches Tailwind's text-sm *!*/
            border: 1px solid #d1d5db;
            /* Tailwind's border-gray-300 */
            border-radius: 0.375rem;
            /* Tailwind's rounded-md */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            /* Tailwind's shadow-sm */
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 1.25;
            /* Aligns text vertically */
            padding-left: 0;
            /* Adjust if needed */
            padding-right: 0;
            /* Adjust if needed */
        }

        /*.select2-selection__arrow*/
        .select2-container .select2-selection--single {
            height: auto;
            /* Ensure the arrow aligns with the adjusted height */
            right: 0.5rem;
            /* Align the arrow similarly to Tailwind's padding */

        }

        .select2-selection__arrow {
            top: 8px !important;
            right: 10px !important;
        }

        /* Closing Statement Styles */
        #closingStatementSection .bg-blue-600 {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        /* Editable Loan Amount Input Styles */
        .loan-amount-input {
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
            border: 2px dashed #3b82f6;
            cursor: pointer;
        }

        .loan-amount-input:hover {
            background-color: #ffffff;
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: scale(1.02);
        }

        .loan-amount-input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: none;
            transform: scale(1.02);
        }

        .loan-amount-input:invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        #closingStatementSection table td {
            vertical-align: middle;
        }

        #closingStatementSection .font-bold {
            font-weight: 700;
        }

        /* Highlight important rows */
        #closingStatementSection tr.bg-gray-50 {
            background-color: #f9fafb;
        }

        #closingStatementSection tr.bg-gray-200 {
            background-color: #e5e7eb;
        }

        /* Hover effect for table rows */
        #closingStatementSection tbody tr:hover {
            background-color: #f3f4f6;
        }

        /* Responsive adjustments for two-column layout */
        @media (max-width: 1279px) {
            .grid.xl\\:grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }

        /* Compact table styling for smaller screens */
        @media (max-width: 768px) {

            #resultsSection table th,
            #resultsSection table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
            }

            #closingStatementSection table th,
            #closingStatementSection table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.875rem;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles



    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <!-- Header -->
                    <div class="mb-6 mx-auto text-center">
                        <h1 class="text-2xl font-bold text-gray-900 mx-auto">Structure My Loan Calculator</h1>
                        <p class="text-gray-600 mt-2">Input your loan summary and request a Pre Approval!</p>
                    </div>

                    <!-- Main Form -->
                    <form id="loanCalculatorForm" method="POST" action="/calculate-loan">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Credit Score -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="credit_score">
                                    Credit Score <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="credit_score" id="credit_score" min="300" max="850" value="700"
                                    placeholder="Enter credit score" required>
                            </div>

                            <!-- Experience -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="experience">
                                    Experience (Years) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="experience" id="experience" min="0" max="50" value="4"
                                    placeholder="Enter years of experience" required>
                            </div>

                            <!-- Borrower Name -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="borrower_name">
                                    Borrower Name
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="text" name="borrower_name" id="borrower_name" value=""
                                    placeholder="Enter full name">
                            </div>

                            <!-- Borrower Email -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="borrower_email">
                                    Borrower Email
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="email" name="borrower_email" id="borrower_email" value=""
                                    placeholder="Enter email address">
                            </div>

                            <!-- Borrower Phone -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="borrower_phone">
                                    Borrower Phone
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="tel" name="borrower_phone" id="borrower_phone" value=""
                                    placeholder="Enter phone number">
                            </div>

                            <!-- Broker Name -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="broker_name">
                                    Broker Name
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="text" name="broker_name" id="broker_name" value=""
                                    placeholder="Enter broker name">
                            </div>

                            <!-- Broker Email -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="broker_email">
                                    Broker Email
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="email" name="broker_email" id="broker_email" value=""
                                    placeholder="Enter broker email">
                            </div>

                            <!-- Broker Phone -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="broker_phone">
                                    Broker Phone
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="tel" name="broker_phone" id="broker_phone" value=""
                                    placeholder="Enter broker phone">
                            </div>

                            <!-- Broker Points -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="broker_points">
                                    Broker Points (%) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="broker_points" id="broker_points" min="0" max="10" step="0.1"
                                    value="" placeholder="Enter points percentage" required>
                            </div>

                            <!-- Loan Type -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="loan_type">
                                    Loan Type <span class="text-red-500">*</span>
                                </label>
                                <select name="loan_type" id="loan_type"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Select Loan Type --</option>
                                    @foreach($loanTypes as $loanType)
                                    <option value="{{ $loanType->name }}">{{ $loanType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Transaction Type -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="transaction_type">
                                    Transaction Type <span class="text-red-500">*</span>
                                </label>
                                <select name="transaction_type" id="transaction_type"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Select Transaction Type --</option>
                                    @foreach($transactionTypes as $transactionType)
                                    <option value="{{ $transactionType->name }}">{{ $transactionType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Loan Term -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="loan_term">
                                    Loan Term <span class="text-red-500">*</span>
                                </label>
                                <select name="loan_term" id="loan_term"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Select Loan Term --</option>
                                    <option value="12">12 Months</option>
                                    <option value="18">18 Months</option>
                                </select>
                            </div>

                            <!-- Property Type -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="property_type">
                                    Property Type <span class="text-red-500">*</span>
                                </label>
                                <select name="property_type" id="property_type"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Select Loan Type First --</option>
                                </select>
                            </div>


                            <!-- Property Address -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="property_address">
                                    Property Address
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="text" name="property_address" id="property_address" value=""
                                    placeholder="Enter property address">
                            </div>

                            <!-- State -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="state">
                                    State <span class="text-red-500">*</span>
                                </label>
                                <select name="state" id="state"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Select Loan Type First --</option>
                                </select>
                            </div>

                            <!-- Zip Code -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="zip_code">
                                    Zip Code
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="text" name="zip_code" id="zip_code" value="" placeholder="Enter zip code">
                            </div>



                            <!-- Purchase Price -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="purchase_price">
                                    Purchase Price <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="purchase_price" id="purchase_price" min="0" value="90000"
                                    placeholder="Enter purchase price" required>
                            </div>

                            <!-- Rehab Budget -->
                            <div id="rehab_budget_field">
                                <label class="block font-medium text-sm text-gray-700" for="rehab_budget">
                                    Rehab Budget <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="rehab_budget" id="rehab_budget" min="0" value="40000"
                                    placeholder="Enter rehab budget" required>
                            </div>

                            <!-- ARV -->
                            <div id="arv_field">
                                <label class="block font-medium text-sm text-gray-700" for="arv">
                                    ARV (After Repair Value) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="arv" id="arv" min="0" value="250000"
                                    placeholder="Enter ARV amount" required>
                            </div>

                            <!-- DSCR Rental Loan Specific Fields -->

                            <!-- Occupancy Type (DSCR only) -->
                            <div id="occupancy_type_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="occupancy_type">
                                    Occupancy Type <span class="text-red-500">*</span>
                                </label>
                                <select name="occupancy_type" id="occupancy_type"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Select Occupancy Type --</option>
                                </select>
                            </div>

                            <!-- Monthly Market Rent (DSCR only) -->
                            <div id="monthly_market_rent_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="monthly_market_rent">
                                    Monthly Market Rent <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="monthly_market_rent" id="monthly_market_rent" min="0" value=""
                                    placeholder="Enter monthly market rent">
                            </div>

                            <!-- Annual Tax (DSCR only) -->
                            <div id="annual_tax_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="annual_tax">
                                    Annual Tax <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="annual_tax" id="annual_tax" min="0" value=""
                                    placeholder="Enter annual tax amount">
                            </div>

                            <!-- Annual Insurance (DSCR only) -->
                            <div id="annual_insurance_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="annual_insurance">
                                    Annual Insurance <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="annual_insurance" id="annual_insurance" min="0" value=""
                                    placeholder="Enter annual insurance amount">
                            </div>

                            <!-- Annual HOA (DSCR only) -->
                            <div id="annual_hoa_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="annual_hoa">
                                    Annual HOA <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="annual_hoa" id="annual_hoa" min="0" value=""
                                    placeholder="Enter annual HOA amount">
                            </div>

                            <!-- DSCR (DSCR only) -->
                            <div id="dscr_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="dscr">
                                    DSCR <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="dscr" id="dscr" min="0" step="0.01" value=""
                                    placeholder="Enter DSCR value">
                            </div>

                            <!-- Purchase Date (DSCR only) -->
                            <div id="purchase_date_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="purchase_date">
                                    Purchase Date
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="date" name="purchase_date" id="purchase_date" value="">
                            </div>

                            <!-- Payoff Amount (DSCR only) -->
                            <div id="payoff_amount_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="payoff_amount">
                                    Payoff Amount <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="payoff_amount" id="payoff_amount" min="0" value=""
                                    placeholder="Enter payoff amount">
                            </div>

                            <!-- Lender Points (DSCR only) -->
                            <div id="lender_points_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="lender_points">
                                    Lender Points (%) <span class="text-red-500">*</span>
                                </label>
                                <select name="lender_points" id="lender_points"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1.000">1.000</option>
                                    <option value="1.500">1.500</option>
                                    <option value="2.000" selected>2.000</option>
                                </select>
                            </div>

                            <!-- Pre Pay Penalty (DSCR only) -->
                            <div id="pre_pay_penalty_field" class="hidden">
                                <label class="block font-medium text-sm text-gray-700" for="pre_pay_penalty">
                                    Pre Pay Penalty <span class="text-red-500">*</span>
                                </label>
                                <select name="pre_pay_penalty" id="pre_pay_penalty"
                                    class="block mt-1 w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Select Pre Pay Penalty --</option>
                                </select>
                            </div>

                        </div>

                        <!-- Submit and Reset Buttons -->
                        <div class="mt-6 flex space-x-4">
                            <button type="submit" id="calculateBtn"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Calculate Loan Options
                            </button>
                            <button type="reset"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Clear All Fields
                            </button>
                        </div>
                    </form>

                    <!-- Results Section -->
                    <div class="mt-8">
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="hidden text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-gray-600 text-sm mt-2">Calculating loan options...</p>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage"
                            class="hidden mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            <div class="flex">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span id="errorText"></span>
                            </div>
                        </div>

                        <!-- Results Section (Hidden until calculation) -->
                        <div id="resultsAndClosingSection" class="hidden">
                            <!-- Loan Program Results - Full Width -->
                            <div id="resultsSection" class="mb-8">
                                <div class="text-center mb-6">
                                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Loan Program Results</h2>
                                    <p class="text-gray-600">Compare loan programs and select the best option for your
                                        needs</p>
                                </div>

                                <!-- Enhanced Results Table -->
                                <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200">
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead id="tableHeader"
                                                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                                <!-- Dynamic header will be populated here -->
                                            </thead>
                                            <tbody id="loanResultsTable" class="divide-y divide-gray-200">
                                                <!-- Dynamic rows will be populated here after calculation -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> <!-- End Loan Program Results -->

                            <!-- Loan Program Selection Cards -->
                            <div id="loanProgramCards" class="mb-8">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">Select Your Loan Program</h2>
                                <div id="loanProgramCardsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Dynamic loan program cards will be populated here -->
                                </div>
                            </div>

                            <!-- Estimated Closing Statement - Beautiful Cards Layout -->
                            <div id="closingStatementSection" class="hidden">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6">Estimated Closing Statement</h2>

                                <!-- Cards Grid Layout -->
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                                    <!-- Loan Amount Card -->
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg border-l-4 border-blue-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-blue-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-dollar-sign text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-blue-800">Loan Amounts</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Purchase Loan:</span>
                                                <span id="closingPurchaseLoan" class="font-bold text-blue-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Rehab Loan:</span>
                                                <span id="closingRehabLoan" class="font-bold text-green-700">$0</span>
                                            </div>
                                            <div class="border-t pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold text-gray-700">Total Loan:</span>
                                                    <span id="closingTotalLoan"
                                                        class="font-bold text-lg text-purple-700">$0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Property Costs Card -->
                                    <div
                                        class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg border-l-4 border-green-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-green-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-home text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-green-800">Property Costs</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Purchase Price:</span>
                                                <span id="closingPurchasePrice"
                                                    class="font-bold text-green-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Rehab Budget:</span>
                                                <span id="closingRehabBudget" class="font-bold text-green-700">$0</span>
                                            </div>
                                            <div class="border-t pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold text-gray-700">Subtotal:</span>
                                                    <span id="closingSubtotalBuyer"
                                                        class="font-bold text-lg text-green-700">$0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lender Fees Card -->
                                    <div
                                        class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-lg border-l-4 border-orange-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-orange-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-university text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-orange-800">Lender Fees</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Origination Fee:</span>
                                                <span id="closingOriginationFee"
                                                    class="font-bold text-orange-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Broker Fee:</span>
                                                <span id="closingBrokerFee" class="font-bold text-orange-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Underwriting:</span>
                                                <span id="closingUnderwritingFee"
                                                    class="font-bold text-orange-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Interest Reserves:</span>
                                                <span id="closingInterestReserves"
                                                    class="font-bold text-orange-700">$0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Final Summary Card -->
                                    <div
                                        class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg border-l-4 border-purple-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-purple-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-calculator text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-purple-800">Other Costs</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Title Charges:</span>
                                                <span id="closingTitleCharges"
                                                    class="font-bold text-purple-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Insurance:</span>
                                                <span id="closingPropertyInsurance"
                                                    class="font-bold text-purple-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Legal/Doc Fee:</span>
                                                <span id="closingLegalDocFee"
                                                    class="font-bold text-purple-700">$0</span>
                                            </div>
                                            <div class="border-t pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold text-gray-700">Closing Costs:</span>
                                                    <span id="closingSubtotalCosts"
                                                        class="font-bold text-lg text-purple-700">$0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Cash Due Summary Card -->
                                <div
                                    class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl shadow-xl p-6 text-white mb-8">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                                                <i class="fas fa-money-bill-wave text-2xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold">Cash Due to Complete</h3>
                                                <p class="text-red-100 text-sm">Total amount you need to bring to
                                                    closing</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span id="closingCashDue" class="text-3xl font-bold">$0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button onclick="downloadLoanPDF()"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-lg font-semibold shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i>
                                        Download PDF
                                    </button>
                                    <button onclick="startApplication()" id="startApplicationBtn"
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-semibold shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Start Application
                                    </button>
                                </div>
                            </div> <!-- End Estimated Closing Statement -->

                        </div> <!-- End Results and Closing Section -->

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loanCalculatorForm');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const errorMessage = document.getElementById('errorMessage');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Hide previous errors
                errorMessage.classList.add('hidden');
                
                // Show loading spinner
                loadingSpinner.classList.remove('hidden');
                
                try {
                    // Get form data
                    const formData = new FormData(form);
                    const loanType = formData.get('loan_type');
                    
                    let apiUrl;
                    const apiParams = new URLSearchParams();
                    
                    // Add common parameters
                    apiParams.append('credit_score', formData.get('credit_score'));
                    apiParams.append('experience', formData.get('experience'));
                    apiParams.append('loan_type', loanType);
                    apiParams.append('transaction_type', formData.get('transaction_type'));
                    apiParams.append('loan_term', formData.get('loan_term'));
                    apiParams.append('purchase_price', formData.get('purchase_price'));
                    apiParams.append('broker_points', formData.get('broker_points'));
                    apiParams.append('state', formData.get('state'));
                    
                    if (loanType === 'DSCR Rental Loans') {
                        // Use DSCR-specific API endpoint and parameters
                        apiUrl = '/api/loan-matrix-dscr';
                        
                        // Add DSCR-specific parameters
                        apiParams.append('property_type', formData.get('property_type'));
                        apiParams.append('occupancy_type', formData.get('occupancy_type'));
                        apiParams.append('monthly_market_rent', formData.get('monthly_market_rent'));
                        apiParams.append('annual_tax', formData.get('annual_tax'));
                        apiParams.append('annual_insurance', formData.get('annual_insurance'));
                        apiParams.append('annual_hoa', formData.get('annual_hoa'));
                        apiParams.append('dscr', formData.get('dscr'));
                        apiParams.append('payoff_amount', formData.get('payoff_amount'));
                        apiParams.append('lender_points', formData.get('lender_points'));
                        apiParams.append('pre_pay_penalty', formData.get('pre_pay_penalty'));
                        
                        // Add purchase date if provided
                        if (formData.get('purchase_date')) {
                            apiParams.append('purchase_date', formData.get('purchase_date'));
                        }
                    } else {
                        // Use regular API endpoint and parameters
                        apiUrl = '/api/loan-matrix';
                        apiParams.append('arv', formData.get('arv'));
                        apiParams.append('rehab_budget', formData.get('rehab_budget'));
                    }
                    
                    const finalApiUrl = `${apiUrl}?${apiParams.toString()}`;
                    
                    // Make API call
                    const response = await fetch(finalApiUrl);
                    
                    // Check if response is ok (status 200-299)
                    if (!response.ok) {
                        const errorData = await response.json();
                        loadingSpinner.classList.add('hidden');
                        
                        // Check if this is a DSCR disqualifier response (status 400 with detailed notifications)
                        if (response.status === 400 && errorData.disqualifier_notifications && Array.isArray(errorData.disqualifier_notifications) && errorData.disqualifier_notifications.length > 0) {
                            showDetailedError(errorData.message || 'Loan application does not meet qualification criteria', errorData.disqualifier_notifications);
                        } else {
                            const errorMsg = errorData.message || `HTTP Error: ${response.status}`;
                            showError(errorMsg);
                        }
                        // Hide entire results and closing section
                        document.getElementById('resultsAndClosingSection').classList.add('hidden');
                        return;
                    }
                    
                    const data = await response.json();
                    
                    // Hide loading spinner
                    loadingSpinner.classList.add('hidden');
                    
                    if (data.success && data.data && data.data.length > 0) {
                        if (loanType === 'DSCR Rental Loans') {
                            populateDscrResults(data.data);
                        } else {
                            populateResults(data.data);
                        }
                    } else {
                        // Check if there are detailed disqualifier notifications
                        if (data.disqualifier_notifications && Array.isArray(data.disqualifier_notifications) && data.disqualifier_notifications.length > 0) {
                            // Show detailed error messages
                            showDetailedError(data.message || 'DSCR loan application does not meet qualification criteria', data.disqualifier_notifications);
                        } else {
                            // Show generic API error message if available, otherwise show generic message
                            const errorMsg = data.message || 'No loan programs found for the given criteria.';
                            showError(errorMsg);
                        }
                        // Hide entire results and closing section
                        document.getElementById('resultsAndClosingSection').classList.add('hidden');
                    }
                    
                } catch (error) {
                    loadingSpinner.classList.add('hidden');
                    showError('An error occurred while calculating loan options. Please try again.');
                    // Hide entire results and closing section
                    document.getElementById('resultsAndClosingSection').classList.add('hidden');
                    console.error('API Error:', error);
                }
            });
            
            // Add reset event listener to hide results when form is reset
            form.addEventListener('reset', function(e) {
                // Hide results and closing sections
                document.getElementById('resultsAndClosingSection').classList.add('hidden');
                document.getElementById('closingStatementSection').classList.add('hidden');
                // Hide error messages
                errorMessage.classList.add('hidden');
                // Hide loading spinner if visible
                loadingSpinner.classList.add('hidden');
                
                // Reset Select2 dropdowns to default state
                setTimeout(() => {
                    // Reset all Select2 dropdowns to their default/first option
                    $('#loan_type').val('').trigger('change');
                    $('#transaction_type').val('').trigger('change');
                    $('#loan_term').val('').trigger('change');
                    $('#property_type').empty().append('<option value="">-- Select Loan Type First --</option>').trigger('change');
                    $('#state').empty().append('<option value="">-- Select Loan Type First --</option>').trigger('change');
                    $('#occupancy_type').empty().append('<option value="">-- Select Occupancy Type --</option>').trigger('change');
                    $('#pre_pay_penalty').empty().append('<option value="">-- Select Pre Pay Penalty --</option>').trigger('change');
                    
                    // Reset field visibility to default state (show regular fields, hide DSCR fields)
                    handleLoanTypeFieldVisibility('');
                }, 100);
            });
            
            function generateTableHeader(loanType) {
                const tableHeader = document.getElementById('tableHeader');
                
                // Define base columns that always appear
                let headerHTML = `
                    <th class="px-6 py-4 text-left font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                        <i class="fas fa-clipboard-list mr-2"></i>Loan Program
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-green-600 to-green-700 border-r border-green-500">
                        <i class="fas fa-calendar-alt mr-2"></i>Term
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 border-r border-purple-500">
                        <i class="fas fa-percentage mr-2"></i>Rate
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-orange-600 to-orange-700 border-r border-orange-500">
                        <i class="fas fa-chart-line mr-2"></i>Points
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 border-r border-indigo-500">
                        <i class="fas fa-home mr-2"></i>Max LTV
                    </th>
                `;
                
                // Add conditional columns based on loan type
                if (loanType === 'Fix and Flip') {
                    // For Fix and Flip: show only Max LTC
                    headerHTML += `
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                            <i class="fas fa-hammer mr-2"></i>Max LTC
                        </th>
                    `;
                } else if (loanType === 'New Construction' || loanType === 'DSCR Rental') {
                    // For New Construction and DSCR Rental: show only Max LTFC
                    headerHTML += `
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-pink-600 to-pink-700 border-r border-pink-500">
                            <i class="fas fa-tools mr-2"></i>Max LTFC
                        </th>
                    `;
                } else {
                    // For other loan types: show both columns (fallback)
                    headerHTML += `
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                            <i class="fas fa-hammer mr-2"></i>Max LTC
                        </th>
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-pink-600 to-pink-700 border-r border-pink-500">
                            <i class="fas fa-tools mr-2"></i>Max LTFC
                        </th>
                    `;
                }
                
                // Add remaining columns
                headerHTML += `
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-cyan-600 to-cyan-700 border-r border-cyan-500">
                        <i class="fas fa-dollar-sign mr-2"></i>Purchase Loan Up To
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 border-r border-emerald-500">
                        <i class="fas fa-wrench mr-2"></i>Rehab Loan Up To
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-violet-600 to-violet-700">
                        <i class="fas fa-coins mr-2"></i>Total Loan Up To
                    </th>
                `;
                
                tableHeader.innerHTML = headerHTML;
            }
            
            function populateResults(loans) {
                console.log('All loans data:', loans); // Debug log
                
                const tableBody = document.getElementById('loanResultsTable');
                
                // Clear existing content
                tableBody.innerHTML = '';
                
                if (!loans || loans.length === 0) {
                    return;
                }
                
                // Generate dynamic table header based on loan type
                const loanType = loans[0].loan_type;
                generateTableHeader(loanType);
                
                // Check if this is New Construction loan type
                const isNewConstruction = loans[0].loan_type === 'New Construction';
                
                // Group loans by loan program for display
                const loansByProgram = {};
                loans.forEach(loan => {
                    const programKey = loan.loan_program || 'Unknown';
                    if (!loansByProgram[programKey]) {
                        loansByProgram[programKey] = [];
                    }
                    loansByProgram[programKey].push(loan);
                });
                
                // Create table rows and cards for each loan program
                Object.keys(loansByProgram).forEach((programName, index) => {
                    const loan = loansByProgram[programName][0]; // Use first loan of each program
                    const loanData = loan.loan_type_and_loan_program_table;
                    
                    // Determine program display name and icon
                    let displayName = programName;
                    let iconClass = 'fas fa-calculator';
                    let colorClass = index === 0 ? 'blue' : 'green';
                    
                    if (isNewConstruction) {
                        if (programName === 'EXPERIENCED BUILDER') {
                            displayName = 'Experienced Builder';
                            iconClass = 'fas fa-hammer';
                            colorClass = 'blue';
                        } else if (programName === 'NEW BUILDER') {
                            displayName = 'New Builder';
                            iconClass = 'fas fa-tools';
                            colorClass = 'green';
                        }
                    } else {
                        if (programName === 'FULL APPRAISAL') {
                            displayName = 'Full Appraisal';
                            iconClass = 'fas fa-file-alt';
                            colorClass = 'blue';
                        } else if (programName === 'DESKTOP APPRAISAL') {
                            displayName = 'Desktop Appraisal';
                            iconClass = 'fas fa-desktop';
                            colorClass = 'green';
                        }
                    }
                    
                    // Create enhanced table row with conditional columns
                    const row = document.createElement('tr');
                    row.className = `hover:bg-gray-50 transition-colors duration-200 ${index % 2 === 0 ? 'bg-white' : 'bg-gray-25'}`;
                    
                    // Base columns that always appear
                    let rowHTML = `
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-${colorClass}-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="${iconClass} text-${colorClass}-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">${displayName}</div>
                                    <div class="text-sm text-gray-500">Loan Program</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                ${loanData?.loan_term || 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-green-600">
                                ${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-orange-600">
                                ${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                ${loanData?.max_ltv ? loanData.max_ltv + '%' : '0%'}
                            </span>
                        </td>
                    `;
                    
                    // Add conditional columns based on loan type
                    if (loan.loan_type === 'Fix and Flip') {
                        // For Fix and Flip: show only Max LTC
                        rowHTML += `
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    ${loanData?.max_ltc ? loanData.max_ltc + '%' : '0%'}
                                </span>
                            </td>
                        `;
                    } else if (loan.loan_type === 'New Construction' || loan.loan_type === 'DSCR Rental') {
                        // For New Construction and DSCR Rental: show only Max LTFC
                        rowHTML += `
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    ${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0%'}
                                </span>
                            </td>
                        `;
                    } else {
                        // For other loan types: show both columns (fallback)
                        rowHTML += `
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    ${loanData?.max_ltc ? loanData.max_ltc + '%' : '0%'}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    ${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0%'}
                                </span>
                            </td>
                        `;
                    }
                    
                    // Add remaining columns
                    rowHTML += `
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-blue-600">
                                $${loanData?.purchase_loan_up_to ? numberWithCommas(loanData.purchase_loan_up_to) : 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-green-600">
                                $${loanData?.rehab_loan_up_to ? numberWithCommas(loanData.rehab_loan_up_to) : 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-xl font-bold text-purple-600" id="total_loan_${index}">
                                $${loanData?.total_loan_up_to ? numberWithCommas(loanData.total_loan_up_to) : 'N/A'}
                            </span>
                        </td>
                    `;
                    
                    row.innerHTML = rowHTML;
                    tableBody.appendChild(row);
                });

                // Create loan program selection cards
                createLoanProgramCards(loansByProgram);
                
                // Show results section but keep closing statement hidden initially
                document.getElementById('resultsAndClosingSection').classList.remove('hidden');
                
                // Smooth scroll to results section after calculation
                setTimeout(() => {
                    document.getElementById('resultsSection').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }

            function createLoanProgramCards(loansByProgram) {
                const cardsContainer = document.getElementById('loanProgramCardsContainer');
                cardsContainer.innerHTML = '';

                // Store loans data globally for later use
                window.allLoansData = loansByProgram;

                // Check if this is New Construction loan type
                const firstLoan = Object.values(loansByProgram)[0][0];
                const isNewConstruction = firstLoan.loan_type === 'New Construction';

                Object.keys(loansByProgram).forEach((programName, index) => {
                    const loan = loansByProgram[programName][0];
                    const loanData = loan.loan_type_and_loan_program_table;
                    
                    // Determine program display name and styling
                    let displayName = programName;
                    let iconClass = 'fas fa-calculator';
                    let cardColorClass = index === 0 ? 'border-blue-500 bg-blue-50' : 'border-green-500 bg-green-50';
                    let headerColorClass = index === 0 ? 'bg-blue-600' : 'bg-green-600';
                    let buttonColorClass = index === 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700';
                    
                    if (isNewConstruction) {
                        if (programName === 'EXPERIENCED BUILDER') {
                            displayName = 'Experienced Builder';
                            iconClass = 'fas fa-hammer';
                        } else if (programName === 'NEW BUILDER') {
                            displayName = 'New Builder';
                            iconClass = 'fas fa-tools';
                        }
                    } else {
                        if (programName === 'FULL APPRAISAL') {
                            displayName = 'Full Appraisal';
                            iconClass = 'fas fa-file-alt';
                        } else if (programName === 'DESKTOP APPRAISAL') {
                            displayName = 'Desktop Appraisal';
                            iconClass = 'fas fa-desktop';
                        }
                    }

                    const card = document.createElement('div');
                    card.className = `bg-white rounded-lg shadow-lg border-2 ${cardColorClass} overflow-hidden`;
                    card.innerHTML = `
                        <div class="${headerColorClass} text-white p-4">
                            <div class="flex items-center">
                                <i class="${iconClass} text-2xl mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-bold">Loan Program: ${displayName}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b pb-2">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-700">You qualify for a Purchase Loan up to:</span>
                                        <span class="text-xs text-blue-600 italic">✏️ Click amount below to edit</span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <input type="number" 
                                               id="card_purchase_loan_${programName.replace(/\s+/g, '_')}" 
                                               class="loan-amount-input w-24 px-2 py-1 text-right font-bold text-blue-600 rounded focus:outline-none"
                                               value="${loanData?.purchase_loan_up_to || 0}"
                                               max="${loanData?.purchase_loan_up_to || 0}"
                                               min="0"
                                               step="1000"
                                               data-program="${programName}"
                                               data-max-amount="${loanData?.purchase_loan_up_to || 0}"
                                               onchange="handleCardLoanAmountChange(this, '${programName}')"
                                               onblur="handleCardLoanAmountChange(this, '${programName}')"
                                               title="Enter desired loan amount (up to $${loanData?.purchase_loan_up_to ? numberWithCommas(loanData.purchase_loan_up_to) : '0'})">
                                        <span class="text-xs text-gray-500 mt-1">Max: $${loanData?.purchase_loan_up_to ? numberWithCommas(loanData.purchase_loan_up_to) : 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">You qualify for a Rehab Loan up to:</span>
                                    <span class="font-bold text-green-600">$${loanData?.rehab_loan_up_to ? numberWithCommas(loanData.rehab_loan_up_to) : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">You qualify for Total Loan up to:</span>
                                    <span class="font-bold text-purple-600" id="card_total_loan_${programName.replace(/\s+/g, '_')}">$${loanData?.total_loan_up_to ? numberWithCommas(loanData.total_loan_up_to) : 'N/A'}</span>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button onclick="selectLoanProgram('${programName}')" 
                                        class="${buttonColorClass} text-white px-6 py-3 rounded-lg font-semibold w-full transition-colors duration-200 hover:shadow-lg">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Check Summary for This Program
                                </button>
                            </div>
                        </div>
                    `;
                    cardsContainer.appendChild(card);
                });
            }
            
            function populateDscrResults(loans) {
                console.log('DSCR loans data:', loans); // Debug log
                
                const tableBody = document.getElementById('loanResultsTable');
                
                // Clear existing content
                tableBody.innerHTML = '';
                
                if (!loans || loans.length === 0) {
                    return;
                }
                
                // Generate DSCR-specific table header
                generateDscrTableHeader();
                
                // Create table rows for each DSCR loan program
                loans.forEach((loan, index) => {
                    const loanData = loan.loan_program_values;
                    const programName = loan.loan_program || `Loan Program #${index + 1}`;
                    
                    // Create enhanced table row for DSCR loans
                    const row = document.createElement('tr');
                    row.className = `hover:bg-gray-50 transition-colors duration-200 ${index % 2 === 0 ? 'bg-white' : 'bg-gray-25'}`;
                    
                    let colorClass = index === 0 ? 'blue' : index === 1 ? 'green' : 'purple';
                    let iconClass = 'fas fa-home';
                    
                    row.innerHTML = `
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-${colorClass}-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="${iconClass} text-${colorClass}-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">${programName}</div>
                                    <div class="text-sm text-gray-500">DSCR Rental Loan</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                ${loanData?.loan_term || 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                ${loanData?.max_ltv ? loanData.max_ltv + '%' : '0%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-green-600">
                                $${loanData?.monthly_payment ? numberWithCommas(loanData.monthly_payment) : 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-orange-600">
                                ${loanData?.interest_rate ? loanData.interest_rate + '%' : '0.00%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-indigo-600">
                                ${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ${loanData?.pre_pay_penalty || 'None'}
                            </span>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });

                // Create DSCR loan program selection cards (simplified for DSCR)
                createDscrLoanProgramCards(loans);
                
                // Show results section
                document.getElementById('resultsAndClosingSection').classList.remove('hidden');
                
                // Smooth scroll to results section after calculation
                setTimeout(() => {
                    document.getElementById('resultsSection').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }
            
            function generateDscrTableHeader() {
                const tableHeader = document.getElementById('tableHeader');
                
                const headerHTML = `
                    <th class="px-6 py-4 text-left font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                        <i class="fas fa-clipboard-list mr-2"></i>Loan Program
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-green-600 to-green-700 border-r border-green-500">
                        <i class="fas fa-calendar-alt mr-2"></i>Loan Term
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 border-r border-purple-500">
                        <i class="fas fa-percentage mr-2"></i>Max LTV
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-orange-600 to-orange-700 border-r border-orange-500">
                        <i class="fas fa-dollar-sign mr-2"></i>Monthly Payment
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 border-r border-indigo-500">
                        <i class="fas fa-chart-line mr-2"></i>Interest Rate
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-cyan-600 to-cyan-700 border-r border-cyan-500">
                        <i class="fas fa-coins mr-2"></i>Lender Points
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-violet-600 to-violet-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Pre Pay Penalty
                    </th>
                `;
                
                tableHeader.innerHTML = headerHTML;
            }
            
            function createDscrLoanProgramCards(loans) {
                const cardsContainer = document.getElementById('loanProgramCardsContainer');
                cardsContainer.innerHTML = '';

                // Store DSCR loans data globally for later use
                window.allLoansData = {};
                
                loans.forEach((loan, index) => {
                    const programName = loan.loan_program || `Loan Program #${index + 1}`;
                    window.allLoansData[programName] = [loan];
                    
                    const loanData = loan.loan_program_values;
                    
                    let cardColorClass = index === 0 ? 'border-blue-500 bg-blue-50' : index === 1 ? 'border-green-500 bg-green-50' : 'border-purple-500 bg-purple-50';
                    let headerColorClass = index === 0 ? 'bg-blue-600' : index === 1 ? 'bg-green-600' : 'bg-purple-600';
                    let buttonColorClass = index === 0 ? 'bg-blue-600 hover:bg-blue-700' : index === 1 ? 'bg-green-600 hover:bg-green-700' : 'bg-purple-600 hover:bg-purple-700';
                    
                    const card = document.createElement('div');
                    card.className = `bg-white rounded-lg shadow-lg border-2 ${cardColorClass} overflow-hidden`;
                    card.innerHTML = `
                        <div class="${headerColorClass} text-white p-4">
                            <div class="flex items-center">
                                <i class="fas fa-home text-2xl mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-bold">${programName}</h3>
                                    <p class="text-sm opacity-90">DSCR Rental Loan</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">Monthly Payment:</span>
                                    <span class="font-bold text-green-600">$${loanData?.monthly_payment ? numberWithCommas(loanData.monthly_payment) : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">Interest Rate:</span>
                                    <span class="font-bold text-orange-600">${loanData?.interest_rate ? loanData.interest_rate + '%' : '0.00%'}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">Max LTV:</span>
                                    <span class="font-bold text-purple-600">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0%'}</span>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button onclick="selectLoanProgram('${programName}')" 
                                        class="${buttonColorClass} text-white px-6 py-3 rounded-lg font-semibold w-full transition-colors duration-200 hover:shadow-lg">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Check Summary for This Program
                                </button>
                            </div>
                        </div>
                    `;
                    cardsContainer.appendChild(card);
                });
            }
            
            function showError(message) {
                document.getElementById('errorText').textContent = message;
                errorMessage.classList.remove('hidden');
            }
            
            function showDetailedError(mainMessage, notifications) {
                const errorTextElement = document.getElementById('errorText');
                
                // Create detailed error content with main message and bullet points
                let errorContent = `<div class="font-semibold mb-2">${mainMessage}</div>`;
                
                if (notifications && notifications.length > 0) {
                    errorContent += '<div class="text-sm"><strong>Specific Issues:</strong></div>';
                    errorContent += '<ul class="list-disc list-inside text-sm mt-1 ml-2">';
                    notifications.forEach(notification => {
                        errorContent += `<li>${notification}</li>`;
                    });
                    errorContent += '</ul>';
                }
                
                errorTextElement.innerHTML = errorContent;
                errorMessage.classList.remove('hidden');
            }

            // Tab functionality
        });

        // Global variables and functions (outside DOMContentLoaded)
        let allLoansData = null;

        function selectLoanProgram(programName) {
            if (!window.allLoansData || !window.allLoansData[programName]) {
                console.error('Loan data not found for program:', programName);
                return;
            }

            // Store selected program globally for PDF generation
            window.selectedLoanProgram = programName;

            const selectedLoan = window.allLoansData[programName][0];
            console.log('Selected loan data:', selectedLoan);
            
            // Show closing statement section
            document.getElementById('closingStatementSection').classList.remove('hidden');
            
            // Populate closing statement with selected loan data
            if (selectedLoan.estimated_closing_statement) {
                console.log('Populating closing statement with:', selectedLoan.estimated_closing_statement);
                populateClosingStatement(selectedLoan.estimated_closing_statement);
            } else {
                console.log('No estimated_closing_statement found in loan data');
            }
            
            // Scroll to closing statement
            setTimeout(() => {
                document.getElementById('closingStatementSection').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
            
            // Update button states to show which program is selected
            const allButtons = document.querySelectorAll('#loanProgramCardsContainer button');
            allButtons.forEach(button => {
                if (button.getAttribute('onclick').includes(programName)) {
                    button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Selected - View Summary Below';
                    button.classList.add('ring-4', 'ring-offset-2');
                    button.classList.add(button.classList.contains('bg-blue-600') ? 'ring-blue-300' : 'ring-green-300');
                } else {
                    button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Check Summary for This Program';
                    button.classList.remove('ring-4', 'ring-offset-2', 'ring-blue-300', 'ring-green-300');
                }
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function populateClosingStatement(closingData) {
            // Loan Amount Section
            if (closingData.loan_amount_section) {
                document.getElementById('closingPurchaseLoan').textContent = '$' + numberWithCommas(closingData.loan_amount_section.purchase_loan_amount || 0);
                document.getElementById('closingRehabLoan').textContent = '$' + numberWithCommas(closingData.loan_amount_section.rehab_loan_amount || 0);
                document.getElementById('closingTotalLoan').textContent = '$' + numberWithCommas(closingData.loan_amount_section.total_loan_amount || 0);
            }
            
            // Buyer Related Charges
            if (closingData.buyer_related_charges) {
                document.getElementById('closingPurchasePrice').textContent = '$' + numberWithCommas(closingData.buyer_related_charges.purchase_price || 0);
                document.getElementById('closingRehabBudget').textContent = '$' + numberWithCommas(closingData.buyer_related_charges.rehab_budget || 0);
                document.getElementById('closingSubtotalBuyer').textContent = '$' + numberWithCommas(closingData.buyer_related_charges.sub_total_buyer_charges || 0);
            }
            
            // Lender Related Charges
            if (closingData.lender_related_charges) {
                document.getElementById('closingOriginationFee').textContent = '$' + numberWithCommas(closingData.lender_related_charges.lender_origination_fee || 0);
                document.getElementById('closingBrokerFee').textContent = '$' + numberWithCommas(closingData.lender_related_charges.broker_fee || 0);
                document.getElementById('closingUnderwritingFee').textContent = '$' + numberWithCommas(closingData.lender_related_charges.underwriting_processing_fee || 0);
                document.getElementById('closingInterestReserves').textContent = '$' + numberWithCommas(closingData.lender_related_charges.interest_reserves || 0);
            }
            
            // Title & Other Charges
            if (closingData.title_other_charges) {
                document.getElementById('closingTitleCharges').textContent = '$' + numberWithCommas(closingData.title_other_charges.title_charges || 0);
                document.getElementById('closingPropertyInsurance').textContent = '$' + numberWithCommas(closingData.title_other_charges.property_insurance || 0);
                document.getElementById('closingLegalDocFee').textContent = '$' + numberWithCommas(closingData.title_other_charges.legal_doc_prep_fee || 0);
                document.getElementById('closingSubtotalCosts').textContent = '$' + numberWithCommas(closingData.title_other_charges.subtotal_closing_costs || 0);
            }
            
            // Cash Due to Buyer
            document.getElementById('closingCashDue').textContent = '$' + numberWithCommas(closingData.cash_due_to_buyer || 0);
        }
                        </script>

                        <script src="{{ url('select2/jquery-3.5.1.js') }}"></script>
                        <script src="{{ url('select2/select2.min.js') }}" defer></script>
                        <script>
                            $(document).ready(function () {
                $('.select2').select2();
                
                // Add the loan type change event listener after Select2 initialization
                $('#loan_type').on('change', async function() {
                    const selectedLoanType = this.value;
                    const propertyTypeSelect = $('#property_type');
                    const stateSelect = $('#state');
                    
                    // Handle field visibility based on loan type
                    handleLoanTypeFieldVisibility(selectedLoanType);
                    
                    if (!selectedLoanType) {
                        // Reset property type and state if no loan type selected
                        propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        propertyTypeSelect.trigger('change');
                        stateSelect.trigger('change');
                        return;
                    }
                    
                    try {
                        let apiEndpoint = '/api/loan-type-options';
                        
                        // For DSCR Rental loans, use different logic
                        if (selectedLoanType === 'DSCR Rental Loans') {
                            // Load DSCR-specific data
                            await loadDscrLoanData();
                        } else {
                            // Regular loan type logic
                            const response = await fetch(`${apiEndpoint}?loan_type=${encodeURIComponent(selectedLoanType)}`);
                            
                            if (response.ok) {
                                const data = await response.json();
                                
                                if (data.success) {
                                    // Update property types
                                    propertyTypeSelect.empty().append('<option value="">-- Select Property Type --</option>');
                                    data.data.property_types.forEach(propertyType => {
                                        propertyTypeSelect.append(`<option value="${propertyType.name}">${propertyType.name}</option>`);
                                    });
                                    propertyTypeSelect.trigger('change');
                                    
                                    // Update states
                                    stateSelect.empty().append('<option value="">-- Select State --</option>');
                                    data.data.states.forEach(state => {
                                        stateSelect.append(`<option value="${state.code}">${state.code}</option>`);
                                    });
                                    stateSelect.trigger('change');
                                } else {
                                    console.error('Failed to load loan type options:', data.message);
                                    resetDropdownsToDefault();
                                }
                            } else {
                                console.error('Failed to fetch loan type options');
                                resetDropdownsToDefault();
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching loan type options:', error);
                        resetDropdownsToDefault();
                    }
                    
                    function resetDropdownsToDefault() {
                        propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        propertyTypeSelect.trigger('change');
                        stateSelect.trigger('change');
                    }
                });
                
                // Function to handle field visibility based on loan type
                function handleLoanTypeFieldVisibility(loanType) {
                    const isDscrLoan = loanType === 'DSCR Rental Loans';
                    
                    console.log('handleLoanTypeFieldVisibility called with:', loanType, 'isDscrLoan:', isDscrLoan); // Debug log
                    
                    // Update loan term options based on loan type
                    const loanTermSelect = $('#loan_term');
                    loanTermSelect.empty();
                    loanTermSelect.append('<option value="">-- Select Loan Term --</option>');
                    
                    if (isDscrLoan) {
                        // DSCR loan terms - fetch from database
                        fetch('/api/dscr-loan-terms')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.data) {
                                    data.data.forEach(loanTerm => {
                                        loanTermSelect.append(`<option value="${loanTerm.value}">${loanTerm.name}</option>`);
                                    });
                                } else {
                                    console.error('Failed to load DSCR loan terms:', data.message);
                                    // Fallback to hardcoded options if API fails
                                    loanTermSelect.append('<option value="30 Year Fixed">30 Year Fixed</option>');
                                    loanTermSelect.append('<option value="10 Year IO">10 Year IO</option>');
                                    loanTermSelect.append('<option value="5/1 ARM">5/1 ARM</option>');
                                }
                                loanTermSelect.trigger('change');
                            })
                            .catch(error => {
                                console.error('Error fetching DSCR loan terms:', error);
                                // Fallback to hardcoded options if API fails
                                loanTermSelect.append('<option value="30 Year Fixed">30 Year Fixed</option>');
                                loanTermSelect.append('<option value="10 Year IO">10 Year IO</option>');
                                loanTermSelect.append('<option value="5/1 ARM">5/1 ARM</option>');
                                loanTermSelect.trigger('change');
                            });
                    } else {
                        // Regular loan terms (months)
                        loanTermSelect.append('<option value="12">12 Months</option>');
                        loanTermSelect.append('<option value="18">18 Months</option>');
                    }
                    loanTermSelect.trigger('change');
                    
                    // Regular loan fields - hide for DSCR
                    const regularFields = ['#rehab_budget_field', '#arv_field'];
                    regularFields.forEach(fieldId => {
                        const field = document.querySelector(fieldId);
                        if (field) {
                            if (isDscrLoan) {
                                field.classList.add('hidden');
                                // Remove required attribute from hidden fields
                                const input = field.querySelector('input');
                                if (input) input.removeAttribute('required');
                            } else {
                                field.classList.remove('hidden');
                                // Add required attribute back for visible fields
                                const input = field.querySelector('input');
                                if (input && (fieldId === '#rehab_budget_field' || fieldId === '#arv_field')) {
                                    input.setAttribute('required', 'required');
                                }
                            }
                        }
                    });
                    
                    // DSCR specific fields - show only for DSCR
                    const dscrFields = [
                        '#occupancy_type_field', '#monthly_market_rent_field', '#annual_tax_field',
                        '#annual_insurance_field', '#annual_hoa_field', '#dscr_field',
                        '#purchase_date_field', '#payoff_amount_field', '#lender_points_field',
                        '#pre_pay_penalty_field'
                    ];
                    dscrFields.forEach(fieldId => {
                        const field = document.querySelector(fieldId);
                        console.log('Processing field:', fieldId, 'found:', !!field); // Debug log
                        if (field) {
                            if (isDscrLoan) {
                                field.classList.remove('hidden');
                                console.log('Showing field:', fieldId); // Debug log
                                // Add required attribute for DSCR required fields
                                const input = field.querySelector('input, select');
                                if (input && [
                                    '#occupancy_type_field', '#monthly_market_rent_field', '#annual_tax_field',
                                    '#annual_insurance_field', '#annual_hoa_field', '#dscr_field',
                                    '#payoff_amount_field', '#lender_points_field', '#pre_pay_penalty_field'
                                ].includes(fieldId)) {
                                    input.setAttribute('required', 'required');
                                }
                                
                                // Reinitialize Select2 for select elements that were just shown
                                const select = field.querySelector('select');
                                if (select && select.classList.contains('select2')) {
                                    // Destroy existing Select2 instance if it exists
                                    if ($(select).hasClass('select2-hidden-accessible')) {
                                        $(select).select2('destroy');
                                    }
                                    // Reinitialize Select2
                                    $(select).select2();
                                }
                            } else {
                                field.classList.add('hidden');
                                console.log('Hiding field:', fieldId); // Debug log
                                // Remove required attribute from hidden fields
                                const input = field.querySelector('input, select');
                                if (input) input.removeAttribute('required');
                            }
                        }
                    });
                }
                
                // Function to load DSCR-specific data (property types, states, occupancy types, prepay penalties)
                async function loadDscrLoanData() {
                    console.log('loadDscrLoanData called'); // Debug log
                    try {
                        // Load DSCR property types
                        console.log('Loading DSCR property types...'); // Debug log
                        const propertyResponse = await fetch('/api/dscr-property-types');
                        if (propertyResponse.ok) {
                            const propertyData = await propertyResponse.json();
                            console.log('Property types API response:', propertyData); // Debug log
                            if (propertyData.success) {
                                const propertyTypeSelect = $('#property_type');
                                propertyTypeSelect.empty().append('<option value="">-- Select Property Type --</option>');
                                propertyData.data.forEach(propertyType => {
                                    propertyTypeSelect.append(`<option value="${propertyType.name}">${propertyType.name}</option>`);
                                });
                                propertyTypeSelect.trigger('change');
                                console.log('Property types loaded successfully from database'); // Debug log
                            }
                        } else {
                            console.error('Failed to load property types from database, status:', propertyResponse.status);
                            // Add default DSCR-eligible property types
                            console.log('Adding default DSCR property types');
                            const propertyTypeSelect = $('#property_type');
                            propertyTypeSelect.empty().append('<option value="">-- Select Property Type --</option>');
                            propertyTypeSelect.append('<option value="Single Family">Single Family</option>');
                            propertyTypeSelect.append('<option value="Townhome">Townhome</option>');
                            propertyTypeSelect.append('<option value="Condo">Condo</option>');
                            propertyTypeSelect.trigger('change');
                        }

                        // Load states for DSCR loans
                        console.log('Loading DSCR states...'); // Debug log
                        const stateResponse = await fetch('/api/dscr-states');
                        if (stateResponse.ok) {
                            const stateData = await stateResponse.json();
                            if (stateData.success) {
                                const stateSelect = $('#state');
                                stateSelect.empty().append('<option value="">-- Select State --</option>');
                                stateData.data.forEach(state => {
                                    stateSelect.append(`<option value="${state.code}">${state.code}</option>`);
                                });
                                stateSelect.trigger('change');
                                console.log('States loaded successfully'); // Debug log
                            }
                        } else {
                            console.error('Failed to load states, status:', stateResponse.status);
                            // Fallback to regular states API
                            console.log('Falling back to regular states API');
                            try {
                                const fallbackResponse = await fetch('/api/loan-type-options?loan_type=DSCR%20Rental%20Loans');
                                if (fallbackResponse.ok) {
                                    const fallbackData = await fallbackResponse.json();
                                    console.log('Fallback states data:', fallbackData);
                                    if (fallbackData.success && fallbackData.data.states) {
                                        const stateSelect = $('#state');
                                        stateSelect.empty().append('<option value="">-- Select State --</option>');
                                        fallbackData.data.states.forEach(state => {
                                            stateSelect.append(`<option value="${state.code}">${state.code}</option>`);
                                        });
                                        stateSelect.trigger('change');
                                        console.log('Fallback states loaded successfully');
                                    }
                                } else {
                                    console.log('Fallback API also failed, adding default states');
                                    const stateSelect = $('#state');
                                    stateSelect.empty().append('<option value="">-- Select State --</option>');
                                    stateSelect.append('<option value="CA">CA</option>');
                                    stateSelect.append('<option value="TX">TX</option>');
                                    stateSelect.append('<option value="FL">FL</option>');
                                    stateSelect.append('<option value="NY">NY</option>');
                                    stateSelect.trigger('change');
                                }
                            } catch (fallbackError) {
                                console.error('Fallback API error:', fallbackError);
                                console.log('Adding default states due to fallback error');
                                const stateSelect = $('#state');
                                stateSelect.empty().append('<option value="">-- Select State --</option>');
                                stateSelect.append('<option value="CA">CA</option>');
                                stateSelect.append('<option value="TX">TX</option>');
                                stateSelect.append('<option value="FL">FL</option>');
                                stateSelect.append('<option value="NY">NY</option>');
                                stateSelect.trigger('change');
                            }
                        }
                        
                        // Load occupancy types
                        console.log('Loading occupancy types...'); // Debug log
                        await loadOccupancyTypes();
                        
                        // Load prepay penalty options from prepay_periods table
                        console.log('Loading prepay penalties...'); // Debug log
                        await loadDscrPrepayPenalties();
                        
                    } catch (error) {
                        console.error('Error loading DSCR loan data:', error);
                    }
                }
                
                // Function to load occupancy types for DSCR loans
                async function loadOccupancyTypes() {
                    console.log('loadOccupancyTypes called'); // Debug log
                    try {
                        const response = await fetch('/api/occupancy-types');
                        console.log('Occupancy types API response status:', response.status); // Debug log
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Occupancy types data:', data); // Debug log
                            if (data.success) {
                                const occupancySelect = $('#occupancy_type');
                                console.log('Occupancy select element found:', occupancySelect.length > 0); // Debug log
                                occupancySelect.empty().append('<option value="">-- Select Occupancy Type --</option>');
                                data.data.forEach(occupancy => {
                                    occupancySelect.append(`<option value="${occupancy.name}">${occupancy.name}</option>`);
                                });
                                occupancySelect.trigger('change');
                                console.log('Occupancy types loaded successfully, total options:', data.data.length); // Debug log
                            }
                        } else {
                            console.error('Failed to load occupancy types, status:', response.status);
                            // Add some default options for testing
                            console.log('Adding default occupancy options for testing');
                            const occupancySelect = $('#occupancy_type');
                            occupancySelect.empty().append('<option value="">-- Select Occupancy Type --</option>');
                            occupancySelect.append('<option value="Owner Occupied">Owner Occupied</option>');
                            occupancySelect.append('<option value="Investment Property">Investment Property</option>');
                            occupancySelect.append('<option value="Second Home">Second Home</option>');
                            occupancySelect.trigger('change');
                        }
                    } catch (error) {
                        console.error('Error loading occupancy types:', error);
                        // Add some default options for testing
                        console.log('Adding default occupancy options due to error');
                        const occupancySelect = $('#occupancy_type');
                        occupancySelect.empty().append('<option value="">-- Select Occupancy Type --</option>');
                        occupancySelect.append('<option value="Owner Occupied">Owner Occupied</option>');
                        occupancySelect.append('<option value="Investment Property">Investment Property</option>');
                        occupancySelect.append('<option value="Second Home">Second Home</option>');
                        occupancySelect.trigger('change');
                    }
                }
                
                // Function to load prepay penalty options from prepay_periods table
                async function loadDscrPrepayPenalties() {
                    console.log('loadDscrPrepayPenalties called'); // Debug log
                    try {
                        const response = await fetch('/api/prepay-periods');
                        console.log('Prepay penalties API response status:', response.status); // Debug log
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Prepay penalties data:', data); // Debug log
                            if (data.success) {
                                const prepaySelect = $('#pre_pay_penalty');
                                console.log('Prepay select element found:', prepaySelect.length > 0); // Debug log
                                prepaySelect.empty().append('<option value="">-- Select Pre Pay Penalty --</option>');
                                data.data.forEach(prepay => {
                                    // Use 'name' field from API response (which maps to prepay_name from database)
                                    prepaySelect.append(`<option value="${prepay.name}">${prepay.name}</option>`);
                                });
                                prepaySelect.trigger('change');
                                console.log('Prepay penalties loaded successfully, total options:', data.data.length); // Debug log
                            }
                        } else {
                            console.error('Failed to load prepay penalties, status:', response.status);
                            // Add some default options for testing
                            console.log('Adding default prepay penalty options for testing');
                            const prepaySelect = $('#pre_pay_penalty');
                            prepaySelect.empty().append('<option value="">-- Select Pre Pay Penalty --</option>');
                            prepaySelect.append('<option value="None">None</option>');
                            prepaySelect.append('<option value="1 Year">1 Year</option>');
                            prepaySelect.append('<option value="2 Year">2 Year</option>');
                            prepaySelect.append('<option value="3 Year">3 Year</option>');
                            prepaySelect.append('<option value="5 Year">5 Year</option>');
                            prepaySelect.trigger('change');
                        }
                    } catch (error) {
                        console.error('Error loading prepay penalties:', error);
                        // Add some default options for testing
                        console.log('Adding default prepay penalty options due to error');
                        const prepaySelect = $('#pre_pay_penalty');
                        prepaySelect.empty().append('<option value="">-- Select Pre Pay Penalty --</option>');
                        prepaySelect.append('<option value="None">None</option>');
                        prepaySelect.append('<option value="1 Year">1 Year</option>');
                        prepaySelect.append('<option value="2 Year">2 Year</option>');
                        prepaySelect.append('<option value="3 Year">3 Year</option>');
                        prepaySelect.append('<option value="5 Year">5 Year</option>');
                        prepaySelect.trigger('change');
                    }
                }
            });

            // Function to handle loan amount changes in the cards
            window.handleCardLoanAmountChange = function(input, programName) {
                const newAmount = parseFloat(input.value) || 0;
                const maxAmount = parseFloat(input.dataset.maxAmount) || 0;
                
                // Validate amount doesn't exceed maximum
                if (newAmount > maxAmount) {
                    input.value = maxAmount;
                    alert(`Loan amount cannot exceed the maximum qualified amount of $${numberWithCommas(maxAmount)}`);
                    return;
                }
                
                // Update the loan data and recalculate
                updateLoanCalculations(programName, newAmount);
            };

            // Function to update loan calculations with new purchase loan amount
            window.updateLoanCalculations = async function(programName, newPurchaseLoanAmount, rowIndex = null) {
                try {
                    // Get the original loan data for this program
                    const originalLoanData = window.allLoansData[programName][0];
                    if (!originalLoanData) {
                        console.error('Original loan data not found for program:', programName);
                        return;
                    }
                    
                    // Calculate new total loan amount
                    const rehabLoanAmount = originalLoanData.loan_type_and_loan_program_table.rehab_loan_up_to || 0;
                    const newTotalLoanAmount = newPurchaseLoanAmount + rehabLoanAmount;
                    
                    // Update the total loan display in table
                    if (rowIndex !== null) {
                        const totalLoanElement = document.getElementById(`total_loan_${rowIndex}`);
                        if (totalLoanElement) {
                            totalLoanElement.textContent = `$${numberWithCommas(newTotalLoanAmount)}`;
                        }
                    }
                    
                    // Update the total loan display in card
                    const cardTotalElement = document.getElementById(`card_total_loan_${programName.replace(/\s+/g, '_')}`);
                    if (cardTotalElement) {
                        cardTotalElement.textContent = `$${numberWithCommas(newTotalLoanAmount)}`;
                    }
                    
                    // Calculate new lender fees based on the new total loan amount
                    const newLenderFees = calculateLenderFees(newTotalLoanAmount, originalLoanData);
                    
                    // Update the loan data with new values
                    const updatedLoanData = JSON.parse(JSON.stringify(originalLoanData)); // Deep copy
                    updatedLoanData.loan_type_and_loan_program_table.purchase_loan_up_to = newPurchaseLoanAmount;
                    updatedLoanData.loan_type_and_loan_program_table.total_loan_up_to = newTotalLoanAmount;
                    
                    // Update closing statement with new fees
                    if (updatedLoanData.estimated_closing_statement) {
                        updatedLoanData.estimated_closing_statement.loan_amount_section.purchase_loan_amount = newPurchaseLoanAmount;
                        updatedLoanData.estimated_closing_statement.loan_amount_section.total_loan_amount = newTotalLoanAmount;
                        updatedLoanData.estimated_closing_statement.lender_related_charges = newLenderFees;
                        
                        // Recalculate subtotal closing costs
                        const lenderCharges = newLenderFees;
                        const titleCharges = updatedLoanData.estimated_closing_statement.title_other_charges;
                        const newSubtotal = (lenderCharges.lender_origination_fee || 0) + 
                                          (lenderCharges.broker_fee || 0) + 
                                          (lenderCharges.underwriting_processing_fee || 0) + 
                                          (lenderCharges.interest_reserves || 0) +
                                          (titleCharges.title_charges || 0) + 
                                          (titleCharges.property_insurance || 0) + 
                                          (titleCharges.legal_doc_prep_fee || 0);
                        
                        updatedLoanData.estimated_closing_statement.title_other_charges.subtotal_closing_costs = newSubtotal;
                        updatedLoanData.estimated_closing_statement.cash_due_to_buyer = newSubtotal;
                    }
                    
                    // Update the global loan data
                    window.allLoansData[programName][0] = updatedLoanData;
                    
                    // If this program is currently selected, update the closing statement
                    const closingSection = document.getElementById('closingStatementSection');
                    if (!closingSection.classList.contains('hidden')) {
                        populateClosingStatement(updatedLoanData.estimated_closing_statement);
                    }
                    
                } catch (error) {
                    console.error('Error updating loan calculations:', error);
                }
            };
            
            // Function to calculate lender fees based on loan amount
            window.calculateLenderFees = function(totalLoanAmount, originalLoanData) {
                const userInputs = originalLoanData.user_inputs;
                const originalFees = originalLoanData.estimated_closing_statement.lender_related_charges;
                
                // Get rates from the loan data (these are percentages)
                const lenderPointsRate = originalLoanData.loan_type_and_loan_program_table.lender_points; // e.g., 2.5%
                const brokerPointsRate = parseFloat(userInputs.broker_points); // e.g., 1%
                
                // Calculate new fees based on the new total loan amount
                const newOriginationFee = (totalLoanAmount * lenderPointsRate) / 100;
                const newBrokerFee = (totalLoanAmount * brokerPointsRate) / 100;
                
                return {
                    lender_origination_fee: newOriginationFee,
                    broker_fee: newBrokerFee,
                    underwriting_processing_fee: originalFees.underwriting_processing_fee, // This usually stays constant
                    interest_reserves: originalFees.interest_reserves // This might change but for now keep original
                };
            };

            // PDF Generation Function
            window.downloadLoanPDF = function() {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                
                // PDF Header
                pdf.setFontSize(16);
                pdf.setFont(undefined, 'bold');
                pdf.text('LOAN CALCULATOR REPORT', 105, 15, { align: 'center' });
                
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'normal');
                pdf.text('Loan Status: Not Submitted', 105, 25, { align: 'center' });
                
                pdf.setFontSize(10);
                pdf.text('Generated on: ' + new Date().toLocaleDateString(), 105, 32, { align: 'center' });
                pdf.text('Structure My Loan Calculator', 105, 38, { align: 'center' });
                
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'bold');
                pdf.text('Speak to a Loan Officer (631) 602-0460', 105, 48, { align: 'center' });
                
                let yPos = 60;
                const margin = 10;
                const pageWidth = 190; // Use full width
                
                // Get form data
                const formData = getFormDataForPDF();
                
                // Section 1: User Inputs
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'bold');
                pdf.text('USER INPUTS', margin, yPos);
                yPos += 8;
                
                pdf.setFontSize(9);
                pdf.setFont(undefined, 'normal');
                
                // Create user inputs table
                const inputsTable = createUserInputsTable(formData);
                yPos = drawTable(pdf, inputsTable.headers, inputsTable.data, yPos, margin, pageWidth);
                
                yPos += 10;
                
                // Section 2: All Loan Programs with Selected Column
                if (window.allLoansData && Object.keys(window.allLoansData).length > 0) {
                    pdf.setFontSize(12);
                    pdf.setFont(undefined, 'bold');
                    pdf.text('LOAN PROGRAMS', margin, yPos);
                    yPos += 8;
                    
                    const loanTable = createAllLoanProgramsTable();
                    yPos = drawTable(pdf, loanTable.headers, loanTable.data, yPos, margin, pageWidth);
                    
                    yPos += 10;
                    
                    // Section 3: Estimated Closing Statement
                    if (yPos > 200) {
                        pdf.addPage();
                        yPos = 20;
                    }
                    
                    if (window.selectedLoanProgram) {
                        pdf.setFontSize(12);
                        pdf.setFont(undefined, 'bold');
                        pdf.text('ESTIMATED CLOSING STATEMENT', margin, yPos);
                        yPos += 8;
                        
                        const closingTable = createClosingStatementTable();
                        yPos = drawTable(pdf, closingTable.headers, closingTable.data, yPos, margin, pageWidth);
                    }
                }
                
                // Save PDF
                const timestamp = new Date().toISOString().split('T')[0];
                const filename = `loan-calculator-report-${timestamp}.pdf`;
                pdf.save(filename);
            };
            
            // Helper function to get form data
            function getFormDataForPDF() {
                const form = document.getElementById('loanCalculatorForm');
                const formData = new FormData(form);
                const data = {};
                
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                return data;
            }
            
            // Helper function to create user inputs table
            function createUserInputsTable(formData) {
                const headers = ['Field', 'Value'];
                const data = [];
                
                const fieldLabels = {
                    'credit_score': 'Credit Score',
                    'experience': 'Experience',
                    'loan_type': 'Loan Type',
                    'transaction_type': 'Transaction Type',
                    'loan_term': 'Loan Term',
                    'purchase_price': 'Purchase Price',
                    'broker_points': 'Broker Points',
                    'state': 'State',
                    'property_type': 'Property Type',
                    'occupancy_type': 'Occupancy Type',
                    'monthly_market_rent': 'Monthly Market Rent',
                    'annual_tax': 'Annual Tax',
                    'annual_insurance': 'Annual Insurance',
                    'annual_hoa': 'Annual HOA',
                    'dscr': 'DSCR',
                    'arv': 'ARV',
                    'rehab_budget': 'Rehab Budget',
                    'payoff_amount': 'Payoff Amount',
                    'lender_points': 'Lender Points',
                    'pre_pay_penalty': 'Pre Pay Penalty',
                    'purchase_date': 'Purchase Date'
                };
                
                Object.keys(fieldLabels).forEach(key => {
                    if (formData[key] && formData[key] !== '') {
                        let value = formData[key];
                        
                        // Format monetary values
                        if (['purchase_price', 'arv', 'rehab_budget', 'monthly_market_rent', 'annual_tax', 'annual_insurance', 'annual_hoa', 'payoff_amount'].includes(key)) {
                            value = '$' + numberWithCommas(value);
                        }
                        
                        // Format percentage values
                        if (['broker_points', 'lender_points', 'dscr'].includes(key)) {
                            value = value + '%';
                        }
                        
                        // Format loan term
                        if (key === 'loan_term') {
                            value = value + ' months';
                        }
                        
                        data.push([fieldLabels[key], value]);
                    }
                });
                
                return { headers, data };
            }
            
            // Helper function to create selected loan table
            function createSelectedLoanTable() {
                const headers = ['Program', 'Term', 'Rate', 'Points', 'Max LTV', 'Purchase Loan', 'Rehab Loan', 'Total Loan'];
                const data = [];
                
                if (window.selectedLoanProgram && window.allLoansData && window.allLoansData[window.selectedLoanProgram]) {
                    const loan = window.allLoansData[window.selectedLoanProgram][0];
                    const loanData = loan.loan_type_and_loan_program_table || loan.loan_program_values;
                    
                    if (loanData) {
                        data.push([
                            window.selectedLoanProgram,
                            (loanData.loan_term || loanData.term || 'N/A') + ' months',
                            (loanData.interest_rate || loanData.rate || 'N/A') + '%',
                            (loanData.lender_points || loanData.points || 'N/A') + '%',
                            (loanData.max_ltv || 'N/A') + '%',
                            '$' + numberWithCommas(loanData.purchase_loan_up_to || 0),
                            '$' + numberWithCommas(loanData.rehab_loan_up_to || 0),
                            '$' + numberWithCommas(loanData.total_loan_up_to || (loanData.purchase_loan_up_to + loanData.rehab_loan_up_to) || 0)
                        ]);
                    }
                }
                
                return { headers, data };
            }
            
            // Helper function to create all loan programs table with selected column
            function createAllLoanProgramsTable() {
                const headers = ['Program', 'Term', 'Rate', 'Points', 'Max LTV', 'Purchase Loan', 'Rehab Loan', 'Total Loan', 'Selected'];
                const data = [];
                
                if (window.allLoansData && Object.keys(window.allLoansData).length > 0) {
                    Object.keys(window.allLoansData).forEach(programName => {
                        const loan = window.allLoansData[programName][0];
                        const loanData = loan.loan_type_and_loan_program_table || loan.loan_program_values;
                        
                        if (loanData) {
                            const isSelected = window.selectedLoanProgram === programName;
                            
                            data.push([
                                programName,
                                (loanData.loan_term || loanData.term || 'N/A') + ' months',
                                (loanData.interest_rate || loanData.rate || 'N/A') + '%',
                                (loanData.lender_points || loanData.points || 'N/A') + '%',
                                (loanData.max_ltv || 'N/A') + '%',
                                '$' + numberWithCommas(loanData.purchase_loan_up_to || 0),
                                '$' + numberWithCommas(loanData.rehab_loan_up_to || 0),
                                '$' + numberWithCommas(loanData.total_loan_up_to || (loanData.purchase_loan_up_to + loanData.rehab_loan_up_to) || 0),
                                isSelected ? 'YES' : 'NO'
                            ]);
                        }
                    });
                }
                
                return { headers, data };
            }
            
            // Helper function to create closing statement table
            function createClosingStatementTable() {
                const headers = ['Description', 'Amount'];
                const data = [];
                
                if (window.selectedLoanProgram && window.allLoansData && window.allLoansData[window.selectedLoanProgram]) {
                    const loan = window.allLoansData[window.selectedLoanProgram][0];
                    const closingData = loan.estimated_closing_statement;
                    
                    if (closingData) {
                        // Loan Amount Section
                        data.push(['LOAN AMOUNT SECTION', '']);
                        if (closingData.loan_amount_section) {
                            data.push(['Purchase Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.purchase_loan_amount || 0)]);
                            data.push(['Rehab Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.rehab_loan_amount || 0)]);
                            data.push(['Total Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.total_loan_amount || 0)]);
                        }
                        
                        data.push(['', '']); // Empty row
                        
                        // Buyer Related Charges
                        data.push(['BUYER RELATED CHARGES', '']);
                        if (closingData.buyer_related_charges) {
                            data.push(['Purchase Price', '$' + numberWithCommas(closingData.buyer_related_charges.purchase_price || 0)]);
                            data.push(['Rehab Budget', '$' + numberWithCommas(closingData.buyer_related_charges.rehab_budget || 0)]);
                            data.push(['Sub Total Buyer Charges', '$' + numberWithCommas(closingData.buyer_related_charges.sub_total_buyer_charges || 0)]);
                        }
                        
                        data.push(['', '']); // Empty row
                        
                        // Lender Related Charges
                        data.push(['LENDER RELATED CHARGES', '']);
                        if (closingData.lender_related_charges) {
                            data.push(['Lender Origination Fee', '$' + numberWithCommas(closingData.lender_related_charges.lender_origination_fee || 0)]);
                            data.push(['Broker Fee', '$' + numberWithCommas(closingData.lender_related_charges.broker_fee || 0)]);
                            data.push(['Underwriting Processing Fee', '$' + numberWithCommas(closingData.lender_related_charges.underwriting_processing_fee || 0)]);
                            data.push(['Interest Reserves', '$' + numberWithCommas(closingData.lender_related_charges.interest_reserves || 0)]);
                        }
                        
                        data.push(['', '']); // Empty row
                        
                        // Title & Other Charges
                        data.push(['TITLE & OTHER CHARGES', '']);
                        if (closingData.title_other_charges) {
                            data.push(['Title Charges', '$' + numberWithCommas(closingData.title_other_charges.title_charges || 0)]);
                            data.push(['Property Insurance', '$' + numberWithCommas(closingData.title_other_charges.property_insurance || 0)]);
                            data.push(['Legal Doc Prep Fee', '$' + numberWithCommas(closingData.title_other_charges.legal_doc_prep_fee || 0)]);
                            data.push(['Subtotal Closing Costs', '$' + numberWithCommas(closingData.title_other_charges.subtotal_closing_costs || 0)]);
                        }
                        
                        data.push(['', '']); // Empty row
                        data.push(['CASH DUE TO BUYER', '$' + numberWithCommas(closingData.cash_due_to_buyer || 0)]);
                    }
                }
                
                return { headers, data };
            }
            
            // Helper function to draw tables in PDF
            function drawTable(pdf, headers, data, startY, margin, pageWidth) {
                const colWidth = (pageWidth - (margin * 2)) / headers.length;
                let yPos = startY;
                const pageHeight = 280;
                
                // Draw headers
                pdf.setFontSize(8);
                pdf.setFont(undefined, 'bold');
                
                let xPos = margin;
                headers.forEach((header, index) => {
                    pdf.rect(xPos, yPos, colWidth, 6);
                    pdf.text(header, xPos + 2, yPos + 4);
                    xPos += colWidth;
                });
                
                yPos += 6;
                pdf.setFont(undefined, 'normal');
                
                // Draw data rows
                data.forEach((row, rowIndex) => {
                    // Check for new page
                    if (yPos > pageHeight - 20) {
                        pdf.addPage();
                        yPos = 20;
                        
                        // Re-add headers
                        pdf.setFont(undefined, 'bold');
                        xPos = margin;
                        headers.forEach((header, index) => {
                            pdf.rect(xPos, yPos, colWidth, 6);
                            pdf.text(header, xPos + 2, yPos + 4);
                            xPos += colWidth;
                        });
                        yPos += 6;
                        pdf.setFont(undefined, 'normal');
                    }
                    
                    // Check if this row has a "Selected" column with "YES"
                    const isSelectedRow = row[row.length - 1] === 'YES' && headers[headers.length - 1] === 'Selected';
                    
                    xPos = margin;
                    row.forEach((cell, cellIndex) => {
                        let text = String(cell || '');
                        
                        // Truncate long text
                        const maxLength = Math.floor(colWidth / 1.5);
                        if (text.length > maxLength) {
                            text = text.substring(0, maxLength - 3) + '...';
                        }
                        
                        // Style section headers differently
                        if (cellIndex === 0 && (text.includes('SECTION') || text.includes('CHARGES') || text.includes('CASH DUE'))) {
                            pdf.setFont(undefined, 'bold');
                        }
                        
                        // Highlight selected row
                        if (isSelectedRow) {
                            pdf.setFillColor(220, 220, 220); // Light gray background
                            pdf.rect(xPos, yPos, colWidth, 6, 'F');
                        }
                        
                        // Draw cell border
                        pdf.rect(xPos, yPos, colWidth, 6);
                        
                        // Special styling for "Selected" column
                        if (cellIndex === row.length - 1 && headers[headers.length - 1] === 'Selected') {
                            if (text === 'YES') {
                                pdf.setFont(undefined, 'bold');
                                pdf.setTextColor(0, 128, 0); // Green color for YES
                            } else {
                                pdf.setTextColor(128, 128, 128); // Gray color for NO
                            }
                        }
                        
                        pdf.text(text, xPos + 2, yPos + 4);
                        
                        // Reset font and color
                        if (cellIndex === 0 && (text.includes('SECTION') || text.includes('CHARGES') || text.includes('CASH DUE'))) {
                            pdf.setFont(undefined, 'normal');
                        }
                        if (cellIndex === row.length - 1 && headers[headers.length - 1] === 'Selected') {
                            pdf.setFont(undefined, 'normal');
                            pdf.setTextColor(0, 0, 0); // Reset to black
                        }
                        
                        xPos += colWidth;
                    });
                    
                    yPos += 6;
                });
                
                return yPos;
            }
            
            // Utility function for number formatting
            function numberWithCommas(x) {
                if (x === null || x === undefined || isNaN(x)) return '0';
                return parseFloat(x).toLocaleString('en-US');
            }

            // Store selected loan program globally
            window.selectedLoanProgram = null;

            // Start Application Function
            window.startApplication = async function() {
                if (!window.selectedLoanProgram || !window.allLoansData) {
                    alert('Please select a loan program first by clicking "Check Summary for This Program"');
                    return;
                }

                // Show loading state
                const button = document.getElementById('startApplicationBtn');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                button.disabled = true;

                try {
                    // Get form data
                    const formData = getFormDataForPDF();
                    
                    // Get selected loan program data
                    const selectedLoanData = window.allLoansData[window.selectedLoanProgram][0];
                    
                    // Prepare loan programs array with selection status
                    const loanPrograms = [];
                    Object.keys(window.allLoansData).forEach(programName => {
                        const loanData = window.allLoansData[programName][0];
                        const loanTypeData = loanData.loan_type_and_loan_program_table || loanData.loan_program_values;
                        
                        loanPrograms.push({
                            loan_type: loanData.loan_type,
                            loan_program: programName,
                            loan_term: loanTypeData?.loan_term || formData.loan_term,
                            interest_rate: loanTypeData?.interest_rate || loanTypeData?.rate,
                            lender_points: loanTypeData?.lender_points || loanTypeData?.points,
                            max_ltv: loanTypeData?.max_ltv,
                            max_ltc: loanTypeData?.max_ltc,
                            max_ltfc: loanTypeData?.max_ltfc,
                            purchase_loan_up_to: loanTypeData?.purchase_loan_up_to || 0,
                            rehab_loan_up_to: loanTypeData?.rehab_loan_up_to || 0,
                            total_loan_up_to: loanTypeData?.total_loan_up_to || 0,
                            rehab_category: loanTypeData?.rehab_category,
                            rehab_percentage: loanTypeData?.percentage_max_ltv_max_ltc,
                            pricing_tier: loanTypeData?.pricing_tier,
                            is_selected: programName === window.selectedLoanProgram
                        });
                    });

                    // Get calculated values from the selected program
                    const closingStatement = selectedLoanData.estimated_closing_statement;
                    const calculatedValues = {
                        purchase_loan_amount: closingStatement?.loan_amount_section?.purchase_loan_amount || 0,
                        rehab_loan_amount: closingStatement?.loan_amount_section?.rehab_loan_amount || 0,
                        total_loan_amount: closingStatement?.loan_amount_section?.total_loan_amount || 0,
                        lender_origination_fee: closingStatement?.lender_related_charges?.lender_origination_fee || 0,
                        broker_fee: closingStatement?.lender_related_charges?.broker_fee || 0,
                        underwriting_processing_fee: closingStatement?.lender_related_charges?.underwriting_processing_fee || 0,
                        interest_reserves: closingStatement?.lender_related_charges?.interest_reserves || 0,
                        title_charges: closingStatement?.title_other_charges?.title_charges || 0,
                        legal_doc_prep_fee: closingStatement?.title_other_charges?.legal_doc_prep_fee || 0,
                        subtotal_closing_costs: closingStatement?.title_other_charges?.subtotal_closing_costs || 0,
                        cash_due_to_buyer: closingStatement?.cash_due_to_buyer || 0
                    };

                    // Show user input modal
                    showUserInputModal(formData, loanPrograms, calculatedValues, selectedLoanData);

                } catch (error) {
                    console.error('Error starting application:', error);
                    alert('An error occurred while starting the application. Please try again.');
                } finally {
                    // Reset button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            };

            // Show user input modal for collecting borrower information
            function showUserInputModal(formData, loanPrograms, calculatedValues, selectedLoanData) {
                // Create modal HTML
                const modalHTML = `
                    <div id="userInputModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Complete Your Loan Application</h3>
                                <form id="borrowerInfoForm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                            <input type="text" id="firstName" name="first_name" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                            <input type="text" id="lastName" name="last_name" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                            <input type="email" id="email" name="email" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                            <input type="tel" id="phone" name="phone" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                        <h4 class="font-semibold text-gray-800 mb-2">Selected Loan Program</h4>
                                        <p class="text-sm text-gray-600">${window.selectedLoanProgram}</p>
                                        <p class="text-sm text-gray-600">Loan Amount: $${numberWithCommas(calculatedValues.total_loan_amount)}</p>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="closeUserInputModal()" 
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            Submit Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `;

                // Add modal to DOM
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                // Handle form submission
                document.getElementById('borrowerInfoForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await submitApplicationData(formData, loanPrograms, calculatedValues, selectedLoanData);
                });
            }

            // Close user input modal
            window.closeUserInputModal = function() {
                const modal = document.getElementById('userInputModal');
                if (modal) {
                    modal.remove();
                }
            };

            // Submit application data to backend
            async function submitApplicationData(formData, loanPrograms, calculatedValues, selectedLoanData) {
                const borrowerForm = document.getElementById('borrowerInfoForm');
                const borrowerData = new FormData(borrowerForm);
                
                const submitButton = borrowerForm.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
                submitButton.disabled = true;

                try {
                    // Prepare the payload
                    const payload = {
                        // Borrower information
                        first_name: borrowerData.get('first_name'),
                        last_name: borrowerData.get('last_name'),
                        email: borrowerData.get('email'),
                        phone: borrowerData.get('phone'),
                        
                        // Calculator inputs
                        credit_score: parseInt(formData.credit_score),
                        experience: parseInt(formData.experience),
                        loan_type: formData.loan_type,
                        transaction_type: formData.transaction_type,
                        loan_term: parseInt(formData.loan_term),
                        purchase_price: parseFloat(formData.purchase_price),
                        arv: parseFloat(formData.arv),
                        rehab_budget: parseFloat(formData.rehab_budget),
                        broker_points: parseFloat(formData.broker_points),
                        state: formData.state,
                        
                        // Optional fields
                        payoff_amount: formData.payoff_amount ? parseFloat(formData.payoff_amount) : null,
                        lender_points: formData.lender_points ? parseFloat(formData.lender_points) : null,
                        pre_pay_penalty: formData.pre_pay_penalty || null,
                        occupancy_type: formData.occupancy_type || null,
                        monthly_market_rent: formData.monthly_market_rent ? parseFloat(formData.monthly_market_rent) : null,
                        annual_tax: formData.annual_tax ? parseFloat(formData.annual_tax) : null,
                        annual_insurance: formData.annual_insurance ? parseFloat(formData.annual_insurance) : null,
                        annual_hoa: formData.annual_hoa ? parseFloat(formData.annual_hoa) : null,
                        dscr: formData.dscr ? parseFloat(formData.dscr) : null,
                        purchase_date: formData.purchase_date || null,
                        title_charges: formData.title_charges ? parseFloat(formData.title_charges) : null,
                        property_insurance: formData.property_insurance ? parseFloat(formData.property_insurance) : null,
                        
                        // Selected loan program
                        selected_loan_program: window.selectedLoanProgram,
                        
                        // All loan programs with selection status
                        loan_programs: loanPrograms,
                        
                        // Calculated values
                        calculated_values: calculatedValues,
                        
                        // API data
                        api_url: window.lastApiUrl || 'Unknown',
                        api_response: selectedLoanData
                    };

                    // Submit to backend
                    const response = await fetch('/api/loan-application/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.success) {
                        closeUserInputModal();
                        alert(result.message || 'Application submitted successfully!');
                    } else {
                        alert('Error submitting application: ' + (result.message || 'Unknown error'));
                    }

                } catch (error) {
                    console.error('Error submitting application:', error);
                    alert('An error occurred while submitting your application. Please try again.');
                } finally {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            }

            $('form').submit(function(){
                // If x-button does not render as a traditional submit button, target it directly by ID or class
                $('#submit-btn').attr('disabled', 'disabled');
            });
                        </script>
                        @stack('modals')

                        {{-- Allow views to push inline scripts -- e.g. @push('scripts') -- so they are rendered here
                        --}}
                        @stack('scripts')
                        @livewireScripts
</body>

</html>