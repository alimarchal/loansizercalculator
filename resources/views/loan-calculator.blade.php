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

        /* Default column visibility - hide LTFC by default, show LTC */
        #maxLtfcHeader,
        .ltfc-column {
            display: none !important;
        }

        #maxLtcHeader,
        .ltc-column {
            display: table-cell !important;
        }

        /* Closing Statement Styles */
        #closingStatementSection .bg-blue-600 {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
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
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Loan Calculator</h1>
                        <p class="text-gray-600 mt-2">Calculate loan options based on your requirements</p>
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
                                    type="number" name="credit_score" id="credit_score" min="300" max="850" value=""
                                    placeholder="Enter credit score" required>
                            </div>

                            <!-- Experience -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="experience">
                                    Experience (Years) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="experience" id="experience" min="0" max="50" value=""
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

                            <!-- Purchase Price -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="purchase_price">
                                    Purchase Price <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="purchase_price" id="purchase_price" min="0" value=""
                                    placeholder="Enter purchase price" required>
                            </div>

                            <!-- Rehab Budget -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="rehab_budget">
                                    Rehab Budget <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="rehab_budget" id="rehab_budget" min="0" value=""
                                    placeholder="Enter rehab budget" required>
                            </div>

                            <!-- ARV -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="arv">
                                    ARV (After Repair Value) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    type="number" name="arv" id="arv" min="0" value="" placeholder="Enter ARV amount"
                                    required>
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
                                Reset Form
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

                        <!-- Two Column Layout for Results and Closing Statement (Hidden until calculation) -->
                        <div id="resultsAndClosingSection" class="hidden">
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                                <!-- Left Column: Loan Program Results -->
                                <div id="resultsSection" class="">
                                    <h2 class="text-xl font-bold text-gray-900 mb-4">Loan Program Results</h2>
                                    <!-- Results Table -->
                                    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                                        <table class="w-full border-collapse" style="border: 1px solid #000;">
                                            <thead>
                                                <tr class="bg-gray-50">
                                                    <th style="border: 1px solid #000;"
                                                        class="px-3 py-2 text-left font-semibold text-gray-900 text-sm">
                                                        Program</th>
                                                    <th style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center font-semibold text-gray-900 text-sm">
                                                        Rate</th>
                                                    <th style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center font-semibold text-gray-900 text-sm">
                                                        Points</th>
                                                    <th style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center font-semibold text-gray-900 text-sm">
                                                        Max LTV</th>
                                                    <th id="maxLtcHeader" style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center font-semibold text-gray-900 text-sm">
                                                        Max LTC</th>
                                                    <th id="maxLtfcHeader" style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center font-semibold text-gray-900 text-sm">
                                                        Max LTFC</th>
                                                </tr>
                                            </thead>
                                            <tbody id="compactResultsTable">
                                                <!-- Default rows showing 0.00 values -->
                                                <tr id="fullAppraisalRow">
                                                    <td style="border: 1px solid #000;"
                                                        class="px-3 py-2 font-medium text-blue-700 text-sm">
                                                        <i class="fas fa-file-alt mr-2"></i>Full Appraisal
                                                    </td>
                                                    <td style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="ltc-column px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="ltfc-column px-2 py-2 text-center text-sm">0.00%</td>
                                                </tr>
                                                <tr id="desktopAppraisalRow">
                                                    <td style="border: 1px solid #000;"
                                                        class="px-3 py-2 font-medium text-green-700 text-sm">
                                                        <i class="fas fa-desktop mr-2"></i>Desktop Appraisal
                                                    </td>
                                                    <td style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="ltc-column px-2 py-2 text-center text-sm">0.00%</td>
                                                    <td style="border: 1px solid #000;"
                                                        class="ltfc-column px-2 py-2 text-center text-sm">0.00%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Loan Amount Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Full Appraisal Card -->
                                        <div id="fullAppraisalCard"
                                            class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                                            <div class="flex items-center mb-4">
                                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                </div>
                                                <h3 class="text-lg font-semibold text-blue-700">For Full Appraisal</h3>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-blue-700 font-medium">You qualify for a Purchase Loan up
                                                    to:
                                                    <span id="fullAppraisalPurchase" class="text-blue-600">$0.00</span>
                                                </p>
                                                <p class="text-green-700 font-medium">You qualify for a Rehab Loan up
                                                    to:
                                                    <span id="fullAppraisalRehab" class="text-green-600">$0.00</span>
                                                </p>
                                                <p class="text-purple-700 font-medium">You qualify for Total Loan up to:
                                                    <span id="fullAppraisalTotal" class="text-purple-600">$0.00</span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Desktop Appraisal Card -->
                                        <div id="desktopAppraisalCard"
                                            class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                                            <div class="flex items-center mb-4">
                                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                                    <i class="fas fa-desktop text-green-600"></i>
                                                </div>
                                                <h3 class="text-lg font-semibold text-green-700">For Desktop Appraisal
                                                </h3>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-blue-700 font-medium">You qualify for a Purchase Loan up
                                                    to:
                                                    <span id="desktopAppraisalPurchase"
                                                        class="text-blue-600">$0.00</span>
                                                </p>
                                                <p class="text-green-700 font-medium">You qualify for a Rehab Loan up
                                                    to:
                                                    <span id="desktopAppraisalRehab" class="text-green-600">$0.00</span>
                                                </p>
                                                <p class="text-purple-700 font-medium">You qualify for Total Loan up to:
                                                    <span id="desktopAppraisalTotal"
                                                        class="text-purple-600">$0.00</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End Left Column: Loan Program Results -->

                                <!-- Right Column: Estimated Closing Statement -->
                                <div id="closingStatementSection" class="">
                                    <h2 class="text-xl font-bold text-gray-900 mb-4">Estimated Closing Statement</h2>

                                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                        <!-- Header -->
                                        <div class="bg-blue-600 text-white p-3">
                                            <h3 class="text-lg font-bold text-center">Estimated Closing Statement</h3>
                                        </div>

                                        <!-- Closing Statement Table -->
                                        <div class="overflow-x-auto">
                                            <table class="w-full border-collapse" style="border: 2px solid #000;">
                                                <!-- Loan Amount Section -->
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"
                                                            class="bg-gray-100 px-4 py-2 font-bold text-gray-900"
                                                            style="border: 1px solid #000;">
                                                            Loan Amount
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Purchase
                                                            Loan Amount</td>
                                                        <td id="closingPurchaseLoan"
                                                            class="px-4 py-2 text-right font-semibold text-blue-600"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Rehab
                                                            Loan
                                                            Amount</td>
                                                        <td id="closingRehabLoan"
                                                            class="px-4 py-2 text-right font-semibold text-green-600"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr class="bg-gray-50">
                                                        <td class="px-4 py-2 text-right font-bold"
                                                            style="border: 2px solid #000;">
                                                            Total Loan Amount</td>
                                                        <td id="closingTotalLoan"
                                                            class="px-4 py-2 text-right font-bold text-lg text-purple-600"
                                                            style="border: 2px solid #000;">$0.00</td>
                                                    </tr>
                                                </tbody>

                                                <!-- Buyer Related Charges -->
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"
                                                            class="bg-gray-100 px-4 py-2 font-bold text-gray-900"
                                                            style="border: 1px solid #000;">
                                                            Buyer Related Charges:
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Purchase
                                                            Price</td>
                                                        <td id="closingPurchasePrice" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Rehab
                                                            Budget</td>
                                                        <td id="closingRehabBudget" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr class="bg-gray-50">
                                                        <td class="px-4 py-2 text-right font-semibold"
                                                            style="border: 2px solid #000;">Subtotal Buyer Charges</td>
                                                        <td id="closingSubtotalBuyer"
                                                            class="px-4 py-2 text-right font-semibold"
                                                            style="border: 2px solid #000;">$0.00</td>
                                                    </tr>
                                                </tbody>

                                                <!-- Lender Related Charges -->
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"
                                                            class="bg-gray-100 px-4 py-2 font-bold text-gray-900"
                                                            style="border: 1px solid #000;">
                                                            Lender Related Charges:
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Lender
                                                            Origination Fee</td>
                                                        <td id="closingOriginationFee" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Broker
                                                            Fee
                                                        </td>
                                                        <td id="closingBrokerFee" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Underwriting & Processing Fee</td>
                                                        <td id="closingUnderwritingFee" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Interest
                                                            Reserves</td>
                                                        <td id="closingInterestReserves" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                </tbody>

                                                <!-- Title & Other Charges -->
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"
                                                            class="bg-gray-100 px-4 py-2 font-bold text-gray-900"
                                                            style="border: 1px solid #000;">
                                                            Title & Other Charges:
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Title
                                                            Charges</td>
                                                        <td id="closingTitleCharges" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Property
                                                            Insurance</td>
                                                        <td id="closingPropertyInsurance" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">
                                                            Legal &
                                                            Doc
                                                            Prep Fee</td>
                                                        <td id="closingLegalDocFee" class="px-4 py-2 text-right"
                                                            style="border: 1px solid #000;">$0.00</td>
                                                    </tr>
                                                    <tr class="bg-gray-50">
                                                        <td class="px-4 py-2 text-right font-semibold"
                                                            style="border: 2px solid #000;">Subtotal Closing Costs</td>
                                                        <td id="closingSubtotalCosts"
                                                            class="px-4 py-2 text-right font-semibold"
                                                            style="border: 2px solid #000;">$0.00</td>
                                                    </tr>
                                                </tbody>

                                                <!-- Cash Due to Buyer -->
                                                <tbody>
                                                    <tr class="bg-gray-200">
                                                        <td class="px-4 py-3 text-right font-bold text-lg"
                                                            style="border: 2px solid #000;">Cash Due to Buyer</td>
                                                        <td id="closingCashDue"
                                                            class="px-4 py-3 text-right font-bold text-xl text-red-600"
                                                            style="border: 2px solid #000;">$0.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="p-4 bg-gray-50 border-t">
                                            <div class="flex gap-3 justify-center">
                                                <button type="button" onclick="javascript:;"
                                                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md">
                                                    Download PDF
                                                </button>
                                                <button type="button" onclick="javascript:;"
                                                    class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-md">
                                                    Email Statement
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End Right Column: Estimated Closing Statement -->

                            </div> <!-- End Grid Layout -->
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
                    
                    // Build API URL with only required parameters
                    const apiParams = new URLSearchParams({
                        credit_score: formData.get('credit_score'),
                        experience: formData.get('experience'),
                        loan_type: formData.get('loan_type'),
                        transaction_type: formData.get('transaction_type'),
                        loan_term: formData.get('loan_term'),
                        purchase_price: formData.get('purchase_price'),
                        arv: formData.get('arv'),
                        rehab_budget: formData.get('rehab_budget'),
                        broker_points: formData.get('broker_points'),
                        state: formData.get('state')
                    });
                    
                    const apiUrl = `/api/loan-matrix?${apiParams.toString()}`;
                    
                    // Make API call
                    const response = await fetch(apiUrl);
                    
                    // Check if response is ok (status 200-299)
                    if (!response.ok) {
                        const errorData = await response.json();
                        loadingSpinner.classList.add('hidden');
                        const errorMsg = errorData.message || `HTTP Error: ${response.status}`;
                        showError(errorMsg);
                        resetToDefaults();
                        return;
                    }
                    
                    const data = await response.json();
                    
                    // Hide loading spinner
                    loadingSpinner.classList.add('hidden');
                    
                    if (data.success && data.data && data.data.length > 0) {
                        populateCompactResults(data.data);
                    } else {
                        // Show specific API error message if available, otherwise show generic message
                        const errorMsg = data.message || 'No loan programs found for the given criteria.';
                        showError(errorMsg);
                        resetToDefaults();
                    }
                    
                } catch (error) {
                    loadingSpinner.classList.add('hidden');
                    showError('An error occurred while calculating loan options. Please try again.');
                    resetToDefaults();
                    console.error('API Error:', error);
                }
            });
            
            function setColumnVisibility(isNewConstruction) {
                const ltcHeader = document.getElementById('maxLtcHeader');
                const ltfcHeader = document.getElementById('maxLtfcHeader');
                const ltcColumns = document.querySelectorAll('.ltc-column');
                const ltfcColumns = document.querySelectorAll('.ltfc-column');

                if (isNewConstruction) {
                    // For New Construction - Check if Rehab Budget > Purchase Price
                    const rehabBudget = parseFloat(document.getElementById('rehab_budget').value) || 0;
                    const purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
                    
                    if (rehabBudget > purchasePrice) {
                        // Show only LTFC, hide LTC
                        ltcHeader.style.setProperty('display', 'none', 'important');
                        ltfcHeader.style.setProperty('display', 'table-cell', 'important');
                        ltcColumns.forEach(col => col.style.setProperty('display', 'none', 'important'));
                        ltfcColumns.forEach(col => col.style.setProperty('display', 'table-cell', 'important'));
                    } else {
                        // Show both LTC and LTFC
                        ltcHeader.style.setProperty('display', 'table-cell', 'important');
                        ltfcHeader.style.setProperty('display', 'table-cell', 'important');
                        ltcColumns.forEach(col => col.style.setProperty('display', 'table-cell', 'important'));
                        ltfcColumns.forEach(col => col.style.setProperty('display', 'table-cell', 'important'));
                    }
                } else {
                    // For Fix & Flip - Show only LTV and LTC, hide LTFC
                    ltcHeader.style.setProperty('display', 'table-cell', 'important');
                    ltfcHeader.style.setProperty('display', 'none', 'important');
                    ltcColumns.forEach(col => col.style.setProperty('display', 'table-cell', 'important'));
                    ltfcColumns.forEach(col => col.style.setProperty('display', 'none', 'important'));
                }
            }

            function populateCompactResults(loans) {
                const fullAppraisalRow = document.getElementById('fullAppraisalRow');
                const desktopAppraisalRow = document.getElementById('desktopAppraisalRow');
                
                // Check if this is New Construction loan type
                const isNewConstruction = loans.length > 0 && loans[0].loan_type === 'New Construction';
                
                // Set column visibility based on loan type and business logic
                setColumnVisibility(isNewConstruction);
                
                if (isNewConstruction) {
                    // For New Construction, separate by EXPERIENCED BUILDER and NEW BUILDER
                    const experiencedBuilderLoans = loans.filter(loan => 
                        loan.loan_program === 'EXPERIENCED BUILDER'
                    );
                    const newBuilderLoans = loans.filter(loan => 
                        loan.loan_program === 'NEW BUILDER'
                    );
                    
                    // Update labels and populate with New Construction data
                    populateNewConstructionResults(experiencedBuilderLoans, newBuilderLoans);
                } else {
                    // Separate loans by program type (existing logic)
                    const fullAppraisalLoans = loans.filter(loan => 
                        loan.loan_program === 'FULL APPRAISAL' || 
                        loan.display_name?.includes('FULL APPRAISAL')
                    );
                    const desktopAppraisalLoans = loans.filter(loan => 
                        loan.loan_program === 'DESKTOP APPRAISAL' || 
                        loan.display_name?.includes('DESKTOP APPRAISAL')
                    );
                    
                    // Populate with regular loan data
                    populateRegularResults(fullAppraisalLoans, desktopAppraisalLoans);
                }
                
                // Show and populate the closing statement with data from the first loan
                if (loans.length > 0 && loans[0].estimated_closing_statement) {
                    populateClosingStatement(loans[0].estimated_closing_statement);
                    document.getElementById('resultsAndClosingSection').classList.remove('hidden');
                }
            }
            
            function populateNewConstructionResults(experiencedBuilderLoans, newBuilderLoans) {
                const fullAppraisalRow = document.getElementById('fullAppraisalRow');
                const desktopAppraisalRow = document.getElementById('desktopAppraisalRow');
                
                // Update Experienced Builder row
                if (experiencedBuilderLoans.length > 0) {
                    const loanData = experiencedBuilderLoans[0].loan_type_and_loan_program_table;
                    fullAppraisalRow.innerHTML = `
                        <td style="border: 1px solid #000;" class="px-3 py-2 font-medium text-blue-700 text-sm">
                            <i class="fas fa-hammer mr-2"></i>Experienced Builder
                        </td>
                        <td style="border: 1px solid #000;" class="px-2 py-2 text-center text-sm">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-2 py-2 text-center text-sm">${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-2 py-2 text-center text-sm">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltc-column px-2 py-2 text-center text-sm">${loanData?.max_ltc ? loanData.max_ltc + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltfc-column px-2 py-2 text-center text-sm">${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0.00%'}</td>
                    `;
                    
                    // Update Experienced Builder card
                    document.getElementById('fullAppraisalCard').querySelector('h3').textContent = 'For Experienced Builder';
                    document.getElementById('fullAppraisalCard').querySelector('i').className = 'fas fa-hammer text-blue-600';
                    document.getElementById('fullAppraisalPurchase').textContent = '$' + numberWithCommas(loanData?.purchase_loan_up_to || 0);
                    document.getElementById('fullAppraisalRehab').textContent = '$' + numberWithCommas(loanData?.rehab_loan_up_to || 0);
                    document.getElementById('fullAppraisalTotal').textContent = '$' + numberWithCommas(loanData?.total_loan_up_to || 0);
                }
                
                // Update New Builder row
                if (newBuilderLoans.length > 0) {
                    const loanData = newBuilderLoans[0].loan_type_and_loan_program_table;
                    desktopAppraisalRow.innerHTML = `
                        <td style="border: 1px solid #000;" class="px-3 py-2 font-medium text-green-700 text-sm">
                            <i class="fas fa-tools mr-2"></i>New Builder
                        </td>
                        <td style="border: 1px solid #000;" class="px-2 py-2 text-center text-sm">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-2 py-2 text-center text-sm">${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-2 py-2 text-center text-sm">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltc-column px-4 py-3 text-center">${loanData?.max_ltc ? loanData.max_ltc + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltfc-column px-4 py-3 text-center">${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0.00%'}</td>
                    `;
                    
                    // Update New Builder card
                    document.getElementById('desktopAppraisalCard').querySelector('h3').textContent = 'For New Builder';
                    document.getElementById('desktopAppraisalCard').querySelector('i').className = 'fas fa-tools text-green-600';
                    document.getElementById('desktopAppraisalPurchase').textContent = '$' + numberWithCommas(loanData?.purchase_loan_up_to || 0);
                    document.getElementById('desktopAppraisalRehab').textContent = '$' + numberWithCommas(loanData?.rehab_loan_up_to || 0);
                    document.getElementById('desktopAppraisalTotal').textContent = '$' + numberWithCommas(loanData?.total_loan_up_to || 0);
                }
            }
            
            function populateRegularResults(fullAppraisalLoans, desktopAppraisalLoans) {
                const fullAppraisalRow = document.getElementById('fullAppraisalRow');
                const desktopAppraisalRow = document.getElementById('desktopAppraisalRow');
                
                // Reset labels back to regular loan types
                document.getElementById('fullAppraisalCard').querySelector('h3').textContent = 'For Full Appraisal';
                document.getElementById('fullAppraisalCard').querySelector('i').className = 'fas fa-file-alt text-blue-600';
                document.getElementById('desktopAppraisalCard').querySelector('h3').textContent = 'For Desktop Appraisal';
                document.getElementById('desktopAppraisalCard').querySelector('i').className = 'fas fa-desktop text-green-600';
                
                // Update Full Appraisal table row
                if (fullAppraisalLoans.length > 0) {
                    const loanData = fullAppraisalLoans[0].loan_type_and_loan_program_table;
                    fullAppraisalRow.innerHTML = `
                        <td style="border: 1px solid #000;" class="px-4 py-3 font-medium text-blue-700">
                            <i class="fas fa-file-alt mr-2"></i>Full Appraisal
                        </td>
                        <td style="border: 1px solid #000;" class="px-4 py-3 text-center">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-4 py-3 text-center">${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-4 py-3 text-center">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltc-column px-4 py-3 text-center">${loanData?.max_ltc ? loanData.max_ltc + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltfc-column px-4 py-3 text-center">${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0.00%'}</td>
                    `;
                    
                    // Update Full Appraisal card
                    document.getElementById('fullAppraisalPurchase').textContent = '$' + numberWithCommas(loanData?.purchase_loan_up_to || 0);
                    document.getElementById('fullAppraisalRehab').textContent = '$' + numberWithCommas(loanData?.rehab_loan_up_to || 0);
                    document.getElementById('fullAppraisalTotal').textContent = '$' + numberWithCommas(loanData?.total_loan_up_to || 0);
                }
                
                // Update Desktop Appraisal table row
                if (desktopAppraisalLoans.length > 0) {
                    const loanData = desktopAppraisalLoans[0].loan_type_and_loan_program_table;
                    desktopAppraisalRow.innerHTML = `
                        <td style="border: 1px solid #000;" class="px-4 py-3 font-medium text-green-700">
                            <i class="fas fa-desktop mr-2"></i>Desktop Appraisal
                        </td>
                        <td style="border: 1px solid #000;" class="px-4 py-3 text-center">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-4 py-3 text-center">${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="px-4 py-3 text-center">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltc-column px-4 py-3 text-center">${loanData?.max_ltc ? loanData.max_ltc + '%' : '0.00%'}</td>
                        <td style="border: 1px solid #000;" class="ltfc-column px-4 py-3 text-center">${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0.00%'}</td>
                    `;
                    
                    // Update Desktop Appraisal card
                    document.getElementById('desktopAppraisalPurchase').textContent = '$' + numberWithCommas(loanData?.purchase_loan_up_to || 0);
                    document.getElementById('desktopAppraisalRehab').textContent = '$' + numberWithCommas(loanData?.rehab_loan_up_to || 0);
                    document.getElementById('desktopAppraisalTotal').textContent = '$' + numberWithCommas(loanData?.total_loan_up_to || 0);
                }
            }
            
            function resetToDefaults() {
                const fullAppraisalRow = document.getElementById('fullAppraisalRow');
                const desktopAppraisalRow = document.getElementById('desktopAppraisalRow');
                
                // Reset column visibility to default (Fix & Flip style - show LTC, hide LTFC)
                const ltcHeader = document.getElementById('maxLtcHeader');
                const ltfcHeader = document.getElementById('maxLtfcHeader');
                const ltcColumns = document.querySelectorAll('.ltc-column');
                const ltfcColumns = document.querySelectorAll('.ltfc-column');
                
                // Remove any inline styles to let CSS defaults take over
                ltcHeader.style.removeProperty('display');
                ltfcHeader.style.removeProperty('display');
                ltcColumns.forEach(col => col.style.removeProperty('display'));
                ltfcColumns.forEach(col => col.style.removeProperty('display'));
                
                // Reset labels back to default
                document.getElementById('fullAppraisalCard').querySelector('h3').textContent = 'For Full Appraisal';
                document.getElementById('fullAppraisalCard').querySelector('i').className = 'fas fa-file-alt text-blue-600';
                document.getElementById('desktopAppraisalCard').querySelector('h3').textContent = 'For Desktop Appraisal';
                document.getElementById('desktopAppraisalCard').querySelector('i').className = 'fas fa-desktop text-green-600';
                
                // Reset Full Appraisal table row to defaults
                fullAppraisalRow.innerHTML = `
                    <td style="border: 1px solid #000;" class="px-4 py-3 font-medium text-blue-700">
                        <i class="fas fa-file-alt mr-2"></i>Full Appraisal
                    </td>
                    <td style="border: 1px solid #000;" class="px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="ltc-column px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="ltfc-column px-4 py-3 text-center">0.00%</td>
                `;
                
                // Reset Desktop Appraisal table row to defaults
                desktopAppraisalRow.innerHTML = `
                    <td style="border: 1px solid #000;" class="px-4 py-3 font-medium text-green-700">
                        <i class="fas fa-desktop mr-2"></i>Desktop Appraisal
                    </td>
                    <td style="border: 1px solid #000;" class="px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="ltc-column px-4 py-3 text-center">0.00%</td>
                    <td style="border: 1px solid #000;" class="ltfc-column px-4 py-3 text-center">0.00%</td>
                `;
                
                // Reset card values to defaults
                document.getElementById('fullAppraisalPurchase').textContent = '$0.00';
                document.getElementById('fullAppraisalRehab').textContent = '$0.00';
                document.getElementById('fullAppraisalTotal').textContent = '$0.00';
                document.getElementById('desktopAppraisalPurchase').textContent = '$0.00';
                document.getElementById('desktopAppraisalRehab').textContent = '$0.00';
                document.getElementById('desktopAppraisalTotal').textContent = '$0.00';
                
                // Hide entire results and closing section
                document.getElementById('resultsAndClosingSection').classList.add('hidden');
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
            
            function showError(message) {
                document.getElementById('errorText').textContent = message;
                errorMessage.classList.remove('hidden');
            }
            
            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Tab functionality
        });
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
                    
                    if (!selectedLoanType) {
                        // Reset property type and state if no loan type selected
                        propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        propertyTypeSelect.trigger('change');
                        stateSelect.trigger('change');
                        return;
                    }
                    
                    try {
                        // Fetch available property types and states for selected loan type
                        const response = await fetch(`/api/loan-type-options?loan_type=${encodeURIComponent(selectedLoanType)}`);
                        
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
                                // Reset to default state
                                propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                                stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                                propertyTypeSelect.trigger('change');
                                stateSelect.trigger('change');
                            }
                        } else {
                            console.error('Failed to fetch loan type options');
                            // Reset to default state
                            propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                            stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                            propertyTypeSelect.trigger('change');
                            stateSelect.trigger('change');
                        }
                    } catch (error) {
                        console.error('Error fetching loan type options:', error);
                        // Reset to default state
                        propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        propertyTypeSelect.trigger('change');
                        stateSelect.trigger('change');
                    }
                });
            });

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