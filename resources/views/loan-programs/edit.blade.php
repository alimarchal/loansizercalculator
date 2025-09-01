<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Loan Program Rule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('loan-programs.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            ← Back to Loan Programs
                        </a>
                    </div>

                    <x-status-message />

                    <form method="POST" action="{{ route('loan-programs.update', $loanRule->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Experience -->
                                <div>
                                    <label for="experience_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Experience Level
                                    </label>
                                    <select name="experience_id" id="experience_id"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Experience</option>
                                        @foreach($experiences as $experience)
                                        <option value="{{ $experience->id }}" {{ old('experience_id', $loanRule->
                                            experience_id) == $experience->id ? 'selected' : '' }}>
                                            {{ $experience->experiences_range }} ({{ $experience->loanType->name ??
                                            'N/A' }})
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
                                        FICO Band
                                    </label>
                                    <select name="fico_band_id" id="fico_band_id"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select FICO Band</option>
                                        @foreach($ficoBands as $ficoBand)
                                        <option value="{{ $ficoBand->id }}" {{ old('fico_band_id', $loanRule->
                                            fico_band_id) == $ficoBand->id ? 'selected' : '' }}>
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
                                        Transaction Type
                                    </label>
                                    <select name="transaction_type_id" id="transaction_type_id"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Transaction Type</option>
                                        @foreach($transactionTypes as $transactionType)
                                        <option value="{{ $transactionType->id }}" {{ old('transaction_type_id',
                                            $loanRule->transaction_type_id) == $transactionType->id ? 'selected' : ''
                                            }}>
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
                                        Max Total Loan ($)
                                    </label>
                                    <input type="number" name="max_total_loan" id="max_total_loan" step="0.01" min="0"
                                        value="{{ old('max_total_loan', $loanRule->max_total_loan) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('max_total_loan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Budget -->
                                <div>
                                    <label for="max_budget"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Max Budget ($)
                                    </label>
                                    <input type="number" name="max_budget" id="max_budget" step="0.01" min="0"
                                        value="{{ old('max_budget', $loanRule->max_budget) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('max_budget')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rehab Limits -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Rehab Limits</h3>

                            <div class="space-y-4">
                                @foreach($rehabLevels as $index => $rehabLevel)
                                @php
                                $existingLimit = $loanRule->rehabLimits->where('rehab_level_id',
                                $rehabLevel->id)->first();
                                @endphp
                                <div class="bg-white dark:bg-gray-800 p-4 rounded border">
                                    <h4 class="font-medium mb-3">{{ $rehabLevel->name }}</h4>

                                    <input type="hidden" name="rehab_limits[{{ $index }}][rehab_level_id]"
                                        value="{{ $rehabLevel->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Max LTC -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Max LTC (%)
                                            </label>
                                            <input type="number" name="rehab_limits[{{ $index }}][max_ltc]" step="0.01"
                                                min="0" max="100" value="{{ old(" rehab_limits.{$index}.max_ltc",
                                                $existingLimit?->max_ltc) }}"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600
                                            dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <!-- Max LTV -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Max LTV (%)
                                            </label>
                                            <input type="number" name="rehab_limits[{{ $index }}][max_ltv]" step="0.01"
                                                min="0" max="100" value="{{ old(" rehab_limits.{$index}.max_ltv",
                                                $existingLimit?->max_ltv) }}"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600
                                            dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <!-- Max LTFC (only for Extensive Rehab) -->
                                        @if($rehabLevel->name === 'EXTENSIVE REHAB')
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Max LTFC (%)
                                            </label>
                                            <input type="number" name="rehab_limits[{{ $index }}][max_ltfc]" step="0.01"
                                                min="0" max="100" value="{{ old(" rehab_limits.{$index}.max_ltfc",
                                                $existingLimit?->max_ltfc) }}"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600
                                            dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Interest Rate Pricing</h3>

                            <div class="space-y-4">
                                @foreach($pricingTiers as $index => $pricingTier)
                                @php
                                $existingPricing = $loanRule->pricings->where('pricing_tier_id',
                                $pricingTier->id)->first();
                                @endphp
                                <div class="bg-white dark:bg-gray-800 p-4 rounded border">
                                    <h4 class="font-medium mb-3">{{ $pricingTier->price_range }}</h4>

                                    <input type="hidden" name="pricings[{{ $index }}][pricing_tier_id]"
                                        value="{{ $pricingTier->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Interest Rate -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Interest Rate (%)
                                            </label>
                                            <input type="number" name="pricings[{{ $index }}][interest_rate]"
                                                step="0.01" min="0" max="50" value="{{ old("
                                                pricings.{$index}.interest_rate", $existingPricing?->interest_rate) }}"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600
                                            dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <!-- Lender Points -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Lender Points
                                            </label>
                                            <input type="number" name="pricings[{{ $index }}][lender_points]"
                                                step="0.01" min="0" max="10" value="{{ old("
                                                pricings.{$index}.lender_points", $existingPricing?->lender_points) }}"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600
                                            dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('loan-programs.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                                Update Loan Program
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>