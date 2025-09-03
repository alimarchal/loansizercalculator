<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Loan Calculator</h1>
            <p class="text-gray-600">Calculate your loan options with ease</p>
        </div>

        <!-- Main Form -->
        <form id="loanCalculatorForm" class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Left Column -->
                <div class="space-y-6">

                    <!-- Borrower Profile -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                            Borrower Profile
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Credit Score</label>
                                <input type="number" id="credit_score" name="credit_score" min="300" max="850"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g. 740" value="740">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Experience (Years)</label>
                                <input type="number" id="experience" name="experience" min="0" max="50"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g. 4" value="4">
                            </div>
                        </div>
                    </div>

                    <!-- Borrower Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-address-card text-green-600 mr-2"></i>
                            Borrower Information
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Borrower Name</label>
                                <input type="text" id="borrower_name" name="borrower_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Jimmy Test" value="Jimmy Test">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Borrower Email</label>
                                <input type="email" id="borrower_email" name="borrower_email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="hedwards@goldmanfunding.com" value="hedwards@goldmanfunding.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Borrower Phone</label>
                                <input type="tel" id="borrower_phone" name="borrower_phone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="631-602-0460" value="631-602-0460">
                            </div>
                        </div>
                    </div>

                    <!-- Broker Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-handshake text-purple-600 mr-2"></i>
                            Broker Information
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Broker Name</label>
                                <input type="text" id="broker_name" name="broker_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="John Doe" value="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Broker Email</label>
                                <input type="email" id="broker_email" name="broker_email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="hedwards@goldmanfunding.com" value="hedwards@goldmanfunding.com">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Broker Phone</label>
                                    <input type="tel" id="broker_phone" name="broker_phone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="631-602-0460" value="631-602-0460">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Broker Points
                                        (%)</label>
                                    <input type="number" id="broker_points" name="broker_points" min="0" max="10"
                                        step="0.1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="1" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">

                    <!-- Loan Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calculator text-orange-600 mr-2"></i>
                            Loan Summary
                        </h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Loan Type</label>
                                    <select id="loan_type" name="loan_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                                    <select id="transaction_type" name="transaction_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Transaction Type</option>
                                        @foreach($transactionTypes as $transactionType)
                                        <option value="{{ $transactionType->name }}" {{ $transactionType->name ==
                                            'Purchase' ? 'selected' : '' }}>
                                            {{ $transactionType->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Address</label>
                                <input type="text" id="property_address" name="property_address"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="2344 Meriden Pkwy CT" value="2344 Meriden Pkwy CT">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                    <select id="state" name="state"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->code }}" {{ $state->code == 'CT' ? 'selected' : '' }}>
                                            {{ $state->code }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <input type="text" id="zip_code" name="zip_code"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="06489" value="06489">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                                    <select id="property_type" name="property_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

                            <!-- Loan Term -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Loan Term</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="loan_term" value="12" checked
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-2 text-gray-700">12 Months</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="loan_term" value="18"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-2 text-gray-700">18 Months</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Financial Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input type="number" id="purchase_price" name="purchase_price" min="0"
                                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="100,000" value="100000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rehab Budget</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input type="number" id="rehab_budget" name="rehab_budget" min="0"
                                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="40,000" value="40000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">ARV</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input type="number" id="arv" name="arv" min="0"
                                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="1,100,000" value="1100000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calculate Button -->
                    <div class="text-center">
                        <button type="submit" id="calculateBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-calculator mr-2"></i>
                            Calculate Loan
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
            <p class="text-gray-600 mt-2">Calculating loan options...</p>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="hidden mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Loan Program Results</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Full Appraisal Table -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4 text-center">
                        <i class="fas fa-file-alt mr-2"></i>
                        Loan Program: Full Appraisal
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-gray-700">
                            <thead class="bg-blue-50 text-blue-900">
                                <tr>
                                    <th class="px-3 py-2 text-left">Loan Term</th>
                                    <th class="px-3 py-2 text-left">Interest Rate</th>
                                    <th class="px-3 py-2 text-left">Lender Points</th>
                                    <th class="px-3 py-2 text-left">Max LTV</th>
                                    <th class="px-3 py-2 text-left">Max LTC</th>
                                </tr>
                            </thead>
                            <tbody id="fullAppraisalTable">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Desktop Appraisal Table -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-green-800 mb-4 text-center">
                        <i class="fas fa-desktop mr-2"></i>
                        Loan Program: Desktop Appraisal
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-gray-700">
                            <thead class="bg-green-50 text-green-900">
                                <tr>
                                    <th class="px-3 py-2 text-left">Loan Term</th>
                                    <th class="px-3 py-2 text-left">Interest Rate</th>
                                    <th class="px-3 py-2 text-left">Lender Points</th>
                                    <th class="px-3 py-2 text-left">Max LTV</th>
                                    <th class="px-3 py-2 text-left">Max LTC</th>
                                </tr>
                            </thead>
                            <tbody id="desktopAppraisalTable">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Loan Details -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Loan Details
                </h3>
                <div id="loanDetails" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span id="errorText"></span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loanCalculatorForm');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const resultsSection = document.getElementById('resultsSection');
            const errorMessage = document.getElementById('errorMessage');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Hide previous results and errors
                resultsSection.classList.add('hidden');
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
                        populateResults(data.data);
                        resultsSection.classList.remove('hidden');
                    } else {
                        showError('No loan programs found for the given criteria.');
                    }
                    
                } catch (error) {
                    loadingSpinner.classList.add('hidden');
                    showError('An error occurred while calculating loan options. Please try again.');
                    console.error('API Error:', error);
                }
            });
            
            function populateResults(loans) {
                const fullAppraisalTable = document.getElementById('fullAppraisalTable');
                const desktopAppraisalTable = document.getElementById('desktopAppraisalTable');
                const loanDetails = document.getElementById('loanDetails');
                
                // Clear existing content
                fullAppraisalTable.innerHTML = '';
                desktopAppraisalTable.innerHTML = '';
                loanDetails.innerHTML = '';
                
                // Separate loans by program type
                const fullAppraisalLoans = loans.filter(loan => 
                    loan.loan_program === 'FULL APPRAISAL' || 
                    loan.display_name?.includes('FULL APPRAISAL')
                );
                const desktopAppraisalLoans = loans.filter(loan => 
                    loan.loan_program === 'DESKTOP APPRAISAL' || 
                    loan.display_name?.includes('DESKTOP APPRAISAL')
                );
                
                // Populate Full Appraisal table
                if (fullAppraisalLoans.length > 0) {
                    fullAppraisalLoans.forEach(loan => {
                        const loanData = loan.loan_type_and_loan_program_table;
                        const row = document.createElement('tr');
                        row.className = 'border-b hover:bg-blue-50';
                        row.innerHTML = `
                            <td class="px-3 py-2">${loanData?.loan_term || 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.lender_points ? loanData.lender_points + '%' : 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.max_ltv ? loanData.max_ltv + '%' : 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.max_ltc ? loanData.max_ltc + '%' : 'N/A'}</td>
                        `;
                        fullAppraisalTable.appendChild(row);
                    });
                } else {
                    fullAppraisalTable.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-center text-gray-500">No data available</td></tr>';
                }
                
                // Populate Desktop Appraisal table
                if (desktopAppraisalLoans.length > 0) {
                    desktopAppraisalLoans.forEach(loan => {
                        const loanData = loan.loan_type_and_loan_program_table;
                        const row = document.createElement('tr');
                        row.className = 'border-b hover:bg-green-50';
                        row.innerHTML = `
                            <td class="px-3 py-2">${loanData?.loan_term || 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.intrest_rate ? loanData.intrest_rate + '%' : 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.lender_points ? loanData.lender_points + '%' : 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.max_ltv ? loanData.max_ltv + '%' : 'N/A'}</td>
                            <td class="px-3 py-2">${loanData?.max_ltc ? loanData.max_ltc + '%' : 'N/A'}</td>
                        `;
                        desktopAppraisalTable.appendChild(row);
                    });
                } else {
                    desktopAppraisalTable.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-center text-gray-500">No data available</td></tr>';
                }
                
                // Populate loan details
                if (loans.length > 0) {
                    const firstLoan = loans[0];
                    const loanData = firstLoan.loan_type_and_loan_program_table;
                    
                    loanDetails.innerHTML = `
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">$${numberWithCommas(loanData?.purchase_loan_up_to || 0)}</div>
                            <div class="text-gray-600">Purchase Loan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">$${numberWithCommas(loanData?.rehab_loan_up_to || 0)}</div>
                            <div class="text-gray-600">Rehab Loan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">$${numberWithCommas(loanData?.total_loan_up_to || 0)}</div>
                            <div class="text-gray-600">Total Loan</div>
                        </div>
                    `;
                }
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
</body>

</html>