@props([
'title',
'description',
'route',
'buttonText',
'theme' => 'blue'
])

@php
$themes = [
'blue' => [
'gradient' => 'from-blue-50 to-indigo-100',
'border' => 'border-blue-200',
'icon' => 'bg-blue-500',
'button' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-600',
'hover' => 'group-hover:text-blue-600'
],
'green' => [
'gradient' => 'from-green-50 to-emerald-100',
'border' => 'border-green-200',
'icon' => 'bg-green-500',
'button' => 'bg-green-600 hover:bg-green-700 focus:ring-green-600',
'hover' => 'group-hover:text-green-600'
],
'purple' => [
'gradient' => 'from-purple-50 to-violet-100',
'border' => 'border-purple-200',
'icon' => 'bg-purple-500',
'button' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-600',
'hover' => 'group-hover:text-purple-600'
],
'orange' => [
'gradient' => 'from-orange-50 to-amber-100',
'border' => 'border-orange-200',
'icon' => 'bg-orange-500',
'button' => 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-600',
'hover' => 'group-hover:text-orange-600'
]
];

$currentTheme = $themes[$theme] ?? $themes['blue'];
@endphp

<div
    class="bg-gradient-to-br {{ $currentTheme['gradient'] }} dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl sm:rounded-lg {{ $currentTheme['border'] }} dark:border-gray-600 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
    <div class="p-8">
        <div class="flex items-center justify-center mb-6">
            <div
                class="flex-shrink-0 {{ $currentTheme['icon'] }} rounded-full p-4 group-hover:scale-110 transition-transform duration-300">
                {{ $slot }}
            </div>
        </div>
        <div class="text-center">
            <h3
                class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3 {{ $currentTheme['hover'] }} transition-colors duration-300">
                {{ $title }}
            </h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                {{ $description }}
            </p>
            <a href="{{ $route }}"
                class="inline-flex items-center px-6 py-3 {{ $currentTheme['button'] }} border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider active:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all ease-in-out duration-200 shadow-lg hover:shadow-xl group-hover:scale-105">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                {{ $buttonText }}
                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg>
            </a>
        </div>
    </div>
</div>