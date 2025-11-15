<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight inline-block">
            Edit Checklist
        </h2>
        <div class="flex justify-center items-center float-right">
            <a href="{{ route('checklists.index') }}"
                class="inline-flex items-center ml-2 px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <x-status-message class="mb-4 mt-4" />
                <div class="p-6">
                    <x-validation-errors class="mb-4 mt-4" />
                    <form method="POST" action="{{ route('checklists.update', $checklist) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Name -->
                            <div>
                                <x-label for="name" value="Checklist Name" :required="true" />
                                <x-input id="name" type="text" name="name" class="mt-1 block w-full"
                                    :value="old('name', $checklist->name)" required />
                            </div>

                            <!-- Description -->
                            <div>
                                <x-label for="description" value="Description" />
                                <textarea id="description" name="description"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    rows="3">{{ old('description', $checklist->description) }}</textarea>
                            </div>

                            <!-- Loan Types -->
                            <div>
                                <x-label for="loan_types" value="Loan Types" :required="true" />
                                <select id="loan_types" name="loan_types[]"
                                    class="select2 mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    multiple required>
                                    @foreach ($loanTypes as $loanType)
                                    <option value="{{ $loanType->id }}" {{ in_array($loanType->id, old('loan_types',
                                        $checklist->loan_types)) ? 'selected' : '' }}>
                                        {{ $loanType->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Select one or more loan types for this checklist
                                </p>
                            </div>

                            <!-- Checklist Items -->
                            <div>
                                <x-label for="checklist_items" value="Checklist Items" :required="true" />
                                <div id="checklist-items-container" class="space-y-2 mt-2">
                                    @php
                                    $items = old('checklist_items', $checklist->checklist_items);
                                    @endphp
                                    @foreach($items as $index => $item)
                                    <div class="flex gap-2 checklist-item">
                                        <input type="text" name="checklist_items[]"
                                            class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            value="{{ $item }}" required placeholder="Enter checklist item">
                                        <button type="button"
                                            class="remove-item px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" id="add-item"
                                    class="mt-3 inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Item
                                </button>
                            </div>

                            <!-- Is Active -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active',
                                    $checklist->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                Update Checklist
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for loan types
            $('#loan_types').select2({
                placeholder: 'Select loan types',
                allowClear: true
            });

            // Add new checklist item
            $('#add-item').click(function() {
                const newItem = `
                    <div class="flex gap-2 checklist-item">
                        <input type="text" name="checklist_items[]"
                            class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required placeholder="Enter checklist item">
                        <button type="button"
                            class="remove-item px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                $('#checklist-items-container').append(newItem);
            });

            // Remove checklist item
            $(document).on('click', '.remove-item', function() {
                if ($('.checklist-item').length > 1) {
                    $(this).closest('.checklist-item').remove();
                } else {
                    alert('At least one checklist item is required.');
                }
            });
        });
    </script>
</x-app-layout>