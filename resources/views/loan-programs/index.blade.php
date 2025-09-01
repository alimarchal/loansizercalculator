<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            Loan Programs Matrix
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
            <a href="javascript:window.location.reload();"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-950 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Refresh
            </a>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <!-- ACTION BUTTONS -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-3">
                <!-- Create New Button -->
                <a href="{{ route('loan-programs.create') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center transition duration-150 ease-in-out shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Loan Rule
                </a>

                <!-- Filter Toggle Button -->
                <x-filter-buttons />
            </div>

            <div class="text-sm text-gray-600 dark:text-gray-400">
                Total Rules: <span class="font-semibold">{{ count($matrixData) > 0 ? array_sum(array_map('count',
                    $matrixData)) : 0 }}</span>
            </div>
        </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg" id="filters"
            style="display: none">
            <div class="p-6">
                <form method="GET" action="{{ route('loan-programs.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Filter by Loan Type -->
                        <x-loan-type-select :loanTypes="$loanTypes" />

                        <!-- Filter by FICO Band -->
                        <x-fico-band-select :ficoBands="$ficoBands" />

                        <!-- Filter by Transaction Type -->
                        <x-transaction-type-select :transactionTypes="$transactionTypes" />
                    </div>

                    <!-- Loan Type Restrictions Info -->
                    <div id="loan-type-restrictions" class="mt-6 hidden">
                        <div
                            class="border border-gray-200 rounded-lg p-4 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">Selected Loan Program
                                Restrictions</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Available States:</h5>
                                    <div id="available-states"
                                        class="text-sm text-gray-600 dark:text-gray-400 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-500 rounded p-2 bg-white dark:bg-gray-800">
                                        Select a loan program to view available states
                                    </div>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Property Types:</h5>
                                    <div id="available-property-types"
                                        class="text-sm text-gray-600 dark:text-gray-400 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-500 rounded p-2 bg-white dark:bg-gray-800">
                                        Select a loan program to view available property types
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <x-filter-buttons />
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
                        <!-- Loan Type Header Row - Added at the top -->
                        @if (count($matrixData) > 0)
                        @foreach ($matrixData as $loanType => $rows)
                        <tr class="bg-green-800 text-white uppercase">
                            <th colspan="21" class="py-3 px-4 text-center font-bold text-lg border border-white">
                                {{ $loanType }}
                            </th>
                        </tr>
                        @endforeach
                        @endif

                        <tr class="bg-green-800 text-white uppercase text-xs">
                            <!-- Basic Info (added Loan Type column back) -->
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">Loan Type</th>
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">Experience</th>
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">FICO</th>
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">Transaction Type</th>
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">Max Total Loan</th>
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">Max Budget</th>

                            <!-- Rehab Headers -->
                            <th class="py-2 px-1 text-center border border-white" colspan="2">LIGHT REHAB</th>
                            <th class="py-2 px-1 text-center border border-white" colspan="2">MODERATE REHAB</th>
                            <th class="py-2 px-1 text-center border border-white" colspan="2">HEAVY REHAB</th>
                            <th class="py-2 px-1 text-center border border-white" colspan="3">EXTENSIVE REHAB</th>

                            <!-- Interest Rate Pricing -->
                            <th class="py-2 px-1 text-center border border-white" colspan="6">INTEREST RATE PRICING</th>

                            <!-- Actions -->
                            <th class="py-2 px-1 text-center border border-white" rowspan="2">Actions</th>
                        </tr>
                        <tr class="bg-green-700 text-white text-xs">
                            <!-- Rehab Sub-headers -->
                            <th class="py-1 px-1 text-center border border-white">Max LTC<br>Light Rehab (0-25%)</th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br>Light Rehab (0-25%)</th>

                            <th class="py-1 px-1 text-center border border-white">Max LTC<br>Moderate Rehab (25%-50%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br>Moderate Rehab (25%-50%)
                            </th>

                            <th class="py-1 px-1 text-center border border-white">Max LTC<br>Heavy Rehab (50%-100%)</th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br>Heavy Rehab (50%-100%)</th>

                            <th class="py-1 px-1 text-center border border-white">Max LTC<br>Extensive Rehab (>100%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTV<br>Extensive Rehab (>100%)
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Max LTFC<br>Extensive Rehab (>100%)
                            </th>

                            <!-- Pricing Sub-headers -->
                            <th class="py-1 px-1 text-center border border-white">Interest Rate<br>Loan Size <
                                    $250K</th>
                            <th class="py-1 px-1 text-center border border-white">Lender Points<br>Loan Size <
                                    $250K</th>

                            <th class="py-1 px-1 text-center border border-white">Interest Rate<br>Loan Size $250K-$500K
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Lender Points<br>Loan Size $250K-$500K
                            </th>

                            <th class="py-1 px-1 text-center border border-white">Interest Rate<br>Loan Size ≥ $500K
                            </th>
                            <th class="py-1 px-1 text-center border border-white">Lender Points<br>Loan Size ≥ $500K
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-black text-xs leading-normal">
                        @foreach ($matrixData as $loanType => $rows)
                        @foreach ($rows as $row)
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-100 {{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <!-- Basic Info (added Loan Type column back) -->
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">
                                {{ $row->loan_type ?? 0 }}
                                @if($row->loan_program)
                                <br><span class="text-xs text-gray-600">{{ $row->loan_program }}</span>
                                @endif
                            </td>
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

                            <!-- Actions -->
                            <td class="py-1 px-1 text-center border border-gray-300">
                                <a href="{{ route('loan-programs.edit', $row->loan_rule_id ?? 0) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                    Edit
                                </a>
                            </td>
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

        // Handle loan type selection for restrictions
        const loanTypeSelect = document.getElementById('loan_type');
        const restrictionsDiv = document.getElementById('loan-type-restrictions');
        const statesDiv = document.getElementById('available-states');
        const propertyTypesDiv = document.getElementById('available-property-types');

        if (loanTypeSelect) {
            loanTypeSelect.addEventListener('change', function() {
                const loanTypeId = this.value;
                
                if (!loanTypeId) {
                    restrictionsDiv.classList.add('hidden');
                    return;
                }
                
                // Show loading state
                statesDiv.innerHTML = 'Loading...';
                propertyTypesDiv.innerHTML = 'Loading...';
                restrictionsDiv.classList.remove('hidden');
                
                // Fetch restrictions
                fetch(`{{ route('loan-programs.restrictions') }}?loan_type_id=${loanTypeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            statesDiv.innerHTML = `Error: ${data.error}`;
                            propertyTypesDiv.innerHTML = `Error: ${data.error}`;
                            return;
                        }
                        
                        // Display states
                        if (data.states && data.states.length > 0) {
                            const statesList = data.states.map(state => `<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">${state.code}</span>`).join('');
                            statesDiv.innerHTML = `<div class="flex flex-wrap">${statesList}</div><p class="text-xs text-gray-500 mt-2">${data.states.length} states available</p>`;
                        } else {
                            statesDiv.innerHTML = '<span class="text-red-500">No states available</span>';
                        }
                        
                        // Display property types  
                        if (data.property_types && data.property_types.length > 0) {
                            const propertyTypesList = data.property_types.map(pt => `<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mb-1">${pt.name}</span>`).join('');
                            propertyTypesDiv.innerHTML = `<div class="flex flex-wrap">${propertyTypesList}</div><p class="text-xs text-gray-500 mt-2">${data.property_types.length} property types available</p>`;
                        } else {
                            propertyTypesDiv.innerHTML = '<span class="text-red-500">No property types available</span>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching restrictions:', error);
                        statesDiv.innerHTML = 'Error loading states';
                        propertyTypesDiv.innerHTML = 'Error loading property types';
                    });
            });
        }
    </script>
    @endpush
</x-app-layout>