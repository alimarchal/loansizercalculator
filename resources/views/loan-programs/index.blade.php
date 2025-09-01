<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            Loan Programs Matrix
        </h2>

        <div class="flex justify-center items-center float-right">
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

    <!-- TABLE SECTION -->
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-2 pb-16">
        <x-status-message />
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            @if (count($matrixData) > 0)
            <div class="relative overflow-x-auto rounded-lg">
                <table class="min-w-max w-full table-auto text-xs border-collapse">
                    <thead>
                        <tr class="bg-green-800 text-white uppercase text-xs">
                            <!-- Basic Info -->
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
                        @foreach ($matrixData as $row)
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-100 {{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <!-- Basic Info -->
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">{{ $row->experience
                                ?? 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">{{ $row->fico ??
                                'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->transaction_type ?? 'N/A'
                                }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">${{ $row->max_total_loan ?
                                number_format($row->max_total_loan) : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">${{ $row->max_budget ?
                                number_format($row->max_budget) : 'N/A' }}</td>

                            <!-- Light Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->light_ltc ?
                                $row->light_ltc . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->light_ltv ?
                                $row->light_ltv . '%' : 'N/A' }}</td>

                            <!-- Moderate Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->moderate_ltc ?
                                $row->moderate_ltc . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->moderate_ltv ?
                                $row->moderate_ltv . '%' : 'N/A' }}</td>

                            <!-- Heavy Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->heavy_ltc ?
                                $row->heavy_ltc . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->heavy_ltv ?
                                $row->heavy_ltv . '%' : 'N/A' }}</td>

                            <!-- Extensive Rehab -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->extensive_ltc ?
                                $row->extensive_ltc . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->extensive_ltv ?
                                $row->extensive_ltv . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->extensive_ltfc ?
                                $row->extensive_ltfc . '%' : 'N/A' }}</td>

                            <!-- Pricing < $250k -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->ir_lt_250k ?
                                $row->ir_lt_250k . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->lp_lt_250k ?
                                $row->lp_lt_250k : 'N/A' }}</td>

                            <!-- Pricing $250k-$500k -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->ir_250_500k ?
                                $row->ir_250_500k . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->lp_250_500k ?
                                $row->lp_250_500k : 'N/A' }}</td>

                            <!-- Pricing ≥ $500k -->
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->ir_gte_500k ?
                                $row->ir_gte_500k . '%' : 'N/A' }}</td>
                            <td class="py-1 px-1 text-center border border-gray-300">{{ $row->lp_gte_500k ?
                                $row->lp_gte_500k : 'N/A' }}</td>
                        </tr>
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
</x-app-layout>