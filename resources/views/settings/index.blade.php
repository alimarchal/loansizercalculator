<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 lg:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Loan Types Management Card -->
                <a href="{{ route('settings.loan-types.index') }}" class="block">
                    <div
                        class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500 hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700/80">
                        <div class="flex items-center">
                            <div
                                class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-lg">
                                <svg class="w-7 h-7 stroke-red-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c0 .621-.504 1.125-1.125 1.125H11.25a9 9 0 01-9-9V5.625z" />
                                </svg>
                            </div>

                            <div class="ml-4 lg:ml-6">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Loan Types
                                </h2>
                                <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                    Manage loan types, fees, and starting rates. Edit underwriting fees, legal
                                    document preparation fees, and initial interest rates.
                                </p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ $settingsData['loan_types_count'] ?? 0 }} Types Available
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Placeholder for future settings - DSCR Configuration -->
                <div
                    class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none opacity-50">
                    <div class="flex items-center">
                        <div
                            class="h-16 w-16 bg-green-50 dark:bg-green-800/20 flex items-center justify-center rounded-lg">
                            <svg class="w-7 h-7 stroke-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5-3h9v4.5m11-4.5h-9v4.5" />
                            </svg>
                        </div>

                        <div class="ml-4 lg:ml-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                DSCR Configuration
                            </h2>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Configure DSCR matrix ranges, LTV adjustments, and calculation parameters.
                            </p>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    Coming Soon
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder for future settings - Rate Matrix -->
                <div
                    class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none opacity-50">
                    <div class="flex items-center">
                        <div
                            class="h-16 w-16 bg-blue-50 dark:bg-blue-800/20 flex items-center justify-center rounded-lg">
                            <svg class="w-7 h-7 stroke-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                            </svg>
                        </div>

                        <div class="ml-4 lg:ml-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Rate Matrix
                            </h2>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Manage interest rate matrices, pricing tiers, and rate adjustments based on loan
                                parameters.
                            </p>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    Coming Soon
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder for future settings - User Management -->
                <div
                    class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none opacity-50">
                    <div class="flex items-center">
                        <div
                            class="h-16 w-16 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-lg">
                            <svg class="w-7 h-7 stroke-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>

                        <div class="ml-4 lg:ml-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                User Management
                            </h2>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Manage user accounts, permissions, and access levels for the loan calculator
                                application.
                            </p>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    Coming Soon
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder for future settings - System Configuration -->
                <div
                    class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none opacity-50">
                    <div class="flex items-center">
                        <div
                            class="h-16 w-16 bg-yellow-50 dark:bg-yellow-800/20 flex items-center justify-center rounded-lg">
                            <svg class="w-7 h-7 stroke-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.240.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>

                        <div class="ml-4 lg:ml-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                System Configuration
                            </h2>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Configure application-wide settings, email templates, and system preferences.
                            </p>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    Coming Soon
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>