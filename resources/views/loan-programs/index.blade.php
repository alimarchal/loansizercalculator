<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            @if($isQuickSearch)
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
                    </div>

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
    @endpush

    @push('modals')
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