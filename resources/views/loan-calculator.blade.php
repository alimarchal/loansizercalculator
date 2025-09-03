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
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles



    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->

        <!-- Main Form -->
        <form id="loanCalculatorForm" class="space-y-10">

            <!-- Single Card: Borrower Profile & Loan Summary -->
            <div
                class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center pb-3 border-b border-gray-200">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-full mr-3">
                        <i class="fas fa-user-circle text-white text-lg"></i>
                    </div>
                    Borrower Profile & Information
                </h2>

                <!-- Row 1: Profile Details + Borrower Information (5 inputs total) -->
                <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-xl p-4 border border-blue-100 mb-4">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                        <!-- Profile Details (2 inputs) -->
                        <div class="lg:col-span-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Credit
                                        Score</label>
                                    <input type="number" id="credit_score" name="credit_score" min="300" max="850"
                                        class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm"
                                        placeholder="e.g. 740" value="740">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Experience
                                        (Years)</label>
                                    <input type="number" id="experience" name="experience" min="0" max="50"
                                        class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm"
                                        placeholder="e.g. 4" value="4">
                                </div>
                            </div>
                        </div>

                        <!-- Borrower Information (3 inputs) -->
                        <div class="lg:col-span-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Name</label>
                                    <input type="text" id="borrower_name" name="borrower_name"
                                        class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm"
                                        placeholder="Jimmy Test" value="Jimmy Test">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                                    <input type="email" id="borrower_email" name="borrower_email"
                                        class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm"
                                        placeholder="hedwards@goldmanfunding.com" value="hedwards@goldmanfunding.com">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                                    <input type="tel" id="borrower_phone" name="borrower_phone"
                                        class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm"
                                        placeholder="631-602-0460" value="631-602-0460">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Broker Information (4 inputs) -->
                <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl p-4 border border-purple-100 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Broker Name</label>
                            <input type="text" id="broker_name" name="broker_name"
                                class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-sm"
                                placeholder="John Doe" value="John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Broker Email</label>
                            <input type="email" id="broker_email" name="broker_email"
                                class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-sm"
                                placeholder="hedwards@goldmanfunding.com" value="hedwards@goldmanfunding.com">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Broker Phone</label>
                            <input type="tel" id="broker_phone" name="broker_phone"
                                class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-sm"
                                placeholder="631-602-0460" value="631-602-0460">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1"> Broker Points (%)</label>
                            <input type="number" id="broker_points" name="broker_points" min="0" max="10" step="0.1"
                                class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-sm"
                                placeholder="1" value="1">
                        </div>
                    </div>
                </div>

                <!-- Loan Summary Section -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-2 rounded-full mr-3">
                            <i class="fas fa-calculator text-white text-sm"></i>
                        </div>
                        Loan Summary
                    </h3>

                    <!-- Row 3: Loan & Property Details (4 inputs) -->
                    <div
                        class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-xl p-4 border border-orange-100 mb-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Loan Type</label>
                                <select id="loan_type" name="loan_type"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm">
                                    <option value="">Select Loan Type</option>
                                    @foreach($loanTypes as $loanType)
                                    <option value="{{ $loanType->name }}" {{ $loanType->name == 'Fix and Flip' ?
                                        'selected' : '' }}>
                                        {{ $loanType->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Transaction
                                    Type</label>
                                <select id="transaction_type" name="transaction_type"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm">
                                    <option value="">Select Transaction Type</option>
                                    @foreach($transactionTypes as $transactionType)
                                    <option value="{{ $transactionType->name }}" {{ $transactionType->name ==
                                        'Purchase' ? 'selected' : '' }}>
                                        {{ $transactionType->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Loan Term</label>
                                <select id="loan_term" name="loan_term"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm">
                                    <option value="12" selected>12 Months</option>
                                    <option value="18">18 Months</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Property
                                    Type</label>
                                <select id="property_type" name="property_type"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm">
                                    <option value="">Select Property Type</option>
                                    @foreach($propertyTypes as $propertyType)
                                    <option value="{{ $propertyType->name }}" {{ $propertyType->name == 'Single
                                        Family' ? 'selected' : '' }}>
                                        {{ $propertyType->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Address & Location (4 inputs) -->
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-4 border border-red-100 mb-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="lg:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Property
                                    Address</label>
                                <input type="text" id="property_address" name="property_address"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-all duration-200 text-sm"
                                    placeholder="2344 Meriden Pkwy CT" value="2344 Meriden Pkwy CT">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">State</label>
                                <select id="state" name="state"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-all duration-200 text-sm">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->code }}" {{ $state->code == 'CT' ? 'selected' : ''
                                        }}>
                                        {{ $state->code }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Zip Code</label>
                                <input type="text" id="zip_code" name="zip_code"
                                    class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-all duration-200 text-sm"
                                    placeholder="06489" value="06489">
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Financial Details (3 inputs) -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-4 border border-indigo-100">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Purchase
                                    Price</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-2 text-gray-500 text-sm">$</span>
                                    <input type="number" id="purchase_price" name="purchase_price" min="0"
                                        class="w-full pl-6 pr-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-sm"
                                        placeholder="100,000" value="100000">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Rehab
                                    Budget</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-2 text-gray-500 text-sm">$</span>
                                    <input type="number" id="rehab_budget" name="rehab_budget" min="0"
                                        class="w-full pl-6 pr-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-sm"
                                        placeholder="40,000" value="40000">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">ARV</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-2 text-gray-500 text-sm">$</span>
                                    <input type="number" id="arv" name="arv" min="0"
                                        class="w-full pl-6 pr-2 py-1.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-sm"
                                        placeholder="1,100,000" value="1100000">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan Program Results Section -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <div class="bg-gradient-to-r from-green-500 to-teal-600 p-2 rounded-full mr-3">
                            <i class="fas fa-chart-bar text-white text-sm"></i>
                        </div>
                        Loan Program Results
                    </h3>

                    <!-- Compact Results Table -->
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-4 border border-gray-100">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-100 text-gray-800">
                                    <tr>
                                        <th class="px-2 py-2 text-left font-semibold">Program</th>
                                        <th class="px-2 py-2 text-center font-semibold">Rate</th>
                                        <th class="px-2 py-2 text-center font-semibold">Points</th>
                                        <th class="px-2 py-2 text-center font-semibold">Max LTV</th>
                                        <th class="px-2 py-2 text-center font-semibold">Max LTC</th>
                                        <th class="px-2 py-2 text-center font-semibold">Purchase Loan</th>
                                        <th class="px-2 py-2 text-center font-semibold">Rehab Loan</th>
                                        <th class="px-2 py-2 text-center font-semibold">Total Loan</th>
                                    </tr>
                                </thead>
                                <tbody id="compactResultsTable" class="text-xs">
                                    <!-- Default rows showing 0.00 values -->
                                    <tr class="border-b border-gray-200 hover:bg-blue-50" id="fullAppraisalRow">
                                        <td class="px-2 py-2 font-medium text-blue-700">
                                            <i class="fas fa-file-alt mr-1"></i>Full Appraisal
                                        </td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center text-blue-600 font-medium">$0.00</td>
                                        <td class="px-2 py-2 text-center text-green-600 font-medium">$0.00</td>
                                        <td class="px-2 py-2 text-center text-purple-600 font-medium">$0.00</td>
                                    </tr>
                                    <tr class="border-b border-gray-200 hover:bg-green-50" id="desktopAppraisalRow">
                                        <td class="px-2 py-2 font-medium text-green-700">
                                            <i class="fas fa-desktop mr-1"></i>Desktop Appraisal
                                        </td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center">0.00%</td>
                                        <td class="px-2 py-2 text-center text-blue-600 font-medium">$0.00</td>
                                        <td class="px-2 py-2 text-center text-green-600 font-medium">$0.00</td>
                                        <td class="px-2 py-2 text-center text-purple-600 font-medium">$0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Loading Spinner (moved inside card) -->
                    <div id="loadingSpinner" class="hidden text-center py-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
                        <p class="text-gray-600 text-sm mt-1">Calculating loan options...</p>
                    </div>

                    <!-- Error Message (moved inside card) -->
                    <div id="errorMessage"
                        class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="errorText"></span>
                    </div>
                </div>

                <!-- Calculate Button - Inside card -->
                <div class="text-center mt-6 pt-4 border-t border-gray-200">
                    <button type="submit" id="calculateBtn"
                        class="bg-gradient-to-r from-blue-600 to-purple-700 hover:from-blue-700 hover:to-purple-800 text-white font-bold py-3 px-12 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg text-lg border-2 border-transparent hover:border-white">
                        <i class="fas fa-calculator mr-2"></i>
                        Calculate Loan Options
                    </button>
                </div>
            </div>
        </form>
    </div>

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
                        rehab_budget: formData.get('rehab_budget')
                    });
                    
                    const apiUrl = `/api/loan-matrix?${apiParams.toString()}`;
                    
                    // Make API call
                    const response = await fetch(apiUrl);
                    const data = await response.json();
                    
                    // Hide loading spinner
                    loadingSpinner.classList.add('hidden');
                    
                    if (data.success && data.data && data.data.length > 0) {
                        populateCompactResults(data.data);
                    } else {
                        showError('No loan programs found for the given criteria.');
                        resetToDefaults();
                    }
                    
                } catch (error) {
                    loadingSpinner.classList.add('hidden');
                    showError('An error occurred while calculating loan options. Please try again.');
                    resetToDefaults();
                    console.error('API Error:', error);
                }
            });
            
            function populateCompactResults(loans) {
                const fullAppraisalRow = document.getElementById('fullAppraisalRow');
                const desktopAppraisalRow = document.getElementById('desktopAppraisalRow');
                
                // Separate loans by program type
                const fullAppraisalLoans = loans.filter(loan => 
                    loan.loan_program === 'FULL APPRAISAL' || 
                    loan.display_name?.includes('FULL APPRAISAL')
                );
                const desktopAppraisalLoans = loans.filter(loan => 
                    loan.loan_program === 'DESKTOP APPRAISAL' || 
                    loan.display_name?.includes('DESKTOP APPRAISAL')
                );
                
                // Update Full Appraisal row
                if (fullAppraisalLoans.length > 0) {
                    const loanData = fullAppraisalLoans[0].loan_type_and_loan_program_table;
                    fullAppraisalRow.innerHTML = `
                        <td class="px-2 py-2 font-medium text-blue-700">
                            <i class="fas fa-file-alt mr-1"></i>Full Appraisal
                        </td>
                        <td class="px-2 py-2 text-center">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center">${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center">${loanData?.max_ltc ? loanData.max_ltc + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center text-blue-600 font-medium">$${numberWithCommas(loanData?.purchase_loan_up_to || 0)}</td>
                        <td class="px-2 py-2 text-center text-green-600 font-medium">$${numberWithCommas(loanData?.rehab_loan_up_to || 0)}</td>
                        <td class="px-2 py-2 text-center text-purple-600 font-medium">$${numberWithCommas(loanData?.total_loan_up_to || 0)}</td>
                    `;
                }
                
                // Update Desktop Appraisal row
                if (desktopAppraisalLoans.length > 0) {
                    const loanData = desktopAppraisalLoans[0].loan_type_and_loan_program_table;
                    desktopAppraisalRow.innerHTML = `
                        <td class="px-2 py-2 font-medium text-green-700">
                            <i class="fas fa-desktop mr-1"></i>Desktop Appraisal
                        </td>
                        <td class="px-2 py-2 text-center">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center">${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center">${loanData?.max_ltc ? loanData.max_ltc + '%' : '0.00%'}</td>
                        <td class="px-2 py-2 text-center text-blue-600 font-medium">$${numberWithCommas(loanData?.purchase_loan_up_to || 0)}</td>
                        <td class="px-2 py-2 text-center text-green-600 font-medium">$${numberWithCommas(loanData?.rehab_loan_up_to || 0)}</td>
                        <td class="px-2 py-2 text-center text-purple-600 font-medium">$${numberWithCommas(loanData?.total_loan_up_to || 0)}</td>
                    `;
                }
            }
            
            function resetToDefaults() {
                const fullAppraisalRow = document.getElementById('fullAppraisalRow');
                const desktopAppraisalRow = document.getElementById('desktopAppraisalRow');
                
                // Reset Full Appraisal row to defaults
                fullAppraisalRow.innerHTML = `
                    <td class="px-2 py-2 font-medium text-blue-700">
                        <i class="fas fa-file-alt mr-1"></i>Full Appraisal
                    </td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center text-blue-600 font-medium">$0.00</td>
                    <td class="px-2 py-2 text-center text-green-600 font-medium">$0.00</td>
                    <td class="px-2 py-2 text-center text-purple-600 font-medium">$0.00</td>
                `;
                
                // Reset Desktop Appraisal row to defaults
                desktopAppraisalRow.innerHTML = `
                    <td class="px-2 py-2 font-medium text-green-700">
                        <i class="fas fa-desktop mr-1"></i>Desktop Appraisal
                    </td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center">0.00%</td>
                    <td class="px-2 py-2 text-center text-blue-600 font-medium">$0.00</td>
                    <td class="px-2 py-2 text-center text-green-600 font-medium">$0.00</td>
                    <td class="px-2 py-2 text-center text-purple-600 font-medium">$0.00</td>
                `;
            }
            
            function showError(message) {
                document.getElementById('errorText').textContent = message;
                errorMessage.classList.remove('hidden');
            }
            
            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        });
    </script>

    <script src="{{ url('select2/jquery-3.5.1.js') }}"></script>
    <script src="{{ url('select2/select2.min.js') }}" defer></script>
    <script>
        $(document).ready(function () {
                $('.select2').select2();
            });

            $('form').submit(function(){
                // If x-button does not render as a traditional submit button, target it directly by ID or class
                $('#submit-btn').attr('disabled', 'disabled');
            });
    </script>
    @stack('modals')

    {{-- Allow views to push inline scripts -- e.g. @push('scripts') -- so they are rendered here --}}
    @stack('scripts')
    @livewireScripts
</body>

</html>