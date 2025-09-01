<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Loan Program Rule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('loan-programs.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Loan Programs
                        </a>
                    </div>

                    <x-status-message />

                    <!-- Form Instructions -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Quick Start Guide
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Fill in the <strong>Basic Information</strong> first - these are required
                                            fields</li>
                                        <li>Set <strong>Rehab Limits</strong> percentages for each rehabilitation level
                                        </li>
                                        <li>Configure <strong>Interest Rate Pricing</strong> for different loan amounts
                                        </li>
                                        <li>Leave fields blank if they don't apply to this loan rule</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('loan-programs.store') }}" class="space-y-6" id="loanRuleForm">
                        @csrf

                        <!-- Basic Information -->
                        <div
                            class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 p-6 rounded-lg border border-green-200 dark:border-green-800">
                            <h3 class="text-lg font-semibold mb-4 text-green-800 dark:text-green-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                1. Basic Information
                                <span
                                    class="ml-2 text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 px-2 py-1 rounded">Required</span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Experience -->
                                <div>
                                    <label for="experience_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Experience Level <span class="text-red-500">*</span>
                                    </label>
                                    <select name="experience_id" id="experience_id" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-green-500 focus:ring-green-500">
                                        <option value="">Select Experience Level</option>
                                        @foreach($experiences as $experience)
                                        <option value="{{ $experience->id }}" {{ old('experience_id')==$experience->id ?
                                            'selected' : '' }}>
                                            {{ $experience->experiences_range }}
                                            ({{ $experience->loanType->name ?? 'N/A' }}{{
                                            $experience->loanType->loan_program ? ' - ' .
                                            $experience->loanType->loan_program : '' }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('experience_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- FICO Band -->
                                <div>
                                    <label for="fico_band_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        FICO Band <span class="text-red-500">*</span>
                                    </label>
                                    <select name="fico_band_id" id="fico_band_id" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-green-500 focus:ring-green-500">
                                        <option value="">Select FICO Band</option>
                                        @foreach($ficoBands as $ficoBand)
                                        <option value="{{ $ficoBand->id }}" {{ old('fico_band_id')==$ficoBand->id ?
                                            'selected' : '' }}>
                                            {{ $ficoBand->fico_range }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('fico_band_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Transaction Type -->
                                <div>
                                    <label for="transaction_type_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Transaction Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="transaction_type_id" id="transaction_type_id" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-green-500 focus:ring-green-500">
                                        <option value="">Select Transaction Type</option>
                                        @foreach($transactionTypes as $transactionType)
                                        <option value="{{ $transactionType->id }}" {{
                                            old('transaction_type_id')==$transactionType->id ? 'selected' : '' }}>
                                            {{ $transactionType->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('transaction_type_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Total Loan -->
                                <div>
                                    <label for="max_total_loan"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Max Total Loan ($) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" name="max_total_loan" id="max_total_loan" step="0.01"
                                            min="0" required value="{{ old('max_total_loan') }}" placeholder="500000.00"
                                            class="w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    @error('max_total_loan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Budget -->
                                <div>
                                    <label for="max_budget"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Max Budget ($) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" name="max_budget" id="max_budget" step="0.01" min="0"
                                            required value="{{ old('max_budget') }}" placeholder="400000.00"
                                            class="w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    @error('max_budget')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rehab Limits -->
                        <div
                            class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-6 rounded-lg border border-purple-200 dark:border-purple-800">
                            <h3
                                class="text-lg font-semibold mb-4 text-purple-800 dark:text-purple-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                2. Rehab Limits (Optional)
                                <span
                                    class="ml-2 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded">Leave
                                    blank if N/A</span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($rehabLevels as $index => $rehabLevel)
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-purple-200 dark:border-purple-700">
                                    <h4 class="font-medium mb-3 text-purple-700 dark:text-purple-300 flex items-center">
                                        <span
                                            class="w-6 h-6 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full flex items-center justify-center text-xs font-bold mr-2">
                                            {{ $index + 1 }}
                                        </span>
                                        {{ $rehabLevel->name }}
                                    </h4>

                                    <input type="hidden" name="rehab_limits[{{ $index }}][rehab_level_id]"
                                        value="{{ $rehabLevel->id }}">

                                    <div
                                        class="grid grid-cols-1 {{ $rehabLevel->name === 'EXTENSIVE REHAB' ? 'md:grid-cols-3' : 'md:grid-cols-2' }} gap-3">
                                        <!-- Max LTC -->
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Max LTC (%)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="rehab_limits[{{ $index }}][max_ltc]"
                                                    step="0.01" min="0" max="100" value="{{ old("
                                                    rehab_limits.{$index}.max_ltc") }}" placeholder="75.00"
                                                    class="w-full pr-8 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-purple-500 focus:ring-purple-500">
                                                <span
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-500">%</span>
                                            </div>
                                        </div>

                                        <!-- Max LTV -->
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Max LTV (%)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="rehab_limits[{{ $index }}][max_ltv]"
                                                    step="0.01" min="0" max="100" value="{{ old("
                                                    rehab_limits.{$index}.max_ltv") }}" placeholder="70.00"
                                                    class="w-full pr-8 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-purple-500 focus:ring-purple-500">
                                                <span
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-500">%</span>
                                            </div>
                                        </div>

                                        <!-- Max LTFC (only for Extensive Rehab) -->
                                        @if($rehabLevel->name === 'EXTENSIVE REHAB')
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Max LTFC (%)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="rehab_limits[{{ $index }}][max_ltfc]"
                                                    step="0.01" min="0" max="100" value="{{ old("
                                                    rehab_limits.{$index}.max_ltfc") }}" placeholder="65.00"
                                                    class="w-full pr-8 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-purple-500 focus:ring-purple-500">
                                                <span
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-500">%</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div
                            class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 p-6 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <h3
                                class="text-lg font-semibold mb-4 text-yellow-800 dark:text-yellow-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                                3. Interest Rate Pricing (Optional)
                                <span
                                    class="ml-2 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded">Leave
                                    blank if N/A</span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($pricingTiers as $index => $pricingTier)
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                    <h4 class="font-medium mb-3 text-yellow-700 dark:text-yellow-300 flex items-center">
                                        <span
                                            class="w-6 h-6 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full flex items-center justify-center text-xs font-bold mr-2">
                                            $
                                        </span>
                                        Loan Size {{ $pricingTier->price_range }}
                                    </h4>

                                    <input type="hidden" name="pricings[{{ $index }}][pricing_tier_id]"
                                        value="{{ $pricingTier->id }}">

                                    <div class="space-y-3">
                                        <!-- Interest Rate -->
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Interest Rate (%)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="pricings[{{ $index }}][interest_rate]"
                                                    step="0.01" min="0" max="50" value="{{ old("
                                                    pricings.{$index}.interest_rate") }}" placeholder="10.50"
                                                    class="w-full pr-8 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-yellow-500 focus:ring-yellow-500">
                                                <span
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-500">%</span>
                                            </div>
                                        </div>

                                        <!-- Lender Points -->
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Lender Points
                                            </label>
                                            <input type="number" name="pricings[{{ $index }}][lender_points]"
                                                step="0.01" min="0" max="10" value="{{ old("
                                                pricings.{$index}.lender_points") }}" placeholder="2.00"
                                                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-yellow-500 focus:ring-yellow-500">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Make sure all required fields are filled before submitting
                                </div>
                                <div class="flex space-x-4">
                                    <a href="{{ route('loan-programs.index') }}"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create Loan Program
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Help Section -->
                    <div class="mt-8 bg-gray-100 dark:bg-gray-900 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Need Help?
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Basic Information</h4>
                                <ul class="text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Choose the borrower's experience level</li>
                                    <li>• Select loan type and program (Full/Desktop Appraisal)</li>
                                    <li>• Pick transaction type (Purchase/Refinance)</li>
                                    <li>• Set maximum loan and budget amounts</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Rehab Limits</h4>
                                <ul class="text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• <strong>LTC:</strong> Loan-to-Cost ratio</li>
                                    <li>• <strong>LTV:</strong> Loan-to-Value ratio</li>
                                    <li>• <strong>LTFC:</strong> Loan-to-Future-Cost (Extensive only)</li>
                                    <li>• Leave blank if not applicable</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Interest Rates</h4>
                                <ul class="text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Set rates for different loan sizes</li>
                                    <li>• Configure lender points</li>
                                    <li>• Higher amounts typically get better rates</li>
                                    <li>• All pricing tiers are optional</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus first input
            document.getElementById('experience_id').focus();
            
            // Add confirmation before leaving page with unsaved changes
            let formChanged = false;
            const form = document.getElementById('loanRuleForm');
            const inputs = form.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
            
            form.addEventListener('submit', function() {
                formChanged = false;
            });
        });
    </script>
    @endpush
</x-app-layout>