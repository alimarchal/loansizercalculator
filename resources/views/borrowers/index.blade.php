<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            @if(auth()->user()->hasRole('borrower'))
            My Loan Applications
            @else
            Borrowers Management
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
                <form method="GET" action="{{ route('borrowers.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

                        <!-- Name Search -->
                        <div>
                            <label for="filter_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                First Name
                            </label>
                            <input type="text" name="filter[name]" id="filter_name" value="{{ request('filter.name') }}"
                                placeholder="Search by first name"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Last Name Search -->
                        <div>
                            <label for="filter_last_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Last Name
                            </label>
                            <input type="text" name="filter[last_name]" id="filter_last_name"
                                value="{{ request('filter.last_name') }}" placeholder="Search by last name"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Email Search -->
                        <div>
                            <label for="filter_email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email
                            </label>
                            <input type="email" name="filter[email]" id="filter_email"
                                value="{{ request('filter.email') }}" placeholder="Search by email"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="filter_status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status
                            </label>
                            <select name="filter[status]" id="filter_status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Statuses</option>
                                @foreach($filterOptions['statuses'] as $value => $label)
                                <option value="{{ $value }}" {{ request('filter.status')==$value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Credit Score Min -->
                        <div>
                            <label for="filter_credit_score_min"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Min Credit Score
                            </label>
                            <input type="number" name="filter[credit_score_min]" id="filter_credit_score_min"
                                value="{{ request('filter.credit_score_min') }}" min="300" max="850"
                                placeholder="Min score (e.g., 650)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Credit Score Max -->
                        <div>
                            <label for="filter_credit_score_max"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Max Credit Score
                            </label>
                            <input type="number" name="filter[credit_score_max]" id="filter_credit_score_max"
                                value="{{ request('filter.credit_score_max') }}" min="300" max="850"
                                placeholder="Max score (e.g., 800)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Loan Amount Min -->
                        <div>
                            <label for="filter_loan_amount_min"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Min Loan Amount
                            </label>
                            <input type="number" name="filter[loan_amount_min]" id="filter_loan_amount_min"
                                value="{{ request('filter.loan_amount_min') }}" min="0"
                                placeholder="Min amount (e.g., 100000)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Loan Amount Max -->
                        <div>
                            <label for="filter_loan_amount_max"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Max Loan Amount
                            </label>
                            <input type="number" name="filter[loan_amount_max]" id="filter_loan_amount_max"
                                value="{{ request('filter.loan_amount_max') }}" min="0"
                                placeholder="Max amount (e.g., 500000)"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </div>

                        <!-- Property State -->
                        <div>
                            <label for="filter_property_state"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Property State
                            </label>
                            <select name="filter[property_state]" id="filter_property_state"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All States</option>
                                @foreach($filterOptions['property_states'] as $value => $label)
                                <option value="{{ $value }}" {{ request('filter.property_state')==$value ? 'selected'
                                    : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Property Type -->
                        <div>
                            <label for="filter_property_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Property Type
                            </label>
                            <select name="filter[property_type]" id="filter_property_type"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Property Types</option>
                                @foreach($filterOptions['property_types'] as $value => $label)
                                <option value="{{ $value }}" {{ request('filter.property_type')==$value ? 'selected'
                                    : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Loan Purpose -->
                        <div>
                            <label for="filter_loan_purpose"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Loan Purpose
                            </label>
                            <select name="filter[loan_purpose]" id="filter_loan_purpose"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">All Purposes</option>
                                @foreach($filterOptions['loan_purposes'] as $value => $label)
                                <option value="{{ $value }}" {{ request('filter.loan_purpose')==$value ? 'selected' : ''
                                    }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('borrowers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Clear Filters
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- STATISTICS SECTION - Only for Superadmin -->
    @if(auth()->user()->hasRole('superadmin'))
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-white">
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ number_format($stats['total_borrowers']) }}</div>
                        <div class="text-sm opacity-90">Total Borrowers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ number_format($stats['active_borrowers']) }}</div>
                        <div class="text-sm opacity-90">Active Borrowers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ number_format($stats['average_credit_score'], 0) }}</div>
                        <div class="text-sm opacity-90">Avg Credit Score</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">${{ number_format($stats['total_loan_requests']) }}</div>
                        <div class="text-sm opacity-90">Total Loan Requests</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- TABLE SECTION -->
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-2 pb-16">
        <x-status-message />
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            @if (count($borrowers) > 0)
            <div class="relative overflow-x-auto rounded-lg">
                <table class="min-w-max w-full table-auto text-xs border-collapse">
                    <thead>
                        <!-- Dynamic Header for Borrowers -->
                        <tr class="bg-green-800 text-white uppercase">
                            <th colspan="8" class="py-3 px-4 text-center font-bold text-lg border border-white">
                                @if(auth()->user()->hasRole('borrower'))
                                My Loan Applications - {{ $borrowers->total() }} Records Found
                                @else
                                Borrowers Management - {{ $borrowers->total() }} Records Found
                                @endif
                            </th>
                        </tr>

                        <tr class="bg-green-800 text-white uppercase text-xs">
                            <th class="py-2 px-1 text-center border border-white text-xs">ID</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Borrower Info</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Broker Info</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Loan Summary</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Property</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Loan Amount</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Status</th>
                            <th class="py-2 px-1 text-center border border-white text-xs">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-black text-xs leading-normal">
                        @foreach ($borrowers as $borrower)
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-100 {{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="py-1 px-1 text-center border border-gray-300 font-semibold">
                                <a class="hover:underline text-blue-600"
                                    href="{{ route('borrowers.show', $borrower) }}">
                                    {{ $borrower->id }}
                                </a>
                            </td>
                            <td class="py-2 px-2 text-left border border-gray-300 align-top">
                                <div class="space-y-1">
                                    <div class="font-semibold text-gray-900">
                                        <span class="text-xs text-gray-500">Name:</span> {{ $borrower->full_name ??
                                        'N/A' }}
                                    </div>
                                    <div class="text-sm text-blue-600">
                                        <span class="text-xs text-gray-500">Email:</span>
                                        @if($borrower->email)
                                        <a href="mailto:{{ $borrower->email }}" class="hover:underline">
                                            {{ $borrower->email }}
                                        </a>
                                        @else
                                        N/A
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Phone:</span> {{ $borrower->phone ?? 'N/A'
                                        }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Credit Score:</span>
                                        @if($borrower->credit_score)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($borrower->credit_score >= 740) bg-green-100 text-green-800
                                            @elseif($borrower->credit_score >= 680) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $borrower->credit_score }}
                                        </span>
                                        @else
                                        N/A
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Experience:</span> {{
                                        $borrower->years_of_experience ?? 'N/A' }} {{ $borrower->years_of_experience ?
                                        'years' : '' }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-2 text-left border border-gray-300 align-top">
                                <div class="space-y-1">
                                    <div class="font-semibold text-gray-900">
                                        <span class="text-xs text-gray-500">Name:</span> {{ $borrower->broker_name ??
                                        'N/A' }}
                                    </div>
                                    <div class="text-sm text-blue-600">
                                        <span class="text-xs text-gray-500">Email:</span>
                                        @if($borrower->broker_email)
                                        <a href="mailto:{{ $borrower->broker_email }}" class="hover:underline">
                                            {{ $borrower->broker_email }}
                                        </a>
                                        @else
                                        N/A
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Phone:</span> {{ $borrower->broker_phone ??
                                        'N/A' }}
                                    </div>
                                    <div class="text-sm font-medium text-green-600">
                                        <span class="text-xs text-gray-500">Points:</span> {{ $borrower->broker_points
                                        ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-2 text-left border border-gray-300 align-top">
                                <div class="space-y-1">
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Loan Type:</span> {{
                                        $borrower->selected_loan_type ??
                                        'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Transaction:</span> {{
                                        $borrower->transaction_type ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="text-xs text-gray-500">Selected Loan Program:</span> {{
                                        $borrower->selected_loan_program ?? 'N/A' }}
                                    </div>

                                </div>
                            </td>
                            <td class="py-2 px-2 text-left border border-gray-300 align-top">
                                <div class="text-sm text-gray-600">
                                    <span class="text-xs text-gray-500">Property Type:</span> {{
                                    $borrower->property_type ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="text-xs text-gray-500">Address:</span> {{
                                    $borrower->property_address ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="text-xs text-gray-500">State:</span> {{ $borrower->property_state
                                    ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="text-xs text-gray-500">Zip:</span> {{ $borrower->property_zip_code
                                    ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                {{ $borrower->loan_amount_requested ? '$' .
                                number_format($borrower->loan_amount_requested) : 'N/A' }}
                                @if($borrower->loan_purpose)
                                <br><small class="text-gray-500">{{ $borrower->loan_purpose }}</small>
                                @endif
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($borrower->status === 'active') bg-green-100 text-green-800
                                    @elseif($borrower->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($borrower->status) }}
                                </span>
                            </td>
                            <td class="py-1 px-1 text-center border border-gray-300">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('borrowers.show', $borrower) }}"
                                        class="text-blue-600 hover:text-blue-900" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('borrowers.edit', $borrower) }}"
                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $borrowers->appends(request()->query())->links() }}
            </div>
            @else
            <p class="text-gray-700 dark:text-gray-300 text-center py-8">
                No borrowers found. {{ request()->hasAny(['filter']) ? 'Try adjusting your filters.' : 'Start by adding
                some borrowers to the system.' }}
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