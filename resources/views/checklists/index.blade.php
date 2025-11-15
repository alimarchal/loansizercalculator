<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            Checklists
        </h2>

        <div class="flex justify-center items-center float-right">
            <a href="{{ route('checklists.create') }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-950 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden md:inline-block">Add Checklist</span>
            </a>
            <a href="javascript:window.location.reload();"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-950 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </a>
        </div>
    </x-slot>

    <!-- TABLE SECTION -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4 pb-16">
        <x-status-message />
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            @if ($checklists->count() > 0)
            <div class="relative overflow-x-auto rounded-lg">
                <table class="min-w-max w-full table-auto text-sm">
                    <thead>
                        <tr class="bg-green-800 text-white uppercase text-sm">
                            <th class="py-2 px-2 text-center">ID</th>
                            <th class="py-2 px-2 text-left">Name</th>
                            <th class="py-2 px-2 text-left">Description</th>
                            <th class="py-2 px-2 text-left">Loan Types</th>
                            <th class="py-2 px-2 text-center">Items Count</th>
                            <th class="py-2 px-2 text-center">Status</th>
                            <th class="py-2 px-2 text-center">Created By</th>
                            <th class="py-2 px-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 dark:text-gray-300 text-sm">
                        @foreach ($checklists as $checklist)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900">
                            <td class="py-2 px-2 text-center">{{ $checklist->id }}</td>
                            <td class="py-2 px-2">
                                <div class="font-semibold">{{ $checklist->name }}</div>
                            </td>
                            <td class="py-2 px-2">
                                <div class="text-xs">{{ Str::limit($checklist->description, 50) }}</div>
                            </td>
                            <td class="py-2 px-2">
                                <div class="flex flex-wrap gap-1">
                                    @php
                                    $loanTypes = \App\Models\LoanType::whereIn('id', $checklist->loan_types)->get();
                                    @endphp
                                    @foreach($loanTypes as $loanType)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $loanType->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-2 px-2 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ count($checklist->checklist_items) }}
                                </span>
                            </td>
                            <td class="py-2 px-2 text-center">
                                @if($checklist->is_active)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                                @endif
                            </td>
                            <td class="py-2 px-2 text-center">
                                {{ $checklist->creator->name ?? 'N/A' }}
                            </td>
                            <td class="py-2 px-2 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('checklists.edit', $checklist) }}"
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('checklists.destroy', $checklist) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this checklist?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $checklists->links() }}
            </div>
            @else
            <div class="p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">No checklists found.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>