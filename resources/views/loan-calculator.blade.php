<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculator</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="{{ url('apexcharts/apexcharts.js') }}"></script>
    <link href="{{ url('select2/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ url('jsandcss/daterangepicker.min.css') }}">
    <script src="{{ url('jsandcss/moment.min.js') }}"></script>
    <script src="{{ url('jsandcss/knockout-3.5.1.js') }}" defer></script>
    <script src="{{ url('jsandcss/daterangepicker.min.js') }}" defer></script>

    <!-- jsPDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    @stack('header')

    <style>
        .select2 {
            /*width:100%!important;*/
            width: auto !important;
            display: block;
        }

        .select2-container .select2-selection--single {
            height: auto;
            /* Reset the height if necessary */
            padding: 0.7rem 1rem;
            /* This should match Tailwind's py-2 px-4 */
            line-height: 1.25;
            /* Adjust based on Tailwind's line height for consistency */
            /*font-size: 0.875rem; !* Matches Tailwind's text-sm *!*/
            border: 1px solid #d1d5db;
            /* Tailwind's border-gray-300 */
            border-radius: 0.375rem;
            /* Tailwind's rounded-md */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            /* Tailwind's shadow-sm */
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 1.25;
            /* Aligns text vertically */
            padding-left: 0;
            /* Adjust if needed */
            padding-right: 0;
            /* Adjust if needed */
        }

        /*.select2-selection__arrow*/
        .select2-container .select2-selection--single {
            height: auto;
            /* Ensure the arrow aligns with the adjusted height */
            right: 0.5rem;
            /* Align the arrow similarly to Tailwind's padding */

        }

        .select2-selection__arrow {
            top: 8px !important;
            right: 10px !important;
        }

        /* Closing Statement Styles */
        #closingStatementSection .bg-blue-600 {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        /* Editable Loan Amount Input Styles */
        .loan-amount-input {
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
            border: 2px dashed #3b82f6;
            cursor: pointer;
        }

        .loan-amount-input:hover {
            background-color: #ffffff;
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: scale(1.02);
        }

        .loan-amount-input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: none;
            transform: scale(1.02);
        }

        .loan-amount-input:invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        #closingStatementSection table td {
            vertical-align: middle;
        }

        #closingStatementSection .font-bold {
            font-weight: 700;
        }

        /* Highlight important rows */
        #closingStatementSection tr.bg-gray-50 {
            background-color: #f9fafb;
        }

        #closingStatementSection tr.bg-gray-200 {
            background-color: #e5e7eb;
        }

        /* Hover effect for table rows */
        #closingStatementSection tbody tr:hover {
            background-color: #f3f4f6;
        }

        /* Responsive adjustments for two-column layout */
        @media (max-width: 1279px) {
            .grid.xl\\:grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }

        /* Compact table styling for smaller screens */
        @media (max-width: 768px) {

            #resultsSection table th,
            #resultsSection table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
            }

            #closingStatementSection table th,
            #closingStatementSection table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.875rem;
            }
        }

        /* Enhanced form section transitions and animations */
        .form-section {
            transition: all 0.3s ease-in-out;
        }

        .form-section.hidden {
            opacity: 0;
            transform: translateX(20px);
            display: none;
        }

        .form-section:not(.hidden) {
            opacity: 1;
            transform: translateX(0);
            display: block;
        }

        /* Progress indicators */
        .step-circle-active {
            background-color: #2563eb !important;
            color: white !important;
        }

        .step-circle-completed {
            background-color: #10b981 !important;
            color: white !important;
        }

        .step-circle-inactive {
            background-color: #e5e7eb !important;
            color: #6b7280 !important;
        }

        /* Enhanced input hover effects */
        input:hover,
        select:hover {
            border-color: #9ca3af !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        }

        /* Card hover effects */
        .form-section .bg-white {
            transition: box-shadow 0.3s ease-in-out;
        }

        .form-section .bg-white:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        /* Icon animations */
        svg {
            transition: transform 0.2s ease-in-out;
        }

        label:hover svg {
            transform: scale(1.1);
        }

        /* Button animations */
        button {
            transition: all 0.2s ease-in-out;
        }

        button:hover {
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .grid.lg\\:grid-cols-3 {
                grid-template-columns: 1fr;
            }

            .grid.lg\\:grid-cols-4 {
                grid-template-columns: 1fr 1fr;
            }

            .flex.items-center.space-x-4 {
                flex-direction: column;
                space-x: 0;
                gap: 1rem;
            }

            .w-16 {
                width: 100%;
                height: 4px;
                max-width: 100px;
            }

            .px-6.py-3 {
                padding: 0.5rem 1rem;
                font-size: 0.75rem;
            }

            .text-xl {
                font-size: 1.125rem;
            }

            .p-6 {
                padding: 1rem;
            }
        }

        /* Extra small screen adjustments */
        @media (max-width: 480px) {
            .grid.md\\:grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .flex.space-x-4 {
                flex-direction: column;
                gap: 1rem;
            }

            .justify-between {
                justify-content: center;
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles



    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <!-- Header -->
                    <div class="mb-6 mx-auto text-center">
                        <h1 class="text-2xl font-bold text-gray-900 mx-auto">Structure My Loan Calculator</h1>
                        <p class="text-gray-600 mt-2">Input your loan summary and request a Pre Approval!</p>
                    </div>

                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <div class="flex items-center justify-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div id="step1-circle"
                                        class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold text-sm">
                                        1
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-blue-600">Step 1</div>
                                        <div class="text-xs text-gray-500">Basic Information</div>
                                    </div>
                                </div>
                                <div class="w-16 h-1 bg-gray-200 rounded">
                                    <div id="progress1-2" class="h-1 bg-blue-600 rounded transition-all duration-300"
                                        style="width: 0%"></div>
                                </div>
                                <div class="flex items-center">
                                    <div id="step2-circle"
                                        class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-semibold text-sm">
                                        2
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-400">Step 2</div>
                                        <div class="text-xs text-gray-400">Loan & Property</div>
                                    </div>
                                </div>
                                <div class="w-16 h-1 bg-gray-200 rounded">
                                    <div id="progress2-3" class="h-1 bg-blue-600 rounded transition-all duration-300"
                                        style="width: 0%"></div>
                                </div>
                                <div class="flex items-center">
                                    <div id="step3-circle"
                                        class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-semibold text-sm">
                                        3
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-400">Step 3</div>
                                        <div class="text-xs text-gray-400">Advanced Options</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Form -->
                    <form id="loanCalculatorForm" method="POST" action="/calculate-loan">
                        @csrf

                        <!-- Section 1: Basic Information -->
                        <div id="section1" class="form-section">
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
                                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-4">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-white">Basic Information</h2>
                                            <p class="text-blue-100 text-sm">Tell us about yourself and your broker</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <!-- Borrower Information -->
                                    <div class="mb-8">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            Borrower Information
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Credit Score -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="credit_score">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                        Credit Score <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="credit_score" id="credit_score" min="300"
                                                    max="850" value="700" placeholder="Enter credit score" required>
                                            </div>

                                            <!-- Experience -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="experience">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-purple-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Experience (# of Deals) <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="experience" id="experience" min="0" max="50"
                                                    value="4" placeholder="Enter number of deals" required>
                                            </div>

                                            <!-- Borrower Name -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="borrower_name">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-indigo-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                        Full Name
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="text" name="borrower_name" id="borrower_name" value=""
                                                    placeholder="Enter your full name">
                                            </div>

                                            <!-- Borrower Email -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="borrower_email">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-red-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        Email Address
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="email" name="borrower_email" id="borrower_email" value=""
                                                    placeholder="Enter your email address">
                                            </div>

                                            <!-- Borrower Phone -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="borrower_phone">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                            </path>
                                                        </svg>
                                                        Phone Number
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="tel" name="borrower_phone" id="borrower_phone" value=""
                                                    placeholder="Enter your phone number">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Broker Information -->
                                    <div class="border-t border-gray-200 pt-8">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H10a2 2 0 00-2-2V4">
                                                </path>
                                            </svg>
                                            Broker Information
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                            <!-- Broker Name -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="broker_name">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-orange-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                        Broker Name
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="text" name="broker_name" id="broker_name" value=""
                                                    placeholder="Enter broker name">
                                            </div>

                                            <!-- Broker Email -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="broker_email">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-red-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        Broker Email
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="email" name="broker_email" id="broker_email" value=""
                                                    placeholder="Enter broker email">
                                            </div>

                                            <!-- Broker Phone -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="broker_phone">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                            </path>
                                                        </svg>
                                                        Broker Phone
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="tel" name="broker_phone" id="broker_phone" value=""
                                                    placeholder="Enter broker phone">
                                            </div>

                                            <!-- Broker Points -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="broker_points">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-yellow-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                            </path>
                                                        </svg>
                                                        Broker Points (%)
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="broker_points" id="broker_points" min="0"
                                                    max="10" step="0.1" value="0" placeholder="Enter points percentage">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Navigation -->
                                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                                        <button type="button" onclick="nextSection(2)"
                                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Continue to Property Details
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Loan & Property Details -->
                        <div id="section2" class="form-section hidden">
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
                                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-4">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-white">Loan & Property Details</h2>
                                            <p class="text-green-100 text-sm">Configure your loan parameters and
                                                property information</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <!-- Loan Configuration -->
                                    <div class="mb-8">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                            Loan Configuration
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Loan Type -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="loan_type">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-blue-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                            </path>
                                                        </svg>
                                                        Loan Type <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="loan_type" id="loan_type"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200"
                                                    required>
                                                    <option value="">-- Select Loan Type --</option>
                                                    @foreach($loanTypes as $loanType)
                                                    <option value="{{ $loanType->name }}">{{ $loanType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Transaction Type -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="transaction_type">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-purple-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                                                            </path>
                                                        </svg>
                                                        Transaction Type <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="transaction_type" id="transaction_type"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200"
                                                    required>
                                                    <option value="">-- Select Transaction Type --</option>
                                                    @foreach($transactionTypes as $transactionType)
                                                    @if(in_array($transactionType->id, [1,2,3]))
                                                    <option value="{{ $transactionType->name }}">
                                                        {{ $transactionType->name }}
                                                    </option>
                                                    @endif
                                                    @endforeach

                                                </select>
                                            </div>

                                            <!-- Loan Term -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="loan_term">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-indigo-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Loan Term <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="loan_term" id="loan_term"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200"
                                                    required>
                                                    <option value="">-- Select Loan Term --</option>
                                                    <option value="12">12 Months</option>
                                                    <option value="18">18 Months</option>
                                                </select>
                                            </div>

                                            <!-- GUC Experience (only for New Construction) -->
                                            <div id="guc_experience_field" style="display: none;">
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="guc_experience">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                        GUC Experience (# of Deals) <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="guc_experience" id="guc_experience" min="0"
                                                    max="100" value="0" placeholder="Enter GUC experience deals">
                                                <div class="mt-2 text-sm text-gray-600">
                                                    <p><strong>Program Qualification:</strong></p>
                                                    <p>• 2+ deals: Qualified for <strong>Experienced Builder</strong> &
                                                        <strong>New Builder</strong> programs
                                                    </p>
                                                    <p>• Less than 2 deals: Qualified for <strong>New Builder</strong>
                                                        program only</p>
                                                </div>
                                            </div>

                                            <!-- Payoff Amount (only for New Construction + Refinance) -->
                                            <div id="new_construction_payoff_amount_field" style="display: none;">
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="new_construction_payoff_amount">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-red-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                            </path>
                                                        </svg>
                                                        Payoff Amount <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="new_construction_payoff_amount"
                                                    id="new_construction_payoff_amount" min="0" step="0.01" value=""
                                                    placeholder="Enter payoff amount">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Property Information -->
                                    <div class="border-t border-gray-200 pt-8 mb-8">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 text-cyan-600 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                </path>
                                            </svg>
                                            Property Information
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                            <!-- Property Type -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="property_type">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-cyan-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v2">
                                                            </path>
                                                        </svg>
                                                        Property Type <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="property_type" id="property_type"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200"
                                                    required>
                                                    <option value="">-- Select Loan Type First --</option>
                                                </select>
                                            </div>

                                            <!-- State -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2" for="state">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-red-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                        </svg>
                                                        State <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="state" id="state"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200"
                                                    required>
                                                    <option value="">-- Select Loan Type First --</option>
                                                </select>
                                            </div>

                                            <!-- Property Address -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="property_address">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-gray-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                            </path>
                                                        </svg>
                                                        Property Address
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="text" name="property_address" id="property_address" value=""
                                                    placeholder="Enter property address">
                                            </div>

                                            <!-- Permit Status (only for New Construction) -->
                                            <div id="permit_status_field" style="display: none;">
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="permit_status">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                        Permit Status <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="permit_status" id="permit_status"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200">
                                                    <option value="">-- Select Permit Status --</option>
                                                    <option value="Permit Approved">Permit Approved</option>
                                                    <option value="Unpermitted">Unpermitted</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Financial Details -->
                                    <div class="border-t border-gray-200 pt-8">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                            Financial Details
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Purchase Price -->
                                            <div>
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="purchase_price">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                            </path>
                                                        </svg>
                                                        Purchase Price <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="purchase_price" id="purchase_price" min="0"
                                                    value="90000" placeholder="Enter purchase price" required>
                                            </div>

                                            <!-- Rehab Budget -->
                                            <div id="rehab_budget_field">
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="rehab_budget">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-orange-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                                            </path>
                                                        </svg>
                                                        Rehab Budget <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="rehab_budget" id="rehab_budget" min="0"
                                                    value="40000" placeholder="Enter rehab budget" required>
                                            </div>

                                            <!-- Seasoning Period (Fix and Flip Refinance only) -->
                                            <div id="fix_flip_seasoning_field" class="hidden">
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="fix_flip_seasoning_period">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-purple-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                        Seasoning Period <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <select name="fix_flip_seasoning_period" id="fix_flip_seasoning_period"
                                                    class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200">
                                                    <option value="">-- Select Seasoning Period --</option>
                                                    <option value="Owned < 6 Months">Owned < 6 Months</option>
                                                    <option value="Owned < 12 Months">Owned < 12 Months</option>
                                                    <option value="Owned 12+ Months">Owned 12+ Months</option>
                                                </select>
                                            </div>

                                            <!-- Payoff Amount (Fix and Flip Refinance only) -->
                                            <div id="fix_flip_payoff_amount_field" class="hidden">
                                                <label class="block font-medium text-sm text-gray-700 mb-2"
                                                    for="fix_flip_payoff_amount">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                            </path>
                                                        </svg>
                                                        Payoff Amount <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="fix_flip_payoff_amount"
                                                    id="fix_flip_payoff_amount" min="0" step="0.01" value=""
                                                    placeholder="Enter payoff amount">
                                            </div>

                                            <!-- ARV -->
                                            <div id="arv_field">
                                                <label class="block font-medium text-sm text-gray-700 mb-2" for="arv">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 text-blue-600 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
                                                            </path>
                                                        </svg>
                                                        ARV (After Repair Value) <span class="text-red-500">*</span>
                                                    </span>
                                                </label>
                                                <input
                                                    class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                    type="number" name="arv" id="arv" min="0" value="250000"
                                                    placeholder="Enter ARV amount" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Navigation -->
                                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                                        <button type="button" onclick="previousSection(1)"
                                            class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                            </svg>
                                            Back to Basic Info
                                        </button>
                                        <button type="button" onclick="nextSection(3)"
                                            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Continue to Rental Information
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Advanced Options -->
                        <div id="section3" class="form-section hidden">
                            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
                                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-4">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-white">Advanced Options</h2>
                                            <p class="text-purple-100 text-sm">Additional loan parameters and property
                                                details</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <!-- DSCR Rental Loan Specific Fields -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <!-- Occupancy Type (DSCR only) -->
                                        <div id="occupancy_type_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="occupancy_type">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-purple-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v2">
                                                        </path>
                                                    </svg>
                                                    Occupancy Type <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <select name="occupancy_type" id="occupancy_type"
                                                class="block w-full select2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 transition duration-200">
                                                <option value="">-- Select Occupancy Type --</option>
                                            </select>
                                        </div>

                                        <!-- Monthly Market Rent (DSCR only) -->
                                        <div id="monthly_market_rent_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="monthly_market_rent">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                        </path>
                                                    </svg>
                                                    Monthly Market Rent <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                type="number" name="monthly_market_rent" id="monthly_market_rent"
                                                min="0" value="" placeholder="Enter monthly market rent">
                                        </div>

                                        <!-- Annual Tax (DSCR only) -->
                                        <div id="annual_tax_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="annual_tax">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-red-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    Annual Tax <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                type="number" name="annual_tax" id="annual_tax" min="0" value=""
                                                placeholder="Enter annual tax amount">
                                        </div>

                                        <!-- Annual Insurance (DSCR only) -->
                                        <div id="annual_insurance_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="annual_insurance">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-blue-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                                        </path>
                                                    </svg>
                                                    Annual Insurance <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                type="number" name="annual_insurance" id="annual_insurance" min="0"
                                                value="" placeholder="Enter annual insurance amount">
                                        </div>

                                        <!-- Annual HOA (DSCR only) -->
                                        <div id="annual_hoa_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="annual_hoa">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-indigo-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                    Annual HOA <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                type="number" name="annual_hoa" id="annual_hoa" min="0" value=""
                                                placeholder="Enter annual HOA amount">
                                        </div>

                                        <!-- Purchase Date (DSCR only) -->
                                        <div id="purchase_date_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="purchase_date">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    Purchase Date
                                                </span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                type="date" name="purchase_date" id="purchase_date" value="">
                                        </div>

                                        <!-- Payoff Amount (DSCR only) -->
                                        <div id="payoff_amount_field" class="hidden">
                                            <label class="block font-medium text-sm text-gray-700 mb-2"
                                                for="payoff_amount">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 text-orange-600 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                        </path>
                                                    </svg>
                                                    Payoff Amount <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm block w-full transition duration-200 hover:border-gray-400"
                                                type="number" name="payoff_amount" id="payoff_amount" min="0" value=""
                                                placeholder="Enter payoff amount">
                                        </div>
                                    </div>

                                    <!-- Additional Information -->
                                    <div
                                        class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <h4 class="text-sm font-semibold text-gray-800">Information</h4>
                                        </div>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            These advanced options are specifically for DSCR (Debt Service Coverage
                                            Ratio) rental loans.
                                            The fields will automatically appear when you select "DSCR Rental Loans" as
                                            your loan type in the previous section.
                                        </p>
                                    </div>

                                    <!-- Final Action Buttons -->
                                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                                        <button type="button" onclick="previousSection(2)"
                                            class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                            </svg>
                                            Back to Loan Details
                                        </button>
                                        <div class="flex space-x-4">
                                            <button type="submit" id="calculateBtn"
                                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-blue-700 hover:to-blue-800 focus:from-blue-700 focus:to-blue-800 active:from-blue-900 active:to-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                Calculate Loan Options
                                            </button>
                                            <button type="reset"
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-gray-700 hover:to-gray-800 focus:from-gray-700 focus:to-gray-800 active:from-gray-900 active:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Clear All Fields
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form> <!-- Results Section -->
                    <div class="mt-8">
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="hidden text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-gray-600 text-sm mt-2">Calculating loan options...</p>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage"
                            class="hidden mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            <div class="flex">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span id="errorText"></span>
                            </div>
                        </div>

                        <!-- Results Section (Hidden until calculation) -->
                        <div id="resultsAndClosingSection" class="hidden">
                            <!-- Loan Program Results - Full Width -->
                            <div id="resultsSection" class="mb-8">
                                <div class="text-center mb-6">
                                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Loan Program Results</h2>
                                    <p class="text-gray-600">Compare loan programs and select the best option for your
                                        needs</p>
                                </div>

                                <!-- Enhanced Results Table -->
                                <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200">
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead id="tableHeader"
                                                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                                <!-- Dynamic header will be populated here -->
                                            </thead>
                                            <tbody id="loanResultsTable" class="divide-y divide-gray-200">
                                                <!-- Dynamic rows will be populated here after calculation -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> <!-- End Loan Program Results -->

                            <!-- Loan Program Selection Cards -->
                            <div id="loanProgramCards" class="mb-8">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">Select Your Loan Program</h2>
                                <div id="loanProgramCardsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Dynamic loan program cards will be populated here -->
                                </div>
                            </div>

                            <!-- Estimated Closing Statement - Beautiful Cards Layout -->
                            <div id="closingStatementSection" class="hidden">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6">Estimated Closing Statement</h2>

                                <!-- Cards Grid Layout -->
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                                    <!-- Loan Amount Card -->
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg border-l-4 border-blue-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-blue-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-dollar-sign text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-blue-800">Loan Amounts</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span id="closingPurchaseLoanLabel"
                                                    class="text-sm text-gray-600">Purchase Loan:</span>
                                                <span id="closingPurchaseLoan" class="font-bold text-blue-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Rehab Loan:</span>
                                                <span id="closingRehabLoan" class="font-bold text-green-700">$0</span>
                                            </div>
                                            <div class="border-t pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold text-gray-700">Total Loan:</span>
                                                    <span id="closingTotalLoan"
                                                        class="font-bold text-lg text-purple-700">$0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Property Costs Card -->
                                    <div
                                        class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg border-l-4 border-green-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-green-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-home text-white text-lg"></i>
                                            </div>
                                            <h3 id="closingPropertyCostsTitle" class="text-lg font-bold text-green-800">
                                                Property Costs</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Purchase Price:</span>
                                                <span id="closingPurchasePrice"
                                                    class="font-bold text-green-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Rehab Budget:</span>
                                                <span id="closingRehabBudget" class="font-bold text-green-700">$0</span>
                                            </div>
                                            <div id="closingSubtotalRow" class="border-t pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold text-gray-700">Subtotal:</span>
                                                    <span id="closingSubtotalBuyer"
                                                        class="font-bold text-lg text-green-700">$0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lender Fees Card -->
                                    <div
                                        class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-lg border-l-4 border-orange-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-orange-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-university text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-orange-800">Lender Fees</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Origination Fee:</span>
                                                <span id="closingOriginationFee"
                                                    class="font-bold text-orange-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Broker Fee:</span>
                                                <span id="closingBrokerFee" class="font-bold text-orange-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Underwriting:</span>
                                                <span id="closingUnderwritingFee"
                                                    class="font-bold text-orange-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Interest Reserves:</span>
                                                <span id="closingInterestReserves"
                                                    class="font-bold text-orange-700">$0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Final Summary Card -->
                                    <div
                                        class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg border-l-4 border-purple-500 p-6 hover:shadow-xl transition-shadow duration-300">
                                        <div class="flex items-center mb-4">
                                            <div class="bg-purple-500 p-3 rounded-full mr-3">
                                                <i class="fas fa-calculator text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-purple-800">Other Costs</h3>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Title Charges:</span>
                                                <span id="closingTitleCharges"
                                                    class="font-bold text-purple-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Insurance:</span>
                                                <span id="closingPropertyInsurance"
                                                    class="font-bold text-purple-700">$0</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Legal/Doc Fee:</span>
                                                <span id="closingLegalDocFee"
                                                    class="font-bold text-purple-700">$0</span>
                                            </div>
                                            <div class="border-t pt-3">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold text-gray-700">Closing Costs:</span>
                                                    <span id="closingSubtotalCosts"
                                                        class="font-bold text-lg text-purple-700">$0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Cash Due Summary Card -->
                                <div
                                    class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl shadow-xl p-6 text-white mb-8">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                                                <i class="fas fa-money-bill-wave text-2xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold">Due From Buyer at Closing</h3>
                                                <p class="text-red-100 text-sm">Total amount you need to bring to
                                                    closing</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span id="closingCashDue" class="text-3xl font-bold">$0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button onclick="downloadLoanPDF()"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-lg font-semibold shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i>
                                        Download PDF
                                    </button>
                                    <button onclick="startApplication()" id="startApplicationBtn"
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-semibold shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Start Application
                                    </button>
                                </div>
                            </div> <!-- End Estimated Closing Statement -->

                        </div> <!-- End Results and Closing Section -->

                        <script>
                            // Section Navigation Functions
                        let currentSection = 1;

                        function showSection(sectionNumber) {
                            // Hide all sections
                            for(let i = 1; i <= 3; i++) {
                                const section = document.getElementById(`section${i}`);
                                if(section) {
                                    section.classList.add('hidden');
                                }
                            }
                            
                            // Show target section
                            const targetSection = document.getElementById(`section${sectionNumber}`);
                            if(targetSection) {
                                targetSection.classList.remove('hidden');
                            }
                            
                            currentSection = sectionNumber;
                            updateProgressIndicators();
                        }

                        function nextSection(sectionNumber) {
                            // Basic validation before moving to next section
                            if (currentSection === 1 && !validateSection1()) {
                                return;
                            }
                            if (currentSection === 2 && !validateSection2()) {
                                return;
                            }
                            
                            showSection(sectionNumber);
                            scrollToTop();
                        }

                        function previousSection(sectionNumber) {
                            showSection(sectionNumber);
                            scrollToTop();
                        }

                        function validateSection1() {
                            const creditScore = document.getElementById('credit_score').value;
                            const experience = document.getElementById('experience').value;
                            
                            if (!creditScore || !experience) {
                                alert('Please fill in all required fields in the Basic Information section.');
                                return false;
                            }
                            return true;
                        }

                        function validateSection2() {
                            const loanType = document.getElementById('loan_type').value;
                            const transactionType = document.getElementById('transaction_type').value;
                            const loanTerm = document.getElementById('loan_term').value;
                            const propertyType = document.getElementById('property_type').value;
                            const state = document.getElementById('state').value;
                            const purchasePrice = document.getElementById('purchase_price').value;
                            
                            // Check if it's a DSCR loan (loan term not required for DSCR)
                            const isDscrLoan = loanType === 'DSCR Rental Loans';
                            
                            console.log('Validation Debug:', {
                                loanType,
                                transactionType, 
                                loanTerm,
                                propertyType,
                                state,
                                purchasePrice,
                                isDscrLoan
                            });
                            
                            // For DSCR loans, validate basic fields + DSCR-specific required fields
                            if (isDscrLoan) {
                                // Basic fields required for DSCR
                                if (!loanType || !transactionType || !propertyType || !state || !purchasePrice) {
                                    console.log('DSCR validation failed - missing basic fields');
                                    alert('Please fill in all required fields in the Loan & Property Details section.');
                                    return false;
                                }
                                
                                // DSCR specific required fields (from Advanced Options section)
                                const occupancyType = document.getElementById('occupancy_type').value;
                                const monthlyMarketRent = document.getElementById('monthly_market_rent').value;
                                const annualTax = document.getElementById('annual_tax').value;
                                const annualInsurance = document.getElementById('annual_insurance').value;
                                const annualHoa = document.getElementById('annual_hoa').value;
                                
                                // Note: For section 2 validation, we only check basic fields
                                // DSCR-specific fields will be validated in section 3
                                
                            } else {
                                // For regular loans, require all fields including loan term
                                const rehabBudget = document.getElementById('rehab_budget').value;
                                const arv = document.getElementById('arv').value;
                                
                                // Check for New Construction GUC experience requirement
                                const isNewConstruction = loanType === 'New Construction';
                                const gucExperience = document.getElementById('guc_experience').value;
                                
                                if (!loanType || !transactionType || !loanTerm || !propertyType || !state || !purchasePrice || !rehabBudget || !arv) {
                                    console.log('Regular loan validation failed - missing fields');
                                    alert('Please fill in all required fields in the Loan & Property Details section.');
                                    return false;
                                }
                                
                                // Additional validation for New Construction loans
                                if (isNewConstruction && (!gucExperience || gucExperience === '')) {
                                    console.log('New Construction validation failed - missing GUC experience');
                                    alert('Please enter GUC Experience (# of Deals) for New Construction loans.');
                                    return false;
                                }
                                
                                // Validate permit status for New Construction
                                if (isNewConstruction) {
                                    const permitStatus = document.getElementById('permit_status').value;
                                    if (!permitStatus) {
                                        console.log('New Construction validation failed - missing permit status');
                                        alert('Please select Permit Status for New Construction loans.');
                                        return false;
                                    }
                                }
                                
                                // Validate payoff amount for New Construction refinance transactions
                                if (isNewConstruction && (transactionType === 'Refinance' || transactionType === 'Refinance Cash Out')) {
                                    const payoffAmount = document.getElementById('new_construction_payoff_amount').value;
                                    if (!payoffAmount || payoffAmount === '') {
                                        console.log('New Construction refinance validation failed - missing payoff amount');
                                        alert('Please enter Payoff Amount for New Construction refinance transactions.');
                                        return false;
                                    }
                                }
                                
                                // Additional validation for Fix and Flip refinance transactions
                                const isFixAndFlip = loanType === 'Fix and Flip';
                                if (isFixAndFlip && (transactionType === 'Refinance' || transactionType === 'Refinance Cash Out')) {
                                    const seasoningPeriod = document.getElementById('fix_flip_seasoning_period').value;
                                    const payoffAmount = document.getElementById('fix_flip_payoff_amount').value;
                                    
                                    if (!seasoningPeriod) {
                                        console.log('Fix and Flip refinance validation failed - missing seasoning period');
                                        alert('Please select Seasoning Period for Fix and Flip refinance transactions.');
                                        return false;
                                    }
                                    
                                    if (!payoffAmount || payoffAmount === '') {
                                        console.log('Fix and Flip refinance validation failed - missing payoff amount');
                                        alert('Please enter Payoff Amount for Fix and Flip refinance transactions.');
                                        return false;
                                    }
                                }
                            }
                            console.log('Validation passed');
                            return true;
                        }

                        function updateProgressIndicators() {
                            // Update step circles
                            for(let i = 1; i <= 3; i++) {
                                const circle = document.getElementById(`step${i}-circle`);
                                const stepLabel = circle.nextElementSibling.firstElementChild;
                                const stepDescription = circle.nextElementSibling.lastElementChild;
                                
                                if(i < currentSection) {
                                    // Completed step
                                    circle.className = circle.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
                                    circle.classList.add('bg-green-600', 'text-white');
                                    circle.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                    stepLabel.className = 'text-sm font-medium text-green-600';
                                    stepDescription.className = 'text-xs text-green-500';
                                } else if(i === currentSection) {
                                    // Active step
                                    circle.className = circle.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
                                    circle.classList.add('bg-blue-600', 'text-white');
                                    circle.innerHTML = i;
                                    stepLabel.className = 'text-sm font-medium text-blue-600';
                                    stepDescription.className = 'text-xs text-blue-500';
                                } else {
                                    // Inactive step
                                    circle.className = circle.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
                                    circle.classList.add('bg-gray-300', 'text-gray-600');
                                    circle.innerHTML = i;
                                    stepLabel.className = 'text-sm font-medium text-gray-400';
                                    stepDescription.className = 'text-xs text-gray-400';
                                }
                            }
                            
                            // Update progress bars
                            const progress1to2 = document.getElementById('progress1-2');
                            const progress2to3 = document.getElementById('progress2-3');
                            
                            if(currentSection >= 2) {
                                progress1to2.style.width = '100%';
                            } else {
                                progress1to2.style.width = '0%';
                            }
                            
                            if(currentSection >= 3) {
                                progress2to3.style.width = '100%';
                            } else {
                                progress2to3.style.width = '0%';
                            }
                        }

                        function scrollToTop() {
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }

                        // Initialize form when page loads
                        document.addEventListener('DOMContentLoaded', function() {
                            showSection(1);
                            updateProgressIndicators();
                        });

                        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loanCalculatorForm');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const errorMessage = document.getElementById('errorMessage');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Reset view - hide closing statement when recalculating
                document.getElementById('closingStatementSection').classList.add('hidden');
                window.selectedLoanProgram = null;
                
                // Reset all button states
                const allButtons = document.querySelectorAll('#loanProgramCardsContainer button');
                allButtons.forEach(button => {
                    button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Check Summary for This Program';
                    button.classList.remove('ring-4', 'ring-offset-2', 'ring-blue-300', 'ring-green-300');
                });
                
                // Hide previous errors
                errorMessage.classList.add('hidden');
                
                // Show loading spinner
                loadingSpinner.classList.remove('hidden');
                
                try {
                    // Get form data
                    const formData = new FormData(form);
                    const loanType = formData.get('loan_type');
                    const selectedState = formData.get('state');
                    
                    // For Fix and Flip loans, state eligibility depends on program type
                    // Remove frontend state validation - let API handle all programs
                    // and mark eligibility on frontend based on program-specific state rules
                    
                    let apiUrl;
                    const apiParams = new URLSearchParams();
                    
                    // Add common parameters
                    apiParams.append('credit_score', formData.get('credit_score'));
                    apiParams.append('experience', formData.get('experience'));
                    apiParams.append('loan_type', loanType);
                    apiParams.append('transaction_type', formData.get('transaction_type'));
                    apiParams.append('purchase_price', formData.get('purchase_price'));
                    apiParams.append('broker_points', formData.get('broker_points'));
                    apiParams.append('state', selectedState);
                    
                    // Add loan_term only for non-DSCR loans
                    if (loanType !== 'DSCR Rental Loans') {
                        apiParams.append('loan_term', formData.get('loan_term'));
                    }
                    
                    if (loanType === 'DSCR Rental Loans') {
                        // Use DSCR-specific API endpoint and parameters
                        apiUrl = '/api/loan-matrix-dscr';
                        
                        // Add DSCR-specific parameters
                        apiParams.append('property_type', formData.get('property_type'));
                        apiParams.append('occupancy_type', formData.get('occupancy_type'));
                        apiParams.append('monthly_market_rent', formData.get('monthly_market_rent'));
                        apiParams.append('annual_tax', formData.get('annual_tax'));
                        apiParams.append('annual_insurance', formData.get('annual_insurance'));
                        apiParams.append('annual_hoa', formData.get('annual_hoa'));
                        
                        // Add payoff_amount only for Refinance Cash Out transactions
                        if (formData.get('transaction_type') === 'Refinance Cash Out') {
                            apiParams.append('payoff_amount', formData.get('payoff_amount'));
                        }
                        
                        // Add purchase date if provided
                        if (formData.get('purchase_date')) {
                            apiParams.append('purchase_date', formData.get('purchase_date'));
                        }
                    } else {
                        // Use regular API endpoint and parameters
                        apiUrl = '/api/loan-matrix';
                        apiParams.append('arv', formData.get('arv'));
                        apiParams.append('rehab_budget', formData.get('rehab_budget'));
                        
                        // Add GUC experience for New Construction loans
                        if (loanType === 'New Construction' && formData.get('guc_experience')) {
                            apiParams.append('guc_experience', formData.get('guc_experience'));
                        }
                        
                        // Add permit status for New Construction loans
                        if (loanType === 'New Construction' && formData.get('permit_status')) {
                            apiParams.append('permit_status', formData.get('permit_status'));
                        }
                        
                        // Add payoff amount for New Construction refinance transactions
                        if (loanType === 'New Construction' && (formData.get('transaction_type') === 'Refinance' || formData.get('transaction_type') === 'Refinance Cash Out') && formData.get('new_construction_payoff_amount')) {
                            apiParams.append('payoff_amount', formData.get('new_construction_payoff_amount'));
                        }
                        
                        // Add Fix and Flip specific parameters for refinance transactions
                        if (loanType === 'Fix and Flip' && (formData.get('transaction_type') === 'Refinance' || formData.get('transaction_type') === 'Refinance Cash Out')) {
                            if (formData.get('fix_flip_seasoning_period')) {
                                apiParams.append('seasoning_period', formData.get('fix_flip_seasoning_period'));
                            }
                            if (formData.get('fix_flip_payoff_amount')) {
                                apiParams.append('payoff_amount', formData.get('fix_flip_payoff_amount'));
                            }
                        }
                    }
                    
                    const finalApiUrl = `${apiUrl}?${apiParams.toString()}`;
                    
                    // Make API call
                    const response = await fetch(finalApiUrl);
                    
                    // Check if response is ok (status 200-299)
                    if (!response.ok) {
                        const errorData = await response.json();
                        loadingSpinner.classList.add('hidden');
                        
                        // Check if this is a DSCR disqualifier response (status 400 with detailed notifications)
                        if (response.status === 400 && errorData.disqualifier_notifications && Array.isArray(errorData.disqualifier_notifications) && errorData.disqualifier_notifications.length > 0) {
                            showDetailedError(errorData.message || 'Loan application does not meet qualification criteria', errorData.disqualifier_notifications);
                        } else {
                            const errorMsg = errorData.message || `HTTP Error: ${response.status}`;
                            showError(errorMsg);
                        }
                        // Hide entire results and closing section
                        document.getElementById('resultsAndClosingSection').classList.add('hidden');
                        return;
                    }
                    
                    const data = await response.json();
                    
                    // Hide loading spinner
                    loadingSpinner.classList.add('hidden');
                    
                    if (data.success && data.data && data.data.length > 0) {
                        if (loanType === 'DSCR Rental Loans') {
                            populateDscrResults(data.data);
                        } else if (loanType === 'Fix and Flip') {
                            // For Fix and Flip, show all programs but mark eligibility status
                            const loansWithEligibility = markFixAndFlipEligibility(data.data, formData);
                            populateResults(loansWithEligibility, formData);
                        } else {
                            // For New Construction and other loan types, pass formData for validation
                            populateResults(data.data, formData);
                        }
                    } else {
                        // Check if there are detailed disqualifier notifications
                        if (data.disqualifier_notifications && Array.isArray(data.disqualifier_notifications) && data.disqualifier_notifications.length > 0) {
                            // Show detailed error messages
                            showDetailedError(data.message || 'DSCR loan application does not meet qualification criteria', data.disqualifier_notifications);
                        } else {
                            // Show generic API error message if available, otherwise show generic message
                            const errorMsg = data.message || 'No loan programs found for the given criteria.';
                            showError(errorMsg);
                        }
                        // Hide entire results and closing section
                        document.getElementById('resultsAndClosingSection').classList.add('hidden');
                    }
                    
                } catch (error) {
                    loadingSpinner.classList.add('hidden');
                    showError('An error occurred while calculating loan options. Please try again.');
                    // Hide entire results and closing section
                    document.getElementById('resultsAndClosingSection').classList.add('hidden');
                    console.error('API Error:', error);
                }
            });
            
            // Add reset event listener to hide results when form is reset
            form.addEventListener('reset', function(e) {
                // Hide results and closing sections
                document.getElementById('resultsAndClosingSection').classList.add('hidden');
                document.getElementById('closingStatementSection').classList.add('hidden');
                // Hide error messages
                errorMessage.classList.add('hidden');
                // Hide loading spinner if visible
                loadingSpinner.classList.add('hidden');
                
                // Reset Select2 dropdowns to default state
                setTimeout(() => {
                    // Reset all Select2 dropdowns to their default/first option
                    $('#loan_type').val('').trigger('change');
                    $('#transaction_type').val('').trigger('change');
                    $('#loan_term').val('').trigger('change');
                    $('#property_type').empty().append('<option value="">-- Select Loan Type First --</option>').trigger('change');
                    $('#state').empty().append('<option value="">-- Select Loan Type First --</option>').trigger('change');
                    $('#occupancy_type').empty().append('<option value="">-- Select Occupancy Type --</option>').trigger('change');
                    
                    // Reset field visibility to default state (show regular fields, hide DSCR fields)
                    handleLoanTypeFieldVisibility('');
                }, 100);
            });
            
            function generateTableHeader(loanType) {
                const tableHeader = document.getElementById('tableHeader');
                
                // Define base columns that always appear
                let headerHTML = `
                    <th class="px-6 py-4 text-left font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                        <i class="fas fa-clipboard-list mr-2"></i>Loan Program
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-green-600 to-green-700 border-r border-green-500">
                        <i class="fas fa-calendar-alt mr-2"></i>Term
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 border-r border-purple-500">
                        <i class="fas fa-percentage mr-2"></i>Rate
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-orange-600 to-orange-700 border-r border-orange-500">
                        <i class="fas fa-chart-line mr-2"></i>Points
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 border-r border-indigo-500">
                        <i class="fas fa-home mr-2"></i>Max LTV
                    </th>
                `;
                
                // Add conditional columns based on loan type
                if (loanType === 'Fix and Flip') {
                    // For Fix and Flip: show only Max LTC
                    headerHTML += `
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                            <i class="fas fa-hammer mr-2"></i>Max LTC
                        </th>
                    `;
                } else if (loanType === 'New Construction' || loanType === 'DSCR Rental Loans') {
                    // For New Construction and DSCR Rental Loans: show only Max LTFC
                    headerHTML += `
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-pink-600 to-pink-700 border-r border-pink-500">
                            <i class="fas fa-tools mr-2"></i>Max LTFC
                        </th>
                    `;
                } else {
                    // For other loan types: show both columns (fallback)
                    headerHTML += `
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                            <i class="fas fa-hammer mr-2"></i>Max LTC
                        </th>
                        <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-pink-600 to-pink-700 border-r border-pink-500">
                            <i class="fas fa-tools mr-2"></i>Max LTFC
                        </th>
                    `;
                }
                
                // Add remaining columns
                headerHTML += `
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-cyan-600 to-cyan-700 border-r border-cyan-500">
                        <i class="fas fa-dollar-sign mr-2"></i>Purchase Loan Up To
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 border-r border-emerald-500">
                        <i class="fas fa-wrench mr-2"></i>Rehab Loan Up To
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-violet-600 to-violet-700">
                        <i class="fas fa-coins mr-2"></i>Total Loan Up To
                    </th>
                `;
                
                tableHeader.innerHTML = headerHTML;
            }
            
            // Function to filter Fix and Flip loans with detailed error messages
            function filterFixAndFlipLoansWithErrors(loans, formData) {
                const transactionType = formData.get('transaction_type');
                const isRefinance = transactionType === 'Refinance' || transactionType === 'Refinance Cash Out';
                const seasoningPeriod = formData.get('fix_flip_seasoning_period');
                const creditScore = parseInt(formData.get('credit_score'));
                const experience = parseInt(formData.get('experience'));
                const state = formData.get('state');
                const errors = [];
                
                const filteredLoans = loans.filter(loan => {
                    const programName = loan.loan_program;
                    const loanData = loan.loan_type_and_loan_program_table;
                    const totalLoanAmount = parseFloat(loanData?.total_loan_amount || 0);
                    
                    // Apply loan limit validation
                    if (programName === 'DESKTOP APPRAISAL') {
                        // Desktop Appraisal: max $1.5M (min $100k)
                        if (totalLoanAmount < 100000) {
                            errors.push('Desktop Appraisal program: minimum loan is $100,000');
                            return false;
                        }
                        if (totalLoanAmount > 1500000) {
                            errors.push('Desktop Appraisal program: maximum loan is $1,500,000');
                            return false;
                        }
                        
                        // Check state eligibility for Desktop Appraisal
                        const desktopIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'NY', 'NJ', 'CA', 'UT'];
                        if (desktopIneligibleStates.includes(state)) {
                            return false;
                        }
                        
                        // Seasoning requirement for refinance: must be less than 6 months
                        if (isRefinance && (seasoningPeriod === '12+ Months' || seasoningPeriod === '< 12 Months')) {
                            errors.push('Desktop Appraisal program: loans with seasoning greater than 6 months are ineligible');
                            return false;
                        }
                        
                        return true;
                    }
                    
                    if (programName === 'FULL APPRAISAL') {
                        // Full Appraisal: max $1.5M (min $50k)
                        if (totalLoanAmount < 50000) {
                            errors.push('Full Appraisal program: minimum loan is $50,000');
                            return false;
                        }
                        if (totalLoanAmount > 1500000) {
                            errors.push('Full Appraisal program: maximum loan is $1,500,000');
                            return false;
                        }
                        
                        // Check state eligibility for Full Appraisal
                        const fullAppraisalIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'UT'];
                        if (fullAppraisalIneligibleStates.includes(state)) {
                            return false;
                        }
                        
                        // Seasoning requirement for refinance: must be less than 12 months
                        if (isRefinance && seasoningPeriod === '12+ Months') {
                            errors.push('Full Appraisal program: loans with seasoning greater than 12 months are ineligible');
                            return false;
                        }
                        
                        // Additional requirements for refinance: FICO >= 680 and Experience >= 3
                        if (isRefinance && creditScore < 680) {
                            errors.push('Full Appraisal program: minimum FICO score of 680 required for refinance transactions');
                            return false;
                        }
                        if (isRefinance && experience < 3) {
                            errors.push('Full Appraisal program: minimum 3 deals experience required for refinance transactions');
                            return false;
                        }
                        
                        return true;
                    }
                    
                    // For unknown programs, return true (let backend handle)
                    return true;
                });
                
                return { filteredData: filteredLoans, errors: [...new Set(errors)] }; // Remove duplicates
            }
            
            // Function to mark Fix and Flip loan eligibility without filtering them out
            function markFixAndFlipEligibility(loans, formData) {
                const transactionType = formData.get('transaction_type');
                const isRefinance = transactionType === 'Refinance' || transactionType === 'Refinance Cash Out';
                const seasoningPeriod = formData.get('fix_flip_seasoning_period');
                const creditScore = parseInt(formData.get('credit_score'));
                const experience = parseInt(formData.get('experience'));
                const state = formData.get('state');
                
                return loans.map(loan => {
                    const programName = loan.loan_program;
                    const loanData = loan.loan_type_and_loan_program_table;
                    const totalLoanAmount = parseFloat(loanData?.total_loan_amount || 0);
                    
                    // Create a copy of the loan with eligibility information
                    const loanWithEligibility = { ...loan };
                    loanWithEligibility.isEligible = true;
                    loanWithEligibility.ineligibilityReasons = [];
                    
                    // Apply loan limit validation
                    if (programName === 'DESKTOP APPRAISAL') {
                        // Desktop Appraisal: max $1.5M (min $100k)
                        if (totalLoanAmount < 100000 || totalLoanAmount > 1500000) {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push(`Loan amount $${totalLoanAmount.toLocaleString()} outside limits ($100k-$1.5M)`);
                        }
                        
                        // Check state eligibility for Desktop Appraisal
                        const desktopIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'NY', 'NJ', 'CA', 'UT'];
                        if (desktopIneligibleStates.includes(state)) {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push(`Desktop Appraisal not available in ${state}`);
                        }
                        
                        // Seasoning requirements for Desktop Appraisal
                        if (isRefinance && seasoningPeriod !== '6+ months') {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push('Desktop Appraisal refinance requires 6+ months seasoning');
                        }
                        
                        // Experience requirements for Desktop Appraisal
                        if (experience < 3) {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push('Desktop Appraisal requires 3+ years experience');
                        }
                    }
                    
                    if (programName === 'FULL APPRAISAL') {
                        // Full Appraisal: max $3M (min $50k)
                        if (totalLoanAmount < 50000 || totalLoanAmount > 3000000) {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push(`Loan amount $${totalLoanAmount.toLocaleString()} outside limits ($50k-$3M)`);
                        }
                        
                        // Check state eligibility for Full Appraisal
                        const fullIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'UT'];
                        if (fullIneligibleStates.includes(state)) {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push(`Full Appraisal not available in ${state}`);
                        }
                        
                        // Seasoning requirements for Full Appraisal
                        if (isRefinance && seasoningPeriod !== '6+ months') {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push('Full Appraisal refinance requires 6+ months seasoning');
                        }
                        
                        // Experience requirements for Full Appraisal
                        if (experience < 2) {
                            loanWithEligibility.isEligible = false;
                            loanWithEligibility.ineligibilityReasons.push('Full Appraisal requires 2+ years experience');
                        }
                    }
                    
                    // Common credit score requirements for both programs
                    if (creditScore < 680) {
                        loanWithEligibility.isEligible = false;
                        loanWithEligibility.ineligibilityReasons.push(`Credit score ${creditScore} below minimum (680)`);
                    }
                    
                    return loanWithEligibility;
                });
            }
            
            // Function to filter Fix and Flip loans based on eligibility criteria
            function filterFixAndFlipLoans(loans, formData) {
                const transactionType = formData.get('transaction_type');
                const isRefinance = transactionType === 'Refinance' || transactionType === 'Refinance Cash Out';
                const seasoningPeriod = formData.get('fix_flip_seasoning_period');
                const creditScore = parseInt(formData.get('credit_score'));
                const experience = parseInt(formData.get('experience'));
                const state = formData.get('state');
                
                return loans.filter(loan => {
                    const programName = loan.loan_program;
                    const loanData = loan.loan_type_and_loan_program_table;
                    const totalLoanAmount = parseFloat(loanData?.total_loan_amount || 0);
                    
                    // Apply loan limit validation
                    if (programName === 'DESKTOP APPRAISAL') {
                        // Desktop Appraisal: max $1.5M (min $100k)
                        if (totalLoanAmount < 100000 || totalLoanAmount > 1500000) {
                            console.log(`Desktop Appraisal loan ${totalLoanAmount} outside limits (100k-1.5M)`);
                            return false;
                        }
                        
                        // Check state eligibility for Desktop Appraisal
                        const desktopIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'NY', 'NJ', 'CA', 'UT'];
                        if (desktopIneligibleStates.includes(state)) {
                            return false;
                        }
                        
                        // Seasoning requirement for refinance: must be less than 6 months
                        if (isRefinance && (seasoningPeriod === 'Owned 12+ Months' || seasoningPeriod === 'Owned < 12 Months')) {
                            return false;
                        }
                        
                        return true;
                    }
                    
                    if (programName === 'FULL APPRAISAL') {
                        // Full Appraisal: max $1.5M (min $50k)
                        if (totalLoanAmount < 50000 || totalLoanAmount > 1500000) {
                            console.log(`Full Appraisal loan ${totalLoanAmount} outside limits (50k-1.5M)`);
                            return false;
                        }
                        
                        // Check state eligibility for Full Appraisal
                        const fullAppraisalIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'UT'];
                        if (fullAppraisalIneligibleStates.includes(state)) {
                            return false;
                        }
                        
                        // Seasoning requirement for refinance: must be less than 12 months
                        if (isRefinance && seasoningPeriod === 'Owned 12+ Months') {
                            return false;
                        }
                        
                        // Additional requirements for refinance: FICO >= 680 and Experience >= 3
                        if (isRefinance && (creditScore < 680 || experience < 3)) {
                            return false;
                        }
                        
                        return true;
                    }
                    
                    // For unknown programs, return true (let backend handle)
                    return true;
                });
            }
            
            // Function to check Fix and Flip loan eligibility
            function checkFixAndFlipEligibility(loan, formData, programName) {
                const transactionType = formData.get('transaction_type');
                const isRefinance = transactionType === 'Refinance' || transactionType === 'Refinance Cash Out';
                const seasoningPeriod = formData.get('fix_flip_seasoning_period');
                const creditScore = parseInt(formData.get('credit_score'));
                const experience = parseInt(formData.get('experience'));
                const state = formData.get('state');
                const errors = [];
                
                // Get actual calculated loan amounts from the API response
                const loanAmounts = loan.estimated_closing_statement?.loan_amount_section;
                const totalLoanAmount = parseFloat(loanAmounts?.total_loan_amount || 0);
                
                if (programName === 'DESKTOP APPRAISAL') {
                    // Desktop Appraisal: max $1.5M (min $100k)
                    if (totalLoanAmount > 0 && totalLoanAmount < 100000) {
                        errors.push('Desktop Appraisal program: minimum loan is $100,000');
                    }
                    if (totalLoanAmount > 1500000) {
                        errors.push('Desktop Appraisal program: maximum loan is $1,500,000');
                    }
                    
                    // Check state eligibility for Desktop Appraisal
                    const desktopIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'NY', 'NJ', 'CA', 'UT'];
                    if (desktopIneligibleStates.includes(state)) {
                        errors.push('Desktop Appraisal program: not available in ' + state);
                    }
                    
                    // Seasoning requirement for refinance: must be less than 6 months
                    if (isRefinance && (seasoningPeriod === 'Owned 12+ Months' || seasoningPeriod === 'Owned < 12 Months')) {
                        errors.push('Desktop Appraisal program: loans with seasoning greater than 6 months are ineligible');
                    }
                }
                
                if (programName === 'FULL APPRAISAL') {
                    // Full Appraisal: max $1.5M (min $50k)
                    if (totalLoanAmount > 0 && totalLoanAmount < 50000) {
                        errors.push('Full Appraisal program: minimum loan is $50,000');
                    }
                    if (totalLoanAmount > 1500000) {
                        errors.push('Full Appraisal program: maximum loan is $1,500,000');
                    }
                    
                    // Check state eligibility for Full Appraisal
                    const fullAppraisalIneligibleStates = ['AK', 'AZ', 'ND', 'SD', 'VT', 'OR', 'UT'];
                    if (fullAppraisalIneligibleStates.includes(state)) {
                        errors.push('Full Appraisal program: not available in ' + state);
                    }
                    
                    // Seasoning requirement for refinance: must be less than 12 months
                    if (isRefinance && seasoningPeriod === 'Owned 12+ Months') {
                        errors.push('Full Appraisal program: loans with seasoning greater than 12 months are ineligible');
                    }
                    
                    // Additional requirements for refinance: FICO >= 680 and Experience >= 3
                    if (isRefinance && creditScore < 680) {
                        errors.push('Full Appraisal program: minimum FICO score of 680 required for refinance transactions');
                    }
                    if (isRefinance && experience < 3) {
                        errors.push('Full Appraisal program: minimum 3 deals experience required for refinance transactions');
                    }
                }
                
                return {
                    isEligible: errors.length === 0,
                    errors: errors,
                    totalLoanAmount: totalLoanAmount
                };
            }
            
            function populateResults(loans, formData = null) {
                console.log('All loans data:', loans); // Debug log
                console.log('FormData passed to populateResults:', formData); // Debug log
                
                const tableBody = document.getElementById('loanResultsTable');
                
                // Clear existing content
                tableBody.innerHTML = '';
                
                if (!loans || loans.length === 0) {
                    return;
                }
                
                // Generate dynamic table header based on loan type
                const loanType = loans[0].loan_type;
                generateTableHeader(loanType);
                
                // Check if this is New Construction loan type
                const isNewConstruction = loans[0].loan_type === 'New Construction';
                console.log('Is New Construction:', isNewConstruction); // Debug log
                
                // Group loans by loan program for display
                const loansByProgram = {};
                loans.forEach(loan => {
                    const programKey = loan.loan_program || 'Unknown';
                    if (!loansByProgram[programKey]) {
                        loansByProgram[programKey] = [];
                    }
                    loansByProgram[programKey].push(loan);
                });
                
                // Create table rows and cards for each loan program
                Object.keys(loansByProgram).forEach((programName, index) => {
                    const loan = loansByProgram[programName][0]; // Use first loan of each program
                    const loanData = loan.loan_type_and_loan_program_table;
                    
                    // Determine program display name and icon
                    let displayName = programName;
                    let iconClass = 'fas fa-calculator';
                    let colorClass = index === 0 ? 'blue' : 'green';
                    
                    if (isNewConstruction) {
                        if (programName === 'EXPERIENCED BUILDER') {
                            displayName = 'Experienced Builder';
                            iconClass = 'fas fa-hammer';
                            colorClass = 'blue';
                        } else if (programName === 'NEW BUILDER') {
                            displayName = 'New Builder';
                            iconClass = 'fas fa-tools';
                            colorClass = 'green';
                        }
                    } else {
                        if (programName === 'FULL APPRAISAL') {
                            displayName = 'Full Appraisal';
                            iconClass = 'fas fa-file-alt';
                            colorClass = 'blue';
                        } else if (programName === 'DESKTOP APPRAISAL') {
                            displayName = 'Desktop Appraisal';
                            iconClass = 'fas fa-desktop';
                            colorClass = 'green';
                        }
                    }
                    
                    // Check Fix and Flip eligibility if formData is provided
                    let isEligible = true;
                    let eligibilityErrors = [];
                    
                    if (formData && loanType === 'Fix and Flip' && (programName === 'FULL APPRAISAL' || programName === 'DESKTOP APPRAISAL')) {
                        // Use eligibility information already attached to the loan object
                        if (loan.hasOwnProperty('isEligible')) {
                            isEligible = loan.isEligible;
                            eligibilityErrors = loan.ineligibilityReasons || [];
                        } else {
                            // Fallback to the original check if eligibility info is not available
                            const eligibilityCheck = checkFixAndFlipEligibility(loan, formData, programName);
                            isEligible = eligibilityCheck.isEligible;
                            eligibilityErrors = eligibilityCheck.errors;
                        }
                    }
                    
                    // Check New Construction state eligibility if formData is provided
                    if (formData && isNewConstruction && loan.user_inputs && (programName === 'EXPERIENCED BUILDER' || programName === 'NEW BUILDER')) {
                        const state = loan.user_inputs.state;
                        
                        console.log('New Construction validation:', {
                            programName,
                            state,
                            hasUserInputs: !!loan.user_inputs,
                            isNewConstruction
                        });
                        
                        if (programName === 'EXPERIENCED BUILDER') {
                            // For Experienced Builder Program, We dont Lend in following states: ND, SD, OR, UT, VT, AZ, AK, NV
                            const experiencedBuilderIneligibleStates = ['ND', 'SD', 'OR', 'UT', 'VT', 'AZ', 'AK', 'NV'];
                            if (experiencedBuilderIneligibleStates.includes(state)) {
                                isEligible = false;
                                eligibilityErrors.push(`For Experienced Builder Program, We don't lend in the following states: ND, SD, OR, UT, VT, AZ, AK, NV`);
                                console.log('Experienced Builder - State not eligible:', state);
                            }
                        } else if (programName === 'NEW BUILDER') {
                            // For New Builder Program, We dont Lend in following states: ND, SD, UT, VT, AK, ID, NV, MN
                            const newBuilderIneligibleStates = ['ND', 'SD', 'UT', 'VT', 'AK', 'ID', 'NV', 'MN'];
                            if (newBuilderIneligibleStates.includes(state)) {
                                isEligible = false;
                                eligibilityErrors.push(`For New Builder Program, We don't lend in the following states: ND, SD, UT, VT, AK, ID, NV, MN`);
                                console.log('New Builder - State not eligible:', state);
                            }
                        }
                    }
                    
                    // Create enhanced table row with conditional columns
                    const row = document.createElement('tr');
                    row.className = `hover:bg-gray-50 transition-colors duration-200 ${index % 2 === 0 ? 'bg-white' : 'bg-gray-25'}`;
                    
                    // If not eligible, add visual indicators
                    if (!isEligible) {
                        row.classList.add('opacity-60', 'bg-red-50');
                        colorClass = 'red'; // Change color scheme for ineligible programs
                    }
                    
                    // Base columns that always appear
                    let rowHTML = `
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-${colorClass}-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="${iconClass} text-${colorClass}-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">${displayName}</div>
                                    <div class="text-sm text-gray-500">Loan Program</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                ${loanData?.loan_term || 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-green-600">
                                ${loanData?.intrest_rate ? loanData.intrest_rate + '%' : '0.00%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-orange-600">
                                ${loanData?.lender_points ? loanData.lender_points + '%' : '0.00%'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                ${loanData?.max_ltv ? loanData.max_ltv + '%' : '0%'}
                            </span>
                        </td>
                    `;
                    
                    // Add conditional columns based on loan type
                    if (loan.loan_type === 'Fix and Flip') {
                        // For Fix and Flip: show only Max LTC
                        rowHTML += `
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    ${loanData?.max_ltc ? loanData.max_ltc + '%' : '0%'}
                                </span>
                            </td>
                        `;
                    } else if (loan.loan_type === 'New Construction' || loan.loan_type === 'DSCR Rental Loans') {
                        // For New Construction and DSCR Rental Loans: show only Max LTFC
                        rowHTML += `
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    ${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0%'}
                                </span>
                            </td>
                        `;
                    } else {
                        // For other loan types: show both columns (fallback)
                        rowHTML += `
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    ${loanData?.max_ltc ? loanData.max_ltc + '%' : '0%'}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    ${loanData?.max_ltfc ? loanData.max_ltfc + '%' : '0%'}
                                </span>
                            </td>
                        `;
                    }
                    
                    // Add remaining columns
                    rowHTML += `
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-blue-600">
                                $${loanData?.purchase_loan_up_to !== undefined ? numberWithCommas(loanData.purchase_loan_up_to) : '0'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-200">
                            <span class="text-lg font-bold text-green-600">
                                $${loanData?.rehab_loan_up_to ? numberWithCommas(loanData.rehab_loan_up_to) : 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-xl font-bold text-purple-600" id="total_loan_${index}">
                                $${loanData?.total_loan_up_to ? numberWithCommas(loanData.total_loan_up_to) : 'N/A'}
                            </span>
                        </td>
                    `;
                    
                    row.innerHTML = rowHTML;
                    tableBody.appendChild(row);
                });

                // Create loan program selection cards
                createLoanProgramCards(loansByProgram, formData);
                
                // Show results section but keep closing statement hidden initially
                document.getElementById('resultsAndClosingSection').classList.remove('hidden');
                
                // Smooth scroll to results section after calculation
                setTimeout(() => {
                    document.getElementById('resultsSection').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }

            function createLoanProgramCards(loansByProgram, formData = null) {
                const cardsContainer = document.getElementById('loanProgramCardsContainer');
                cardsContainer.innerHTML = '';
                
                // Reset view state - ensure cards are shown and closing statement is hidden
                document.getElementById('resultsSection').classList.remove('hidden');
                document.getElementById('closingStatementSection').classList.add('hidden');
                window.selectedLoanProgram = null;

                // Store loans data globally for later use
                window.allLoansData = loansByProgram;

                // Check if this is New Construction loan type
                const firstLoan = Object.values(loansByProgram)[0][0];
                const isNewConstruction = firstLoan.loan_type === 'New Construction';
                const isFixAndFlip = firstLoan.loan_type === 'Fix and Flip';

                Object.keys(loansByProgram).forEach((programName, index) => {
                    const loan = loansByProgram[programName][0];
                    const loanData = loan.loan_type_and_loan_program_table;
                    
                    // Check if this is a DSCR loan - if so, skip validation
                    const isDscrLoan = loan.loan_type === 'DSCR Rental Loans';
                    
                    // Check Fix and Flip eligibility if applicable
                    let isEligible = true;
                    let eligibilityErrors = [];
                    
                    if (formData && isFixAndFlip && (programName === 'FULL APPRAISAL' || programName === 'DESKTOP APPRAISAL')) {
                        const eligibilityCheck = checkFixAndFlipEligibility(loan, formData, programName);
                        isEligible = eligibilityCheck.isEligible;
                        eligibilityErrors = eligibilityCheck.errors;
                    }
                    
                    // Check New Construction state eligibility if applicable
                    if (formData && isNewConstruction && loan.user_inputs) {
                        const state = loan.user_inputs.state;
                        
                        console.log('New Construction Card validation:', {
                            programName,
                            state,
                            hasUserInputs: !!loan.user_inputs,
                            isNewConstruction
                        });
                        
                        if (programName === 'EXPERIENCED BUILDER') {
                            // For Experienced Builder Program, We dont Lend in following states: ND, SD, OR, UT, VT, AZ, AK, NV
                            const experiencedBuilderIneligibleStates = ['ND', 'SD', 'OR', 'UT', 'VT', 'AZ', 'AK', 'NV'];
                            if (experiencedBuilderIneligibleStates.includes(state)) {
                                isEligible = false;
                                eligibilityErrors.push(`For Experienced Builder Program, We don't lend in the following states: ND, SD, OR, UT, VT, AZ, AK, NV`);
                                console.log('Experienced Builder - State not eligible:', state);
                            }
                        } else if (programName === 'NEW BUILDER') {
                            // For New Builder Program, We dont Lend in following states: ND, SD, UT, VT, AK, ID, NV, MN
                            const newBuilderIneligibleStates = ['ND', 'SD', 'UT', 'VT', 'AK', 'ID', 'NV', 'MN'];
                            if (newBuilderIneligibleStates.includes(state)) {
                                isEligible = false;
                                eligibilityErrors.push(`For New Builder Program, We don't lend in the following states: ND, SD, UT, VT, AK, ID, NV, MN`);
                                console.log('New Builder - State not eligible:', state);
                            }
                        }
                    }
                    
                    // Check if this loan program has zero loan amounts (skip for DSCR loans)
                    const purchaseLoan = parseFloat(loanData?.purchase_loan_up_to || 0);
                    const rehabLoan = parseFloat(loanData?.rehab_loan_up_to || 0);
                    const totalLoan = parseFloat(loanData?.total_loan_up_to || 0);
                    const hasZeroLoanAmount = isDscrLoan ? false : (purchaseLoan === 0 && rehabLoan === 0 && totalLoan === 0);
                    
                    // Determine program display name and styling
                    let displayName = programName;
                    let iconClass = 'fas fa-calculator';
                    let cardColorClass = index === 0 ? 'border-blue-500 bg-blue-50' : 'border-green-500 bg-green-50';
                    let headerColorClass = index === 0 ? 'bg-blue-600' : 'bg-green-600';
                    let buttonColorClass = index === 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700';
                    
                    // If loan amounts are zero, use different styling
                    if (hasZeroLoanAmount) {
                        cardColorClass = 'border-gray-400 bg-gray-50';
                        headerColorClass = 'bg-gray-500';
                        buttonColorClass = 'bg-gray-400 cursor-not-allowed';
                        iconClass = 'fas fa-exclamation-triangle';
                    }
                    
                    // If not eligible, use red styling
                    if (!isEligible) {
                        cardColorClass = 'border-red-400 bg-red-50';
                        headerColorClass = 'bg-red-500';
                        buttonColorClass = 'bg-red-400 cursor-not-allowed';
                        iconClass = 'fas fa-times-circle';
                    }
                    
                    if (isNewConstruction) {
                        if (programName === 'EXPERIENCED BUILDER') {
                            displayName = 'Experienced Builder';
                            iconClass = 'fas fa-hammer';
                        } else if (programName === 'NEW BUILDER') {
                            displayName = 'New Builder';
                            iconClass = 'fas fa-tools';
                        }
                    } else {
                        if (programName === 'FULL APPRAISAL') {
                            displayName = 'Full Appraisal';
                            iconClass = 'fas fa-file-alt';
                        } else if (programName === 'DESKTOP APPRAISAL') {
                            displayName = 'Desktop Appraisal';
                            iconClass = 'fas fa-desktop';
                        }
                    }

                    const card = document.createElement('div');
                    card.className = `bg-white rounded-lg shadow-lg border-2 ${cardColorClass} overflow-hidden`;
                    card.innerHTML = `
                        <div class="${headerColorClass} text-white p-4">
                            <div class="flex items-center">
                                <i class="${iconClass} text-2xl mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-bold">Loan Program: ${displayName}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b pb-2">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-700">You qualify for a Purchase Loan up to:</span>
                                        <span class="text-xs text-blue-600 italic">✏️ Click amount below to edit</span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <input type="number" 
                                               id="card_purchase_loan_${programName.replace(/\s+/g, '_')}" 
                                               class="loan-amount-input w-24 px-2 py-1 text-right font-bold text-blue-600 rounded focus:outline-none"
                                               value="${loanData?.purchase_loan_up_to || 0}"
                                               max="${loanData?.purchase_loan_up_to || 0}"
                                               min="0"
                                               step="1000"
                                               data-program="${programName}"
                                               data-max-amount="${loanData?.purchase_loan_up_to || 0}"
                                               onchange="handleCardLoanAmountChange(this, '${programName}')"
                                               onblur="handleCardLoanAmountChange(this, '${programName}')"
                                               title="Enter desired loan amount (up to $${loanData?.purchase_loan_up_to ? numberWithCommas(loanData.purchase_loan_up_to) : '0'})">
                                        <span class="text-xs text-gray-500 mt-1">Max: $${loanData?.purchase_loan_up_to !== undefined ? numberWithCommas(loanData.purchase_loan_up_to) : '0'}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">You qualify for a Rehab Loan up to:</span>
                                    <span class="font-bold text-green-600">$${loanData?.rehab_loan_up_to ? numberWithCommas(loanData.rehab_loan_up_to) : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">You qualify for Total Loan up to:</span>
                                    <span class="font-bold text-purple-600" id="card_total_loan_${programName.replace(/\s+/g, '_')}">$${loanData?.total_loan_up_to ? numberWithCommas(loanData.total_loan_up_to) : 'N/A'}</span>
                                </div>
                                ${!isEligible ? `
                                    <div class="mt-4 p-3 bg-red-100 border border-red-400 rounded-lg">
                                        <h4 class="font-semibold text-red-800 mb-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Program Not Available
                                        </h4>
                                        <ul class="text-sm text-red-700 space-y-1">
                                            ${eligibilityErrors.map(error => `<li>• ${error}</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                            </div>
                            <div class="mt-6">
                                <button onclick="${(hasZeroLoanAmount || !isEligible) ? '' : `selectLoanProgram('${programName}')`}" 
                                        class="${buttonColorClass} text-white px-6 py-3 rounded-lg font-semibold w-full transition-colors duration-200 ${(hasZeroLoanAmount || !isEligible) ? '' : 'hover:shadow-lg'}"
                                        ${(hasZeroLoanAmount || !isEligible) ? 'disabled' : ''} 
                                        title="${hasZeroLoanAmount ? 'This loan program shows zero loan amount based on your inputs' : (!isEligible ? 'This program is not available based on your criteria' : '')}">
                                    <i class="fas ${(hasZeroLoanAmount || !isEligible) ? 'fa-ban' : 'fa-check-circle'} mr-2"></i>
                                    ${hasZeroLoanAmount ? 'Not Available - Zero Loan Amount' : (!isEligible ? 'Not Available - See Requirements Above' : 'Check Summary for This Program')}
                                </button>
                            </div>
                        </div>
                    `;
                    cardsContainer.appendChild(card);
                });
            }
            
            function populateDscrResults(loans) {
                console.log('DSCR loans data:', loans); // Debug log
                
                const tableBody = document.getElementById('loanResultsTable');
                
                // Clear existing content
                tableBody.innerHTML = '';
                
                if (!loans || loans.length === 0) {
                    return;
                }
                
                // Store loans data globally for dropdown changes
                window.dscrLoansData = loans;
                
                // Initialize global dropdown values from the first loan program
                if (loans.length > 0 && loans[0].loan_program_values) {
                    window.currentDscrValues.loanTerm = loans[0].loan_program_values.loan_term || '30 Year Fixed';
                    // Convert lender_points to proper format with 3 decimal places
                    const lenderPointsValue = loans[0].loan_program_values.lender_points;
                    if (lenderPointsValue !== null && lenderPointsValue !== undefined) {
                        window.currentDscrValues.lenderPoints = parseFloat(lenderPointsValue).toFixed(3);
                    } else {
                        window.currentDscrValues.lenderPoints = '2.000'; // fallback only if API doesn't provide value
                    }
                    window.currentDscrValues.prepayPenalty = loans[0].loan_program_values.pre_pay_penalty || '5 Year Prepay';
                }
                
                // Load dropdown options before generating table
                loadDscrDropdownOptions().then(() => {
                    // Generate DSCR-specific table header
                    generateDscrTableHeader();
                    
                    // Create table rows for each DSCR loan program
                    loans.forEach((loan, index) => {
                        const loanData = loan.loan_program_values;
                        const programName = loan.loan_program || `Loan Program #${index + 1}`;
                        
                        // Create enhanced table row for DSCR loans
                        const row = document.createElement('tr');
                        row.className = `hover:bg-gray-50 transition-colors duration-200 ${index % 2 === 0 ? 'bg-white' : 'bg-gray-25'}`;
                        
                        let colorClass = index === 0 ? 'blue' : index === 1 ? 'green' : 'purple';
                        let iconClass = 'fas fa-home';
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 border-r border-gray-200">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-${colorClass}-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="${iconClass} text-${colorClass}-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">${programName}</div>
                                        <div class="text-sm text-gray-500">DSCR Rental Loan</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <select class="loan-term-dropdown bg-blue-50 border border-blue-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none bg-no-repeat bg-right pr-4" 
                                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 4 5&quot;><path fill=&quot;%23666&quot; d=&quot;M2 0L0 2h4zm0 5L0 3h4z&quot;/></svg>'); background-size: 12px;" 
                                        data-program-index="${index}">
                                    ${generateLoanTermOptions(window.currentDscrValues.loanTerm || loanData?.loan_term || '30 Year Fixed')}
                                </select>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    ${loanData?.max_ltv ? loanData.max_ltv + '%' : '0%'}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="text-lg font-bold text-green-600">
                                    $${loanData?.monthly_payment ? numberWithCommas(loanData.monthly_payment) : 'N/A'}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <span class="text-lg font-bold text-orange-600">
                                    ${loanData?.interest_rate ? loanData.interest_rate + '%' : '0.00%'}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center border-r border-gray-200">
                                <select class="lender-points-dropdown bg-cyan-50 border border-cyan-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 appearance-none bg-no-repeat bg-right pr-4" 
                                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 4 5&quot;><path fill=&quot;%23666&quot; d=&quot;M2 0L0 2h4zm0 5L0 3h4z&quot;/></svg>'); background-size: 12px;" 
                                        data-program-index="${index}">
                                    ${generateLenderPointsOptions(
                                        loanData?.lender_points !== null && loanData?.lender_points !== undefined 
                                            ? parseFloat(loanData.lender_points).toFixed(3) 
                                            : window.currentDscrValues.lenderPoints || '2.000'
                                    )}
                                </select>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <select class="prepay-penalty-dropdown bg-violet-50 border border-violet-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 appearance-none bg-no-repeat bg-right pr-4" 
                                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 4 5&quot;><path fill=&quot;%23666&quot; d=&quot;M2 0L0 2h4zm0 5L0 3h4z&quot;/></svg>'); background-size: 12px;" 
                                        data-program-index="${index}">
                                    ${generatePrepayPenaltyOptions(window.currentDscrValues.prepayPenalty || loanData?.pre_pay_penalty || '5 Year Prepay')}
                                </select>
                            </td>
                        `;
                        
                        tableBody.appendChild(row);
                    });

                    // Add event listeners for dropdowns
                    addDscrDropdownEventListeners();

                    // Create DSCR loan program selection cards (simplified for DSCR)
                    createDscrLoanProgramCards(loans);
                    
                    // Show results section
                    document.getElementById('resultsAndClosingSection').classList.remove('hidden');
                    
                    // Smooth scroll to results section after calculation
                    setTimeout(() => {
                        document.getElementById('resultsSection').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 300);
                });
            }
            
            function generateDscrTableHeader() {
                const tableHeader = document.getElementById('tableHeader');
                
                const headerHTML = `
                    <th class="px-6 py-4 text-left font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 border-r border-blue-500">
                        <i class="fas fa-clipboard-list mr-2"></i>Loan Program
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-green-600 to-green-700 border-r border-green-500">
                        <i class="fas fa-calendar-alt mr-2"></i>Loan Term
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 border-r border-purple-500">
                        <i class="fas fa-percentage mr-2"></i>Max LTV
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-orange-600 to-orange-700 border-r border-orange-500">
                        <i class="fas fa-dollar-sign mr-2"></i>Monthly Payment
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 border-r border-indigo-500">
                        <i class="fas fa-chart-line mr-2"></i>Interest Rate
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-cyan-600 to-cyan-700 border-r border-cyan-500">
                        <i class="fas fa-coins mr-2"></i>Lender Points
                    </th>
                    <th class="px-4 py-4 text-center font-bold text-white bg-gradient-to-r from-violet-600 to-violet-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Pre Pay Penalty
                    </th>
                `;
                
                tableHeader.innerHTML = headerHTML;
            }
            
            function createDscrLoanProgramCards(loans) {
                const cardsContainer = document.getElementById('loanProgramCardsContainer');
                cardsContainer.innerHTML = '';
                
                // Reset view state - ensure cards are shown and closing statement is hidden
                document.getElementById('resultsSection').classList.remove('hidden');
                document.getElementById('closingStatementSection').classList.add('hidden');
                window.selectedLoanProgram = null;

                // Store DSCR loans data globally for later use
                window.allLoansData = {};
                
                loans.forEach((loan, index) => {
                    const programName = loan.loan_program || `Loan Program #${index + 1}`;
                    window.allLoansData[programName] = [loan];
                    
                    const loanData = loan.loan_program_values;
                    
                    // For DSCR loans, always show as valid (no validation needed)
                    let hasValidLoanAmount = true;
                    
                    let cardColorClass = index === 0 ? 'border-blue-500 bg-blue-50' : index === 1 ? 'border-green-500 bg-green-50' : 'border-purple-500 bg-purple-50';
                    let headerColorClass = index === 0 ? 'bg-blue-600' : index === 1 ? 'bg-green-600' : 'bg-purple-600';
                    let buttonColorClass = index === 0 ? 'bg-blue-600 hover:bg-blue-700' : index === 1 ? 'bg-green-600 hover:bg-green-700' : 'bg-purple-600 hover:bg-purple-700';
                    
                    // If loan amounts are zero, use different styling
                    if (!hasValidLoanAmount) {
                        cardColorClass = 'border-gray-400 bg-gray-50';
                        headerColorClass = 'bg-gray-500';
                        buttonColorClass = 'bg-gray-400 cursor-not-allowed';
                    }
                    
                    const card = document.createElement('div');
                    card.className = `bg-white rounded-lg shadow-lg border-2 ${cardColorClass} overflow-hidden`;
                    card.innerHTML = `
                        <div class="${headerColorClass} text-white p-4">
                            <div class="flex items-center">
                                <i class="fas fa-home text-2xl mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-bold">${programName}</h3>
                                    <p class="text-sm opacity-90">DSCR Rental Loan</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b pb-2">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-700">You qualify for a DSCR Loan up to:</span>
                                        <span class="text-xs text-blue-600 italic">✏️ Click amount below to edit</span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <input type="number" 
                                               id="card_dscr_loan_${programName.replace(/\s+/g, '_')}" 
                                               class="loan-amount-input w-24 px-2 py-1 text-right font-bold text-blue-600 rounded focus:outline-none"
                                               value="${loan?.ltv_formula?.loan_amount?.input || 0}"
                                               max="${loan?.ltv_formula?.loan_amount?.input || 0}"
                                               min="0"
                                               step="1000"
                                               data-program="${programName}"
                                               data-max-amount="${loan?.ltv_formula?.loan_amount?.input || 0}"
                                               onchange="handleCardDscrLoanAmountChange(this, '${programName}')"
                                               onblur="handleCardDscrLoanAmountChange(this, '${programName}')"
                                               title="Enter desired loan amount (up to $${loan?.ltv_formula?.loan_amount?.input ? numberWithCommas(loan.ltv_formula.loan_amount.input) : '0'})">
                                        <span class="text-xs text-gray-500 mt-1">Max: $${loan?.ltv_formula?.loan_amount?.input ? numberWithCommas(loan.ltv_formula.loan_amount.input) : 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">Monthly Payment:</span>
                                    <span class="font-bold text-green-600">$${loanData?.monthly_payment ? numberWithCommas(loanData.monthly_payment) : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">Interest Rate:</span>
                                    <span class="font-bold text-orange-600">${loanData?.interest_rate ? loanData.interest_rate + '%' : '0.00%'}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2">
                                    <span class="font-medium text-gray-700">Max LTV:</span>
                                    <span class="font-bold text-purple-600">${loanData?.max_ltv ? loanData.max_ltv + '%' : '0%'}</span>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button onclick="${hasValidLoanAmount ? `selectLoanProgram('${programName}')` : ''}" 
                                        class="${buttonColorClass} text-white px-6 py-3 rounded-lg font-semibold w-full transition-colors duration-200 ${hasValidLoanAmount ? 'hover:shadow-lg' : ''}"
                                        ${hasValidLoanAmount ? '' : 'disabled title="This loan program shows zero loan amount based on your inputs"'}>
                                    <i class="fas ${hasValidLoanAmount ? 'fa-check-circle' : 'fa-ban'} mr-2"></i>
                                    ${hasValidLoanAmount ? 'Check Summary for This Program' : 'Not Available - Zero Loan Amount'}
                                </button>
                            </div>
                        </div>
                    `;
                    cardsContainer.appendChild(card);
                });
            }
            
            function showError(message) {
                document.getElementById('errorText').textContent = message;
                errorMessage.classList.remove('hidden');
            }
            
            function showDetailedError(mainMessage, notifications) {
                const errorTextElement = document.getElementById('errorText');
                
                // Create detailed error content with main message and bullet points
                let errorContent = `<div class="font-semibold mb-2">${mainMessage}</div>`;
                
                if (notifications && notifications.length > 0) {
                    errorContent += '<div class="text-sm"><strong>Specific Issues:</strong></div>';
                    errorContent += '<ul class="list-disc list-inside text-sm mt-1 ml-2">';
                    notifications.forEach(notification => {
                        errorContent += `<li>${notification}</li>`;
                    });
                    errorContent += '</ul>';
                }
                
                errorTextElement.innerHTML = errorContent;
                errorMessage.classList.remove('hidden');
            }

            // DSCR Dropdown Helper Functions
            
            // Store dropdown options globally
            window.dscrDropdownOptions = {
                loanTerms: [],
                prepayPeriods: [],
                lenderPoints: [
                    { value: '1.000', label: '1.000%' },
                    { value: '1.500', label: '1.500%' }, 
                    { value: '2.000', label: '2.000%' }
                ]
            };
            
            // Store current DSCR dropdown values globally to persist across table rebuilds
            window.currentDscrValues = {
                loanTerm: '30 Year Fixed',
                lenderPoints: null, // Will be set from API response
                prepayPenalty: '5 Year Prepay'
            };
            
            // Load dropdown options from API
            async function loadDscrDropdownOptions() {
                try {
                    // Load loan terms
                    const loanTermsResponse = await fetch('/api/dscr-loan-terms');
                    if (loanTermsResponse.ok) {
                        const loanTermsData = await loanTermsResponse.json();
                        if (loanTermsData.success) {
                            window.dscrDropdownOptions.loanTerms = loanTermsData.data;
                        }
                    }
                    
                    // Load prepay periods
                    const prepayResponse = await fetch('/api/prepay-periods');
                    if (prepayResponse.ok) {
                        const prepayData = await prepayResponse.json();
                        if (prepayData.success) {
                            window.dscrDropdownOptions.prepayPeriods = prepayData.data;
                        }
                    }
                } catch (error) {
                    console.error('Error loading DSCR dropdown options:', error);
                }
            }
            
            // Generate loan term dropdown options
            function generateLoanTermOptions(selectedValue) {
                const options = window.dscrDropdownOptions.loanTerms.map(term => 
                    `<option value="${term.value}" ${term.value === selectedValue ? 'selected' : ''}>${term.name}</option>`
                ).join('');
                return options;
            }
            
            // Generate lender points dropdown options
            function generateLenderPointsOptions(selectedValue) {
                const options = window.dscrDropdownOptions.lenderPoints.map(point => 
                    `<option value="${point.value}" ${point.value === selectedValue ? 'selected' : ''}>${point.label}</option>`
                ).join('');
                return options;
            }
            
            // Generate prepay penalty dropdown options
            function generatePrepayPenaltyOptions(selectedValue) {
                const options = window.dscrDropdownOptions.prepayPeriods.map(period => 
                    `<option value="${period.name}" ${period.name === selectedValue ? 'selected' : ''}>${period.name}</option>`
                ).join('');
                return options;
            }
            
            // Add event listeners for DSCR dropdowns
            function addDscrDropdownEventListeners() {
                // Loan term dropdown change handler
                document.querySelectorAll('.loan-term-dropdown').forEach(dropdown => {
                    dropdown.addEventListener('change', function() {
                        const programIndex = this.getAttribute('data-program-index');
                        handleDscrDropdownChange(programIndex, 'loan_term', this.value);
                    });
                });
                
                // Lender points dropdown change handler
                document.querySelectorAll('.lender-points-dropdown').forEach(dropdown => {
                    dropdown.addEventListener('change', function() {
                        const programIndex = this.getAttribute('data-program-index');
                        handleDscrDropdownChange(programIndex, 'lender_points', this.value);
                    });
                });
                
                // Prepay penalty dropdown change handler
                document.querySelectorAll('.prepay-penalty-dropdown').forEach(dropdown => {
                    dropdown.addEventListener('change', function() {
                        const programIndex = this.getAttribute('data-program-index');
                        handleDscrDropdownChange(programIndex, 'pre_pay_penalty', this.value);
                    });
                });
            }
            
            // Handle DSCR dropdown changes
            async function handleDscrDropdownChange(programIndex, parameterType, newValue) {
                try {
                    // Update global values
                    if (parameterType === 'loan_term') {
                        window.currentDscrValues.loanTerm = newValue;
                    } else if (parameterType === 'lender_points') {
                        window.currentDscrValues.lenderPoints = newValue;
                    } else if (parameterType === 'pre_pay_penalty') {
                        window.currentDscrValues.prepayPenalty = newValue;
                    }
                    
                    // Show loading indicator
                    showDscrLoadingIndicator();
                    
                    // Get current form data
                    const form = document.getElementById('loanCalculatorForm');
                    const formData = new FormData(form);
                    
                    // Build API URL with current parameters
                    const apiParams = new URLSearchParams();
                    apiParams.append('credit_score', formData.get('credit_score'));
                    apiParams.append('experience', formData.get('experience'));
                    apiParams.append('loan_type', formData.get('loan_type'));
                    apiParams.append('transaction_type', formData.get('transaction_type'));
                    apiParams.append('purchase_price', formData.get('purchase_price'));
                    apiParams.append('broker_points', formData.get('broker_points'));
                    apiParams.append('state', formData.get('state'));
                    apiParams.append('property_type', formData.get('property_type'));
                    apiParams.append('occupancy_type', formData.get('occupancy_type'));
                    apiParams.append('monthly_market_rent', formData.get('monthly_market_rent'));
                    apiParams.append('annual_tax', formData.get('annual_tax'));
                    apiParams.append('annual_insurance', formData.get('annual_insurance'));
                    apiParams.append('annual_hoa', formData.get('annual_hoa') || '0');
                    
                    // Add payoff_amount for refinance transactions
                    const transactionType = formData.get('transaction_type');
                    if (transactionType === 'Refinance Cash Out' || transactionType === 'Refinance No Cash Out') {
                        apiParams.append('payoff_amount', formData.get('payoff_amount') || '0');
                    }
                    
                    // Use global values for API parameters
                    apiParams.append('loan_term', window.currentDscrValues.loanTerm);
                    apiParams.append('lender_points', window.currentDscrValues.lenderPoints);
                    apiParams.append('pre_pay_penalty', window.currentDscrValues.prepayPenalty);
                    
                    const apiUrl = `/api/loan-matrix-dscr?${apiParams.toString()}`;
                    console.log('Making DSCR API call:', apiUrl); // Debug log
                    
                    // Make API call
                    const response = await fetch(apiUrl);
                    if (!response.ok) {
                        throw new Error(`API request failed: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success && data.data && data.data.length > 0) {
                        // Update the results with new data
                        populateDscrResults(data.data);
                        console.log('DSCR dropdown change processed successfully');
                    } else {
                        console.error('No data returned from DSCR API');
                        showError('No loan data available for the selected options');
                    }
                    
                } catch (error) {
                    console.error('Error handling DSCR dropdown change:', error);
                    showError('An error occurred while updating loan data. Please try again.');
                } finally {
                    hideDscrLoadingIndicator();
                }
            }
            
            // Show loading indicator for DSCR updates
            function showDscrLoadingIndicator() {
                const tableBody = document.getElementById('loanResultsTable');
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-blue-500 mr-2"></i>
                                <span class="text-gray-600">Updating loan data...</span>
                            </div>
                        </td>
                    </tr>
                `;
            }
            
            // Hide loading indicator
            function hideDscrLoadingIndicator() {
                // The loading indicator will be replaced by the new results
            }

            // Tab functionality
        });

        // Global variables and functions (outside DOMContentLoaded)
        let allLoansData = null;

        function selectLoanProgram(programName) {
            if (!window.allLoansData || !window.allLoansData[programName]) {
                console.error('Loan data not found for program:', programName);
                return;
            }

            // Store selected program globally for PDF generation
            window.selectedLoanProgram = programName;

            const selectedLoan = window.allLoansData[programName][0];
            console.log('Selected loan data:', selectedLoan);
            
            // Check if loan has zero amounts - if so, don't show closing statement
            let hasValidLoanAmount = false;
            
            // Determine loan type to use appropriate validation
            // For DSCR loans, check multiple possible indicators
            const loanType = selectedLoan.loan_type;
            const isDscrLoan = loanType === 'DSCR Rental Loans' || 
                             programName.includes('DSCR') || 
                             selectedLoan.loan_program?.includes('DSCR') ||
                             (selectedLoan.estimated_closing_statement && !selectedLoan.estimated_closing_statement.loan_amount_section?.total_loan_amount);
            
            console.log('Loan type detection:', {
                loanType,
                programName,
                selectedLoan_loan_program: selectedLoan.loan_program,
                isDscrLoan
            });
            
            if (isDscrLoan) {
                // For DSCR loans, check if there's a valid loan amount in the DSCR structure
                if (selectedLoan.estimated_closing_statement && selectedLoan.estimated_closing_statement.loan_amount_section) {
                    const dscrLoanAmount = parseFloat(selectedLoan.estimated_closing_statement.loan_amount_section.initial_loan_amount || 0);
                    hasValidLoanAmount = dscrLoanAmount > 0;
                    console.log('DSCR loan amount validation:', {
                        dscrLoanAmount,
                        hasValidLoanAmount
                    });
                } else {
                    // If no loan amount section, assume valid for DSCR loans
                    hasValidLoanAmount = true;
                    console.log('DSCR loan detected - no loan amount section, assuming valid');
                }
            } else if (selectedLoan.estimated_closing_statement && selectedLoan.estimated_closing_statement.loan_amount_section) {
                const loanAmounts = selectedLoan.estimated_closing_statement.loan_amount_section;
                const totalLoan = parseFloat(loanAmounts.total_loan_amount || 0);
                const purchaseLoan = parseFloat(loanAmounts.purchase_loan_amount || 0);
                const rehabLoan = parseFloat(loanAmounts.rehab_loan_amount || 0);
                
                // For Fix & Flip and New Construction loans, consider valid if total loan > 0 OR if either purchase or rehab loan > 0
                hasValidLoanAmount = totalLoan > 0 || purchaseLoan > 0 || rehabLoan > 0;
                
                console.log('Non-DSCR loan amount validation:', {
                    loanType,
                    isDscrLoan,
                    totalLoan,
                    purchaseLoan, 
                    rehabLoan,
                    hasValidLoanAmount
                });
            } else {
                // If no closing statement data exists, assume valid for non-DSCR loans
                hasValidLoanAmount = true;
            }
            
            if (!hasValidLoanAmount) {
                // Don't show closing statement for zero loan amounts
                console.log('Skipping closing statement display - loan amount is zero');
                alert('This loan program shows zero loan amount based on your inputs. Please try different parameters or another loan program.');
                return;
            }
            
            // Show closing statement section only if loan amount is valid
            document.getElementById('closingStatementSection').classList.remove('hidden');
            
            // Populate closing statement with selected loan data
            if (selectedLoan.estimated_closing_statement) {
                console.log('Populating closing statement with:', selectedLoan.estimated_closing_statement);
                
                // Use backend calculated closing statement data directly
                populateClosingStatement(selectedLoan.estimated_closing_statement);
            } else {
                console.log('No estimated_closing_statement found in loan data');
            }
            
            // Scroll to closing statement
            setTimeout(() => {
                document.getElementById('closingStatementSection').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
            
            // Update button states to show which program is selected
            const allButtons = document.querySelectorAll('#loanProgramCardsContainer button');
            allButtons.forEach(button => {
                if (button.getAttribute('onclick').includes(programName)) {
                    button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Selected - View Summary Below';
                    button.classList.add('ring-4', 'ring-offset-2');
                    button.classList.add(button.classList.contains('bg-blue-600') ? 'ring-blue-300' : 'ring-green-300');
                } else {
                    button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Check Summary for This Program';
                    button.classList.remove('ring-4', 'ring-offset-2', 'ring-blue-300', 'ring-green-300');
                }
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function populateClosingStatement(closingData) {
            console.log('Populating closing statement with data:', closingData);
            
            // Determine if this is a DSCR loan by checking the data structure
            const isDscrLoan = closingData.loan_amount_section && 
                             closingData.loan_amount_section.initial_loan_amount !== undefined &&
                             closingData.buyer_related_charges &&
                             closingData.buyer_related_charges.purchase_price !== undefined;
            
            console.log('Is DSCR Loan:', isDscrLoan);
            
            // Loan Amount Section
            if (closingData.loan_amount_section) {
                if (isDscrLoan) {
                    // For DSCR loans: Show Initial Loan Amount instead of Purchase Loan
                    const initialLoanAmount = closingData.loan_amount_section.initial_loan_amount || 0;
                    const totalLoan = closingData.loan_amount_section.initial_loan_amount || 0;
                    
                    // Update label and value for DSCR loans
                    document.getElementById('closingPurchaseLoanLabel').textContent = 'Initial Loan Amount:';
                    document.getElementById('closingPurchaseLoan').textContent = '$' + numberWithCommas(initialLoanAmount);
                    document.getElementById('closingTotalLoan').textContent = '$' + numberWithCommas(totalLoan);
                    
                    // Hide Rehab Loan row for DSCR loans
                    const rehabLoanRow = document.getElementById('closingRehabLoan').closest('.flex');
                    if (rehabLoanRow) {
                        rehabLoanRow.style.display = 'none';
                    }
                } else {
                    // For Fix & Flip and New Construction loans: use existing logic
                    document.getElementById('closingPurchaseLoanLabel').textContent = 'Purchase Loan:';
                    document.getElementById('closingPurchaseLoan').textContent = '$' + numberWithCommas(closingData.loan_amount_section.purchase_loan_amount || 0);
                    document.getElementById('closingRehabLoan').textContent = '$' + numberWithCommas(closingData.loan_amount_section.rehab_loan_amount || 0);
                    document.getElementById('closingTotalLoan').textContent = '$' + numberWithCommas(closingData.loan_amount_section.total_loan_amount || 0);
                    
                    // Show Rehab Loan row for non-DSCR loans
                    const rehabLoanRow = document.getElementById('closingRehabLoan').closest('.flex');
                    if (rehabLoanRow) {
                        rehabLoanRow.style.display = 'flex';
                    }
                }
            }
            
            // Buyer Related Charges
            if (closingData.buyer_related_charges) {
                // Update title and subtotal visibility based on loan type
                if (isDscrLoan) {
                    // For DSCR loans: Change title and hide subtotal
                    document.getElementById('closingPropertyCostsTitle').textContent = 'Buyer Related Charges';
                    
                    // Hide subtotal row for DSCR loans
                    const subtotalRow = document.getElementById('closingSubtotalRow');
                    if (subtotalRow) {
                        subtotalRow.style.display = 'none';
                    }
                    
                    // Hide Rehab Budget row for DSCR loans
                    const rehabBudgetRow = document.getElementById('closingRehabBudget').closest('.flex');
                    if (rehabBudgetRow) {
                        rehabBudgetRow.style.display = 'none';
                    }
                } else {
                    // For Fix & Flip / New Construction loans: Keep original title and show subtotal
                    document.getElementById('closingPropertyCostsTitle').textContent = 'Buyer Related Fees';
                    
                    // Show subtotal row for non-DSCR loans
                    const subtotalRow = document.getElementById('closingSubtotalRow');
                    if (subtotalRow) {
                        subtotalRow.style.display = 'block';
                    }
                    
                    // Show and populate Rehab Budget for non-DSCR loans
                    document.getElementById('closingRehabBudget').textContent = '$' + numberWithCommas(closingData.buyer_related_charges.rehab_budget || 0);
                    const rehabBudgetRow = document.getElementById('closingRehabBudget').closest('.flex');
                    if (rehabBudgetRow) {
                        rehabBudgetRow.style.display = 'flex';
                    }
                    
                    // Update subtotal for non-DSCR loans
                    document.getElementById('closingSubtotalBuyer').textContent = '$' + numberWithCommas(closingData.buyer_related_charges.sub_total_buyer_charges || 0);
                }
                
                // Common field for all loan types
                document.getElementById('closingPurchasePrice').textContent = '$' + numberWithCommas(closingData.buyer_related_charges.purchase_price || 0);
            }
            
            // Lender Related Charges
            if (closingData.lender_related_charges) {
                document.getElementById('closingOriginationFee').textContent = '$' + numberWithCommas(closingData.lender_related_charges.lender_origination_fee || 0);
                document.getElementById('closingBrokerFee').textContent = '$' + numberWithCommas(closingData.lender_related_charges.broker_fee || 0);
                document.getElementById('closingUnderwritingFee').textContent = '$' + numberWithCommas(closingData.lender_related_charges.underwriting_processing_fee || 0);
                document.getElementById('closingInterestReserves').textContent = '$' + numberWithCommas(closingData.lender_related_charges.interest_reserves || 0);
            }
            
            // Title & Other Charges
            if (closingData.title_other_charges) {
                document.getElementById('closingTitleCharges').textContent = '$' + numberWithCommas(closingData.title_other_charges.title_charges || 0);
                document.getElementById('closingPropertyInsurance').textContent = '$' + numberWithCommas(closingData.title_other_charges.property_insurance || 0);
                document.getElementById('closingLegalDocFee').textContent = '$' + numberWithCommas(closingData.title_other_charges.legal_doc_prep_fee || 0);
                document.getElementById('closingSubtotalCosts').textContent = '$' + numberWithCommas(closingData.title_other_charges.subtotal_closing_costs || 0);
            }
            
            // Due From Buyer at Closing
            document.getElementById('closingCashDue').textContent = '$' + numberWithCommas(closingData.cash_due_to_buyer || 0);
            
            console.log('Closing statement populated successfully');
        }
                        </script>

                        <script src="{{ url('select2/jquery-3.5.1.js') }}"></script>
                        <script src="{{ url('select2/select2.min.js') }}" defer></script>
                        <script>
                            $(document).ready(function () {
                $('.select2').select2();
                
                // Add the loan type change event listener after Select2 initialization
                $('#loan_type').on('change', async function() {
                    const selectedLoanType = this.value;
                    const propertyTypeSelect = $('#property_type');
                    const stateSelect = $('#state');
                    
                    // Handle field visibility based on loan type
                    handleLoanTypeFieldVisibility(selectedLoanType);
                    
                    if (!selectedLoanType) {
                        // Reset property type and state if no loan type selected
                        propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        propertyTypeSelect.trigger('change');
                        stateSelect.trigger('change');
                        return;
                    }
                    
                    try {
                        let apiEndpoint = '/api/loan-type-options';
                        
                        // For DSCR Rental loans, use different logic
                        if (selectedLoanType === 'DSCR Rental Loans') {
                            // Load DSCR-specific data
                            await loadDscrLoanData();
                        } else {
                            // Regular loan type logic
                            const response = await fetch(`${apiEndpoint}?loan_type=${encodeURIComponent(selectedLoanType)}`);
                            
                            if (response.ok) {
                                const data = await response.json();
                                
                                if (data.success) {
                                    // Update property types
                                    propertyTypeSelect.empty().append('<option value="">-- Select Property Type --</option>');
                                    data.data.property_types.forEach(propertyType => {
                                        propertyTypeSelect.append(`<option value="${propertyType.name}">${propertyType.name}</option>`);
                                    });
                                    propertyTypeSelect.trigger('change');
                                    
                                    // Update states
                                    stateSelect.empty().append('<option value="">-- Select State --</option>');
                                    data.data.states.forEach(state => {
                                        stateSelect.append(`<option value="${state.code}">${state.code}</option>`);
                                    });
                                    stateSelect.trigger('change');
                                } else {
                                    console.error('Failed to load loan type options:', data.message);
                                    resetDropdownsToDefault();
                                }
                            } else {
                                console.error('Failed to fetch loan type options');
                                resetDropdownsToDefault();
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching loan type options:', error);
                        resetDropdownsToDefault();
                    }
                    
                    function resetDropdownsToDefault() {
                        propertyTypeSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        stateSelect.empty().append('<option value="">-- Select Loan Type First --</option>');
                        propertyTypeSelect.trigger('change');
                        stateSelect.trigger('change');
                    }
                });
                
                // Add transaction type change event listener for handling payoff amount visibility
                $('#transaction_type').on('change', function() {
                    const selectedTransactionType = this.value;
                    const selectedLoanType = $('#loan_type').value;
                    const isDscrLoan = selectedLoanType === 'DSCR Rental Loans';
                    
                    // Handle payoff_amount_field visibility - show for DSCR + Refinance Cash Out OR Fix and Flip + any Refinance
                    const payoffField = document.querySelector('#payoff_amount_field');
                    const isFixAndFlip = selectedLoanType === 'Fix and Flip';
                    const isRefinanceTransaction = selectedTransactionType === 'Refinance' || selectedTransactionType === 'Refinance Cash Out';
                    if (payoffField) {
                        if ((isDscrLoan && selectedTransactionType === 'Refinance Cash Out') || (isFixAndFlip && isRefinanceTransaction)) {
                            payoffField.classList.remove('hidden');
                            const input = payoffField.querySelector('input');
                            if (input) input.setAttribute('required', 'required');
                        } else {
                            payoffField.classList.add('hidden');
                            const input = payoffField.querySelector('input');
                            if (input) input.removeAttribute('required');
                        }
                    }

                    // Handle New Construction payoff amount visibility
                    handleNewConstructionPayoffVisibility();
                    
                    // Handle Fix and Flip fields visibility
                    handleFixAndFlipFieldsVisibility();
                });
                
                // Function to handle field visibility based on loan type
                function handleLoanTypeFieldVisibility(loanType) {
                    const isDscrLoan = loanType === 'DSCR Rental Loans';
                    
                    console.log('handleLoanTypeFieldVisibility called with:', loanType, 'isDscrLoan:', isDscrLoan); // Debug log
                    
                    // Handle loan term field visibility - hide for DSCR loans
                    const loanTermField = document.querySelector('#loan_term').closest('div');
                    if (loanTermField) {
                        if (isDscrLoan) {
                            loanTermField.style.display = 'none';
                            const loanTermSelect = document.getElementById('loan_term');
                            if (loanTermSelect) {
                                loanTermSelect.removeAttribute('required');
                                loanTermSelect.disabled = true;
                                loanTermSelect.value = ''; // Clear any selected value
                            }
                        } else {
                            loanTermField.style.display = 'block';
                            const loanTermSelect = document.getElementById('loan_term');
                            if (loanTermSelect) {
                                loanTermSelect.setAttribute('required', 'required');
                                loanTermSelect.disabled = false;
                            }
                            
                            // Update loan term options for regular loans
                            const loanTermSelectJQ = $('#loan_term');
                            loanTermSelectJQ.empty();
                            loanTermSelectJQ.append('<option value="">-- Select Loan Term --</option>');
                            // Regular loan terms (months)
                            loanTermSelectJQ.append('<option value="12">12 Months</option>');
                            loanTermSelectJQ.append('<option value="18">18 Months</option>');
                            loanTermSelectJQ.trigger('change');
                        }
                    }
                    
                    // Regular loan fields - hide for DSCR
                    const regularFields = ['#rehab_budget_field', '#arv_field'];
                    regularFields.forEach(fieldId => {
                        const field = document.querySelector(fieldId);
                        if (field) {
                            if (isDscrLoan) {
                                field.classList.add('hidden');
                                // Remove required attribute from hidden fields
                                const input = field.querySelector('input');
                                if (input) input.removeAttribute('required');
                            } else {
                                field.classList.remove('hidden');
                                // Add required attribute back for visible fields
                                const input = field.querySelector('input');
                                if (input && (fieldId === '#rehab_budget_field' || fieldId === '#arv_field')) {
                                    input.setAttribute('required', 'required');
                                }
                            }
                        }
                    });
                    
                    // DSCR specific fields - show only for DSCR
                    const dscrFields = [
                        '#occupancy_type_field', '#monthly_market_rent_field', '#annual_tax_field',
                        '#annual_insurance_field', '#annual_hoa_field',
                        '#purchase_date_field'
                    ];
                    dscrFields.forEach(fieldId => {
                        const field = document.querySelector(fieldId);
                        console.log('Processing field:', fieldId, 'found:', !!field); // Debug log
                        if (field) {
                            if (isDscrLoan) {
                                field.classList.remove('hidden');
                                console.log('Showing field:', fieldId); // Debug log
                                // Add required attribute for DSCR required fields
                                const input = field.querySelector('input, select');
                                if (input && [
                                    '#occupancy_type_field', '#monthly_market_rent_field', '#annual_tax_field',
                                    '#annual_insurance_field', '#annual_hoa_field'
                                ].includes(fieldId)) {
                                    input.setAttribute('required', 'required');
                                }
                                
                                // Reinitialize Select2 for select elements that were just shown
                                const select = field.querySelector('select');
                                if (select && select.classList.contains('select2')) {
                                    // Destroy existing Select2 instance if it exists
                                    if ($(select).hasClass('select2-hidden-accessible')) {
                                        $(select).select2('destroy');
                                    }
                                    // Reinitialize Select2
                                    $(select).select2();
                                }
                            } else {
                                field.classList.add('hidden');
                                console.log('Hiding field:', fieldId); // Debug log
                                // Remove required attribute from hidden fields
                                const input = field.querySelector('input, select');
                                if (input) input.removeAttribute('required');
                            }
                        }
                    });

                    // Handle payoff_amount_field separately - show for DSCR + Refinance Cash Out OR Fix and Flip + any Refinance
                    const payoffField = document.querySelector('#payoff_amount_field');
                    const isFixAndFlip = loanType === 'Fix and Flip';
                    if (payoffField) {
                        const currentTransactionType = document.getElementById('transaction_type').value;
                        const isRefinanceTransaction = currentTransactionType === 'Refinance' || currentTransactionType === 'Refinance Cash Out';
                        if ((isDscrLoan && currentTransactionType === 'Refinance Cash Out') || (isFixAndFlip && isRefinanceTransaction)) {
                            payoffField.classList.remove('hidden');
                            const input = payoffField.querySelector('input');
                            if (input) input.setAttribute('required', 'required');
                        } else {
                            payoffField.classList.add('hidden');
                            const input = payoffField.querySelector('input');
                            if (input) input.removeAttribute('required');
                        }
                    }

                    // Handle GUC Experience field - only show for New Construction
                    const isNewConstruction = loanType === 'New Construction';
                    const gucExperienceField = document.querySelector('#guc_experience_field');
                    if (gucExperienceField) {
                        if (isNewConstruction) {
                            gucExperienceField.style.display = 'block';
                            const input = gucExperienceField.querySelector('input');
                            if (input) input.setAttribute('required', 'required');
                        } else {
                            gucExperienceField.style.display = 'none';
                            const input = gucExperienceField.querySelector('input');
                            if (input) {
                                input.removeAttribute('required');
                                input.value = '0'; // Reset to default when hidden
                            }
                        }
                    }

                    // Handle Permit Status field - only show for New Construction
                    const permitStatusField = document.querySelector('#permit_status_field');
                    if (permitStatusField) {
                        if (isNewConstruction) {
                            permitStatusField.style.display = 'block';
                            const select = permitStatusField.querySelector('select');
                            if (select) select.setAttribute('required', 'required');
                        } else {
                            permitStatusField.style.display = 'none';
                            const select = permitStatusField.querySelector('select');
                            if (select) {
                                select.removeAttribute('required');
                                select.value = ''; // Reset when hidden
                            }
                        }
                    }

                    // Handle Fix and Flip specific fields - only show for Fix and Flip + Refinance
                    handleFixAndFlipFieldsVisibility();

                    // Handle New Construction Payoff Amount field - only show for New Construction + Refinance
                    handleNewConstructionPayoffVisibility();
                }

                // Function to handle New Construction payoff amount field visibility
                function handleNewConstructionPayoffVisibility() {
                    const loanType = document.getElementById('loan_type').value;
                    const transactionType = document.getElementById('transaction_type').value;
                    const isNewConstruction = loanType === 'New Construction';
                    const isRefinance = transactionType === 'Refinance' || transactionType === 'Refinance Cash Out' || transactionType === 'Refinance No Cash Out';
                    
                    const payoffField = document.querySelector('#new_construction_payoff_amount_field');
                    if (payoffField) {
                        if (isNewConstruction && isRefinance) {
                            payoffField.style.display = 'block';
                            const input = payoffField.querySelector('input');
                            if (input) input.setAttribute('required', 'required');
                        } else {
                            payoffField.style.display = 'none';
                            const input = payoffField.querySelector('input');
                            if (input) {
                                input.removeAttribute('required');
                                input.value = ''; // Reset when hidden
                            }
                        }
                    }
                }

                // Function to handle Fix and Flip specific fields visibility
                function handleFixAndFlipFieldsVisibility() {
                    const loanType = document.getElementById('loan_type').value;
                    const transactionType = document.getElementById('transaction_type').value;
                    const isFixAndFlip = loanType === 'Fix and Flip';
                    const isRefinance = transactionType === 'Refinance' || transactionType === 'Refinance Cash Out';
                    
                    // Handle Seasoning Period field
                    const seasoningField = document.querySelector('#fix_flip_seasoning_field');
                    if (seasoningField) {
                        if (isFixAndFlip && isRefinance) {
                            seasoningField.classList.remove('hidden');
                            const select = seasoningField.querySelector('select');
                            if (select) select.setAttribute('required', 'required');
                        } else {
                            seasoningField.classList.add('hidden');
                            const select = seasoningField.querySelector('select');
                            if (select) {
                                select.removeAttribute('required');
                                select.value = ''; // Reset when hidden
                            }
                        }
                    }
                    
                    // Handle Fix and Flip Payoff Amount field
                    const fixFlipPayoffField = document.querySelector('#fix_flip_payoff_amount_field');
                    if (fixFlipPayoffField) {
                        if (isFixAndFlip && isRefinance) {
                            fixFlipPayoffField.classList.remove('hidden');
                            const input = fixFlipPayoffField.querySelector('input');
                            if (input) input.setAttribute('required', 'required');
                        } else {
                            fixFlipPayoffField.classList.add('hidden');
                            const input = fixFlipPayoffField.querySelector('input');
                            if (input) {
                                input.removeAttribute('required');
                                input.value = ''; // Reset when hidden
                            }
                        }
                    }
                }
                
                // Function to load DSCR-specific data (property types, states, occupancy types, prepay penalties)
                async function loadDscrLoanData() {
                    console.log('loadDscrLoanData called'); // Debug log
                    try {
                        // Load DSCR property types
                        console.log('Loading DSCR property types...'); // Debug log
                        const propertyResponse = await fetch('/api/dscr-property-types');
                        if (propertyResponse.ok) {
                            const propertyData = await propertyResponse.json();
                            console.log('Property types API response:', propertyData); // Debug log
                            if (propertyData.success) {
                                const propertyTypeSelect = $('#property_type');
                                propertyTypeSelect.empty().append('<option value="">-- Select Property Type --</option>');
                                propertyData.data.forEach(propertyType => {
                                    propertyTypeSelect.append(`<option value="${propertyType.name}">${propertyType.name}</option>`);
                                });
                                propertyTypeSelect.trigger('change');
                                console.log('Property types loaded successfully from database'); // Debug log
                            }
                        } else {
                            console.error('Failed to load property types from database, status:', propertyResponse.status);
                            // Add default DSCR-eligible property types
                            console.log('Adding default DSCR property types');
                            const propertyTypeSelect = $('#property_type');
                            propertyTypeSelect.empty().append('<option value="">-- Select Property Type --</option>');
                            propertyTypeSelect.append('<option value="Single Family">Single Family</option>');
                            propertyTypeSelect.append('<option value="Townhome">Townhome</option>');
                            propertyTypeSelect.append('<option value="Condo">Condo</option>');
                            propertyTypeSelect.trigger('change');
                        }

                        // Load states for DSCR loans
                        console.log('Loading DSCR states...'); // Debug log
                        const stateResponse = await fetch('/api/dscr-states');
                        if (stateResponse.ok) {
                            const stateData = await stateResponse.json();
                            if (stateData.success) {
                                const stateSelect = $('#state');
                                stateSelect.empty().append('<option value="">-- Select State --</option>');
                                stateData.data.forEach(state => {
                                    stateSelect.append(`<option value="${state.code}">${state.code}</option>`);
                                });
                                stateSelect.trigger('change');
                                console.log('States loaded successfully'); // Debug log
                            }
                        } else {
                            console.error('Failed to load states, status:', stateResponse.status);
                            // Fallback to regular states API
                            console.log('Falling back to regular states API');
                            try {
                                const fallbackResponse = await fetch('/api/loan-type-options?loan_type=DSCR%20Rental%20Loans');
                                if (fallbackResponse.ok) {
                                    const fallbackData = await fallbackResponse.json();
                                    console.log('Fallback states data:', fallbackData);
                                    if (fallbackData.success && fallbackData.data.states) {
                                        const stateSelect = $('#state');
                                        stateSelect.empty().append('<option value="">-- Select State --</option>');
                                        fallbackData.data.states.forEach(state => {
                                            stateSelect.append(`<option value="${state.code}">${state.code}</option>`);
                                        });
                                        stateSelect.trigger('change');
                                        console.log('Fallback states loaded successfully');
                                    }
                                } else {
                                    console.log('Fallback API also failed, adding default states');
                                    const stateSelect = $('#state');
                                    stateSelect.empty().append('<option value="">-- Select State --</option>');
                                    stateSelect.append('<option value="CA">CA</option>');
                                    stateSelect.append('<option value="TX">TX</option>');
                                    stateSelect.append('<option value="FL">FL</option>');
                                    stateSelect.append('<option value="NY">NY</option>');
                                    stateSelect.trigger('change');
                                }
                            } catch (fallbackError) {
                                console.error('Fallback API error:', fallbackError);
                                console.log('Adding default states due to fallback error');
                                const stateSelect = $('#state');
                                stateSelect.empty().append('<option value="">-- Select State --</option>');
                                stateSelect.append('<option value="CA">CA</option>');
                                stateSelect.append('<option value="TX">TX</option>');
                                stateSelect.append('<option value="FL">FL</option>');
                                stateSelect.append('<option value="NY">NY</option>');
                                stateSelect.trigger('change');
                            }
                        }
                        
                        // Load occupancy types
                        console.log('Loading occupancy types...'); // Debug log
                        await loadOccupancyTypes();
                        
                    } catch (error) {
                        console.error('Error loading DSCR loan data:', error);
                    }
                }
                
                // Function to load occupancy types for DSCR loans
                async function loadOccupancyTypes() {
                    console.log('loadOccupancyTypes called'); // Debug log
                    try {
                        const response = await fetch('/api/occupancy-types');
                        console.log('Occupancy types API response status:', response.status); // Debug log
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Occupancy types data:', data); // Debug log
                            if (data.success) {
                                const occupancySelect = $('#occupancy_type');
                                console.log('Occupancy select element found:', occupancySelect.length > 0); // Debug log
                                occupancySelect.empty().append('<option value="">-- Select Occupancy Type --</option>');
                                data.data.forEach(occupancy => {
                                    occupancySelect.append(`<option value="${occupancy.name}">${occupancy.name}</option>`);
                                });
                                occupancySelect.trigger('change');
                                console.log('Occupancy types loaded successfully, total options:', data.data.length); // Debug log
                            }
                        } else {
                            console.error('Failed to load occupancy types, status:', response.status);
                            // Add some default options for testing
                            console.log('Adding default occupancy options for testing');
                            const occupancySelect = $('#occupancy_type');
                            occupancySelect.empty().append('<option value="">-- Select Occupancy Type --</option>');
                            occupancySelect.append('<option value="Owner Occupied">Owner Occupied</option>');
                            occupancySelect.append('<option value="Investment Property">Investment Property</option>');
                            occupancySelect.append('<option value="Second Home">Second Home</option>');
                            occupancySelect.trigger('change');
                        }
                    } catch (error) {
                        console.error('Error loading occupancy types:', error);
                        // Add some default options for testing
                        console.log('Adding default occupancy options due to error');
                        const occupancySelect = $('#occupancy_type');
                        occupancySelect.empty().append('<option value="">-- Select Occupancy Type --</option>');
                        occupancySelect.append('<option value="Owner Occupied">Owner Occupied</option>');
                        occupancySelect.append('<option value="Investment Property">Investment Property</option>');
                        occupancySelect.append('<option value="Second Home">Second Home</option>');
                        occupancySelect.trigger('change');
                    }
                }
            });

            // Function to handle loan amount changes in the cards
            window.handleCardLoanAmountChange = function(input, programName) {
                const newAmount = parseFloat(input.value) || 0;
                const maxAmount = parseFloat(input.dataset.maxAmount) || 0;
                
                // Validate amount doesn't exceed maximum
                if (newAmount > maxAmount) {
                    input.value = maxAmount;
                    alert(`Loan amount cannot exceed the maximum qualified amount of $${numberWithCommas(maxAmount)}`);
                    return;
                }
                
                // Update the loan data and recalculate
                updateLoanCalculations(programName, newAmount);
            };

            // Function to update loan calculations with new purchase loan amount
            window.updateLoanCalculations = async function(programName, newPurchaseLoanAmount, rowIndex = null) {
                try {
                    // Get the original loan data for this program
                    const originalLoanData = window.allLoansData[programName][0];
                    if (!originalLoanData) {
                        console.error('Original loan data not found for program:', programName);
                        return;
                    }
                    
                    // Calculate new total loan amount
                    const rehabLoanAmount = originalLoanData.loan_type_and_loan_program_table.rehab_loan_up_to || 0;
                    const newTotalLoanAmount = newPurchaseLoanAmount + rehabLoanAmount;
                    
                    // Update the total loan display in table
                    if (rowIndex !== null) {
                        const totalLoanElement = document.getElementById(`total_loan_${rowIndex}`);
                        if (totalLoanElement) {
                            totalLoanElement.textContent = `$${numberWithCommas(newTotalLoanAmount)}`;
                        }
                    }
                    
                    // Update the total loan display in card
                    const cardTotalElement = document.getElementById(`card_total_loan_${programName.replace(/\s+/g, '_')}`);
                    if (cardTotalElement) {
                        cardTotalElement.textContent = `$${numberWithCommas(newTotalLoanAmount)}`;
                    }
                    
                    // Calculate new lender fees based on the new total loan amount
                    const newLenderFees = calculateLenderFees(newTotalLoanAmount, originalLoanData);
                    
                    // Update the loan data with new values
                    const updatedLoanData = JSON.parse(JSON.stringify(originalLoanData)); // Deep copy
                    updatedLoanData.loan_type_and_loan_program_table.purchase_loan_up_to = newPurchaseLoanAmount;
                    updatedLoanData.loan_type_and_loan_program_table.total_loan_up_to = newTotalLoanAmount;
                    
                    // Update closing statement with new fees
                    if (updatedLoanData.estimated_closing_statement) {
                        updatedLoanData.estimated_closing_statement.loan_amount_section.purchase_loan_amount = newPurchaseLoanAmount;
                        updatedLoanData.estimated_closing_statement.loan_amount_section.total_loan_amount = newTotalLoanAmount;
                        updatedLoanData.estimated_closing_statement.lender_related_charges = newLenderFees;
                        
                        // Recalculate subtotal closing costs
                        const lenderCharges = newLenderFees;
                        const titleCharges = updatedLoanData.estimated_closing_statement.title_other_charges;
                        const newSubtotal = (lenderCharges.lender_origination_fee || 0) + 
                                          (lenderCharges.broker_fee || 0) + 
                                          (lenderCharges.underwriting_processing_fee || 0) + 
                                          (lenderCharges.interest_reserves || 0) +
                                          (titleCharges.title_charges || 0) + 
                                          (titleCharges.property_insurance || 0) + 
                                          (titleCharges.legal_doc_prep_fee || 0);
                        
                        updatedLoanData.estimated_closing_statement.title_other_charges.subtotal_closing_costs = newSubtotal;
                        updatedLoanData.estimated_closing_statement.cash_due_to_buyer = newSubtotal;
                    }
                    
                    // Update the global loan data
                    window.allLoansData[programName][0] = updatedLoanData;
                    
                    // If this program is currently selected, update the closing statement
                    const closingSection = document.getElementById('closingStatementSection');
                    if (!closingSection.classList.contains('hidden')) {
                        populateClosingStatement(updatedLoanData.estimated_closing_statement);
                    }
                    
                } catch (error) {
                    console.error('Error updating loan calculations:', error);
                }
            };

            // Function to handle DSCR loan amount changes
            window.handleCardDscrLoanAmountChange = function(input, programName) {
                const newAmount = parseFloat(input.value) || 0;
                const maxAmount = parseFloat(input.dataset.maxAmount) || 0;
                
                // Validate amount doesn't exceed maximum
                if (newAmount > maxAmount) {
                    input.value = maxAmount;
                    alert(`DSCR loan amount cannot exceed the maximum qualified amount of $${numberWithCommas(maxAmount)}`);
                    return;
                }
                
                // Update DSCR loan calculations with new loan amount
                updateDscrLoanCalculations(programName, newAmount);
            };

            // Function to update DSCR loan calculations with new loan amount
            window.updateDscrLoanCalculations = async function(programName, newLoanAmount) {
                try {
                    console.log(`Updating DSCR loan calculations for ${programName} with amount: $${numberWithCommas(newLoanAmount)}`);
                    
                    // Get the original loan data for this program to extract the current parameters
                    const originalLoanData = window.allLoansData[programName][0];
                    if (!originalLoanData) {
                        console.error('Original DSCR loan data not found for program:', programName);
                        return;
                    }

                    // Get current form data to build API request
                    const formData = new FormData(document.getElementById('loanCalculatorForm'));
                    
                    // Build API parameters for DSCR loan with the new loan amount
                    const apiParams = new URLSearchParams();
                    
                    // Add required parameters with fallbacks
                    apiParams.append('credit_score', formData.get('credit_score') || '700');
                    apiParams.append('experience', formData.get('experience') || '4');
                    apiParams.append('loan_type', 'DSCR Rental Loans');
                    apiParams.append('transaction_type', formData.get('transaction_type') || 'Purchase');
                    apiParams.append('purchase_price', formData.get('purchase_price') || '475000');
                    apiParams.append('broker_points', formData.get('broker_points') || '1');
                    apiParams.append('state', formData.get('state') || 'AL');
                    apiParams.append('property_type', formData.get('property_type') || 'Single Family');
                    apiParams.append('occupancy_type', formData.get('occupancy_type') || 'Occupied');
                    apiParams.append('monthly_market_rent', formData.get('monthly_market_rent') || '5200');
                    apiParams.append('annual_tax', formData.get('annual_tax') || '4400');
                    apiParams.append('annual_insurance', formData.get('annual_insurance') || '2400');
                    apiParams.append('annual_hoa', formData.get('annual_hoa') || '0');
                    
                    // Add current dropdown values with fallbacks
                    apiParams.append('loan_term', window.currentDscrValues?.loanTerm || '30 Year Fixed');
                    apiParams.append('lender_points', window.currentDscrValues?.lenderPoints || '2.000');
                    apiParams.append('pre_pay_penalty', window.currentDscrValues?.prepayPenalty || '5 Year Prepay');
                    
                    // Add the new user input loan amount
                    apiParams.append('user_input_loan_amount', newLoanAmount);
                    
                    // Filter to only get this specific loan program
                    apiParams.append('loan_program', programName);

                    console.log('API Parameters:', apiParams.toString());

                    // Make API call to recalculate DSCR loan with new amount (using GET method)
                    const apiUrl = `/api/loan-matrix-dscr?${apiParams.toString()}`;
                    console.log('Making API call to:', apiUrl);
                    
                    const response = await fetch(apiUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    console.log('API Response status:', response.status);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('API Response data:', data);
                    
                    if (data.success && data.data && data.data.length > 0) {
                        const updatedLoanData = data.data[0];
                        console.log('Updated DSCR loan data:', updatedLoanData);
                        
                        // Update the global loan data
                        window.allLoansData[programName][0] = updatedLoanData;
                        
                        // Update the card display with new values
                        updateDscrCardDisplay(programName, updatedLoanData);
                        
                        // If this program is currently selected, update the closing statement
                        const closingSection = document.getElementById('closingStatementSection');
                        if (!closingSection.classList.contains('hidden') && window.selectedLoanProgram === programName) {
                            populateClosingStatement(updatedLoanData.estimated_closing_statement);
                        }
                        
                        console.log('DSCR loan calculations updated successfully');
                        
                    } else {
                        console.error('Failed to update DSCR loan calculations:', data);
                        const errorMsg = data.message || 'Failed to update loan calculations. Please try again.';
                        alert(errorMsg);
                    }
                    
                } catch (error) {
                    console.error('Error updating DSCR loan calculations:', error);
                    console.error('Error details:', error.message);
                    alert('An error occurred while updating loan calculations. Please check the console for details.');
                }
            };

            // Function to update DSCR card display with new calculated values
            window.updateDscrCardDisplay = function(programName, updatedLoanData) {
                const loanData = updatedLoanData.loan_program_values;
                
                // Find the card for this program
                const cards = document.querySelectorAll('#loanProgramCardsContainer > div');
                let targetCard = null;
                
                cards.forEach(card => {
                    const headerText = card.querySelector('h3');
                    if (headerText && headerText.textContent === programName) {
                        targetCard = card;
                    }
                });
                
                if (!targetCard) {
                    console.error('Could not find card for program:', programName);
                    return;
                }
                
                // Update monthly payment
                const monthlyPaymentSpan = targetCard.querySelector('.text-green-600');
                if (monthlyPaymentSpan && loanData.monthly_payment) {
                    monthlyPaymentSpan.textContent = `$${numberWithCommas(loanData.monthly_payment)}`;
                }
                
                // Update interest rate
                const interestRateSpan = targetCard.querySelector('.text-orange-600');
                if (interestRateSpan && loanData.interest_rate) {
                    interestRateSpan.textContent = `${loanData.interest_rate}%`;
                }
                
                // Update max LTV
                const maxLtvSpan = targetCard.querySelector('.text-purple-600');
                if (maxLtvSpan && loanData.max_ltv) {
                    maxLtvSpan.textContent = `${loanData.max_ltv}%`;
                }
                
                console.log(`Updated card display for ${programName}`);
            };
            
            // Function to calculate lender fees based on loan amount
            window.calculateLenderFees = function(totalLoanAmount, originalLoanData) {
                const userInputs = originalLoanData.user_inputs;
                const originalFees = originalLoanData.estimated_closing_statement.lender_related_charges;
                
                // Get rates from the loan data (these are percentages)
                const lenderPointsRate = originalLoanData.loan_type_and_loan_program_table.lender_points; // e.g., 2.5%
                const brokerPointsRate = parseFloat(userInputs.broker_points); // e.g., 1%
                
                // Calculate new fees based on the new total loan amount
                const newOriginationFee = (totalLoanAmount * lenderPointsRate) / 100;
                const newBrokerFee = (totalLoanAmount * brokerPointsRate) / 100;
                
                return {
                    lender_origination_fee: newOriginationFee,
                    broker_fee: newBrokerFee,
                    underwriting_processing_fee: originalFees.underwriting_processing_fee, // This usually stays constant
                    interest_reserves: originalFees.interest_reserves // This might change but for now keep original
                };
            };

            // PDF Generation Function
            window.downloadLoanPDF = function() {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                
                // Company Header
                pdf.setFontSize(16);
                pdf.setFont(undefined, 'bold');
                pdf.text('GOLDMAN FUNDING', 105, 15, { align: 'center' });
                
                pdf.setFontSize(10);
                pdf.setFont(undefined, 'normal');
                pdf.text('200 Broadhollow rd Suite 207', 105, 22, { align: 'center' });
                pdf.text('Melville NY, 11747', 105, 27, { align: 'center' });
                pdf.text('T: 631-602-0460', 105, 32, { align: 'center' });
                pdf.text('www.goldman-funding.com', 105, 37, { align: 'center' });
                
                let yPos = 50;
                const margin = 10;
                const pageWidth = 190; // Use full width
                
                // Loan Terms Summary Section
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'bold');
                pdf.text('LOAN TERMS SUMMARY', margin, yPos);
                yPos += 8;
                
                pdf.setFontSize(9);
                pdf.setFont(undefined, 'normal');
                const summaryText = 'Congratulations! You are Pre Approved based on our preliminary review of your information provided to Goldman Funding. The terms of your prequalification are described below. Note: This is preliminary term sheet and not a binding agreement. Terms are only valid for 30 days after issue.';
                const splitSummary = pdf.splitTextToSize(summaryText, pageWidth - (margin * 2));
                pdf.text(splitSummary, margin, yPos);
                yPos += splitSummary.length * 4 + 10;
                
                // Get form data
                const formData = getFormDataForPDF();
                
                // Section 1: User Inputs
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'bold');
                pdf.text('USER INPUTS', margin, yPos);
                yPos += 8;
                
                pdf.setFontSize(9);
                pdf.setFont(undefined, 'normal');
                
                // Create user inputs table
                const inputsTable = createUserInputsTable(formData);
                yPos = drawTable(pdf, inputsTable.headers, inputsTable.data, yPos, margin, pageWidth);
                
                yPos += 10;
                
                // Section 2: All Loan Programs with Selected Column
                if (window.allLoansData && Object.keys(window.allLoansData).length > 0) {
                    pdf.setFontSize(12);
                    pdf.setFont(undefined, 'bold');
                    pdf.text('LOAN PROGRAMS', margin, yPos);
                    yPos += 8;
                    
                    const loanTable = createAllLoanProgramsTable();
                    yPos = drawTable(pdf, loanTable.headers, loanTable.data, yPos, margin, pageWidth);
                    
                    yPos += 10;
                    
                    // Title and Settlement Section (before Estimated Closing Statement)
                    if (yPos > 200) {
                        pdf.addPage();
                        yPos = 20;
                    }
                    
                    pdf.setFontSize(12);
                    pdf.setFont(undefined, 'bold');
                    pdf.text('TITLE AND SETTLEMENT', margin, yPos);
                    yPos += 8;
                    
                    pdf.setFontSize(9);
                    pdf.setFont(undefined, 'normal');
                    const titleText = 'Borrowers to provide their own Title and Settlement Company. If none available, Goldman Funding will assign a preferred title company to handle the Title report and settlement. Loan Documents may be signed remotely from borrowers location. Closing Agent requires at least 48 Hours Notice to schedule closing with borrower.';
                    const splitTitleText = pdf.splitTextToSize(titleText, pageWidth - (margin * 2));
                    pdf.text(splitTitleText, margin, yPos);
                    yPos += splitTitleText.length * 4 + 15;
                    
                    // Section 3: Estimated Closing Statement
                    if (yPos > 200) {
                        pdf.addPage();
                        yPos = 20;
                    }
                    
                    if (window.selectedLoanProgram) {
                        pdf.setFontSize(12);
                        pdf.setFont(undefined, 'bold');
                        pdf.text('ESTIMATED CLOSING COSTS', margin, yPos);
                        yPos += 3;
                        
                        pdf.setFontSize(9);
                        pdf.setFont(undefined, 'normal');
                        const closingCostsText = 'Closing costs are calculated by adding all buyer related charges such as purchase price, construction costs and closing costs. These costs are offset against the approved loan amount, as outlined in your loan terms. "Due from buyer at closing" represents the required funds due "from" the buyer at closing. For refinance transactions, a negative number represents the net funds (or cash out) "due to" the borrower at closing.';
                        const splitClosingText = pdf.splitTextToSize(closingCostsText, pageWidth - (margin * 2));
                        pdf.text(splitClosingText, margin, yPos);
                        yPos += splitClosingText.length * 4 + 8;
                        
                        const closingTable = createClosingStatementTable();
                        yPos = drawTable(pdf, closingTable.headers, closingTable.data, yPos, margin, pageWidth);
                    }
                }
                
                // Footer Note - add at the end
                if (yPos > 220) {
                    pdf.addPage();
                    yPos = 20;
                } else {
                    yPos += 15;
                }
                
                pdf.setFontSize(9);
                pdf.setFont(undefined, 'normal');
                const footerText = '(1) Buyer related charges represent the costs incurred by borrower to purchase and rehab the subject property. For refinance transactions, the buyer related charges = current mortgage payoff + rehab costs. Please be advised Title and Property Insurance charges are estimates and subject to change. Borrower may shop for these services. All lender based charges will be accurate and solely based on borrower stated credentials and transaction details. With any questions regarding your closing costs contact your loan officer.';
                const splitFooterText = pdf.splitTextToSize(footerText, pageWidth - (margin * 2));
                pdf.text(splitFooterText, margin, yPos);
                
                // Save PDF
                const timestamp = new Date().toISOString().split('T')[0];
                const filename = `goldman-funding-loan-report-${timestamp}.pdf`;
                pdf.save(filename);
            };
            
            // Helper function to get form data
            function getFormDataForPDF() {
                const form = document.getElementById('loanCalculatorForm');
                const formData = new FormData(form);
                const data = {};
                
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                return data;
            }
            
            // Helper function to create user inputs table
            function createUserInputsTable(formData) {
                const headers = ['Field', 'Value'];
                const data = [];
                
                const fieldLabels = {
                    'credit_score': 'Credit Score',
                    'experience': 'Experience',
                    'guc_experience': 'GUC Experience (# of Deals)',
                    'loan_type': 'Loan Type',
                    'transaction_type': 'Transaction Type',
                    'loan_term': 'Loan Term',
                    'purchase_price': 'Purchase Price',
                    'broker_points': 'Broker Points',
                    'state': 'State',
                    'property_type': 'Property Type',
                    'permit_status': 'Permit Status',
                    'new_construction_payoff_amount': 'Payoff Amount (New Construction)',
                    'fix_flip_seasoning_period': 'Seasoning Period',
                    'fix_flip_payoff_amount': 'Payoff Amount (Fix and Flip)',
                    'occupancy_type': 'Occupancy Type',
                    'monthly_market_rent': 'Monthly Market Rent',
                    'annual_tax': 'Annual Tax',
                    'annual_insurance': 'Annual Insurance',
                    'annual_hoa': 'Annual HOA',
                    'dscr': 'DSCR',
                    'arv': 'ARV',
                    'rehab_budget': 'Rehab Budget',
                    'payoff_amount': 'Payoff Amount',
                    'lender_points': 'Lender Points',
                    'pre_pay_penalty': 'Pre Pay Penalty',
                    'purchase_date': 'Purchase Date'
                };
                
                Object.keys(fieldLabels).forEach(key => {
                    if (formData[key] && formData[key] !== '') {
                        let value = formData[key];
                        
                        // Format monetary values
                        if (['purchase_price', 'arv', 'rehab_budget', 'monthly_market_rent', 'annual_tax', 'annual_insurance', 'annual_hoa', 'payoff_amount'].includes(key)) {
                            value = '$' + numberWithCommas(value);
                        }
                        
                        // Format percentage values
                        if (['broker_points', 'lender_points', 'dscr'].includes(key)) {
                            value = value + '%';
                        }
                        
                        // Format loan term
                        if (key === 'loan_term') {
                            value = value + ' months';
                        }
                        
                        data.push([fieldLabels[key], value]);
                    }
                });
                
                return { headers, data };
            }
            
            // Helper function to create selected loan table
            function createSelectedLoanTable() {
                // Check if current loan is DSCR
                const isDscrLoan = window.selectedLoanProgram && window.allLoansData && 
                                   window.allLoansData[window.selectedLoanProgram] && 
                                   window.allLoansData[window.selectedLoanProgram][0].loan_type === 'DSCR Rental Loans';
                
                const headers = isDscrLoan 
                    ? ['Program', 'Term', 'Rate', 'Points', 'Max LTV', 'Initial Loan', 'Total Loan']
                    : ['Program', 'Term', 'Rate', 'Points', 'Max LTV', 'Purchase Loan', 'Rehab Loan', 'Total Loan'];
                const data = [];
                
                if (window.selectedLoanProgram && window.allLoansData && window.allLoansData[window.selectedLoanProgram]) {
                    const loan = window.allLoansData[window.selectedLoanProgram][0];
                    const loanData = loan.loan_type_and_loan_program_table || loan.loan_program_values;
                    
                    if (loanData) {
                        if (isDscrLoan) {
                            // DSCR loan table row - exclude rehab loan column
                            data.push([
                                window.selectedLoanProgram,
                                (loanData.loan_term || loanData.term || 'N/A') + ' months',
                                (loanData.interest_rate || loanData.rate || 'N/A') + '%',
                                (loanData.lender_points || loanData.points || 'N/A') + '%',
                                (loanData.max_ltv || 'N/A') + '%',
                                '$' + numberWithCommas(loanData.purchase_loan_up_to || loanData.loan_amount || 0),
                                '$' + numberWithCommas(loanData.total_loan_up_to || loanData.loan_amount || 0)
                            ]);
                        } else {
                            // Fix & Flip / New Construction loan table row
                            data.push([
                                window.selectedLoanProgram,
                                (loanData.loan_term || loanData.term || 'N/A') + ' months',
                                (loanData.interest_rate || loanData.rate || 'N/A') + '%',
                                (loanData.lender_points || loanData.points || 'N/A') + '%',
                                (loanData.max_ltv || 'N/A') + '%',
                                '$' + numberWithCommas(loanData.purchase_loan_up_to || 0),
                                '$' + numberWithCommas(loanData.rehab_loan_up_to || 0),
                                '$' + numberWithCommas(loanData.total_loan_up_to || (loanData.purchase_loan_up_to + loanData.rehab_loan_up_to) || 0)
                            ]);
                        }
                    }
                }
                
                return { headers, data };
            }
            
            // Helper function to create all loan programs table with selected column
            function createAllLoanProgramsTable() {
                // Check if we have any DSCR loans in the data
                let hasDscrLoans = false;
                if (window.allLoansData && Object.keys(window.allLoansData).length > 0) {
                    hasDscrLoans = Object.values(window.allLoansData).some(loanArray => 
                        loanArray[0].loan_type === 'DSCR Rental Loans'
                    );
                }
                
                const headers = hasDscrLoans 
                    ? ['Program', 'Term', 'Rate', 'Points', 'Max LTV', 'Initial Loan', 'Total Loan', 'Selected']
                    : ['Program', 'Term', 'Rate', 'Points', 'Max LTV', 'Purchase Loan', 'Rehab Loan', 'Total Loan', 'Selected'];
                const data = [];
                
                if (window.allLoansData && Object.keys(window.allLoansData).length > 0) {
                    Object.keys(window.allLoansData).forEach(programName => {
                        const loan = window.allLoansData[programName][0];
                        const loanData = loan.loan_type_and_loan_program_table || loan.loan_program_values;
                        
                        if (loanData) {
                            const isSelected = window.selectedLoanProgram === programName;
                            const isDscrLoan = loan.loan_type === 'DSCR Rental Loans';
                            
                            if (hasDscrLoans) {
                                // When any DSCR loans exist, use DSCR format for all (simplified view)
                                data.push([
                                    programName,
                                    (loanData.loan_term || loanData.term || 'N/A') + ' months',
                                    (loanData.interest_rate || loanData.rate || 'N/A') + '%',
                                    (loanData.lender_points || loanData.points || 'N/A') + '%',
                                    (loanData.max_ltv || 'N/A') + '%',
                                    '$' + numberWithCommas(isDscrLoan ? (loanData.loan_amount || 0) : (loanData.purchase_loan_up_to || 0)),
                                    '$' + numberWithCommas(isDscrLoan ? (loanData.loan_amount || 0) : (loanData.total_loan_up_to || (loanData.purchase_loan_up_to + loanData.rehab_loan_up_to) || 0)),
                                    isSelected ? 'YES' : 'NO'
                                ]);
                            } else {
                                // Standard Fix & Flip / New Construction format
                                data.push([
                                    programName,
                                    (loanData.loan_term || loanData.term || 'N/A') + ' months',
                                    (loanData.interest_rate || loanData.rate || 'N/A') + '%',
                                    (loanData.lender_points || loanData.points || 'N/A') + '%',
                                    (loanData.max_ltv || 'N/A') + '%',
                                    '$' + numberWithCommas(loanData.purchase_loan_up_to || 0),
                                    '$' + numberWithCommas(loanData.rehab_loan_up_to || 0),
                                    '$' + numberWithCommas(loanData.total_loan_up_to || (loanData.purchase_loan_up_to + loanData.rehab_loan_up_to) || 0),
                                    isSelected ? 'YES' : 'NO'
                                ]);
                            }
                        }
                    });
                }
                
                return { headers, data };
            }
            
            // Helper function to create closing statement table
            function createClosingStatementTable() {
                const headers = ['Description', 'Amount'];
                const data = [];
                
                if (window.selectedLoanProgram && window.allLoansData && window.allLoansData[window.selectedLoanProgram]) {
                    const loan = window.allLoansData[window.selectedLoanProgram][0];
                    const closingData = loan.estimated_closing_statement;
                    const isDscrLoan = loan.loan_type === 'DSCR Rental Loans';
                    
                    if (closingData) {
                        // Loan Amount Section
                        data.push(['LOAN AMOUNT SECTION', '']);
                        if (closingData.loan_amount_section) {
                            if (isDscrLoan) {
                                // For DSCR loans: Show Initial Loan Amount instead of Purchase Loan
                                data.push(['Initial Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.initial_loan_amount || 0)]);
                                data.push(['Total Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.initial_loan_amount || 0)]);
                            } else {
                                // For Fix & Flip and New Construction loans
                                data.push(['Purchase Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.purchase_loan_amount || 0)]);
                                data.push(['Rehab Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.rehab_loan_amount || 0)]);
                                data.push(['Total Loan Amount', '$' + numberWithCommas(closingData.loan_amount_section.total_loan_amount || 0)]);
                            }
                        }
                        
                        data.push(['', '']); // Empty row
                        
                        // Buyer Related Charges
                        data.push(['BUYER RELATED CHARGES', '']);
                        if (closingData.buyer_related_charges) {
                            data.push(['Purchase Price', '$' + numberWithCommas(closingData.buyer_related_charges.purchase_price || 0)]);
                            
                            if (!isDscrLoan) {
                                // Only show rehab budget and subtotal for non-DSCR loans
                                data.push(['Rehab Budget', '$' + numberWithCommas(closingData.buyer_related_charges.rehab_budget || 0)]);
                                data.push(['Sub Total Buyer Charges', '$' + numberWithCommas(closingData.buyer_related_charges.sub_total_buyer_charges || 0)]);
                            }
                        }
                        
                        data.push(['', '']); // Empty row
                        
                        // Lender Related Charges
                        data.push(['LENDER RELATED CHARGES', '']);
                        if (closingData.lender_related_charges) {
                            data.push(['Lender Origination Fee', '$' + numberWithCommas(closingData.lender_related_charges.lender_origination_fee || 0)]);
                            data.push(['Broker Fee', '$' + numberWithCommas(closingData.lender_related_charges.broker_fee || 0)]);
                            data.push(['Underwriting Processing Fee', '$' + numberWithCommas(closingData.lender_related_charges.underwriting_processing_fee || 0)]);
                            data.push(['Interest Reserves', '$' + numberWithCommas(closingData.lender_related_charges.interest_reserves || 0)]);
                        }
                        
                        data.push(['', '']); // Empty row
                        
                        // Title & Other Charges
                        data.push(['TITLE & OTHER CHARGES', '']);
                        if (closingData.title_other_charges) {
                            data.push(['Title Charges', '$' + numberWithCommas(closingData.title_other_charges.title_charges || 0)]);
                            data.push(['Property Insurance', '$' + numberWithCommas(closingData.title_other_charges.property_insurance || 0)]);
                            data.push(['Legal Doc Prep Fee', '$' + numberWithCommas(closingData.title_other_charges.legal_doc_prep_fee || 0)]);
                            data.push(['Subtotal Closing Costs', '$' + numberWithCommas(closingData.title_other_charges.subtotal_closing_costs || 0)]);
                        }
                        
                        data.push(['', '']); // Empty row
                        data.push(['DUE FROM BUYER AT CLOSING', '$' + numberWithCommas(closingData.cash_due_to_buyer || 0)]);
                    }
                }
                
                return { headers, data };
            }
            
            // Helper function to draw tables in PDF
            function drawTable(pdf, headers, data, startY, margin, pageWidth) {
                const colWidth = (pageWidth - (margin * 2)) / headers.length;
                let yPos = startY;
                const pageHeight = 280;
                
                // Draw headers
                pdf.setFontSize(8);
                pdf.setFont(undefined, 'bold');
                
                let xPos = margin;
                headers.forEach((header, index) => {
                    pdf.rect(xPos, yPos, colWidth, 6);
                    pdf.text(header, xPos + 2, yPos + 4);
                    xPos += colWidth;
                });
                
                yPos += 6;
                pdf.setFont(undefined, 'normal');
                
                // Draw data rows
                data.forEach((row, rowIndex) => {
                    // Check for new page
                    if (yPos > pageHeight - 20) {
                        pdf.addPage();
                        yPos = 20;
                        
                        // Re-add headers
                        pdf.setFont(undefined, 'bold');
                        xPos = margin;
                        headers.forEach((header, index) => {
                            pdf.rect(xPos, yPos, colWidth, 6);
                            pdf.text(header, xPos + 2, yPos + 4);
                            xPos += colWidth;
                        });
                        yPos += 6;
                        pdf.setFont(undefined, 'normal');
                    }
                    
                    // Check if this row has a "Selected" column with "YES"
                    const isSelectedRow = row[row.length - 1] === 'YES' && headers[headers.length - 1] === 'Selected';
                    
                    xPos = margin;
                    row.forEach((cell, cellIndex) => {
                        let text = String(cell || '');
                        
                        // Truncate long text
                        const maxLength = Math.floor(colWidth / 1.5);
                        if (text.length > maxLength) {
                            text = text.substring(0, maxLength - 3) + '...';
                        }
                        
                        // Style section headers differently
                        if (cellIndex === 0 && (text.includes('SECTION') || text.includes('CHARGES') || text.includes('CASH DUE'))) {
                            pdf.setFont(undefined, 'bold');
                        }
                        
                        // Highlight selected row
                        if (isSelectedRow) {
                            pdf.setFillColor(220, 220, 220); // Light gray background
                            pdf.rect(xPos, yPos, colWidth, 6, 'F');
                        }
                        
                        // Draw cell border
                        pdf.rect(xPos, yPos, colWidth, 6);
                        
                        // Special styling for "Selected" column
                        if (cellIndex === row.length - 1 && headers[headers.length - 1] === 'Selected') {
                            if (text === 'YES') {
                                pdf.setFont(undefined, 'bold');
                                pdf.setTextColor(0, 128, 0); // Green color for YES
                            } else {
                                pdf.setTextColor(128, 128, 128); // Gray color for NO
                            }
                        }
                        
                        pdf.text(text, xPos + 2, yPos + 4);
                        
                        // Reset font and color
                        if (cellIndex === 0 && (text.includes('SECTION') || text.includes('CHARGES') || text.includes('CASH DUE'))) {
                            pdf.setFont(undefined, 'normal');
                        }
                        if (cellIndex === row.length - 1 && headers[headers.length - 1] === 'Selected') {
                            pdf.setFont(undefined, 'normal');
                            pdf.setTextColor(0, 0, 0); // Reset to black
                        }
                        
                        xPos += colWidth;
                    });
                    
                    yPos += 6;
                });
                
                return yPos;
            }
            
            // Utility function for number formatting
            function numberWithCommas(x) {
                if (x === null || x === undefined || isNaN(x)) return '0';
                return parseFloat(x).toLocaleString('en-US');
            }

            // Store selected loan program globally
            window.selectedLoanProgram = null;

            // Start Application Function
            window.startApplication = async function() {
                if (!window.selectedLoanProgram || !window.allLoansData) {
                    alert('Please select a loan program first by clicking "Check Summary for This Program"');
                    return;
                }

                // Show loading state
                const button = document.getElementById('startApplicationBtn');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                button.disabled = true;

                try {
                    // Get form data
                    const formData = getFormDataForPDF();
                    
                    // Get selected loan program data
                    const selectedLoanData = window.allLoansData[window.selectedLoanProgram][0];
                    
                    // Prepare loan programs array with selection status
                    const loanPrograms = [];
                    const isDscrLoan = formData.loan_type === 'DSCR Rental Loans';
                    
                    Object.keys(window.allLoansData).forEach(programName => {
                        const loanData = window.allLoansData[programName][0];
                        
                        if (isDscrLoan) {
                            // For DSCR loans, use loan_program_values structure
                            const loanTypeData = loanData.loan_program_values;
                            
                            loanPrograms.push({
                                loan_type: loanData.loan_type || 'DSCR Rental Loans',
                                loan_program: programName,
                                loan_program_values: loanTypeData, // Include the full structure for DSCR
                                loan_term: loanTypeData?.loan_term,
                                interest_rate: loanTypeData?.interest_rate,
                                lender_points: loanTypeData?.lender_points,
                                max_ltv: loanTypeData?.max_ltv,
                                loan_amount: loanTypeData?.loan_amount || loanData?.ltv_formula?.loan_amount?.input,
                                monthly_payment: loanTypeData?.monthly_payment,
                                pricing_tier: loanTypeData?.pricing_tier,
                                is_selected: programName === window.selectedLoanProgram
                            });
                        } else {
                            // For Fix & Flip / New Construction loans, use existing logic
                            const loanTypeData = loanData.loan_type_and_loan_program_table || loanData.loan_program_values;
                            
                            loanPrograms.push({
                                loan_type: loanData.loan_type,
                                loan_program: programName,
                                loan_term: loanTypeData?.loan_term || formData.loan_term,
                                interest_rate: loanTypeData?.interest_rate || loanTypeData?.rate,
                                lender_points: loanTypeData?.lender_points || loanTypeData?.points,
                                max_ltv: loanTypeData?.max_ltv,
                                max_ltc: loanTypeData?.max_ltc,
                                max_ltfc: loanTypeData?.max_ltfc,
                                purchase_loan_up_to: loanTypeData?.purchase_loan_up_to || 0,
                                rehab_loan_up_to: loanTypeData?.rehab_loan_up_to || 0,
                                total_loan_up_to: loanTypeData?.total_loan_up_to || 0,
                                rehab_category: loanTypeData?.rehab_category,
                                rehab_percentage: loanTypeData?.percentage_max_ltv_max_ltc,
                                pricing_tier: loanTypeData?.pricing_tier,
                                is_selected: programName === window.selectedLoanProgram
                            });
                        }
                    });

                    // Get calculated values from the selected program
                    const closingStatement = selectedLoanData.estimated_closing_statement;
                    let calculatedValues = {};
                    
                    if (isDscrLoan) {
                        // For DSCR loans, extract values from DSCR structure
                        calculatedValues = {
                            purchase_loan_amount: closingStatement?.buyer_related_charges?.purchase_price || 0,
                            rehab_loan_amount: 0, // DSCR loans don't have rehab
                            total_loan_amount: closingStatement?.loan_amount_section?.initial_loan_amount || 0,
                            lender_origination_fee: closingStatement?.lender_related_charges?.lender_origination_fee || 0,
                            broker_fee: closingStatement?.lender_related_charges?.broker_fee || 0,
                            underwriting_processing_fee: closingStatement?.lender_related_charges?.underwriting_processing_fee || 0,
                            interest_reserves: closingStatement?.lender_related_charges?.interest_reserves || 0,
                            title_charges: closingStatement?.title_other_charges?.title_charges || 0,
                            legal_doc_prep_fee: closingStatement?.title_other_charges?.legal_doc_prep_fee || 0,
                            subtotal_closing_costs: closingStatement?.title_other_charges?.subtotal_closing_costs || 0,
                            cash_due_to_buyer: closingStatement?.cash_due_to_buyer || 0
                        };
                    } else {
                        // For Fix & Flip / New Construction loans, use existing logic
                        calculatedValues = {
                            purchase_loan_amount: closingStatement?.loan_amount_section?.purchase_loan_amount || 0,
                            rehab_loan_amount: closingStatement?.loan_amount_section?.rehab_loan_amount || 0,
                            total_loan_amount: closingStatement?.loan_amount_section?.total_loan_amount || 0,
                            lender_origination_fee: closingStatement?.lender_related_charges?.lender_origination_fee || 0,
                            broker_fee: closingStatement?.lender_related_charges?.broker_fee || 0,
                            underwriting_processing_fee: closingStatement?.lender_related_charges?.underwriting_processing_fee || 0,
                            interest_reserves: closingStatement?.lender_related_charges?.interest_reserves || 0,
                            title_charges: closingStatement?.title_other_charges?.title_charges || 0,
                            legal_doc_prep_fee: closingStatement?.title_other_charges?.legal_doc_prep_fee || 0,
                            subtotal_closing_costs: closingStatement?.title_other_charges?.subtotal_closing_costs || 0,
                            cash_due_to_buyer: closingStatement?.cash_due_to_buyer || 0
                        };
                    }

                    // Show user input modal
                    showUserInputModal(formData, loanPrograms, calculatedValues, selectedLoanData);

                } catch (error) {
                    console.error('Error starting application:', error);
                    alert('An error occurred while starting the application. Please try again.');
                } finally {
                    // Reset button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            };

            // Show user input modal for collecting borrower information
            function showUserInputModal(formData, loanPrograms, calculatedValues, selectedLoanData) {
                // Create modal HTML
                const modalHTML = `
                    <div id="userInputModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Complete Your Loan Application</h3>
                                <form id="borrowerInfoForm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                            <input type="text" id="firstName" name="first_name" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                            <input type="text" id="lastName" name="last_name" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                            <input type="email" id="email" name="email" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                            <input type="tel" id="phone" name="phone" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                        <h4 class="font-semibold text-gray-800 mb-2">Selected Loan Program</h4>
                                        <p class="text-sm text-gray-600">${window.selectedLoanProgram}</p>
                                        <p class="text-sm text-gray-600">Loan Amount: $${numberWithCommas(calculatedValues.total_loan_amount)}</p>
                                    </div>

                                    <!-- Guarantor Disclosures -->
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-800 mb-3">Guarantor Disclosures (Must Check All)</h4>
                                        <div class="space-y-2 text-sm">
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="guarantor_disclosures[]" value="fico_score" required class="mt-1">
                                                <span>I certify my FICO score is equal to or higher than stated; lower scores may affect terms.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="guarantor_disclosures[]" value="experience_verifiable" required class="mt-1">
                                                <span>I certify my experience is verifiable with HUDs and entity documentation.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="guarantor_disclosures[]" value="full_recourse" required class="mt-1">
                                                <span>I understand this is a full recourse loan; members with 20%+ ownership must guaranty.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="guarantor_disclosures[]" value="no_bankruptcies" required class="mt-1">
                                                <span>I have no unresolved bankruptcies or foreclosures in the past 4 years.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="guarantor_disclosures[]" value="no_delinquencies" required class="mt-1">
                                                <span>I have no delinquent mortgages, unpaid liens, or active lawsuits.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="guarantor_disclosures[]" value="no_financial_crimes" required class="mt-1">
                                                <span>I have no history of financial crimes or serious felonies.</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Property Disclosures -->
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-800 mb-3">Property Disclosures (Must Check All)</h4>
                                        <div class="space-y-2 text-sm">
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="property_disclosures[]" value="property_type" required class="mt-1">
                                                <span>The property is 1–4 units, non-owner occupied, and for investment use only.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="property_disclosures[]" value="not_rural" required class="mt-1">
                                                <span>The property is not in a rural area as defined by USDA Rural Designation Map.</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="property_disclosures[]" value="loan_good_standing" required class="mt-1">
                                                <span>For Refinance Fix and Flip, current loan must be in Good Standing (No Default)</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="property_disclosures[]" value="assignment_fees" required class="mt-1">
                                                <span>Assignment fees are under 20% of underlying purchase price (terms may be affected)</span>
                                            </label>
                                            <label class="flex items-start space-x-2">
                                                <input type="checkbox" name="property_disclosures[]" value="profitability" required class="mt-1">
                                                <span>I understand the project must meet minimum profitability requirements (15% minimum).</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="closeUserInputModal()" 
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit" id="submitApplicationBtn"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span id="submitButtonText">Submit Application</span>
                                            <span id="submitButtonLoader" class="hidden">
                                                <svg class="animate-spin h-4 w-4 inline mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Submitting...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `;

                // Add modal to DOM
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                // Add success modal HTML
                const successModalHTML = `
                    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                        <svg class="w-6 h-6 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Application Submitted Successfully!
                                    </h3>
                                </div>
                                
                                <div class="mb-6">
                                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                                        <p class="text-green-800 mb-2">
                                            <strong>Email Confirmation:</strong> We've sent a confirmation email with your application details.
                                        </p>
                                        <p id="successModalContent" class="text-gray-700">
                                            <!-- Dynamic content will be inserted here -->
                                        </p>
                                    </div>
                                    
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                        <h4 class="font-semibold text-blue-800 mb-2">Next Steps:</h4>
                                        <ul class="text-blue-700 text-sm space-y-1">
                                            <li>• Check your email for application confirmation</li>
                                            <li>• Our team will review your application within 24-48 hours</li>
                                            <li>• We'll contact you with any additional requirements</li>
                                            <li id="loginInstruction">• Log in to track your application status</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="flex justify-between">
                                    <button id="loginButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Login to Account
                                    </button>
                                    <button id="closeSuccessModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', successModalHTML);

                // Handle form submission
                document.getElementById('borrowerInfoForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Validate all disclosure checkboxes are checked
                    const guarantorCheckboxes = document.querySelectorAll('input[name="guarantor_disclosures[]"]');
                    const propertyCheckboxes = document.querySelectorAll('input[name="property_disclosures[]"]');
                    
                    const allGuarantorChecked = Array.from(guarantorCheckboxes).every(cb => cb.checked);
                    const allPropertyChecked = Array.from(propertyCheckboxes).every(cb => cb.checked);
                    
                    if (!allGuarantorChecked || !allPropertyChecked) {
                        alert('Please check all disclosure statements before submitting your application.');
                        return;
                    }
                    
                    await submitApplicationData(formData, loanPrograms, calculatedValues, selectedLoanData);
                });
            }

            // Close user input modal
            window.closeUserInputModal = function() {
                const modal = document.getElementById('userInputModal');
                if (modal) {
                    modal.remove();
                }
            };

            // Submit application data to backend
            async function submitApplicationData(formData, loanPrograms, calculatedValues, selectedLoanData) {
                const borrowerForm = document.getElementById('borrowerInfoForm');
                const borrowerData = new FormData(borrowerForm);
                
                const submitButton = document.getElementById('submitApplicationBtn');
                const submitButtonText = document.getElementById('submitButtonText');
                const submitButtonLoader = document.getElementById('submitButtonLoader');
                
                // Disable button and show loading state
                submitButton.disabled = true;
                submitButtonText.classList.add('hidden');
                submitButtonLoader.classList.remove('hidden');

                try {
                    // Determine if this is a DSCR loan
                    const isDscrLoan = formData.loan_type === 'DSCR Rental Loans';
                    
                    console.log('Submitting application for loan type:', formData.loan_type, 'isDscrLoan:', isDscrLoan);
                    console.log('Form data:', formData);
                    console.log('Property address:', formData.property_address);
                    
                    // Prepare the payload
                    const payload = {
                        // Borrower information
                        first_name: borrowerData.get('first_name'),
                        last_name: borrowerData.get('last_name'),
                        email: borrowerData.get('email'),
                        phone: borrowerData.get('phone'),
                        
                        // Calculator inputs - common fields
                        credit_score: parseInt(formData.credit_score),
                        experience: parseInt(formData.experience),
                        loan_type: formData.loan_type,
                        transaction_type: formData.transaction_type,
                        purchase_price: parseFloat(formData.purchase_price),
                        broker_points: parseFloat(formData.broker_points),
                        state: formData.state,
                        property_type: formData.property_type || null,
                        property_address: formData.property_address || null,
                        
                        // Optional fields - populate based on loan type
                        payoff_amount: formData.payoff_amount ? parseFloat(formData.payoff_amount) : null,
                        title_charges: formData.title_charges ? parseFloat(formData.title_charges) : null,
                        property_insurance: formData.property_insurance ? parseFloat(formData.property_insurance) : null,
                        
                        // Selected loan program
                        selected_loan_program: window.selectedLoanProgram,
                        
                        // All loan programs with selection status
                        loan_programs: loanPrograms,
                        
                        // Calculated values
                        calculated_values: calculatedValues,
                        
                        // API data
                        api_url: window.lastApiUrl || 'Unknown',
                        api_response: selectedLoanData
                    };
                    
                    // Add loan type specific fields
                    if (isDscrLoan) {
                        // DSCR loan specific fields
                        payload.arv = null; // DSCR doesn't use ARV
                        payload.rehab_budget = null; // DSCR doesn't use rehab_budget
                        
                        // DSCR specific required fields
                        payload.occupancy_type = formData.occupancy_type || null;
                        payload.monthly_market_rent = formData.monthly_market_rent ? parseFloat(formData.monthly_market_rent) : null;
                        payload.annual_tax = formData.annual_tax ? parseFloat(formData.annual_tax) : null;
                        payload.annual_insurance = formData.annual_insurance ? parseFloat(formData.annual_insurance) : null;
                        payload.annual_hoa = formData.annual_hoa ? parseFloat(formData.annual_hoa) : null;
                        payload.purchase_date = formData.purchase_date || null;
                        
                        // Extract DSCR value from API response (calculated_dscr in loan_program_values)
                        let dscrValue = null;
                        if (selectedLoanData && selectedLoanData.loan_program_values && selectedLoanData.loan_program_values.calculated_dscr) {
                            dscrValue = parseFloat(selectedLoanData.loan_program_values.calculated_dscr);
                        } else if (formData.dscr) {
                            dscrValue = parseFloat(formData.dscr);
                        }
                        payload.dscr = dscrValue;
                        
                        // DSCR dropdown values - prioritize API response over current values
                        payload.loan_term = selectedLoanData?.loan_program_values?.loan_term || window.currentDscrValues?.loanTerm || null;
                        payload.lender_points = selectedLoanData?.loan_program_values?.lender_points ? 
                            parseFloat(selectedLoanData.loan_program_values.lender_points) : 
                            (window.currentDscrValues?.lenderPoints ? parseFloat(window.currentDscrValues.lenderPoints) : null);
                        payload.pre_pay_penalty = selectedLoanData?.loan_program_values?.pre_pay_penalty || window.currentDscrValues?.prepayPenalty || null;
                        
                        // Extract DSCR-specific loan amount from selected loan data
                        if (selectedLoanData && selectedLoanData.ltv_formula && selectedLoanData.ltv_formula.loan_amount) {
                            payload.loan_amount_requested = parseFloat(selectedLoanData.ltv_formula.loan_amount.input || 0);
                        }
                        
                    } else {
                        // Fix & Flip / New Construction loan specific fields
                        payload.loan_term = parseInt(formData.loan_term);
                        payload.arv = parseFloat(formData.arv);
                        payload.rehab_budget = parseFloat(formData.rehab_budget);
                        payload.lender_points = formData.lender_points ? parseFloat(formData.lender_points) : null;
                        payload.pre_pay_penalty = formData.pre_pay_penalty || null;
                        
                        // Fix and Flip specific refinance fields
                        if (formData.loan_type === 'Fix and Flip' && (formData.transaction_type === 'Refinance' || formData.transaction_type === 'Refinance Cash Out')) {
                            payload.seasoning_period = formData.fix_flip_seasoning_period || null;
                            payload.fix_flip_payoff_amount = formData.fix_flip_payoff_amount ? parseFloat(formData.fix_flip_payoff_amount) : null;
                        }
                        
                        // New Construction specific fields
                        if (formData.loan_type === 'New Construction') {
                            payload.guc_experience = formData.guc_experience ? parseInt(formData.guc_experience) : null;
                            payload.permit_status = formData.permit_status || null;
                            payload.new_construction_payoff_amount = formData.new_construction_payoff_amount ? parseFloat(formData.new_construction_payoff_amount) : null;
                        }
                        
                        // These fields are not used for Fix & Flip / New Construction
                        payload.occupancy_type = null;
                        payload.monthly_market_rent = null;
                        payload.annual_tax = null;
                        payload.annual_insurance = null;
                        payload.annual_hoa = null;
                        payload.dscr = null;
                        payload.purchase_date = null;
                    }
                    
                    console.log('Final submission payload:', payload);

                    // Submit to backend
                    const response = await fetch('/api/loan-application/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.success) {
                        closeUserInputModal();
                        showSuccessModal(result);
                    } else {
                        alert('Error submitting application: ' + (result.message || 'Unknown error'));
                    }

                } catch (error) {
                    console.error('Error submitting application:', error);
                    alert('An error occurred while submitting your application. Please try again.');
                } finally {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButtonText.classList.remove('hidden');
                    submitButtonLoader.classList.add('hidden');
                }
            }

            // Show success modal with account-specific content
            function showSuccessModal(result) {
                const modal = document.getElementById('successModal');
                const modalContent = document.getElementById('successModalContent');
                const loginButton = document.getElementById('loginButton');
                const loginInstruction = document.getElementById('loginInstruction');
                
                // Set content based on account type
                if (result.data && result.data.is_new_user) {
                    modalContent.innerHTML = `
                        <p class="mb-2">
                            <strong>New Account Created:</strong> We've created an account for you using your email address.
                        </p>
                        <p class="mb-2">
                            <strong>Email:</strong> <span class="font-mono bg-gray-100 px-2 py-1 rounded">${result.data.email || 'Your email address'}</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Your login credentials have been sent to your email. Please check your inbox for login instructions.
                        </p>
                    `;
                    loginInstruction.textContent = '• Use the credentials sent to your email to log in';
                } else {
                    modalContent.innerHTML = `
                        <p class="mb-2">
                            <strong>Existing Account:</strong> This application has been added to your existing account.
                        </p>
                        <p class="mb-2">
                            <strong>Email:</strong> <span class="font-mono bg-gray-100 px-2 py-1 rounded">${result.data.email || 'Your email address'}</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Please log in using your existing password. If you've forgotten your password, you can reset it using the link below.
                        </p>
                    `;
                    loginInstruction.innerHTML = '• Log in with your existing password or <a href="{{ route("password.request") }}" class="text-blue-600 hover:underline">reset password</a>';
                }
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Handle login button click
                loginButton.onclick = function() {
                    window.location.href = '{{ route("login") }}';
                };
                
                // Handle close button
                document.getElementById('closeSuccessModal').onclick = function() {
                    modal.classList.add('hidden');
                };
                
                // Close modal when clicking outside
                modal.onclick = function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                };
            }

            $('form').submit(function(){
                // If x-button does not render as a traditional submit button, target it directly by ID or class
                $('#submit-btn').attr('disabled', 'disabled');
            });
                        </script>
                        @stack('modals')

                        {{-- Allow views to push inline scripts -- e.g. @push('scripts') -- so they are rendered here
                        --}}
                        @stack('scripts')
                        @livewireScripts
</body>

</html>