<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            DSCR Rental Loans - {{ $loanProgram }} Matrix
        </h2>

        <div class="flex justify-center items-center float-right">
            <button id="toggle"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-950 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filter
            </button>
            <a href="{{ route('loan-programs.index') }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Regular Matrix
            </a>
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
                    <input type="hidden" name="view" value="dscr-matrix">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                        <!-- Filter by FICO Band -->
                        <div>
                            <label for="fico_band_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                FICO Band
                            </label>
                            <select name="filter[fico_band_id]" id="fico_band_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All FICO Bands</option>
                                @foreach($ficoBands as $ficoBand)
                                <option value="{{ $ficoBand->id }}" {{ request('filter.fico_band_id')==$ficoBand->id ?
                                    'selected' : '' }}>
                                    {{ $ficoBand->fico_range }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter by Transaction Type -->
                        <div>
                            <label for="transaction_type_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Transaction Type
                            </label>
                            <select name="filter[transaction_type_id]" id="transaction_type_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Transaction Types</option>
                                @foreach($transactionTypes as $transactionType)
                                <option value="{{ $transactionType->id }}" {{
                                    request('filter.transaction_type_id')==$transactionType->id ? 'selected' : '' }}>
                                    {{ $transactionType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Apply Filters
                        </button>
                        <a href="{{ route('loan-programs.index', ['view' => 'dscr-matrix']) }}"
                            class="inline-flex items-center ml-2 px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DSCR MATRIX TABLE -->
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-2 pb-16">
        <x-status-message />
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            @if (count($groupedData) > 0)
            <div class="relative overflow-x-auto rounded-lg">
                <table class="min-w-max w-full table-auto text-xs border-collapse">
                    <thead>
                        <!-- Header -->
                        <tr class="bg-green-800 text-white uppercase">
                            <th class="py-3 px-2 text-left font-bold border border-white w-32">Category</th>
                            <th class="py-3 px-2 text-left font-bold border border-white w-40">Item</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">50% LTV or less</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">55% LTV</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">60% LTV</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">65% LTV</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">70% LTV</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">75% LTV</th>
                            <th class="py-3 px-2 text-center font-bold border border-white">80% LTV</th>
                        </tr>
                    </thead>
                    <tbody class="text-black text-xs leading-normal">
                        @foreach ($groupedData as $rowGroup => $rows)
                        @foreach ($rows as $index => $row)
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-100 {{ $loop->parent->iteration % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            @if ($index === 0)
                            <!-- First row of each group shows the category -->
                            <td class="py-2 px-2 border border-gray-300 font-bold bg-gray-100 align-top"
                                rowspan="{{ count($rows) }}">
                                {{ $rowGroup }}
                            </td>
                            @endif
                            <td class="py-2 px-2 border border-gray-300 font-semibold">{{ $row->row_label }}</td>

                            <!-- LTV columns with conditional styling -->
                            @php
                            $ltvColumns = [
                            '50% LTV or less' => $row->{'50% LTV or less'},
                            '55% LTV' => $row->{'55% LTV'},
                            '60% LTV' => $row->{'60% LTV'},
                            '65% LTV' => $row->{'65% LTV'},
                            '70% LTV' => $row->{'70% LTV'},
                            '75% LTV' => $row->{'75% LTV'},
                            '80% LTV' => $row->{'80% LTV'}
                            ];
                            @endphp

                            @foreach ($ltvColumns as $ltvColumn => $value)
                            <td class="py-2 px-2 text-center border border-gray-300 
                                @if ($value === null)
                                    bg-gray-200 text-gray-500
                                @elseif ($value == 0)
                                    bg-blue-100
                                @elseif ($value > 0)
                                    bg-yellow-100
                                @else
                                    bg-white
                                @endif
                            ">
                                @if ($value === null)
                                N/A
                                @elseif ($value == 0)
                                0.0000%
                                @else
                                {{ number_format((float)$value, 4) }}%
                                @endif
                            </td>
                            @endforeach
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
        </div>
    </div>

    @push('styles')
    <style>
        /* Matrix table styling */
        .table-auto td,
        .table-auto th {
            white-space: nowrap;
        }

        /* Ensure proper column widths */
        .table-auto th:first-child,
        .table-auto td:first-child {
            min-width: 120px;
        }

        .table-auto th:nth-child(2),
        .table-auto td:nth-child(2) {
            min-width: 150px;
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

        /* Color legend styles */
        .bg-yellow-100 {
            background-color: #fef3c7 !important;
        }

        .bg-blue-100 {
            background-color: #dbeafe !important;
        }

        .bg-gray-200 {
            background-color: #e5e7eb !important;
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