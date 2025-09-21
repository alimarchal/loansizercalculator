<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Borrower Details - {{ $borrower->full_name }}
            </h2>
            <div class="flex space-x-4">

                <a href="{{ route('borrowers.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Status Badge -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Application Status</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($borrower->status === 'active') bg-green-100 text-green-800
                        @elseif($borrower->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($borrower->status) }}
                    </span>
                </div>
            </div>

            <!-- Main Information Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Borrower Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                </path>
                            </svg>
                            Borrower Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-blue-600">
                                @if($borrower->email)
                                <a href="mailto:{{ $borrower->email }}" class="hover:underline">{{ $borrower->email
                                    }}</a>
                                @else
                                N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Credit Score</label>
                            <div class="mt-1">
                                @if($borrower->credit_score)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($borrower->credit_score >= 740) bg-green-100 text-green-800
                                    @elseif($borrower->credit_score >= 680) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $borrower->credit_score }}
                                </span>
                                @else
                                <span class="text-sm text-gray-900">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Experience</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->years_of_experience ?? 'N/A' }} {{
                                $borrower->years_of_experience ? 'years' : '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Broker Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-purple-50 to-violet-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 112 2v6a2 2 0 11-2 2v-2M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m0 0V6a2 2 0 112 2v6a2 2 0 11-2 2V6z">
                                </path>
                            </svg>
                            Broker Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Broker Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->broker_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Broker Email</label>
                            <p class="mt-1 text-sm text-blue-600">
                                @if($borrower->broker_email)
                                <a href="mailto:{{ $borrower->broker_email }}" class="hover:underline">{{
                                    $borrower->broker_email }}</a>
                                @else
                                N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Broker Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->broker_phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Broker Points</label>
                            <p class="mt-1 text-sm font-medium text-green-600">{{ $borrower->broker_points ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Loan Summary -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Loan Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Loan Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->selected_loan_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Transaction Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->transaction_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Selected Loan Program</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->selected_loan_program ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Property Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->property_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">State & Zip</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->property_state ?? 'N/A' }} {{
                                $borrower->property_zip_code ?? '' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Property Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->property_type ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Loan Details Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-gradient-to-r from-orange-50 to-amber-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        Loan Details
                    </h3>
                </div>
                <div class="p-6">
                    @if($borrower->selected_loan_type == 'DSCR Rental Loans')
                    <!-- DSCR Rental Loan Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Purchase Price/As Is Value</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->purchase_price ? '$' .
                                number_format($borrower->purchase_price) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Occupancy</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->occupancy_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Monthly Rent/Market Rent</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->monthly_market_rent ? '$' .
                                number_format($borrower->monthly_market_rent) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Annual Tax</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->annual_tax ? '$' .
                                number_format($borrower->annual_tax) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Annual Insurance</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->annual_insurance ? '$' .
                                number_format($borrower->annual_insurance) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Annual HOA</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->annual_hoa ? '$' .
                                number_format($borrower->annual_hoa) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">DSCR</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->dscr ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Purchase Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->purchase_date ? date('m/d/Y',
                                strtotime($borrower->purchase_date)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Payoff Amount (if applicable)</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->payoff_amount ? '$' .
                                number_format($borrower->payoff_amount) : 'N/A' }}</p>
                        </div>
                    </div>
                    @else
                    <!-- Non-DSCR Loan Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Purchase Price/As Is Value</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->purchase_price ? '$' .
                                number_format($borrower->purchase_price) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Rehab Budget</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->rehab_budget ? '$' .
                                number_format($borrower->rehab_budget) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ARV</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->arv ? '$' .
                                number_format($borrower->arv) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Purchase Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->purchase_date ? date('m/d/Y',
                                strtotime($borrower->purchase_date)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Rehab Completed (if
                                applicable)</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->rehab_completed ? date('m/d/Y',
                                strtotime($borrower->rehab_completed)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Payoff (if applicable)</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->payoff_amount ? '$' .
                                number_format($borrower->payoff_amount) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Permit Status</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $borrower->permit_status ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- DSCR Loan Program Details Table -->
            @if($borrower->selected_loan_type == 'DSCR Rental Loans' && $borrower->selectedLoanProgram)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        Selected DSCR Loan Program Details
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Loan Term</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Max LTV</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Monthly Payment</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Interest Rate</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lender Points</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pre Pay Penalty</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="bg-green-50 border-l-4 border-green-400">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $borrower->selectedLoanProgram->loan_term ?
                                    $borrower->selectedLoanProgram->loan_term . ' months' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $borrower->selectedLoanProgram->max_ltv ?
                                    number_format($borrower->selectedLoanProgram->max_ltv, 2) . '%' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    @php
                                    $monthlyPayment = null;
                                    if($borrower->selectedLoanProgram->raw_loan_data) {
                                    $rawData = is_array($borrower->selectedLoanProgram->raw_loan_data)
                                    ? $borrower->selectedLoanProgram->raw_loan_data
                                    : json_decode($borrower->selectedLoanProgram->raw_loan_data, true);
                                    $monthlyPayment = $rawData['monthly_payment'] ??
                                    ($rawData['loan_program_values']['monthly_payment'] ?? null);
                                    }
                                    @endphp
                                    {{ $monthlyPayment ? '$' . number_format($monthlyPayment, 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $borrower->selectedLoanProgram->interest_rate ?
                                    number_format($borrower->selectedLoanProgram->interest_rate, 3) . '%' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $borrower->selectedLoanProgram->lender_points ?
                                    number_format($borrower->selectedLoanProgram->lender_points, 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                    $prepayPenalty = null;
                                    if($borrower->selectedLoanProgram->raw_loan_data) {
                                    $rawData = is_array($borrower->selectedLoanProgram->raw_loan_data)
                                    ? $borrower->selectedLoanProgram->raw_loan_data
                                    : json_decode($borrower->selectedLoanProgram->raw_loan_data, true);
                                    $prepayPenalty = $rawData['pre_pay_penalty'] ??
                                    ($rawData['loan_program_values']['pre_pay_penalty'] ?? null);
                                    }
                                    @endphp
                                    {{ $prepayPenalty ?? 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Loan Amount & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Loan Amount Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($borrower->selected_loan_type == 'DSCR Rental Loans')
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Requested Amount</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $borrower->loan_amount_requested ?
                                '$' . number_format($borrower->loan_amount_requested) : 'N/A' }}</p>
                        </div>
                        @else
                        <!-- Fix and Flip / Other Loan Types -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Purchase Loan Amount</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $borrower->purchase_loan_amount ?
                                '$' . number_format($borrower->purchase_loan_amount) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Rehab Loan Amount</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $borrower->rehab_loan_amount ?
                                '$' . number_format($borrower->rehab_loan_amount) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Total Loan Amount</label>
                            <p class="mt-1 text-xl font-bold text-green-600">{{ $borrower->total_loan_amount ?
                                '$' . number_format($borrower->total_loan_amount) : 'N/A' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-gray-50 to-slate-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Additional Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Notes</label>
                            <p class="mt-1 text-sm text-gray-600">{{ $borrower->notes ?? 'No additional notes' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Program Results Section -->
            @if($borrower->loanProgramResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-gradient-to-r from-teal-50 to-cyan-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2m0 10V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Loan Program Results ({{ $borrower->loanProgramResults->count() }} Programs Available)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Loan Program</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Loan Term</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Max LTV</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Monthly Payment</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Interest Rate</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lender Points</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pre Pay Penalty</th>
                                @if($borrower->selected_loan_type != 'DSCR Rental Loans')
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Max LTC</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Purchase Loan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Loan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pricing Tier</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($borrower->orderedLoanProgramResults as $result)
                            <tr
                                class="{{ $result->is_selected ? 'bg-green-50 border-l-4 border-green-400' : 'hover:bg-gray-50' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($result->is_selected)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Selected
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Available
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $result->loan_program ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $result->loan_type ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->loan_term ? $result->loan_term : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->max_ltv ? number_format($result->max_ltv, 2) . '%' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    @php
                                    $monthlyPayment = null;
                                    if($result->raw_loan_data) {
                                    $rawData = is_array($result->raw_loan_data)
                                    ? $result->raw_loan_data
                                    : json_decode($result->raw_loan_data, true);
                                    $monthlyPayment = $rawData['monthly_payment'] ??
                                    ($rawData['loan_program_values']['monthly_payment'] ?? null);
                                    }
                                    @endphp
                                    {{ $monthlyPayment ? '$' . number_format($monthlyPayment, 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->interest_rate ? number_format($result->interest_rate, 3) . '%' : 'N/A'
                                    }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->lender_points ? number_format($result->lender_points, 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                    $prepayPenalty = null;
                                    if($result->raw_loan_data) {
                                    $rawData = is_array($result->raw_loan_data)
                                    ? $result->raw_loan_data
                                    : json_decode($result->raw_loan_data, true);
                                    $prepayPenalty = $rawData['pre_pay_penalty'] ??
                                    ($rawData['loan_program_values']['pre_pay_penalty'] ?? null);
                                    }
                                    @endphp
                                    {{ $prepayPenalty ?? 'N/A' }}
                                </td>
                                @if($borrower->selected_loan_type != 'DSCR Rental Loans')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->max_ltc ? number_format($result->max_ltc, 2) . '%' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->purchase_loan_up_to ? '$' . number_format($result->purchase_loan_up_to)
                                    : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->total_loan_up_to ? '$' . number_format($result->total_loan_up_to) :
                                    'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $result->pricing_tier ?? 'Standard' }}
                                    </span>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- API Response JSON Section (if available) -->
            @if($borrower->selectedLoanProgram && $borrower->selectedLoanProgram->raw_loan_data)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-gradient-to-r from-gray-50 to-slate-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4">
                            </path>
                        </svg>
                        Selected Program API Response
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre
                            class="text-green-400 text-sm"><code>{{ json_encode(is_string($borrower->selectedLoanProgram->raw_loan_data) ? json_decode($borrower->selectedLoanProgram->raw_loan_data) : $borrower->selectedLoanProgram->raw_loan_data, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>