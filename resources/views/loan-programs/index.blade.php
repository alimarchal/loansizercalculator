<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            @if(isset($isDscrMatrix) && $isDscrMatrix)
            DSCR Rental Loans - LTV Adjustment Matrix
            @elseif($isQuickSearch)
            Search Results
            @if(isset($searchInfo['credit_score']))
            - Credit Score: {{ $searchInfo['credit_score'] }}
            @endif
            @if(isset($searchInfo['experience_years']))
            - Experience: {{ $searchInfo['experience_years'] }} years
            @endif
            @else
            Loan Programs Matrix
            @endif
        </h2>

        <div class="flex justify-center items-center float-right">
            @if(isset($isDscrMatrix) && $isDscrMatrix)
            <a href="{{ route('loan-programs.index') }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0V17m0-10a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z" />
                </svg>
                Regular Matrix
            </a>
            @else
            <a href="{{ route('loan-programs.index', ['view' => 'dscr-matrix']) }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                DSCR Matrix
            </a>
            @endif
            <button id="toggle"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-950 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Search
            </button>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg> &nbsp;
                Back
            </a>
        </div>
    </x-slot>

    <!-- FILTER SECTION -->
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg" id="filters"
            style="display: none">
            <div class="p-6">
                <form method="GET" action="{{ route('loan-programs.index') }}">
                    @if(isset($isDscrMatrix) && $isDscrMatrix)
                    <input type="hidden" name="view" value="dscr-matrix">
                    @endif

                    @if(isset($isDscrMatrix) && $isDscrMatrix)
                    <!-- DSCR Matrix Enhanced Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Credit Score Input -->
                        <div>
                            <label for="credit_score"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Credit Score (FICO)
                            </label>
                            <input type="number" name="credit_score" id="credit_score"
                                value="{{ request('credit_score') }}" min="300" max="850"
                                placeholder="Enter credit score (e.g., 700)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <small class="text-gray-500 dark:text-gray-400">Range: 300-850</small>
                        </div>

                        <!-- Loan Amount Input -->
                        <div>
                            <label for="loan_amount"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Loan Amount
                            </label>
                            <input type="number" name="loan_amount" id="loan_amount"
                                value="{{ request('loan_amount') }}" min="0"
                                placeholder="Enter loan amount (e.g., 250000)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <small class="text-gray-500 dark:text-gray-400">Filter by specific loan amount</small>
                        </div>

                        <!-- Filter by Loan Program -->
                        <div>
                            <label for="loan_program"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Loan Program
                            </label>
                            <select name="filter[loan_program]" id="loan_program"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                @foreach($loanPrograms as $programValue => $programDisplay)
                                <option value="{{ $programValue }}" {{ request('filter.loan_program', 'Loan Program #1'
                                    )==$programValue ? 'selected' : '' }}>
                                    {{ $programDisplay }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter by Loan Type -->
                        <div>
                            <label for="loan_type_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Loan Type
                            </label>
                            <select name="filter[loan_type_id]" id="loan_type_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All DSCR Types</option>
                                @foreach($loanTypes as $loanType)
                                <option value="{{ $loanType->id }}" {{ request('filter.loan_type_id')==$loanType->id ?
                                    'selected' : '' }}>
                                    {{ $loanType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Property Type Filter -->
                        @if(isset($propertyTypes) && count($propertyTypes) > 0)
                        <div>
                            <label for="property_type_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Property Type
                            </label>
                            <select name="filter[property_type_id]" id="property_type_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Property Types</option>
                                @foreach($propertyTypes as $propertyType)
                                <option value="{{ $propertyType->id }}" {{
                                    request('filter.property_type_id')==$propertyType->id ? 'selected' : '' }}>
                                    {{ $propertyType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Occupancy Type Filter -->
                        @if(isset($occupancyTypes) && count($occupancyTypes) > 0)
                        <div>
                            <label for="occupancy_type_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Occupancy Type
                            </label>
                            <select name="filter[occupancy_type_id]" id="occupancy_type_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Occupancy Types</option>
                                @foreach($occupancyTypes as $occupancyType)
                                <option value="{{ $occupancyType->id }}" {{
                                    request('filter.occupancy_type_id')==$occupancyType->id ? 'selected' : '' }}>
                                    {{ $occupancyType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- DSCR Range Filter -->
                        @if(isset($dscrRanges) && count($dscrRanges) > 0)
                        <div>
                            <label for="dscr_range"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                DSCR Range
                            </label>
                            <select name="filter[dscr_range]" id="dscr_range"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All DSCR Ranges</option>
                                @foreach($dscrRanges as $dscrRange)
                                <option value="{{ $dscrRange->id }}" {{ request('filter.dscr_range')==$dscrRange->id ?
                                    'selected' : '' }}>
                                    {{ $dscrRange->dscr_range }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    @else
                    <!-- Regular Matrix Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Credit Score Input -->
                        <div>
                            <label for="credit_score"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Credit Score (FICO)
                            </label>
                            <input type="number" name="credit_score" id="credit_score"
                                value="{{ request('credit_score') }}" min="300" max="850"
                                placeholder="Enter credit score (e.g., 700)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <small class="text-gray-500 dark:text-gray-400">Range: 300-850</small>
                        </div>

                        <!-- Experience Years Input -->
                        <div>
                            <label for="experience_years"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Years of Experience
                            </label>
                            <input type="number" name="experience_years" id="experience_years"
                                value="{{ request('experience_years') }}" min="0" max="50"
                                placeholder="Enter years (e.g., 3)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <small class="text-gray-500 dark:text-gray-400">0 for no experience</small>
                        </div>

                        <!-- Filter by Loan Program -->
                        <div>
                            <label for="loan_program"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Loan Program
                            </label>
                            <select name="filter[loan_program]" id="loan_program"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Programs</option>
                                @foreach($loanPrograms as $programValue => $programDisplay)
                                <option value="{{ $programValue }}" {{ request('filter.loan_program')==$programValue
                                    ? 'selected' : '' }}>
                                    {{ $programDisplay }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <!-- Filter by Loan Type -->
                            <x-loan-type-select :loanTypes="$loanTypes" />
                        </div>
                        <!-- Filter by FICO Band -->
                        <div>
                            <x-fico-band-select :ficoBands="$ficoBands" />
                        </div>
                        <!-- Filter by Transaction Type -->
                        <x-transaction-type-select :transactionTypes="$transactionTypes" />

                        <!-- Category Filter -->
                        <div>
                            <label for="category_filter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category Filter
                            </label>
                            <select name="filter[category]" id="category_filter"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Categories</option>
                                <option value="FICO" {{ old('filter.category', request()->input('filter.category')) ==
                                    'FICO' ? 'selected' : '' }}>FICO</option>
                                <option value="Loan Amount" {{ old('filter.category', request()->
                                    input('filter.category')) == 'Loan Amount' ? 'selected' : '' }}>Loan Amount</option>
                                <option value="Property Type" {{ old('filter.category', request()->
                                    input('filter.category')) == 'Property Type' ? 'selected' : '' }}>Property Type
                                </option>
                                <option value="Occupancy" {{ old('filter.category', request()->input('filter.category'))
                                    == 'Occupancy' ? 'selected' : '' }}>Occupancy</option>
                                <option value="Transaction Type" {{ old('filter.category', request()->
                                    input('filter.category')) == 'Transaction Type' ? 'selected' : '' }}>Transaction
                                    Type</option>
                                <option value="DSCR" {{ old('filter.category', request()->input('filter.category')) ==
                                    'DSCR' ? 'selected' : '' }}>DSCR</option>
                                <option value="Pre Pay" {{ old('filter.category', request()->input('filter.category'))
                                    == 'Pre Pay' ? 'selected' : '' }}>Pre Pay</option>
                                <option value="Loan Type" {{ old('filter.category', request()->input('filter.category'))
                                    == 'Loan Type' ? 'selected' : '' }}>Loan Type</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <x-submit-button />
                </form>
            </div>
        </div>
    </div>

    <!-- TABLE SECTION -->
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-2 pb-16">
        <x-status-message />
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            @if(isset($isDscrMatrix) && $isDscrMatrix)
            <!-- DSCR MATRIX TABLE -->
            @if (count($groupedData) > 0)
            <div class="relative overflow-x-auto rounded-lg">
                <table class="min-w-max w-full table-auto text-xs border-collapse">
                    <thead>
                        <!-- Dynamic Header for DSCR -->
                        <tr class="bg-green-800 text-white uppercase">
                            <th colspan="9" class="py-3 px-4 text-center font-bold text-lg border border-white">
                                DSCR Rental Loans - {{ $loanProgram ?? 'Loan Program #1' }} - LTV Adjustment Matrix
                            </th>
                        </tr>

                        <tr class="bg-green-800 text-white uppercase text-xs">
                            <!-- Main columns with same styling as regular matrix -->
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">Category</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">Item</th>

                            <!-- LTV Headers -->
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">50% LTV<br>or less
                            </th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">55% LTV</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">60% LTV</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">65% LTV</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">70% LTV</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">75% LTV</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">80% LTV</th>
                        </tr>
                    </thead>
                    <tbody class="text-black text-xs leading-normal">
                        @foreach ($groupedData as $rowGroup => $rows)
                        @foreach ($rows as $index => $row)
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-100 {{ $loop->parent->iteration % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            @if ($index === 0)
                            <!-- First row of each group shows the category -->
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold"
                                rowspan="{{ count($rows) }}">
                                {{ $rowGroup }}
                            </td>
                            @endif
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">{{ $row->row_label }}
                            </td>

                            <!-- LTV columns with same styling as regular matrix -->
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'50% LTV or less'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'50% LTV or less'} * 100, 3) }}%
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'55% LTV'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'55% LTV'} * 100, 3) }}%
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'60% LTV'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'60% LTV'} * 100, 3) }}%
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'65% LTV'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'65% LTV'} * 100, 3) }}%
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'70% LTV'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'70% LTV'} * 100, 3) }}%
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'75% LTV'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'75% LTV'} * 100, 3) }}%
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                @if($row->{'80% LTV'} === null)
                                N/A
                                @else
                                {{ number_format((float)$row->{'80% LTV'} * 100, 3) }}%
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-700 dark:text-gray-300 text-center py-8">
                No DSCR matrix data found for the selected filters. Please ensure the database has DSCR adjustment data.
            </p>
            @endif
            @else
            <!-- REGULAR MATRIX TABLE -->
            @if (count($matrixData) > 0)
            <div class="relative overflow-x-auto rounded-lg">
                <table class="min-w-max w-full table-auto text-xs border-collapse">
                    <thead>
                        <!-- Dynamic Header for Loan Program -->
                        <tr class="bg-green-800 text-white uppercase">
                            <th colspan="21" class="py-3 px-4 text-center font-bold text-lg border border-white">
                                @if($isQuickSearch)
                                Search Results
                                @if(isset($searchInfo['credit_score']))
                                (Credit Score: {{ $searchInfo['credit_score'] }})
                                @endif
                                @if(isset($searchInfo['experience_years']))
                                (Experience: {{ $searchInfo['experience_years'] }} years)
                                @endif
                                @else
                                @php
                                $headerText = match($currentLoanProgram) {
                                'FULL APPRAISAL' => 'Fix and Flip - FULL APPRAISAL',
                                'DESKTOP APPRAISAL' => 'Fix and Flip - DESKTOP APPRAISAL',
                                'EXPERIENCED BUILDER' => 'New Construction - EXPERIENCED BUILDER',
                                'NEW BUILDER' => 'New Construction - NEW BUILDER',
                                'Loan # 1' => 'DSCR Rental - Loan # 1',
                                default => 'Fix and Flip - FULL APPRAISAL'
                                };
                                @endphp
                                {{ $headerText }}
                                @endif
                            </th>
                        </tr>

                        @if($isQuickSearch && count($matrixData) > 0)
                        <!-- Search Result Summary -->
                        <tr class="bg-blue-100 dark:bg-blue-900">
                            <th colspan="20"
                                class="py-2 px-4 text-center text-blue-800 dark:text-blue-200 border border-gray-300">
                                @if(count($matrixData) > 0)
                                @php $firstRow = collect($matrixData)->flatten()->first(); @endphp
                                @if($firstRow)
                                Found matching rules:
                                @if(isset($searchInfo['credit_score']))
                                FICO Range: {{ $firstRow->fico }} |
                                @endif
                                @if(isset($searchInfo['experience_years']))
                                Experience Range: {{ $firstRow->experience }}
                                @endif
                                @if($firstRow->loan_program)
                                | Program: {{ $firstRow->loan_program }}
                                @endif
                                @endif
                                @endif
                            </th>
                        </tr>
                        @endif

                        <tr class="bg-green-800 text-white uppercase text-xs">
                            <!-- Sr# as first column -->
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">Sr#</th>
                            <!-- Basic Info (removed Loan Type column) -->
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">EXP</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">FICO</th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">Trans
                                <br>Type
                            </th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">Max <br>Total Loan
                            </th>
                            <th class="py-2 px-1 text-center border border-white text-xs" rowspan="2">Max <br>Budget
                            </th>

                            <!-- Rehab Headers -->
                            <th class="py-2 px-1 text-center border border-white" colspan="2">LIGHT REHAB</th>
                            <th class="py-2 px-1 text-center border border-white" colspan="2">MODERATE REHAB</th>
                            <th class="py-2 px-1 text-center border border-white" colspan="2">HEAVY REHAB</th>
                            <th class="py-2 px-1 text-center border border-white" colspan="3">EXTENSIVE REHAB</th>

                            <!-- Interest Rate Pricing -->
                            <th class="py-2 px-1 text-center border border-white" colspan="6">INTEREST RATE PRICING</th>
                        </tr>
                        <tr class="bg-green-700 text-white text-xs">
                            <!-- Rehab Sub-headers -->
                            <th class="py-1 px-1 text-center border border-white">Max LTC<br>(0-25%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br>(0-25%)
                            </th>

                            <th class="py-1 px-1 text-center border border-white">Max LTC
                                <br> (25%-50%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br> (25%-50%)
                            </th>

                            <th class="py-1 px-1 text-center border border-white">Max LTC<br> (50%-100%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br> (50%-100%)
                            </th>

                            <th class="py-1 px-1 text-center border border-white">Max LTC<br>(>100%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br>(>100%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTFC<br>
                                (>100%)
                            </th>

                            <!-- Pricing Sub-headers -->
                            <th class="py-1 px-1 text-center border border-white"><abbr
                                    title="Interest Rate">IR</abbr><br>Loan Size <br>
                                < $250K</th>
                            <th class="py-1 px-1 text-center border border-white"><abbr
                                    title="Lender Points">LP</abbr><br>Loan Size <br>
                                < $250K</th>

                            <th class="py-1 px-1 text-center border border-white"><abbr
                                    title="Interest Rate">IR</abbr><br>Loan Size
                                <br>$250K-$500K
                            </th>
                            <th class="py-1 px-1 text-center border border-white"><abbr
                                    title="Lender Points">LP</abbr><br>Loan Size
                                <br>$250K-$500K
                            </th>

                            <th class="py-1 px-1 text-center border border-white"><abbr
                                    title="Interest Rate">IR</abbr><br>Loan Size <br>≥ $500K
                            </th>
                            <th class="py-1 px-1 text-center border border-white"><abbr
                                    title="Lender Points">LP</abbr><br>Loan Size <br>≥ $500K
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-black text-xs leading-normal">
                        @php $srNumber = 1; @endphp
                        @foreach ($matrixData as $loanType => $rows)
                        @foreach ($rows as $row)
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-100 {{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <!-- Sr# as first column -->
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">
                                <a class="hover:underline text-blue-600"
                                    href="{{ route('loan-programs.edit', $row->loan_rule_id ?? 0) }}">
                                    {{ $srNumber++ }}
                                </a>

                            </td>
                            <!-- Basic Info (removed Loan Type column) -->
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">{{ $row->experience
                                ?? 0 }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">{{ $row->fico ??
                                0 }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->transaction_type ?? 0
                                }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->max_total_loan ?
                                '$' . number_format($row->max_total_loan) : '$0.00' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->max_budget ?
                                '$' . number_format($row->max_budget) : '$0.00' }}</td>

                            <!-- Light Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->light_ltc ?
                                number_format($row->light_ltc, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->light_ltv ?
                                number_format($row->light_ltv, 2) . '%' : '0.00%' }}</td>

                            <!-- Moderate Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->moderate_ltc ?
                                number_format($row->moderate_ltc, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->moderate_ltv ?
                                number_format($row->moderate_ltv, 2) . '%' : '0.00%' }}</td>

                            <!-- Heavy Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->heavy_ltc ?
                                number_format($row->heavy_ltc, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->heavy_ltv ?
                                number_format($row->heavy_ltv, 2) . '%' : '0.00%' }}</td>

                            <!-- Extensive Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->extensive_ltc ?
                                number_format($row->extensive_ltc, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->extensive_ltv ?
                                number_format($row->extensive_ltv, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->extensive_ltfc ?
                                number_format($row->extensive_ltfc, 2) . '%' : '0.00%' }}</td>

                            <!-- Pricing < $250k -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->ir_lt_250k ?
                                number_format($row->ir_lt_250k, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->lp_lt_250k ?
                                number_format($row->lp_lt_250k, 2) : '0.00' }}</td>

                            <!-- Pricing $250k-$500k -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->ir_250_500k ?
                                number_format($row->ir_250_500k, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->lp_250_500k ?
                                number_format($row->lp_250_500k, 2) : '0.00' }}</td>

                            <!-- Pricing ≥ $500k -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->ir_gte_500k ?
                                number_format($row->ir_gte_500k, 2) . '%' : '0.00%' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->lp_gte_500k ?
                                number_format($row->lp_gte_500k, 2) : '0.00' }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-700 dark:text-gray-300 text-center py-8">
                No loan program matrix data found. Please ensure the database is properly seeded.
            </p>
            @endif
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        /* Additional styles for better table readability */
        .table-auto td,
        .table-auto th {
            white-space: nowrap;
        }

        /* Responsive text sizing */
        @media (max-width: 768px) {
            .table-auto {
                font-size: 0.65rem;
            }

            .table-auto th,
            .table-auto td {
                padding: 0.25rem;
            }
        }

        /* Print styles */
        @media print {
            .table-auto {
                font-size: 0.7rem;
            }

            .table-auto th,
            .table-auto td {
                padding: 0.1rem;
                border: 1px solid #000 !important;
            }
        }
    </style>
    @endpush @push('modals')
    <script>
        const targetDiv = document.getElementById("filters");
        const btn = document.getElementById("toggle");

        function showFilters() {
            targetDiv.style.display = 'block';
            targetDiv.style.opacity = '0';
            targetDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                targetDiv.style.opacity = '1';
                targetDiv.style.transform = 'translateY(0)';
            }, 10);
        }

        function hideFilters() {
            targetDiv.style.opacity = '0';
            targetDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                targetDiv.style.display = 'none';
            }, 300);
        }

        btn.onclick = function(event) {
            event.stopPropagation();
            if (targetDiv.style.display === "none") {
                showFilters();
            } else {
                hideFilters();
            }
        };

        // Hide filters when clicking outside
        document.addEventListener('click', function(event) {
            if (targetDiv.style.display === 'block' && !targetDiv.contains(event.target) && event.target !== btn) {
                hideFilters();
            }
        });

        // Prevent clicks inside the filter from closing it
        targetDiv.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // Add CSS for smooth transitions
        const style = document.createElement('style');
        style.textContent = `#filters {transition: opacity 0.3s ease, transform 0.3s ease;}`;
        document.head.appendChild(style);
    </script>
    @endpush
</x-app-layout>