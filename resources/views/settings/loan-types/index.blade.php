<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Loan Types Settings
        </h2>
    </x-slot>

    @push('styles')
    <style>
        /* Additional styles for better table readability - Similar to DSCR Matrix */
        .table-auto td,
        .table-auto th {
            white-space: nowrap;
        }

        /* Compact editable cells - Similar to DSCR Matrix */
        .editable-cell {
            max-width: 80px;
            min-width: 60px;
            display: inline-block;
            text-align: center;
            font-size: 0.75rem;
        }

        /* Responsive text sizing - Similar to DSCR Matrix */
        @media (max-width: 768px) {
            .table-auto {
                font-size: 0.65rem;
            }

            .table-auto th,
            .table-auto td {
                padding: 0.25rem;
            }

            .editable-cell {
                max-width: 60px;
                min-width: 40px;
                font-size: 0.65rem;
            }
        }

        /* Print styles - Similar to DSCR Matrix */
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

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <!-- Loan Types Table - Similar to DSCR Matrix Design -->
                @if (count($loanTypes) > 0)
                <div class="relative overflow-x-auto rounded-lg">
                    <table class="min-w-max w-full table-auto text-xs border-collapse">
                        <thead>
                            <!-- Header similar to DSCR Matrix -->
                            <tr class="bg-green-800 text-white uppercase">
                                <th colspan="6" class="py-3 px-4 text-center font-bold text-lg border border-white">
                                    Loan Types Settings - Management Table
                                </th>
                            </tr>

                            <tr class="bg-green-800 text-white uppercase text-xs">
                                <th class="py-2 px-1 text-center border border-white text-xs">ID</th>
                                <th class="py-2 px-1 text-center border border-white text-xs">Loan Type Name</th>
                                <th class="py-2 px-1 text-center border border-white text-xs">Loan Program</th>
                                <th class="py-2 px-1 text-center border border-white text-xs">Underwriting Fee<br><span
                                        class="text-xs normal-case">(Click to edit)</span></th>
                                <th class="py-2 px-1 text-center border border-white text-xs">Legal Doc Prep
                                    Fee<br><span class="text-xs normal-case">(Click to edit)</span></th>
                                <th class="py-2 px-1 text-center border border-white text-xs">Starting Rate<br><span
                                        class="text-xs normal-case">(Click to edit)</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loanTypes as $loanType)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <!-- ID Column -->
                                <td class="py-1 px-1 text-center border border-gray-300">
                                    {{ $loanType->id }}
                                </td>

                                <!-- Name Column (Non-editable) -->
                                <td class="py-1 px-1 text-center border border-gray-300">
                                    {{ $loanType->name }}
                                </td>

                                <!-- Loan Program Column (Non-editable) -->
                                <td class="py-1 px-1 text-center border border-gray-300">
                                    {{ $loanType->loan_program }}
                                </td>

                                <!-- Underwriting Fee Column (Editable) -->
                                <td class="py-1 px-1 text-center border border-gray-300">
                                    <span
                                        class="editable-cell inline-block cursor-pointer hover:bg-yellow-100 focus:bg-yellow-200 px-1 py-1 rounded transition-colors duration-200 min-w-12 text-center"
                                        contenteditable="true" data-loan-type-id="{{ $loanType->id }}"
                                        data-field="underwritting_fee"
                                        data-original-value="{{ $loanType->underwritting_fee }}"
                                        title="Click to edit underwriting fee">${{
                                        number_format($loanType->underwritting_fee, 2) }}</span>
                                </td>

                                <!-- Legal Doc Prep Fee Column (Editable) -->
                                <td class="py-1 px-1 text-center border border-gray-300">
                                    <span
                                        class="editable-cell inline-block cursor-pointer hover:bg-yellow-100 focus:bg-yellow-200 px-1 py-1 rounded transition-colors duration-200 min-w-12 text-center"
                                        contenteditable="true" data-loan-type-id="{{ $loanType->id }}"
                                        data-field="legal_doc_prep_fee"
                                        data-original-value="{{ $loanType->legal_doc_prep_fee }}"
                                        title="Click to edit legal doc prep fee">${{
                                        number_format($loanType->legal_doc_prep_fee, 2) }}</span>
                                </td>

                                <!-- Starting Rate Column (Editable) -->
                                <td class="py-1 px-1 text-center border border-gray-300">
                                    <span
                                        class="editable-cell inline-block cursor-pointer hover:bg-yellow-100 focus:bg-yellow-200 px-1 py-1 rounded transition-colors duration-200 min-w-12 text-center"
                                        contenteditable="true" data-loan-type-id="{{ $loanType->id }}"
                                        data-field="loan_starting_rate"
                                        data-original-value="{{ $loanType->loan_starting_rate }}"
                                        title="Click to edit starting rate">{{
                                        number_format($loanType->loan_starting_rate, 3) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="py-8 px-3 text-center text-gray-500 dark:text-gray-400">
                    <div class="text-lg">No loan types found</div>
                    <div class="text-sm mt-2">Add your first loan type to get started</div>
                </div>
                @endif

                <!-- Help Text -->
                <div class="p-6">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <h4 class="font-semibold mb-2">How to use:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Click on any editable field (fees and rates) to modify the value</li>
                            <li>Press Enter to save your changes or click outside the field</li>
                            <li>Press Escape to cancel editing and restore the original value</li>
                            <li>Currency fields should be entered as numbers only (e.g., 1500 for $1,500.00)</li>
                            <li>Rate fields should be entered as percentages (e.g., 5.5 for 5.500%)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Loan Types Inline Editing JavaScript - Similar to DSCR Matrix -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Loan Types editing script loaded');
            
            // Find all editable cells
            const editableCells = document.querySelectorAll('.editable-cell');
            console.log('Found editable cells:', editableCells.length);
            
            editableCells.forEach(function(cell) {
                let originalValue = '';
                let isEscapePressed = false;
                
                // Focus event - when user clicks to edit
                cell.addEventListener('focus', function() {
                    console.log('Cell focused for editing');
                    originalValue = this.getAttribute('data-original-value') || this.textContent.trim();
                    isEscapePressed = false; // Reset escape flag
                    
                    // Remove formatting for editing
                    const field = this.getAttribute('data-field');
                    let editValue = originalValue;
                    
                    if (field === 'underwritting_fee' || field === 'legal_doc_prep_fee') {
                        // Remove $ and commas for currency fields
                        editValue = this.textContent.replace(/[$,]/g, '');
                    } else if (field === 'loan_starting_rate') {
                        // Remove % for percentage fields
                        editValue = this.textContent.replace('%', '');
                    }
                    
                    this.textContent = editValue;
                    this.style.backgroundColor = '#fef3c7'; // yellow-100
                    this.style.border = '2px solid #f59e0b'; // yellow-500
                    this.style.outline = 'none';
                    
                    // Select all text when focused
                    if (window.getSelection) {
                        const selection = window.getSelection();
                        const range = document.createRange();
                        range.selectNodeContents(this);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                });
                
                // Blur event - when user clicks away
                cell.addEventListener('blur', function() {
                    console.log('Cell lost focus');
                    this.style.backgroundColor = '';
                    this.style.border = '';
                    
                    // If escape was pressed, don't save
                    if (isEscapePressed) {
                        console.log('Escape was pressed, not saving');
                        this.setAttribute('data-original-value', originalValue);
                        formatCellValue(this);
                        return;
                    }
                    
                    const newValue = this.textContent.trim();
                    
                    // Only save if value actually changed and is not empty
                    if (newValue !== originalValue && newValue !== '') {
                        console.log('Value changed from', originalValue, 'to', newValue);
                        saveLoanTypeValue(this, newValue);
                    } else if (newValue === '') {
                        // Restore original value if empty
                        this.setAttribute('data-original-value', originalValue);
                        formatCellValue(this);
                    } else {
                        // No change, just reformat
                        formatCellValue(this);
                    }
                });
                
                // Enter key to save and exit
                cell.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur();
                    }
                    
                    // Escape key to cancel
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        isEscapePressed = true;
                        this.setAttribute('data-original-value', originalValue);
                        formatCellValue(this);
                        this.blur();
                    }
                });
            });
            
            function formatCellValue(cell) {
                const field = cell.getAttribute('data-field');
                const value = cell.getAttribute('data-original-value');
                
                switch (field) {
                    case 'underwritting_fee':
                    case 'legal_doc_prep_fee':
                        cell.textContent = '$' + parseFloat(value).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        break;
                    case 'loan_starting_rate':
                        cell.textContent = parseFloat(value).toFixed(3) + '%';
                        break;
                    default:
                        cell.textContent = value;
                }
            }
            
            function saveLoanTypeValue(cell, newValue) {
                console.log('Saving loan type value...');
                
                // Get data attributes
                const loanTypeId = cell.getAttribute('data-loan-type-id');
                const field = cell.getAttribute('data-field');
                
                console.log('Saving:', {
                    loanTypeId: loanTypeId,
                    field: field,
                    newValue: newValue
                });
                
                // Show loading state
                const originalContent = cell.innerHTML;
                cell.innerHTML = '<div class="animate-spin inline-block w-3 h-3 border-2 border-blue-500 border-t-transparent rounded-full"></div>';
                
                // Make AJAX request
                fetch('/settings/loan-types/api/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        loan_type_id: parseInt(loanTypeId),
                        field: field,
                        value: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Save response:', data);
                    
                    if (data.success) {
                        // Update the stored original value
                        cell.setAttribute('data-original-value', data.data.value);
                        
                        // Update display with formatted value
                        cell.textContent = data.data.formatted_value;
                        
                        // Show success feedback
                        cell.style.backgroundColor = '#d1fae5'; // green-100
                        setTimeout(() => {
                            cell.style.backgroundColor = '';
                        }, 1000);
                        
                        console.log('Loan type updated successfully');
                    } else {
                        throw new Error(data.message || 'Failed to update value');
                    }
                })
                .catch(error => {
                    console.error('Error saving loan type value:', error);
                    
                    // Show error feedback
                    cell.style.backgroundColor = '#fee2e2'; // red-100
                    cell.style.border = '2px solid #ef4444'; // red-500
                    
                    // Restore original content
                    cell.innerHTML = originalContent;
                    
                    // Show error message
                    alert('Error updating value: ' + error.message);
                    
                    setTimeout(() => {
                        cell.style.backgroundColor = '';
                        cell.style.border = '';
                    }, 3000);
                });
            }
        });
    </script>
    @endpush
</x-app-layout>